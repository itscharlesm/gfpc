import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:mobile_app/config/api_config.dart';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/features/bookings/widgets/booking_step_indicator.dart';
import 'package:mobile_app/shared/widgets/headers/app_back_header.dart';
import 'package:mobile_app/features/appointments/pages/client_appointments_page.dart';
import 'package:mobile_app/features/bookings/widgets/booking_review_skeleton_load.dart';

class ClientBookingReviewPage extends StatefulWidget {
  final String email;
  final Map<String, dynamic> selectedAddress;
  final List<Map<String, dynamic>> selectedServicePackages;
  final List<Map<String, dynamic>> selectedAreas;
  final Map<String, dynamic>? selectedTermiteSqm;
  final String description;
  final List<XFile> selectedImages;
  final DateTime selectedDate;
  final String selectedTime;
  final String selectedUrgency;

  const ClientBookingReviewPage({
    super.key,
    required this.email,
    required this.selectedAddress,
    required this.selectedServicePackages,
    required this.selectedAreas,
    required this.selectedTermiteSqm,
    required this.description,
    required this.selectedImages,
    required this.selectedDate,
    required this.selectedTime,
    required this.selectedUrgency,
  });

  @override
  State<ClientBookingReviewPage> createState() =>
      _ClientBookingReviewPageState();
}

class _ClientBookingReviewPageState extends State<ClientBookingReviewPage> {
  bool isSubmitting = false;
  bool isLoadingReview = true;

  @override
  void initState() {
    super.initState();
    _loadReviewSkeleton();
  }

  Future<void> _loadReviewSkeleton() async {
    await Future.delayed(const Duration(milliseconds: 900));

    if (!mounted) return;

    setState(() {
      isLoadingReview = false;
    });
  }

  bool get hasTermitesSelected {
    return widget.selectedServicePackages.any(
      (service) => service['name'].toString().toUpperCase() == 'TERMITES',
    );
  }

  String _timeStart(String timeWindow) {
    if (timeWindow.contains('8:00 AM')) return '08:00:00';
    if (timeWindow.contains('12:00 PM')) return '12:00:00';
    if (timeWindow.contains('5:00 PM')) return '17:00:00';

    return '08:00:00';
  }
  
  double get totalPrice {
    if (hasTermitesSelected && widget.selectedTermiteSqm != null) {
      return double.tryParse(
            widget.selectedTermiteSqm?['cost'].toString() ?? '0',
          ) ??
          0;
    }

    double total = 0;

    for (final area in widget.selectedAreas) {
      final value = area['cost'] ?? area['price'] ?? area['svcpa_cost'] ?? 0;
      total += double.tryParse(value.toString()) ?? 0;
    }

    return total;
  }

  String get formattedDate {
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

    return '${months[widget.selectedDate.month - 1]} ${widget.selectedDate.day}, ${widget.selectedDate.year}';
  }

  String _formatName(String text) {
    return text
        .toLowerCase()
        .split(' ')
        .map((word) {
          if (word.isEmpty) return word;
          return word[0].toUpperCase() + word.substring(1);
        })
        .join(' ');
  }

