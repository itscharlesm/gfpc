import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/features/bookings/pages/client_booking_problem_page.dart';
import 'package:mobile_app/features/bookings/widgets/booking_saved_address_card.dart';
import 'package:mobile_app/features/bookings/widgets/booking_step_indicator.dart';
import 'package:mobile_app/shared/shared.dart';
import 'package:mobile_app/shared/widgets/navigation/app_drawer.dart';
import 'package:mobile_app/features/profiles/widgets/add_address_sheet.dart';
import 'package:mobile_app/config/api_config.dart';
import 'package:mobile_app/features/bookings/widgets/booking_location_skeleton.dart';

class ClientBookingLocationPage extends StatefulWidget {
  final String email;

  const ClientBookingLocationPage({
    super.key,
    required this.email,
  });

  @override
  State<ClientBookingLocationPage> createState() =>
      _ClientBookingLocationPageState();
}

class _ClientBookingLocationPageState extends State<ClientBookingLocationPage> {
  String? selectedSavedAddress;
  String? selectedSavedAddressId;
  Map<String, dynamic>? selectedAddressData;
  List<Map<String, dynamic>> savedAddresses = [];
  bool isLoadingAddresses = false;

  String _addressTypeLabel(dynamic addId) {
    switch (addId.toString()) {
      case '1':
        return 'HOME';
      case '2':
        return 'WORK';
      case '3':
        return 'COMPANY';
      case '4':
        return 'FAVORITE';
      case '5':
        return 'RESIDENTIAL';
      default:
        return 'ADDRESS';
    }
  }

  @override
  void initState() {
    super.initState();
    _loadSavedAddresses();
  }

  void _continueToProblem() {
    if (selectedAddressData == null) {
      _showMessage('Please select a saved address.');
      return;
    }

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => ClientBookingProblemPage(
          email: widget.email,
          selectedAddress: selectedAddressData!,
        ),
      ),
    );
  }

  void _showMessage(String message) {
    if (!mounted) return;

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  }

  void _openAddAddressSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppTheme.white,
      builder: (_) {
        return AddAddressSheet(
          email: widget.email,
          onSaved: (_) async {
            await _loadSavedAddresses();
          },
        );
      },
    );
  }

  Future<void> _loadSavedAddresses() async {
    setState(() {
      isLoadingAddresses = true;
    });

    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/address/list'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'email': widget.email,
        }),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (data['success'] == true) {
        final List addressData = data['data'] ?? [];

        final mappedAddresses = addressData.map((item) {
          final street = item['uadd_street'] ?? '';
          final barangay = item['uadd_barangay'] ?? '';
          final city = item['uadd_city'] ?? '';
          final province = item['uadd_province'] ?? '';

          final fullAddress = [
            street,
            barangay,
            city,
            province,
          ].where((part) => part.toString().trim().isNotEmpty).join(', ');

          return {
            'id': item['uadd_id'],
            'type': _addressTypeLabel(item['add_id']),
            'address': fullAddress,
            'is_primary': item['uadd_active'],
          };
        }).toList();

        final activeAddress = mappedAddresses.firstWhere(
          (address) => address['is_primary'].toString() == '1',
          orElse: () => {},
        );

        setState(() {
          savedAddresses = List<Map<String, dynamic>>.from(mappedAddresses);

          if (activeAddress.isNotEmpty) {
            selectedAddressData = Map<String, dynamic>.from(activeAddress);
            selectedSavedAddressId = activeAddress['id'].toString();
            selectedSavedAddress = activeAddress['address'];
          }
        });
      }
    } catch (e) {
      _showMessage('Unable to load saved addresses.');
    } finally {
      if (mounted) {
        setState(() {
          isLoadingAddresses = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.lightGray,
      drawer: AppDrawer(
        userType: 3,
        email: widget.email,
        currentPage: 'book_service',
      ),
      appBar: const AppTitleHeader(
        title: 'Book Service',
      ),
      body: isLoadingAddresses
        ? const BookingLocationSkeletonLoad()
        : Column(
          children: [
            const BookingStepIndicator(currentStep: 1),
            Expanded(
              child: SingleChildScrollView(
                padding: EdgeInsets.fromLTRB(20, 18, 20, 24),
                child: Column(
                  children: [
                    _introCard(),
                    SizedBox(height: 18),

                    BookingSavedAddressCard(
                      addresses: savedAddresses,
                      selectedAddressId: selectedSavedAddressId,
                      onSelected: (address) async {
                        setState(() {
                          selectedAddressData = address;
                          selectedSavedAddressId = address['id'].toString();
                          selectedSavedAddress = address['address'];
                        });

                        await _setPrimaryAddress(address['id']);
                      },
                      onAddNewAddress: _openAddAddressSheet,
                    ),

                    SizedBox(height: 12),
                  ],
                ),
              ),
            ),
            _bottomButton(),
          ],
        ),
    );
  }

  Widget _introCard() {
    return const Padding(
      padding: EdgeInsets.only(left: 2, right: 2),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Where do you need the service?',
            style: TextStyle(
              color: AppTheme.black,
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          SizedBox(height: 6),
          Text(
            'Choose a saved address or enter a new service location for this appointment.',
            style: TextStyle(
              color: AppTheme.gray,
              fontSize: 13,
              height: 1.35,
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
        onPressed: _continueToProblem,
        child: const Text('Continue'),
      ),
    );
  }

  Future<void> _setPrimaryAddress(dynamic uaddId) async {
    try {
      await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/address/set-primary'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': widget.email,
          'uadd_id': uaddId,
        }),
      );
    } catch (e) {
      if (!mounted) return;
      _showMessage('Unable to update selected address.');
    }
  }
}