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
                final imagePath = _serviceImage(service['name'] ?? '');

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
                            child: GestureDetector(
                              onLongPress: () => _openImagePreview(
                                context,
                                imagePath,
                              ),
                              child: Image.asset(
                                imagePath,
                                fit: BoxFit.contain,
                              ),
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
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.black.withOpacity(0.025),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: Colors.black.withOpacity(0.05),
              ),
            ),
            child: Row(
              children: [
               Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    color: Colors.black.withOpacity(0.04),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(
                    selectedServicePackages.length > 1
                        ? Icons.inventory_2_outlined
                        : Icons.pest_control_rounded,
                    color: AppTheme.black,
                    size: 18,
                  ),
                ),

                const SizedBox(width: 10),

                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        selectedServicePackages.length > 1
                            ? 'Service Package'
                            : 'Selected Pest',
                        style: const TextStyle(
                          color: AppTheme.black,
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                        ),
                      ),

                      const SizedBox(height: 3),

                      Text(
                        '${selectedServicePackages.length} pest type${selectedServicePackages.length > 1 ? 's' : ''} selected',
                        style: const TextStyle(
                          color: AppTheme.gray,
                          fontSize: 11.5,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),

                GestureDetector(
                  onTap: onClearAll,
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 6,
                    ),
                    decoration: BoxDecoration(
                      color: AppTheme.white,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Text(
                      'Clear',
                      style: TextStyle(
                        color: AppTheme.black,
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                      ),
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