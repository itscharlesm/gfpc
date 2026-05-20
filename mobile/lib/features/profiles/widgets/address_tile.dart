import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class AddressTile extends StatelessWidget {
  final String label;
  final String address;
  final bool isPrimary;

  final VoidCallback onSelect;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  const AddressTile({
    super.key,
    required this.label,
    required this.address,
    required this.isPrimary,
    required this.onSelect,
    required this.onEdit,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(14),
      onTap: onSelect,
      child: Container(
        width: double.infinity,
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppTheme.white,
          borderRadius: BorderRadius.circular(14),

          border: Border.all(
            color: isPrimary
                ? AppTheme.black.withOpacity(0.25)
                : AppTheme.borderGray,
            width: isPrimary ? 1.3 : 1,
          ),
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 🔹 ICON
            CircleAvatar(
              radius: 18,
              backgroundColor: AppTheme.primaryRed.withOpacity(0.10),
              child: Icon(
                _addressIcon(label),
                color: AppTheme.primaryRed,
                size: 19,
              ),
            ),

            const SizedBox(width: 12),

            // 🔹 TEXT CONTENT
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        label,
                        style: const TextStyle(
                          color: AppTheme.black,
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      if (isPrimary) ...[
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 3,
                          ),
                          decoration: BoxDecoration(
                            color: AppTheme.primaryRed.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: const Text(
                            'Primary',
                            style: TextStyle(
                              color: AppTheme.primaryRed,
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ],
                    ],
                  ),

                  const SizedBox(height: 8),

                  Text(
                    address,
                    style: const TextStyle(
                      color: AppTheme.gray,
                      fontSize: 12,
                      height: 1.35,
                    ),
                  ),

                  const SizedBox(height: 8),

                  Row(
                    children: [
                      GestureDetector(
                        onTap: onEdit,
                        child: const Text(
                          'Edit',
                          style: TextStyle(
                            color: AppTheme.primaryRed,
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                      const SizedBox(width: 14),
                      GestureDetector(
                        onTap: onDelete,
                        child: const Text(
                          'Delete',
                          style: TextStyle(
                            color: AppTheme.primaryRed,
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // 🔹 SELECT CHECKBOX
            Checkbox(
              value: isPrimary,
              activeColor: AppTheme.primaryRed,
              onChanged: (_) => onSelect(),
            ),
          ],
        ),
      ),
    );
  }

  IconData _addressIcon(String label) {
    switch (label.toLowerCase()) {
      case 'home':
        return Icons.home_rounded;
      case 'office':
      case 'work':
        return Icons.work_rounded;
      case 'hotel':
        return Icons.apartment_rounded;
      default:
        return Icons.location_on_rounded;
    }
  }
}