import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:mobile_app/config/api_config.dart';
import 'dart:convert';
import 'login_page.dart';
import 'package:mobile_app/app/theme.dart';

class SignUpPage extends StatefulWidget {
  const SignUpPage({super.key});

  @override
  State<SignUpPage> createState() => _SignUpPageState();
}

class _SignUpPageState extends State<SignUpPage> {
  bool isPasswordVisible = false;
  bool isConfirmPasswordVisible = false;
  bool isLoading = false;
  String? passwordError;
  String? confirmPasswordError;
  String? mobileError;
  String? emailError;

  final TextEditingController firstNameController = TextEditingController();
  final TextEditingController middleNameController = TextEditingController();
  final TextEditingController lastNameController = TextEditingController();
  final TextEditingController mobileController = TextEditingController();
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final TextEditingController confirmPasswordController = TextEditingController();

  @override
  void initState() {
    super.initState();
    passwordController.addListener(_validatePasswordLength);
  }

  @override
  void dispose() {
    passwordController.dispose();
    confirmPasswordController.dispose();
    firstNameController.dispose();
    middleNameController.dispose();
    lastNameController.dispose();
    mobileController.dispose();
    emailController.dispose();
    super.dispose();
  }

  void _validatePasswordLength() {
    final password = passwordController.text;
    if (password.length > 64) {
      setState(() {
        passwordError = 'Password cannot exceed 64 characters';
      });
    } else if (passwordError == 'Password cannot exceed 64 characters') {
      setState(() {
        passwordError = null;
      });
    }
  }

  String cleanMobileNumber(String value) {
    return value.trim().replaceAll(RegExp(r'[\s-]'), '');
  }

  bool isValidPhMobileNumber(String value) {
    final mobile = cleanMobileNumber(value);

    final bool isLocalFormat = RegExp(r'^09\d{9}$').hasMatch(mobile);
    final bool isInternationalFormat = RegExp(r'^\+639\d{9}$').hasMatch(mobile);

    return isLocalFormat || isInternationalFormat;
  }

  String normalizePhMobileNumber(String value) {
    final mobile = cleanMobileNumber(value);

    if (mobile.startsWith('09')) {
      return mobile.substring(1); // remove 0 → 9XXXXXXXXX
    }

    if (mobile.startsWith('+63')) {
      return mobile.substring(3); // remove +63 → 9XXXXXXXXX
    }

    return mobile;
  }

  Future<void> _register() async {
    final String firstName = firstNameController.text.trim();
    final String middleName = middleNameController.text.trim();
    final String lastName = lastNameController.text.trim();
    final String mobile = cleanMobileNumber(mobileController.text);
    final String email = emailController.text.trim();
    final String password = passwordController.text;
    final String confirmPassword = confirmPasswordController.text;

    setState(() {
      passwordError = null;
      confirmPasswordError = null;
      mobileError = null;
      emailError = null;
    });

    if (firstName.isEmpty || lastName.isEmpty || mobile.isEmpty || email.isEmpty || password.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fill all required fields')),
      );
      return;
    }

