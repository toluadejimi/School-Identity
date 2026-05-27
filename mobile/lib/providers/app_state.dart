import 'package:flutter/foundation.dart';

import '../config/api_config.dart';
import '../models/student.dart';
import '../services/api_service.dart';
import '../services/offline_sync_service.dart';

enum ScanModule {
  identity,
  clinic,
  attendance,
  exam,
  busFare,
  libraryCheckIn,
  libraryCheckOut,
}

class AppState extends ChangeNotifier {
  AppState(this._api, {OfflineSyncService? offline})
    : _offline = offline ?? OfflineSyncService();

  final ApiService _api;
  final OfflineSyncService _offline;

  bool isLoading = false;
  bool isSyncing = false;
  int pendingSyncCount = 0;
  String? token;
  String? userName;
  List<String> roles = [];
  ScanModule module = ScanModule.identity;
  int? attendanceSessionId;
  int? examId;
  int? busRouteId;
  List<Map<String, dynamic>> attendanceSessions = [];
  List<Map<String, dynamic>> exams = [];
  List<Map<String, dynamic>> busRoutes = [];
  StudentProfile? lastStudent;
  String? lastMessage;
  bool? lastAllowed;

  Future<bool> login(String email, String password) async {
    isLoading = true;
    notifyListeners();

    try {
      final data = await _api.post('/auth/login', {
        'email': email,
        'password': password,
        'device_name': 'School Identity Mobile',
      }, auth: false);

      token = data['token'] as String?;
      final user = data['user'] as Map<String, dynamic>;
      userName = user['name'] as String?;
      roles = List<String>.from(user['roles'] as List? ?? []);

      if (token != null) {
        await _api.saveToken(token!);
        await _api.post('/devices/register', {
          'device_uuid': ApiConfig.deviceUuid,
          'name': 'School Identity Mobile',
          'type': 'mobile',
        });
        try {
          await loadReferenceData();
          await syncQueuedActions();
        } on NetworkException {
          pendingSyncCount = await _offline.pendingCount();
        }
      }

      return token != null;
    } finally {
      isLoading = false;
      notifyListeners();
    }
  }

  Future<void> loadReferenceData() async {
    attendanceSessions = List<Map<String, dynamic>>.from(
      (await _api.get('/attendance/sessions'))['sessions'] as List? ?? [],
    );
    exams = List<Map<String, dynamic>>.from(
      (await _api.get('/exams'))['exams'] as List? ?? [],
    );
    busRoutes = List<Map<String, dynamic>>.from(
      (await _api.get('/bus-routes'))['routes'] as List? ?? [],
    );
    notifyListeners();
  }

  Future<void> syncQueuedActions() async {
    if (isSyncing) return;

    final queue = await _offline.pendingActions();
    pendingSyncCount = queue.length;

    if (queue.isEmpty) {
      notifyListeners();
      return;
    }

    isSyncing = true;
    notifyListeners();

    final remaining = <OfflineAction>[];

    for (var i = 0; i < queue.length; i++) {
      final action = queue[i];

      try {
        await _api.post(action.path, {
          ...action.body,
          'offline_action_id': action.id,
          'offline_recorded_at': action.createdAt.toIso8601String(),
        });
      } on NetworkException {
        remaining
          ..add(action)
          ..addAll(queue.skip(i + 1));
        break;
      } on ApiException catch (e) {
        remaining
          ..add(action)
          ..addAll(queue.skip(i + 1));
        lastMessage = 'Offline sync paused: ${e.message}';
        break;
      }
    }

    await _offline.replaceQueue(remaining);
    pendingSyncCount = remaining.length;
    isSyncing = false;

    if (remaining.isEmpty) {
      lastMessage = 'Offline records synced to cloud';
    }

    notifyListeners();
  }

