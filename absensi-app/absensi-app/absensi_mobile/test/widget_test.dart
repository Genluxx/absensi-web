import 'package:flutter_test/flutter_test.dart';
import 'package:absensi_mobile/main.dart';

void main() {
  testWidgets('App starts without crashing', (WidgetTester tester) async {
    await tester.pumpWidget(const AbsensiApp());
    await tester.pump(const Duration(milliseconds: 100));
  });
}