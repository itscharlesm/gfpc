import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/config/api_config.dart';
import 'package:mobile_app/features/auth/pages/login_page.dart';
import 'package:mobile_app/features/home/pages/client_home_page.dart';
import 'package:mobile_app/features/home/pages/technician_home_page.dart';
import 'package:mobile_app/features/profiles/pages/client_profile_page.dart';
import 'package:mobile_app/features/profiles/pages/technician_profile_page.dart';
import 'package:mobile_app/shared/widgets/dialogs/app_confirmation_dialog.dart';
import 'package:mobile_app/features/bookings/pages/client_booking_location_page.dart';
import 'package:mobile_app/features/appointments/pages/client_appointments_page.dart';

class AppDrawer extends StatefulWidget {
  final int userType; // 3 = Client, 2 = Technician
  final String email;
  final String? name;
  final VoidCallback? onLogout;
  final String currentPage;

  const AppDrawer({
    super.key,
    required this.userType,
    required this.email,
    required this.currentPage,
    this.name,
    this.onLogout,
  });

  @override
  State<AppDrawer> createState() => _AppDrawerState();
}

class _AppDrawerState extends State<AppDrawer> {
  String? fullName;
  String? profileImagePath;

  @override
  void initState() {
    super.initState();
    _fetchDrawerProfile();
  }

  String _formatName(String value) {
    if (value.isEmpty) return '';

    return value
        .toLowerCase()
        .split(' ')
        .map(
          (word) =>
              word.isNotEmpty ? word[0].toUpperCase() + word.substring(1) : '',
        )
        .join(' ');
  }

