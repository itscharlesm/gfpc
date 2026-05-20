import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class ProfileMenuItem {
  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;
  final Color? color;

  ProfileMenuItem({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
    this.color,
  });
}

class ProfileMenuList extends StatelessWidget {
  final String title;
  final List<ProfileMenuItem> items;

  const ProfileMenuList({
    super.key,
    required this.title,
    required this.items,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title.toUpperCase(),
          style: const TextStyle(
            color: AppTheme.gray,
            fontSize: 13,
            fontWeight: FontWeight.bold,
            letterSpacing: 0.4,
          ),
        ),

        const SizedBox(height: 6),

        Column(
          children: List.generate(items.length, (index) {
            final item = items[index];
            final itemColor = item.color ?? AppTheme.black;
            final isLast = index == items.length - 1;

            return Column(
              children: [
                Material(
                  color: Colors.transparent,
                  child: InkWell(
                    onTap: item.onTap,
                    borderRadius: BorderRadius.circular(10),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 2,
                        vertical: 14,
                      ),
                      child: Row(
                        children: [
                          Icon(
                            item.icon,
                            color: itemColor,
                            size: 24,
                          ),

                          const SizedBox(width: 16),

                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  item.title,
                                  style: TextStyle(
                                    color: item.color ?? AppTheme.black,
                                    fontSize: 15.5,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                                const SizedBox(height: 3),
                                Text(
                                  item.subtitle,
                                  style: const TextStyle(
                                    color: AppTheme.gray,
                                    fontSize: 13,
                                  ),
                                ),
                              ],
                            ),
                          ),

                          const Icon(
                            Icons.chevron_right_rounded,
                            color: AppTheme.gray,
                            size: 24,
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                if (!isLast)
                  const Divider(
                    height: 1,
                    thickness: 1,
                    color: AppTheme.borderGray,
                  ),
              ],
            );
          }),
        ),
      ],
    );
  }
}