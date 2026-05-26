import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/features/bookings/pages/client_booking_review_page.dart';
import 'package:mobile_app/features/bookings/widgets/booking_step_indicator.dart';
import 'package:mobile_app/shared/widgets/headers/app_back_header.dart';
import 'package:mobile_app/features/bookings/widgets/booking_schedule_skeleton_load.dart';

class ClientBookingSchedulePage extends StatefulWidget {
  final String email;
  final Map<String, dynamic> selectedAddress;
  final List<Map<String, dynamic>> selectedServicePackages;
  final List<Map<String, dynamic>> selectedAreas;
  final Map<String, dynamic>? selectedTermiteSqm;
  final String description;
  final List<XFile> selectedImages;
  final double? termiteInputSqm;

  const ClientBookingSchedulePage({
    super.key,
    required this.email,
    required this.selectedAddress,
    required this.selectedServicePackages,
    required this.selectedAreas,
    required this.selectedTermiteSqm,
    required this.description,
    required this.selectedImages,
    required this.termiteInputSqm,
  });

  @override
  State<ClientBookingSchedulePage> createState() =>
      _ClientBookingSchedulePageState();
}

class _ClientBookingSchedulePageState extends State<ClientBookingSchedulePage> {
  bool isLoadingSchedule = true;
  DateTime? selectedDate;
  Map<String, String>? selectedTimeWindow;

  final List<Map<String, String>> timeWindows = [
    {
      'title': 'Morning',
      'time': '8:00 AM - 12:00 PM',
      'subtitle': 'Best for early home visits',
      'icon': '🌅',
    },
    {
      'title': 'Afternoon',
      'time': '12:00 PM - 5:00 PM',
      'subtitle': 'Ideal for regular service hours',
      'icon': '☀️',
    },
    {
      'title': 'Evening',
      'time': '5:00 PM - 8:00 PM',
      'subtitle': 'For late-day inspection requests',
      'icon': '🌆',
    },
  ];

  @override
  void initState() {
    super.initState();
    _loadScheduleSkeleton();
  }

  Future<void> _loadScheduleSkeleton() async {
    await Future.delayed(const Duration(milliseconds: 900));

    if (!mounted) return;

    setState(() {
      isLoadingSchedule = false;
    });
  }

  Future<void> _pickDate() async {
    final now = DateTime.now();

    final tomorrow = DateTime(
      now.year,
      now.month,
      now.day + 1,
    );

    final date = await showDatePicker(
      context: context,
      initialDate: selectedDate ?? tomorrow,
      firstDate: tomorrow,
      lastDate: DateTime(now.year + 1),
    );

    if (date == null) return;

    setState(() {
      selectedDate = date;
    });
  }

