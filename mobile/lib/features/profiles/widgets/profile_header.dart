import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class ProfileHeader extends StatelessWidget {
  final String name;
  final String role;
  final String email;
  final VoidCallback onEdit;
  final String? imageUrl;

  const ProfileHeader({
    super.key,
    required this.name,
    required this.role,
    required this.email,
    this.imageUrl,
    required this.onEdit,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(16, 18, 16, 16), // reduced
      decoration: AppTheme.cardDecoration,
      child: Column(
        children: [
          Stack(
            children: [
              CircleAvatar(
              radius: 46,
              backgroundColor: AppTheme.primaryRed,
              backgroundImage: imageUrl != null && imageUrl!.isNotEmpty
                  ? NetworkImage(imageUrl!)
                  : null,
              child: (imageUrl == null || imageUrl!.isEmpty)
                  ? const Icon(
                      Icons.person,
                      color: Colors.white,
                      size: 46,
                    )
                  : null,
            ),
              Positioned(
                bottom: 2,
                right: 2,
                child: Container(
                  padding: const EdgeInsets.all(5), // maller
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.camera_alt,
                    color: AppTheme.primaryRed,
                    size: 14, // smaller
                  ),
                ),
              ),
            ],
          ),

          const SizedBox(height: 10), // reduced

          Text(
            name,
            style: const TextStyle(
              fontSize: 18, // slightly smaller
              fontWeight: FontWeight.w700,
            ),
          ),

          const SizedBox(height: 2),

          Text(
            role,
            style: const TextStyle(
              color: AppTheme.primaryRed,
              fontWeight: FontWeight.w600,
              fontSize: 13,
            ),
          ),

          const SizedBox(height: 2),

          Text(
            email,
            style: const TextStyle(
              color: AppTheme.gray,
              fontSize: 12,
            ),
          ),
        ],
      ),
    );
  }
}