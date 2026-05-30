import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/shared/shared.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:mobile_app/config/api_config.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import 'package:image_cropper/image_cropper.dart';
import 'package:mobile_app/shared/widgets/bottom_sheets/app_bottom_sheet.dart';
import 'package:mobile_app/features/profiles/widgets/client_edit_profile_skeleton_load.dart';

class ClientEditProfilePage extends StatefulWidget {
  final String email;

  const ClientEditProfilePage({
    super.key,
    required this.email,
  });

  @override
  State<ClientEditProfilePage> createState() => _ClientEditProfilePageState();
}

class _ClientEditProfilePageState extends State<ClientEditProfilePage> {

  XFile? _selectedImage;
  bool isSaving = false;
  bool isFetching = true;
  String? profileImagePath;
  bool isImageRemoved = false;

  final TextEditingController firstNameController = TextEditingController();

  final TextEditingController middleNameController = TextEditingController();

  final TextEditingController lastNameController = TextEditingController();

  final TextEditingController phoneController = TextEditingController();

  final TextEditingController birthDateController = TextEditingController();

  final TextEditingController emailController = TextEditingController();

  DateTime? selectedDate;

  String formatName(String value) {
    if (value.isEmpty) return '';
    return value
        .toLowerCase()
        .split(' ')
        .map((word) =>
            word.isNotEmpty
                ? word[0].toUpperCase() + word.substring(1)
                : '')
        .join(' ');
  }

  String formatPhone(String value) {
    if (value.isEmpty) return '';

    // remove spaces just in case
    value = value.replaceAll(' ', '');

    if (!value.startsWith('0')) {
      return '0$value';
    }

    return value;
  }

  @override
  void initState() {
    super.initState();
    emailController.text = widget.email;
    _fetchUserProfile();
  }

  @override
  void dispose() {
    firstNameController.dispose();
    middleNameController.dispose();
    lastNameController.dispose();
    phoneController.dispose();
    emailController.dispose();
    birthDateController.dispose();
    super.dispose();
  }

  Future<void> _editSelectedImage() async {
    if (_selectedImage == null) return;

    await _cropImage(_selectedImage!.path);
  }

