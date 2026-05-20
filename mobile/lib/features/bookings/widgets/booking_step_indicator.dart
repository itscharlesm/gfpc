import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class BookingStepIndicator extends StatelessWidget {
  final int currentStep;

  const BookingStepIndicator({
    super.key,
    required this.currentStep,
  });

  final List<String> steps = const [
    'Location',
    'Problem',
    'Schedule',
    'Review',
  ];

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(16, 14, 16, 12),
      decoration: const BoxDecoration(
        color: AppTheme.white,
        border: Border(
          bottom: BorderSide(
            color: AppTheme.borderGray,
            width: 1,
          ),
        ),
      ),
      child: Row(
        children: List.generate(steps.length * 2 - 1, (index) {
          // EVEN = STEP
          if (index % 2 == 0) {
            final stepIndex = index ~/ 2;
            final stepNumber = stepIndex + 1;

            final isActive = stepNumber == currentStep;
            final isDone = stepNumber < currentStep;

            return Expanded(
              flex: 2,
              child: _stepItem(
                number: stepNumber,
                label: steps[stepIndex],
                isActive: isActive,
                isDone: isDone,
              ),
            );
          }

          // ODD = LINE
          return Expanded(
            flex: 1,
            child: _stepLine(
              (index ~/ 2 + 1) < currentStep,
            ),
          );
        }),
      ),
    );
  }

  Widget _stepItem({
    required int number,
    required String label,
    required bool isActive,
    required bool isDone,
  }) {
    final Color circleColor = isActive || isDone
        ? AppTheme.primaryRed
        : AppTheme.borderGray;

    final Color textColor = isActive || isDone
        ? AppTheme.primaryRed
        : AppTheme.gray;

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        CircleAvatar(
          radius: 13,
          backgroundColor: circleColor,
          child: isDone
              ? const Icon(
                  Icons.check,
                  size: 15,
                  color: AppTheme.white,
                )
              : Text(
                  number.toString(),
                  style: const TextStyle(
                    color: AppTheme.white,
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                  ),
                ),
        ),
        const SizedBox(height: 5),
        Text(
          label,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: TextStyle(
            color: textColor,
            fontSize: 11,
            fontWeight: isActive ? FontWeight.bold : FontWeight.w600,
          ),
        ),
      ],
    );
  }

  Widget _stepLine(bool isActive) {
    return Container(
      width: 18,
      height: 2,
      margin: const EdgeInsets.only(bottom: 20),
      color: isActive ? AppTheme.primaryRed : AppTheme.borderGray,
    );
  }
}