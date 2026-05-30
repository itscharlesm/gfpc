import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/config/api_config.dart';
import 'package:mobile_app/shared/widgets/navigation/app_drawer.dart';
import 'package:mobile_app/shared/widgets/headers/app_home_header.dart';

class TechnicianHomePage extends StatefulWidget {
  final String email;

  const TechnicianHomePage({
    super.key,
    required this.email,
  });

  @override
  State<TechnicianHomePage> createState() => _TechnicianHomePageState();
}

class _TechnicianHomePageState extends State<TechnicianHomePage> {
  String? profileImagePath;

  bool isOnDuty = false;
  bool hasActiveJob = true; // change to false if you want empty state
  String jobStatus = 'assigned';

  @override
  void initState() {
    super.initState();
    _fetchProfileImage();
  }

  Future<void> _fetchProfileImage() async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/profile'),
        headers: {'Accept': 'application/json'},
        body: {'email': widget.email},
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 200 && data['success'] == true) {
        final user = data['data'];

        setState(() {
          profileImagePath = user['usr_image_path'];
        });
      }
    } catch (_) {
      // Keep default avatar if fetching fails.
    }
  }

  String? get profileImageUrl {
    if (profileImagePath == null || profileImagePath!.isEmpty) {
      return null;
    }

    return '${ApiConfig.baseUrl}/$profileImagePath';
  }

  String get statusLabel {
    switch (jobStatus) {
      case 'on_the_way':
        return 'On the Way';
      case 'arrived':
        return 'Arrived';
      case 'in_progress':
        return 'Service in Progress';
      case 'completed':
        return 'Completed';
      default:
        return 'Assigned';
    }
  }

  Color get statusColor {
    switch (jobStatus) {
      case 'on_the_way':
        return Colors.blue;
      case 'arrived':
        return Colors.orange;
      case 'in_progress':
        return AppTheme.primaryRed;
      case 'completed':
        return Colors.green;
      default:
        return Colors.grey;
    }
  }

  void _startTravel() {
    if (!isOnDuty) {
      _showMessage('Please turn on duty first before starting travel.');
      return;
    }

    setState(() {
      jobStatus = 'on_the_way';
    });

    _showMessage('Travel started. Client tracking is now enabled.');
  }

  void _markArrived() {
    setState(() {
      jobStatus = 'arrived';
    });

    _showMessage('Marked as arrived.');
  }

  void _startService() {
    setState(() {
      jobStatus = 'in_progress';
    });

    _showMessage('Service started.');
  }

  void _completeService() {
    setState(() {
      jobStatus = 'completed';
    });

    _showMessage('Service completed. You may now submit the report.');
  }

  void _showMessage(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  }

  String _formattedDate() {
    final now = DateTime.now();

    const months = [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December',
    ];

    const weekdays = [
      'Monday',
      'Tuesday',
      'Wednesday',
      'Thursday',
      'Friday',
      'Saturday',
      'Sunday',
    ];

    return '${weekdays[now.weekday - 1]}, ${months[now.month - 1]} ${now.day}, ${now.year}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      drawer: AppDrawer(
        userType: 2,
        email: widget.email,
        currentPage: 'home',
      ),
      appBar: AppHomeHeader(
        imageUrl: profileImageUrl,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(20, 18, 20, 24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Hello, Technician!',
              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.w800,
                  ),
            ),
            const SizedBox(height: 4),
            Text(
              _formattedDate(),
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: AppTheme.gray,
                  ),
            ),

            const SizedBox(height: 18),

            _onDutyCard(),

            const SizedBox(height: 22),

            Row(
              children: [
                _summaryCard(
                  title: "Today's Jobs",
                  value: hasActiveJob ? '1' : '0',
                  icon: Icons.calendar_month_outlined,
                  iconBgColor: const Color(0xFFE3EEFF),
                  iconColor: Colors.blue,
                ),
                const SizedBox(width: 12),
                _summaryCard(
                  title: 'Completed Today',
                  value: jobStatus == 'completed' ? '1' : '0',
                  icon: Icons.check_circle_outline,
                  iconBgColor: const Color(0xFFE3F8E9),
                  iconColor: Colors.green,
                ),
              ],
            ),

            const SizedBox(height: 12),

            Row(
              children: [
                _summaryCard(
                  title: 'Upcoming',
                  value: '0',
                  icon: Icons.schedule_outlined,
                  iconBgColor: const Color(0xFFF3E5FF),
                  iconColor: Colors.purple,
                ),
                const SizedBox(width: 12),
                _summaryCard(
                  title: 'Rating',
                  value: '5.0',
                  icon: Icons.person_outline,
                  iconBgColor: const Color(0xFFFFF2C7),
                  iconColor: Colors.orange,
                ),
              ],
            ),

            const SizedBox(height: 24),

            Text(
              "Today's Assignment",
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
            ),

            const SizedBox(height: 12),

            hasActiveJob ? _activeJobCard() : _emptyAssignmentCard(),

            const SizedBox(height: 24),

            Text(
              'Quick Actions',
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
            ),

            const SizedBox(height: 12),

            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () {
                  // TODO: Navigate to assigned jobs page
                },
                icon: const Icon(Icons.list_alt),
                label: const Text('View Assigned Jobs'),
              ),
            ),

            const SizedBox(height: 12),

            SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: jobStatus == 'completed'
                    ? () {
                        // TODO: Navigate to service report page
                      }
                    : null,
                icon: const Icon(Icons.edit_note),
                label: const Text('Submit Service Report'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _onDutyCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: isOnDuty ? const Color(0xFFEFFBF3) : AppTheme.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isOnDuty ? const Color(0xFFC9F2D5) : AppTheme.borderGray,
        ),
      ),
      child: Row(
        children: [
          Icon(
            Icons.toggle_on_outlined,
            color: isOnDuty ? Colors.green : AppTheme.gray,
          ),
          const SizedBox(width: 10),
          Text(
            isOnDuty ? 'On Duty' : 'Off Duty',
            style: TextStyle(
              fontWeight: FontWeight.w700,
              color: isOnDuty ? Colors.green : AppTheme.gray,
            ),
          ),
          const Spacer(),
          Switch(
            value: isOnDuty,
            activeColor: Colors.green,
            onChanged: (value) {
              setState(() {
                isOnDuty = value;
              });
            },
          ),
        ],
      ),
    );
  }

  Widget _summaryCard({
    required String title,
    required String value,
    required IconData icon,
    required Color iconBgColor,
    required Color iconColor,
  }) {
    return Expanded(
      child: Container(
        height: 112,
        padding: const EdgeInsets.all(16),
        decoration: AppTheme.softCardDecoration,
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 13,
                      color: AppTheme.gray,
                    ),
                  ),
                  const Spacer(),
                  Text(
                    value,
                    style: const TextStyle(
                      fontSize: 23,
                      fontWeight: FontWeight.w800,
                      color: AppTheme.black,
                    ),
                  ),
                ],
              ),
            ),
            Container(
              width: 38,
              height: 38,
              decoration: BoxDecoration(
                color: iconBgColor,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(
                icon,
                color: iconColor,
                size: 22,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _activeJobCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: AppTheme.softCardDecoration,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Expanded(
                child: Text(
                  'Pest Control Service',
                  style: TextStyle(
                    fontWeight: FontWeight.w800,
                    fontSize: 16,
                  ),
                ),
              ),
              _statusBadge(),
            ],
          ),

          const SizedBox(height: 14),

          _jobInfoRow(
            icon: Icons.person_outline,
            title: 'Client',
            value: 'Juan Dela Cruz',
          ),
          const SizedBox(height: 10),
          _jobInfoRow(
            icon: Icons.location_on_outlined,
            title: 'Location',
            value: 'Bajada, Davao City',
          ),
          const SizedBox(height: 10),
          _jobInfoRow(
            icon: Icons.bug_report_outlined,
            title: 'Pest Type',
            value: 'Termites',
          ),
          const SizedBox(height: 10),
          _jobInfoRow(
            icon: Icons.access_time,
            title: 'Schedule',
            value: 'Today, 2:00 PM',
          ),

          const SizedBox(height: 18),

          _workflowButtons(),
        ],
      ),
    );
  }

  Widget _workflowButtons() {
    if (jobStatus == 'assigned') {
      return SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
          onPressed: _startTravel,
          icon: const Icon(Icons.navigation_outlined),
          label: const Text('Start Travel'),
        ),
      );
    }

    if (jobStatus == 'on_the_way') {
      return Column(
        children: [
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: () {
                // TODO: Navigate to technician tracking map page
              },
              icon: const Icon(Icons.map_outlined),
              label: const Text('Open Tracking Map'),
            ),
          ),
          const SizedBox(height: 10),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton.icon(
              onPressed: _markArrived,
              icon: const Icon(Icons.flag_outlined),
              label: const Text('Mark as Arrived'),
            ),
          ),
        ],
      );
    }

    if (jobStatus == 'arrived') {
      return SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
          onPressed: _startService,
          icon: const Icon(Icons.play_circle_outline),
          label: const Text('Start Service'),
        ),
      );
    }

    if (jobStatus == 'in_progress') {
      return SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
          onPressed: _completeService,
          icon: const Icon(Icons.check_circle_outline),
          label: const Text('Complete Service'),
        ),
      );
    }

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFFEFFBF3),
        borderRadius: BorderRadius.circular(12),
      ),
      child: const Text(
        'Service completed. You can now submit the service report.',
        style: TextStyle(
          color: Colors.green,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _statusBadge() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: statusColor.withOpacity(0.10),
        borderRadius: BorderRadius.circular(50),
      ),
      child: Text(
        statusLabel,
        style: TextStyle(
          color: statusColor,
          fontSize: 12,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }

  Widget _jobInfoRow({
    required IconData icon,
    required String title,
    required String value,
  }) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(
          icon,
          size: 20,
          color: AppTheme.gray,
        ),
        const SizedBox(width: 10),
        Expanded(
          child: RichText(
            text: TextSpan(
              style: const TextStyle(
                color: AppTheme.black,
                fontSize: 13.5,
              ),
              children: [
                TextSpan(
                  text: '$title: ',
                  style: const TextStyle(
                    color: AppTheme.gray,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                TextSpan(
                  text: value,
                  style: const TextStyle(
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _emptyAssignmentCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: AppTheme.softCardDecoration,
      child: const Column(
        children: [
          Icon(
            Icons.calendar_month_outlined,
            size: 42,
            color: AppTheme.borderGray,
          ),
          SizedBox(height: 12),
          Text(
            'No assigned job yet.',
            style: TextStyle(
              fontWeight: FontWeight.w700,
            ),
          ),
          SizedBox(height: 6),
          Text(
            'Assigned service requests will appear here once approved by the admin.',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: AppTheme.gray,
              fontSize: 13,
            ),
          ),
        ],
      ),
    );
  }
}