// import 'package:flutter/material.dart';
// import 'login_page.dart';
// import 'signup_page.dart';

// class WelcomePage extends StatelessWidget {
//   const WelcomePage({super.key});

//   @override
//   Widget build(BuildContext context) {
//     return Scaffold(
//       body: Stack(
//         children: [
//           // Background Image
//           Positioned.fill(
//             child: Image.asset(
//               'assets/images/img_wlcmpagebg.png',
//               fit: BoxFit.cover,
//             ),
//           ),

//           // Dark overlay (for readability)
//           Positioned.fill(
//             child: Container(
//               color: Colors.black.withOpacity(0.15),
//             ),
//           ),

//           // Content
//           SafeArea(
//             child: Padding(
//               padding: const EdgeInsets.symmetric(horizontal: 28),
//               child: Column(
//                 crossAxisAlignment: CrossAxisAlignment.start,
//                 children: [
//                   const SizedBox(height: 16),

//                   const Icon(
//                     Icons.arrow_back_ios_new,
//                     color: Colors.white,
//                     size: 20,
//                   ),

//                   const SizedBox(height: 20),

//                   // Logo
//                   Center(
//                     child: Image.asset(
//                       'assets/images/img_goforwardlogo.png',
//                       width: 270,
//                     ),
//                   ),

//                   const Spacer(),

//                   // Move content upward (adjust this value)
//                   Transform.translate(
//                     offset: const Offset(0, -60),
//                     child: Column(
//                       crossAxisAlignment: CrossAxisAlignment.start,
//                       children: [
//                         Text(
//                           'Pest Control Service',
//                           style: Theme.of(context).textTheme.titleLarge?.copyWith(
//                             color: Colors.black,
//                           ),
//                         ),

//                         const SizedBox(height: 10),

//                         Text(
//                           'Fast, reliable pest control at your location.',
//                           style: Theme.of(context).textTheme.bodyMedium?.copyWith(
//                             color: const Color(0xFF444444),
//                             height: 1.4,
//                           ),
//                         ),

//                         const SizedBox(height: 30),

//                         SizedBox(
//                           width: double.infinity,
//                           height: 52,
//                           child: ElevatedButton(
//                             onPressed: () {
//                               Navigator.push(
//                                 context,
//                                 MaterialPageRoute(
//                                   builder: (_) => const LoginPage(),
//                                 ),
//                               );
//                             },
//                             child: const Text('Log in'),
//                           ),
//                         ),

//                         const SizedBox(height: 14),

//                         SizedBox(
//                           width: double.infinity,
//                           height: 52,
//                           child: OutlinedButton(
//                              onPressed: () {
//                               Navigator.push(
//                                 context,
//                                 MaterialPageRoute(
//                                   builder: (_) => const SignUpPage(),
//                                 ),
//                               );
//                             },
//                             child: const Text('Sign up'),
//                           ),
//                         ),
//                       ],
//                     ),
//                   ),

//                   const SizedBox(height: 40),
//                 ],
//               ),
//             ),
//           ),
//         ],
//       ),
//     );
//   }
// }

import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart' as geo;
import 'login_page.dart';
import 'signup_page.dart';

class WelcomePage extends StatefulWidget {
  const WelcomePage({super.key});

  @override
  State<WelcomePage> createState() => _WelcomePageState();
}

class _WelcomePageState extends State<WelcomePage> {
  Future<bool> _checkLocationAccess() async {
    final serviceEnabled = await geo.Geolocator.isLocationServiceEnabled();

    if (!serviceEnabled) {
      _showLocationServiceDialog();
      return false;
    }

    var permission = await geo.Geolocator.checkPermission();

    if (permission == geo.LocationPermission.denied) {
      permission = await geo.Geolocator.requestPermission();
    }

    if (permission == geo.LocationPermission.denied ||
        permission == geo.LocationPermission.deniedForever) {
      _showLocationPermissionDialog();
      return false;
    }

    return true;
  }

  void _showLocationServiceDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => AlertDialog(
        title: const Text('Turn On Location'),
        content: const Text(
          'Please turn on your phone location to continue using the app.',
        ),
        actions: [
          TextButton(
            onPressed: () async {
              Navigator.pop(context);
              await geo.Geolocator.openLocationSettings();
            },
            child: const Text('Open Settings'),
          ),
        ],
      ),
    );
  }

  void _showLocationPermissionDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => AlertDialog(
        title: const Text('Location Permission Required'),
        content: const Text(
          'This app needs location access to detect your address and assign the nearest branch.',
        ),
        actions: [
          TextButton(
            onPressed: () async {
              Navigator.pop(context);
              await geo.Geolocator.openAppSettings();
            },
            child: const Text('Open App Settings'),
          ),
        ],
      ),
    );
  }

  Future<void> _goToLogin() async {
    final hasAccess = await _checkLocationAccess();

    if (!hasAccess || !mounted) return;

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => const LoginPage(),
      ),
    );
  }

  Future<void> _goToSignUp() async {
    final hasAccess = await _checkLocationAccess();

    if (!hasAccess || !mounted) return;

    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => const SignUpPage(),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          Positioned.fill(
            child: Image.asset(
              'assets/images/img_wlcmpagebg.png',
              fit: BoxFit.cover,
            ),
          ),

          Positioned.fill(
            child: Container(
              color: Colors.black.withOpacity(0.15),
            ),
          ),

          SafeArea(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 28),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 16),

                  const Icon(
                    Icons.arrow_back_ios_new,
                    color: Colors.white,
                    size: 20,
                  ),

                  const SizedBox(height: 20),

                  Center(
                    child: Image.asset(
                      'assets/images/img_goforwardlogo.png',
                      width: 270,
                    ),
                  ),

                  const Spacer(),

                  Transform.translate(
                    offset: const Offset(0, -60),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Pest Control Service',
                          style:
                              Theme.of(context).textTheme.titleLarge?.copyWith(
                                    color: Colors.black,
                                  ),
                        ),

                        const SizedBox(height: 10),

                        Text(
                          'Fast, reliable pest control at your location.',
                          style:
                              Theme.of(context).textTheme.bodyMedium?.copyWith(
                                    color: const Color(0xFF444444),
                                    height: 1.4,
                                  ),
                        ),

                        const SizedBox(height: 30),

                        SizedBox(
                          width: double.infinity,
                          height: 52,
                          child: ElevatedButton(
                            onPressed: _goToLogin,
                            child: const Text('Log in'),
                          ),
                        ),

                        const SizedBox(height: 14),

                        SizedBox(
                          width: double.infinity,
                          height: 52,
                          child: OutlinedButton(
                            onPressed: _goToSignUp,
                            child: const Text('Sign up'),
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 40),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}