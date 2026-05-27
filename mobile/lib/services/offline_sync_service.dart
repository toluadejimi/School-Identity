import 'dart:convert';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';

import '../models/student.dart';

class OfflineSyncService {
  OfflineSyncService({FlutterSecureStorage? storage})
    : _storage = storage ?? const FlutterSecureStorage();

  final FlutterSecureStorage _storage;

  static const _queueKey = 'offline_scan_queue_v1';
  static const _identityPrefix = 'offline_identity_';

  Future<void> cacheIdentity({
    required String uid,
    required StudentProfile student,
    Map<String, dynamic>? card,
  }) async {
    await _storage.write(
      key: _identityKey(uid),
      value: jsonEncode({
        'uid': _normalizeUid(uid),
        'student': student.toJson(),
        'card': card,
        'cached_at': DateTime.now().toIso8601String(),
      }),
    );
  }

  Future<OfflineIdentity?> getCachedIdentity(String uid) async {
    final raw = await _storage.read(key: _identityKey(uid));
    if (raw == null) return null;

    final data = jsonDecode(raw) as Map<String, dynamic>;
    return OfflineIdentity(
      uid: data['uid'] as String,
      student: StudentProfile.fromJson(data['student'] as Map<String, dynamic>),
      cachedAt: DateTime.tryParse(data['cached_at'] as String? ?? ''),
    );
  }

  Future<void> enqueue(OfflineAction action) async {
    final queue = await pendingActions();
    queue.add(action);
    await _writeQueue(queue);
  }

  Future<List<OfflineAction>> pendingActions() async {
    final raw = await _storage.read(key: _queueKey);
    if (raw == null || raw.isEmpty) return [];

    final items = jsonDecode(raw) as List<dynamic>;
    return items
        .map((item) => OfflineAction.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  Future<int> pendingCount() async => (await pendingActions()).length;

  Future<void> replaceQueue(List<OfflineAction> actions) =>
      _writeQueue(actions);

  Future<void> clear() => _storage.delete(key: _queueKey);

  Future<void> _writeQueue(List<OfflineAction> actions) async {
    await _storage.write(
      key: _queueKey,
      value: jsonEncode(actions.map((action) => action.toJson()).toList()),
    );
  }

  String _identityKey(String uid) => '$_identityPrefix${_normalizeUid(uid)}';

  String _normalizeUid(String uid) {
    return uid.replaceAll(RegExp(r'[^0-9A-Fa-f]'), '').toUpperCase();
  }
}

class OfflineIdentity {
  OfflineIdentity({
    required this.uid,
    required this.student,
    required this.cachedAt,
  });

  final String uid;
  final StudentProfile student;
  final DateTime? cachedAt;
}

class OfflineAction {
  OfflineAction({
    required this.id,
    required this.path,
    required this.body,
    required this.module,
    required this.createdAt,
  });

  final String id;
  final String path;
  final Map<String, dynamic> body;
  final String module;
  final DateTime createdAt;

  factory OfflineAction.create({
    required String path,
    required Map<String, dynamic> body,
    required String module,
  }) {
    final now = DateTime.now();
    return OfflineAction(
      id: '${now.microsecondsSinceEpoch}-$module',
      path: path,
      body: body,
      module: module,
      createdAt: now,
    );
  }

  factory OfflineAction.fromJson(Map<String, dynamic> json) {
    return OfflineAction(
      id: json['id'] as String,
      path: json['path'] as String,
      body: Map<String, dynamic>.from(json['body'] as Map),
      module: json['module'] as String,
      createdAt: DateTime.parse(json['created_at'] as String),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'path': path,
      'body': body,
      'module': module,
      'created_at': createdAt.toIso8601String(),
    };
  }
}
