import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';

import '../models/student.dart';
import '../services/api_service.dart';
import '../services/nfc_service.dart';
import 'student_detail_screen.dart';

class RegisterStudentScreen extends StatefulWidget {
  const RegisterStudentScreen({super.key});

  @override
  State<RegisterStudentScreen> createState() => _RegisterStudentScreenState();
}

class _RegisterStudentScreenState extends State<RegisterStudentScreen> {
  final _formKey = GlobalKey<FormState>();
  final _api = ApiService();
  final _nfc = NfcService();
  final _picker = ImagePicker();

  final _uid = TextEditingController();
  final _matricNo = TextEditingController();
  final _firstName = TextEditingController();
  final _lastName = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _session = TextEditingController();
  final _faculty = TextEditingController();
  final _department = TextEditingController();
  final _level = TextEditingController();
  final _className = TextEditingController();

  XFile? _photo;
  bool _isScanning = false;
  bool _isSaving = false;

  @override
  void dispose() {
    _nfc.stopSession();
    for (final controller in [
      _uid,
      _matricNo,
      _firstName,
      _lastName,
      _email,
      _phone,
      _session,
      _faculty,
      _department,
      _level,
      _className,
    ]) {
      controller.dispose();
    }
    super.dispose();
  }

  Future<void> _capturePhoto() async {
    final photo = await _picker.pickImage(
      source: ImageSource.camera,
      imageQuality: 75,
      maxWidth: 1200,
    );
    if (photo != null && mounted) {
      setState(() => _photo = photo);
    }
  }

  Future<void> _scanCard() async {
    final available = await _nfc.isAvailable();
    if (!available) {
      _showMessage('Passa Card reader is not available on this device.');
      return;
    }

    setState(() => _isScanning = true);

    await _nfc.startSession((uid) async {
      await _nfc.stopSession();
      if (!mounted) return;
      setState(() {
        _uid.text = uid;
        _isScanning = false;
      });
      _showMessage('Passa Card captured: $uid');
    });
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSaving = true);

    try {
      final data = await _api.multipartPost(
        '/students/register',
        {
          'uid': _uid.text.trim(),
          'student_number': _matricNo.text.trim(),
          'first_name': _firstName.text.trim(),
          'last_name': _lastName.text.trim(),
          'email': _email.text.trim(),
          'phone': _phone.text.trim(),
          'session': _session.text.trim(),
          'faculty': _faculty.text.trim(),
          'department': _department.text.trim(),
          'level': _level.text.trim(),
          'class_name': _className.text.trim(),
        },
        filePath: _photo?.path,
      );

      final student = StudentProfile.fromJson(data['student'] as Map<String, dynamic>);

      if (!mounted) return;
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(
          builder: (_) => StudentDetailScreen(
            student: student,
            message: data['message'] as String? ?? 'Student registered',
          ),
        ),
      );
    } on ApiException catch (e) {
      _showMessage(e.message);
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  void _showMessage(String message) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(message)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Register Student')),
      backgroundColor: Theme.of(context).colorScheme.surfaceContainerLowest,
      body: Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
          children: [
            Text(
              'Create student profile',
              style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                    fontWeight: FontWeight.w900,
                  ),
            ),
            const SizedBox(height: 6),
            Text(
              'Capture photo, map Passa Card, and add key academic information.',
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: Theme.of(context).colorScheme.onSurfaceVariant,
                  ),
            ),
            const SizedBox(height: 18),
            _photoAndNfcSection(),
            const SizedBox(height: 12),
            _section(
              title: 'Personal Information',
              children: [
                _field(_firstName, 'First name', required: true),
                _field(_lastName, 'Last name', required: true),
                _field(_phone, 'Phone', keyboardType: TextInputType.phone),
                _field(_email, 'Email', keyboardType: TextInputType.emailAddress),
              ],
            ),
            _section(
              title: 'Academic Information',
              children: [
                _field(_matricNo, 'Matric no.', required: true),
                _field(_session, 'Session (e.g. 2025/2026)', required: true),
                _field(_faculty, 'Faculty'),
                _field(_department, 'Department'),
                _field(_level, 'Level'),
                _field(_className, 'Class / programme'),
              ],
            ),
            const SizedBox(height: 20),
            FilledButton.icon(
              onPressed: _isSaving ? null : _submit,
              icon: _isSaving
                  ? const SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    )
                  : const Icon(Icons.save),
              label: Text(_isSaving ? 'Registering...' : 'Register Student'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _photoAndNfcSection() {
    return Card(
      elevation: 0,
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            _SectionTitle(icon: Icons.add_card, title: 'Passa Card & Photo'),
            const SizedBox(height: 16),
            Center(
              child: Stack(
                children: [
                  CircleAvatar(
                    radius: 48,
                    backgroundImage: _photo != null ? FileImage(File(_photo!.path)) : null,
                    child: _photo == null ? const Icon(Icons.person, size: 48) : null,
                  ),
                  Positioned(
                    right: 0,
                    bottom: 0,
                    child: IconButton.filled(
                      onPressed: _capturePhoto,
                      icon: const Icon(Icons.camera_alt),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _uid,
              decoration: InputDecoration(
                labelText: 'Passa Card ID',
                prefixIcon: const Icon(Icons.credit_card_rounded),
                suffixIcon: IconButton(
                  onPressed: _isScanning ? null : _scanCard,
                  icon: Icon(_isScanning ? Icons.hourglass_top : Icons.credit_card_rounded),
                ),
              ),
              validator: _required,
            ),
          ],
        ),
      ),
    );
  }

  Widget _section({required String title, required List<Widget> children}) {
    return Card(
      elevation: 0,
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            _SectionTitle(
              icon: title.startsWith('Personal')
                  ? Icons.person_outline
                  : Icons.school_outlined,
              title: title,
            ),
            const SizedBox(height: 12),
            ...children,
          ],
        ),
      ),
    );
  }

  Widget _field(
    TextEditingController controller,
    String label, {
    bool required = false,
    int maxLines = 1,
    TextInputType? keyboardType,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: TextFormField(
        controller: controller,
        maxLines: maxLines,
        keyboardType: keyboardType,
        decoration: InputDecoration(labelText: label),
        validator: required ? _required : null,
      ),
    );
  }

  String? _required(String? value) {
    if (value == null || value.trim().isEmpty) return 'Required';
    return null;
  }
}

class _SectionTitle extends StatelessWidget {
  const _SectionTitle({
    required this.icon,
    required this.title,
  });

  final IconData icon;
  final String title;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Container(
          width: 38,
          height: 38,
          decoration: BoxDecoration(
            color: Theme.of(context).colorScheme.primary.withValues(alpha: 0.10),
            borderRadius: BorderRadius.circular(14),
          ),
          child: Icon(icon, color: Theme.of(context).colorScheme.primary),
        ),
        const SizedBox(width: 10),
        Text(
          title,
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.w900,
              ),
        ),
      ],
    );
  }
}
