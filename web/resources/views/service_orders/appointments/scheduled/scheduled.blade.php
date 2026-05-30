@extends('layouts.themes.main')

@section('content')
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    <style>
        .flatpickr-calendar {
            font-size: 13px;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .12);
        }

        .flatpickr-day.disabled,
        .flatpickr-day.disabled:hover {
            color: #ccc !important;
            background: transparent !important;
            cursor: not-allowed !important;
            text-decoration: line-through;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #007bff !important;
            border-color: #007bff !important;
        }
    </style>

    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Scheduled Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ action('App\Http\Controllers\AdminController@home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">Appointments</li>
                        <li class="breadcrumb-item active">Scheduled</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <section class="content">
        @include('layouts.partials.onclick')
        @include('layouts.partials.alerts')
        @include('layouts.partials.modal_style')

        <div class="container-fluid">
            <div class="card">

                {{-- Card Header --}}
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
                    <h3 class="card-title mb-0">
                        <i class="far fa-calendar-alt mr-1"></i>
                        Scheduled Appointments
                    </h3>
                </div>

                <div class="card-body p-0">

                    {{-- Day navigation bar --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap p-3 border-bottom"
                        style="gap:10px;">

                        {{-- Previous day --}}
                        @if ($prevDate)
                            <a href="{{ url('service/orders/appointments/scheduled') }}?tl_date={{ $prevDate }}"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-left mr-1"></i> Previous day
                            </a>
                        @else
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="fas fa-chevron-left mr-1"></i> Previous day
                            </button>
                        @endif

                        {{-- Date label + Flatpickr --}}
                        <div class="d-flex align-items-center" style="gap:10px; flex-wrap:wrap;">
                            <span class="font-weight-bold" style="font-size:15px;">
                                {{ \Carbon\Carbon::parse($tlDate)->format('l, F d, Y') }}
                            </span>

                            <form method="GET" action="{{ url('service/orders/appointments/scheduled') }}" id="tlDateForm"
                                class="d-flex align-items-center" style="gap:6px;">
                                <input type="hidden" name="view" value="timeline">
                                <input type="text" name="tl_date" id="tlDatePicker" value="{{ $tlDate }}"
                                    class="form-control form-control-sm" style="width:150px;" placeholder="Pick a date"
                                    readonly>
                            </form>
                        </div>

                        {{-- Next day --}}
                        @if ($nextDate)
                            <a href="{{ url('service/orders/appointments/scheduled') }}?tl_date={{ $nextDate }}"
                                class="btn btn-sm btn-outline-secondary">
                                Next day <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        @else
                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                Next day <i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        @endif
                    </div>

                    {{-- Legend --}}
                    <div class="px-3 pt-2 pb-1 d-flex flex-wrap" style="gap:16px; font-size:12px; color:#666;">
                        <span class="d-flex align-items-center" style="gap:5px;">
                            <span
                                style="width:12px;height:12px;border-radius:3px;background:#B5D4F4;border:0.5px solid #85B7EB;display:inline-block;"></span>
                            Appointment slot
                        </span>
                        <span class="d-flex align-items-center" style="gap:5px;">
                            <span
                                style="width:12px;height:12px;border-radius:3px;background:#C0DD97;border:0.5px solid #97C459;display:inline-block;"></span>
                            Paid
                        </span>
                        <span class="d-flex align-items-center" style="gap:5px;">
                            <span
                                style="width:12px;height:12px;border-radius:3px;background:#FAC775;border:0.5px solid #EF9F27;display:inline-block;"></span>
                            Unpaid
                        </span>
                        <span class="d-flex align-items-center" style="gap:5px;">
                            <span
                                style="width:12px;height:12px;border-radius:3px;background:#fff;border:1px solid #bbb;display:inline-block;"></span>
                            Assessed (unassigned)
                        </span>
                    </div>

                    {{-- Timeline container --}}
                    <div class="p-3" style="overflow-x:auto;" id="tlOuter">
                        @if (empty($timelineByTech) && count($assessedRows) === 0)
                            <p class="text-muted text-center py-4">No appointments scheduled for this day.</p>
                        @else
                            <div id="tlContainer" style="min-width:900px;"></div>
                        @endif
                    </div>

                    {{-- Pass scheduled PHP data to JS --}}
                    @php
                        $tlDataForJs = [];
                        foreach ($timelineByTech as $techName => $rows) {
                            foreach ($rows as $row) {
                                $tlDataForJs[] = [
                                    'tech' => $techName,
                                    'client' => $row->usr_last_name . ', ' . $row->usr_first_name,
                                    'email' => $row->usr_email,
                                    'mobile' => $row->usr_mobile,
                                    'branch' => $row->branch_name,
                                    'from' => $row->svca_approved_time_from,
                                    'to' => $row->svca_approved_time_to,
                                    'payment' => $row->svc_payment_status,
                                    'termite' => (bool) $row->svc_is_termite,
                                    'package' => (bool) $row->svc_is_package,
                                    'sa_number' => 'SA-' . str_pad($row->svc_sa_number, 6, '0', STR_PAD_LEFT),
                                    'svc_id' => $row->svc_id,
                                ];
                            }
                        }
                    @endphp

                    {{-- Pass assessed PHP data to JS --}}
                    @php
                        $assessedDataForJs = [];
                        foreach ($assessedRows as $row) {
                            $assessedDataForJs[] = [
                                'client' => $row->usr_last_name . ', ' . $row->usr_first_name,
                                'email' => $row->usr_email,
                                'mobile' => $row->usr_mobile,
                                'branch' => $row->branch_name,
                                'from' => $row->svca_approved_time_from,
                                'to' => $row->svca_approved_time_to,
                                'payment' => $row->svc_payment_status,
                                'termite' => (bool) $row->svc_is_termite,
                                'package' => (bool) $row->svc_is_package,
                                'sa_number' => 'SA-' . str_pad($row->svc_sa_number, 6, '0', STR_PAD_LEFT),
                                'svc_id' => $row->svc_id,
                            ];
                        }
                    @endphp

                    {{-- Flatpickr JS --}}
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>

                    <script>
                        (function() {
                            const availableDates = @json($availableDates);
                            const assessedDates = @json($assessedDates);
                            const clickableDates = @json($clickableDates); // merged

                            document.addEventListener('DOMContentLoaded', function() {
                                flatpickr('#tlDatePicker', {
                                    enable: clickableDates, // ← was: availableDates
                                    dateFormat: 'Y-m-d',
                                    defaultDate: '{{ $tlDate }}',
                                    disableMobile: true,
                                    onChange: function(selectedDates, dateStr) {
                                        if (dateStr) {
                                            document.getElementById('tlDateForm').submit();
                                        }
                                    },
                                    onDayCreate: function(dObj, dStr, fp, dayElem) {
                                        // Fix: use local date parts, NOT toISOString() which shifts timezone
                                        const d = dayElem.dateObj;
                                        const iso = d.getFullYear() + '-' +
                                            String(d.getMonth() + 1).padStart(2, '0') + '-' +
                                            String(d.getDate()).padStart(2, '0');

                                        if (assessedDates.includes(iso)) {
                                            const badge = document.createElement('span');
                                            badge.textContent = '!';
                                            badge.style.cssText = [
                                                'position:absolute;top:1px;right:2px;',
                                                'font-size:8px;font-weight:700;',
                                                'color:#c0392b;line-height:1;',
                                            ].join('');
                                            dayElem.style.position = 'relative';
                                            dayElem.appendChild(badge);
                                        }
                                    },
                                });
                            });
                        })();
                    </script>

                    {{-- Timeline JS --}}
                    <script>
                        (function() {

                            /* Config */
                            const HOURS_S = 0;
                            const HOURS_E = 23;
                            const TOTAL = HOURS_E - HOURS_S;
                            const ROW_H = 40;
                            const LABEL_W = 130;
                            const BASE_URL = '{{ url('service/orders/appointments/scheduled') }}';
                            const ASSESSED_URL = '{{ url('service/orders/appointments/assessed') }}';

                            const DATA = @json($tlDataForJs);
                            const ASSESSED_DATA = @json($assessedDataForJs);

                            /* Helpers */
                            function parseTm(s) {
                                if (!s) return null;
                                const p = s.split(':');
                                return +p[0] + +p[1] / 60;
                            }

                            function pct(h) {
                                return ((h - HOURS_S) / TOTAL * 100).toFixed(4) + '%';
                            }

                            function fmt12(s) {
                                if (!s) return '';
                                const h = parseInt(s);
                                const m = s.split(':')[1] ?? '00';
                                const ampm = h < 12 ? 'AM' : 'PM';
                                const h12 = h === 0 ? 12 : h > 12 ? h - 12 : h;
                                return h12 + (m !== '00' ? ':' + m : '') + ' ' + ampm;
                            }

                            /* Tooltip */
                            let tt = null;

                            function ensureTT() {
                                if (tt) return;
                                tt = document.createElement('div');
                                tt.style.cssText = [
                                    'position:fixed;background:#fff;border:1px solid #ddd;',
                                    'border-radius:8px;padding:8px 12px;font-size:12px;',
                                    'pointer-events:none;z-index:9999;display:none;',
                                    'box-shadow:0 4px 12px rgba(0,0,0,.1);',
                                    'max-width:240px;line-height:1.6;color:#333;',
                                ].join('');
                                document.body.appendChild(tt);
                            }

                            function showTip(e, html) {
                                ensureTT();
                                tt.innerHTML = html;
                                tt.style.display = 'block';
                                moveTip(e);
                            }

                            function moveTip(e) {
                                if (!tt) return;
                                const offX = 14,
                                    offY = 14;
                                let x = e.clientX + offX;
                                let y = e.clientY + offY;
                                if (x + 240 > window.innerWidth) x = e.clientX - 240 - offX;
                                if (y + 160 > window.innerHeight) y = e.clientY - 160 - offY;
                                tt.style.left = x + 'px';
                                tt.style.top = y + 'px';
                            }

                            function hideTip() {
                                if (tt) tt.style.display = 'none';
                            }

                            /* makeBlock (scheduled) */
                            function makeBlock(row) {
                                const frm = parseTm(row.from);
                                const to_ = parseTm(row.to);
                                if (frm === null || to_ === null) return null;

                                const df = Math.max(frm, HOURS_S);
                                const dt = Math.min(to_, HOURS_E);
                                if (df >= dt) return null;

                                let bg, color, border;
                                if (row.payment === 'PAID') {
                                    bg = '#C0DD97';
                                    color = '#27500A';
                                    border = '#97C459';
                                } else {
                                    bg = '#FAC775';
                                    color = '#633806';
                                    border = '#EF9F27';
                                }

                                const blk = document.createElement('div');
                                blk.style.cssText = [
                                    'position:absolute;top:4px;bottom:4px;',
                                    'left:' + pct(df) + ';',
                                    'width:' + ((dt - df) / TOTAL * 100).toFixed(4) + '%;',
                                    'background:' + bg + ';color:' + color + ';',
                                    'border:0.5px solid ' + border + ';',
                                    'border-radius:5px;',
                                    'display:flex;flex-direction:column;',
                                    'align-items:center;justify-content:center;',
                                    'overflow:hidden;padding:0 4px;cursor:pointer;',
                                ].join('');

                                const nameEl = document.createElement('span');
                                nameEl.style.cssText =
                                    'font-size:10px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;';
                                nameEl.textContent = row.client;

                                const saEl = document.createElement('span'); // ← changed: was techEl
                                saEl.style.cssText =
                                    'font-size:9px;opacity:0.8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;';
                                saEl.textContent = row.sa_number; // ← changed: was row.tech

                                blk.appendChild(nameEl);
                                blk.appendChild(saEl); // ← changed: was techEl

                                const tipHtml = [
                                    '<strong>' + row.sa_number + ' &mdash; ' + row.client + '</strong>',
                                    '<hr style="margin:4px 0;border-color:#eee;">',
                                    '<span style="color:#666;">Tech:</span> ' + row.tech,
                                    '<span style="color:#666;">Branch:</span> ' + row.branch,
                                    '<span style="color:#666;">Mobile:</span> ' + (row.mobile || '—'),
                                    '<span style="color:#666;">Email:</span> ' + (row.email || '—'),
                                    '<span style="color:#666;">Time:</span> ' + fmt12(row.from) + ' – ' + fmt12(row.to),
                                    '<span style="color:#666;">Payment:</span> ' + row.payment,
                                    '<span style="color:#666;">Termite:</span> ' + (row.termite ? 'Yes' : 'No') +
                                    '&nbsp;&nbsp;<span style="color:#666;">Package:</span> ' + (row.package ? 'Yes' : 'No'),
                                ].join('<br>');

                                blk.addEventListener('mouseenter', e => showTip(e, tipHtml));
                                blk.addEventListener('mousemove', moveTip);
                                blk.addEventListener('mouseleave', hideTip);
                                blk.addEventListener('click', () => {
                                    hideTip();
                                    window.location.href = BASE_URL + '/' + row.svc_id;
                                });

                                return blk;
                            }

                            /* makeAssessedBlock */
                            function makeAssessedBlock(row) {
                                const frm = parseTm(row.from);
                                const to_ = parseTm(row.to);
                                if (frm === null || to_ === null) return null;

                                const df = Math.max(frm, HOURS_S);
                                const dt = Math.min(to_, HOURS_E);
                                if (df >= dt) return null;

                                const blk = document.createElement('div');
                                blk.style.cssText = [
                                    'position:absolute;top:4px;bottom:4px;',
                                    'left:' + pct(df) + ';',
                                    'width:' + ((dt - df) / TOTAL * 100).toFixed(4) + '%;',
                                    'background:#fff;color:#555;',
                                    'border:1px solid #bbb;',
                                    'border-radius:5px;',
                                    'display:flex;flex-direction:column;',
                                    'align-items:center;justify-content:center;',
                                    'overflow:hidden;padding:0 4px;cursor:pointer;',
                                ].join('');

                                const nameEl = document.createElement('span');
                                nameEl.style.cssText =
                                    'font-size:10px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;';
                                nameEl.textContent = row.client;

                                const saEl = document.createElement('span');
                                saEl.style.cssText =
                                    'font-size:9px;opacity:0.7;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;';
                                saEl.textContent = row.sa_number;

                                blk.appendChild(nameEl);
                                blk.appendChild(saEl);

                                const tipHtml = [
                                    '<strong>' + row.sa_number + ' &mdash; ' + row.client + '</strong>',
                                    '<hr style="margin:4px 0;border-color:#eee;">',
                                    '<span style="color:#c0392b;font-weight:600;">ASSESSED – Unassigned</span>',
                                    '<span style="color:#666;">Branch:</span> ' + row.branch,
                                    '<span style="color:#666;">Mobile:</span> ' + (row.mobile || '—'),
                                    '<span style="color:#666;">Email:</span> ' + (row.email || '—'),
                                    '<span style="color:#666;">Time:</span> ' + fmt12(row.from) + ' – ' + fmt12(row.to),
                                    '<span style="color:#666;">Payment:</span> ' + row.payment,
                                    '<span style="color:#666;">Termite:</span> ' + (row.termite ? 'Yes' : 'No') +
                                    '&nbsp;&nbsp;<span style="color:#666;">Package:</span> ' + (row.package ? 'Yes' : 'No'),
                                ].join('<br>');

                                blk.addEventListener('mouseenter', e => showTip(e, tipHtml));
                                blk.addEventListener('mousemove', moveTip);
                                blk.addEventListener('mouseleave', hideTip);
                                blk.addEventListener('click', () => {
                                    hideTip();
                                    window.location.href = ASSESSED_URL + '/' + row.svc_id;
                                });

                                return blk;
                            }

                            /* buildTimeline */
                            function buildTimeline() {
                                const container = document.getElementById('tlContainer');
                                if (!container) return;
                                container.innerHTML = '';

                                /* Hour axis */
                                const axisRow = document.createElement('div');
                                axisRow.style.cssText = 'display:flex;margin-bottom:6px;';

                                const axisLabel = document.createElement('div');
                                axisLabel.style.cssText = `flex:none;width:${LABEL_W}px;`;
                                axisRow.appendChild(axisLabel);

                                const axisTrack = document.createElement('div');
                                axisTrack.style.cssText = 'flex:1;position:relative;height:20px;';

                                for (let h = HOURS_S; h <= HOURS_E; h++) {
                                    const span = document.createElement('span');
                                    span.style.cssText = [
                                        'position:absolute;',
                                        'left:' + pct(h) + ';',
                                        'transform:translateX(-50%);',
                                        'font-size:10px;color:#999;white-space:nowrap;',
                                    ].join('');
                                    const h12 = h === 0 || h === 24 ? 12 : h > 12 ? h - 12 : h;
                                    const ampm = h < 12 ? 'am' : 'pm';
                                    span.textContent = h12 + ampm;
                                    axisTrack.appendChild(span);
                                }

                                axisRow.appendChild(axisTrack);
                                container.appendChild(axisRow);

                                /* UNASSIGNED / ASSESSED section */
                                if (ASSESSED_DATA.length > 0) {

                                    // Section header row
                                    const uHeader = document.createElement('div');
                                    uHeader.style.cssText = 'display:flex;align-items:center;margin-bottom:4px;';

                                    const uHeaderLabel = document.createElement('div');
                                    uHeaderLabel.style.cssText = [
                                        `flex:none;width:${LABEL_W}px;`,
                                        'font-size:11px;font-weight:700;color:#c0392b;',
                                        'padding-right:8px;text-align:right;',
                                    ].join('');
                                    uHeaderLabel.textContent = 'UNASSIGNED';

                                    const uHeaderLine = document.createElement('div');
                                    uHeaderLine.style.cssText = 'flex:1;height:1px;background:#e8b4b4;';

                                    uHeader.appendChild(uHeaderLabel);
                                    uHeader.appendChild(uHeaderLine);
                                    container.appendChild(uHeader);

                                    // Single track row — spacer replaces the removed "Assessed" label
                                    const uRowWrap = document.createElement('div');
                                    uRowWrap.style.cssText = 'display:flex;align-items:center;margin-bottom:8px;';

                                    const uSpacer = document.createElement('div');
                                    uSpacer.style.cssText = `flex:none;width:${LABEL_W}px;`;
                                    uRowWrap.appendChild(uSpacer);

                                    const uTrack = document.createElement('div');
                                    uTrack.style.cssText = [
                                        'flex:1;position:relative;',
                                        `height:${ROW_H}px;`,
                                        'background:#fafafa;',
                                        'border:0.5px dashed #ccc;',
                                        'border-radius:6px;overflow:visible;',
                                    ].join('');

                                    // Hour grid lines
                                    for (let h = HOURS_S; h <= HOURS_E; h++) {
                                        const line = document.createElement('div');
                                        line.style.cssText = [
                                            'position:absolute;top:0;bottom:0;',
                                            'left:' + pct(h) + ';',
                                            'width:0.5px;background:#e0e0e0;',
                                        ].join('');
                                        uTrack.appendChild(line);
                                    }

                                    // Assessed bars
                                    ASSESSED_DATA.forEach(row => {
                                        const blk = makeAssessedBlock(row);
                                        if (blk) uTrack.appendChild(blk);
                                    });

                                    uRowWrap.appendChild(uTrack);
                                    container.appendChild(uRowWrap);

                                    // Divider before scheduled rows
                                    const divider = document.createElement('div');
                                    divider.style.cssText = 'border-top:1px solid #e0e0e0;margin-bottom:8px;';
                                    container.appendChild(divider);
                                }

                                /* Scheduled rows (unchanged) */
                                if (!DATA.length) return;

                                const techs = [...new Set(DATA.map(r => r.tech))].sort();

                                techs.forEach(tech => {
                                    const rows = DATA.filter(r => r.tech === tech);

                                    const rowWrap = document.createElement('div');
                                    rowWrap.style.cssText = 'display:flex;align-items:center;margin-bottom:4px;';

                                    const label = document.createElement('div');
                                    label.style.cssText = [
                                        `flex:none;width:${LABEL_W}px;`,
                                        'font-size:11px;color:#555;padding-right:8px;',
                                        'text-align:right;overflow:hidden;',
                                        'text-overflow:ellipsis;white-space:nowrap;',
                                    ].join('');
                                    label.title = tech;
                                    label.textContent = tech;
                                    rowWrap.appendChild(label);

                                    const track = document.createElement('div');
                                    track.style.cssText = [
                                        'flex:1;position:relative;',
                                        `height:${ROW_H}px;`,
                                        'background:#f7f7f7;',
                                        'border:0.5px solid #ddd;',
                                        'border-radius:6px;overflow:visible;',
                                    ].join('');

                                    for (let h = HOURS_S; h <= HOURS_E; h++) {
                                        const line = document.createElement('div');
                                        line.style.cssText = [
                                            'position:absolute;top:0;bottom:0;',
                                            'left:' + pct(h) + ';',
                                            'width:0.5px;background:#e0e0e0;',
                                        ].join('');
                                        track.appendChild(line);
                                    }

                                    rows.forEach(row => {
                                        const blk = makeBlock(row);
                                        if (blk) track.appendChild(blk);
                                    });

                                    rowWrap.appendChild(track);
                                    container.appendChild(rowWrap);
                                });
                            }

                            document.addEventListener('DOMContentLoaded', buildTimeline);
                            if (document.readyState !== 'loading') buildTimeline();

                        })();
                    </script>

                </div>
            </div>
        </div>
    </section>
@endsection