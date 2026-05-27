import 'package:flutter/material.dart';
import 'package:mobile_app/app/theme.dart';

class ClientAppointmentCard extends StatefulWidget {
  final Map<String, dynamic> appointment;

  const ClientAppointmentCard({
    super.key,
    required this.appointment,
  });

  @override
  State<ClientAppointmentCard> createState() => _ClientAppointmentCardState();
}

class _ClientAppointmentCardState extends State<ClientAppointmentCard> {
  bool isExpanded = false;

  bool _isAssessedStatus(String status) {
    return status.toUpperCase() == 'ASSESSED';
  }

  String _costTitle(String status) {
    switch (status.toUpperCase()) {
      case 'ASSESSED':
        return 'ASSESSED COST';
      case 'SCHEDULED':
        return 'SCHEDULED COST';
      case 'ONGOING':
        return 'SERVICE COST';
      case 'COMPLETED':
        return 'FINAL COST';
      default:
        return 'ESTIMATED COST';
    }
  }

  String _costSubtitle(String status) {
    switch (status.toUpperCase()) {
      case 'ASSESSED':
        return 'Assessment completed';
      case 'SCHEDULED':
        return 'Service visit scheduled';
      case 'ONGOING':
        return 'Service is currently ongoing';
      case 'COMPLETED':
        return 'Service completed successfully';
      default:
        return 'Initial estimate only';
    }
  }

  String _bottomMessage(String status) {
    switch (status.toUpperCase()) {
      case 'ASSESSED':
        return 'Assessment completed.';
      case 'SCHEDULED':
        return 'Visit has been scheduled.';
      case 'ONGOING':
        return 'Service is currently ongoing.';
      case 'COMPLETED':
        return 'Service completed.';
      default:
        return 'We’ll contact you after assessment.';
    }
  }

  String _text(dynamic value, [String fallback = '']) {
    if (value == null) return fallback;
    final text = value.toString().trim();
    return text.isEmpty ? fallback : text;
  }

  String _formatPestName(String pest) {
    final normalized = pest.trim().toUpperCase();

    switch (normalized) {
      case 'RATS/MICE':
        return 'Rodent';

      case 'BED BUGS':
        return 'Bed Bug';

      case 'COCKROACHES':
        return 'Cockroach';

      case 'ANTS':
        return 'Ant';

      case 'TERMITES':
        return 'Termite';

      default:
        return normalized
            .toLowerCase()
            .split(' ')
            .map((word) {
              if (word.isEmpty) return word;

              return word[0].toUpperCase() +
                  word.substring(1);
            })
            .join(' ');
    }
  }

  List<String> _pestList() {
    final fullPestTypes = _text(widget.appointment['fullPestTypes']);

    if (fullPestTypes.isNotEmpty) {
      return fullPestTypes
          .split(',')
          .map((item) => item.trim())
          .where((item) => item.isNotEmpty)
          .toList();
    }

    return [_text(widget.appointment['service'], 'Pest Control Service')];
  }
  
  List<String> _areaList() {
    final fullAreas =
        _text(widget.appointment['areaTypes']);

    if (fullAreas.isNotEmpty) {
      return fullAreas
          .split(',')
          .map((item) => item.trim())
          .where((item) => item.isNotEmpty)
          .toList();
    }

    if (widget.appointment['isTermite'] == true) {
      final sqm =
          _text(widget.appointment['sqmDetails']);

      if (sqm.isNotEmpty) {
        return [sqm];
      }
    }

    return ['Not specified'];
  }

  String _recommendation() {
    final status =
        _text(widget.appointment['status']);

    final recommendation =
        _text(widget.appointment[
            'assessmentRecommendation']);

    if (recommendation.isNotEmpty) {
      return recommendation;
    }

    switch (status.toUpperCase()) {
      case 'ASSESSED':
        return 'Assessment Done';

      case 'SCHEDULED':
        return 'Visit Scheduled';

      case 'ONGOING':
        return 'Treatment Ongoing';

      case 'COMPLETED':
        return 'Treatment Completed';

      case 'ONGOING':
        return 'Treatment Ongoing';

      default:
        return 'Pending Review';
    }
  }

  String? _recommendationSubtitle() {
    final status =
        _text(widget.appointment['status']);

    switch (status.toUpperCase()) {
      case 'ASSESSED':
        return 'Service quotation prepared';

      case 'SCHEDULED':
        return 'Technician assigned';

      case 'ONGOING':
        return 'Technician is currently servicing';

      case 'COMPLETED':
        return 'Service has been finalized';

      default:
        return 'Admin review ongoing';
    }
  }

