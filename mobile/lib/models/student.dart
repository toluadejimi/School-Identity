class StudentProfile {
  StudentProfile({
    required this.id,
    required this.studentNumber,
    required this.fullName,
    this.email,
    this.phone,
    this.address,
    this.guardianName,
    this.guardianPhone,
    this.className,
    this.department,
    this.session,
    this.faculty,
    this.level,
    this.photoUrl,
    this.bloodGroup,
    this.allergies,
    this.medicalNotes,
    required this.status,
    required this.walletBalance,
    required this.walletCurrency,
  });

  final int id;
  final String studentNumber;
  final String fullName;
  final String? email;
  final String? phone;
  final String? address;
  final String? guardianName;
  final String? guardianPhone;
  final String? className;
  final String? department;
  final String? session;
  final String? faculty;
  final String? level;
  final String? photoUrl;
  final String? bloodGroup;
  final String? allergies;
  final String? medicalNotes;
  final String status;
  final String walletBalance;
  final String walletCurrency;

  factory StudentProfile.fromJson(Map<String, dynamic> json) {
    return StudentProfile(
      id: json['id'] as int,
      studentNumber: json['student_number'] as String,
      fullName: json['full_name'] as String,
      email: json['email'] as String?,
      phone: json['phone'] as String?,
      address: json['address'] as String?,
      guardianName: json['guardian_name'] as String?,
      guardianPhone: json['guardian_phone'] as String?,
      className: json['class_name'] as String?,
      department: json['department'] as String?,
      session: json['session'] as String?,
      faculty: json['faculty'] as String?,
      level: json['level'] as String?,
      photoUrl: json['photo_url'] as String?,
      bloodGroup: json['blood_group'] as String?,
      allergies: json['allergies'] as String?,
      medicalNotes: json['medical_notes'] as String?,
      status: json['status'] as String,
      walletBalance: json['wallet_balance']?.toString() ?? '0.00',
      walletCurrency: json['wallet_currency'] as String? ?? 'NGN',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'student_number': studentNumber,
      'full_name': fullName,
      'email': email,
      'phone': phone,
      'address': address,
      'guardian_name': guardianName,
      'guardian_phone': guardianPhone,
      'class_name': className,
      'department': department,
      'session': session,
      'faculty': faculty,
      'level': level,
      'photo_url': photoUrl,
      'blood_group': bloodGroup,
      'allergies': allergies,
      'medical_notes': medicalNotes,
      'status': status,
      'wallet_balance': walletBalance,
      'wallet_currency': walletCurrency,
    };
  }
}
