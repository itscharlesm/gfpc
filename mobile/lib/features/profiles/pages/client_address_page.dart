import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:mobile_app/config/api_config.dart';
import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/features/profiles/widgets/add_address_sheet.dart';
import 'package:mobile_app/shared/widgets/headers/app_back_header.dart';
import 'package:mobile_app/features/profiles/widgets/edit_address_sheet.dart';
import 'package:mobile_app/features/profiles/widgets/delete_address_dialog.dart';

class ClientAddressPage extends StatefulWidget {
  final String email;

  const ClientAddressPage({
    super.key,
    required this.email,
  });

  @override
  State<ClientAddressPage> createState() => _ClientAddressPageState();
}

class _ClientAddressPageState extends State<ClientAddressPage> {
  List<Map<String, dynamic>> addresses = [];
  bool isLoading = true;

  String _addressTypeName(dynamic addId) {
    switch (addId) {
      case 1:
        return 'HOME';
      case 2:
        return 'WORK';
      case 3:
        return 'COMPANY';
      case 4:
        return 'FAVORITE';
      case 5:
        return 'RESIDENTIAL';
      default:
        return 'ADDRESS';
    }
  }

  IconData _addressIcon(String label) {
    switch (label.toUpperCase()) {
      case 'HOME':
        return Icons.home_outlined;
      case 'WORK':
        return Icons.business_center_outlined;
      case 'COMPANY':
        return Icons.business_outlined;
      case 'FAVORITE':
        return Icons.star_outline_rounded;
      case 'RESIDENTIAL':
        return Icons.apartment_rounded;
      default:
        return Icons.location_on_outlined;
    }
  }

  String _formatLabel(String label) {
    switch (label.toUpperCase()) {
      case 'HOME':
        return 'Home';
      case 'WORK':
        return 'Office';
      case 'COMPANY':
        return 'Company';
      case 'FAVORITE':
        return 'Favorite';
      case 'RESIDENTIAL':
        return 'Residential';
      default:
        return label;
    }
  }

  @override
  void initState() {
    super.initState();
    _loadAddresses();
  }

