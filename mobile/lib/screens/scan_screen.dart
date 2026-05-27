import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/app_state.dart';
import '../services/nfc_service.dart';
import 'student_detail_screen.dart';

class ScanScreen extends StatefulWidget {
  const ScanScreen({super.key});

  @override
  State<ScanScreen> createState() => _ScanScreenState();
}

class _ScanScreenState extends State<ScanScreen> with SingleTickerProviderStateMixin {
  final _nfc = NfcService();
  bool _nfcReady = false;
  String? _lastUid;
  late final AnimationController _scanController;

  @override
  void initState() {
    super.initState();
    _scanController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1800),
    )..repeat();
    _initNfc();
  }

  Future<void> _initNfc() async {
    final available = await _nfc.isAvailable();
    if (!mounted) return;
    setState(() => _nfcReady = available);

    if (available) {
      await _nfc.startSession((uid) async {
        if (_lastUid == uid) return;
        _lastUid = uid;
        await context.read<AppState>().handleScan(uid);
        if (!mounted) return;
        final app = context.read<AppState>();
        if (app.lastStudent != null) {
          Navigator.of(context).push(
            MaterialPageRoute(
              builder: (_) => StudentDetailScreen(
                student: app.lastStudent!,
                message: app.lastMessage,
                allowed: app.lastAllowed,
              ),
            ),
          );
        } else if (app.lastMessage != null) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(app.lastMessage!)),
          );
        }
      });
    }
  }

  @override
  void dispose() {
    _scanController.dispose();
    _nfc.stopSession();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final app = context.watch<AppState>();

    return Scaffold(
      appBar: AppBar(title: const Text('Tap Passa Card')),
      body: SafeArea(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Card(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    _ScanningCardTarget(
                      animation: _scanController,
                      enabled: _nfcReady,
                    ),
                    const SizedBox(height: 24),
                    Text(
                      _nfcReady ? 'Ready for Passa Card' : 'Card Reader Not Available',
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                            fontWeight: FontWeight.w900,
                          ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      _nfcReady
                          ? 'Hold the Passa Card close to this device. Scanning starts automatically.'
                          : 'This device does not support card scanning or the reader is disabled.',
                      textAlign: TextAlign.center,
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                            color: Theme.of(context).colorScheme.onSurfaceVariant,
                          ),
                    ),
                    const SizedBox(height: 20),
                    if (app.isLoading) const CircularProgressIndicator(),
                    if (app.lastMessage != null && !app.isLoading) ...[
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: Theme.of(context).colorScheme.primary.withValues(alpha: 0.08),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Text(app.lastMessage!, textAlign: TextAlign.center),
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _ScanningCardTarget extends StatelessWidget {
  const _ScanningCardTarget({
    required this.animation,
    required this.enabled,
  });

  final Animation<double> animation;
  final bool enabled;

  @override
  Widget build(BuildContext context) {
    final color = enabled ? Theme.of(context).colorScheme.primary : Colors.grey;

    return SizedBox(
      width: 190,
      height: 190,
      child: AnimatedBuilder(
        animation: animation,
        builder: (context, child) {
          final progress = enabled ? animation.value : 0.0;

          return Stack(
            alignment: Alignment.center,
            children: [
              for (var i = 0; i < 3; i++)
                _GlowRing(
                  color: color,
                  progress: (progress + (i * 0.28)) % 1,
                ),
              Container(
                width: 112,
                height: 112,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      color.withValues(alpha: 0.20),
                      color.withValues(alpha: 0.06),
                    ],
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: color.withValues(alpha: enabled ? 0.24 : 0.08),
                      blurRadius: 28,
                      spreadRadius: 4,
                    ),
                  ],
                ),
                child: Icon(Icons.credit_card_rounded, size: 58, color: color),
              ),
            ],
          );
        },
      ),
    );
  }
}

class _GlowRing extends StatelessWidget {
  const _GlowRing({
    required this.color,
    required this.progress,
  });

  final Color color;
  final double progress;

  @override
  Widget build(BuildContext context) {
    final size = 92 + (progress * 82);
    final opacity = (1 - progress).clamp(0.0, 1.0) * 0.32;

    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(
          color: color.withValues(alpha: opacity),
          width: 2,
        ),
      ),
    );
  }
}
