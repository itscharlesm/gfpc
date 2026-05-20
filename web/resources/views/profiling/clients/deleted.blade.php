@extends('layouts.themes.main')

@section('content')
    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Clients</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ action('App\Http\Controllers\AdminController@home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">Profiling</li>
                        <li class="breadcrumb-item active">Clients</li>
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
                            <a class="btn btn-success btn-md mb-3" href="{{ url('profiling/clients/active') }}">
                                <span class="fa fa-users"></span> Clients
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Table Column -->
                        <div class="col-lg-12 col-md-7">
                            <form method="GET" action="{{ url('profiling/clients/active') }}" class="mb-3">
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
                                                <em><small>Last login: {{ getLastLogin($client->usr_id) }}</small></em>
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ $client->branch_name }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                <a class="btn btn-primary btn-sm mb-1" href="javascript:void(0)"
                                                    data-toggle="modal"
                                                    data-target="#viewClientModal-{{ $client->usr_id }}">
                                                    <span class="fa fa-eye"></span>
                                                </a>
                                                @if (session('SUPERADMIN') == '1' || session('ADMIN') == '1')
                                                    <a class="btn btn-success btn-sm mb-1" href="javascript:void(0)"
                                                        data-toggle="modal"
                                                        data-target="#restoreModal-{{ $client->usr_id }}">
                                                        <span class="fa fa-refresh"></span>
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

    @foreach ($clients as $client)
        {{-- View Client Modal --}}
        <div class="modal fade" id="viewClientModal-{{ $client->usr_id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Client Details</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            &times;
                        </button>
                    </div>

                    <div class="modal-body">

                        {{-- PERSONAL INFO --}}
                        <h6 class="font-weight-bold text-black">Personal Information
                        </h6>
                        <hr class="mt-1 mb-2">

                        <div class="row mb-2">
                            <div class="col-md-4">
                                <small class="text-muted">First Name</small>
                                <div>{{ $client->usr_first_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Middle Name</small>
                                <div>{{ $client->usr_middle_name ?? '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Last Name</small>
                                <div>{{ $client->usr_last_name }}</div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4">
                                <small class="text-muted">Branch</small>
                                <div>{{ $client->branch_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Email</small>
                                <div>{{ $client->usr_email }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Mobile</small>
                                <div>{{ $client->usr_mobile }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted">Birthdate</small>
                                <div>
                                    {{ $client->usr_birth_date ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Age</small>
                                <div>
                                    @if ($client->usr_birth_date)
                                        {{ \Carbon\Carbon::parse($client->usr_birth_date)->age }}
                                        years old
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- ADDRESSES --}}
                        <h6 class="font-weight-bold text-black">Addresses</h6>
                        <hr class="mt-1 mb-2">

                        <div class="row">
                            @php
                                $clientAddresses = $addresses[$client->usr_id] ?? collect();
                            @endphp

                            @if ($clientAddresses->isEmpty())
                                <div class="col-12">
                                    <div class="alert alert-light text-center mb-0">
                                        No address registered
                                    </div>
                                </div>
                            @else
                                @foreach ($clientAddresses as $addr)
                                    <div class="col-md-6 mb-2">
                                        <div
                                            class="card border {{ $addr->uadd_active ? 'border-success' : 'border-secondary' }}">
                                            <div class="card-body p-2">

                                                <strong>
                                                    {{ $addr->add_name ?? 'Address' }}
                                                </strong>

                                                @if ($addr->uadd_street)
                                                    <div class="small">
                                                        {{ $addr->uadd_street }}
                                                    </div>
                                                @endif

                                                @php
                                                    $location = collect([
                                                        $addr->uadd_barangay,
                                                        $addr->uadd_city,
                                                        $addr->uadd_province,
                                                    ])
                                                        ->filter()
                                                        ->implode(', ');
                                                @endphp

                                                @if ($location)
                                                    <div class="small">
                                                        {{ $location }}</div>
                                                @endif

                                                @if ($addr->uadd_region)
                                                    <div class="small text-muted">
                                                        {{ $addr->uadd_region }}
                                                    </div>
                                                @endif

                                                <div class="mt-1">
                                                    <span
                                                        class="badge {{ $addr->uadd_active ? 'badge-success' : 'badge-secondary' }}">
                                                        {{ $addr->uadd_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">
                            <span class="fa fa-close"></span> Close
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- Restore Modal --}}
        <div class="modal fade" id="restoreModal-{{ $client->usr_id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST"
                        action="{{ action('App\Http\Controllers\ProfilingController@clients_restore', [$client->usr_id]) }}">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title text-white" id="exampleModalLabel">
                                Please Confirm
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to <strong>RESTORE</strong> client
                                <strong>{{ $client->usr_first_name }}
                                    {{ $client->usr_last_name }}</strong>?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <span class="fa fa-close"></span> Close
                            </button>
                            <button type="submit" class="btn btn-success">
                                <span class="fa fa-refresh"></span> Confirm Delete
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
@endsection