  void _continueToReview() {
    if (selectedDate == null || selectedTimeWindow == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please choose your preferred date and time window.'),
        ),
      );
      return;
    }

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => ClientBookingReviewPage(
          email: widget.email,
          selectedAddress: widget.selectedAddress,
          selectedServicePackages: widget.selectedServicePackages,
          selectedAreas: widget.selectedAreas,
          selectedTermiteSqm: widget.selectedTermiteSqm,
          termiteInputSqm: widget.termiteInputSqm,
          description: widget.description,
          selectedImages: widget.selectedImages,
          selectedDate: selectedDate!,
          selectedTime: selectedTimeWindow!['time']!,
          selectedUrgency: selectedTimeWindow!['title']!,
        ),
      ),
    );
  }

  String get formattedDate {
    if (selectedDate == null) return 'Choose preferred date';

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

    return '${months[selectedDate!.month - 1]} ${selectedDate!.day}, ${selectedDate!.year}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.lightGray,
      appBar: const AppBackHeader(
        title: 'Book Service',
      ),
      body: isLoadingSchedule
        ? const BookingScheduleSkeletonLoad()
        : Column(
        children: [
          const BookingStepIndicator(currentStep: 3),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(20, 18, 20, 24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _pageHeader(),
                  const SizedBox(height: 18),
                  _scheduleCard(),
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
          'Schedule Your Service',
          style: TextStyle(
            color: AppTheme.black,
            fontSize: 18,
            fontWeight: FontWeight.bold,
          ),
        ),
        SizedBox(height: 5),
        Text(
          'Choose the date and time window that works best for you.',
          style: TextStyle(
            color: AppTheme.gray,
            fontSize: 13,
            height: 1.35,
          ),
        ),
      ],
    );
  }

  Widget _scheduleCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: AppTheme.borderedCardDecoration,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _dateSelector(),
          const SizedBox(height: 22),
          const Text(
            'Time Window',
            style: TextStyle(
              color: AppTheme.black,
              fontSize: 15,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 12),
          ...timeWindows.map(_timeWindowItem),
          const SizedBox(height: 4),
          _scheduleNote(),
        ],
      ),
    );
  }

  Widget _dateSelector() {
    final hasDate = selectedDate != null;

    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: _pickDate,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: hasDate
              ? AppTheme.primaryRed.withOpacity(0.06)
              : AppTheme.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: hasDate ? AppTheme.primaryRed : AppTheme.borderGray,
            width: hasDate ? 1.3 : 1,
          ),
        ),
        child: Row(
          children: [
            Container(
              width: 42,
              height: 42,
              decoration: BoxDecoration(
                color: hasDate
                    ? AppTheme.primaryRed
                    : AppTheme.primaryRed.withOpacity(0.08),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(
                Icons.calendar_month_outlined,
                color: hasDate ? AppTheme.white : AppTheme.primaryRed,
                size: 22,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Preferred Date',
                    style: TextStyle(
                      color: AppTheme.gray,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Text(
                    formattedDate,
                    style: TextStyle(
                      color: hasDate ? AppTheme.black : AppTheme.gray,
                      fontSize: 14,
                      fontWeight: hasDate ? FontWeight.bold : FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
            const Icon(
              Icons.chevron_right_rounded,
              color: AppTheme.gray,
            ),
          ],
        ),
      ),
    );
  }

  Widget _timeWindowItem(Map<String, String> window) {
    final isSelected = selectedTimeWindow?['title'] == window['title'];

    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: () {
        setState(() {
          selectedTimeWindow = window;
        });
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: isSelected ? AppTheme.primaryRed : AppTheme.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isSelected ? AppTheme.primaryRed : AppTheme.borderGray,
            width: isSelected ? 1.3 : 1,
          ),
        ),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              alignment: Alignment.center,
              decoration: BoxDecoration(
                color: isSelected
                    ? AppTheme.white
                    : AppTheme.primaryRed.withOpacity(0.06),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Text(
                window['icon']!,
                style: const TextStyle(fontSize: 23),
              ),
            ),
            const SizedBox(width: 13),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    window['title']!,
                    style: TextStyle(
                      color: isSelected ? AppTheme.white : AppTheme.black,
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 3),
                  Text(
                    window['time']!,
                    style: TextStyle(
                      color: isSelected ? AppTheme.white : AppTheme.gray,
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    window['subtitle']!,
                    style: TextStyle(
                      color: isSelected
                          ? AppTheme.white.withOpacity(0.9)
                          : AppTheme.gray,
                      fontSize: 11,
                      height: 1.2,
                    ),
                  ),
                ],
              ),
            ),
            Icon(
              isSelected ? Icons.check_circle : Icons.radio_button_unchecked,
              color: isSelected ? AppTheme.white : AppTheme.gray,
              size: 22,
            ),
          ],
        ),
      ),
    );
  }

  Widget _scheduleNote() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppTheme.lightGray,
        borderRadius: BorderRadius.circular(12),
      ),
      child: const Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(
            Icons.info_outline,
            color: AppTheme.gray,
            size: 16,
          ),
          SizedBox(width: 8),
          Expanded(
            child: Text(
              'Final schedule is subject to company approval and technician availability.',
              style: TextStyle(
                color: AppTheme.gray,
                fontSize: 12,
                height: 1.3,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _bottomButton() {
    final canContinue = selectedDate != null && selectedTimeWindow != null;

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
        onPressed: canContinue ? _continueToReview : null,
        child: const Text('Continue'),
      ),
    );
  }
}