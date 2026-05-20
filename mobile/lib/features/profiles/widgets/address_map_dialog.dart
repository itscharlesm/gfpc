import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart' as geo;
import 'package:mapbox_maps_flutter/mapbox_maps_flutter.dart';
import 'package:mobile_app/app/theme.dart';
import 'dart:async';
import 'dart:ui' as ui;

class AddressMapDialog extends StatefulWidget {
  const AddressMapDialog({super.key});

  @override
  State<AddressMapDialog> createState() => _AddressMapDialogState();
}

class _AddressMapDialogState extends State<AddressMapDialog> {
  geo.Position? currentPosition;
  bool isLoadingLocation = true;
  Timer? _cameraDebounce;
  bool isMovingMap = false;

  MapboxMap? mapboxMap;
  final GlobalKey mapKey = GlobalKey();

  double? selectedLatitude;
  double? selectedLongitude;

  @override
  void initState() {
    super.initState();
    _loadCurrentLocation();
  }

  Future<void> _loadCurrentLocation() async {
    try {
      final position = await geo.Geolocator.getCurrentPosition(
        desiredAccuracy: geo.LocationAccuracy.medium,
        timeLimit: const Duration(seconds: 8),
      );

      if (!mounted) return;

      setState(() {
        currentPosition = position;
        isLoadingLocation = false;
      });
    } catch (e) {
      if (!mounted) return;

      setState(() {
        isLoadingLocation = false;
      });
    }
  }

  Future<void> _updatePinCoordinates() async {
    if (mapboxMap == null) return;

    final renderBox = mapKey.currentContext?.findRenderObject() as RenderBox?;

    if (renderBox == null) return;

    final mapSize = renderBox.size;

    final coordinate = await mapboxMap!.coordinateForPixel(
      ScreenCoordinate(
        x: mapSize.width / 2,
        y: (mapSize.height / 2) + 31,
      ),
    );

    if (!mounted) return;

    setState(() {
      selectedLongitude = coordinate.coordinates.lng.toDouble();
      selectedLatitude = coordinate.coordinates.lat.toDouble();
    });
  }

