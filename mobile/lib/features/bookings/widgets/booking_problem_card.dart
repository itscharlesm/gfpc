import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class BookingProblemCard extends StatelessWidget {
  final bool isLoadingPackages;
  final List<Map<String, dynamic>> servicePackages;
  final List<Map<String, dynamic>> selectedServicePackages;
  final void Function(Map<String, dynamic> service) onToggleService;
  final VoidCallback onClearAll;

  const BookingProblemCard({
    super.key,
    required this.isLoadingPackages,
    required this.servicePackages,
    required this.selectedServicePackages,
    required this.onToggleService,
    required this.onClearAll,
  });

  String _serviceImage(String name) {
    switch (name.toUpperCase()) {
      case 'ANTS':
        return 'assets/images/img_ant.png';
      case 'BED BUGS':
        return 'assets/images/img_bedbug.png';
      case 'COCKROACHES':
        return 'assets/images/img_cockroach.png';
      case 'FLIES':
        return 'assets/images/img_fly.png';
      case 'MOSQUITOES':
        return 'assets/images/img_mosquito.png';
      case 'RATS/MICE':
        return 'assets/images/img_rat.png';
      case 'SPIDERS':
        return 'assets/images/img_spider.png';
      case 'TERMITES':
        return 'assets/images/img_termites.png';
      case 'OTHERS':
        return 'assets/images/img_others.png';
      default:
        return 'assets/images/img_defaultcards.png';
    }
  }

  String _serviceShortText(String name) {
    switch (name.toUpperCase()) {
      case 'ANTS':
        return 'Small ants found indoors or outdoors.';
      case 'BED BUGS':
        return 'Bugs usually found in beds and furniture.';
      case 'COCKROACHES':
        return 'Common pests in kitchens and bathrooms.';
      case 'FLIES':
        return 'Flies around food, waste, or damp areas.';
      case 'MOSQUITOES':
        return 'Biting insects common in open areas.';
      case 'RATS/MICE':
        return 'Rodents found in rooms or storage areas.';
      case 'SPIDERS':
        return 'Webs or spiders seen around the property.';
      case 'TERMITES':
        return 'Wood-damaging pests in walls or furniture.';
      case 'OTHERS':
        return 'Other pest issues not listed here.';
      default:
        return 'Pest control service option.';
    }
  }

  String _formatServiceName(String text) {
    return text.toLowerCase().split(' ').map((word) {
      if (word.isEmpty) return word;
      return word[0].toUpperCase() + word.substring(1);
    }).join(' ');
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          "What’s the pest problem you have?",
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: AppTheme.black,
          ),
        ),
        const SizedBox(height: 5),
        const Text(
          'Select the type of pest you’re dealing with.',
          style: TextStyle(
            color: AppTheme.gray,
            fontSize: 12,
          ),
        ),
        const SizedBox(height: 14),

        if (isLoadingPackages)
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
              scrollDirection: Axis.horizontal,
              itemCount: servicePackages.length,
              separatorBuilder: (_, __) => const SizedBox(width: 10),
              itemBuilder: (context, index) {
                final service = servicePackages[index];
                final isSelected = selectedServicePackages.any(
                  (selected) => selected['id'] == service['id'],
                );

                return GestureDetector(
                  onTap: () => onToggleService(service),
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
                          child: Padding(
                            padding: const EdgeInsets.all(4),
                            child: Image.asset(
                              _serviceImage(service['name'] ?? ''),
                              fit: BoxFit.contain,
                            ),
                          ),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          _formatServiceName(service['name'] ?? ''),
                          maxLines: 3,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(
                            color: isSelected ? AppTheme.white : AppTheme.black,
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            height: 1.15,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          _serviceShortText(service['name'] ?? ''),
                          maxLines: 3,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(
                            color: isSelected ? AppTheme.white : AppTheme.gray,
                            fontSize: 12,
                            height: 1.25,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),

        if (selectedServicePackages.isNotEmpty) ...[
          const SizedBox(height: 12),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 9),
            decoration: BoxDecoration(
              color: AppTheme.white,
              borderRadius: BorderRadius.circular(9),
              border: Border.all(
                color: AppTheme.primaryRed.withOpacity(0.15),
              ),
            ),
            child: Row(
              children: [
                const Icon(
                  Icons.check_circle,
                  color: AppTheme.primaryRed,
                  size: 16,
                ),
                const SizedBox(width: 7),
                Expanded(
                  child: Text(
                    '${selectedServicePackages.length} pest types selected',
                    style: const TextStyle(
                      color: AppTheme.primaryRed,
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                GestureDetector(
                  onTap: onClearAll,
                  child: const Text(
                    'Clear all',
                    style: TextStyle(
                      color: AppTheme.primaryRed,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ],
    );
  }
}