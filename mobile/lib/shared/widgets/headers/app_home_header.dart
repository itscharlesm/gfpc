import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class AppHomeHeader extends StatelessWidget implements PreferredSizeWidget {
  final String? imageUrl;

  const AppHomeHeader({
    super.key,
    this.imageUrl,
  });

  @override
  Size get preferredSize => const Size.fromHeight(60);

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: AppTheme.headerDecoration,
      child: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16),
          child: SizedBox(
            height: 60,
            child: Row(
              children: [
                CircleAvatar(
                  radius: 16,
                  backgroundColor: AppTheme.primaryRed,
                  backgroundImage: imageUrl != null && imageUrl!.isNotEmpty
                      ? NetworkImage(imageUrl!)
                      : null,
                  child: (imageUrl == null || imageUrl!.isEmpty)
                      ? const Icon(
                          Icons.person,
                          color: Colors.white,
                          size: 18,
                        )
                      : null,
                ),

                const Spacer(), // 🔥 pushes menu to right

                Builder(
                  builder: (context) => IconButton(
                    icon: const Icon(
                      Icons.menu_rounded,
                      color: AppTheme.primaryRed,
                      size: 30,
                    ),
                    onPressed: () => Scaffold.of(context).openDrawer(),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}