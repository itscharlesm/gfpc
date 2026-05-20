import 'package:flutter/material.dart';
import 'signup_page.dart';
import 'forgot_password_page.dart';
import '../../../shared/widgets/snackbars/login_snackbar.dart';
import 'package:mobile_app/config/api_config.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:mobile_app/app/theme.dart';
import '../../home/pages/client_home_page.dart';
import '../../home/pages/technician_home_page.dart';
import 'welcome_page.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  
    final TextEditingController emailController = TextEditingController();
    final TextEditingController passwordController = TextEditingController();

    bool isLoading = false;
    bool isPasswordVisible = false;

    String? emailError;
    String? passwordError;

   Future<void> login() async {
      final String email = emailController.text.trim();
      final String password = passwordController.text;

      setState(() {
        emailError = null;
        passwordError = null;
      });

      if (email.isEmpty) {
        setState(() => emailError = 'Email is required.');
        return;
      }

      if (!RegExp(r'^[^\s@]+@[^\s@]+\.[^\s@]+$').hasMatch(email)) {
        setState(() => emailError = 'Enter a valid email address.');
        return;
      }

      if (password.isEmpty) {
        setState(() => passwordError = 'Password is required.');
        return;
      }

      try {
        setState(() => isLoading = true);

        final response = await http.post(
          Uri.parse('${ApiConfig.baseUrl}/api/mobile/login'),
          headers: {'Content-Type': 'application/json'},
          body: jsonEncode({
            'email': email,
            'password': password,
          }),
        );

        final data = jsonDecode(response.body);

        if (!mounted) return;

        if (response.statusCode == 200 && data['success'] == true) {
          final user = data['user'];

          if (user != null) {
            final int? userType = int.tryParse(user['utyp_id'].toString());
            final String userEmail = user['usr_email'] ?? '';

            if (userType == 3) {
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(
                  builder: (_) => ClientHomePage(email: userEmail),
                ),
              );
            } else if (userType == 2) {
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(
                  builder: (_) => TechnicianHomePage(email: userEmail),
                ),
              );
            } else {
              showLoginSnackbar(
                context: context,
                message: 'User role not recognized.',
              );
            }
          } else {
            showLoginSnackbar(
              context: context,
              message: 'Login successful but user data is missing.',
            );
          }
        } else {
          showLoginSnackbar(
            context: context,
            message: data['message'] ?? 'Email or password is incorrect.',
          );
        }
      } catch (e) {
        if (!mounted) return;
        showLoginSnackbar(
          context: context,
          message: 'Unable to connect to the server.',
        );
      } finally {
        if (mounted) {
          setState(() => isLoading = false);
        }
      }
    }

    @override
    void dispose() {
      emailController.dispose();
      passwordController.dispose();
      super.dispose();
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

                // Back button
                IconButton(
                  onPressed: () {
                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(
                        builder: (_) => const WelcomePage(),
                      ),
                    );
                  },
                  icon: const Icon(Icons.arrow_back_ios_new),
                ),

                const SizedBox(height: 20),

                // Title
                const Text(
                  "Welcome Back",
                  style: TextStyle(
                    fontSize: 26,
                    fontWeight: FontWeight.w800,
                  ),
                ),

                const SizedBox(height: 6),

                const Text(
                  "Login to your account",
                  style: TextStyle(
                    fontSize: 15,
                    color: Color(0xFF6B6B6B),
                  ),
                ),

                const SizedBox(height: 30),

                // Email
                TextField(
                  controller: emailController,
                  keyboardType: TextInputType.emailAddress,
                  decoration: InputDecoration(
                    labelText: 'Email Address',
                    errorText: emailError,
                  ),
                ),

                const SizedBox(height: 16),

                // Password
                TextField(
                  controller: passwordController,
                  obscureText: !isPasswordVisible,
                  decoration: InputDecoration(
                    labelText: 'Password',
                    errorText: passwordError,
                    suffixIcon: IconButton(
                      icon: Icon(
                        isPasswordVisible ? Icons.visibility : Icons.visibility_off,
                      ),
                      onPressed: () {
                        setState(() {
                          isPasswordVisible = !isPasswordVisible;
                        });
                      },
                    ),
                  ),
                ),

                const SizedBox(height: 10),

                // Forgot password
                Align(
                  alignment: Alignment.centerRight,
                  child: TextButton(
                    onPressed: () {
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                        builder: (_) => const ForgotPasswordPage(),
                        ),
                    );
                    },
                    child: const Text("Forgot password?"),
                  ),
                ),

                const SizedBox(height: 20),

                // Login button
                LoadingButton(
                  isLoading: isLoading,
                  onPressed: isLoading ? null : login,
                  child: const Text("Log in"),
                ),

                const SizedBox(height: 20),

                // Sign up redirect
                Center(
                  child: GestureDetector(
                    onTap: () {
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                        builder: (_) => const SignUpPage(),
                        ),
                    );
                    },
                    child: RichText(
                    text: TextSpan(
                        text: "Don't have an account? ",
                        style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                        color: Colors.black54,
                        fontSize: 14,
                        ),
                        children: [
                        TextSpan(
                            text: "Sign up",
                            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
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