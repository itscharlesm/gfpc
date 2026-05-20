import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/config/api_config.dart';

class EditAddressSheet extends StatefulWidget {
  final String email;
  final Map<String, dynamic> address;
  final VoidCallback onUpdated;

  const EditAddressSheet({
    super.key,
    required this.email,
    required this.address,
    required this.onUpdated,
  });

  @override
  State<EditAddressSheet> createState() => _EditAddressSheetState();
}

class _EditAddressSheetState extends State<EditAddressSheet> {
  bool isSaving = false;

  final ScrollController sheetScrollController = ScrollController();

  late TextEditingController streetController;
  late TextEditingController barangayController;
  late TextEditingController cityController;
  late TextEditingController provinceController;
  late TextEditingController regionController;

  bool get _isFormComplete {
    return streetController.text.trim().isNotEmpty &&
        cityController.text.trim().isNotEmpty &&
        barangayController.text.trim().isNotEmpty &&
        provinceController.text.trim().isNotEmpty &&
        regionController.text.trim().isNotEmpty;
  }

  @override
  void initState() {
    super.initState();

    streetController = TextEditingController(
      text: widget.address['uadd_street'] ?? '',
    );
    barangayController = TextEditingController(
      text: widget.address['uadd_barangay'] ?? '',
    );
    cityController = TextEditingController(
      text: widget.address['uadd_city'] ?? '',
    );
    provinceController = TextEditingController(
      text: widget.address['uadd_province'] ?? '',
    );
    regionController = TextEditingController(
      text: widget.address['uadd_region'] ?? '',
    );
  }

  @override
  void dispose() {
    streetController.dispose();
    barangayController.dispose();
    cityController.dispose();
    provinceController.dispose();
    regionController.dispose();
    sheetScrollController.dispose();
    super.dispose();
  }

  Future<void> _updateAddress() async {
    if (!_isFormComplete) {
      _showMessage('Please complete all address fields.');
      return;
    }

    setState(() => isSaving = true);

    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/address/update'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': widget.email,
          'uadd_id': widget.address['uadd_id'],
          'uadd_street': streetController.text.trim(),
          'uadd_barangay': barangayController.text.trim(),
          'uadd_city': cityController.text.trim(),
          'uadd_province': provinceController.text.trim(),
          'uadd_region': regionController.text.trim(),
        }),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 200 && data['success'] == true) {
        widget.onUpdated();
        Navigator.pop(context);

        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Address updated successfully.')),
        );
      } else {
        _showMessage(data['message'] ?? 'Unable to update address.');
      }
    } catch (e) {
      debugPrint(e.toString());
      _showMessage('Connection error while updating address.');
    } finally {
      if (mounted) {
        setState(() => isSaving = false);
      }
    }
  }

  void _showMessage(String message) {
    if (!mounted) return;

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  }

  void _scrollSheetDown(double offset) {
    Future.delayed(const Duration(milliseconds: 250), () {
      if (!mounted || !sheetScrollController.hasClients) return;

      sheetScrollController.animateTo(
        offset,
        duration: const Duration(milliseconds: 260),
        curve: Curves.easeOut,
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    return FractionallySizedBox(
      heightFactor: 0.78,
      child: Container(
        color: AppTheme.white,
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 8),
              child: Column(
                children: [
                  _dragHandle(),
                  const SizedBox(height: 14),
                  _sheetHeader(),
                ],
              ),
            ),

            Expanded(
              child: SingleChildScrollView(
                controller: sheetScrollController,
                keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
                padding: EdgeInsets.only(
                  left: 16,
                  right: 16,
                  bottom: MediaQuery.of(context).viewInsets.bottom + 18,
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 8),

                    _smallNote(
                      'Review and update the saved address information below.',
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('Street / House No. / Building'),
                    _textField(
                      controller: streetController,
                      hint: 'Complete street, house no., or building',
                      scrollOffset: 40,
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('Barangay'),
                    _textField(
                      controller: barangayController,
                      hint: 'Enter barangay',
                      scrollOffset: 120,
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('City / Municipality'),
                    _textField(
                      controller: cityController,
                      hint: 'Enter city or municipality',
                      scrollOffset: 200,
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('Province'),
                    _textField(
                      controller: provinceController,
                      hint: 'Enter province',
                      scrollOffset: 280,
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('Region'),
                    _textField(
                      controller: regionController,
                      hint: 'Enter region',
                      scrollOffset: 360,
                    ),

                    const SizedBox(height: 22),

                    SizedBox(
                      width: double.infinity,
                      height: 48,
                      child: LoadingButton(
                        isLoading: isSaving,
                        onPressed: _isFormComplete && !isSaving
                            ? _updateAddress
                            : null,
                        child: const Text('Update Address'),
                      ),
                    ),

                    const SizedBox(height: 14),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _textField({
    required TextEditingController controller,
    required String hint,
    required double scrollOffset,
  }) {
    return TextField(
      controller: controller,
      enabled: !isSaving,
      textCapitalization: TextCapitalization.words,
      style: const TextStyle(
        fontSize: 12,
        fontWeight: FontWeight.w500,
        color: AppTheme.black,
      ),
      decoration: _inputDecoration(hint: hint),
      onTap: () => _scrollSheetDown(scrollOffset),
      onChanged: (_) => setState(() {}),
    );
  }

  InputDecoration _inputDecoration({
    required String hint,
  }) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(
        color: AppTheme.gray,
        fontSize: 12,
        fontWeight: FontWeight.w500,
      ),
      contentPadding: const EdgeInsets.symmetric(
        horizontal: 12,
        vertical: 12,
      ),
      filled: true,
      fillColor: AppTheme.white,
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(9),
        borderSide: const BorderSide(color: AppTheme.borderGray),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(9),
        borderSide: const BorderSide(color: AppTheme.primaryRed),
      ),
      disabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(9),
        borderSide: const BorderSide(color: AppTheme.borderGray),
      ),
    );
  }

  Widget _sheetHeader() {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Edit Address',
                style: TextStyle(
                  color: AppTheme.black,
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                ),
              ),
              SizedBox(height: 3),
              Text(
                'Update your saved service address for better appointment handling.',
                style: TextStyle(
                  color: AppTheme.gray,
                  fontSize: 11.5,
                  height: 1.25,
                ),
              ),
            ],
          ),
        ),
        InkWell(
          borderRadius: BorderRadius.circular(10),
          onTap: isSaving ? null : () => Navigator.pop(context),
          child: Container(
            width: 34,
            height: 34,
            decoration: BoxDecoration(
              color: AppTheme.white,
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: AppTheme.borderGray),
            ),
            child: const Icon(
              Icons.close_rounded,
              color: AppTheme.black,
              size: 20,
            ),
          ),
        ),
      ],
    );
  }

  Widget _fieldLabel(String label) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Text(
        '$label',
        style: const TextStyle(
          color: AppTheme.black,
          fontSize: 12,
          fontWeight: FontWeight.w800,
        ),
      ),
    );
  }

  Widget _smallNote(String text) {
    return Text(
      text,
      style: const TextStyle(
        color: AppTheme.gray,
        fontSize: 11.5,
        height: 1.35,
      ),
    );
  }

  Widget _dragHandle() {
    return Center(
      child: Container(
        width: 38,
        height: 4,
        decoration: BoxDecoration(
          color: AppTheme.borderGray,
          borderRadius: BorderRadius.circular(20),
        ),
      ),
    );
  }
}