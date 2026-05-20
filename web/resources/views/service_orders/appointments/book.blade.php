@extends('layouts.themes.main')

@section('content')
    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Book Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ action('App\Http\Controllers\AdminController@home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">Appointments</li>
                        <li class="breadcrumb-item active">Book Appointment</li>
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
                    <div class="row">
                        <!-- Table Column -->
                        <div class="col-lg-12 col-md-7">
                            <form method="GET" action="{{ url('service/orders/appointments/clients') }}" class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="search" id="searchInput" class="form-control"
                                        placeholder="Search clients..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="fa fa-search"></span> Search
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <table id="profilingTable" class="table table-hover table-bordered table-sm responsive">
                                <thead>
                                    <tr>
                                        <th style="vertical-align: middle; text-align: center">Name</th>
                                        <th style="vertical-align: middle; text-align: center">Branch</th>
                                        <th style="vertical-align: middle; text-align: center">Address(es)</th>
                                        <th style="vertical-align: middle; text-align: center" width="110px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr>
                                            <td style="vertical-align: middle; text-align: left">
                                                {{ $client->usr_last_name }}, {{ $client->usr_first_name }}
                                                {{ $client->usr_middle_name }}
                                                <br />
                                                <small>{{ $client->usr_email }}</small>
                                                <br />
                                                <small>0{{ $client->usr_mobile }}</small>
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ $client->branch_name }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: left">
                                                @if (isset($addresses[$client->usr_id]) && count($addresses[$client->usr_id]) > 0)
                                                    <ul style="padding-left: 18px; margin-bottom: 0;">
                                                        @foreach ($addresses[$client->usr_id] as $address)
                                                            <li>
                                                                {{ $address->uadd_street }},
                                                                {{ $address->uadd_barangay }},
                                                                {{ $address->uadd_city }},
                                                                {{ $address->uadd_province }},
                                                                {{ $address->uadd_region }}

                                                                @if (!empty($address->uadd_longitude) && !empty($address->uadd_latitude))
                                                                    ({{ $address->uadd_longitude }},
                                                                    {{ $address->uadd_latitude }})
                                                                @endif

                                                                - {{ $address->add_name }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <small class="text-muted">No active address</small>
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                <a class="btn btn-success btn-sm mb-1" href="javascript:void(0)"
                                                    data-toggle="modal"
                                                    data-target="#bookAppointmentModal-{{ $client->usr_id }}">
                                                    <span class="fa fa-calendar"></span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @foreach ($clients as $client)
        {{-- Book Appointment Modal --}}
        <div class="modal fade" id="bookAppointmentModal-{{ $client->usr_id }}" tabindex="-1" role="dialog"
            aria-labelledby="bookAppointmentModal-{{ $client->usr_id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ url('service/orders/appointments/clients/book') }}">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title text-white" id="bookAppointmentModal-{{ $client->usr_id }}">
                                Book Appointment for {{ $client->usr_last_name }},
                                {{ $client->usr_first_name }}
                                {{ $client->usr_middle_name }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" name="usr_id" value="{{ $client->usr_id }}">

                            {{-- ADDRESS --}}
                            <div class="form-group">
                                <label>Address: <span class="text-danger">*</span></label>
                                <select class="form-control" name="uadd_id" required>
                                    @if (isset($addresses[$client->usr_id]) && count($addresses[$client->usr_id]) > 0)
                                        @foreach ($addresses[$client->usr_id] as $address)
                                            <option value="{{ $address->uadd_id }}">
                                                {{ $address->add_name }} —
                                                {{ $address->uadd_street }},
                                                {{ $address->uadd_barangay }},
                                                {{ $address->uadd_city }},
                                                {{ $address->uadd_province }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled>No active address available
                                        </option>
                                    @endif
                                </select>
                            </div>

                            {{-- ASSIGN BRANCH --}}
                            <div class="form-group">
                                <label>Assign Branch: <span class="text-danger">*</span></label>
                                <select class="form-control book-branch-select" name="branch_id"
                                    data-client="{{ $client->usr_id }}" required>
                                    <option value="">— Select Branch —</option>
                                    @foreach ($branches as $branch)
                                        @if ($branch->branch_id != 1)
                                            <option value="{{ $branch->branch_id }}">
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <hr>

                            {{-- PROBLEM TYPE --}}
                            <div class="form-group">
                                <label>Problem Type <span class="text-danger">*</span></label>
                                <select class="form-control book-pest-select" name="svcp_id"
                                    data-client="{{ $client->usr_id }}" required>
                                    <option value="">— Select Problem Type —</option>
                                    @foreach ($servicePackages as $pkg)
                                        <option value="{{ $pkg->svcp_id }}">
                                            {{ $pkg->svcp_pest_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Location (area) — shown when svcp_id != 9 --}}
                            <div class="form-group book-area-group-{{ $client->usr_id }}" style="display:none;">
                                <label>Location <span class="text-danger">*</span></label>
                                <select class="form-control book-area-select" name="svcpa_id"
                                    data-client="{{ $client->usr_id }}">
                                    <option value="">— Select Branch First —</option>

                                    {{-- Pre-render all areas grouped by branch as data attributes --}}
                                    @foreach ($servicePackageAreas as $branchId => $areas)
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->svcpa_id }}" data-branch="{{ $branchId }}"
                                                data-cost="{{ $area->svcpa_cost }}" style="display:none;">
                                                {{ $area->svcpa_area }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <small class="text-muted book-area-cost-{{ $client->usr_id }}"></small>
                            </div>

                            {{-- Termite Treatment Size — shown when svcp_id == 9 --}}
                            <div class="form-group book-termite-group-{{ $client->usr_id }}" style="display:none;">
                                <label>Termite Treatment Size <span class="text-danger">*</span></label>
                                <select class="form-control book-termite-select" name="svcpat_id"
                                    data-client="{{ $client->usr_id }}">
                                    <option value="">— Select Branch First —</option>

                                    @foreach ($termiteAreas as $branchId => $tAreas)
                                        @foreach ($tAreas as $t)
                                            <option value="{{ $t->svcpat_id }}" data-branch="{{ $branchId }}"
                                                style="display:none;">
                                                {{ $t->svcpat_sqm_details }}
                                                (₱{{ number_format($t->svcpat_cost, 2) }})
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            {{-- Problem Description --}}
                            <div class="form-group">
                                <label><strong>Problem Description <span
                                            class="text-muted">(Optional)</span></strong></label>
                                <textarea class="form-control" name="svc_problem_description" rows="3" placeholder="Describe the problem..."></textarea>
                            </div>

                            <hr>

                            {{-- SCHEDULE --}}
                            <div class="form-group">
                                <label>Preferred Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="svca_client_date"
                                    value="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                                    min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Time Window <span class="text-danger">*</span></label>
                                <select class="form-control" name="svca_client_time" required>
                                    <option value="">— Select Time Window —</option>
                                    <option value="08:00">Morning (8:00 AM – 12:00 PM)
                                    </option>
                                    <option value="12:00">Afternoon (12:00 PM – 5:00 PM)
                                    </option>
                                    <option value="17:00">Evening (5:00 PM – 8:00 PM)
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <span class="fa fa-close"></span> Close
                            </button>
                            <button type="submit" class="btn btn-success">
                                <span class="fa fa-check"></span> Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Dynamic Search While Typing --}}
    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let value = this.value.toLowerCase();

            // Select all tables with ID containing "Table"
            let tables = document.querySelectorAll('table[id*="Table"]');

            tables.forEach(function(table) {
                let rows = table.querySelectorAll("tbody tr");

                rows.forEach(function(row) {
                    let text = row.innerText.toLowerCase();
                    row.style.display = text.includes(value) ? "" : "none";
                });
            });
        });
    </script>

    <script>
        (function() {
            const TERMITE_ID = 8;

            function filterOptionsByBranch(select, branchId) {
                const options = select.querySelectorAll('option[data-branch]');
                let hasVisible = false;

                options.forEach(function(opt) {
                    if (opt.dataset.branch == branchId) {
                        opt.style.display = '';
                        hasVisible = true;
                    } else {
                        opt.style.display = 'none';
                        opt.selected = false;
                    }
                });

                // Reset to placeholder
                select.value = '';
                select.querySelector('option:not([data-branch])').textContent =
                    hasVisible ? '— Select —' : '— No options for this branch —';
            }

            function toggleAreaTermite(clientId, pestId, branchId) {
                const areaGroup = document.querySelector('.book-area-group-' + clientId);
                const termiteGroup = document.querySelector('.book-termite-group-' + clientId);

                if (!pestId) {
                    areaGroup.style.display = 'none';
                    termiteGroup.style.display = 'none';
                    return;
                }

                if (parseInt(pestId) === TERMITE_ID) {
                    areaGroup.style.display = 'none';
                    termiteGroup.style.display = 'block';
                    if (branchId) {
                        filterOptionsByBranch(
                            document.querySelector('.book-termite-select[data-client="' + clientId + '"]'),
                            branchId
                        );
                    }
                } else {
                    termiteGroup.style.display = 'none';
                    areaGroup.style.display = 'block';
                    if (branchId) {
                        filterOptionsByBranch(
                            document.querySelector('.book-area-select[data-client="' + clientId + '"]'),
                            branchId
                        );
                    }
                }
            }

            // Branch change
            document.querySelectorAll('.book-branch-select').forEach(function(branchSel) {
                branchSel.addEventListener('change', function() {
                    const clientId = this.dataset.client;
                    const branchId = this.value;
                    const pestId = document.querySelector('.book-pest-select[data-client="' + clientId +
                        '"]').value;

                    toggleAreaTermite(clientId, pestId, branchId);
                });
            });

            // Pest type change
            document.querySelectorAll('.book-pest-select').forEach(function(pestSel) {
                pestSel.addEventListener('change', function() {
                    const clientId = this.dataset.client;
                    const pestId = this.value;
                    const branchId = document.querySelector('.book-branch-select[data-client="' +
                        clientId + '"]').value;

                    toggleAreaTermite(clientId, pestId, branchId);
                });
            });

            // Area cost hint
            document.querySelectorAll('.book-area-select').forEach(function(areaSel) {
                areaSel.addEventListener('change', function() {
                    const clientId = this.dataset.client;
                    const selected = this.options[this.selectedIndex];
                    const costEl = document.querySelector('.book-area-cost-' + clientId);

                    costEl.textContent = selected && selected.dataset.cost ?
                        'Cost: ₱' + parseFloat(selected.dataset.cost).toLocaleString('en-PH', {
                            minimumFractionDigits: 2
                        }) :
                        '';
                });
            });
        })();
    </script>
@endsection