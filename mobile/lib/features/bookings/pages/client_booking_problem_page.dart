import 'dart:io';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:mobile_app/config/api_config.dart';
import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/shared/widgets/headers/app_back_header.dart';
import 'package:mobile_app/features/bookings/widgets/booking_step_indicator.dart';
import 'package:mobile_app/features/bookings/pages/client_booking_schedule_page.dart';
import 'package:image_picker/image_picker.dart';
import 'package:mobile_app/features/bookings/widgets/booking_problem_card.dart';
import 'package:mobile_app/features/bookings/widgets/booking_area_card.dart';
import 'package:mobile_app/features/bookings/widgets/booking_termite_sqm_card.dart';

  class ClientBookingProblemPage extends StatefulWidget {
    final String email;
    final Map<String, dynamic> selectedAddress;

    const ClientBookingProblemPage({
      super.key,
      required this.email,
      required this.selectedAddress,
    });

    @override
    State<ClientBookingProblemPage> createState() =>
        _ClientBookingProblemPageState();
  }

  class _ClientBookingProblemPageState extends State<ClientBookingProblemPage> {

    List<Map<String, dynamic>> termiteSqmOptions = [];
    Map<String, dynamic>? selectedTermiteSqm;
    bool isLoadingTermiteSqm = false;

    List<Map<String, dynamic>> selectedServicePackages = [];
    List<Map<String, dynamic>> selectedAreas = [];
    List<XFile> selectedImages = [];
    final ImagePicker _picker = ImagePicker();

    List<Map<String, dynamic>> servicePackages = [];
    bool isLoadingPackages = false;

    List<Map<String, dynamic>> serviceAreas = [];
    bool isLoadingAreas = false;

    final TextEditingController descriptionController =
        TextEditingController();

    double get totalPrice {
      double total = 0;

      for (final area in selectedAreas) {
        total +=
            double.tryParse(area['cost'].toString()) ?? 0;
      }

      return total;
    }

    void _showMessage(String message) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(message)),
      );
    }

    bool get hasTermitesSelected {
      return selectedServicePackages.any(
        (service) => service['name'].toString().toUpperCase() == 'TERMITES',
      );
    }

    bool get isPageLoading {
      return isLoadingPackages || isLoadingAreas || isLoadingTermiteSqm;
    }

    @override
    void initState() {
      super.initState();
      _loadServicePackages();
      _loadServiceAreas(2);
      _loadTermiteSqmOptions(2);

      descriptionController.addListener(() {
        if (!mounted) return;
        setState(() {});
      });
    }

    @override
    void dispose() {
      descriptionController.dispose();
      super.dispose();
    }

    @override
    Widget build(BuildContext context) {
      return Scaffold(
        backgroundColor: AppTheme.lightGray,
        appBar: const AppBackHeader(
          title: 'Book Service',
        ),
        body: isPageLoading
    ? const Center(
        child: CircularProgressIndicator(
          color: AppTheme.primaryRed,
        ),
      )
      : Column(
          children: [
            const BookingStepIndicator(currentStep: 2),
            Expanded(
              child: SingleChildScrollView(
                padding: EdgeInsets.all(20),
                child: Column(
                  children: [
                    BookingProblemCard(
                      isLoadingPackages: isLoadingPackages,
                      servicePackages: servicePackages,
                      selectedServicePackages: selectedServicePackages,
                      onToggleService: (service) {
                        setState(() {
                          final serviceName =
                              service['name'].toString().toUpperCase();

                          if (serviceName == 'TERMITES') {
                            final alreadySelected =
                                selectedServicePackages.any(
                              (selected) =>
                                  selected['name']
                                      .toString()
                                      .toUpperCase() ==
                                  'TERMITES',
                            );

                            if (alreadySelected) {
                              selectedServicePackages.clear();
                              selectedAreas.clear();
                              selectedTermiteSqm = null;
                              return;
                            }

                            selectedServicePackages.clear();
                            selectedAreas.clear();
                            selectedTermiteSqm = null;
                            selectedServicePackages.add(service);
                            return;
                          }

                          selectedTermiteSqm = null;

                          selectedServicePackages.removeWhere(
                            (selected) =>
                                selected['name'].toString().toUpperCase() ==
                                'TERMITES',
                          );

                          final isSelected = selectedServicePackages.any(
                            (selected) => selected['id'] == service['id'],
                          );

                          if (isSelected) {
                            selectedServicePackages.removeWhere(
                              (selected) => selected['id'] == service['id'],
                            );
                          } else {
                            selectedServicePackages.add(service);
                          }
                        });
                      },
                      onClearAll: () {
                        setState(() {
                          selectedServicePackages.clear();
                          selectedAreas.clear();
                          selectedTermiteSqm = null;
                        });
                      },
                    ),

                    const SizedBox(height: 20),

                    if (hasTermitesSelected)
                      BookingTermiteSqmCard(
                        isLoading: isLoadingTermiteSqm,
                        termiteSqmOptions: termiteSqmOptions,
                        selectedTermiteSqm: selectedTermiteSqm,
                        onSelect: (option) {
                          setState(() {
                            selectedTermiteSqm = option;
                          });
                        },
                      )
                    else
                      BookingAreaCard(
                        serviceAreas: serviceAreas,
                        selectedAreas: selectedAreas,
                        onToggleArea: (area) {
                          setState(() {
                            final isSelected = selectedAreas.any(
                              (selected) => selected['id'] == area['id'],
                            );

                            if (isSelected) {
                              selectedAreas.removeWhere(
                                (selected) => selected['id'] == area['id'],
                              );
                            } else {
                              selectedAreas.add(area);
                            }
                          });
                        },
                        onClearAll: () {
                          setState(() {
                            selectedAreas.clear();
                          });
                        },
                      ),

                    const SizedBox(height: 20),
                    _descriptionCard(),
                    const SizedBox(height: 20),
                    _photoCard(),
                  ],
                ),
              ),
            ),
            _bottomBar(),
          ],
        ),
      );
    }

    Widget _descriptionCard() {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Text(
                'Describe the problem',
                style: TextStyle(
                  color: AppTheme.black,
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
              SizedBox(width: 4),
              Text(
                '(optional)',
                style: TextStyle(
                  color: AppTheme.gray,
                  fontSize: 13,
                ),
              ),
            ],
          ),

          const SizedBox(height: 6),

          const Text(
            'Tell us more about the pest problem you’re experiencing.',
            style: TextStyle(
              color: AppTheme.gray,
              fontSize: 13,
              height: 1.3,
            ),
          ),

          const SizedBox(height: 12),

          Stack(
            children: [
              TextField(
                controller: descriptionController,
                textAlignVertical: TextAlignVertical.top,
                maxLines: 4,
                maxLength: 500,
                decoration: InputDecoration(
                  hintText: 'Type your problem here...',
                  hintStyle: const TextStyle(
                    color: AppTheme.gray,
                    fontSize: 12,
                  ),
                  counterText: '',
                  filled: true,
                  fillColor: AppTheme.white,
                  contentPadding: const EdgeInsets.fromLTRB(14, 14, 14, 34),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: const BorderSide(color: AppTheme.borderGray),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: const BorderSide(color: AppTheme.primaryRed),
                  ),
                ),
              ),

              Positioned(
                right: 12,
                bottom: 10,
                child: Text(
                  '${descriptionController.text.length}/500',
                  style: const TextStyle(
                    color: AppTheme.gray,
                    fontSize: 11,
                  ),
                ),
              ),
            ],
          ),
        ],
      );
    }

    Widget _photoCard() {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Text(
                'Add photos',
                style: TextStyle(
                  color: AppTheme.black,
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
              SizedBox(width: 4),
              Text(
                '(optional)',
                style: TextStyle(
                  color: AppTheme.gray,
                  fontSize: 13,
                ),
              ),
            ],
          ),

          const SizedBox(height: 6),

          const Text(
            'Attach clear photos to help us understand the problem better.',
            style: TextStyle(
              color: AppTheme.gray,
              fontSize: 13,
              height: 1.3,
            ),
          ),

          const SizedBox(height: 14),

          SizedBox(
            height: 98,
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: selectedImages.length + 1,
              separatorBuilder: (_, __) => const SizedBox(width: 12),
              itemBuilder: (context, index) {

                // ADD PHOTO CARD
                if (index == 0) {
                  return GestureDetector(
                    onTap: _pickImages,
                    child: Container(
                    width: 110,
                    decoration: BoxDecoration(
                      color: AppTheme.white,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(
                        color: AppTheme.primaryRed.withOpacity(0.35),
                      ),
                    ),
                      child: const Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.camera_alt,
                          color: AppTheme.primaryRed,
                          size: 30,
                        ),

                        SizedBox(height: 8),

                        Text(
                          'Add Photo',
                          style: TextStyle(
                            color: AppTheme.primaryRed,
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                    ),
                  );
                }

                final image = selectedImages[index - 1];

                return Stack(
                  children: [
                    Container(
                      width: 112,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(14),
                        image: DecorationImage(
                          image: FileImage(
                            File(image.path),
                          ),
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),

                    Positioned(
                      top: 8,
                      right: 8,
                      child: GestureDetector(
                        onTap: () {
                          setState(() {
                            selectedImages.removeAt(index - 1);
                          });
                        },
                        child: Container(
                          width: 24,
                          height: 24,
                          decoration: const BoxDecoration(
                            color: AppTheme.white,
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.close,
                            size: 16,
                            color: AppTheme.black,
                          ),
                        ),
                      ),
                    ),
                  ],
                );
              },
            ),
          ),

          const SizedBox(height: 10),

          Row(
            children: [
              Icon(
                Icons.info_outline,
                size: 14,
                color: AppTheme.gray.withOpacity(0.9),
              ),

              const SizedBox(width: 5),

              Text(
                'You can add up to 10 photos',
                style: TextStyle(
                  color: AppTheme.gray.withOpacity(0.9),
                  fontSize: 12,
                ),
              ),
            ],
          ),
        ],
      );
    }

    Widget _bottomBar() {
      return Container(
        padding: const EdgeInsets.all(16),
        color: AppTheme.white,
        child: ElevatedButton(
          onPressed: selectedServicePackages.isNotEmpty && (hasTermitesSelected ? selectedTermiteSqm != null : selectedAreas.isNotEmpty)
              ? () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => ClientBookingSchedulePage(
                        email: widget.email,
                        selectedAddress: widget.selectedAddress,
                        selectedServicePackages: selectedServicePackages,
                        selectedAreas: selectedAreas,
                        selectedTermiteSqm: selectedTermiteSqm,
                        description: descriptionController.text.trim(),
                        selectedImages: selectedImages,
                      ),
                    ),
                  );
                }
              : null,
          child: const Text('Continue'),
        ),
      );
    }

    Future<void> _loadServicePackages() async {
      setState(() {
        isLoadingPackages = true;
      });

      try {
        final response = await http.get(
          Uri.parse('${ApiConfig.baseUrl}/api/mobile/service-packages'),
          headers: {
            'Accept': 'application/json',
          },
        );

        if (!mounted) return;

        final data = jsonDecode(response.body);

        if (data['success'] == true) {
          final List packageData = data['data'] ?? [];

          setState(() {
            servicePackages = packageData.map((item) {
              return {
                'id': item['svcp_id'],
                'name': item['svcp_pest_type'],
              };
            }).toList();

            servicePackages.sort((a, b) {
              if (a['name'] == 'OTHERS') return 1;
              if (b['name'] == 'OTHERS') return -1;
              return 0;
            });
          });
        }
      } catch (e) {
        _showMessage('Unable to load service packages.');
      } finally {
        if (mounted) {
          setState(() {
            isLoadingPackages = false;
          });
        }
      }
    }

    Future<void> _loadServiceAreas(int branchId) async {
      setState(() {
        isLoadingAreas = true;
      });

      try {
        final response = await http.get(
          Uri.parse('${ApiConfig.baseUrl}/api/mobile/service-package-areas/$branchId'),
          headers: {
            'Accept': 'application/json',
          },
        );

        if (!mounted) return;

        final data = jsonDecode(response.body);

        if (data['success'] == true) {
          final List areaData = data['data'] ?? [];

          setState(() {
            serviceAreas = areaData.map((item) {
              return {
                'id': item['svcpa_id'],
                'area': item['svcpa_area'],
                'cost': item['svcpa_cost'],
              };
            }).toList();
          });
        }
      } catch (e) {
        _showMessage('Unable to load service areas.');
      } finally {
        if (mounted) {
          setState(() {
            isLoadingAreas = false;
          });
        }
      }
    }

    Future<void> _loadTermiteSqmOptions(int branchId) async {
      setState(() {
        isLoadingTermiteSqm = true;
      });

      try {
        final response = await http.get(
          Uri.parse('${ApiConfig.baseUrl}/api/mobile/service-package-area-termites/$branchId'),
          headers: {
            'Accept': 'application/json',
          },
        );

        if (!mounted) return;

        final data = jsonDecode(response.body);

        if (data['success'] == true) {
          final List sqmData = data['data'] ?? [];

          setState(() {
            termiteSqmOptions = sqmData.map((item) {
              return {
                'id': item['svcpat_id'],
                'sqm_details': item['svcpat_sqm_details'],
                'cost': item['svcpat_cost'],
              };
            }).toList();
          });
        }
      } catch (e) {
        _showMessage('Unable to load termite treatment sizes.');
      } finally {
        if (mounted) {
          setState(() {
            isLoadingTermiteSqm = false;
          });
        }
      }
    }

    Future<void> _pickImages() async {
      if (selectedImages.length >= 10) {
        _showMessage('Maximum of 10 photos only.');
        return;
      }

      final List<XFile> images = await _picker.pickMultiImage();

      if (!mounted) return;

      if (images.isNotEmpty) {
        setState(() {
          final remaining = 10 - selectedImages.length;
          selectedImages.addAll(images.take(remaining),
          );
        });
      }
    }
  }