import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/config/api_config.dart';
import 'package:mobile_app/shared/widgets/navigation/app_drawer.dart';
import 'package:mobile_app/features/profiles/widgets/profile_header.dart';
import 'package:mobile_app/features/profiles/widgets/profile_menu_list.dart';
import 'package:mobile_app/features/profiles/pages/client_edit_profile_page.dart';
import 'package:mobile_app/shared/shared.dart';
import 'package:mobile_app/features/profiles/pages/client_address_page.dart';
import 'package:mobile_app/app/theme.dart';

class ClientProfilePage extends StatefulWidget {
  final String email;

  const ClientProfilePage({
    super.key,
    required this.email,
  });

  @override
  State<ClientProfilePage> createState() => _ClientProfilePageState();
}

class _ClientProfilePageState extends State<ClientProfilePage> {
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
          fullName = 'Client';
          isLoading = false;
        });
      }
    } catch (e) {
      if (!mounted) return;

      setState(() {
        fullName = 'Client';
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
      backgroundColor: AppTheme.lightGray,
      drawer: AppDrawer(
        userType: 3,
        email: widget.email,
        currentPage: 'profile',
      ),
      appBar: const AppTitleHeader(
        title: "My Profile",
      ),
      body: isLoading
          ? const Center(
            child: CircularProgressIndicator(
              color: AppTheme.primaryRed,
            ),
          )
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  ProfileHeader(
                    name: fullName.isNotEmpty ? fullName : 'Client',
                    role: "Client",
                    email: widget.email,
                    imageUrl: profileImageUrl,
                    onEdit: () async {
                      await Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => ClientEditProfilePage(
                            email: widget.email,
                          ),
                        ),
                      );

                      _fetchProfile();
                    },
                  ),

                  const SizedBox(height: 28),

                  ProfileMenuList(
                    title: "Account",
                    items: [
                      ProfileMenuItem(
                        icon: Icons.person_outline,
                        title: "Edit Profile",
                        subtitle: "Manage your personal information",
                        onTap: () async {
                          await Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => ClientEditProfilePage(
                                email: widget.email,
                              ),
                            ),
                          );

                          _fetchProfile();
                        },
                      ),
                      ProfileMenuItem(
                        icon: Icons.location_on_outlined,
                        title: "Address",
                        subtitle: "Manage your service location",
                        onTap: () async {
                          await Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => ClientAddressPage(
                                email: widget.email,
                              ),
                            ),
                          );

                          _fetchProfile();
                        },
                      ),
                      ProfileMenuItem(
                        icon: Icons.calendar_month_outlined,
                        title: "My Appointments",
                        subtitle: "View your upcoming services",
                        onTap: () {},
                      ),
                      ProfileMenuItem(
                        icon: Icons.history_outlined,
                        title: "Service History",
                        subtitle: "Review your past services",
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
                        color: Colors.red,
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