import 'package:flutter_test/flutter_test.dart';
import 'package:school_identity_nfc/main.dart';

void main() {
  testWidgets('App loads login screen', (tester) async {
    await tester.pumpWidget(const SchoolIdentityApp());
    expect(find.text('School Identity Passa'), findsOneWidget);
    expect(find.text('Sign In'), findsOneWidget);
  });
}
