import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/app_state.dart';
import 'register_student_screen.dart';
import 'scan_screen.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();
    final colorScheme = Theme.of(context).colorScheme;
    final actions = _actionsForRoles(context, app);
    final showCollections = _hasAnyRole(app, [
      'admin',
      'transport',
      'driver',
      'logistics',
    ]);

    return Scaffold(
      body: SafeArea(
        child: CustomScrollView(
          slivers: [
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(20, 12, 20, 24),
              sliver: SliverList(
                delegate: SliverChildListDelegate([
                  Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'School Identity',
                              style: Theme.of(context).textTheme.headlineSmall
                                  ?.copyWith(
                                    fontWeight: FontWeight.w900,
                                    color: const Color(0xFF102A22),
                                  ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'Passa Card operations dashboard',
                              style: Theme.of(context).textTheme.bodyMedium
                                  ?.copyWith(
                                    color: colorScheme.onSurfaceVariant,
                                  ),
                            ),
                          ],
                        ),
                      ),
                      IconButton.filledTonal(
                        onPressed: () => app.logout(),
                        icon: const Icon(Icons.logout),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  _WelcomeCard(
                    userName: app.userName ?? 'Staff',
                    roles: app.roles,
                  ),
                  if (app.pendingSyncCount > 0 || app.isSyncing) ...[
                    const SizedBox(height: 14),
                    _OfflineSyncCard(
                      pendingCount: app.pendingSyncCount,
                      isSyncing: app.isSyncing,
                      onSync: () =>
                          context.read<AppState>().syncQueuedActions(),
                    ),
                  ],
                  if (showCollections) ...[
                    const SizedBox(height: 14),
                    const _CollectionCard(),
                  ],
                  const SizedBox(height: 24),
                  Text(
                    'Quick Actions',
                    style: Theme.of(context).textTheme.titleLarge?.copyWith(
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const SizedBox(height: 12),
                ]),
              ),
            ),
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(20, 0, 20, 24),
              sliver: SliverGrid(
                delegate: SliverChildBuilderDelegate((context, index) {
                  final action = actions[index];
                  return _ModuleCard(action: action);
                }, childCount: actions.length),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  mainAxisSpacing: 14,
                  crossAxisSpacing: 14,
                  mainAxisExtent: 146,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _openAttendance(BuildContext context) async {
    final app = context.read<AppState>();
    if (app.attendanceSessions.isEmpty) await app.loadReferenceData();
    if (!context.mounted) return;

    final selected = await showModalBottomSheet<int>(
      context: context,
      showDragHandle: true,
      builder: (ctx) => ListView(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
        children: app.attendanceSessions
            .map(
              (s) => ListTile(
                title: Text(s['name'].toString()),
                subtitle: Text('${s['class_name']} • ${s['session_date']}'),
                onTap: () => Navigator.pop(ctx, s['id'] as int),
              ),
            )
            .toList(),
      ),
    );

    if (selected != null && context.mounted) {
      app.module = ScanModule.attendance;
      app.attendanceSessionId = selected;
      Navigator.of(
        context,
      ).push(MaterialPageRoute(builder: (_) => const ScanScreen()));
    }
  }

  Future<void> _openExam(BuildContext context) async {
    final app = context.read<AppState>();
    if (app.exams.isEmpty) await app.loadReferenceData();
    if (!context.mounted) return;

    final selected = await showModalBottomSheet<int>(
      context: context,
      showDragHandle: true,
      builder: (ctx) => ListView(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
        children: app.exams
            .map(
              (e) => ListTile(
                title: Text(e['name'].toString()),
                subtitle: Text('${e['subject']} • ${e['exam_date']}'),
                onTap: () => Navigator.pop(ctx, e['id'] as int),
              ),
            )
            .toList(),
      ),
    );

    if (selected != null && context.mounted) {
      app.module = ScanModule.exam;
      app.examId = selected;
      Navigator.of(
        context,
      ).push(MaterialPageRoute(builder: (_) => const ScanScreen()));
    }
  }

  Future<void> _openBusFare(BuildContext context) async {
    final app = context.read<AppState>();
    if (app.busRoutes.isEmpty) await app.loadReferenceData();
    if (!context.mounted) return;

    final selected = await showModalBottomSheet<int>(
      context: context,
      showDragHandle: true,
      builder: (ctx) => ListView(
        padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
        children: app.busRoutes
            .map(
              (r) => ListTile(
                title: Text(r['name'].toString()),
                subtitle: Text('Fare: ₦${r['fare_amount']}'),
                onTap: () => Navigator.pop(ctx, r['id'] as int),
              ),
            )
            .toList(),
      ),
    );

    if (selected != null && context.mounted) {
      app.module = ScanModule.busFare;
      app.busRouteId = selected;
      Navigator.of(
        context,
      ).push(MaterialPageRoute(builder: (_) => const ScanScreen()));
    }
  }

  List<_DashboardAction> _actionsForRoles(BuildContext context, AppState app) {
    final actions = <_DashboardAction>[];

    void add(_DashboardAction action) {
      if (actions.every((existing) => existing.title != action.title)) {
        actions.add(action);
      }
    }

    final isAdmin = _hasAnyRole(app, ['admin']);

    if (isAdmin || _hasAnyRole(app, ['clinic'])) {
      add(_registerAction(context));
      add(_identityAction(context, app));
      add(
        _scanAction(
          context: context,
          app: app,
          title: 'Clinic Visit',
          subtitle: 'Check-in student',
          icon: Icons.local_hospital_outlined,
          accentColor: const Color(0xFFB14D5A),
          module: ScanModule.clinic,
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Medical Records',
          subtitle: 'Reports & history',
          icon: Icons.assignment_outlined,
          accentColor: const Color(0xFFB14D5A),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Doctor Notes',
          subtitle: 'Attending doctor',
          icon: Icons.note_alt_outlined,
          accentColor: const Color(0xFFB14D5A),
        ),
      );
    }

    if (isAdmin ||
        _hasAnyRole(app, ['lecture', 'lecturer', 'attendance', 'exam'])) {
      add(_registerAction(context));
      add(_identityAction(context, app));
      add(
        _DashboardAction(
          title: 'Course / Attendance',
          subtitle: 'Class presence',
          icon: Icons.fact_check_outlined,
          accentColor: const Color(0xFF996515),
          onTap: () => _openAttendance(context),
        ),
      );
      add(
        _DashboardAction(
          title: 'Exam Pass',
          subtitle: 'Activate access',
          icon: Icons.school_outlined,
          accentColor: const Color(0xFF5B4B9A),
          onTap: () => _openExam(context),
        ),
      );
    }

    if (isAdmin || _hasAnyRole(app, ['transport', 'driver', 'logistics'])) {
      add(
        _DashboardAction(
          title: 'Pay',
          subtitle: 'Collect bus fare',
          icon: Icons.payments_outlined,
          accentColor: const Color(0xFF0F766E),
          onTap: () => _openBusFare(context),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Transactions',
          subtitle: 'View collections',
          icon: Icons.receipt_long_outlined,
          accentColor: const Color(0xFF0F766E),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Transfer',
          subtitle: 'Bank or merchant',
          icon: Icons.account_balance_outlined,
          accentColor: const Color(0xFF0F766E),
        ),
      );
    }

    if (isAdmin || _hasAnyRole(app, ['security'])) {
      add(_identityAction(context, app));
      add(
        _comingSoonAction(
          context,
          title: 'Report Card',
          subtitle: 'Lost or stolen',
          icon: Icons.report_gmailerrorred_outlined,
          accentColor: const Color(0xFFDC2626),
        ),
      );
    }

    if (isAdmin || _hasAnyRole(app, ['library', 'librarian'])) {
      add(_registerAction(context));
      add(_identityAction(context, app));
      add(
        _scanAction(
          context: context,
          app: app,
          title: 'Check-in',
          subtitle: 'Library entry pass',
          icon: Icons.login_outlined,
          accentColor: const Color(0xFF0E7490),
          module: ScanModule.libraryCheckIn,
        ),
      );
      add(
        _scanAction(
          context: context,
          app: app,
          title: 'Check out',
          subtitle: 'Library exit pass',
          icon: Icons.logout_outlined,
          accentColor: const Color(0xFF0E7490),
          module: ScanModule.libraryCheckOut,
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Card Request',
          subtitle: 'New library pass',
          icon: Icons.add_card_outlined,
          accentColor: const Color(0xFF0E7490),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Book Request',
          subtitle: 'Borrow request',
          icon: Icons.menu_book_outlined,
          accentColor: const Color(0xFF7C3AED),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Enable / Disable',
          subtitle: 'Card access',
          icon: Icons.toggle_on_outlined,
          accentColor: const Color(0xFFDC2626),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Check History',
          subtitle: 'In and out records',
          icon: Icons.history_outlined,
          accentColor: const Color(0xFF315C9A),
        ),
      );
    }

    if (isAdmin || _hasAnyRole(app, ['vendor', 'merchant', 'marchant'])) {
      add(
        _DashboardAction(
          title: 'Pay',
          subtitle: 'Accept payment',
          icon: Icons.point_of_sale_outlined,
          accentColor: const Color(0xFF7C3AED),
          onTap: () => _openBusFare(context),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Transactions',
          subtitle: 'Payment history',
          icon: Icons.receipt_long_outlined,
          accentColor: const Color(0xFF7C3AED),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Bank Transfer',
          subtitle: 'Withdraw funds',
          icon: Icons.account_balance_outlined,
          accentColor: const Color(0xFF7C3AED),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Merchant Transfer',
          subtitle: 'Send to merchant',
          icon: Icons.swap_horiz_outlined,
          accentColor: const Color(0xFF7C3AED),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'New Card',
          subtitle: 'Request card',
          icon: Icons.add_card_outlined,
          accentColor: const Color(0xFF7C3AED),
        ),
      );
    }

    if (isAdmin || _hasAnyRole(app, ['student'])) {
      add(
        _comingSoonAction(
          context,
          title: 'Fund Wallet',
          subtitle: 'Top up account',
          icon: Icons.account_balance_wallet_outlined,
          accentColor: const Color(0xFF174D3C),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Lock Card',
          subtitle: 'Disable card',
          icon: Icons.lock_outline,
          accentColor: const Color(0xFFDC2626),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Card Activities',
          subtitle: 'Where used',
          icon: Icons.history_outlined,
          accentColor: const Color(0xFF315C9A),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Use PIN',
          subtitle: 'Transaction PIN',
          icon: Icons.pin_outlined,
          accentColor: const Color(0xFF996515),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Report Stolen',
          subtitle: 'Block card',
          icon: Icons.report_outlined,
          accentColor: const Color(0xFFDC2626),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'New Card',
          subtitle: 'Request card',
          icon: Icons.add_card_outlined,
          accentColor: const Color(0xFF174D3C),
        ),
      );
      add(
        _comingSoonAction(
          context,
          title: 'Share Contact',
          subtitle: 'Student contact',
          icon: Icons.ios_share_outlined,
          accentColor: const Color(0xFF0F766E),
        ),
      );
    }

    if (actions.isEmpty) {
      add(_identityAction(context, app));
    }

    return actions;
  }

  bool _hasAnyRole(AppState app, List<String> expectedRoles) {
    final normalized = app.roles
        .map((role) => role.toLowerCase().replaceAll('_', '-'))
        .toSet();

    return expectedRoles.any(
      (role) => normalized.contains(role.toLowerCase().replaceAll('_', '-')),
    );
  }

  _DashboardAction _registerAction(BuildContext context) {
    return _DashboardAction(
      title: 'Register',
      subtitle: 'Student & Passa Card',
      icon: Icons.person_add_alt_1_outlined,
      accentColor: const Color(0xFF174D3C),
      onTap: () {
        Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const RegisterStudentScreen()),
        );
      },
    );
  }

  _DashboardAction _identityAction(BuildContext context, AppState app) {
    return _scanAction(
      context: context,
      app: app,
      title: 'Identity',
      subtitle: 'Verify profile',
      icon: Icons.badge_outlined,
      accentColor: const Color(0xFF315C9A),
      module: ScanModule.identity,
    );
  }

  _DashboardAction _scanAction({
    required BuildContext context,
    required AppState app,
    required String title,
    required String subtitle,
    required IconData icon,
    required Color accentColor,
    required ScanModule module,
  }) {
    return _DashboardAction(
      title: title,
      subtitle: subtitle,
      icon: icon,
      accentColor: accentColor,
      onTap: () {
        app.module = module;
        Navigator.of(
          context,
        ).push(MaterialPageRoute(builder: (_) => const ScanScreen()));
      },
    );
  }

  _DashboardAction _comingSoonAction(
    BuildContext context, {
    required String title,
    required String subtitle,
    required IconData icon,
    required Color accentColor,
  }) {
    return _DashboardAction(
      title: title,
      subtitle: subtitle,
      icon: icon,
      accentColor: accentColor,
      onTap: () => _showComingSoon(context, title),
    );
  }

  void _showComingSoon(BuildContext context, String title) {
    showModalBottomSheet<void>(
      context: context,
      showDragHandle: true,
      builder: (context) {
        return Padding(
          padding: const EdgeInsets.fromLTRB(24, 8, 24, 28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: Theme.of(
                  context,
                ).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900),
              ),
              const SizedBox(height: 8),
              Text(
                'This module is ready in the role menu and can be connected to its backend workflow next.',
                style: Theme.of(context).textTheme.bodyMedium,
              ),
            ],
          ),
        );
      },
    );
  }
}

