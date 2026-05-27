class ApiConfig {
  // Change to your machine IP when testing on a physical device.
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://127.0.0.1:8000/api/v1',
  );

  static const String deviceUuid = String.fromEnvironment(
    'DEVICE_UUID',
    defaultValue: 'DEMO-DEVICE-001',
  );
}