  Future<void> _fetchUserProfile() async {
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
          firstNameController.text =
              formatName(user['usr_first_name'] ?? '');

          middleNameController.text =
              formatName(user['usr_middle_name'] ?? '');

          lastNameController.text =
              formatName(user['usr_last_name'] ?? '');

          phoneController.text = formatPhone(user['usr_mobile'] ?? '');
          birthDateController.text = user['usr_birth_date'] ?? '';
          emailController.text = user['usr_email'] ?? widget.email;
          profileImagePath = user['usr_image_path'];
          isFetching = false;
        });
      } else {
        setState(() => isFetching = false);
      }
    } catch (e) {
      if (!mounted) return;
      setState(() => isFetching = false);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Unable to load profile: $e'),
        ),
      );
    }
  }

  Future<void> _cropImage(String imagePath) async {
    final croppedFile = await ImageCropper().cropImage(
      sourcePath: imagePath,
      aspectRatio: const CropAspectRatio(ratioX: 1, ratioY: 1),
      uiSettings: [
        AndroidUiSettings(
          toolbarTitle: 'Adjust Profile Photo',
          toolbarColor: AppTheme.primaryRed,
          toolbarWidgetColor: Colors.white,
          lockAspectRatio: true,
        ),
        IOSUiSettings(
          title: 'Adjust Profile Photo',
          aspectRatioLockEnabled: true,
        ),
      ],
    );

    if (croppedFile != null) {
      setState(() {
        _selectedImage = XFile(croppedFile.path);
        isImageRemoved = false;
      });
    }
  }

  Future<void> _pickDate() async {
    final now = DateTime.now();

    DateTime initialDate;

    if (birthDateController.text.isNotEmpty) {
      initialDate = DateTime.parse(birthDateController.text);
    } else {
      initialDate = DateTime(now.year - 18, now.month, now.day);
    }

    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: initialDate,
      firstDate: DateTime(1950),
      lastDate: now,
    );

    if (picked != null) {
      setState(() {
        selectedDate = picked;
        birthDateController.text = DateFormat('yyyy-MM-dd').format(picked);
      });
    }
  }

  Future<void> _saveProfile() async {
    if (firstNameController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("First name is required.")),
      );
      return;
    }

    if (lastNameController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Last name is required.")),
      );
      return;
    }

    setState(() => isSaving = true);

    try {
      final request = http.MultipartRequest(
        'POST',
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/profile/update'),
      );

      request.fields['email'] = emailController.text.trim();
      request.fields['first_name'] = firstNameController.text.trim();
      request.fields['middle_name'] = middleNameController.text.trim();
      request.fields['last_name'] = lastNameController.text.trim();
      request.fields['birth_date'] = birthDateController.text.trim();
      request.fields['remove_image'] = isImageRemoved ? '1' : '0';

      if (_selectedImage != null) {
        request.files.add(
          await http.MultipartFile.fromPath(
            'profile_image',
            _selectedImage!.path,
          ),
        );
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      final data = jsonDecode(response.body);

      if (!mounted) return;

      if (response.statusCode == 200 && data['success'] == true) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Profile updated successfully.")),
        );

        Navigator.pop(context);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(data['message'] ?? "Failed to update profile.")),
        );
      }
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Unable to update profile: $e")),
      );
    } finally {
      if (mounted) {
        setState(() => isSaving = false);
      }
    }
  }

  Future<void> _pickImageFromGallery() async {
    final ImagePicker picker = ImagePicker();

    final XFile? image = await picker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 70,
    );

    if (image != null) {
      await _cropImage(image.path);
    }
  }

  Future<void> _pickImageFromCamera() async {
    final ImagePicker picker = ImagePicker();

    final XFile? image = await picker.pickImage(
      source: ImageSource.camera,
      imageQuality: 70,
    );

    if (image != null) {
      await _cropImage(image.path);
    }
  }

  void _showPhotoOptions() {
    final bool hasImage =
        _selectedImage != null ||
        (profileImagePath != null && profileImagePath!.isNotEmpty);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return AppBottomSheet(
          children: [
            AppBottomSheetItem(
              icon: Icons.photo_library_outlined,
              title: "Choose from Gallery",
              onTap: () {
                Navigator.pop(context);
                _pickImageFromGallery();
              },
            ),

            AppBottomSheetItem(
              icon: Icons.camera_alt_outlined,
              title: "Take a Photo",
              showDivider: hasImage,
              onTap: () {
                Navigator.pop(context);
                _pickImageFromCamera();
              },
            ),

            if (_selectedImage != null) ...[
              AppBottomSheetItem(
                icon: Icons.edit_outlined,
                title: "Edit Current Photo",
                onTap: () {
                  Navigator.pop(context);
                  _editSelectedImage();
                },
              ),
            ],

            if (hasImage) ...[
              AppBottomSheetItem(
                icon: Icons.delete_outline,
                title: "Remove Photo",
                showDivider: false,
                isDestructive: true,
                onTap: () {
                  Navigator.pop(context);

                  setState(() {
                    _selectedImage = null;
                    profileImagePath = null;
                    isImageRemoved = true;
                  });
                },
              ),
            ],
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: const AppBackHeader(
        title: "Edit Profile",
      ),
      body: isFetching
      ? const ClientEditProfileSkeletonLoad()
      : SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(20, 22, 20, 30),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 🔴 PROFILE IMAGE
            Center(
              child: Column(
                children: [
                  GestureDetector(
                    onTap: _showPhotoOptions,
                    child: Stack(
                      children: [
                        CircleAvatar(
                          radius: 46,
                          backgroundColor: AppTheme.primaryRed,
                          backgroundImage: _selectedImage != null
                          ? FileImage(File(_selectedImage!.path))
                          : (profileImagePath != null && profileImagePath!.isNotEmpty
                              ? NetworkImage('${ApiConfig.baseUrl}/$profileImagePath')
                              : null),
                          child: (_selectedImage == null &&
                              (profileImagePath == null || profileImagePath!.isEmpty))
                          ? const Icon(
                              Icons.person,
                              color: Colors.white,
                              size: 46,
                            )
                          : null,
                        ),
                        Positioned(
                          bottom: 2,
                          right: 2,
                          child: Container(
                            padding: const EdgeInsets.all(6),
                            decoration: const BoxDecoration(
                              color: Colors.white,
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(
                              Icons.camera_alt,
                              color: AppTheme.primaryRed,
                              size: 16,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    "Tap to change photo",
                    style: TextStyle(
                      color: AppTheme.gray,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 28),

            // NAME FIELDS
            _input("First Name", firstNameController, Icons.person_outline),
            const SizedBox(height: 16),

            _input("Middle Name", middleNameController, Icons.person_outline),
            const SizedBox(height: 16),

            _input("Last Name", lastNameController, Icons.person_outline),
            const SizedBox(height: 16),

            // EMAIL (READ ONLY)
            _input("Email", emailController, Icons.email_outlined,
                readOnly: true), //readonly kay need api
            const SizedBox(height: 16),

            // PHONE
            _input(
              "Mobile Number",
              phoneController,
              Icons.phone_outlined,
              readOnly: true, //readonly sani kay need ni ug api for verification
            ),
            const SizedBox(height: 16),

            // BIRTHDATE
            GestureDetector(
              onTap: _pickDate,
              child: AbsorbPointer(
                child: _input(
                  "Birth Date",
                  birthDateController,
                  Icons.calendar_month_outlined,
                ),
              ),
            ),

            const SizedBox(height: 24),

            // INFO BOX
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppTheme.primaryRed.withOpacity(0.06),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Row(
                children: const [
                  Icon(Icons.shield_outlined, color: AppTheme.primaryRed),
                  SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      "Your information is secure and used only for service purposes.",
                      style: TextStyle(fontSize: 13),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 28),

            // SAVE BUTTON
            LoadingButton(
              isLoading: isSaving,
              onPressed: isSaving ? null : _saveProfile,
              child: const Text("Save Changes"),
            ),
          ],
        ),
      ),
    );
  }

  Widget _input(String label, TextEditingController controller, IconData icon,
      {bool readOnly = false, TextInputType keyboard = TextInputType.text}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontWeight: FontWeight.w600,
            fontSize: 14,
          ),
        ),
        const SizedBox(height: 6),
        TextField(
          controller: controller,
          readOnly: readOnly,
          keyboardType: keyboard,
          decoration: InputDecoration(
            prefixIcon: Icon(icon, color: AppTheme.gray),
          ),
        ),
      ],
    );
  }
}

