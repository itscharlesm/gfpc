@extends('layouts.themes.main')

@section('content')
    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Technicians</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ action('App\Http\Controllers\AdminController@home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">Profiling</li>
                        <li class="breadcrumb-item active">Technicians</li>
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
                        <div class="col-md-12">
                            <a class="btn btn-danger btn-md mb-3" href="{{ url('profiling/technicians/deleted') }}">
                                <span class="fa fa-archive"></span> Deleted Technicians
                            </a>
                            @if (session('SUPERADMIN') == '1' || session('ADMIN') == '1')
                                <button type="button" class="btn btn-success mb-3" data-toggle="modal"
                                    data-target="#addTechnicianModal">
                                    <span class="fa fa-plus"></span> Add Technician
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <!-- Table Column -->
                        <div class="col-lg-12 col-md-7">
                            <form method="GET" action="{{ url('profiling/technicians/active') }}" class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="search" id="searchInput" class="form-control"
                                        placeholder="Search technicians..." value="{{ request('search') }}">
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
                                        <th style="vertical-align: middle; text-align: center" width="130px">Availabilities
                                        </th>
                                        <th style="vertical-align: middle; text-align: center" width="110px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($technicians as $technician)
                                        <tr>
                                            <td style="vertical-align: middle; text-align: left">
                                                {{ $technician->usr_last_name }}, {{ $technician->usr_first_name }}
                                                {{ $technician->usr_middle_name }}
                                                <br />
                                                <small>{{ $technician->usr_email }}</small>
                                                <br />
                                                <em><small>Last login: {{ getLastLogin($technician->usr_id) }}</small></em>
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ $technician->branch_name }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                @if (!empty($technician->availabilities))
                                                    <span class="badge badge-success">
                                                        {{ $technician->availabilities }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">None</span>
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                <a class="btn btn-warning btn-sm mb-1" href="javascript:void(0)"
                                                    data-toggle="modal"
                                                    data-target="#updateAvailabilityModal-{{ $technician->usr_id }}">
                                                    <span class="fa fa-edit"></span>
                                                </a>
                                                <a class="btn btn-info btn-sm mb-1" href="javascript:void(0)"
                                                    data-toggle="modal"
                                                    data-target="#resetModal-{{ $technician->usr_id }}">
                                                    <span class="fa fa-key"></span>
                                                </a>
                                                @if (session('SUPERADMIN') == '1' || session('ADMIN') == '1')
                                                    <a class="btn btn-danger btn-sm mb-1" href="javascript:void(0)"
                                                        data-toggle="modal"
                                                        data-target="#deleteModal-{{ $technician->usr_id }}">
                                                        <span class="fa fa-trash"></span>
                                                    </a>
                                                @endif
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

    {{-- Add Technician Modal --}}
    <div class="modal fade" id="addTechnicianModal" tabindex="-1" role="dialog" aria-labelledby="addTechnicianModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ url('profiling/technicians/add') }}" method="POST">
                @csrf
                {{-- Hidden fields --}}
                <input type="hidden" name="usr_password" value="{{ md5('123456') }}">
                <input type="hidden" name="usr_code" value="123456">
                <input type="hidden" name="region" id="add_hidden_region" value="">
                <input type="hidden" name="province" id="add_hidden_province" value="">
                <input type="hidden" name="municipality" id="add_hidden_municipality" value="">
                <input type="hidden" name="barangay" id="add_hidden_barangay" value="">

                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white" id="addTechnicianModalLabel">
                            <span class="fa fa-plus text-white"></span> Add Technician
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            {{-- First Name --}}
                            <div class="col-md-4 mb-3">
                                <label for="add_first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_first_name" name="usr_first_name"
                                    placeholder="First Name" required>
                            </div>

                            {{-- Middle Name --}}
                            <div class="col-md-4 mb-3">
                                <label for="add_middle_name">Middle Name</label>
                                <input type="text" class="form-control" id="add_middle_name" name="usr_middle_name"
                                    placeholder="Middle Name">
                            </div>

                            {{-- Last Name --}}
                            <div class="col-md-4 mb-3">
                                <label for="add_last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_last_name" name="usr_last_name"
                                    placeholder="Last Name" required>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label for="add_email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="add_email" name="usr_email"
                                    placeholder="Email" required>
                            </div>

                            {{-- Mobile --}}
                            <div class="col-md-6 mb-3">
                                <label for="add_mobile">
                                    Mobile Number
                                    <small class="text-muted">(9123456789)</small>
                                </label>
                                <input type="number" class="form-control" id="add_mobile" name="usr_mobile"
                                    placeholder="Mobile Number" oninput="this.value=this.value.slice(0,10)">
                            </div>

                            {{-- Branch --}}
                            <div class="col-md-12 mb-3">
                                <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                <select class="form-control" id="branch_id" name="branch_id" required>
                                    @foreach ($branches as $branch)
                                        @if ($branch->branch_id != 1)
                                            <option value="{{ $branch->branch_id }}">
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- Street --}}
                            <div class="col-md-12 mb-3">
                                <label for="add_street">Street</label>
                                <input type="text" class="form-control" id="add_street" name="street"
                                    placeholder="Street / House No. / Bldg.">
                            </div>

                            {{-- Region --}}
                            <div class="col-md-3 mb-3">
                                <label for="add_region">Region</label>
                                <select id="add_region" class="form-control">
                                    <option value="">-- SELECT REGION --</option>
                                    @foreach ($regions as $r)
                                        <option value="{{ $r->reg_id }}" data-name="{{ $r->reg_name }}">
                                            {{ $r->reg_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Province --}}
                            <div class="col-md-3 mb-3">
                                <label for="add_province">Province</label>
                                <select id="add_province" class="form-control" disabled>
                                    <option value="">-- SELECT PROVINCE --</option>
                                </select>
                            </div>

                            {{-- Municipality --}}
                            <div class="col-md-3 mb-3">
                                <label for="add_municipality">City/Municipality</label>
                                <select id="add_municipality" class="form-control" disabled>
                                    <option value="">-- SELECT MUNICIPALITY --</option>
                                </select>
                            </div>

                            {{-- Barangay --}}
                            <div class="col-md-3 mb-3">
                                <label for="add_barangay">Barangay</label>
                                <select id="add_barangay" class="form-control" disabled>
                                    <option value="">-- SELECT BARANGAY --</option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="add_barangay">Availability</label>
                                <div class="row">
                                    @php
                                        $days = [
                                            'Monday',
                                            'Tuesday',
                                            'Wednesday',
                                            'Thursday',
                                            'Friday',
                                            'Saturday',
                                            'Sunday',
                                        ];
                                    @endphp

                                    @foreach ($days as $day)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="availability[]"
                                                    value="{{ $day }}" id="avail_{{ $day }}">
                                                <label class="form-check-label" for="avail_{{ $day }}">
                                                    {{ $day }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <span class="fa fa-close"></span> Close
                        </button>
                        <button type="submit" class="btn btn-success">
                            <span class="fa fa-save"></span> Save Technician
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @foreach ($technicians as $technician)
        {{-- Update Availability Modal --}}
        <div class="modal fade" id="updateAvailabilityModal-{{ $technician->usr_id }}" tabindex="-1" role="dialog"
            aria-labelledby="updateAvailabilityModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ url('profiling/technicians/update/availability', $technician->usr_id) }}"
                    method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title text-black" id="updateAvailabilityModalLabel">
                                Update Avaialability
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

                                $userAvail = DB::table('user_availabilities')
                                    ->where('usr_id', $technician->usr_id)
                                    ->pluck('uavail_active', 'uavail_name')
                                    ->toArray();
                            @endphp

                            <div class="form-group">
                                <label>Availability for:
                                    <span style="color:black;">
                                        {{ $technician->usr_first_name }}
                                        {{ $technician->usr_last_name }}
                                    </span>
                                </label>
                            </div>

                            <div class="row">
                                @foreach ($days as $day)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="availability[]"
                                                value="{{ $day }}"
                                                id="edit_avail_{{ $technician->usr_id }}_{{ $day }}"
                                                {{ !empty($userAvail[$day]) && $userAvail[$day] == 1 ? 'checked' : '' }}>

                                            <label class="form-check-label"
                                                for="edit_avail_{{ $technician->usr_id }}_{{ $day }}">
                                                {{ $day }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <span class="fa fa-close"></span> Close
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <span class="fa fa-save"></span> Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Reset Password Modal --}}
        <div class="modal fade" id="resetModal-{{ $technician->usr_id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST"
                        action="{{ action('App\Http\Controllers\ProfilingController@technicians_reset_password', [$technician->usr_id]) }}">
                        @csrf
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title text-black" id="exampleModalLabel">
                                Please Confirm
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to RESET this technician's password
                                to
                                <strong>123456</strong>?
                            </p>
                            <small>{{ $technician->usr_first_name }}
                                {{ $technician->usr_last_name }}<br>{{ $technician->usr_email }}</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <span class="fa fa-close"></span> Close
                            </button>
                            <button type="submit" class="btn btn-info">
                                <span class="fa fa-refresh"></span> Confirm Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div class="modal fade" id="deleteModal-{{ $technician->usr_id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST"
                        action="{{ action('App\Http\Controllers\ProfilingController@technicians_delete', [$technician->usr_id]) }}">
                        @csrf
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title text-white" id="exampleModalLabel">
                                Please Confirm
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to <strong>DELETE</strong>
                                technician
                                <strong>{{ $technician->usr_first_name }}
                                    {{ $technician->usr_last_name }}</strong>?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
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

    <script>
        $(function() {
            // Initialize Select2 Elements with classic theme
            $('.select2').select2({
                theme: "classic"
            });
        });
    </script>

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
        document.addEventListener('DOMContentLoaded', function() {
            const addRegionSel = document.getElementById('add_region');
            const addProvSel = document.getElementById('add_province');
            const addMuniSel = document.getElementById('add_municipality');
            const addBrgySel = document.getElementById('add_barangay');

            const addHiddenRegion = document.getElementById('add_hidden_region');
            const addHiddenProv = document.getElementById('add_hidden_province');
            const addHiddenMuni = document.getElementById('add_hidden_municipality');
            const addHiddenBrgy = document.getElementById('add_hidden_barangay');

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const baseUrl = window.location.origin;

            function addResetSelect(el, placeholder) {
                el.innerHTML = `<option value="">${placeholder}</option>`;
                el.disabled = true;
            }

            async function addLoadOptions(el, endpoint, textKey, valueKey, placeholder) {
                el.innerHTML = '<option value="">Loading...</option>';
                try {
                    const response = await fetch(`${baseUrl}${endpoint}`, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    const data = await response.json();
                    el.innerHTML = `<option value="">${placeholder}</option>`;
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            el.insertAdjacentHTML('beforeend',
                                `<option value="${item[valueKey]}" data-name="${item[textKey]}">${item[textKey]}</option>`
                            );
                        });
                        el.disabled = false;
                    } else {
                        el.innerHTML = `<option value="">No options available</option>`;
                    }
                } catch (err) {
                    console.error(`Error loading ${el.id}:`, err);
                    el.innerHTML = `<option value="">Error loading data</option>`;
                }
            }

            addRegionSel.addEventListener('change', function() {
                const regionId = this.value;
                const regionName = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                addHiddenRegion.value = regionName;

                addResetSelect(addProvSel, '-- SELECT PROVINCE --');
                addResetSelect(addMuniSel, '-- SELECT MUNICIPALITY --');
                addResetSelect(addBrgySel, '-- SELECT BARANGAY --');
                addHiddenProv.value = '';
                addHiddenMuni.value = '';
                addHiddenBrgy.value = '';

                if (regionId) {
                    addLoadOptions(addProvSel, `/locations/${regionId}/provinces`, 'prov_name', 'prov_id',
                        '-- SELECT PROVINCE --');
                }
            });

            addProvSel.addEventListener('change', function() {
                const provId = this.value;
                const provName = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                addHiddenProv.value = provName;

                addResetSelect(addMuniSel, '-- SELECT MUNICIPALITY --');
                addResetSelect(addBrgySel, '-- SELECT BARANGAY --');
                addHiddenMuni.value = '';
                addHiddenBrgy.value = '';

                if (provId) {
                    addLoadOptions(addMuniSel, `/locations/${provId}/municipalities`, 'mun_name', 'mun_id',
                        '-- SELECT MUNICIPALITY --');
                }
            });

            addMuniSel.addEventListener('change', function() {
                const muniId = this.value;
                const muniName = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
                addHiddenMuni.value = muniName;

                addResetSelect(addBrgySel, '-- SELECT BARANGAY --');
                addHiddenBrgy.value = '';

                if (muniId) {
                    addLoadOptions(addBrgySel, `/locations/${muniId}/barangays`, 'brg_name', 'brg_id',
                        '-- SELECT BARANGAY --');
                }
            });

            addBrgySel.addEventListener('change', function() {
                addHiddenBrgy.value = this.options[this.selectedIndex]?.getAttribute('data-name') || '';
            });

            // Reset modal fields on close
            document.getElementById('addTechnicianModal').addEventListener('hidden.bs.modal', function() {
                this.querySelector('form').reset();
                addResetSelect(addProvSel, '-- SELECT PROVINCE --');
                addResetSelect(addMuniSel, '-- SELECT MUNICIPALITY --');
                addResetSelect(addBrgySel, '-- SELECT BARANGAY --');
                addHiddenRegion.value = '';
                addHiddenProv.value = '';
                addHiddenMuni.value = '';
                addHiddenBrgy.value = '';
            });
        });
    </script>
@endsection