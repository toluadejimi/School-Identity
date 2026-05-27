import 'package:flutter/material.dart';

import '../models/student.dart';

class StudentDetailScreen extends StatelessWidget {
  const StudentDetailScreen({
    super.key,
    required this.student,
    this.message,
    this.allowed,
  });

  final StudentProfile student;
  final String? message;
  final bool? allowed;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).colorScheme.surfaceContainerLowest,
      appBar: AppBar(
        title: const Text('Student Details'),
        centerTitle: true,
        backgroundColor: Colors.transparent,
      ),
      body: CustomScrollView(
        slivers: [
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  if (message != null) ...[
                    _ResultBanner(message: message!, allowed: allowed),
                    const SizedBox(height: 16),
                  ],
                  _ProfileHeader(student: student),
                  const SizedBox(height: 16),
                  _InfoSection(
                    title: 'Academic Information',
                    icon: Icons.school_outlined,
                    children: [
                      _InfoTile(label: 'Matric No.', value: student.studentNumber),
                      _InfoTile(label: 'Session', value: student.session),
                      _InfoTile(label: 'Faculty', value: student.faculty),
                      _InfoTile(label: 'Department', value: student.department),
                      _InfoTile(label: 'Level', value: student.level),
                      _InfoTile(label: 'Class / Programme', value: student.className),
                    ],
                  ),
                  const SizedBox(height: 12),
                  _InfoSection(
                    title: 'Contact Information',
                    icon: Icons.contact_phone_outlined,
                    children: [
                      _InfoTile(label: 'Phone', value: student.phone),
                      _InfoTile(label: 'Email', value: student.email),
                      _InfoTile(label: 'Address', value: student.address),
                      _InfoTile(label: 'Guardian', value: student.guardianName),
                      _InfoTile(label: 'Guardian Phone', value: student.guardianPhone),
                    ],
                  ),
                  const SizedBox(height: 12),
                  _InfoSection(
                    title: 'Medical Notes',
                    icon: Icons.local_hospital_outlined,
                    children: [
                      _InfoTile(label: 'Blood Group', value: student.bloodGroup),
                      _InfoTile(label: 'Allergies', value: student.allergies),
                      _InfoTile(label: 'Medical Notes', value: student.medicalNotes),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _ProfileHeader extends StatelessWidget {
  const _ProfileHeader({
    required this.student,
  });

  final StudentProfile student;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Card(
      elevation: 0,
      color: colorScheme.primaryContainer,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(28)),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            CircleAvatar(
              radius: 54,
              backgroundColor: colorScheme.surface,
              backgroundImage: student.photoUrl != null ? NetworkImage(student.photoUrl!) : null,
              child: student.photoUrl == null
                  ? Icon(Icons.person, size: 54, color: colorScheme.primary)
                  : null,
            ),
            const SizedBox(height: 16),
            Text(
              student.fullName,
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                    fontWeight: FontWeight.w800,
                    color: colorScheme.onPrimaryContainer,
                  ),
            ),
            const SizedBox(height: 4),
            Text(
              student.studentNumber,
              style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                    color: colorScheme.onPrimaryContainer.withValues(alpha: 0.78),
                  ),
            ),
            const SizedBox(height: 16),
            Wrap(
              alignment: WrapAlignment.center,
              spacing: 8,
              runSpacing: 8,
              children: [
                _Pill(
                  icon: Icons.verified_user_outlined,
                  label: student.status.toUpperCase(),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _ResultBanner extends StatelessWidget {
  const _ResultBanner({
    required this.message,
    this.allowed,
  });

  final String message;
  final bool? allowed;

  @override
  Widget build(BuildContext context) {
    final isDenied = allowed == false;
    final isAllowed = allowed == true;
    final color = isDenied
        ? Colors.red
        : isAllowed
            ? Colors.green
            : Theme.of(context).colorScheme.primary;

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.10),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withValues(alpha: 0.25)),
      ),
      child: Row(
        children: [
          Icon(
            isDenied ? Icons.cancel_outlined : Icons.check_circle_outline,
            color: color,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              message,
              style: TextStyle(color: color, fontWeight: FontWeight.w700),
            ),
          ),
        ],
      ),
    );
  }
}

class _InfoSection extends StatelessWidget {
  const _InfoSection({
    required this.title,
    required this.icon,
    required this.children,
  });

  final String title;
  final IconData icon;
  final List<_InfoTile> children;

  @override
  Widget build(BuildContext context) {
    final visibleChildren = children.where((child) => child.hasValue).toList();
    if (visibleChildren.isEmpty) return const SizedBox.shrink();

    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(22)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon, color: Theme.of(context).colorScheme.primary),
                const SizedBox(width: 10),
                Text(
                  title,
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                        fontWeight: FontWeight.w800,
                      ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            ...visibleChildren,
          ],
        ),
      ),
    );
  }
}

class _InfoTile extends StatelessWidget {
  const _InfoTile({
    required this.label,
    required this.value,
  });

  final String label;
  final String? value;

  bool get hasValue => value != null && value!.trim().isNotEmpty;

  @override
  Widget build(BuildContext context) {
    if (!hasValue) return const SizedBox.shrink();

    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: Theme.of(context).textTheme.labelMedium?.copyWith(
                  color: Theme.of(context).colorScheme.onSurfaceVariant,
                  fontWeight: FontWeight.w700,
                ),
          ),
          const SizedBox(height: 3),
          Text(
            value!,
            style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                  fontWeight: FontWeight.w600,
                ),
          ),
        ],
      ),
    );
  }
}

class _Pill extends StatelessWidget {
  const _Pill({
    required this.icon,
    required this.label,
  });

  final IconData icon;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.surface.withValues(alpha: 0.82),
        borderRadius: BorderRadius.circular(999),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 16),
          const SizedBox(width: 6),
          Text(label, style: const TextStyle(fontWeight: FontWeight.w800)),
        ],
      ),
    );
  }
}
