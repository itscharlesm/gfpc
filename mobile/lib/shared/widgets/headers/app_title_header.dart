import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class AppTitleHeader extends StatelessWidget implements PreferredSizeWidget {
  final String title;

  const AppTitleHeader({
    super.key,
    required this.title,
  });

  @override
  Size get preferredSize => const Size.fromHeight(50);

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: AppTheme.headerDecoration,
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 8),
          child: SizedBox(
            height: 50,
            child: Row(
              children: [
                const SizedBox(width: 48),
                Expanded(
                  child: Center(
                    child: Text(
                      title,
                      style: const TextStyle(
                        color: AppTheme.primaryRed,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
                IconButton(
                  icon: const Icon(
                    Icons.menu_rounded,
                    color: AppTheme.primaryRed,
                    size: 30,
                  ),
                  onPressed: () => Scaffold.of(context).openDrawer(),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}