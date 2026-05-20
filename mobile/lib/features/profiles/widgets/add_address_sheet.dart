import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/config/api_config.dart';
import 'package:mobile_app/features/profiles/widgets/address_map_dialog.dart';

class AddAddressSheet extends StatefulWidget {
  final String email;
  final Function(Map<String, dynamic> address) onSaved;

  const AddAddressSheet({
    super.key,
    required this.email,
    required this.onSaved,
  });

  @override
  State<AddAddressSheet> createState() => _AddAddressSheetState();
}

class _AddAddressSheetState extends State<AddAddressSheet> {
  bool isInitializing = true;
  bool isLoadingUsedTypes = true;
  bool isSaving = false;

  double? selectedLatitude;
  double? selectedLongitude;

  List<int> unavailableAddressTypeIds = [];
  int selectedAddressTypeId = 1;

  List regions = [];
  List provinces = [];
  List municipalities = [];
  List barangays = [];

  int? selectedRegionId;
  int? selectedProvinceId;
  int? selectedMunicipalityId;
  int? selectedBarangayId;

  String? selectedRegionName;
  String? selectedProvinceName;
  String? selectedMunicipalityName;
  String? selectedBarangayName;

  final TextEditingController regionController = TextEditingController();
  final TextEditingController provinceController = TextEditingController();
  final TextEditingController municipalityController = TextEditingController();
  final TextEditingController barangayController = TextEditingController();
  final TextEditingController streetController = TextEditingController();
  final ScrollController sheetScrollController = ScrollController();

  final FocusNode regionFocusNode = FocusNode();
  final FocusNode provinceFocusNode = FocusNode();
  final FocusNode municipalityFocusNode = FocusNode();
  final FocusNode barangayFocusNode = FocusNode();

  final List<Map<String, dynamic>> addressTypes = [
    {'id': 1, 'name': 'HOME', 'label': 'Home', 'icon': Icons.home_rounded},
    {'id': 2, 'name': 'WORK', 'label': 'Office', 'icon': Icons.work_rounded},
    {'id': 3, 'name': 'COMPANY', 'label': 'Company', 'icon': Icons.business_rounded},
    {'id': 4, 'name': 'FAVORITE', 'label': 'Favorite', 'icon': Icons.star_rounded},
    {'id': 5, 'name': 'RESIDENTIAL', 'label': 'Residential', 'icon': Icons.apartment_rounded},
  ];

  bool get _isFormComplete {
    return selectedRegionId != null &&
        selectedProvinceId != null &&
        selectedMunicipalityId != null &&
        selectedBarangayId != null &&
        streetController.text.trim().isNotEmpty &&
        selectedLatitude != null &&
        selectedLongitude != null &&
        !unavailableAddressTypeIds.contains(selectedAddressTypeId);
  }

  Future<void> _openAddressMapDialog() async {
    final result = await showDialog<Map<String, dynamic>>(
      context: context,
      barrierDismissible: false,
      builder: (_) => const AddressMapDialog(),
    );

    if (result == null) return;

    setState(() {
      selectedLatitude = result['latitude'];
      selectedLongitude = result['longitude'];
    });
  }

  @override
  void initState() {
    super.initState();
    _initializeSheet();
  }

  @override
  void dispose() {
    regionController.dispose();
    provinceController.dispose();
    municipalityController.dispose();
    barangayController.dispose();
    streetController.dispose();

    regionFocusNode.dispose();
    provinceFocusNode.dispose();
    municipalityFocusNode.dispose();
    barangayFocusNode.dispose();
    sheetScrollController.dispose();

    super.dispose();
  }

  Future<void> _initializeSheet() async {
    setState(() {
      isInitializing = true;
      isLoadingUsedTypes = true;
    });

    await Future.wait([
      _loadRegions(),
      _loadUsedAddressTypes(),
    ]);

    if (!mounted) return;

    _selectFirstAvailableAddressType();

    setState(() {
      isInitializing = false;
    });
  }

  void _selectFirstAvailableAddressType() {
    final availableTypes = addressTypes.where(
      (type) => !unavailableAddressTypeIds.contains(type['id']),
    ).toList();

    if (availableTypes.isNotEmpty) {
      selectedAddressTypeId = availableTypes.first['id'];
    }
  }

