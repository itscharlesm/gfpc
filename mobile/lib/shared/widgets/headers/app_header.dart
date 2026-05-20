import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class AppHeader extends StatelessWidget implements PreferredSizeWidget {
  final Widget? left;
  final Widget? center;
  final Widget? right;
  final double height;

  const AppHeader({
    super.key,
    this.left,
    this.center,
    this.right,
    this.height = 60,
  });

  @override
  Size get preferredSize => Size.fromHeight(height);

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: AppTheme.headerDecoration,
      child: SafeArea(
        child: SizedBox(
          height: height,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Row(
              children: [
                Expanded(
                  child: left ?? const SizedBox(),
                ),

                if (center != null)
                  Expanded(
                    child: Center(child: center!),
                  ),

                SizedBox(
                  width: 48,
                  child: Align(
                    alignment: Alignment.centerRight,
                    child: right ?? const SizedBox(),
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