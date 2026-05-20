import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class ProfileInfoCard extends StatelessWidget {
  final String phone;
  final String address;

  const ProfileInfoCard({
    super.key,
    required this.phone,
    required this.address,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: AppTheme.cardDecoration,
      child: Column(
        children: [
          _infoRow(Icons.phone_outlined, "Phone Number", phone),
            Divider(
              height: 28,
              color: AppTheme.borderGray,
              thickness: 1,
            ),
          _infoRow(Icons.location_on_outlined, "Address", address),
        ],
      ),
    );
  }

  Widget _infoRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, color: AppTheme.primaryRed, size: 23),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: const TextStyle(color: AppTheme.gray)),
              const SizedBox(height: 3),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
} 