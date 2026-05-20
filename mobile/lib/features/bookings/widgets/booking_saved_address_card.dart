import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class BookingSavedAddressCard extends StatelessWidget {
  final List<Map<String, dynamic>> addresses;
  final String? selectedAddressId;
  final Function(Map<String, dynamic> address) onSelected;
  final VoidCallback onAddNewAddress;

  const BookingSavedAddressCard({
    super.key,
    required this.addresses,
    required this.selectedAddressId,
    required this.onSelected,
    required this.onAddNewAddress,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: AppTheme.borderedCardDecoration,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Choose Saved Address',
            style: TextStyle(
              color: AppTheme.black,
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 4),
          const Text(
            'Select the address where the service will happen.',
            style: TextStyle(
              color: AppTheme.gray,
              fontSize: 13,
              height: 1.3,
            ),
          ),
          const SizedBox(height: 14),

          if (addresses.isEmpty)
            _emptyAddressState()
          else
            ...List.generate(addresses.length, (index) {
              final address = addresses[index];
              final id = address['id'].toString();
              final type = address['type'] ?? 'ADDRESS';
              final fullAddress = address['address'] ?? '';
              final isSelected = selectedAddressId == id;
              final isLast = index == addresses.length - 1;

              return _addressItem(
                type: type,
                fullAddress: fullAddress,
                isSelected: isSelected,
                isLast: isLast,
                onTap: () => onSelected(address),
              );
            }),

            const SizedBox(height: 10),
            _addNewAddressItem(),
        ],
      ),
    );
  }

  Widget _addressItem({
    required String type,
    required String fullAddress,
    required bool isSelected,
    required bool isLast,
    required VoidCallback onTap,
  }) {
    return Container(
      margin: EdgeInsets.only(bottom: isLast ? 0 : 10),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: isSelected
                ? AppTheme.primaryRed.withOpacity(0.08)
                : AppTheme.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: isSelected ? AppTheme.primaryRed : AppTheme.borderGray,
              width: isSelected ? 1.2 : 1,
            ),
          ),
          child: Row(
            children: [
              Container(
                width: 42,
                height: 42,
                decoration: BoxDecoration(
                  color: isSelected
                      ? AppTheme.primaryRed.withOpacity(0.12)
                      : AppTheme.lightGray,
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  _iconForType(type),
                  color: isSelected ? AppTheme.primaryRed : AppTheme.gray,
                  size: 22,
                ),
              ),
              const SizedBox(width: 12),

              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      type.toString().toUpperCase(),
                      style: const TextStyle(
                        color: AppTheme.black,
                        fontSize: 13,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 0.2,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                        fullAddress,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          color: AppTheme.gray,
                          fontSize: 13,
                          height: 1.3,
                        ),
                      ),
                  ],
                ),
              ),

              const SizedBox(width: 10),

              Icon(
                isSelected
                    ? Icons.check_circle
                    : Icons.radio_button_unchecked,
                color: isSelected ? AppTheme.primaryRed : AppTheme.borderGray,
                size: 22,
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _emptyAddressState() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppTheme.lightGray,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppTheme.borderGray),
      ),
      child: const Row(
        children: [
          Icon(
            Icons.location_off_outlined,
            color: AppTheme.gray,
            size: 22,
          ),
          SizedBox(width: 10),
          Expanded(
            child: Text(
              'No saved address found. Please enter a new address.',
              style: TextStyle(
                color: AppTheme.gray,
                fontSize: 13,
                height: 1.3,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _addNewAddressItem() {
    return Material(
    color: Colors.transparent,
    child: InkWell(
      borderRadius: BorderRadius.circular(12),
      splashColor: AppTheme.black.withOpacity(0.08),
      highlightColor: AppTheme.black.withOpacity(0.04),
      onTap: onAddNewAddress,
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(
          color: const Color.fromARGB(255, 43, 42, 42).withOpacity(0.18),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
          color: const Color.fromARGB(255, 43, 42, 42).withOpacity(0.18),
          ),
        ),
        child: const Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.add_rounded,
              color: Color.fromARGB(255, 92, 89, 89),
              size: 20,
            ),
            SizedBox(width: 8),
            Text(
              'Add New Address',
              style: TextStyle(
                color: Color.fromARGB(255, 92, 89, 89),
                fontSize: 13,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
        ),
      ),
    );
  }

  IconData _iconForType(String type) {
    switch (type.toUpperCase()) {
      case 'HOME':
        return Icons.home_outlined;

      case 'WORK':
        return Icons.work_outline;

      case 'COMPANY':
        return Icons.business_outlined;

      case 'FAVORITE':
        return Icons.favorite_border;

      case 'RESIDENTIAL':
        return Icons.apartment_outlined;

      default:
        return Icons.location_on_outlined;
    }
  }
}