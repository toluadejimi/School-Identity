import 'package:nfc_manager/nfc_manager.dart';
import 'package:nfc_manager/nfc_manager_android.dart';
import 'package:nfc_manager/nfc_manager_ios.dart';

class NfcService {
  Future<bool> isAvailable() async {
    final availability = await NfcManager.instance.checkAvailability();
    return availability == NfcAvailability.enabled;
  }

  Future<void> startSession(void Function(String uid) onDiscovered) async {
    await NfcManager.instance.startSession(
      pollingOptions: {NfcPollingOption.iso14443, NfcPollingOption.iso15693},
      onDiscovered: (NfcTag tag) async {
        final uid = _extractUid(tag);
        if (uid != null) {
          onDiscovered(uid);
        }
      },
    );
  }

  Future<void> stopSession() => NfcManager.instance.stopSession();

  String? _extractUid(NfcTag tag) {
    final android = NfcTagAndroid.from(tag);
    if (android != null) {
      return _bytesToHex(android.id);
    }

    final iosMifare = MiFareIos.from(tag);
    if (iosMifare != null) {
      return _bytesToHex(iosMifare.identifier);
    }

    final iosIso15693 = Iso15693Ios.from(tag);
    if (iosIso15693 != null) {
      return _bytesToHex(iosIso15693.identifier);
    }

    return null;
  }

  String _bytesToHex(List<int> bytes) {
    return bytes.map((b) => b.toRadixString(16).padLeft(2, '0')).join().toUpperCase();
  }
}
