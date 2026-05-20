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

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Termite Treatment Size',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: AppTheme.black,
          ),
        ),
        const SizedBox(height: 5),
        const Text(
          'Select the estimated area size for termite treatment.',
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
          Column(
            children: termiteSqmOptions.map((option) {
              final isSelected =
                  selectedTermiteSqm?['id'] == option['id'];

              return GestureDetector(
                onTap: () => onSelect(option),
                child: Container(
                  margin: const EdgeInsets.only(bottom: 10),
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: isSelected
                        ? AppTheme.primaryRed
                        : AppTheme.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                      color: isSelected
                          ? AppTheme.primaryRed
                          : AppTheme.borderGray,
                      width: isSelected ? 1.3 : 1,
                    ),
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: Text(
                          option['sqm_details'] ?? '',
                          style: TextStyle(
                            color: isSelected
                                ? AppTheme.white
                                : AppTheme.black,
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      Text(
                        '₱${option['cost']}',
                        style: TextStyle(
                          color: isSelected
                              ? AppTheme.white
                              : AppTheme.primaryRed,
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            }).toList(),
          ),
      ],
    );
  }
}