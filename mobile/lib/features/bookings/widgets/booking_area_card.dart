import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class BookingAreaCard extends StatelessWidget {
  final List<Map<String, dynamic>> serviceAreas;
  final List<Map<String, dynamic>> selectedAreas;
  final void Function(Map<String, dynamic> area) onToggleArea;
  final VoidCallback onClearAll;

  const BookingAreaCard({
    super.key,
    required this.serviceAreas,
    required this.selectedAreas,
    required this.onToggleArea,
    required this.onClearAll,
  });

  String _areaImage(String area) {
    switch (area.toUpperCase()) {
      case 'ATTIC':
        return 'assets/images/img_attic.png';
      case 'BASEMENT':
        return 'assets/images/img_basement.png';
      case 'BATHROOM':
        return 'assets/images/img_bathroom.png';
      case 'BEDROOM':
        return 'assets/images/img_bedroom.png';
      case 'DINING ROOM':
        return 'assets/images/img_diningroom.png';
      case 'GARAGE':
        return 'assets/images/img_garage.png';
      case 'GARDEN/YARD':
        return 'assets/images/img_gardenyard.png';
      case 'KITCHEN':
        return 'assets/images/img_kitchen.png';
      case 'LIVING ROOM':
        return 'assets/images/img_livingroom.png';
      case 'OFFICE/STUDY':
        return 'assets/images/img_officestudy.png';
      case 'STORAGE ROOM':
        return 'assets/images/img_storageroom.png';
      case 'WHOLE PROPERTY':
        return 'assets/images/img_wholeproperty.png';
      default:
        return 'assets/images/img_defaultcards.png';
    }
  }

  String _formatAreaName(String text) {
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
          'Where did the problem occur?',
          style: TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: AppTheme.black,
          ),
        ),
        const SizedBox(height: 5),
        const Text(
          'Select all areas where the pest problem is present.',
          style: TextStyle(
            color: AppTheme.gray,
            fontSize: 12,
          ),
        ),
        const SizedBox(height: 14),

        SizedBox(
          height: 220,
          child: ListView.separated(
            padding: const EdgeInsets.only(right: 20),
            scrollDirection: Axis.horizontal,
            itemCount: serviceAreas.length,
            separatorBuilder: (_, __) => const SizedBox(width: 10),
            itemBuilder: (context, index) {
              final area = serviceAreas[index];

              final isSelected = selectedAreas.any(
                (selected) => selected['id'] == area['id'],
              );
              final imagePath = _areaImage(area['area'] ?? '');

              return GestureDetector(
                onTap: () => onToggleArea(area),
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
                          child: Center(
                            child: SizedBox(
                              width: 82,
                              height: 82,
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
                        ),
                      ),
                      const SizedBox(height: 12),
                      Text(
                        _formatAreaName(area['area'] ?? ''),
                        maxLines: 2,
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
                        '₱${area['cost']}',
                        style: TextStyle(
                          color: isSelected ? AppTheme.white : AppTheme.gray,
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        ),

       if (selectedAreas.isNotEmpty) ...[
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
                child: const Icon(
                  Icons.home_work_outlined,
                  color: AppTheme.black,
                  size: 18,
                ),
              ),

              const SizedBox(width: 10),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Affected Areas',
                      style: TextStyle(
                        color: AppTheme.black,
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                      ),
                    ),

                    const SizedBox(height: 3),

                    Text(
                      '${selectedAreas.length} area${selectedAreas.length > 1 ? 's' : ''} selected',
                      style: const TextStyle(
                        color: AppTheme.gray,
                        fontSize: 11.5,
                        fontWeight: FontWeight.w500,
                      ),
                    ),

                    const SizedBox(height: 2),
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