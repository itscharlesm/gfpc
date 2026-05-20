import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/config/api_config.dart';
import 'package:mobile_app/shared/widgets/navigation/app_drawer.dart';
import 'package:mobile_app/features/profiles/widgets/profile_header.dart';
import 'package:mobile_app/features/profiles/widgets/profile_menu_list.dart';
import 'package:mobile_app/shared/widgets/headers/app_title_header.dart';

class TechnicianProfilePage extends StatefulWidget {
  final String email;

  const TechnicianProfilePage({
    super.key,
    required this.email,
  });

  @override
  State<TechnicianProfilePage> createState() => _TechnicianProfilePageState();
}

class _TechnicianProfilePageState extends State<TechnicianProfilePage> {
  bool isLoading = true;

  String fullName = '';
  String? profileImagePath;

  @override
  void initState() {
    super.initState();
    _fetchProfile();
  }

  String formatName(String value) {
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

  Future<void> _fetchProfile() async {
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

        final firstName = formatName(user['usr_first_name'] ?? '');
        final lastName = formatName(user['usr_last_name'] ?? '');

        setState(() {
          fullName = '$firstName $lastName'.trim();
          profileImagePath = user['usr_image_path'];
          isLoading = false;
        });
      } else {
        setState(() {
          fullName = 'Technician';
          isLoading = false;
        });
      }
    } catch (e) {
      if (!mounted) return;

      setState(() {
        fullName = 'Technician';
        isLoading = false;
      });
    }
  }

  String? get profileImageUrl {
    if (profileImagePath == null || profileImagePath!.isEmpty) {
      return null;
    }

    return '${ApiConfig.baseUrl}/$profileImagePath';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: AppDrawer(
        userType: 2,
        email: widget.email,
        currentPage: 'profile',
      ),
      appBar: const AppTitleHeader(
        title: "My Profile",
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  ProfileHeader(
                    name: fullName.isNotEmpty ? fullName : 'Technician',
                    role: "Technician",
                    email: widget.email,
                    imageUrl: profileImageUrl,
                    onEdit: () {
                      // TODO: Navigate to technician edit profile page
                    },
                  ),

                  const SizedBox(height: 28),

                  ProfileMenuList(
                    title: "Work",
                    items: [
                      ProfileMenuItem(
                        icon: Icons.assignment_outlined,
                        title: "Assigned Jobs",
                        subtitle: "View service requests assigned to you",
                        onTap: () {},
                      ),
                      ProfileMenuItem(
                        icon: Icons.edit_note_outlined,
                        title: "Service Reports",
                        subtitle: "Create and submit service reports",
                        onTap: () {},
                      ),
                      ProfileMenuItem(
                        icon: Icons.location_on_outlined,
                        title: "Job Locations",
                        subtitle: "View client service locations",
                        onTap: () {},
                      ),
                    ],
                  ),

                  const SizedBox(height: 24),

                  ProfileMenuList(
                    title: "Settings",
                    items: [
                      ProfileMenuItem(
                        icon: Icons.settings_outlined,
                        title: "Settings",
                        subtitle: "App preferences and notifications",
                        onTap: () {},
                      ),
                      ProfileMenuItem(
                        icon: Icons.privacy_tip_outlined,
                        title: "Privacy & Security",
                        subtitle: "Manage your privacy settings",
                        onTap: () {},
                      ),
                      ProfileMenuItem(
                        icon: Icons.logout_rounded,
                        title: "Log Out",
                        subtitle: "Sign out from your account",
                        color: AppTheme.primaryRed,
                        onTap: () {},
                      ),
                    ],
                  ),
                ],
              ),
            ),
    );
  }
}