  Future<void> _submitBooking() async {
    setState(() => isSubmitting = true);

    try {
      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/api/mobile/service-appointments/store',
      );

      final request = http.MultipartRequest('POST', uri);
      request.headers['Accept'] = 'application/json';
      
      request.fields['email'] = widget.email;

      request.fields['uadd_id'] =
          widget.selectedAddress['id'].toString();

      request.fields['client_date'] =
        '${widget.selectedDate.year}-${widget.selectedDate.month.toString().padLeft(2, '0')}-${widget.selectedDate.day.toString().padLeft(2, '0')}';

      request.fields['client_time'] = _timeStart(widget.selectedTime);

      request.fields['initial_price'] = totalPrice.toStringAsFixed(2);

      request.fields['is_termite'] = hasTermitesSelected ? '1' : '0';

      if (hasTermitesSelected && widget.selectedTermiteSqm != null) {
        request.fields['termite_sqm_id'] =
            widget.selectedTermiteSqm!['id'].toString();
      }

      request.fields['problem_description'] =
          widget.description.trim().toUpperCase();

      request.fields['service_packages'] = jsonEncode(
          widget.selectedServicePackages.map((service) {
          return {
            'id': service['id'],
          };
        }).toList(),
      );

      request.fields['service_areas'] = jsonEncode(
        widget.selectedAreas.map((area) {
          return {
            'id': area['id'],
          };
        }).toList(),
      );

      for (final image in widget.selectedImages) {
        request.files.add(
          await http.MultipartFile.fromPath(
            'images[]',
            image.path,
          ),
        );
      }

      final response = await request.send();

      final responseBody =
          await response.stream.bytesToString();

      final data = jsonDecode(responseBody);

      if (response.statusCode == 200 &&
          data['success'] == true) {

        if (!mounted) return;

        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text(
              'Booking request submitted successfully.',
            ),
          ),
        );

        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(
            builder: (_) => ClientAppointmentsPage(
              email: widget.email,
            ),
          ),
          (route) => false,
        );

      } else {
        throw Exception(data['message']);
      }

    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Submission failed: $e'),
        ),
      );
    }

    if (mounted) {
      setState(() => isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.lightGray,
      appBar: const AppBackHeader(
        title: 'Review Booking',
      ),
      body: isLoadingReview
        ? const BookingReviewSkeletonLoad()
        : Column(
        children: [
          const BookingStepIndicator(currentStep: 4),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(20, 18, 20, 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _pageHeader(),
                  const SizedBox(height: 18),
                  _locationCard(),
                  const SizedBox(height: 14),
                  _pestCard(),
                  const SizedBox(height: 14),
                  hasTermitesSelected ? _termiteSqmCard() : _areasCard(),
                  const SizedBox(height: 14),
                  _descriptionCard(),
                  const SizedBox(height: 14),
                  _photosCard(),
                  const SizedBox(height: 14),
                  _scheduleCard(),
                  const SizedBox(height: 16),
                  _nextStepBox(),
                ],
              ),
            ),
          ),
          _bottomButton(),
        ],
      ),
    );
  }

  Widget _pageHeader() {
    return const Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Review Your Request',
          style: TextStyle(
            color: AppTheme.black,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
        SizedBox(height: 5),
        Text(
          'Please check the details before submitting your appointment request.',
          style: TextStyle(
            color: AppTheme.gray,
            fontSize: 13,
            height: 1.35,
          ),
        ),
      ],
    );
  }

  Widget _locationCard() {
    return _summaryBox(
      icon: Icons.location_on_outlined,
      title: 'Service Location',
      children: [
        _MainValue(widget.selectedAddress['type'] ?? 'Address'),
        const SizedBox(height: 6),
        _SubValue(widget.selectedAddress['address'] ?? 'No address selected'),
      ],
    );
  }

  Widget _pestCard() {
    return _summaryBox(
      icon: Icons.bug_report_outlined,
      title: 'Pest Problem',
      children: [
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: widget.selectedServicePackages.map((service) {
            return _chip(
              _formatName(service['name'] ?? ''),
            );
          }).toList(),
        ),
      ],
    );
  }

  Widget _areasCard() {
    return _summaryBox(
      icon: Icons.home_work_outlined,
      title: 'Areas to Treat',
      children: [
        Wrap(
          spacing: 8,
          runSpacing: 8,
          children: widget.selectedAreas.map((area) {
            return _chip(
              '${_formatName(area['area'] ?? '')} • ₱${area['cost'] ?? area['price'] ?? area['svcpa_cost'] ?? 0}',
            );
          }).toList(),
        ),
        const SizedBox(height: 14),
        _totalRow('Estimated Total', '₱${totalPrice.toStringAsFixed(2)}'),
      ],
    );
  }

  Widget _termiteSqmCard() {
    final termiteSqm = widget.selectedTermiteSqm;

    return _summaryBox(
      icon: Icons.square_foot_outlined,
      title: 'Termite Treatment Size',
      children: [
        _MainValue(
          termiteSqm?['sqm_details'] ?? 'No size selected',
        ),

        const SizedBox(height: 14),

        _totalRow(
          'Estimated Total',
          '₱${double.tryParse((termiteSqm?['cost'] ?? 0).toString())?.toStringAsFixed(2) ?? '0.00'}',
        ),
      ],
    );
  }

  Widget _descriptionCard() {
    final description = widget.description.trim();

    return _summaryBox(
      icon: Icons.notes_outlined,
      title: 'Problem Description',
      children: [
        _SubValue(
          description.isEmpty ? 'No additional description provided.' : description,
        ),
      ],
    );
  }

  Widget _photosCard() {
    return _summaryBox(
      icon: Icons.photo_library_outlined,
      title: 'Attached Photos',
      children: [
        if (widget.selectedImages.isEmpty)
          const _SubValue('No photos attached.')
        else
          SizedBox(
            height: 82,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: widget.selectedImages.length,
              separatorBuilder: (_, __) => const SizedBox(width: 10),
              itemBuilder: (context, index) {
                final image = widget.selectedImages[index];

                return ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: Image.file(
                    File(image.path),
                    width: 92,
                    height: 82,
                    fit: BoxFit.cover,
                  ),
                );
              },
            ),
          ),
      ],
    );
  }

  Widget _scheduleCard() {
    return _summaryBox(
      icon: Icons.calendar_month_outlined,
      title: 'Preferred Schedule',
      children: [
        _MainValue(formattedDate),
        const SizedBox(height: 6),
        _SubValue('${widget.selectedUrgency} • ${widget.selectedTime}'),
      ],
    );
  }

  Widget _summaryBox({
    required IconData icon,
    required String title,
    required List<Widget> children,
  }) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: AppTheme.borderedCardDecoration,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                icon,
                color: AppTheme.primaryRed,
                size: 18,
              ),
              const SizedBox(width: 8),
              Text(
                title,
                style: const TextStyle(
                  color: AppTheme.black,
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          ...children,
        ],
      ),
    );
  }

  Widget _chip(String text) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 7),
      decoration: BoxDecoration(
        color: AppTheme.primaryRed.withOpacity(0.06),
        borderRadius: BorderRadius.circular(9),
        border: Border.all(
          color: AppTheme.primaryRed.withOpacity(0.18),
        ),
      ),
      child: Text(
        text,
        style: const TextStyle(
          color: AppTheme.primaryRed,
          fontSize: 12,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  Widget _totalRow(String label, String amount) {
    return Container(
      padding: const EdgeInsets.only(top: 12),
      decoration: const BoxDecoration(
        border: Border(
          top: BorderSide(color: AppTheme.borderGray),
        ),
      ),
      child: Row(
        children: [
          Expanded(
            child: Text(
              label,
              style: const TextStyle(
                color: AppTheme.gray,
                fontSize: 13,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          Text(
            amount,
            style: const TextStyle(
              color: AppTheme.primaryRed,
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _nextStepBox() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppTheme.black.withOpacity(0.04),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: AppTheme.black.withOpacity(0.08),
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(
            Icons.info_outline,
            color: AppTheme.black.withOpacity(0.65),
            size: 18,
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              'The displayed amount is only an estimated price. Final cost may still change after inspection and additional service fees.',
              style: TextStyle(
                color: AppTheme.black.withOpacity(0.72),
                fontSize: 12,
                height: 1.35,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _bottomButton() {
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 14, 20, 20),
      decoration: const BoxDecoration(
        color: AppTheme.white,
        border: Border(
          top: BorderSide(
            color: AppTheme.borderGray,
            width: 1,
          ),
        ),
      ),
      child: ElevatedButton(
        onPressed: isSubmitting ? null : _submitBooking,
        child: isSubmitting
            ? const SizedBox(
                width: 18,
                height: 18,
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                  color: AppTheme.white,
                ),
              )
            : const Text('Submit for Approval'),
      ),
    );
  }
}

class _MainValue extends StatelessWidget {
  final String text;

  const _MainValue(this.text);

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: const TextStyle(
        color: AppTheme.black,
        fontSize: 15,
        fontWeight: FontWeight.bold,
      ),
    );
  }
}

class _SubValue extends StatelessWidget {
  final String text;

  const _SubValue(this.text);

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: const TextStyle(
        color: AppTheme.gray,
        fontSize: 13,
        height: 1.35,
        fontWeight: FontWeight.w500,
      ),
    );
  }
}