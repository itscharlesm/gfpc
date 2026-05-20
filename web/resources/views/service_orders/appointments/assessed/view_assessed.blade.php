<style>
    @media print {
        body {
            zoom: 100%;
        }

        .no-print {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        .print-hide {
            display: none !important;
        }
    }

    .table-responsive {
        overflow: visible !important;
    }

    #sectionB {
        overflow: visible !important;
    }

    .content-wrapper {
        overflow-x: hidden;
    }

    #timelineTabs .nav-link {
        color: #28a745;
    }

    #timelineTabs .nav-link.active {
        color: #28a745;
        border-color: #28a745 #28a745 #fff;
    }

    #timelineTabs .nav-link:hover {
        color: #1e7e34;
        border-color: #1e7e34 #1e7e34 transparent;
    }
</style>

@extends('layouts.themes.main')

@section('content')
    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Assessed Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ action('App\Http\Controllers\AdminController@home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">Appointment</li>
                        <li class="breadcrumb-item">Assessed</li>
                        <li class="breadcrumb-item active">View</li>
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
                <div class="card-body overflow-auto">
                    <div class="text-center mb-2">
                        <h5 style="margin: 0; font-weight: bold;">GO FORWARD PEST CONTROL</h5>
                        <p style="margin: 0;">{{ $display->branch_name }}</p>
                    </div>

                    <div class="mb-4 position-relative text-center">
                        <strong style="color: red;">SERVICE ORDER</strong>

                        <strong style="position: absolute; right: 0; top: 0;">
                            SA-{{ str_pad($display->svc_sa_number, 6, '0', STR_PAD_LEFT) }}
                        </strong>
                    </div>
                    <div class="row">

                        {{-- Appointment Information Display --}}
                        <div class="table-responsive overflow-auto" id="sectionA">
                            <table class="table table-bordered text-left align-middle">
                                <tbody>
                                    <tr>
                                        <th colspan="6" class="text-center table-light">CLIENT INFORMATION</th>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">CLIENT NAME</td>
                                        <td>{{ $display->usr_first_name }} {{ $display->usr_last_name }}</td>
                                        <td style="font-weight: bold;">EMAIL</td>
                                        <td>{{ $display->usr_email }}</td>
                                        <td style="font-weight: bold;">MOBILE</td>
                                        <td>{{ $display->usr_mobile }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">ADDRESS</td>
                                        <td colspan="3">
                                            {{ implode(
                                                ', ',
                                                array_filter([
                                                    $display->uadd_street,
                                                    $display->uadd_barangay,
                                                    $display->uadd_city,
                                                    $display->uadd_province,
                                                    $display->uadd_region,
                                                ]),
                                            ) }}
                                        </td>
                                        <td style="font-weight: bold;">ADDRESS TYPE</td>
                                        <td>{{ $display->add_name }}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="6" class="text-center table-light">SERVICE INFORMATION</th>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">BRANCH</td>
                                        <td>{{ $display->branch_name }}</td>
                                        <td style="font-weight: bold;">STATUS</td>
                                        <td>{{ $display->svc_status }}</td>
                                        <td style="font-weight: bold;">PAYMENT STATUS</td>
                                        <td>{{ $display->svc_payment_status }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">PROPERTY TYPE</td>
                                        <td>{{ $display->svc_property_type }}</td>
                                        <td style="font-weight: bold;">INFESTATION</td>
                                        <td>{{ $display->svc_infestation }}</td>
                                        <td style="font-weight: bold;">DISTANCE</td>
                                        <td>{{ $display->svc_km_distance }} KM</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">IS TERMITE</td>
                                        <td>{{ $display->svc_is_termite ? 'YES' : 'NO' }}</td>
                                        <td style="font-weight: bold;">IS PACKAGE</td>
                                        <td colspan="3">{{ $display->svc_is_package ? 'YES' : 'NO' }}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="6" class="text-center table-light">PRICING</th>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">INITIAL PRICE</td>
                                        <td>₱{{ number_format($display->svc_initial_price, 2) }}</td>
                                        <td style="font-weight: bold;">LOCATION PRICE</td>
                                        <td>₱{{ number_format($display->svc_location_price, 2) }}</td>
                                        <td style="font-weight: bold;">FIXED PRICE</td>
                                        <td>₱{{ number_format($display->svc_fixed_price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">FINAL PRICE</td>
                                        <td>₱{{ number_format($display->svc_final_price, 2) }}</td>
                                        <td style="font-weight: bold;">BALANCE</td>
                                        <td colspan="3">₱{{ number_format($display->svc_balance, 2) }}</td>
                                    </tr>

                                    @if ($display->svc_is_termite)
                                        <tr>
                                            <th colspan="6" class="text-center table-light">TERMITE CASE</th>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">TREATMENT TYPE</td>
                                            <td>{{ $display->svc_type_treatment }}</td>
                                            <td style="font-weight: bold;">INITIAL SQM</td>
                                            <td>{{ $display->svc_sqm_initial }}</td>
                                            <td style="font-weight: bold;">FINAL SQM</td>
                                            <td>{{ $display->svc_sqm_final }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">WITH DEVICE</td>
                                            <td>{{ $display->svc_with_device ? 'YES' : 'NO' }}</td>
                                            <td style="font-weight: bold;">DEVICE COUNT</td>
                                            <td colspan="3">{{ $display->svc_device_count ?? 'N/A' }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <th colspan="6" class="text-center table-light">CHEMICAL</th>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">CHEMICAL QUANTITY</td>
                                        <td>{{ $display->svc_chemical_quantity }}</td>
                                        <td style="font-weight: bold;">CHEMICAL METRIC</td>
                                        <td colspan="3">{{ $display->svc_chemical_metric }}</td>
                                    </tr>

                                    <tr>
                                        <th colspan="6" class="text-center table-light">SCHEDULE</th>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">APPROVED BY</td>
                                        <td>{{ $display->approved_first_name }}
                                            {{ $display->approved_last_name }}</td>
                                        <td style="font-weight: bold;">CLIENT DATE</td>
                                        <td>{{ \Carbon\Carbon::parse($display->svca_client_date)->format('m/d/Y') }}</td>
                                        <td style="font-weight: bold;">CLIENT TIME</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($display->svca_client_time)->format('h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">DATE APPROVED</td>
                                        <td>{{ \Carbon\Carbon::parse($display->svca_date_approved)->format('m/d/Y') }}</td>
                                        <td style="font-weight: bold;">TIME FROM</td>
                                        <td>{{ \Carbon\Carbon::parse($display->svca_approved_time_from)->format('h:i A') }}
                                        </td>
                                        <td style="font-weight: bold;">TIME TO</td>
                                        <td>{{ \Carbon\Carbon::parse($display->svca_approved_time_to)->format('h:i A') }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="6" class="text-center table-light">PROBLEM DESCRIPTION</th>
                                    </tr>
                                    <tr>
                                        <td colspan="6" style="text-align: justify">
                                            {{ $display->svc_problem_description }}
                                        </td>
                                    </tr>

                                    @if (!empty($display->svc_assessment_recommendation))
                                        <tr>
                                            <th colspan="6" class="text-center table-light">ASSESSMENT RECOMMENDATION
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="text-align: justify">
                                                {{ $display->svc_assessment_recommendation }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end flex-wrap mb-3 no-print">
                            <button type="button" class="btn btn-success mr-2 mb-2" data-toggle="modal"
                                data-target="#assignTechnicianModal">
                                <span class="fa fa-user-plus"></span> Assign Technician
                            </button>
                            <button type="button" class="btn btn-primary mr-2 mb-2" onclick="printDefault()">
                                <span class="fa fa-print"></span> Print
                            </button>
                            <div class="btn-group mr-2 mb-2">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="fa fa-print"></span> Print Options
                                </button>
                                <div class="dropdown-menu p-3" style="min-width: 200px;">
                                    <label class="dropdown-item">
                                        <input type="checkbox" class="print-toggle mr-1" data-target="sectionA">
                                        Appointment Information
                                    </label>
                                    <label class="dropdown-item">
                                        <input type="checkbox" class="print-toggle mr-1" data-target="sectionB"> Service
                                        Orders
                                    </label>
                                    @if ($appointmentImages->count() > 0)
                                        <label class="dropdown-item">
                                            <input type="checkbox" class="print-toggle mr-1" data-target="sectionC">
                                            Client Appointment Images
                                        </label>
                                    @endif
                                    <div class="dropdown-item text-center">
                                        <button class="btn btn-primary btn-sm mt-2" onclick="handlePrint()">Confirm &
                                            Print</button>
                                    </div>
                                    <div class="dropdown-item">
                                        <p id="warning" style="color:red; display:none;" class="mt-1">Select at least
                                            one section.</p>
                                    </div>
                                </div>
                            </div>

                            <a class="btn btn-danger mr-2 mb-2" href="">
                                <span class="fa fa-user-shield"></span> Override
                            </a>
                        </div>

                        {{-- Service Order Display --}}
                        <div class="table-responsive" id="sectionB">
                            <hr class="no-print">
                            @if ($display->svc_is_termite == 0)
                                {{-- NON-TERMITE: Two columns - Pest Type & Service Order --}}
                                <div class="row">
                                    {{-- PEST TYPE TABLE --}}
                                    <div class="col-md-6">
                                        <table class="table table-bordered text-center mb-2">
                                            <thead>
                                                <tr style="background-color: #f5f5f5;">
                                                    <th colspan="3"><strong>PEST TYPE</strong></th>
                                                </tr>
                                                <tr style="background-color: #f5f5f5;">
                                                    <th style="width: 50px;">No.</th>
                                                    <th>Pest</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pestTypes as $index => $type)
                                                    <tr>
                                                        <td style="vertical-align: middle; text-align: center">
                                                            {{ $index + 1 }}</td>
                                                        <td style="vertical-align: middle; text-align: center">
                                                            {{ $type->svcp_pest_type }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- SERVICE ORDER TABLE (non-termite areas) --}}
                                    <div class="col-md-6">
                                        <table class="table table-bordered text-center mb-2">
                                            <thead>
                                                <tr style="background-color: #f5f5f5;">
                                                    <th colspan="4"><strong>SERVICE ORDER</strong></th>
                                                </tr>
                                                <tr style="background-color: #f5f5f5;">
                                                    <th style="width: 50px;">No.</th>
                                                    <th>Service</th>
                                                    <th>Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($serviceAreas as $index => $area)
                                                    <tr>
                                                        <td style="vertical-align: middle; text-align: center">
                                                            {{ $index + 1 }}</td>
                                                        <td style="vertical-align: middle; text-align: center">
                                                            {{ $area->svcpa_area }}</td>
                                                        <td style="vertical-align: middle; text-align: center">
                                                            ₱{{ number_format($area->svcpa_cost, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @elseif ($display->svc_is_termite == 1)
                                {{-- TERMITE: Full width - Termite Details --}}
                                <div class="col-md-12">
                                    <table class="table table-bordered text-center mb-2">
                                        <thead>
                                            <tr style="background-color: #f5f5f5;">
                                                <th colspan="5"><strong>TERMITE DETAILS</strong></th>
                                            </tr>
                                            <tr style="background-color: #f5f5f5;">
                                                <th style="width: 50px;">No.</th>
                                                <th>SQM Details</th>
                                                <th>Cost</th>
                                                <th>Computation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($termiteAreas as $index => $area)
                                                <tr>
                                                    <td style="vertical-align: middle; text-align: center">
                                                        {{ $index + 1 }}</td>
                                                    <td style="vertical-align: middle; text-align: center">
                                                        {{ $area->svcpat_sqm_details }}</td>
                                                    <td style="vertical-align: middle; text-align: center">
                                                        ₱{{ number_format($area->svcpat_cost, 2) }}</td>
                                                    <td style="vertical-align: middle; text-align: center">
                                                        @if ($display->svc_type_treatment == 'STANDARD TREATMENT')
                                                            <p>STANDARD TREATMENT: sqm × cost</p>
                                                        @elseif ($display->svc_type_treatment == 'HYBRID TREATMENT')
                                                            <p>HYBRID TREATMENT: sqm × cost + device</p>
                                                        @else
                                                            <p>{{ $display->svc_type_treatment }}: sqm × cost</p>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No termite areas
                                                        added.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- Client Appointment Images --}}
                        @if ($appointmentImages->count() > 0)
                            <div class="table-responsive" id="sectionC">
                                <table class="table table-bordered text-center mb-2">
                                    <thead>
                                        <tr style="background-color: #f5f5f5;">
                                            <th colspan="5">
                                                <strong>CLIENT APPOINTMENT IMAGES</strong>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($appointmentImages->chunk(5) as $chunk)
                                            <tr>
                                                @foreach ($chunk as $img)
                                                    <td style="width:20%; padding:10px; vertical-align:middle;">
                                                        <a href="{{ asset('images/client_images/' . $img->svcap_image) }}"
                                                            target="_blank"
                                                            style="display:block; width:100%; aspect-ratio:1/1; overflow:hidden;">
                                                            <img src="{{ asset('images/client_images/' . $img->svcap_image) }}"
                                                                alt="Appointment Image"
                                                                style="width:100%; height:100%; object-fit:cover; display:block;">
                                                        </a>
                                                    </td>
                                                @endforeach
                                                @for ($i = $chunk->count(); $i < 5; $i++)
                                                    <td style="vertical-align: middle;"></td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Assign Technician Modal --}}
    <div class="modal fade" id="assignTechnicianModal" tabindex="-1" role="dialog"
        aria-labelledby="assignTechnicianModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form action="{{ url('management/service-orders/assign-technician') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white" id="assignTechnicianModalLabel">
                            <span class="fa fa-user text-white"></span> Assign Technician
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="svc_id" value="{{ $display->svc_id }}">

                        <div class="form-group mb-3">
                            <label>Assign Technician <span class="text-danger">*</span></label>
                            <select class="form-control" name="svcas_assigned_to" id="technicianSelect" required>
                                <option value="" disabled selected>Select Technician</option>
                                @foreach ($technicians as $tech)
                                    @php
                                        $label = $tech->usr_last_name . ', ' . $tech->usr_first_name;
                                        $disabled = $tech->is_rest_day || $tech->is_busy;
                                        $suffix = $tech->is_rest_day
                                            ? ' — (Rest Day)'
                                            : ($tech->is_busy
                                                ? ' — (Not Available)'
                                                : '');
                                    @endphp
                                    <option value="{{ $tech->usr_id }}" data-rest="{{ $tech->is_rest_day ? 1 : 0 }}"
                                        data-busy="{{ $tech->is_busy ? 1 : 0 }}"
                                        @if ($disabled) disabled @endif>
                                        {{ $label }}{{ $suffix }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <hr>

                        {{-- Tab Navigation --}}
                        <ul class="nav nav-tabs" id="timelineTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="selected-tech-tab" data-toggle="tab"
                                    href="#selectedTechPane" role="tab" aria-controls="selectedTechPane"
                                    aria-selected="true">
                                    <span class="fa fa-user mr-1"></span> Selected Technician
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="all-tech-tab" data-toggle="tab" href="#allTechPane"
                                    role="tab" aria-controls="allTechPane" aria-selected="false">
                                    <span class="fa fa-users mr-1"></span> All Technicians
                                </a>
                            </li>
                        </ul>

                        {{-- Tab Content --}}
                        <div class="tab-content pt-3" id="timelineTabContent">

                            {{-- Tab 1: Selected Technician --}}
                            <div class="tab-pane fade show active" id="selectedTechPane" role="tabpanel"
                                aria-labelledby="selected-tech-tab">
                                <p id="noTechSelected" class="text-muted" style="font-size:13px;">
                                    Select a technician above to view their schedule.
                                </p>
                                <div id="techTimelineWrap" style="display:none;">
                                    <p class="mb-2" style="font-size:13px; color:#666;">
                                        Schedule for
                                        <strong>{{ \Carbon\Carbon::parse($approvedDate)->format('F d, Y') }}</strong>
                                    </p>
                                    <div class="d-flex mb-2" style="gap:12px; font-size:11px; color:#666;">
                                        <span style="display:inline-flex;align-items:center;gap:4px;">
                                            <span
                                                style="width:12px;height:12px;border-radius:3px;background:#B5D4F4;border:0.5px solid #85B7EB;display:inline-block;"></span>
                                            Existing appointment
                                        </span>
                                        <span style="display:inline-flex;align-items:center;gap:4px;">
                                            <span
                                                style="width:12px;height:12px;border-radius:3px;background:#C0DD97;border:0.5px solid #97C459;display:inline-block;"></span>
                                            This appointment
                                        </span>
                                    </div>
                                    <div style="overflow-x:auto;">
                                        <div id="techTimeline" style="min-width:600px; padding-bottom:8px;"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: All Technicians --}}
                            <div class="tab-pane fade" id="allTechPane" role="tabpanel" aria-labelledby="all-tech-tab">
                                <p class="mb-1" style="font-size:13px; font-weight:600; color:#333;">
                                    All Technicians — {{ \Carbon\Carbon::parse($approvedDate)->format('F d, Y') }}
                                </p>
                                <p class="mb-2" style="font-size:12px; color:#666;">
                                    Overview of the full team's schedule for this day.
                                </p>
                                <div class="d-flex mb-2" style="gap:12px; font-size:11px; color:#666;">
                                    <span style="display:inline-flex;align-items:center;gap:4px;">
                                        <span
                                            style="width:12px;height:12px;border-radius:3px;background:#B5D4F4;border:0.5px solid #85B7EB;display:inline-block;"></span>
                                        Existing appointment
                                    </span>
                                    <span style="display:inline-flex;align-items:center;gap:4px;">
                                        <span
                                            style="width:12px;height:12px;border-radius:3px;background:#C0DD97;border:0.5px solid #97C459;display:inline-block;"></span>
                                        This appointment
                                    </span>
                                </div>
                                <div style="overflow-x:auto;">
                                    <div id="allTechTimeline" style="min-width:600px; padding-bottom:8px;"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <span class="fa fa-times"></span> Close
                        </button>
                        <button type="submit" class="btn btn-success">
                            <span class="fa fa-save"></span> Assign
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function printDefault() {
            window.print();
        }

        function handlePrint() {
            console.log("Print button clicked!");

            const checkboxes = document.querySelectorAll('.print-toggle');
            const warning = document.getElementById('warning');
            let anyChecked = false;

            const sectionIds = ['sectionA', 'sectionB', 'sectionC'];

            sectionIds.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                if (section) {
                    section.classList.remove('print-hide');
                }
            });

            checkboxes.forEach(checkbox => {
                const targetId = checkbox.dataset.target;
                const targetDiv = document.getElementById(targetId);

                if (targetDiv) {
                    if (checkbox.checked) {
                        anyChecked = true;
                        targetDiv.classList.remove('print-hide');
                    } else {
                        targetDiv.classList.add('print-hide');
                    }
                } else {
                    console.error(`Element with ID ${targetId} not found!`);
                }
            });

            if (!anyChecked) {
                warning.style.display = 'block';
                return;
            }

            warning.style.display = 'none';

            setTimeout(() => {
                console.log("Triggering print dialog...");
                window.print();
            }, 500);

            setTimeout(() => {
                sectionIds.forEach(sectionId => {
                    const section = document.getElementById(sectionId);
                    if (section) {
                        section.classList.remove('print-hide');
                    }
                });
            }, 1000);
        }
    </script>

    <script>
        (function() {
            const HOURS_START = 0;
            const HOURS_END = 24;
            const TOTAL_HRS = HOURS_END - HOURS_START;
            const ROW_HEIGHT = 36;
            const LABEL_W = 52;

            const NEW_FROM_STR = "{{ $approvedTimeFrom }}";
            const NEW_TO_STR = "{{ $approvedTimeTo }}";
            const DAY_SCHEDULES = @json($daySchedules);

            function parseTime(str) {
                if (!str) return null;
                const parts = str.split(':');
                return parseInt(parts[0]) + parseInt(parts[1]) / 60;
            }

            const NEW_FROM = parseTime(NEW_FROM_STR);
            const NEW_TO = parseTime(NEW_TO_STR);

            // ── Only change: toggle placeholder + wrap together ──
            document.getElementById('technicianSelect').addEventListener('change', function() {
                const techId = this.value;
                const wrap = document.getElementById('techTimelineWrap');
                const noTechMsg = document.getElementById('noTechSelected');
                if (!techId) {
                    wrap.style.display = 'none';
                    noTechMsg.style.display = 'block';
                    return;
                }
                wrap.style.display = 'block';
                noTechMsg.style.display = 'none';
                renderTimeline(techId);
            });

            function pct(h) {
                return ((h - HOURS_START) / TOTAL_HRS * 100).toFixed(4) + '%';
            }

            function renderTimeline(techId) {
                const container = document.getElementById('techTimeline');
                container.innerHTML = '';

                const axis = document.createElement('div');
                axis.style.cssText = `display:flex; margin-left:0; position:relative; height:20px; margin-bottom:4px;`;

                for (let h = HOURS_START; h <= HOURS_END; h++) {
                    const span = document.createElement('span');
                    const leftPct = ((h - HOURS_START) / TOTAL_HRS * 100).toFixed(4) + '%';
                    span.style.cssText =
                        `position:absolute; left:${leftPct}; transform:translateX(-50%); font-size:10px; color:#999; white-space:nowrap;`;
                    const h12 = h === 0 || h === 24 ? 12 : h > 12 ? h - 12 : h;
                    const ampm = h === 0 || h === 24 ? 'am' : h < 12 ? 'am' : h === 12 ? 'pm' : 'pm';
                    span.textContent = h12 + ampm;
                    axis.appendChild(span);
                }
                container.appendChild(axis);

                const rowWrap = document.createElement('div');
                rowWrap.style.cssText = `display:flex; align-items:center; margin-bottom:2px;`;

                const track = document.createElement('div');
                track.style.cssText =
                    `flex:1; position:relative; height:${ROW_HEIGHT}px; background:#f7f7f7; border:0.5px solid #ddd; border-radius:6px; overflow:visible;`;

                for (let h = HOURS_START; h <= HOURS_END; h++) {
                    const line = document.createElement('div');
                    line.style.cssText =
                        `position:absolute; top:0; bottom:0; left:${pct(h)}; width:0.5px; background:#e0e0e0;`;
                    track.appendChild(line);
                }

                const existingSlots = DAY_SCHEDULES[techId] || [];
                existingSlots.forEach(ev => {
                    const evFrom = parseTime(ev.svca_approved_time_from);
                    const evTo = parseTime(ev.svca_approved_time_to);
                    if (evFrom === null || evTo === null) return;
                    const dispFrom = Math.max(evFrom, HOURS_START);
                    const dispTo = Math.min(evTo, HOURS_END);
                    if (dispFrom >= dispTo) return;

                    const contactParts = ['Email: ' + ev.usr_email, 'Number: 0' + ev.usr_mobile].filter(p =>
                        p && p.trim());
                    const addressParts = ['Address: ' + ev.uadd_street, ev.uadd_barangay, ev.uadd_city, ev
                        .uadd_province, ev
                        .uadd_region
                    ].filter(p => p && p.trim());
                    const distanceLine = ev.svc_km_distance ? 'Distance: ' + ev.svc_km_distance +
                        'KM from office' : null;
                    let dateAssignedLine = null;
                    if (ev.svcas_date_assigned) {
                        const d = new Date(ev.svcas_date_assigned);
                        const mm = String(d.getMonth() + 1).padStart(2, '0');
                        const dd = String(d.getDate()).padStart(2, '0');
                        const yyyy = d.getFullYear();
                        dateAssignedLine = 'Date Assigned: ' + mm + '/' + dd + '/' + yyyy;
                    }

                    const assignedByLine = (ev.assigner_first_name && ev.assigner_last_name) ?
                        'Assigned By: ' + ev.assigner_first_name + ' ' + ev.assigner_last_name :
                        null;

                    const addr = [
                        ...contactParts,
                        addressParts.join(', '),
                        distanceLine,
                        dateAssignedLine,
                        assignedByLine
                    ].filter(Boolean).join('<br>');

                    const blk = makeBlock(dispFrom, dispTo,
                        ev.usr_first_name + ' ' + ev.usr_last_name, addr,
                        '#B5D4F4', '#0C447C', '#85B7EB');
                    track.appendChild(blk);
                });

                if (NEW_FROM !== null && NEW_TO !== null) {
                    const dispFrom = Math.max(NEW_FROM, HOURS_START);
                    const dispTo = Math.min(NEW_TO, HOURS_END);
                    if (dispFrom < dispTo) {
                        const blk = makeBlock(dispFrom, dispTo,
                            'This appointment', '',
                            '#C0DD97', '#27500A', '#97C459');
                        track.appendChild(blk);
                    }
                }

                rowWrap.appendChild(track);
                container.appendChild(rowWrap);
            }

            function makeBlock(from, to, label, addr, bg, color, border) {
                const blk = document.createElement('div');
                const leftPct = ((from - HOURS_START) / TOTAL_HRS * 100).toFixed(4) + '%';
                const widthPct = ((to - from) / TOTAL_HRS * 100).toFixed(4) + '%';
                blk.style.cssText = [
                    'position:absolute; top:4px; bottom:4px;',
                    'left:' + leftPct + '; width:' + widthPct + ';',
                    'background:' + bg + '; color:' + color + '; border:0.5px solid ' + border + ';',
                    'border-radius:5px; display:flex; align-items:center; justify-content:center;',
                    'font-size:11px; font-weight:500; overflow:hidden; white-space:nowrap;',
                    'text-overflow:ellipsis; padding:0 6px; cursor:default;'
                ].join('');
                blk.textContent = label;
                blk.addEventListener('mouseenter', e => showTooltip(e, label, addr));
                blk.addEventListener('mousemove', moveTooltip);
                blk.addEventListener('mouseleave', hideTooltip);
                return blk;
            }

            let tt = null;

            function ensureTT() {
                if (!tt) {
                    tt = document.createElement('div');
                    tt.style.cssText = [
                        'position:fixed; background:#fff; border:0.5px solid #ccc;',
                        'border-radius:8px; padding:8px 12px; font-size:12px;',
                        'pointer-events:none; z-index:9999; display:none;',
                        'box-shadow:0 4px 12px rgba(0,0,0,.08); max-width:220px; line-height:1.5;'
                    ].join('');
                    document.body.appendChild(tt);
                }
            }

            function showTooltip(e, label, addr) {
                ensureTT();
                tt.innerHTML = '<strong>' + label + '</strong>' + (addr ? '<br>' + addr : '');
                tt.style.display = 'block';
                moveTooltip(e);
            }

            function moveTooltip(e) {
                if (tt) {
                    tt.style.left = (e.clientX + 14) + 'px';
                    tt.style.top = (e.clientY + 14) + 'px';
                }
            }

            function hideTooltip() {
                if (tt) tt.style.display = 'none';
            }
        })();
    </script>

    <script>
        (function() {
            const HOURS_START = 0;
            const HOURS_END = 24;
            const TOTAL_HRS = HOURS_END - HOURS_START;
            const ROW_HEIGHT = 36;
            const LABEL_W = 110;

            const NEW_FROM_STR = "{{ $approvedTimeFrom }}";
            const NEW_TO_STR = "{{ $approvedTimeTo }}";
            const DAY_SCHEDULES = @json($daySchedules);
            const ALL_TECHS = @json($allTechs);

            function parseTime(str) {
                if (!str) return null;
                const p = str.split(':');
                return parseInt(p[0]) + parseInt(p[1]) / 60;
            }

            const NEW_FROM = parseTime(NEW_FROM_STR);
            const NEW_TO = parseTime(NEW_TO_STR);

            function pct(h) {
                return ((h - HOURS_START) / TOTAL_HRS * 100).toFixed(4) + '%';
            }

            function buildAllTechTimeline() {
                const container = document.getElementById('allTechTimeline');
                if (!container) return;
                container.innerHTML = '';

                const axis = document.createElement('div');
                axis.style.cssText =
                    `display:flex; position:relative; height:20px; margin-bottom:4px; margin-left:${LABEL_W}px;`;
                for (let h = HOURS_START; h <= HOURS_END; h++) {
                    const span = document.createElement('span');
                    const leftPct = ((h - HOURS_START) / TOTAL_HRS * 100).toFixed(4) + '%';
                    span.style.cssText =
                        `position:absolute; left:${leftPct}; transform:translateX(-50%); font-size:10px; color:#999; white-space:nowrap;`;
                    const h12 = h === 0 || h === 24 ? 12 : h > 12 ? h - 12 : h;
                    const ampm = h < 12 || h === 0 ? 'am' : 'pm';
                    span.textContent = h12 + ampm;
                    axis.appendChild(span);
                }
                container.appendChild(axis);

                ALL_TECHS.forEach(tech => {
                    const rowWrap = document.createElement('div');
                    rowWrap.style.cssText = `display:flex; align-items:center; margin-bottom:3px;`;

                    const labelDiv = document.createElement('div');
                    const restBusy = tech.is_rest ?
                        ' <span style="color:#c00;font-size:9px;">(rest)</span>' :
                        tech.is_busy ?
                        ' <span style="color:#a06000;font-size:9px;">(busy)</span>' :
                        '';
                    labelDiv.style.cssText =
                        `flex:none; width:${LABEL_W}px; font-size:11px; color:#555; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; padding-right:6px;`;
                    labelDiv.innerHTML = tech.label + restBusy;
                    rowWrap.appendChild(labelDiv);

                    const track = document.createElement('div');
                    track.style.cssText =
                        `flex:1; position:relative; height:${ROW_HEIGHT}px; background:#f7f7f7; border:0.5px solid #ddd; border-radius:6px; overflow:visible;`;

                    for (let h = HOURS_START; h <= HOURS_END; h++) {
                        const line = document.createElement('div');
                        line.style.cssText =
                            `position:absolute; top:0; bottom:0; left:${pct(h)}; width:0.5px; background:#e0e0e0;`;
                        track.appendChild(line);
                    }

                    const slots = DAY_SCHEDULES[tech.id] || [];
                    slots.forEach(ev => {
                        const evFrom = parseTime(ev.svca_approved_time_from);
                        const evTo = parseTime(ev.svca_approved_time_to);
                        if (evFrom === null || evTo === null) return;
                        const dF = Math.max(evFrom, HOURS_START);
                        const dT = Math.min(evTo, HOURS_END);
                        if (dF >= dT) return;

                        const contactParts = [
                            'Email: ' + ev.usr_email,
                            'Number: 0' + ev.usr_mobile
                        ].filter(p => p && p.trim());

                        const addrParts = [
                            'Address: ' + ev.uadd_street,
                            ev.uadd_barangay,
                            ev.uadd_city,
                            ev.uadd_province,
                            ev.uadd_region
                        ].filter(p => p && p.trim());

                        const addressLine = addrParts.join(', ');

                        const distLine = ev.svc_km_distance ?
                            'Distance: ' + ev.svc_km_distance + 'KM from office' :
                            null;

                        let dateAssignedLine = null;
                        if (ev.svcas_date_assigned) {
                            const d = new Date(ev.svcas_date_assigned);
                            const mm = String(d.getMonth() + 1).padStart(2, '0');
                            const dd = String(d.getDate()).padStart(2, '0');
                            const yyyy = d.getFullYear();
                            dateAssignedLine = 'Date Assigned: ' + mm + '/' + dd + '/' + yyyy;
                        }

                        const assignedByLine = (ev.assigner_first_name && ev.assigner_last_name) ?
                            'Assigned By: ' + ev.assigner_first_name + ' ' + ev.assigner_last_name :
                            null;

                        const addr = [
                            ...contactParts,
                            addressLine,
                            distLine,
                            dateAssignedLine,
                            assignedByLine
                        ].filter(Boolean).join('<br>');

                        track.appendChild(makeBlock(
                            dF,
                            dT,
                            ev.usr_first_name + ' ' + ev.usr_last_name,
                            addr,
                            '#B5D4F4',
                            '#0C447C',
                            '#85B7EB'
                        ));
                    });

                    if (NEW_FROM !== null && NEW_TO !== null) {
                        const dF = Math.max(NEW_FROM, HOURS_START);
                        const dT = Math.min(NEW_TO, HOURS_END);
                        if (dF < dT) {
                            track.appendChild(makeBlock(dF, dT,
                                'This appointment', '',
                                '#C0DD97', '#27500A', '#97C459'));
                        }
                    }

                    rowWrap.appendChild(track);
                    container.appendChild(rowWrap);
                });
            }

            function makeBlock(from, to, label, addr, bg, color, border) {
                const blk = document.createElement('div');
                const leftPct = ((from - HOURS_START) / TOTAL_HRS * 100).toFixed(4) + '%';
                const widthPct = ((to - from) / TOTAL_HRS * 100).toFixed(4) + '%';
                blk.style.cssText = [
                    'position:absolute; top:4px; bottom:4px;',
                    'left:' + leftPct + '; width:' + widthPct + ';',
                    'background:' + bg + '; color:' + color + '; border:0.5px solid ' + border + ';',
                    'border-radius:5px; display:flex; align-items:center; justify-content:center;',
                    'font-size:11px; font-weight:500; overflow:hidden; white-space:nowrap;',
                    'text-overflow:ellipsis; padding:0 6px; cursor:default;'
                ].join('');
                blk.textContent = label;
                blk.addEventListener('mouseenter', e => showTooltip(e, label, addr));
                blk.addEventListener('mousemove', moveTooltip);
                blk.addEventListener('mouseleave', hideTooltip);
                return blk;
            }

            let tt2 = null;

            function ensureTT() {
                if (!tt2) {
                    tt2 = document.createElement('div');
                    tt2.style.cssText = [
                        'position:fixed; background:#fff; border:0.5px solid #ccc;',
                        'border-radius:8px; padding:8px 12px; font-size:12px;',
                        'pointer-events:none; z-index:9999; display:none;',
                        'box-shadow:0 4px 12px rgba(0,0,0,.08); max-width:220px; line-height:1.5;'
                    ].join('');
                    document.body.appendChild(tt2);
                }
            }

            function showTooltip(e, label, addr) {
                ensureTT();
                tt2.innerHTML = '<strong>' + label + '</strong>' + (addr ? '<br>' + addr : '');
                tt2.style.display = 'block';
                moveTooltip(e);
            }

            function moveTooltip(e) {
                if (tt2) {
                    tt2.style.left = (e.clientX + 14) + 'px';
                    tt2.style.top = (e.clientY + 14) + 'px';
                }
            }

            function hideTooltip() {
                if (tt2) tt2.style.display = 'none';
            }

            document.addEventListener('DOMContentLoaded', buildAllTechTimeline);

            const modal = document.getElementById('assignTechnicianModal');
            if (modal) {
                modal.addEventListener('shown.bs.modal', buildAllTechTimeline);
                if (typeof $ !== 'undefined') {
                    $('#assignTechnicianModal').on('shown.bs.modal', buildAllTechTimeline);
                    // Re-render when switching to the All Technicians tab
                    $('a[href="#allTechPane"]').on('shown.bs.tab', buildAllTechTimeline);
                }
            }
        })();
    </script>
@endsection