  void _openAddAddressSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppTheme.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(
          top: Radius.circular(18),
        ),
      ),
      builder: (_) {
        return AddAddressSheet(
          email: widget.email,
          onSaved: (newAddress) {
            _loadAddresses();
          },
        );
      },
    );
  }

  Future<void> _setPrimaryAddress(int index) async {
    final selectedAddress = addresses[index];

    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/address/set-primary'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': widget.email,
          'uadd_id': selectedAddress['uadd_id'],
        }),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 200 && data['success'] == true) {
        setState(() {
          for (final address in addresses) {
            address['primary'] = false;
          }

          selectedAddress['primary'] = true;
        });
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              data['message'] ?? 'Unable to set primary address.',
            ),
          ),
        );
      }
    } catch (e) {
      debugPrint(e.toString());

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Connection error while updating address.'),
        ),
      );
    }
  }

  void _deleteAddress(int index) {
    setState(() {
      addresses.removeAt(index);
    });
  }

  void _openEditAddressSheet(Map<String, dynamic> address) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: AppTheme.white,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(
          top: Radius.circular(18),
        ),
      ),
      builder: (_) {
        return EditAddressSheet(
          email: widget.email,
          address: address,
          onUpdated: _loadAddresses,
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.lightGray,
      appBar: const AppBackHeader(
        title: 'My Address',
      ),
      body: isLoading
      ? const Center(
          child: CircularProgressIndicator(
            color: AppTheme.primaryRed,
          ),
        )
      : SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(20, 18, 20, 24),
          child: _addressListCard(),
        ),
    );
  }

  Widget _addressListCard() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _addAddressButton(),
        const SizedBox(height: 10),

        if (addresses.isEmpty)
          Center(
            child: Padding(
              padding: const EdgeInsets.only(top: 70),
              child: Column(
                children: [
                  Container(
                    width: 62,
                    height: 62,
                    decoration: BoxDecoration(
                      color: AppTheme.primaryRed.withOpacity(0.08),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.location_on_outlined,
                      color: AppTheme.primaryRed,
                      size: 30,
                    ),
                  ),

                  const SizedBox(height: 16),

                  const Text(
                    'No Address Yet',
                    style: TextStyle(
                      color: AppTheme.black,
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                    ),
                  ),

                  const SizedBox(height: 8),

                  const Padding(
                    padding: EdgeInsets.symmetric(horizontal: 28),
                    child: Text(
                      'Add your delivery or service address to make future bookings faster and more convenient.',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: AppTheme.gray,
                        fontSize: 12.5,
                        height: 1.45,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          ...addresses.asMap().entries.map((entry) {
          final index = entry.key;
          final item = entry.value;

          return _addressCard(
            label: item['label'],
            address: item['address'],
            isPrimary: item['primary'] == true,
            onSelect: () => _setPrimaryAddress(index),
            onEdit: () => _openEditAddressSheet(item),
            onDelete: () => _confirmDeleteAddress(item),
            );
        }),
      ],
    );
  }

  Widget _addressCard({
    required String label,
    required String address,
    required bool isPrimary,
    required VoidCallback onSelect,
    required VoidCallback onEdit,
    required VoidCallback onDelete,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: isPrimary ? AppTheme.primaryRed : AppTheme.borderGray,
          width: isPrimary ? 1.2 : 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                _addressIcon(label),
                color: AppTheme.black,
                size: 19,
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Row(
                  children: [
                    Text(
                      _formatLabel(label),
                      style: const TextStyle(
                        color: AppTheme.black,
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                      ),
                    ),

                    if (isPrimary) ...[
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 3,
                        ),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryRed.withOpacity(0.10),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: const Text(
                          'Primary',
                          style: TextStyle(
                            color: AppTheme.primaryRed,
                            fontSize: 10.5,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
              InkWell(
                borderRadius: BorderRadius.circular(20),
                onTap: onSelect,
                child: Icon(
                  isPrimary
                      ? Icons.check_box_rounded
                      : Icons.check_box_outline_blank_rounded,
                  color: isPrimary ? AppTheme.primaryRed : AppTheme.gray,
                  size: 22,
                ),
              ),
            ],
          ),

          const SizedBox(height: 10),

          Text(
            address,
            style: const TextStyle(
              color: AppTheme.black,
              fontSize: 12.5,
              height: 1.35,
              fontWeight: FontWeight.w500,
            ),
          ),

          const SizedBox(height: 20),

          Row(
            children: [
              InkWell(
                borderRadius: BorderRadius.circular(8),
                onTap: onEdit,
                child: const Padding(
                  padding: EdgeInsets.symmetric(
                    horizontal: 4,
                    vertical: 4,
                  ),
                  child: Text(
                    'Edit',
                    style: TextStyle(
                      color: AppTheme.primaryRed,
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ),

              const SizedBox(width: 16),

              InkWell(
                borderRadius: BorderRadius.circular(8),
                onTap: onDelete,
                child: const Padding(
                  padding: EdgeInsets.symmetric(
                    horizontal: 4,
                    vertical: 4,
                  ),
                  child: Text(
                    'Delete',
                    style: TextStyle(
                      color: Colors.red,
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _addAddressButton() {
    return InkWell(
      borderRadius: BorderRadius.circular(14),
      onTap: _openAddAddressSheet,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(
          horizontal: 14,
          vertical: 14,
        ),
        decoration: BoxDecoration(
          color: AppTheme.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: AppTheme.borderGray,
            width: 1,
          ),
        ),
        child: const Row(
          children: [
            Icon(
              Icons.add_rounded,
              color: Color.fromARGB(255, 58, 58, 58),
              size: 22,
            ),
            SizedBox(width: 12),
            Text(
              'Add New Address',
              style: TextStyle(
                color: Color.fromARGB(255, 58, 58, 58),
                fontSize: 13,
                fontWeight: FontWeight.w700,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _loadAddresses() async {
    setState(() {
      isLoading = true;
    });

    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/address/list'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': widget.email,
        }),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 200 && data['success'] == true) {
        final List fetchedAddresses = data['data'] ?? [];

        setState(() {
          addresses = fetchedAddresses.map<Map<String, dynamic>>((item) {
          return {
            'uadd_id': item['uadd_id'],
            'label': _addressTypeName(item['add_id']),
            'add_id': item['add_id'],

            'uadd_street': item['uadd_street'] ?? '',
            'uadd_barangay': item['uadd_barangay'] ?? '',
            'uadd_city': item['uadd_city'] ?? '',
            'uadd_province': item['uadd_province'] ?? '',
            'uadd_region': item['uadd_region'] ?? '',

            'address':
                '${item['uadd_street'] ?? ''}, ${item['uadd_barangay'] ?? ''}\n${item['uadd_city'] ?? ''}, ${item['uadd_province'] ?? ''}, ${item['uadd_region'] ?? ''}',

            'primary': item['uadd_active'] == 1,
          };
        }).toList();
        });
      }
    } catch (e) {
      debugPrint(e.toString());

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Unable to load addresses.'),
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          isLoading = false;
        });
      }
    }
  }

  Future<void> _confirmDeleteAddress(Map<String, dynamic> address) async {
    showDialog(
      context: context,
      builder: (context) {
        return DeleteAddressDialog(
          isPrimary: address['primary'] == true,
          onConfirm: () {
            _deleteAddressFromDatabase(address['uadd_id']);
          },
        );
      },
    );
  }

  Future<void> _deleteAddressFromDatabase(int uaddId) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/address/delete'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': widget.email,
          'uadd_id': uaddId,
        }),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 200 && data['success'] == true) {
        _loadAddresses();

        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Address deleted successfully.'),
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              data['message'] ?? 'Unable to delete address.',
            ),
          ),
        );
      }
    } catch (e) {
      debugPrint(e.toString());

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Connection error while deleting address.'),
        ),
      );
    }
  }
}