  Future<void> _fetchDrawerProfile() async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/profile'),
        headers: {
          'Accept': 'application/json',
        },
        body: {
          'email': widget.email,
        },
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 200 && data['success'] == true) {
        final user = data['data'];

        final firstName = _formatName(user['usr_first_name'] ?? '');
        final lastName = _formatName(user['usr_last_name'] ?? '');

        setState(() {
          fullName = '$firstName $lastName'.trim();
          profileImagePath = user['usr_image_path'];
        });
      }
    } catch (_) {
      // Keep default drawer UI if profile fetch fails.
    }
  }

  String? get profileImageUrl {
    if (profileImagePath == null || profileImagePath!.isEmpty) {
      return null;
    }

    return '${ApiConfig.baseUrl}/$profileImagePath';
  }

  void _logout(BuildContext context) {
    Navigator.pushAndRemoveUntil(
      context,
      MaterialPageRoute(
        builder: (_) => const LoginPage(),
      ),
      (route) => false,
    );
  }

  @override
  Widget build(BuildContext context) {
    final bool isClient = widget.userType == 3;
    final bool isTechnician = widget.userType == 2;

    return Drawer(
      backgroundColor: AppTheme.white,
      child: Column(
        children: [
          _drawerTopImage(),
          _profileSection(isClient, isTechnician),

          Expanded(
            child: ListView(
              padding: const EdgeInsets.fromLTRB(12, 0, 12, 0),
              children: [
                _drawerItem(
                  icon: Icons.home_rounded,
                  title: 'Home',
                  isSelected: widget.currentPage == 'home',
                  onTap: () {
                    Navigator.pop(context);

                    if (widget.currentPage != 'home') {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => widget.userType == 3
                              ? ClientHomePage(email: widget.email)
                              : TechnicianHomePage(email: widget.email),
                        ),
                      );
                    }
                  },
                ),

                _drawerItem(
                  icon: Icons.person_rounded,
                  title: 'Profile',
                  isSelected: widget.currentPage == 'profile',
                  onTap: () {
                    Navigator.pop(context);

                    if (widget.currentPage != 'profile') {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => widget.userType == 3
                              ? ClientProfilePage(email: widget.email)
                              : TechnicianProfilePage(email: widget.email),
                        ),
                      );
                    }
                  },
                ),

                if (isClient) ...[
                  _drawerItem(
                    icon: Icons.add_circle_outline_rounded,
                    title: 'Book Service',
                    isSelected: widget.currentPage == 'book_service',
                    onTap: () {
                      Navigator.pop(context);

                      if (widget.currentPage != 'book_service') {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => ClientBookingLocationPage(
                              email: widget.email,
                            ),
                          ),
                        );
                      }
                    },
                  ),
                  _drawerItem(
                    icon: Icons.calendar_month_rounded,
                    title: 'My Appointments',
                    isSelected: widget.currentPage == 'appointments',
                    onTap: () {
                      Navigator.pop(context);

                      if (widget.currentPage != 'appointments') {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => ClientAppointmentsPage(
                              email: widget.email,
                            ),
                          ),
                        );
                      }
                    },
                  ),
                  _drawerItem(
                    icon: Icons.history_rounded,
                    title: 'Service History',
                    onTap: () => Navigator.pop(context),
                  ),
                ],

                if (isTechnician) ...[
                  _drawerItem(
                    icon: Icons.assignment_rounded,
                    title: 'Assigned Jobs',
                    onTap: () => Navigator.pop(context),
                  ),
                  _drawerItem(
                    icon: Icons.location_on_rounded,
                    title: 'Job Locations',
                    onTap: () => Navigator.pop(context),
                  ),
                  _drawerItem(
                    icon: Icons.edit_note_rounded,
                    title: 'Service Reports',
                    onTap: () => Navigator.pop(context),
                  ),
                ],

                _drawerItem(
                  icon: Icons.settings_rounded,
                  title: 'Settings',
                  onTap: () => Navigator.pop(context),
                ),
              ],
            ),
          ),

          _divider(),

          Padding(
            padding: const EdgeInsets.fromLTRB(12, 0, 12, 40),
            child: _drawerItem(
              icon: Icons.logout_rounded,
              title: 'Logout',
              color: AppTheme.primaryRed,
              onTap: () {
                final navigator = Navigator.of(context, rootNavigator: true);

                Navigator.pop(context);

                Future.delayed(const Duration(milliseconds: 150), () {
                  showDialog(
                    context: navigator.context,
                    barrierDismissible: false,
                    builder: (dialogContext) {
                      return AppConfirmationDialog(
                        icon: Icons.logout_rounded,
                        title: "Log Out?",
                        message:
                            "Are you sure you want to log out from your account?",
                        cancelText: "No",
                        confirmText: "Yes",
                        onConfirm: () {
                          if (widget.onLogout != null) {
                            widget.onLogout!();
                          } else {
                            _logout(navigator.context);
                          }
                        },
                      );
                    },
                  );
                });
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _drawerTopImage() {
    return SizedBox(
      height: 230,
      width: double.infinity,
      child: Stack(
        children: [
          Positioned.fill(
            child: Image.asset(
              'assets/images/img_drawerbg.png',
              fit: BoxFit.fill,
            ),
          ),
          Positioned(
            top: 42,
            left: 24,
            child: Image.asset(
              'assets/images/img_goforwardlogo.png',
              height: 125,
              fit: BoxFit.contain,
            ),
          ),
        ],
      ),
    );
  }

  Widget _profileSection(bool isClient, bool isTechnician) {
    final String roleText = isClient
        ? '...'
        : isTechnician
            ? 'Technician'
            : 'User';

    final String displayName = fullName?.isNotEmpty == true
    ? fullName!
    : (widget.name?.isNotEmpty == true ? widget.name! : '');

    return Transform.translate(
      offset: const Offset(0, -28),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(24, 0, 24, 0),
        child: Row(
          children: [
            CircleAvatar(
              radius: 28,
              backgroundColor: const Color.fromARGB(255, 233, 231, 231),
              backgroundImage: profileImageUrl != null
                  ? NetworkImage(profileImageUrl!)
                  : null,
              child: profileImageUrl == null
                  ? const Icon(
                      Icons.person,
                      color: AppTheme.primaryRed,
                      size: 32,
                    )
                  : null,
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    displayName,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: AppTheme.black,
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Text(
                    widget.email,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: AppTheme.gray,
                      fontSize: 13,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _divider() {
    return const Padding(
      padding: EdgeInsets.fromLTRB(12, 8, 12, 8),
      child: Divider(height: 1),
    );
  }

  Widget _drawerItem({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
    Color? color,
    bool isSelected = false,
  }) {
    final bgColor =
        isSelected ? Colors.black.withOpacity(0.40) : Colors.transparent;

    final itemColor = isSelected ? Colors.white : (color ?? AppTheme.black);

    final iconColor = isSelected ? Colors.white : (color ?? AppTheme.black);

    return Container(
      margin: const EdgeInsets.only(bottom: 6),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: ListTile(
        dense: true,
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 2),
        leading: Icon(icon, color: iconColor),
        title: Text(
          title,
          style: TextStyle(
            color: itemColor,
            fontWeight: isSelected ? FontWeight.bold : FontWeight.w600,
          ),
        ),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
        onTap: onTap,
      ),
    );
  }
}