  Future<void> _saveAddress() async {
    if (!_isFormComplete) {
      _showMessage('Please complete all address fields and pin your location on the map.');
      return;
    }

    setState(() => isSaving = true);

    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/address/store'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'email': widget.email,
          'add_id': selectedAddressTypeId,
          'uadd_street': streetController.text.trim(),
          'uadd_barangay': selectedBarangayName,
          'uadd_city': selectedMunicipalityName,
          'uadd_province': selectedProvinceName,
          'uadd_region': selectedRegionName,
          'uadd_latitude': selectedLatitude,
          'uadd_longitude': selectedLongitude,
        }),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if ((response.statusCode == 200 || response.statusCode == 201) &&
          data['success'] == true) {
        final selectedType = addressTypes.firstWhere(
          (type) => type['id'] == selectedAddressTypeId,
        );

        widget.onSaved({
          'label': selectedType['name'],
          'add_id': selectedAddressTypeId,
          'address':
              '${streetController.text.trim()}, $selectedBarangayName\n$selectedMunicipalityName, $selectedProvinceName, $selectedRegionName',
          'primary': false,
        });

        Navigator.pop(context);

        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Address saved successfully.')),
        );
      } else {
        _showMessage(data['message'] ?? 'Unable to save address.');
      }
    } catch (e) {
      debugPrint(e.toString());
      _showMessage('Connection error while saving address.');
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

  @override
  Widget build(BuildContext context) {
    final allTypesUsed =
        unavailableAddressTypeIds.length >= addressTypes.length;

    if (isInitializing) {
      return _loadingSheet();
    }

    return FractionallySizedBox(
      heightFactor: 0.82,
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
                keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.manual,
                padding: EdgeInsets.only(
                  left: 16,
                  right: 16,
                  bottom: MediaQuery.of(context).viewInsets.bottom + 18,
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 8),

                    _addressTypeChips(),

                    const SizedBox(height: 18),

                    if (allTypesUsed)
                      _smallNote(
                        'All address types are already added. Please edit an existing address instead.',
                      ),

                    _fieldLabel('Region'),
                    _locationPredictField(
                      controller: regionController,
                      focusNode: regionFocusNode,
                      enabled: !isSaving && !allTypesUsed,
                      hint: 'Select region',
                      items: regions,
                      idKey: 'reg_id',
                      nameKey: 'reg_name',
                      onTyping: () {
                        setState(() {
                          selectedRegionId = null;
                          selectedRegionName = null;
                          _clearProvinceDown();
                        });
                      },
                      onSelected: (item) async {
                        setState(() {
                          selectedRegionId = item['reg_id'];
                          selectedRegionName = item['reg_name'];
                          regionController.text = item['reg_name'];
                          _clearProvinceDown();
                        });

                        regionFocusNode.unfocus();
                        await _loadProvinces(item['reg_id']);
                      },
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('Province'),
                    _locationPredictField(
                      controller: provinceController,
                      focusNode: provinceFocusNode,
                      enabled: !isSaving && selectedRegionId != null && !allTypesUsed,
                      hint: selectedRegionId == null
                          ? 'Select region first'
                          : 'Select province',
                      items: provinces,
                      idKey: 'prov_id',
                      nameKey: 'prov_name',
                      lockedMessage: 'Choose a region first to show provinces.',
                      onTyping: () {
                        setState(() {
                          selectedProvinceId = null;
                          selectedProvinceName = null;
                          _clearMunicipalityDown();
                        });
                      },
                      onSelected: (item) async {
                        setState(() {
                          selectedProvinceId = item['prov_id'];
                          selectedProvinceName = item['prov_name'];
                          provinceController.text = item['prov_name'];
                          _clearMunicipalityDown();
                        });

                        provinceFocusNode.unfocus();
                        await _loadMunicipalities(item['prov_id']);
                      },
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('City / Municipality'),
                    _locationPredictField(
                      controller: municipalityController,
                      focusNode: municipalityFocusNode,
                      enabled: !isSaving && selectedProvinceId != null && !allTypesUsed,
                      hint: selectedProvinceId == null
                          ? 'Select province first'
                          : 'Select city / municipality',
                      items: municipalities,
                      idKey: 'mun_id',
                      nameKey: 'mun_name',
                      lockedMessage: 'Choose a province first to show cities.',
                      onTyping: () {
                        setState(() {
                          selectedMunicipalityId = null;
                          selectedMunicipalityName = null;
                          _clearBarangay();
                        });
                      },
                      onSelected: (item) async {
                        setState(() {
                          selectedMunicipalityId = item['mun_id'];
                          selectedMunicipalityName = item['mun_name'];
                          municipalityController.text = item['mun_name'];
                          _clearBarangay();
                        });

                        municipalityFocusNode.unfocus();
                        await _loadBarangays(item['mun_id']);
                      },
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('Barangay'),
                    _locationPredictField(
                      controller: barangayController,
                      focusNode: barangayFocusNode,
                      enabled: !isSaving && selectedMunicipalityId != null && !allTypesUsed,
                      hint: selectedMunicipalityId == null
                          ? 'Select city / municipality first'
                          : 'Select barangay',
                      items: barangays,
                      idKey: 'brg_id',
                      nameKey: 'brg_name',
                      lockedMessage:
                          'Choose a city or municipality first to show barangays.',
                      onTyping: () {
                        setState(() {
                          selectedBarangayId = null;
                          selectedBarangayName = null;
                        });
                      },
                      onSelected: (item) {
                        setState(() {
                          selectedBarangayId = item['brg_id'];
                          selectedBarangayName = item['brg_name'];
                          barangayController.text = item['brg_name'];
                        });

                        barangayFocusNode.unfocus();
                      },
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('Street / House No. / Building'),
                    TextField(
                      controller: streetController,
                      style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        color: AppTheme.black,
                      ),
                      enabled: !isSaving && !allTypesUsed,
                      textCapitalization: TextCapitalization.words,
                      decoration: _inputDecoration(
                        hint: 'Complete street, house no., or building',
                      ),
                      onChanged: (_) => setState(() {}),
                    ),

                    const SizedBox(height: 14),

                    _fieldLabel('Map Location'),

                    InkWell(
                      borderRadius: BorderRadius.circular(12),
                      onTap: isSaving || allTypesUsed ? null : _openAddressMapDialog,
                      child: Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(13),
                        decoration: BoxDecoration(
                          color: selectedLatitude != null && selectedLongitude != null
                                ? Colors.green.withOpacity(0.05)
                                : AppTheme.white,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                          color: selectedLatitude != null && selectedLongitude != null
                                ? Colors.green.withOpacity(0.35)
                                : AppTheme.borderGray,
                          ),
                        ),
                        child: Row(
                          children: [
                            Container(
                              width: 38,
                              height: 38,
                              decoration: BoxDecoration(
                                color: selectedLatitude != null && selectedLongitude != null
                                    ? Colors.green.withOpacity(0.10)
                                    : AppTheme.primaryRed.withOpacity(0.10),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: Icon(
                                selectedLatitude != null && selectedLongitude != null
                                    ? Icons.check_circle_rounded
                                    : Icons.location_on_outlined,
                                color: selectedLatitude != null && selectedLongitude != null
                                  ? Colors.green
                                  : AppTheme.primaryRed,
                                size: 21,
                              ),
                            ),

                            const SizedBox(width: 12),

                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    selectedLatitude != null && selectedLongitude != null
                                        ? 'Location pinned'
                                        : 'Pin exact location on map',
                                    style: const TextStyle(
                                      color: AppTheme.black,
                                      fontSize: 12.5,
                                      fontWeight: FontWeight.w800,
                                    ),
                                  ),
                                  const SizedBox(height: 3),
                                  Text(
                                    selectedLatitude != null && selectedLongitude != null
                                        ? 'Tap to adjust the pinned location.'
                                        : 'Required to accurately identify and serve your location.',
                                    style: const TextStyle(
                                      color: AppTheme.gray,
                                      fontSize: 11.5,
                                      height: 1.25,
                                    ),
                                  ),
                                ],
                              ),
                            ),

                            const SizedBox(width: 8),

                            const Icon(
                              Icons.chevron_right_rounded,
                              color: AppTheme.gray,
                              size: 22,
                            ),
                          ],
                        ),
                      ),
                    ),
                    
                    const SizedBox(height: 22),

                    SizedBox(
                      width: double.infinity,
                      height: 48,
                      child: LoadingButton(
                        isLoading: isSaving,
                        onPressed: _isFormComplete && !allTypesUsed
                            ? _saveAddress
                            : null,
                        child: const Text('Save Address'),
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

  Widget _loadingSheet() {
    return FractionallySizedBox(
      heightFactor: 0.25,
      child: Container(
        color: AppTheme.white,
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 20),
        child: Column(
          children: [
            _dragHandle(),
            const SizedBox(height: 36),
            const SizedBox(
              height: 28,
              width: 28,
              child: CircularProgressIndicator(
                strokeWidth: 2.6,
                color: AppTheme.primaryRed,
              ),
            ),
            const SizedBox(height: 14),
            const Text(
              'Preparing address form...',
              style: TextStyle(
                color: AppTheme.gray,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
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
                'Address Details',
                style: TextStyle(
                  color: AppTheme.black,
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                ),
              ),
              SizedBox(height: 3),
              Text(
                'Complete your service address for better appointment handling.',
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

  Widget _addressTypeChips() {
    final availableAddressTypes = addressTypes.where(
      (type) => !unavailableAddressTypeIds.contains(type['id']),
    ).toList();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Select address type',
          style: TextStyle(
            color: AppTheme.black,
            fontSize: 12.5,
            fontWeight: FontWeight.w800,
          ),
        ),
        const SizedBox(height: 9),

        SizedBox(
          height: 38,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            itemCount: availableAddressTypes.length,
            separatorBuilder: (_, __) => const SizedBox(width: 7),
            itemBuilder: (context, index) {
              final type = availableAddressTypes[index];
              final id = type['id'];
              final isSelected = selectedAddressTypeId == id;

              return InkWell(
                borderRadius: BorderRadius.circular(9),
                onTap: isSaving
                    ? null
                    : () {
                        setState(() {
                          selectedAddressTypeId = id;
                        });
                      },
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10),
                  decoration: BoxDecoration(
                    color: isSelected
                        ? AppTheme.primaryRed.withOpacity(0.08)
                        : AppTheme.white,
                    borderRadius: BorderRadius.circular(9),
                    border: Border.all(
                      color: isSelected
                          ? AppTheme.primaryRed
                          : AppTheme.borderGray,
                    ),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        type['icon'],
                        size: 16,
                        color: isSelected
                            ? AppTheme.primaryRed
                            : AppTheme.gray,
                      ),
                      const SizedBox(width: 5),
                      Text(
                        type['label'],
                        style: TextStyle(
                          color: isSelected
                              ? AppTheme.primaryRed
                              : AppTheme.gray,
                          fontSize: 11.5,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ),
      ],
    );
  }

  Widget _locationPredictField({
    required TextEditingController controller,
    required FocusNode focusNode,
    required bool enabled,
    required String hint,
    required List items,
    required String idKey,
    required String nameKey,
    required VoidCallback onTyping,
    required Function(Map<String, dynamic> item) onSelected,
    String? lockedMessage,
  }) {
    final suggestions = _suggestions(
      source: items,
      nameKey: nameKey,
      query: controller.text,
    );

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        TextField(
          controller: controller,
          focusNode: focusNode,
          enabled: enabled,
          textCapitalization: TextCapitalization.words,
          style: const TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w500,
            color: AppTheme.black,
          ),
          decoration: _inputDecoration(
            hint: hint,
            suffixIcon: enabled
                ? Icons.keyboard_arrow_down_rounded
                : Icons.lock_outline_rounded,
          ),
          onTap: () {
            if (!enabled && lockedMessage != null) {
              _showMessage(lockedMessage);
              return;
            }

            setState(() {});

            if (nameKey == 'reg_name') {
              _scrollSheetDown(80);
            } else if (nameKey == 'prov_name') {
              _scrollSheetDown(180);
            } else if (nameKey == 'mun_name') {
              _scrollSheetDown(280);
            } else if (nameKey == 'brg_name') {
              _scrollSheetDown(380);
            }
          },
          onChanged: (_) {
            onTyping();
            setState(() {});
          },
        ),

        if (enabled && focusNode.hasFocus && suggestions.isNotEmpty)
          Container(
            margin: const EdgeInsets.only(top: 6),
            constraints: const BoxConstraints(maxHeight: 170),
            decoration: BoxDecoration(
              color: AppTheme.white,
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: AppTheme.borderGray),
            ),
            child: ListView.separated(
              shrinkWrap: true,
              primary: false,
              keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.manual,
              padding: EdgeInsets.zero,
              itemCount: suggestions.length,
              separatorBuilder: (_, __) => const Divider(height: 1),
              itemBuilder: (context, index) {
                final item = suggestions[index];

                return ListTile(
                  dense: true,
                  visualDensity: VisualDensity.compact,
                  title: Text(
                    item[nameKey].toString(),
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 12.5),
                  ),
                  onTap: () => onSelected(item),
                );
              },
            ),
          ),
      ],
    );
  }

  InputDecoration _inputDecoration({
    required String hint,
    IconData? suffixIcon,
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
      suffixIcon: suffixIcon == null
          ? null
          : Icon(
              suffixIcon,
              color: AppTheme.gray,
              size: 19,
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
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Text(
        text,
        style: const TextStyle(
          color: AppTheme.gray,
          fontSize: 11.5,
          height: 1.35,
        ),
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

  List<Map<String, dynamic>> _suggestions({
    required List source,
    required String nameKey,
    required String query,
  }) {
    final sorted = source.cast<Map<String, dynamic>>().toList()
      ..sort(
        (a, b) => a[nameKey]
            .toString()
            .toLowerCase()
            .compareTo(b[nameKey].toString().toLowerCase()),
      );

    final search = query.toLowerCase().trim();

    if (search.isEmpty) return sorted;

    final exactMatch = sorted.any(
      (item) => item[nameKey].toString().toLowerCase().trim() == search,
    );

    if (exactMatch) return [];

    return sorted.where((item) {
      return item[nameKey].toString().toLowerCase().contains(search);
    }).toList();
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

  void _clearProvinceDown() {
    selectedProvinceId = null;
    selectedMunicipalityId = null;
    selectedBarangayId = null;

    selectedProvinceName = null;
    selectedMunicipalityName = null;
    selectedBarangayName = null;

    provinceController.clear();
    municipalityController.clear();
    barangayController.clear();

    provinces = [];
    municipalities = [];
    barangays = [];
  }

  void _clearMunicipalityDown() {
    selectedMunicipalityId = null;
    selectedBarangayId = null;

    selectedMunicipalityName = null;
    selectedBarangayName = null;

    municipalityController.clear();
    barangayController.clear();

    municipalities = [];
    barangays = [];
  }

  void _clearBarangay() {
    selectedBarangayId = null;
    selectedBarangayName = null;
    barangayController.clear();
    barangays = [];
  }

  Future<void> _loadRegions() async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/location/regions'),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      setState(() {
        regions = data['data'] ?? [];
      });
    } catch (e) {
      debugPrint(e.toString());
    }
  }

  Future<void> _loadProvinces(int regId) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/location/provinces/$regId'),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      setState(() {
        provinces = data['data'] ?? [];
      });
    } catch (e) {
      debugPrint(e.toString());
    }
  }

  Future<void> _loadMunicipalities(int provId) async {
    try {
      final response = await http.get(
        Uri.parse(
          '${ApiConfig.baseUrl}/api/mobile/location/municipalities/$provId',
        ),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      setState(() {
        municipalities = data['data'] ?? [];
      });
    } catch (e) {
      debugPrint(e.toString());
    }
  }

  Future<void> _loadBarangays(int munId) async {
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/location/barangays/$munId'),
      );

      final data = jsonDecode(response.body);

      if (!mounted) return;

      setState(() {
        barangays = data['data'] ?? [];
      });
    } catch (e) {
      debugPrint(e.toString());
    }
  }

  Future<void> _loadUsedAddressTypes() async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/address/used-types'),
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
        unavailableAddressTypeIds =
            List<int>.from(data['data'].map((item) => item as int));
      }
    } catch (e) {
      debugPrint(e.toString());
    } finally {
      if (mounted) {
        setState(() {
          isLoadingUsedTypes = false;
        });
      }
    }
  }
}