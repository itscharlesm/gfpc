import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/config/api_config.dart';
import 'package:mobile_app/shared/shared.dart';
import 'package:mobile_app/shared/widgets/navigation/app_drawer.dart';
import 'package:mobile_app/features/appointments/widgets/client_appointment_card.dart';

class ClientAppointmentsPage extends StatefulWidget {
  final String email;

  const ClientAppointmentsPage({
    super.key,
    required this.email,
  });

  @override
  State<ClientAppointmentsPage> createState() => _ClientAppointmentsPageState();
}

class _ClientAppointmentsPageState extends State<ClientAppointmentsPage> {
  bool isLoading = true;
  String selectedFilter = 'All';

  List<Map<String, dynamic>> appointments = [];

  final List<String> filters = [
    'All',
    'Requested',
    'Assessed',
    'Scheduled',
    'Ongoing',
    'Completed',
  ];

  @override
  void initState() {
    super.initState();
    _loadAppointments();
  }

  List<Map<String, dynamic>> get filteredAppointments {
    if (selectedFilter == 'All') return appointments;

    return appointments.where((appointment) {
      return appointment['status'].toString().toUpperCase() ==
          selectedFilter.toUpperCase();
    }).toList();
  }

  Future<void> _loadAppointments() async {
    setState(() {
      isLoading = true;
    });

    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/appointments/client'),
        headers: {
          'Accept': 'application/json',
        },
        body: {
          'email': widget.email,
        },
      );

      final data = jsonDecode(response.body);

      if (data['success'] == true) {
        final List appointmentData = data['data'] ?? [];

        setState(() {
          appointments = appointmentData.map((item) {
            return {
              'status': item['svca_status'] ?? 'REQUESTED',

              'service': item['svc_is_termite'] == 1
                ? 'Termite Treatment'
                : 'General Pest Control',

              'schedule':
                  '${_formatDate(item['svca_client_date'])} • ${_formatTime(item['svca_client_time'])}',

              'locationType': 'SERVICE LOCATION',

              'address': _formatAddress(item),

              'price': '₱${_formatPrice(item['svc_balance'])}',

              'requestedDate':
                  'Requested on ${_formatDate(item['svca_date_created'])}',

              'isTermite': item['svc_is_termite'] == 1,

              'sqmDetails': item['svcpat_sqm_details'],

              'fullPestTypes': item['fullPestTypes'],

              'areaTypes': item['areaTypes'],
            };
          }).toList();

          isLoading = false;
        });
      } else {
        setState(() {
          appointments = [];
          isLoading = false;
        });
      }
    } catch (e) {
      if (!mounted) return;

      setState(() {
        appointments = [];
        isLoading = false;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Unable to load appointments.'),
        ),
      );
    }
  }

  String _formatAddress(Map<String, dynamic> item) {
    final parts = [
      item['uadd_street'],
      item['uadd_barangay'],
      item['uadd_city'],
      item['uadd_province'],
    ];

    return parts
        .where((part) => part != null && part.toString().trim().isNotEmpty)
        .join(', ');
  }

  String _formatPrice(dynamic value) {
    final price = double.tryParse(value.toString()) ?? 0;
    return price.toStringAsFixed(2);
  }

  String _formatDate(dynamic value) {
    if (value == null) return 'No date';

    final date = DateTime.tryParse(value.toString());
    if (date == null) return value.toString();

    final months = [
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

    return '${months[date.month - 1]} ${date.day}, ${date.year}';
  }

  String _formatTime(dynamic value) {
    if (value == null) return 'No time';

    final time = value.toString();

    if (time.startsWith('08:00')) return '8:00 AM - 12:00 PM';
    if (time.startsWith('12:00')) return '12:00 PM - 5:00 PM';
    if (time.startsWith('17:00')) return '5:00 PM - 8:00 PM';

    return time;
  }

  @override
  Widget build(BuildContext context) {
    final visibleAppointments = filteredAppointments;

    return Scaffold(
      backgroundColor: AppTheme.lightGray,
      drawer: AppDrawer(
        userType: 3,
        email: widget.email,
        currentPage: 'appointments',
      ),
      appBar: const AppTitleHeader(
        title: 'My Appointments',
      ),
      body: isLoading
          ? const Center(
              child: CircularProgressIndicator(
                color: AppTheme.primaryRed,
              ),
            )
          : Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _pageHeader(),
                  const SizedBox(height: 16),
                  _filterChips(),
                  const SizedBox(height: 16),
                  Expanded(
                    child: visibleAppointments.isEmpty
                        ? const Align(
                            alignment: Alignment(0, -0.18),
                            child: _EmptyAppointmentsContent(),
                          )
                        : RefreshIndicator(
                            color: AppTheme.primaryRed,
                            onRefresh: _loadAppointments,
                            child: ListView.separated(
                              itemCount: visibleAppointments.length,
                              separatorBuilder: (_, __) =>
                                  const SizedBox(height: 12),
                              itemBuilder: (context, index) {
                                return ClientAppointmentCard(
                                  appointment: visibleAppointments[index],
                                );
                              },
                            ),
                          ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _pageHeader() {
    return const Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Your Service Requests',
          style: TextStyle(
            color: AppTheme.black,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
        SizedBox(height: 5),
        Text(
          'View and track your submitted appointment requests.',
          style: TextStyle(
            color: AppTheme.gray,
            fontSize: 13,
            height: 1.35,
          ),
        ),
      ],
    );
  }

  Widget _filterChips() {
    return SizedBox(
      height: 38,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemCount: filters.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (context, index) {
          final filter = filters[index];
          final isSelected = selectedFilter == filter;

          return GestureDetector(
            onTap: () {
              setState(() {
                selectedFilter = filter;
              });
            },
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 14),
              alignment: Alignment.center,
              decoration: BoxDecoration(
                color: isSelected ? AppTheme.primaryRed : AppTheme.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: isSelected ? AppTheme.primaryRed : AppTheme.borderGray,
                ),
              ),
              child: Text(
                filter,
                style: TextStyle(
                  color: isSelected ? AppTheme.white : AppTheme.black,
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

class _EmptyAppointmentsContent extends StatelessWidget {
  const _EmptyAppointmentsContent();

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 68,
          height: 68,
          decoration: BoxDecoration(
            color: AppTheme.primaryRed.withOpacity(0.08),
            shape: BoxShape.circle,
          ),
          child: const Icon(
            Icons.calendar_month_outlined,
            color: AppTheme.primaryRed,
            size: 34,
          ),
        ),
        const SizedBox(height: 16),
        const Text(
          'No appointments yet',
          style: TextStyle(
            color: AppTheme.black,
            fontSize: 16,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 7),
        const Padding(
          padding: EdgeInsets.symmetric(horizontal: 28),
          child: Text(
            'Book a service now and your appointment requests will appear here.',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: AppTheme.gray,
              fontSize: 13,
              height: 1.35,
            ),
          ),
        ),
      ],
    );
  }
}