class _DashboardAction {
  const _DashboardAction({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.accentColor,
    required this.onTap,
  });

  final String title;
  final String subtitle;
  final IconData icon;
  final Color accentColor;
  final VoidCallback onTap;
}

class _WelcomeCard extends StatelessWidget {
  const _WelcomeCard({required this.userName, required this.roles});

  final String userName;
  final List<String> roles;

  @override
  Widget build(BuildContext context) {
    final colorScheme = Theme.of(context).colorScheme;

    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF174D3C), Color(0xFF102A22)],
        ),
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF174D3C).withValues(alpha: 0.18),
            blurRadius: 24,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.14),
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Icon(
              Icons.credit_card_rounded,
              color: Colors.white,
              size: 30,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Welcome back',
                  style: TextStyle(color: Colors.white.withValues(alpha: 0.72)),
                ),
                const SizedBox(height: 3),
                Text(
                  userName,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    color: Colors.white,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  roles.isEmpty ? 'Staff access' : roles.join('  •  '),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(color: colorScheme.primaryContainer),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _CollectionCard extends StatelessWidget {
  const _CollectionCard();

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(
          color: const Color(0xFF0F766E).withValues(alpha: 0.10),
        ),
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: const Color(0xFF0F766E).withValues(alpha: 0.10),
              borderRadius: BorderRadius.circular(18),
            ),
            child: const Icon(
              Icons.summarize_outlined,
              color: Color(0xFF0F766E),
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Total Collected',
                  style: Theme.of(context).textTheme.labelLarge?.copyWith(
                    color: Theme.of(context).colorScheme.onSurfaceVariant,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'View from transactions',
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _OfflineSyncCard extends StatelessWidget {
  const _OfflineSyncCard({
    required this.pendingCount,
    required this.isSyncing,
    required this.onSync,
  });

  final int pendingCount;
  final bool isSyncing;
  final VoidCallback onSync;

  @override
  Widget build(BuildContext context) {
    final color = Theme.of(context).colorScheme.primary;

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: color.withValues(alpha: 0.14)),
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.10),
              borderRadius: BorderRadius.circular(18),
            ),
            child: Icon(Icons.cloud_sync_outlined, color: color),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  isSyncing
                      ? 'Syncing offline records'
                      : '$pendingCount pending sync',
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Saved offline and ready to push to cloud.',
                  style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: Theme.of(context).colorScheme.onSurfaceVariant,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 10),
          IconButton.filledTonal(
            onPressed: isSyncing ? null : onSync,
            icon: isSyncing
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  )
                : const Icon(Icons.sync),
          ),
        ],
      ),
    );
  }
}

class _ModuleCard extends StatelessWidget {
  const _ModuleCard({required this.action});

  final _DashboardAction action;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(22),
      child: InkWell(
        borderRadius: BorderRadius.circular(22),
        onTap: action.onTap,
        child: Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(22),
            border: Border.all(
              color: action.accentColor.withValues(alpha: 0.10),
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: action.accentColor.withValues(alpha: 0.10),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Icon(action.icon, color: action.accentColor, size: 24),
              ),
              const SizedBox(height: 10),
              Text(
                action.title,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                  fontSize: 15,
                  fontWeight: FontWeight.w900,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                action.subtitle,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  fontSize: 11,
                  color: Theme.of(context).colorScheme.onSurfaceVariant,
                ),
              ),
              const Spacer(),
              Align(
                alignment: Alignment.centerRight,
                child: Icon(
                  Icons.arrow_forward_rounded,
                  color: action.accentColor,
                  size: 20,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
