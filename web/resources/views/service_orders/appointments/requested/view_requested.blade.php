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
</style>

@extends('layouts.themes.main')

@section('content')
    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Requested Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ action('App\Http\Controllers\AdminController@home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">Appointment</li>
                        <li class="breadcrumb-item">Requested</li>
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
                                        <td style="font-weight: bold;">INITIAL PRICE</td>
                                        <td>₱{{ number_format($display->svc_initial_price, 2) }}</td>
                                        <td style="font-weight: bold;">LOCATION PRICE</td>
                                        <td>₱{{ number_format($display->svc_location_price, 2) }}</td>
                                        <td style="font-weight: bold;">BALANCE</td>
                                        <td>₱{{ number_format($display->svc_balance, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">IS TERMITE</td>
                                        <td>{{ $display->svc_is_termite ? 'YES' : 'NO' }}</td>
                                        <td style="font-weight: bold;">DISTANCE</td>
                                        <td>{{ $display->svc_km_distance }}KM</td>
                                        <td style="font-weight: bold;">IS PACKAGE</td>
                                        <td>{{ $display->svc_is_package ? 'YES' : 'NO' }}</td>
                                    </tr>
                                    @if ($display->svc_is_termite)
                                        <tr>
                                            <th colspan="6" class="text-center table-light">TERMITE CASE</th>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">INITIAL SQM</td>
                                            <td>{{ $display->svc_sqm_initial }}</td>
                                            <td style="font-weight: bold;">WITH DEVICE</td>
                                            <td>{{ $display->svc_with_device ? 'YES' : 'NO' }}</td>
                                            <td style="font-weight: bold;">DEVICE COUNT</td>
                                            <td>{{ $display->svc_device_count ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">TREATMENT TYPE</td>
                                            <td colspan="5">{{ $display->svc_type_treatment }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th colspan="6" class="text-center table-light">SCHEDULE</th>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">CLIENT DATE REQUESTED</td>
                                        <td colspan="2">
                                            {{ \Carbon\Carbon::parse($display->svca_client_date)->format('m/d/Y') }}</td>
                                        <td style="font-weight: bold;">CLIENT TIME REQUESTED</td>
                                        <td colspan="2">
                                            {{ \Carbon\Carbon::parse($display->svca_client_time)->format('h:i A') }}
                                        </td>
                                    </tr>
                                    @if (!empty($display->svc_problem_description))
                                        <tr>
                                            <th colspan="6" class="text-center table-light">PROBLEM DESCRIPTION</th>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="text-align: justify">
                                                {{ $display->svc_problem_description }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end flex-wrap mb-3 no-print">
                            @if ($display->svc_status == 'REQUESTED')
                                <button type="button" class="btn btn-warning mr-2 mb-2" data-toggle="modal"
                                    data-target="#assessAppointmentModal">
                                    <span class="fa fa-pen"></span> Assess Appointment
                                </button>
                            @endif

                            @if ($display->svc_status == 'CONFIRM ASSESSMENT')
                                <button type="button" class="btn btn-success mr-2 mb-2" data-toggle="modal"
                                    data-target="#confirmAssessmentAppointmentModal">
                                    <span class="fa fa-check"></span> Confirm Appointment
                                </button>
                            @endif

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
                                        <input type="checkbox" class="print-toggle mr-1" data-target="sectionA"> Appointment
                                        Information
                                    </label>
                                    <label class="dropdown-item">
                                        <input type="checkbox" class="print-toggle mr-1" data-target="sectionB"> Service
                                        Orders
                                    </label>
                                    @if ($appointmentImages->count() > 0)
                                        <label class="dropdown-item">
                                            <input type="checkbox" class="print-toggle mr-1" data-target="sectionC"> Client
                                            Appointment Images
                                        </label>
                                    @endif
                                    <div class="dropdown-item text-center">
                                        <button class="btn btn-primary btn-sm mt-2" onclick="handlePrint()">Confirm &
                                            Print</button>
                                    </div>
                                    <div class="dropdown-item">
                                        <p id="warning" style="color:red; display:none;" class="mt-1">Select at least
                                            one
                                            section.</p>
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
                                <div class="d-flex gap-2 mb-3 no-print">
                                    <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal"
                                        data-target="#addPestTypeModal">
                                        <span class="fa fa-plus"></span> Add Pest Type
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                        data-target="#addAreaModal">
                                        <span class="fa fa-plus"></span> Add Area
                                    </button>
                                </div>

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
                                                    <th class="no-print" style="width: 80px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pestTypes as $index => $type)
                                                    <tr>
                                                        <td style="vertical-align: middle; text-align: center">
                                                            {{ $index + 1 }}</td>
                                                        <td style="vertical-align: middle; text-align: center">
                                                            {{ $type->svcp_pest_type }}</td>
                                                        <td class="no-print"
                                                            style="vertical-align: middle; text-align: center">
                                                            <a class="btn btn-danger btn-sm mb-1"
                                                                href="javascript:void(0)" data-toggle="modal"
                                                                data-target="#deletePestTypeModal-{{ $type->svcop_id }}">
                                                                <span class="fa fa-trash"></span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        {{-- Delete Pest Type Modal --}}
                                        @foreach ($pestTypes as $type)
                                            <div class="modal fade" id="deletePestTypeModal-{{ $type->svcop_id }}"
                                                tabindex="-1" role="dialog" aria-labelledby="deletePestTypeModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="POST"
                                                            action="{{ action('App\Http\Controllers\AppointmentController@requested_appointments_view_delete_pest', [$type->svcop_id]) }}">
                                                            @csrf
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title text-white"
                                                                    id="deletePestTypeModalLabel">
                                                                    Please Confirm
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to <strong>DELETE</strong> the
                                                                    <strong>{{ $type->svcp_pest_type }}</strong> pest type?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">
                                                                    <span class="fa fa-close"></span> Close
                                                                </button>
                                                                <button type="submit" class="btn btn-danger">
                                                                    <span class="fa fa-trash"></span> Confirm Delete
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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
                                                    <th class="no-print" style="width: 80px;">Action</th>
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
                                                        <td class="no-print"
                                                            style="vertical-align: middle; text-align: center">
                                                            <a class="btn btn-danger btn-sm mb-1"
                                                                href="javascript:void(0)" data-toggle="modal"
                                                                data-target="#deleteAreaModal-{{ $area->svcpa_id }}">
                                                                <span class="fa fa-trash"></span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        {{-- Delete Area Modal --}}
                                        @foreach ($serviceAreas as $area)
                                            <div class="modal fade" id="deleteAreaModal-{{ $area->svcpa_id }}"
                                                tabindex="-1" role="dialog" aria-labelledby="deleteAreaModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="POST"
                                                            action="{{ action('App\Http\Controllers\AppointmentController@requested_appointments_view_delete_service', [$area->svcpa_id]) }}">
                                                            @csrf
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title text-white"
                                                                    id="deleteAreaModalLabel">
                                                                    Please Confirm
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to <strong>DELETE</strong> the
                                                                    service for <strong>{{ $area->svcpa_area }}</strong>
                                                                    with the cost of
                                                                    <em>₱{{ number_format($area->svcpa_cost, 2) }}</em>?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">
                                                                    <span class="fa fa-close"></span> Close
                                                                </button>
                                                                <button type="submit" class="btn btn-danger">
                                                                    <span class="fa fa-trash"></span> Confirm Delete
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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
                                                        <p>STANDARD TREATMENT: sqm × cost</p>
                                                        <p>HYBRID TREATMENT: sqm × cost + device</p>
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

                                                {{-- Fill empty cells if less than 5 images --}}
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

    {{-- Add Pest Type Modal --}}
    <div class="modal fade" id="addPestTypeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST"
                    action="{{ action('App\Http\Controllers\AppointmentController@requested_appointments_view_add_pest') }}">
                    @csrf

                    <input type="hidden" name="svc_id" value="{{ $display->svc_id }}">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">Add Pest Type <span class="text-danger">*</span></h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Pest Type</label>
                            <select name="svcp_id" class="form-control" required>
                                @foreach ($servicePackages as $package)
                                    <option value="{{ $package->svcp_id }}">
                                        {{ $package->svcp_pest_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <span class="fa fa-close"></span> Close
                        </button>

                        <button type="submit" class="btn btn-success">
                            <span class="fa fa-save"></span> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Area Modal --}}
    <div class="modal fade" id="addAreaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST"
                    action="{{ action('App\Http\Controllers\AppointmentController@requested_appointments_view_add_service') }}">
                    @csrf

                    <input type="hidden" name="svc_id" value="{{ $display->svc_id }}">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">Add Service Area</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Area <span class="text-danger">*</span></label>
                            <select name="svcpa_id" id="svcpaSelect" class="form-control" required>
                                @foreach ($servicePackageAreas as $area)
                                    <option value="{{ $area->svcpa_id }}" data-cost="{{ $area->svcpa_cost }}">
                                        {{ $area->svcpa_area }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Cost</label>
                            <input type="text" id="areaCost" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <span class="fa fa-close"></span> Close
                        </button>
                        <button type="submit" class="btn btn-success">
                            <span class="fa fa-save"></span> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Assess Modal --}}
    <div class="modal fade" id="assessAppointmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form method="POST"
                    action="{{ action('App\Http\Controllers\AppointmentController@requested_appointments_view_assess') }}">
                    @csrf

                    <input type="hidden" name="svc_id" value="{{ $display->svc_id }}">

                    <div class="modal-header bg-warning text-black">
                        <h5 class="modal-title text-black">Assess Requested Appointment
                            (SA-{{ str_pad($display->svc_sa_number, 6, '0', STR_PAD_LEFT) }})</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row overflow-auto">
                            <div class="table-responsive" id="sectionA">
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
                                            <td style="font-weight: bold;">INITIAL PRICE</td>
                                            <td>₱{{ number_format($display->svc_initial_price, 2) }}</td>
                                            <td style="font-weight: bold;">LOCATION PRICE</td>
                                            <td>₱{{ number_format($display->svc_location_price, 2) }}</td>
                                            <td style="font-weight: bold;">BALANCE</td>
                                            <td>₱{{ number_format($display->svc_balance, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">IS TERMITE</td>
                                            <td>{{ $display->svc_is_termite ? 'YES' : 'NO' }}</td>
                                            <td style="font-weight: bold;">DISTANCE</td>
                                            <td>{{ $display->svc_km_distance }}KM</td>
                                            <td style="font-weight: bold;">IS PACKAGE</td>
                                            <td>{{ $display->svc_is_package ? 'YES' : 'NO' }}</td>
                                        </tr>
                                        @if ($display->svc_is_termite)
                                            <tr>
                                                <th colspan="6" class="text-center table-light">TERMITE CASE</th>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;">INITIAL SQM</td>
                                                <td>{{ $display->svc_sqm_initial }}</td>
                                                <td style="font-weight: bold;">WITH DEVICE</td>
                                                <td>{{ $display->svc_with_device ? 'YES' : 'NO' }}</td>
                                                <td style="font-weight: bold;">DEVICE COUNT</td>
                                                <td>{{ $display->svc_device_count ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;">TREATMENT TYPE</td>
                                                <td colspan="5">{{ $display->svc_type_treatment }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th colspan="6" class="text-center table-light">SCHEDULE</th>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">CLIENT DATE REQUESTED</td>
                                            <td colspan="2">
                                                {{ \Carbon\Carbon::parse($display->svca_client_date)->format('m/d/Y') }}
                                            </td>
                                            <td style="font-weight: bold;">CLIENT TIME REQUESTED</td>
                                            <td colspan="2">
                                                {{ \Carbon\Carbon::parse($display->svca_client_time)->format('h:i A') }}
                                            </td>
                                        </tr>
                                        @if (!empty($display->svc_problem_description))
                                            <tr>
                                                <th colspan="6" class="text-center table-light">PROBLEM DESCRIPTION
                                                </th>
                                            </tr>
                                            <tr>
                                                <td colspan="6" style="text-align: justify">
                                                    {{ $display->svc_problem_description }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            {{-- SA Number --}}
                            <div class="col-md-6 mb-3">
                                <label for="svc_sa_number">
                                    SA Number (SA-{{ str_pad($display->svc_sa_number, 6, '0', STR_PAD_LEFT) }}) <span
                                        class="text-muted">(Ignore starting 0 digits)</span> <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" name="svc_sa_number" placeholder="SA Number"
                                    value="{{ $display->svc_sa_number }}" required>
                            </div>

                            {{-- Property Type --}}
                            <div class="col-md-6 mb-3">
                                <label>Property Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="svc_property_type" required>
                                    <option value="PERSONAL"
                                        {{ $display->svc_property_type === 'PERSONAL' ? 'selected' : '' }}>
                                        PERSONAL</option>
                                    <option value="COMMERCIAL"
                                        {{ $display->svc_property_type === 'COMMERCIAL' ? 'selected' : '' }}>
                                        COMMERCIAL</option>
                                </select>
                            </div>
                        </div>

                        @php
                            $isTermite = $display->svc_is_termite == 1;

                            $fieldName = $isTermite ? 'svc_is_termite' : 'svc_is_package';
                            $label = $isTermite ? 'Is Termite' : 'Is Package';
                            $value = $isTermite ? $display->svc_is_termite : $display->svc_is_package;

                            // layout control
                            $selectCol = $isTermite ? 'col-md-6' : 'col-md-12';
                            $sqmCol = $isTermite ? 'col-md-6' : 'col-md-4';
                        @endphp

                        <div class="row">

                            <div class="{{ $selectCol }} mb-3">
                                <label>
                                    {{ $label }}

                                    @if ($isTermite)
                                        <span class="text-muted">(Not Editable)</span>
                                    @else
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                <select class="form-control" name="{{ $fieldName }}" required
                                    {{ $isTermite ? 'disabled' : '' }}>

                                    <option value="1" {{ $value == 1 ? 'selected' : '' }}>YES</option>
                                    <option value="0" {{ $value == 0 ? 'selected' : '' }}>NO</option>
                                </select>

                                {{-- IMPORTANT: keep value when disabled --}}
                                @if ($isTermite)
                                    <input type="hidden" name="{{ $fieldName }}" value="{{ $value }}">
                                @endif
                            </div>

                            <div class="{{ $sqmCol }} mb-3">
                                <label>SQM Meters <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="svc_sqm_initial"
                                    value="{{ $display->svc_sqm_initial }}" placeholder="Initial SQM Meters">
                            </div>

                        </div>

                        @if ($display->svc_is_termite)
                            <div class="row">
                                {{-- SQM Details --}}
                                <div class="col-md-6 mb-3">
                                    <label>SQM DETAILS <span class="text-danger">*</span></label>
                                    <select class="form-control" name="svcpat_id" id="svcpat_id_select" required>
                                        @foreach ($termiteAreaOptions as $opt)
                                            <option value="{{ $opt->svcpat_id }}" data-cost="{{ $opt->svcpat_cost }}"
                                                {{ $display->svcpat_id == $opt->svcpat_id ? 'selected' : '' }}>
                                                {{ $opt->svcpat_sqm_details }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- SQM Cost (read-only, derived from selected svcpat_id) --}}
                                <div class="col-md-6 mb-3">
                                    <label>SQM Cost <span class="text-muted">(Not Editable)</span></label>
                                    <input type="number" step="0.01" class="form-control" id="svcpat_cost_display"
                                        placeholder="SQM Cost" readonly>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Treatment Type --}}
                                <div class="col-md-6 mb-3">
                                    <label for="svc_type_treatment">
                                        Treatment Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="svc_type_treatment" required>
                                        <option value="STANDARD TREATMENT">STANDARD TREATMENT</option>
                                        <option value="HYBRID TREATMENT">HYBRID TREATMENT</option>
                                    </select>
                                </div>

                                {{-- Device Count --}}
                                <div class="col-md-6 mb-3" id="termiteDeviceCountCol" style="display:none;">
                                    <label for="svc_device_count">
                                        Device Count <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="svc_device_count"
                                        placeholder="Device Count" data-device-cost="{{ $deviceCost->svcpad_cost ?? 0 }}"
                                        required>
                                    <small class="text-muted device-cost-hint" style="display:none;">
                                        {{ $display->branch_name }} device price:
                                        ₱{{ number_format($deviceCost->svcpad_cost ?? 0, 2) }} / unit
                                    </small>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            {{-- Infestation --}}
                            <div class="col-md-6 mb-3">
                                <label>Infestation <span class="text-danger">*</span></label>
                                <select class="form-control" name="svc_infestation" required>
                                    <option value="LOW" {{ $display->svc_infestation === 'LOW' ? 'selected' : '' }}>
                                        LOW</option>
                                    <option value="MID" {{ $display->svc_infestation === 'MID' ? 'selected' : '' }}>
                                        MID</option>
                                    <option value="HIGH" {{ $display->svc_infestation === 'HIGH' ? 'selected' : '' }}>
                                        HIGH</option>
                                </select>
                            </div>

                            {{-- Distance --}}
                            <div class="col-md-6 mb-3">
                                <label for="svc_km_distance">
                                    Distance (in KM [whole number]) <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" name="svc_km_distance"
                                    id="svc_km_distance_input" placeholder="Location Price"
                                    value="{{ $display->svc_km_distance }}" required>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Location Price --}}
                            <div class="{{ $display->svc_is_termite ? 'col-md-4' : 'col-md-3' }} mb-3">
                                <label for="svc_location_price">
                                    Location Price <span class="text-muted">(Not Editable)</span>
                                </label>
                                <input type="number" step="0.01" class="form-control" name="svc_location_price"
                                    id="svc_location_price_input" placeholder="Location Price"
                                    value="{{ $display->svc_location_price }}"
                                    data-first-cost="{{ $locationRate->svcpal_first_cost ?? 0 }}"
                                    data-succeeding-cost="{{ $locationRate->svcpal_succeeding_cost ?? 0 }}" readonly>
                                @if ($locationRate)
                                    <small class="text-muted">
                                        {{ $display->branch_name }}: First 10 KM =
                                        ₱{{ number_format($locationRate->svcpal_first_cost, 2) }} /
                                        Succeeding KM = ₱{{ number_format($locationRate->svcpal_succeeding_cost, 2) }}
                                    </small>
                                @endif
                            </div>

                            {{-- Service Order Price (non-termite only) --}}
                            @if (!$display->svc_is_termite)
                                <div class="col-md-3 mb-3">
                                    <label for="svc_initial_price">
                                        Service Order Price <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control" name="svc_initial_price"
                                        placeholder="Price" value="{{ $display->svc_initial_price }}" required>
                                </div>
                            @endif

                            {{-- Fixed Price --}}
                            <div class="{{ $display->svc_is_termite ? 'col-md-4' : 'col-md-3' }} mb-3">
                                <label for="svc_fixed_price">Fixed Price <span class="text-muted">(Not
                                        Editable)</span></label>
                                <input type="number" step="0.01" class="form-control" name="svc_fixed_price"
                                    id="svc_fixed_price" placeholder="Fixed Price" readonly>
                            </div>

                            {{-- Final Price --}}
                            <div class="{{ $display->svc_is_termite ? 'col-md-4' : 'col-md-3' }} mb-3">
                                <label for="svc_final_price">Final Price <span class="text-muted">(Not
                                        Editable)</span></label>
                                <input type="number" step="0.01" class="form-control" name="svc_final_price"
                                    placeholder="Final Price" readonly>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Appointment Date --}}
                            <div class="col-md-4 mb-3">
                                <label>Approve Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="svca_approved_date"
                                    value="{{ \Carbon\Carbon::parse($display->svca_client_date)->format('Y-m-d') }}"
                                    required>
                            </div>

                            {{-- Appointment From --}}
                            <div class="col-md-4 mb-3">
                                <label for="svca_approved_time_from">
                                    Approve Time From <span class="text-danger">*</span>
                                </label>
                                <input type="time" class="form-control" name="svca_approved_time_from"
                                    value="{{ \Carbon\Carbon::parse($display->svca_client_time)->format('H:i') }}"
                                    required>
                            </div>

                            {{-- Appointment To --}}
                            <div class="col-md-4 mb-3">
                                <label for="svca_approved_time_to">Approve Time To <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="svca_approved_time_to"
                                    value="{{ \Carbon\Carbon::parse($display->svca_client_time)->addHours(2)->format('H:i') }}"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Chemical Quantity --}}
                            <div class="col-md-6 mb-3">
                                <label for="svc_chemical_quantity">
                                    Chemical Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" class="form-control" name="svc_chemical_quantity"
                                    placeholder="Chemical Quantity" value="{{ $display->svc_chemical_quantity }}"
                                    required>
                            </div>

                            {{-- Chemical Metric --}}
                            <div class="col-md-6 mb-3">
                                <label>Chemical Metric <span class="text-danger">*</span></label>
                                <select class="form-control" name="svc_chemical_metric" required>
                                    <option value="PIECES"
                                        {{ $display->svc_chemical_metric === 'PIECES' ? 'selected' : '' }}>
                                        PIECES</option>
                                    <option value="MILLILITERS"
                                        {{ $display->svc_chemical_metric === 'MILLILITERS' ? 'selected' : '' }}>
                                        MILLILITERS</option>
                                    <option value="LITERS"
                                        {{ $display->svc_chemical_metric === 'LITERS' ? 'selected' : '' }}>
                                        LITERS</option>
                                    <option value="MILLIGRAMS"
                                        {{ $display->svc_chemical_metric === 'MILLIGRAMS' ? 'selected' : '' }}>
                                        MILLIGRAMS</option>
                                    <option value="GRAMS"
                                        {{ $display->svc_chemical_metric === 'GRAMS' ? 'selected' : '' }}>
                                        GRAMS</option>
                                    <option value="KILOGRAMS"
                                        {{ $display->svc_chemical_metric === 'KILOGRAMS' ? 'selected' : '' }}>
                                        KILOGRAMS</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="svc_assessment_recommendation">
                                    Assessment Recommendation <span class="text-muted">(Optional)</span>
                                </label>
                                <textarea class="form-control" name="svc_assessment_recommendation" rows="3" maxlength="500"
                                    placeholder="Enter assessment recommendation here...">{{ $display->svc_assessment_recommendation }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <span class="fa fa-close"></span> Close
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <span class="fa fa-save"></span> Save Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Confirm Assessment Modal --}}
    <div class="modal fade" id="confirmAssessmentAppointmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form method="POST"
                    action="{{ action('App\Http\Controllers\AppointmentController@requested_appointments_view_assess_confirmation') }}">
                    @csrf

                    <input type="hidden" name="svc_id" value="{{ $display->svc_id }}">

                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white">Confirm Assessment
                            (SA-{{ str_pad($display->svc_sa_number, 6, '0', STR_PAD_LEFT) }})</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        {{-- Read-only info table (same as assess) --}}
                        <div class="row overflow-auto">
                            <div class="table-responsive">
                                <table class="table table-bordered text-left align-middle">
                                    <tbody>
                                        <tr>
                                            <th colspan="6" class="text-center table-light">CLIENT INFORMATION</th>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">CLIENT NAME</td>
                                            <td>{{ $display->usr_first_name }} {{ $display->usr_last_name }}</td>
                                            <td style="font-weight:bold;">EMAIL</td>
                                            <td>{{ $display->usr_email }}</td>
                                            <td style="font-weight:bold;">MOBILE</td>
                                            <td>{{ $display->usr_mobile }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">ADDRESS</td>
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
                                            <td style="font-weight:bold;">ADDRESS TYPE</td>
                                            <td>{{ $display->add_name }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="6" class="text-center table-light">SERVICE INFORMATION</th>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">BRANCH</td>
                                            <td>{{ $display->branch_name }}</td>
                                            <td style="font-weight:bold;">STATUS</td>
                                            <td>{{ $display->svc_status }}</td>
                                            <td style="font-weight:bold;">PAYMENT STATUS</td>
                                            <td>{{ $display->svc_payment_status }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">INITIAL PRICE</td>
                                            <td>₱{{ number_format($display->svc_initial_price, 2) }}</td>
                                            <td style="font-weight: bold;">LOCATION PRICE</td>
                                            <td>₱{{ number_format($display->svc_location_price, 2) }}</td>
                                            <td style="font-weight: bold;">BALANCE</td>
                                            <td>₱{{ number_format($display->svc_balance, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold;">IS TERMITE</td>
                                            <td>{{ $display->svc_is_termite ? 'YES' : 'NO' }}</td>
                                            <td style="font-weight: bold;">DISTANCE</td>
                                            <td>{{ $display->svc_km_distance }}KM</td>
                                            <td style="font-weight: bold;">IS PACKAGE</td>
                                            <td>{{ $display->svc_is_package ? 'YES' : 'NO' }}</td>
                                        </tr>

                                        @if ($display->svc_is_termite)
                                            <tr>
                                                <th colspan="6" class="text-center table-light">TERMITE CASE</th>
                                            </tr>
                                            <tr>
                                                <td style="font-weight:bold;">INITIAL SQM</td>
                                                <td>{{ $display->svc_sqm_initial }}</td>
                                                <td style="font-weight:bold;">WITH DEVICE</td>
                                                <td>{{ $display->svc_with_device ? 'YES' : 'NO' }}</td>
                                                <td style="font-weight:bold;">DEVICE COUNT</td>
                                                <td>{{ $display->svc_device_count ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight:bold;">TREATMENT TYPE</td>
                                                <td colspan="5">{{ $display->svc_type_treatment }}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <th colspan="6" class="text-center table-light">SCHEDULE</th>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:bold;">CLIENT DATE REQUESTED</td>
                                            <td colspan="2">
                                                {{ \Carbon\Carbon::parse($display->svca_client_date)->format('m/d/Y') }}
                                            </td>
                                            <td style="font-weight:bold;">CLIENT TIME REQUESTED</td>
                                            <td colspan="2">
                                                {{ \Carbon\Carbon::parse($display->svca_client_time)->format('h:i A') }}
                                            </td>
                                        </tr>

                                        @if (!empty($display->svc_problem_description))
                                            <tr>
                                                <th colspan="6" class="text-center table-light">PROBLEM DESCRIPTION
                                                </th>
                                            </tr>
                                            <tr>
                                                <td colspan="6" style="text-align:justify">
                                                    {{ $display->svc_problem_description }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            {{-- SA Number --}}
                            <div class="col-md-6 mb-3">
                                <label for="svc_sa_number">
                                    SA Number (SA-{{ str_pad($display->svc_sa_number, 6, '0', STR_PAD_LEFT) }})
                                    <span class="text-muted">(Ignore starting 0 digits)</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" name="svc_sa_number"
                                    value="{{ $display->svc_sa_number }}" required>
                            </div>

                            {{-- Property Type --}}
                            <div class="col-md-6 mb-3">
                                <label>Property Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="svc_property_type" required>
                                    <option value="PERSONAL"
                                        {{ $display->svc_property_type === 'PERSONAL' ? 'selected' : '' }}>
                                        PERSONAL</option>
                                    <option value="COMMERCIAL"
                                        {{ $display->svc_property_type === 'COMMERCIAL' ? 'selected' : '' }}>
                                        COMMERCIAL</option>
                                </select>
                            </div>
                        </div>

                        @php
                            $isTermiteConfirm = $display->svc_is_termite == 1;
                            $sqmColConfirm = $isTermiteConfirm ? 'col-md-6' : 'col-md-4';
                            $selectColConfirm = $isTermiteConfirm ? 'col-md-6' : 'col-md-12';
                        @endphp

                        <div class="row">
                            {{-- Is Termite / Package --}}
                            <div class="{{ $selectColConfirm }} mb-3" id="confirmPackageCol">
                                @if ($isTermiteConfirm)
                                    <label>Is Termite <span class="text-muted">(Not Editable)</span></label>
                                    <select class="form-control" disabled>
                                        <option value="1" selected>YES</option>
                                    </select>
                                    <input type="hidden" name="svc_is_termite" value="1">
                                @else
                                    <label>Is Package? <span class="text-danger">*</span></label>
                                    <select class="form-control" name="svc_is_package" id="confirmPackageSelect"
                                        required>
                                        <option value="1" {{ $display->svc_is_package == 1 ? 'selected' : '' }}>YES
                                        </option>
                                        <option value="0" {{ $display->svc_is_package == 0 ? 'selected' : '' }}>NO
                                        </option>
                                    </select>
                                @endif
                            </div>

                            {{-- SQM --}}
                            <div class="{{ $sqmColConfirm }} mb-3" id="confirmSqmCol">
                                <label>SQM Meters <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="svc_sqm_initial"
                                    id="confirmSqmInput" value="{{ $display->svc_sqm_initial }}">
                            </div>
                        </div>

                        {{-- TERMITE ONLY --}}
                        @if ($isTermiteConfirm)
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>SQM DETAILS <span class="text-danger">*</span></label>
                                    <select class="form-control" name="svcpat_id" id="confirmSvcpatSelect" required>
                                        @foreach ($termiteAreaOptions as $opt)
                                            <option value="{{ $opt->svcpat_id }}" data-cost="{{ $opt->svcpat_cost }}"
                                                {{ $display->svcpat_id == $opt->svcpat_id ? 'selected' : '' }}>
                                                {{ $opt->svcpat_sqm_details }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>SQM Cost <span class="text-muted">(Not Editable)</span></label>
                                    <input type="number" step="0.01" class="form-control"
                                        id="confirmSvcpatCostDisplay" placeholder="SQM Cost" readonly>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Treatment Type --}}
                                <div class="col-md-6 mb-3" id="confirmTreatmentCol">
                                    <label for="confirmTreatmentSelect">
                                        Treatment Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="svc_type_treatment" id="confirmTreatmentSelect"
                                        required>
                                        <option value="STANDARD TREATMENT"
                                            {{ $display->svc_type_treatment === 'STANDARD TREATMENT' ? 'selected' : '' }}>
                                            STANDARD TREATMENT</option>
                                        <option value="HYBRID TREATMENT"
                                            {{ $display->svc_type_treatment === 'HYBRID TREATMENT' ? 'selected' : '' }}>
                                            HYBRID TREATMENT</option>
                                    </select>
                                </div>

                                {{-- Device Count --}}
                                <div class="col-md-6 mb-3" id="confirmDeviceCountCol" style="display:none;">
                                    <label for="confirmDeviceCountInput">
                                        Device Count <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="svc_device_count"
                                        id="confirmDeviceCountInput" placeholder="Device Count"
                                        value="{{ $display->svc_device_count }}"
                                        data-device-cost="{{ $deviceCost->svcpad_cost ?? 0 }}">
                                    <small class="text-muted" id="confirmDeviceCostHint" style="display:none;">
                                        {{ $display->branch_name }} device price:
                                        ₱{{ number_format($deviceCost->svcpad_cost ?? 0, 2) }} / unit
                                    </small>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            {{-- Infestation --}}
                            <div class="col-md-6 mb-3">
                                <label>Infestation <span class="text-danger">*</span></label>
                                <select class="form-control" name="svc_infestation" required>
                                    <option value="LOW" {{ $display->svc_infestation === 'LOW' ? 'selected' : '' }}>
                                        LOW</option>
                                    <option value="MID" {{ $display->svc_infestation === 'MID' ? 'selected' : '' }}>
                                        MID</option>
                                    <option value="HIGH" {{ $display->svc_infestation === 'HIGH' ? 'selected' : '' }}>
                                        HIGH</option>
                                </select>
                            </div>

                            {{-- Distance --}}
                            <div class="col-md-6 mb-3">
                                <label>Distance (KM) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="svc_km_distance"
                                    id="confirmKmDistanceInput" value="{{ $display->svc_km_distance }}" required>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Location Price --}}
                            <div class="{{ $isTermiteConfirm ? 'col-md-4' : 'col-md-3' }} mb-3">
                                <label>Location Price <span class="text-muted">(Not Editable)</span></label>
                                <input type="number" step="0.01" class="form-control" name="svc_location_price"
                                    id="confirmLocationPrice" value="{{ $display->svc_location_price }}"
                                    data-first-cost="{{ $locationRate->svcpal_first_cost ?? 0 }}"
                                    data-succeeding-cost="{{ $locationRate->svcpal_succeeding_cost ?? 0 }}" readonly>
                                @if ($locationRate)
                                    <small class="text-muted">
                                        {{ $display->branch_name }}: First 10 KM =
                                        ₱{{ number_format($locationRate->svcpal_first_cost, 2) }} /
                                        Succeeding KM = ₱{{ number_format($locationRate->svcpal_succeeding_cost, 2) }}
                                    </small>
                                @endif
                            </div>

                            {{-- Service Order Price (non-termite only) --}}
                            @if (!$isTermiteConfirm)
                                <div class="col-md-3 mb-3">
                                    <label>Service Order Price <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="svc_initial_price"
                                        id="confirmInitialPrice" value="{{ $display->svc_initial_price }}" required>
                                </div>
                            @endif

                            {{-- Fixed Price --}}
                            <div class="{{ $isTermiteConfirm ? 'col-md-4' : 'col-md-3' }} mb-3">
                                <label>Fixed Price <span class="text-muted">(Not Editable)</span></label>
                                <input type="number" step="0.01" class="form-control" id="confirmFixedPrice"
                                    name="svc_fixed_price" value="{{ $display->svc_fixed_price }}" readonly>
                            </div>

                            {{-- Final Price --}}
                            <div class="{{ $isTermiteConfirm ? 'col-md-4' : 'col-md-3' }} mb-3">
                                <label>Final Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="svc_final_price"
                                    id="confirmFinalPrice" value="{{ $display->svc_final_price }}">
                            </div>
                        </div>

                        <div class="row">
                            {{-- Approved Date --}}
                            <div class="col-md-4 mb-3">
                                <label>Approve Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="svca_approved_date"
                                    value="{{ $display->svca_approved_date }}" required>
                            </div>

                            {{-- Time From --}}
                            <div class="col-md-4 mb-3">
                                <label>Approve Time From <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="svca_approved_time_from"
                                    value="{{ $display->svca_approved_time_from }}" required>
                            </div>

                            {{-- Time To --}}
                            <div class="col-md-4 mb-3">
                                <label>Approve Time To <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="svca_approved_time_to"
                                    value="{{ $display->svca_approved_time_to }}" required>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Chemical Quantity --}}
                            <div class="col-md-6 mb-3">
                                <label>Chemical Quantity <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="svc_chemical_quantity"
                                    value="{{ $display->svc_chemical_quantity }}" required>
                            </div>

                            {{-- Chemical Metric --}}
                            <div class="col-md-6 mb-3">
                                <label>Chemical Metric <span class="text-danger">*</span></label>
                                <select class="form-control" name="svc_chemical_metric" required>
                                    <option value="PIECES"
                                        {{ $display->svc_chemical_metric === 'PIECES' ? 'selected' : '' }}>PIECES</option>
                                    <option value="MILLILITERS"
                                        {{ $display->svc_chemical_metric === 'MILLILITERS' ? 'selected' : '' }}>
                                        MILLILITERS</option>
                                    <option value="LITERS"
                                        {{ $display->svc_chemical_metric === 'LITERS' ? 'selected' : '' }}>LITERS</option>
                                    <option value="MILLIGRAMS"
                                        {{ $display->svc_chemical_metric === 'MILLIGRAMS' ? 'selected' : '' }}>MILLIGRAMS
                                    </option>
                                    <option value="GRAMS"
                                        {{ $display->svc_chemical_metric === 'GRAMS' ? 'selected' : '' }}>GRAMS</option>
                                    <option value="KILOGRAMS"
                                        {{ $display->svc_chemical_metric === 'KILOGRAMS' ? 'selected' : '' }}>KILOGRAMS
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Assessment Recommendation <span class="text-muted">(Optional)</span></label>
                                <textarea class="form-control" name="svc_assessment_recommendation" rows="3" maxlength="500">{{ $display->svc_assessment_recommendation }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <span class="fa fa-close"></span> Close
                        </button>
                        <button type="submit" class="btn btn-success">
                            <span class="fa fa-check"></span> Confirm Assessment
                        </button>
                    </div>
                </form>
            </div>
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

            // Reset all sections (show all initially) - Check if the section exists
            const sectionIds = [
                'sectionA', 'sectionB', 'sectionC'
            ];

            sectionIds.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                if (section) {
                    section.classList.remove('print-hide');
                }
            });

            // Hide unselected sections
            checkboxes.forEach(checkbox => {
                const targetId = checkbox.dataset.target;
                const targetDiv = document.getElementById(targetId);

                // Only modify the target if it exists
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

            // Delay the print to ensure sections are properly hidden/shown
            setTimeout(() => {
                console.log("Triggering print dialog...");
                window.print();
            }, 500); // Allow time for the layout to update

            // Restore view after printing
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
        document.addEventListener('DOMContentLoaded', function() {

            // SECTION 1 — SHARED MODAL LOGIC (final price, sqm toggle, treatment)
            // Targets both #assessAppointmentModal and #confirmAssessmentAppointmentModal

            const modals = document.querySelectorAll('#assessAppointmentModal, #confirmAssessmentAppointmentModal');

            modals.forEach(function(modal) {

                const locationPriceInput = modal.querySelector('[name="svc_location_price"]');
                const initialPriceInput = modal.querySelector('[name="svc_initial_price"]');
                const finalPriceInput = modal.querySelector('[name="svc_final_price"]');
                const fixedPriceInput = modal.querySelector('[name="svc_fixed_price"]');

                const packageSelect = modal.querySelector('[name="svc_is_package"]');
                const termiteSelect = modal.querySelector('[name="svc_is_termite"]');
                const sqmInput = modal.querySelector('[name="svc_sqm_initial"]');

                // Prevent errors if fields do not exist
                if (!locationPriceInput || !initialPriceInput || !finalPriceInput) {
                    return;
                }

                function setComputedPrice(value) {
                    if (fixedPriceInput) fixedPriceInput.value = value;
                    if (finalPriceInput) finalPriceInput.value = value;
                }

                // FINAL PRICE COMPUTATION
                function updateFinalPrice() {

                    let locationPrice = parseFloat(locationPriceInput.value) || 0;
                    let initialPrice = parseFloat(initialPriceInput.value) || 0;

                    let finalPrice = locationPrice + initialPrice;

                    if (locationPrice === 0) {
                        finalPrice = initialPrice;
                    }

                    if (initialPrice === 0) {
                        finalPrice = locationPrice;
                    }

                    setComputedPrice(finalPrice.toFixed(2));
                }

                locationPriceInput.addEventListener('input', updateFinalPrice);
                initialPriceInput.addEventListener('input', updateFinalPrice);

                // Run on load
                updateFinalPrice();

                // TERMITE / PACKAGE + SQM TOGGLE
                if (sqmInput) {

                    const sqmCol = sqmInput.closest('.col-md-4, .col-md-6');
                    const packageCol = (packageSelect || termiteSelect) ?
                        (packageSelect || termiteSelect).closest('.col-md-12, .col-md-6') :
                        null;

                    function toggleSQM() {

                        // TERMITE MODE (READONLY + 6 COL LAYOUT)
                        if (termiteSelect && termiteSelect.value == '1') {

                            termiteSelect.setAttribute('disabled', true);

                            sqmCol.style.display = 'block';

                            sqmCol.classList.remove('col-md-4');
                            sqmCol.classList.add('col-md-6');

                            if (packageCol) {
                                packageCol.classList.remove('col-md-12');
                                packageCol.classList.add('col-md-6');
                            }

                            return;
                        }

                        // PACKAGE MODE
                        if (packageSelect && packageSelect.value == '1') {

                            sqmCol.style.display = 'block';

                            if (packageCol) {
                                packageCol.classList.remove('col-md-12');
                                packageCol.classList.add('col-md-6');
                            }

                            sqmCol.classList.remove('col-md-4');
                            sqmCol.classList.add('col-md-6');

                        } else {

                            sqmCol.style.display = 'none';

                            if (packageCol) {
                                packageCol.classList.remove('col-md-6');
                                packageCol.classList.add('col-md-12');
                            }
                        }
                    }

                    toggleSQM();

                    if (packageSelect) {
                        packageSelect.addEventListener('change', toggleSQM);
                    }

                    if (termiteSelect) {
                        termiteSelect.addEventListener('change', toggleSQM);
                    }
                }

                // TREATMENT TYPE <-> DEVICE COUNT TOGGLE
                const treatmentSelect = modal.querySelector('[name="svc_type_treatment"]');
                const deviceCountInput = modal.querySelector('[name="svc_device_count"]');
                const deviceCountCol = modal.querySelector('#termiteDeviceCountCol');

                if (treatmentSelect && deviceCountCol) {

                    const treatmentCol = treatmentSelect.closest('.col-md-6');
                    const deviceCostHint = deviceCountCol.querySelector('.device-cost-hint');
                    const deviceCostPerUnit = parseFloat(deviceCountInput?.getAttribute(
                        'data-device-cost') || 0);

                    // Updated final price function that factors in device cost for hybrid
                    function updateFinalPriceWithDevice() {

                        let locationPrice = parseFloat(locationPriceInput.value) || 0;
                        let initialPrice = parseFloat(initialPriceInput.value) || 0;

                        let base = 0;

                        if (locationPrice === 0) {
                            base = initialPrice;
                        } else if (initialPrice === 0) {
                            base = locationPrice;
                        } else {
                            base = locationPrice + initialPrice;
                        }

                        let deviceTotal = 0;

                        if (treatmentSelect.value === 'HYBRID TREATMENT') {
                            const deviceCount = parseInt(deviceCountInput?.value) || 0;
                            deviceTotal = deviceCount * deviceCostPerUnit;
                        }

                        setComputedPrice((base + deviceTotal).toFixed(2));
                    }

                    // Re-bind service/initial price listeners to use the device-aware function
                    locationPriceInput.removeEventListener('input', updateFinalPrice);
                    initialPriceInput.removeEventListener('input', updateFinalPrice);
                    locationPriceInput.addEventListener('input', updateFinalPriceWithDevice);
                    initialPriceInput.addEventListener('input', updateFinalPriceWithDevice);

                    // Device count change also updates final price
                    if (deviceCountInput) {
                        deviceCountInput.addEventListener('input', updateFinalPriceWithDevice);
                    }

                    function toggleTreatmentLayout() {

                        if (treatmentSelect.value === 'HYBRID TREATMENT') {

                            if (treatmentCol) {
                                treatmentCol.classList.remove('col-md-12');
                                treatmentCol.classList.add('col-md-6');
                            }

                            deviceCountCol.style.display = '';

                            if (deviceCostHint) deviceCostHint.style.display = 'block';

                        } else {

                            if (treatmentCol) {
                                treatmentCol.classList.remove('col-md-6');
                                treatmentCol.classList.add('col-md-12');
                            }

                            deviceCountCol.style.display = 'none';

                            if (deviceCostHint) deviceCostHint.style.display = 'none';

                            // Clear device count so it doesn't affect price when switching back to standard
                            if (deviceCountInput) deviceCountInput.value = '';
                        }

                        // Recompute price whenever treatment type changes
                        updateFinalPriceWithDevice();
                    }

                    treatmentSelect.addEventListener('change', toggleTreatmentLayout);

                    // Run on load
                    toggleTreatmentLayout();
                }
            });

            // SECTION 2 — AREA COST DISPLAY (unchanged)

            const svcpaSelect = document.getElementById('svcpaSelect');

            if (svcpaSelect) {
                svcpaSelect.addEventListener('change', function() {

                    let cost = this.options[this.selectedIndex].getAttribute('data-cost');

                    const areaCost = document.getElementById('areaCost');

                    if (areaCost) {
                        areaCost.value = cost ? parseFloat(cost).toFixed(2) : '';
                    }
                });
            }

            // SECTION 3 — TERMITE SQM DETAILS → price computation + device toggle
            // Scoped to #assessAppointmentModal only

            (function() {

                const modal = document.getElementById('assessAppointmentModal');
                if (!modal) return;

                const svcpatSelect = modal.querySelector('#svcpat_id_select');
                const svcpatCostDisplay = modal.querySelector('#svcpat_cost_display');
                const treatmentSelect = modal.querySelector('[name="svc_type_treatment"]');
                const deviceCountInput = modal.querySelector('[name="svc_device_count"]');
                const deviceCountCol = modal.querySelector('#termiteDeviceCountCol');
                const deviceCostHint = deviceCountCol?.querySelector('.device-cost-hint');
                const treatmentCol = treatmentSelect?.closest('.col-md-6');
                const locationPriceInput = modal.querySelector('[name="svc_location_price"]');
                const finalPriceInput = modal.querySelector('[name="svc_final_price"]');
                const fixedPriceInput = modal.querySelector('[name="svc_fixed_price"]');
                const sqmInput = modal.querySelector('[name="svc_sqm_initial"]');

                // Only run when termite SQM select exists (termite service only)
                if (!svcpatSelect) return;

                const deviceCostPerUnit = parseFloat(
                    deviceCountInput?.getAttribute('data-device-cost') || 0
                );

                function setComputedPrice(value) {
                    if (fixedPriceInput) fixedPriceInput.value = value;
                    if (finalPriceInput) finalPriceInput.value = value;
                }

                function getSelectedSvcpatCost() {
                    const selected = svcpatSelect.options[svcpatSelect.selectedIndex];
                    return parseFloat(selected?.getAttribute('data-cost') || 0);
                }

                // Checks if the selected SQM detail label is the 1–50 sqm tier (upper bound <= 50)
                function isFixedPriceTier() {
                    const selected = svcpatSelect.options[svcpatSelect.selectedIndex];
                    const label = selected?.text || '';

                    // Match patterns like "1sqm - 50sqm", "1 sqm - 50 sqm", "1-50sqm", etc.
                    const match = label.match(/(\d+)\s*sqm?\s*[-–to]+\s*(\d+)\s*sqm?/i);

                    if (match) {
                        const upperBound = parseInt(match[2]);
                        return upperBound <= 50;
                    }

                    return false;
                }

                // COMPUTATION RULES:
                // 1–50 sqm tier  → svcpat_cost as-is (fixed, no multiplication) + location price
                // 51+  sqm tier  → svcpat_cost × sqm_meters + location price
                // HYBRID adds    → + (device_count × svcpad_cost) on top of either
                function updateTermiteFinalPrice() {

                    const svcpatCost = getSelectedSvcpatCost();
                    const sqmMeters = parseFloat(sqmInput?.value) || 0;
                    const locationPrice = parseFloat(locationPriceInput?.value) || 0;
                    const deviceCount = parseInt(deviceCountInput?.value) || 0;
                    const isHybrid = treatmentSelect?.value === 'HYBRID TREATMENT';

                    // Reflect the sqm cost in the read-only display field
                    if (svcpatCostDisplay) {
                        svcpatCostDisplay.value = svcpatCost.toFixed(2);
                    }

                    // 1–50 sqm: use svcpat_cost directly (already the fixed total from DB)
                    // 51+ sqm:  multiply svcpat_cost × actual sqm meters entered
                    let sqmTotal = isFixedPriceTier() ?
                        svcpatCost :
                        svcpatCost * sqmMeters;

                    // STANDARD: sqmTotal + svc_location_price
                    // HYBRID:   sqmTotal + svc_location_price + (device_count × svcpad_cost)
                    let finalPrice = sqmTotal + locationPrice;

                    if (isHybrid) {
                        finalPrice += deviceCount * deviceCostPerUnit;
                    }

                    setComputedPrice(finalPrice.toFixed(2));
                }

                // DEVICE COUNT VISIBILITY TOGGLE (termite only)
                function toggleTermiteDeviceCount() {

                    if (!treatmentSelect || !deviceCountCol) return;

                    const isHybrid = treatmentSelect.value === 'HYBRID TREATMENT';

                    if (isHybrid) {

                        if (treatmentCol) {
                            treatmentCol.classList.remove('col-md-12');
                            treatmentCol.classList.add('col-md-6');
                        }

                        deviceCountCol.style.display = '';

                        if (deviceCostHint) deviceCostHint.style.display = 'block';

                    } else {

                        if (treatmentCol) {
                            treatmentCol.classList.remove('col-md-6');
                            treatmentCol.classList.add('col-md-12');
                        }

                        deviceCountCol.style.display = 'none';

                        if (deviceCostHint) deviceCostHint.style.display = 'none';

                        // Clear device count when switching back to standard
                        if (deviceCountInput) deviceCountInput.value = '';
                    }

                    // Recompute price after toggling
                    updateTermiteFinalPrice();
                }

                // Bind all relevant inputs
                svcpatSelect.addEventListener('change', updateTermiteFinalPrice);
                if (sqmInput) sqmInput.addEventListener('input', updateTermiteFinalPrice);
                if (treatmentSelect) treatmentSelect.addEventListener('change', toggleTermiteDeviceCount);
                if (deviceCountInput) deviceCountInput.addEventListener('input', updateTermiteFinalPrice);
                if (locationPriceInput) locationPriceInput.addEventListener('input', updateTermiteFinalPrice);

                // Run on load — toggle first so visibility is correct, then price
                toggleTermiteDeviceCount();
                updateTermiteFinalPrice();

            })();

            // SECTION 4 — LOCATION PRICE AUTO-COMPUTE from svc_km_distance
            // Isolated — does not touch any other function.
            // Dispatches 'input' on the location price field so Sections 1 & 3
            // automatically pick up the updated value without any modification.

            (function() {

                const modal = document.getElementById('assessAppointmentModal');
                if (!modal) return;

                const kmInput = modal.querySelector('#svc_km_distance_input');
                const locInput = modal.querySelector('#svc_location_price_input');

                if (!kmInput || !locInput) return;

                // Rates passed from PHP via Blade — read from data attributes on the input itself
                const firstCost = parseFloat(locInput.getAttribute('data-first-cost')) || 0;
                const succeedingCost = parseFloat(locInput.getAttribute('data-succeeding-cost')) || 0;

                // Early exit if no rate is configured for this branch
                if (firstCost === 0 && succeedingCost === 0) return;

                function computeLocationPrice() {

                    const km = parseInt(kmInput.value) || 0;
                    let price = 0;

                    if (km <= 10) {
                        // Any distance within the first 10 KM uses the flat first-cost
                        price = firstCost;
                    } else {
                        // e.g. km = 14 → firstCost + (14 - 10) × succeedingCost
                        price = firstCost + ((km - 10) * succeedingCost);
                    }

                    locInput.value = price.toFixed(2);

                    // Notify Sections 1 & 3 that location price changed — no direct calls needed
                    locInput.dispatchEvent(new Event('input'));
                }

                kmInput.addEventListener('input', computeLocationPrice);

                // Sync on page load using the existing svc_km_distance value
                computeLocationPrice();

            })();

            // SECTION 5 — CONFIRM ASSESSMENT MODAL — full dynamic logic
            // Scoped entirely to #confirmAssessmentAppointmentModal.
            // Mirrors Sections 3 & 4 for the assess modal but uses confirm-specific IDs.

            (function() {

                const modal = document.getElementById('confirmAssessmentAppointmentModal');
                if (!modal) return;

                // Shared refs
                const locationPriceInput = modal.querySelector('#confirmLocationPrice');
                const finalPriceInput = modal.querySelector('#confirmFinalPrice');
                const fixedPriceInput = modal.querySelector('#confirmFixedPrice');

                // Termite-specific refs
                const svcpatSelect = modal.querySelector('#confirmSvcpatSelect');
                const svcpatCostDisplay = modal.querySelector('#confirmSvcpatCostDisplay');
                const treatmentSelect = modal.querySelector('#confirmTreatmentSelect');
                const treatmentCol = modal.querySelector('#confirmTreatmentCol');
                const deviceCountCol = modal.querySelector('#confirmDeviceCountCol');
                const deviceCountInput = modal.querySelector('#confirmDeviceCountInput');
                const deviceCostHint = modal.querySelector('#confirmDeviceCostHint');
                const sqmInput = modal.querySelector('#confirmSqmInput');

                // Non-termite-specific refs
                const initialPriceInput = modal.querySelector('#confirmInitialPrice');
                const packageSelect = modal.querySelector('#confirmPackageSelect');
                const packageCol = modal.querySelector('#confirmPackageCol');
                const sqmCol = modal.querySelector('#confirmSqmCol');

                // KM distance → location price
                const kmInput = modal.querySelector('#confirmKmDistanceInput');

                function setComputedPrice(value) {
                    if (fixedPriceInput) fixedPriceInput.value = value;
                    if (finalPriceInput) finalPriceInput.value = value;
                }

                // KM location price (same formula as assess modal Section 4)
                if (kmInput && locationPriceInput) {

                    const firstCost = parseFloat(locationPriceInput.getAttribute('data-first-cost')) || 0;
                    const succeedingCost = parseFloat(locationPriceInput.getAttribute(
                        'data-succeeding-cost')) || 0;

                    function computeConfirmLocationPrice() {
                        if (firstCost === 0 && succeedingCost === 0) return;

                        const km = parseInt(kmInput.value) || 0;
                        const price = km <= 10 ?
                            firstCost :
                            firstCost + ((km - 10) * succeedingCost);

                        locationPriceInput.value = price.toFixed(2);

                        // Propagate to price functions below
                        locationPriceInput.dispatchEvent(new Event('input'));
                    }

                    kmInput.addEventListener('input', computeConfirmLocationPrice);
                    computeConfirmLocationPrice(); // run on load
                }

                // TERMITE PATH
                if (svcpatSelect) {

                    const deviceCostPerUnit = parseFloat(deviceCountInput?.getAttribute('data-device-cost') ||
                        0);

                    function getSelectedSvcpatCost() {
                        const sel = svcpatSelect.options[svcpatSelect.selectedIndex];
                        return parseFloat(sel?.getAttribute('data-cost') || 0);
                    }

                    function isFixedPriceTier() {
                        const label = svcpatSelect.options[svcpatSelect.selectedIndex]?.text || '';
                        const match = label.match(/(\d+)\s*sqm?\s*[-–to]+\s*(\d+)\s*sqm?/i);
                        return match ? parseInt(match[2]) <= 50 : false;
                    }

                    function updateConfirmTermiteFinalPrice() {
                        const svcpatCost = getSelectedSvcpatCost();
                        const sqmMeters = parseFloat(sqmInput?.value) || 0;
                        const locationPrice = parseFloat(locationPriceInput?.value) || 0;
                        const deviceCount = parseInt(deviceCountInput?.value) || 0;
                        const isHybrid = treatmentSelect?.value === 'HYBRID TREATMENT';

                        if (svcpatCostDisplay) {
                            svcpatCostDisplay.value = svcpatCost.toFixed(2);
                        }

                        const sqmTotal = isFixedPriceTier() ? svcpatCost : svcpatCost * sqmMeters;
                        let finalPrice = sqmTotal + locationPrice;

                        if (isHybrid) {
                            finalPrice += deviceCount * deviceCostPerUnit;
                        }

                        setComputedPrice(finalPrice.toFixed(2));
                    }

                    function toggleConfirmDeviceCount() {
                        const isHybrid = treatmentSelect?.value === 'HYBRID TREATMENT';

                        if (isHybrid) {
                            treatmentCol?.classList.replace('col-md-12', 'col-md-6');
                            if (deviceCountCol) deviceCountCol.style.display = '';
                            if (deviceCostHint) deviceCostHint.style.display = 'block';
                        } else {
                            treatmentCol?.classList.replace('col-md-6', 'col-md-12');
                            if (deviceCountCol) deviceCountCol.style.display = 'none';
                            if (deviceCostHint) deviceCostHint.style.display = 'none';
                            if (deviceCountInput) deviceCountInput.value = '';
                        }

                        updateConfirmTermiteFinalPrice();
                    }

                    svcpatSelect.addEventListener('change', updateConfirmTermiteFinalPrice);
                    sqmInput?.addEventListener('input', updateConfirmTermiteFinalPrice);
                    treatmentSelect?.addEventListener('change', toggleConfirmDeviceCount);
                    deviceCountInput?.addEventListener('input', updateConfirmTermiteFinalPrice);
                    locationPriceInput?.addEventListener('input', updateConfirmTermiteFinalPrice);

                    // Init
                    toggleConfirmDeviceCount();
                    updateConfirmTermiteFinalPrice();
                    return; // Done — skip non-termite block
                }

                // NON-TERMITE PATH
                if (!initialPriceInput || !finalPriceInput) return;

                function updateConfirmNonTermiteFinalPrice() {
                    const locationPrice = parseFloat(locationPriceInput?.value) || 0;
                    const initialPrice = parseFloat(initialPriceInput?.value) || 0;

                    const finalPrice = locationPrice === 0 ? initialPrice :
                        initialPrice === 0 ? locationPrice :
                        locationPrice + initialPrice;

                    setComputedPrice(finalPrice.toFixed(2));
                }

                function toggleConfirmPackageSqm() {
                    if (!packageSelect || !sqmCol) return;
                    const isPackage = packageSelect.value == '1';

                    sqmCol.style.display = isPackage ? 'block' : 'none';

                    if (packageCol) {
                        if (isPackage) {
                            packageCol.classList.replace('col-md-12', 'col-md-6');
                            sqmCol.classList.replace('col-md-4', 'col-md-6');
                        } else {
                            packageCol.classList.replace('col-md-6', 'col-md-12');
                        }
                    }

                    updateConfirmNonTermiteFinalPrice();
                }

                locationPriceInput?.addEventListener('input', updateConfirmNonTermiteFinalPrice);
                initialPriceInput?.addEventListener('input', updateConfirmNonTermiteFinalPrice);
                packageSelect?.addEventListener('change', toggleConfirmPackageSqm);

                // Init
                toggleConfirmPackageSqm();
                updateConfirmNonTermiteFinalPrice();

            })();

        });
    </script>
@endsection