  @override
  Widget build(BuildContext context) {
    final appointment = widget.appointment;

    final isTermite = appointment['isTermite'] == true;
    final status = _text(appointment['status'], 'REQUESTED');
    final pests = _pestList();

    final service = isTermite
      ? 'Termite Treatment'
      : pests.length == 1
          ? '${_formatPestName(pests.first)} Treatment'
          : 'General Pest Control';
    final price = _text(appointment['price'], '₱0.00');
    final schedule = _text(appointment['schedule'], 'No schedule');
    final address = _text(appointment['address'], 'No address');
    final requestedDate = _text(appointment['requestedDate'], 'No request date');

    final areaType = isTermite
        ? _text(appointment['sqmDetails'], 'Not specified')
        : _text(appointment['areaTypes'], 'No area details');

    return AnimatedContainer(
      duration: const Duration(milliseconds: 220),
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: AppTheme.borderGray),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              StatusBadge(status: status),
              const Spacer(),
              Text(
                price,
                style: const TextStyle(
                  color: AppTheme.primaryRed,
                  fontSize: 18,
                  fontWeight: FontWeight.w900,
                ),
              ),
            ],
          ),

          const SizedBox(height: 16),

          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            service,
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                              color: AppTheme.black,
                              fontSize: 16,
                              fontWeight: FontWeight.w900,
                              height: 1.2,
                            ),
                          ),
                        ),
                        InkWell(
                          borderRadius: BorderRadius.circular(20),
                          onTap: () {
                            setState(() {
                              isExpanded = !isExpanded;
                            });
                          },
                          child: Icon(
                            isExpanded
                                ? Icons.keyboard_arrow_up_rounded
                                : Icons.keyboard_arrow_down_rounded,
                            color: AppTheme.black,
                            size: 24,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    InfoRow(
                      icon: Icons.calendar_month_outlined,
                      text: schedule,
                    ),
                    const SizedBox(height: 7),
                    InfoRow(
                      icon: Icons.location_on_outlined,
                      text: address,
                      showTooltip: true,
                    ),
                  ],
                ),
              ),
            ],
          ),

          if (isExpanded) ...[
            const SizedBox(height: 16),

            Column(
              children: [
                IntrinsicHeight(
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Expanded(
                        child: DetailCard(
                          icon: Icons.assignment_outlined,
                          title: 'PEST TYPES',
                          value: _pestList().join(', '),
                          badges: _pestList(),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: DetailCard(
                          icon: Icons.home_outlined,
                          title: isTermite
                            ? 'AREA SIZE'
                            : 'AREA TYPES',
                          value: areaType,
                          badges: _areaList(),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Expanded(
                      child: DetailCard(
                        icon: Icons.tips_and_updates_outlined,
                        title: 'SERVICE STATUS',
                        value: _recommendation(),
                        subtitle: _recommendationSubtitle(),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: DetailCard(
                        icon: Icons.receipt_long_outlined,
                        title: _costTitle(status),
                        value: price,
                        subtitle: _costSubtitle(status),
                      ),
                    ),
                  ],
                ),
              ],
            ),

            const SizedBox(height: 14),

            Column(
              children: [
                InfoSummaryBox(
                  icon: Icons.access_time_rounded,
                  title: 'Requested on',
                  value: requestedDate,
                ),

                const SizedBox(height: 8),

                InfoSummaryBox(
                  icon: status.toUpperCase() == 'ASSESSED'
                    ? Icons.assignment_turned_in_outlined
                    : status.toUpperCase() == 'SCHEDULED'
                        ? Icons.event_available_outlined
                        : status.toUpperCase() == 'ONGOING'
                            ? Icons.build_circle_outlined
                            : status.toUpperCase() == 'COMPLETED'
                                ? Icons.check_circle_outline_rounded
                                : Icons.phone_outlined,

                  title: status.toUpperCase() == 'ASSESSED'
                    ? 'Assessment update'
                    : status.toUpperCase() == 'SCHEDULED'
                        ? 'Schedule update'
                        : status.toUpperCase() == 'ONGOING'
                            ? 'Service update'
                            : status.toUpperCase() == 'COMPLETED'
                                ? 'Completion update'
                                : 'Request update',

                  value: status.toUpperCase() == 'ASSESSED'
                    ? 'Assessment completed. Your appointment is ready for scheduling.'
                    : status.toUpperCase() == 'SCHEDULED'
                        ? 'Your service visit has been scheduled.'
                        : status.toUpperCase() == 'ONGOING'
                            ? 'Our technician is currently performing the treatment service.'
                            : status.toUpperCase() == 'COMPLETED'
                                ? 'Your pest control service has been completed successfully.'
                                : 'Pending assessment. We’ll review your request shortly.',
                ),
              ],
            ),

            const SizedBox(height: 14),

           AppointmentProgress(status: status),

            // const SizedBox(height: 12),

            // ViewServiceInformationButton(
            //   onTap: () {
            //     // TODO: Navigate to service information page later
            //   },
            // ),
          ],
        ],
      ),
    );
  }
}

