import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';
import 'package:mobile_app/shared/widgets/dialogs/app_confirmation_dialog.dart';

class DeleteAddressDialog extends StatelessWidget {
  final bool isPrimary;
  final VoidCallback onConfirm;

  const DeleteAddressDialog({
    super.key,
    required this.isPrimary,
    required this.onConfirm,
  });

  @override
  Widget build(BuildContext context) {
    return AppConfirmationDialog(
      icon: isPrimary
          ? Icons.warning_amber_rounded
          : Icons.delete_outline_rounded,
      title: isPrimary ? 'Delete Primary Address?' : 'Delete Address?',
      message: isPrimary
          ? 'Primary address cannot be deleted. Please set another address as primary first.'
          : 'This address will be removed from your saved addresses.',
      confirmText: isPrimary ? 'Okay' : 'Delete',
      cancelText: 'Cancel',
      confirmColor: Colors.red,
      onConfirm: isPrimary ? () {} : onConfirm,
    );
  }
}