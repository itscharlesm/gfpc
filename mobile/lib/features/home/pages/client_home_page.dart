import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/config/api_config.dart';
import 'package:mobile_app/features/bookings/pages/client_booking_location_page.dart';
import 'package:mobile_app/shared/shared.dart';

class ClientHomePage extends StatefulWidget {
  final String email;

  const ClientHomePage({
    super.key,
    required this.email,
  });

  @override
  State<ClientHomePage> createState() => _ClientHomePageState();
}

class _ClientHomePageState extends State<ClientHomePage> {
  String? profileImagePath;
  String clientName = 'Haru';

  final List<Map<String, dynamic>> stats = [
    {
      'title': 'Active\nAppointments',
      'value': '4',
      'icon': Icons.calendar_month_rounded,
      'bg': Color(0xFFE1ECFF),
      'color': Color(0xFF2F73FF),
    },
    {
      'title': 'Pending\nJobs',
      'value': '0',
      'icon': Icons.schedule_rounded,
      'bg': Color(0xFFFFF1C9),
      'color': Color(0xFFFF9800),
    },
    {
      'title': 'Completed\nServices',
      'value': '0',
      'icon': Icons.check_circle_outline_rounded,
      'bg': Color(0xFFD9FBE4),
      'color': Color(0xFF20B85A),
    },
    {
      'title': 'Pending\nPayments',
      'value': '0',
      'icon': Icons.credit_card_rounded,
      'bg': Color(0xFFFFDDE1),
      'color': Color(0xFFE83D4B),
    },
  ];

  final List<Map<String, dynamic>> recentAppointments = [
    {
      'service': 'Rats',
      'schedule': 'Pending schedule',
      'status': 'Requested',
    },
    {
      'service': 'Cockroaches',
      'schedule': 'Pending schedule',
      'status': 'Requested',
    },
    {
      'service': 'Other',
      'schedule': 'Pending schedule',
      'status': 'Requested',
    },
  ];

  @override
  void initState() {
    super.initState();
    _fetchProfileImage();
  }

  Future<void> _fetchProfileImage() async {
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

        setState(() {
          profileImagePath = user['usr_image_path'];

          final firstName = user['usr_first_name'];
          if (firstName != null && firstName.toString().trim().isNotEmpty) {
            clientName = firstName.toString();
          }
        });
      }
    } catch (_) {
      // Keep default values if fetching fails.
    }
  }

  String? get profileImageUrl {
    if (profileImagePath == null || profileImagePath!.isEmpty) {
      return null;
    }

    return '${ApiConfig.baseUrl}/$profileImagePath';
  }

  void _goToBooking() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => ClientBookingLocationPage(
          email: widget.email,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.lightGray,
      drawer: AppDrawer(
        userType: 3,
        email: widget.email,
        currentPage: 'home',
      ),
      appBar: AppHomeHeader(
        imageUrl: profileImageUrl,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(20, 18, 20, 26),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _welcomeSection(),
            const SizedBox(height: 20),
            _statsGrid(),
            const SizedBox(height: 22),
            _recentAppointmentsCard(),
            const SizedBox(height: 18),
            _recentJobsCard(),
          ],
        ),
      ),
    );
  }

  Widget _welcomeSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Welcome back, Client!',
          style: const TextStyle(
            color: AppTheme.black,
            fontSize: 23,
            fontWeight: FontWeight.w900,
            height: 1.1,
          ),
        ),
        const SizedBox(height: 6),
        const Text(
          'Manage your pest control services',
          style: TextStyle(
            color: AppTheme.gray,
            fontSize: 13,
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 18),
        SizedBox(
          height: 44,
          child: ElevatedButton.icon(
            onPressed: _goToBooking,
            icon: const Icon(
              Icons.add_rounded,
              size: 20,
            ),
            label: const Text(
              'Book Service',
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w800,
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _statsGrid() {
    return GridView.builder(
      itemCount: stats.length,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        crossAxisSpacing: 14,
        mainAxisSpacing: 14,
        childAspectRatio: 1.34,
      ),
      itemBuilder: (context, index) {
        final item = stats[index];

        return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: AppTheme.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: AppTheme.borderGray.withOpacity(0.55),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.025),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      item['title'],
                      style: const TextStyle(
                        color: AppTheme.gray,
                        fontSize: 12.2,
                        height: 1.35,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    const Spacer(),
                    Text(
                      item['value'],
                      style: const TextStyle(
                        color: AppTheme.black,
                        fontSize: 24,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: item['bg'],
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(
                  item['icon'],
                  color: item['color'],
                  size: 23,
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _recentAppointmentsCard() {
    return _sectionCard(
      title: 'Recent Appointments',
      onViewAll: () {},
      child: Column(
        children: recentAppointments.map((appointment) {
          return Container(
            margin: const EdgeInsets.only(bottom: 10),
            padding: const EdgeInsets.fromLTRB(14, 13, 14, 13),
            decoration: BoxDecoration(
              color: AppTheme.lightGray.withOpacity(0.65),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        appointment['service'],
                        style: const TextStyle(
                          color: AppTheme.black,
                          fontSize: 14,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      const SizedBox(height: 5),
                      Text(
                        appointment['schedule'],
                        style: const TextStyle(
                          color: AppTheme.gray,
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 11,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFFE4EEFF),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    appointment['status'],
                    style: const TextStyle(
                      color: Color(0xFF1F62E8),
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ],
            ),
          );
        }).toList(),
      ),
    );
  }

  Widget _recentJobsCard() {
    return _sectionCard(
      title: 'Recent Jobs',
      onViewAll: () {},
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(vertical: 34),
        child: Column(
          children: [
            Icon(
              Icons.description_outlined,
              size: 46,
              color: AppTheme.gray.withOpacity(0.35),
            ),
            const SizedBox(height: 12),
            const Text(
              'No jobs yet',
              style: TextStyle(
                color: AppTheme.gray,
                fontSize: 14,
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: 6),
            const Text(
              'Jobs appear after your appointment is confirmed',
              textAlign: TextAlign.center,
              style: TextStyle(
                color: AppTheme.gray,
                fontSize: 12,
                height: 1.35,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _sectionCard({
    required String title,
    required Widget child,
    VoidCallback? onViewAll,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 12),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: AppTheme.borderGray.withOpacity(0.55),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.025),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  title,
                  style: const TextStyle(
                    color: AppTheme.black,
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ),
              InkWell(
                borderRadius: BorderRadius.circular(8),
                onTap: onViewAll,
                child: const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 4, vertical: 4),
                  child: Text(
                    'View all',
                    style: TextStyle(
                      color: AppTheme.primaryRed,
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          child,
        ],
      ),
    );
  }
}