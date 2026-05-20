import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class ClientAppointmentCard extends StatefulWidget {
  final Map<String, dynamic> appointment;

  const ClientAppointmentCard({
    super.key,
    required this.appointment,
  });

  @override
  State<ClientAppointmentCard> createState() =>
      _ClientAppointmentCardState();
}

class _ClientAppointmentCardState
    extends State<ClientAppointmentCard> {
  bool isExpanded = false;

  @override
  Widget build(BuildContext context) {
    final appointment = widget.appointment;
    final isTermite = appointment['isTermite'] == true;

    return GestureDetector(
      onTap: () {
        setState(() {
          isExpanded = !isExpanded;
        });
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 220),
        width: double.infinity,
        padding: const EdgeInsets.all(12),
        decoration: AppTheme.borderedCardDecoration,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                StatusBadge(
                  status: appointment['status'],
                ),
                const Spacer(),
                Text(
                  appointment['price'],
                  style: const TextStyle(
                    color: AppTheme.primaryRed,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),

            const SizedBox(height: 14),

            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                CircleIcon(
                  icon: isTermite
                      ? Icons.square_foot_outlined
                      : Icons.bug_report_outlined,
                ),

                const SizedBox(width: 10),

                Expanded(
                  child: Column(
                    crossAxisAlignment:
                        CrossAxisAlignment.start,
                    children: [
                      Text(
                        appointment['service'],
                        style: const TextStyle(
                          color: AppTheme.black,
                          fontSize: 15,
                          fontWeight: FontWeight.w700,
                          height: 1.2,
                        ),
                      ),

                      const SizedBox(height: 4),

                      Text(
                        isTermite
                            ? (appointment['sqmDetails'] ??
                                'No sqm details')
                            : (appointment['areaTypes'] ??
                                'No area details'),
                        style: const TextStyle(
                          color: AppTheme.gray,
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                          height: 1.3,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(width: 8),

                Icon(
                  isExpanded
                      ? Icons.keyboard_arrow_up_rounded
                      : Icons.chevron_right_rounded,
                  color: AppTheme.primaryRed,
                  size: 24,
                ),
              ],
            ),

            const SizedBox(height: 14),

            InfoRow(
              icon: Icons.calendar_month_outlined,
              text: appointment['schedule'],
            ),

            const SizedBox(height: 8),

            InfoRow(
              icon: Icons.location_on_outlined,
              text: appointment['address'],
            ),

            if (isExpanded) ...[
              const SizedBox(height: 14),

              Container(
                height: 1,
                color: AppTheme.borderGray,
              ),

              const SizedBox(height: 14),

              ExpandedDetail(
                label: 'Pest Types',
                value: appointment['fullPestTypes'] ??
                    appointment['service'],
              ),

              const SizedBox(height: 12),

              ExpandedDetail(
                label: isTermite
                    ? 'Termite Area Size'
                    : 'Area Type',
                value: isTermite
                    ? (appointment['sqmDetails'] ??
                        'No sqm details')
                    : (appointment['areaTypes'] ??
                        'No area details'),
              ),

              const SizedBox(height: 12),

              ExpandedDetail(
                label: 'Estimated Total Cost',
                value: appointment['price'],
              ),
            ],

            const SizedBox(height: 14),

            Container(
              height: 1,
              color: AppTheme.borderGray,
            ),

            const SizedBox(height: 10),

            InfoRow(
              icon: Icons.access_time_rounded,
              text: appointment['requestedDate'],
            ),
          ],
        ),
      ),
    );
  }
}

class CircleIcon extends StatelessWidget {
  final IconData icon;

  const CircleIcon({
    super.key,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 38,
      height: 38,
      decoration: BoxDecoration(
        color: AppTheme.primaryRed.withOpacity(0.08),
        shape: BoxShape.circle,
      ),
      child: Icon(
        icon,
        color: AppTheme.primaryRed,
        size: 20,
      ),
    );
  }
}

class ExpandedDetail extends StatelessWidget {
  final String label;
  final String value;

  const ExpandedDetail({
    super.key,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment:
          CrossAxisAlignment.start,
      children: [
        Text(
          label.toUpperCase(),
          style: const TextStyle(
            color: AppTheme.gray,
            fontSize: 10,
            fontWeight: FontWeight.bold,
            letterSpacing: 0.5,
          ),
        ),

        const SizedBox(height: 4),

        Text(
          value,
          style: const TextStyle(
            color: AppTheme.black,
            fontSize: 13,
            fontWeight: FontWeight.w600,
            height: 1.3,
          ),
        ),
      ],
    );
  }
}

class StatusBadge extends StatelessWidget {
  final String status;

  const StatusBadge({
    super.key,
    required this.status,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: 8,
        vertical: 4,
      ),
      decoration: BoxDecoration(
        color: AppTheme.primaryRed.withOpacity(0.08),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        status,
        style: const TextStyle(
          color: AppTheme.primaryRed,
          fontSize: 9.5,
          fontWeight: FontWeight.bold,
          letterSpacing: 0.3,
        ),
      ),
    );
  }
}

class InfoRow extends StatelessWidget {
  final IconData icon;
  final String text;

  const InfoRow({
    super.key,
    required this.icon,
    required this.text,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Icon(
          icon,
          color: AppTheme.gray,
          size: 15,
        ),

        const SizedBox(width: 7),

        Expanded(
          child: Text(
            text,
            style: const TextStyle(
              color: AppTheme.gray,
              fontSize: 12,
              height: 1.3,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ],
    );
  }
}