  Future<void> handleScan(String uid) async {
    isLoading = true;
    lastMessage = null;
    lastAllowed = null;
    notifyListeners();

    try {
      switch (module) {
        case ScanModule.identity:
          await _postIdentityScan(uid);
          break;
        case ScanModule.clinic:
          await _postOrQueueScan(
            uid: uid,
            path: '/clinic/check-in',
            body: {'uid': uid},
            moduleName: 'clinic',
            onlineMessage: 'Clinic check-in recorded',
            offlineMessage:
                'Clinic check-in saved offline. It will sync when network returns.',
          );
          break;
        case ScanModule.attendance:
          if (attendanceSessionId == null) {
            throw ApiException('Select an attendance session first', 422);
          }
          await _postOrQueueScan(
            uid: uid,
            path: '/attendance/scan',
            body: {'uid': uid, 'attendance_session_id': attendanceSessionId},
            moduleName: 'attendance',
            onlineMessage: 'Attendance recorded',
            offlineMessage:
                'Attendance saved offline. It will sync when network returns.',
          );
          break;
        case ScanModule.exam:
          if (examId == null) {
            throw ApiException('Select an exam first', 422);
          }
          final data = await _api.post('/exams/scan', {
            'uid': uid,
            'exam_id': examId,
          });
          lastStudent = StudentProfile.fromJson(
            data['student'] as Map<String, dynamic>,
          );
          lastAllowed = data['allowed'] as bool?;
          lastMessage = lastAllowed == true
              ? 'Exam entry allowed'
              : data['denial_reason'] as String?;
          break;
        case ScanModule.busFare:
          if (busRouteId == null) {
            throw ApiException('Select a bus route first', 422);
          }
          final data = await _api.post('/bus-fare/scan', {
            'uid': uid,
            'bus_route_id': busRouteId,
          });
          lastStudent = StudentProfile.fromJson(
            data['student'] as Map<String, dynamic>,
          );
          lastMessage = 'Fare paid. Ref: ${data['reference']}';
          break;
        case ScanModule.libraryCheckIn:
          await _postOrQueueScan(
            uid: uid,
            path: '/library/check-in',
            body: {'uid': uid},
            moduleName: 'library-check-in',
            onlineMessage: 'Library check-in recorded',
            offlineMessage:
                'Library check-in saved offline. It will sync when network returns.',
          );
          break;
        case ScanModule.libraryCheckOut:
          await _postOrQueueScan(
            uid: uid,
            path: '/library/check-out',
            body: {'uid': uid},
            moduleName: 'library-check-out',
            onlineMessage: 'Library check-out recorded',
            offlineMessage:
                'Library check-out saved offline. It will sync when network returns.',
          );
          break;
      }
    } on ApiException catch (e) {
      lastMessage = e.message;
      lastStudent = null;
    } finally {
      isLoading = false;
      notifyListeners();
    }
  }

  Future<void> _postIdentityScan(String uid) async {
    try {
      final data = await _api.post('/identity/scan', {'uid': uid});
      await _cacheStudentFromResponse(uid, data);
      lastMessage = 'Identity verified';
      await syncQueuedActions();
    } on NetworkException {
      final cached = await _offline.getCachedIdentity(uid);
      if (cached == null) {
        throw ApiException(
          'No network and this Passa Card is not cached on this device.',
          0,
        );
      }

      lastStudent = cached.student;
      lastMessage = 'Offline identity verified from local cache';
    }
  }

  Future<void> _postOrQueueScan({
    required String uid,
    required String path,
    required Map<String, dynamic> body,
    required String moduleName,
    required String onlineMessage,
    required String offlineMessage,
  }) async {
    try {
      final data = await _api.post(path, body);
      await _cacheStudentFromResponse(uid, data);
      lastMessage = data['message'] as String? ?? onlineMessage;
      await syncQueuedActions();
    } on NetworkException {
      final cached = await _offline.getCachedIdentity(uid);
      if (cached == null) {
        throw ApiException(
          'No network and this Passa Card is not cached on this device.',
          0,
        );
      }

      await _offline.enqueue(
        OfflineAction.create(path: path, body: body, module: moduleName),
      );

      pendingSyncCount = await _offline.pendingCount();
      lastStudent = cached.student;
      lastMessage = offlineMessage;
    }
  }

  Future<void> _cacheStudentFromResponse(
    String uid,
    Map<String, dynamic> data,
  ) async {
    final studentData = data['student'] as Map<String, dynamic>;
    final student = StudentProfile.fromJson(studentData);

    lastStudent = student;

    await _offline.cacheIdentity(
      uid: uid,
      student: student,
      card: data['card'] is Map
          ? Map<String, dynamic>.from(data['card'] as Map)
          : null,
    );
  }

  Future<void> logout() async {
    try {
      await _api.post('/auth/logout', {});
    } catch (_) {}
    await _api.clearToken();
    token = null;
    userName = null;
    roles = [];
    lastStudent = null;
    notifyListeners();
  }
}