class SquareIcon extends StatelessWidget {
  final IconData icon;

  const SquareIcon({
    super.key,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 44,
      height: 44,
      decoration: BoxDecoration(
        color: AppTheme.lightGray,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Icon(
        icon,
        color: AppTheme.black,
        size: 22,
      ),
    );
  }
}

class SmallCircleIcon extends StatelessWidget {
  final IconData icon;

  const SmallCircleIcon({
    super.key,
    required this.icon,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 24,
      height: 24,
      decoration: const BoxDecoration(
        color: AppTheme.white,
        shape: BoxShape.circle,
      ),
      child: Icon(
        icon,
        color: AppTheme.black,
        size: 15,
      ),
    );
  }
}

class DetailCard extends StatelessWidget {
  final IconData icon;
  final String title;
  final String value;
  final String? subtitle;
  final List<String>? badges;

  const DetailCard({
    super.key,
    required this.icon,
    required this.title,
    required this.value,
    this.subtitle,
    this.badges,
  });

  @override
  Widget build(BuildContext context) {
    final hasBadges = badges != null && badges!.isNotEmpty;

    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppTheme.borderGray),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 22,
                height: 22,
                decoration: BoxDecoration(
                  color: AppTheme.lightGray,
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Icon(
                  icon,
                  color: AppTheme.black,
                  size: 13,
                ),
              ),
              const SizedBox(width: 6),
              Expanded(
                child: Text(
                  title,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    color: AppTheme.gray,
                    fontSize: 7.5,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 0.4,
                  ),
                ),
              ),
            ],
          ),

          const SizedBox(height: 9),

          if (hasBadges)
            Expanded(
              child: SingleChildScrollView(
                physics: const NeverScrollableScrollPhysics(),
                child: Wrap(
                  spacing: 5,
                  runSpacing: 5,
                  children: badges!.map((badge) {
                    return PestBadge(text: badge);
                  }).toList(),
                ),
              ),
            )
          else ...[
            Text(
              value,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                color: AppTheme.black,
                fontSize: 10.5,
                height: 1.2,
                fontWeight: FontWeight.w800,
              ),
            ),
            if (subtitle != null) ...[
              const SizedBox(height: 3),
              Text(
                subtitle!,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                  color: AppTheme.gray,
                  fontSize: 8,
                  height: 1.15,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ],
        ],
      ),
    );
  }
}

class PestBadge extends StatelessWidget {
  final String text;