    if (!email.contains('@') || !email.contains('.') || email.indexOf('.') < email.indexOf('@')) {
      setState(() {
        emailError = 'Enter a valid email address';
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Enter a valid email address')),
      );
      return;
    }

    if (!isValidPhMobileNumber(mobile)) {
      setState(() {
        mobileError = 'Enter a valid mobile number.';
      });
      return;
    }

    final String normalizedMobile = normalizePhMobileNumber(mobile);
    
    if (password.length < 12) {
      setState(() {
        passwordError = 'Password must be at least 12 characters';
      });
      return;
    }

    if (!RegExp(r'^(?=.*[a-zA-Z])(?=.*\d).{12,64}$').hasMatch(password)) {
      setState(() {
        passwordError = 'Password must be 12 characters and include letters and numbers.';
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please fix the password format')),
      );
      return;
    }

    if (password != confirmPassword) {
      setState(() {
        confirmPasswordError = 'Passwords do not match';
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Passwords do not match')),
      );
      return;
    }

    try {
      setState(() {
        isLoading = true;
      });

      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/api/mobile/register'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'first_name': firstName,
          'middle_name': middleName,
          'last_name': lastName,
          'mobile': normalizedMobile,
          'email': email,
          'password': password,
          'password_confirmation': confirmPassword,
        }),
      );

      if (response.statusCode == 201) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Registration successful')),
        );
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => const LoginPage()),
        );
      } else {
        final data = jsonDecode(response.body);
        if (data['errors'] != null && data['errors'] is Map) {
          final errors = data['errors'] as Map<String, dynamic>;
          setState(() {
            mobileError = errors['mobile']?.join('\n');
            emailError = errors['email']?.join('\n');
            passwordError = errors['password']?.join('\n');
          });
          // Show general error in SnackBar if no field-specific errors
          if (mobileError == null && emailError == null && passwordError == null) {
            String message = data['message'] ?? 'Registration failed';
            final errorMessages = errors.values.expand((e) => e).toList();
            message += '\n' + errorMessages.join('\n');
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(message)),
            );
          }
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(data['message'] ?? 'Registration failed')),
          );
        }
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e')),
      );
    } finally {
      setState(() {
        isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => FocusScope.of(context).unfocus(),
      child: Scaffold(
        backgroundColor: Colors.white,
        resizeToAvoidBottomInset: true,
        body: SafeArea(
          child: SingleChildScrollView(
            padding: EdgeInsets.fromLTRB(
              24,
              0,
              24,
              MediaQuery.of(context).viewInsets.bottom + 24,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 10),

                IconButton(
                  onPressed: () {
                    Navigator.pop(context);
                  },
                  icon: const Icon(Icons.arrow_back_ios_new),
                ),

                const SizedBox(height: 20),

                Text(
                  "Create Account",
                  style: Theme.of(context).textTheme.titleLarge,
                ),

                const SizedBox(height: 6),

                Text(
                  "Sign up to get started",
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: const Color(0xFF6B6B6B),
                  ),
                ),

                const SizedBox(height: 30),

                TextField(
                  controller: firstNameController,
                  decoration: const InputDecoration(
                    labelText: "First Name",
                  ),
                ),

                const SizedBox(height: 16),

                TextField(
                  controller: middleNameController,
                  decoration: const InputDecoration(
                    labelText: "Middle Name (Optional)",
                  ),
                ),

                const SizedBox(height: 16),

                TextField(
                  controller: lastNameController,
                  decoration: const InputDecoration(
                    labelText: "Last Name",
                  ),
                ),

                const SizedBox(height: 16),

                TextField(
                  controller: mobileController,
                  keyboardType: TextInputType.phone,
                  decoration: InputDecoration(
                    labelText: "Mobile Number",
                    errorText: mobileError,
                    errorMaxLines: 2,
                    errorStyle: const TextStyle(fontSize: 12),
                  ),
                ),

                const SizedBox(height: 16),

                TextField(
                  controller: emailController,
                  keyboardType: TextInputType.emailAddress,
                  decoration: InputDecoration(
                    labelText: "Email Address",
                    errorText: emailError,
                    errorMaxLines: 2,
                    errorStyle: const TextStyle(fontSize: 12),
                  ),
                ),

                const SizedBox(height: 16),

                TextField(
                  controller: passwordController,
                  obscureText: !isPasswordVisible,
                  decoration: InputDecoration(
                    labelText: "Password",
                    errorText: passwordError,
                    errorMaxLines: 2,
                    errorStyle: const TextStyle(fontSize: 12),
                    suffixIcon: IconButton(
                      icon: Icon(
                        isPasswordVisible
                            ? Icons.visibility
                            : Icons.visibility_off,
                      ),
                      onPressed: () {
                        setState(() {
                          isPasswordVisible = !isPasswordVisible;
                        });
                      },
                    ),
                  ),
                ),

                const SizedBox(height: 16),

                TextField(
                  controller: confirmPasswordController,
                  obscureText: !isConfirmPasswordVisible,
                  decoration: InputDecoration(
                    labelText: "Confirm Password",
                    errorText: confirmPasswordError,
                    errorMaxLines: 2,
                    errorStyle: const TextStyle(fontSize: 12),
                    suffixIcon: IconButton(
                      icon: Icon(
                        isConfirmPasswordVisible
                            ? Icons.visibility
                            : Icons.visibility_off,
                      ),
                      onPressed: () {
                        setState(() {
                          isConfirmPasswordVisible =
                              !isConfirmPasswordVisible;
                        });
                      },
                    ),
                  ),
                ),

                const SizedBox(height: 24),

                LoadingButton(
                  isLoading: isLoading,
                  onPressed: _register,
                  child: const Text("Sign up"),
                ),

                const SizedBox(height: 20),

                Center(
                  child: GestureDetector(
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const LoginPage(),
                        ),
                      );
                    },
                    child: RichText(
                      text: TextSpan(
                        text: "Already have an account? ",
                        style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                          color: Colors.black54,
                          fontSize: 14,
                        ),
                        children: [
                          TextSpan(
                            text: "Log in",
                            style: Theme.of(context)
                                .textTheme
                                .bodyMedium
                                ?.copyWith(
                                  color: Theme.of(context).colorScheme.primary,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 14,
                                ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }
}