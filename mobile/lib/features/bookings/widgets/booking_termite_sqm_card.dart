import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class BookingTermiteSqmCard extends StatelessWidget {
  final bool isLoading;
  final List<Map<String, dynamic>> termiteSqmOptions;
  final Map<String, dynamic>? selectedTermiteSqm;
  final void Function(Map<String, dynamic> option) onSelect;

  const BookingTermiteSqmCard({
    super.key,
    required this.isLoading,
    required this.termiteSqmOptions,
    required this.selectedTermiteSqm,
    required this.onSelect,
  });

  String _sqmImage(String sqmDetails) {
    final value = sqmDetails.toLowerCase();

    if (value.contains('1sqm') &&
        value.contains('50sqm')) {
      return 'assets/images/img_1-50sqm.png';
    }

    if (value.contains('51sqm') &&
        value.contains('100sqm')) {
      return 'assets/images/img_51-100sqm.png';
    }

    if (value.contains('101sqm') &&
        value.contains('500sqm')) {
      return 'assets/images/img_101-500sqm.png';
    }

    if (value.contains('501sqm') &&
        value.contains('1000sqm')) {
      return 'assets/images/img_501-1000sqm.png';
    }

    if (value.contains('1001sqm') &&
        value.contains('999999sqm')) {
      return 'assets/images/img_1001-999999sqm.png';
    }

    return 'assets/images/img_defaultcards.png';
  }

  String _sqmRange(String sqmDetails) {
    final numbers = RegExp(r'\d+')
        .allMatches(sqmDetails)
        .map((match) => int.tryParse(match.group(0) ?? ''))
        .whereType<int>()
        .toList();

    if (numbers.isEmpty) {
      return sqmDetails;
    }

    if (numbers.length == 1) {
      return '${numbers.first} sqm';
    }

    final start = numbers[0];
    final end = numbers[1];

    if (end >= 999999) {
      return '$start+ sqm';
    }

    return '$start - $end sqm';
  }

  String _sqmDescription(String sqmDetails) {
    final value = sqmDetails.toLowerCase();

    if (value.contains('1sqm') &&
        value.contains('50sqm')) {
      return 'Bedrooms and small rooms';
    }

    if (value.contains('51sqm') &&
        value.contains('100sqm')) {
      return 'Apartments and small homes';
    }

    if (value.contains('101sqm') &&
        value.contains('500sqm')) {
      return 'Large homes and multi-room areas';
    }

    if (value.contains('501sqm') &&
        value.contains('1000sqm')) {
      return 'Commercial and industrial spaces';
    }

    if (value.contains('1001sqm')) {
      return 'Large-scale properties';
    }

    return 'Property treatment area';
  }

  String _sqmPricing(String sqmDetails, dynamic cost) {
    final value = sqmDetails.toLowerCase();

    final formattedCost =
        cost.toString().replaceAllMapped(
              RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
              (Match m) => '${m[1]},',
            );

    if (value.contains('1sqm') &&
        value.contains('50sqm')) {
      return 'From ₱$formattedCost';
    }

    return '₱$formattedCost/sqm';
  }

  void _openImagePreview(BuildContext context, String imagePath) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => Scaffold(
          backgroundColor: Colors.black,
          body: SafeArea(
            child: Stack(
              children: [
                Center(
                  child: InteractiveViewer(
                    minScale: 1,
                    maxScale: 4,
                    child: Image.asset(
                      imagePath,
                      fit: BoxFit.contain,
                    ),
                  ),
                ),
                Positioned(
                  top: 12,
                  right: 12,
                  child: IconButton(
                    icon: const Icon(
                      Icons.close,
                      color: Colors.white,
                      size: 28,
                    ),
                    onPressed: () => Navigator.pop(context),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Property Size',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: AppTheme.black,
          ),
        ),
        const SizedBox(height: 5),
        const Text(
          'Select the approximate treatment area size.',
          style: TextStyle(
            color: AppTheme.gray,
            fontSize: 12,
          ),
        ),
        const SizedBox(height: 14),

        if (isLoading)
          const Center(
            child: Padding(
              padding: EdgeInsets.all(24),
              child: CircularProgressIndicator(
                color: AppTheme.primaryRed,
              ),
            ),
          )
        else
          SizedBox(
            height: 220,
            child: ListView.separated(
              padding: const EdgeInsets.only(right: 20),
              scrollDirection: Axis.horizontal,
              itemCount: termiteSqmOptions.length,
              separatorBuilder: (_, __) => const SizedBox(width: 10),
              itemBuilder: (context, index) {
                final option = termiteSqmOptions[index];
                final sqmDetails = option['sqm_details']?.toString() ?? '';
                final isSelected = selectedTermiteSqm?['id'] == option['id'];
                final imagePath = _sqmImage(sqmDetails);

                return GestureDetector(
                  onTap: () => onSelect(option),
                  child: Container(
                    width: 130,
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: isSelected ? AppTheme.primaryRed : AppTheme.white,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: isSelected
                            ? AppTheme.primaryRed
                            : AppTheme.borderGray,
                        width: isSelected ? 1.3 : 1,
                      ),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          height: 110,
                          width: double.infinity,
                          decoration: BoxDecoration(
                            color: AppTheme.white,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Center(
                            child: SizedBox(
                              width: 82,
                              height: 82,
                              child: GestureDetector(
                                onLongPress: () {
                                  _openImagePreview(
                                    context,
                                    imagePath,
                                  );
                                },
                                child: Image.asset(
                                  imagePath,
                                  fit: BoxFit.contain,
                                  errorBuilder: (context, error, stackTrace) {
                                    return const Icon(
                                      Icons.image_not_supported_outlined,
                                      color: AppTheme.gray,
                                      size: 34,
                                    );
                                  },
                                ),
                              ),
                            ),
                          ),
                        ),

                        const SizedBox(height: 10),

                        SizedBox(
                          width: double.infinity,
                          child: FittedBox(
                            fit: BoxFit.scaleDown,
                            alignment: Alignment.centerLeft,
                            child: Text(
                              _sqmRange(sqmDetails),
                              maxLines: 1,
                              style: TextStyle(
                                color: isSelected
                                    ? AppTheme.white
                                    : AppTheme.black,
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                height: 1.15,
                              ),
                            ),
                          ),
                        ),

                        const SizedBox(height: 6),

                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                _sqmDescription(sqmDetails),
                                maxLines: 2,
                                overflow: TextOverflow.ellipsis,
                                style: TextStyle(
                                  color: isSelected
                                      ? Colors.white70
                                      : AppTheme.gray,
                                  fontSize: 10.5,
                                  height: 1.2,
                                ),
                              ),

                              const Spacer(),

                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 8,
                                  vertical: 5,
                                ),
                                decoration: BoxDecoration(
                                  color: isSelected
                                      ? Colors.white.withOpacity(0.25)
                                      : AppTheme.primaryRed.withOpacity(0.08),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Text(
                                  _sqmPricing(
                                    sqmDetails,
                                    option['cost'],
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: TextStyle(
                                    color: isSelected
                                        ? AppTheme.white
                                        : AppTheme.primaryRed,
                                    fontSize: 10.5,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),

        const SizedBox(height: 12),

        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.black.withOpacity(0.025),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: Colors.black.withOpacity(0.05),
            ),
          ),
          child: const Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Icon(
                Icons.info_outline,
                color: AppTheme.gray,
                size: 18,
              ),

              SizedBox(width: 9),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Quotation Notice',
                      style: TextStyle(
                        color: AppTheme.black,
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                      ),
                    ),

                    SizedBox(height: 3),

                    Text(
                      'Final quotation will be confirmed after inspection and assessment.',
                      style: TextStyle(
                        color: AppTheme.gray,
                        fontSize: 11.5,
                        height: 1.3,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}