  const PestBadge({
    super.key,
    required this.text,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(
        horizontal: 7,
        vertical: 4,
      ),
      decoration: BoxDecoration(
        color: AppTheme.primaryRed.withOpacity(0.08),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(
        text,
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
        style: const TextStyle(
          color: AppTheme.black,
          fontSize: 8.5,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}

class AppointmentProgress extends StatelessWidget {
  final String status;

  const AppointmentProgress({
    super.key,
    required this.status,
  });

  int get activeIndex {
    switch (status.toUpperCase()) {
      case 'ASSESSED':
        return 1;
      case 'SCHEDULED':
        return 2;
      case 'ONGOING':
        return 3;
      case 'COMPLETED':
        return 4;
      default:
        return 0;
    }
  }

  @override
  Widget build(BuildContext context) {
    final steps = [
      'Requested',
      'Assessed',
      'Scheduled',
      'Ongoing',
      'Completed',
    ];

    return Container(
      padding: const EdgeInsets.fromLTRB(14, 13, 14, 12),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppTheme.borderGray),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Track your appointment progress',
            style: TextStyle(
              color: AppTheme.black,
              fontSize: 11.5,
              fontWeight: FontWeight.w900,
            ),
          ),
          const SizedBox(height: 2),
          const Text(
            'Stay updated on every step.',
            style: TextStyle(
              color: AppTheme.gray,
              fontSize: 9,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 18),

          Row(
            children: List.generate(steps.length * 2 - 1, (i) {
              if (i.isOdd) {
                final lineIndex = i ~/ 2;
                final isActiveLine = lineIndex < activeIndex;

                return Expanded(
                  child: Container(
                    height: 1.5,
                    color: isActiveLine
                        ? AppTheme.primaryRed
                        : AppTheme.borderGray,
                  ),
                );
              }

              final index = i ~/ 2;
              final isActive = index <= activeIndex;
              final isCurrent = index == activeIndex;

              return Container(
                width: 26,
                height: 26,
                decoration: BoxDecoration(
                  color: AppTheme.white,
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: isActive
                        ? AppTheme.primaryRed
                        : AppTheme.borderGray,
                    width: isCurrent ? 2 : 1.4,
                  ),
                ),
                child: Icon(
                  isCurrent
                      ? Icons.radio_button_checked
                      : isActive
                          ? Icons.check
                          : Icons.circle,
                  size: isCurrent ? 14 : 13,
                  color: isActive
                      ? AppTheme.primaryRed
                      : AppTheme.borderGray,
                ),
              );
            }),
          ),

          const SizedBox(height: 7),

          Row(
            children: List.generate(steps.length, (index) {
              final isCurrent = index == activeIndex;

              return Expanded(
                child: Text(
                  steps[index],
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: isCurrent
                        ? AppTheme.primaryRed
                        : AppTheme.gray,
                    fontSize: 7.2,
                    fontWeight:
                        isCurrent ? FontWeight.w900 : FontWeight.w600,
                  ),
                ),
              );
            }),
          ),
        ],
      ),
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
        horizontal: 10,
        vertical: 5,
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
  final bool showTooltip;

  const InfoRow({
    super.key,
    required this.icon,
    required this.text,
    this.showTooltip = false,
  });

  @override
  Widget build(BuildContext context) {
    final textWidget = Text(
      text,
      maxLines: 1,
      overflow: TextOverflow.ellipsis,
      style: const TextStyle(
        color: AppTheme.gray,
        fontSize: 12,
        height: 1.3,
        fontWeight: FontWeight.w500,
      ),
    );

    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(
          icon,
          color: AppTheme.gray,
          size: 15,
        ),
        const SizedBox(width: 7),
        Expanded(
          child: showTooltip
              ? Tooltip(
                  message: text,
                  triggerMode: TooltipTriggerMode.longPress,
                  child: textWidget,
                )
              : textWidget,
        ),
      ],
    );
  }
}

class InfoSummaryBox extends StatelessWidget {
  final IconData icon;
  final String title;
  final String value;

  const InfoSummaryBox({
    super.key,
    required this.icon,
    required this.title,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(11),
      decoration: BoxDecoration(
        color: AppTheme.lightGray.withOpacity(0.5),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                icon,
                size: 13,
                color: AppTheme.gray,
              ),
              const SizedBox(width: 5),
              Expanded(
                child: Text(
                  title.toUpperCase(),
                  style: const TextStyle(
                    color: AppTheme.gray,
                    fontSize: 8,
                    fontWeight: FontWeight.w900,
                    letterSpacing: 0.4,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 4),
          Text(
            value,
            style: const TextStyle(
              color: AppTheme.black,
              fontSize: 10.5,
              height: 1.25,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }
}

// class ViewServiceInformationButton extends StatelessWidget {
//   final VoidCallback onTap;

//   const ViewServiceInformationButton({
//     super.key,
//     required this.onTap,
//   });

//   @override
//   Widget build(BuildContext context) {
//     return InkWell(
//       borderRadius: BorderRadius.circular(14),
//       onTap: onTap,
//       child: Container(
//         width: double.infinity,
//         padding: const EdgeInsets.symmetric(
//           horizontal: 14,
//           vertical: 14,
//         ),
//         decoration: BoxDecoration(
//           color: AppTheme.white,
//           borderRadius: BorderRadius.circular(14),
//           border: Border.all(color: AppTheme.borderGray),
//         ),
//         child: const Row(
//           children: [
//             Expanded(
//               child: Text(
//                 'View Service Information',
//                 style: TextStyle(
//                   color: AppTheme.black,
//                   fontSize: 12,
//                   fontWeight: FontWeight.w900,
//                 ),
//               ),
//             ),
//             Icon(
//               Icons.chevron_right_rounded,
//               color: AppTheme.black,
//               size: 22,
//             ),
//           ],
//         ),
//       ),
//     );
//   }
// }