  @override
  void dispose() {
    _cameraDebounce?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final longitude = currentPosition?.longitude ?? 125.6131;
    final latitude = currentPosition?.latitude ?? 7.0731;

    return Dialog(
      backgroundColor: AppTheme.white,
      insetPadding: const EdgeInsets.symmetric(
        horizontal: 10,
        vertical: 10,
      ),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(18),
      ),
      child: SizedBox(
        width: double.infinity,
        height: MediaQuery.of(context).size.height * 0.82,
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 14, 10, 10),
              child: Row(
                children: [
                  const Expanded(
                    child: Text(
                      'Pin Your Address',
                      style: TextStyle(
                        color: AppTheme.black,
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  IconButton(
                    onPressed: () async {
                      await _updatePinCoordinates();

                      if (!mounted) return;

                      Navigator.pop(context, {
                        'latitude': selectedLatitude,
                        'longitude': selectedLongitude,
                      });
                    },
                    icon: const Icon(Icons.close_rounded),
                  ),
                ],
              ),
            ),

            const Divider(height: 1),

            Expanded(
              child: ClipRRect(
                key: mapKey,
                borderRadius: BorderRadius.zero,
                child: isLoadingLocation
                    ? const Center(
                        child: CircularProgressIndicator(),
                      )
                    : Stack(
                        children: [
                          MapWidget(
                            cameraOptions: CameraOptions(
                              center: Point(
                                coordinates: Position(
                                  longitude,
                                  latitude,
                                ),
                              ),
                              zoom: 15,
                            ),
                            
                            onMapCreated: (controller) async {
                              mapboxMap = controller;

                              controller.scaleBar.updateSettings(
                                ScaleBarSettings(
                                  enabled: false,
                                ),
                              );

                              await controller.location.updateSettings(
                                LocationComponentSettings(
                                  enabled: true,
                                  pulsingEnabled: true,
                                ),
                              );

                              await Future.delayed(
                                const Duration(milliseconds: 500),
                              );

                              await _updatePinCoordinates();
                            },

                            onCameraChangeListener: (eventData) {
                              if (!isMovingMap) {
                                setState(() {
                                  isMovingMap = true;
                                });
                              }

                              _cameraDebounce?.cancel();

                              _cameraDebounce = Timer(
                                const Duration(milliseconds: 500),
                                () async {
                                  if (!mounted) return;

                                  setState(() {
                                    isMovingMap = false;
                                  });

                                  await _updatePinCoordinates();
                                },
                              );
                            },
                          ),

                          IgnorePointer(
                            child: Center(
                              child: SizedBox(
                                width: 120,
                                height: 110,
                                child: Stack(
                                  alignment: Alignment.center,
                                  children: [
                                    AnimatedPositioned(
                                      duration: const Duration(milliseconds: 220),
                                      curve: Curves.easeOut,
                                      bottom: isMovingMap ? 1 : 6,
                                      left: isMovingMap ? 55 : 46,
                                      child: AnimatedContainer(
                                        duration: const Duration(milliseconds: 220),
                                        curve: Curves.easeOut,
                                        width: isMovingMap ? 10 : 30,
                                        height: isMovingMap ? 5 : 5,
                                        child: ClipPath(
                                          clipper: PinShadowClipper(),
                                          child: Container(
                                            color: Colors.black.withOpacity(
                                              isMovingMap ? 0.18 : 0.24,
                                            ),
                                          ),
                                        ),
                                      ),
                                    ),

                                    AnimatedPositioned(
                                      duration: const Duration(milliseconds: 220),
                                      curve: Curves.easeOut,
                                      top: isMovingMap ? 25 : 38,
                                      child: AnimatedScale(
                                        duration: const Duration(milliseconds: 220),
                                        curve: Curves.easeOut,
                                        scale: isMovingMap ? 1.03 : 1.0,
                                        child: Image.asset(
                                          'assets/images/img_map_pin.png',
                                          width: 70,
                                          height: 70,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),

                          Positioned(
                            right: 12,
                            top: 12,
                            child: Column(
                              children: [
                                _mapControlButton(
                                  icon: Icons.my_location_rounded,
                                  onTap: () async {
                                    if (mapboxMap == null || currentPosition == null) return;

                                    await mapboxMap!.flyTo(
                                      CameraOptions(
                                        center: Point(
                                          coordinates: Position(
                                            currentPosition!.longitude,
                                            currentPosition!.latitude,
                                          ),
                                        ),
                                        zoom: 15,
                                      ),
                                      MapAnimationOptions(duration: 700),
                                    );

                                    await Future.delayed(const Duration(milliseconds: 750));
                                    await _updatePinCoordinates();
                                  },
                                ),

                                const SizedBox(height: 8),

                                _mapControlButton(
                                  icon: Icons.add_rounded,
                                  onTap: () async {
                                    if (mapboxMap == null) return;

                                    final camera = await mapboxMap!.getCameraState();

                                    await mapboxMap!.easeTo(
                                      CameraOptions(zoom: camera.zoom + 1),
                                      MapAnimationOptions(duration: 250),
                                    );
                                  },
                                ),

                                const SizedBox(height: 8),

                                _mapControlButton(
                                  icon: Icons.remove_rounded,
                                  onTap: () async {
                                    if (mapboxMap == null) return;

                                    final camera = await mapboxMap!.getCameraState();

                                    await mapboxMap!.easeTo(
                                      CameraOptions(zoom: camera.zoom - 1),
                                      MapAnimationOptions(duration: 250),
                                    );
                                  },
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
              ),
            ),

            Container(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 16),
              decoration: const BoxDecoration(
                color: AppTheme.white,
                borderRadius: BorderRadius.vertical(
                  bottom: Radius.circular(18),
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [

                  const SizedBox(height: 12),

                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: ElevatedButton(
                      onPressed: mapboxMap == null
                      ? null
                      : () async {
                          await _updatePinCoordinates();

                          if (!mounted) return;

                          Navigator.pop(context, {
                            'latitude': selectedLatitude,
                            'longitude': selectedLongitude,
                          });
                        },
                      child: const Text('Use This Location'),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _mapControlButton({
    required IconData icon,
    required VoidCallback onTap,
  }) {
    return Material(
      color: AppTheme.white,
      borderRadius: BorderRadius.circular(12),
      elevation: 3,
      shadowColor: Colors.black.withOpacity(0.12),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: onTap,
        child: SizedBox(
          width: 42,
          height: 42,
          child: Icon(
            icon,
            color: AppTheme.black,
            size: 21,
          ),
        ),
      ),
    );
  }
}

class PinShadowClipper extends CustomClipper<ui.Path> {
  @override
  ui.Path getClip(ui.Size size) {
    final path = ui.Path();

    path.moveTo(size.width / 2, 0);

    path.quadraticBezierTo(
      size.width,
      0,
      size.width,
      size.height / 2,
    );

    path.quadraticBezierTo(
      size.width,
      size.height,
      size.width / 2,
      size.height,
    );

    path.quadraticBezierTo(
      0,
      size.height,
      0,
      size.height / 2,
    );

    path.quadraticBezierTo(
      0,
      0,
      size.width / 2,
      0,
    );

    path.close();

    return path;
  }

  @override
  bool shouldReclip(covariant CustomClipper<ui.Path> oldClipper) {
    return false;
  }
}