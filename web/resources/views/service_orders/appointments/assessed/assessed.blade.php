@extends('layouts.themes.main')

@section('content')
    {{-- Content Header --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Assessed Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ action('App\Http\Controllers\AdminController@home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">Appointments</li>
                        <li class="breadcrumb-item active">Assessed</li>
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
                            <form method="GET" action="{{ url('service/orders/appointments/assessed') }}" class="mb-3">
                                <div class="input-group">
                                    <input type="text" name="search" id="searchInput" class="form-control"
                                        placeholder="Search client..." value="{{ request('search') }}">
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
                                        <th class="text-center">SA Number</th>
                                        <th class="text-center">Client</th>
                                        <th class="text-center">Mobile Number</th>
                                        <th class="text-center">Branch</th>
                                        <th class="text-center">Is Termite</th>
                                        <th class="text-center">Is Package</th>
                                        <th class="text-center">Payment Status</th>
                                        <th class="text-center">Approved Date</th>
                                        <th class="text-center">Approved Time</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $appointment)
                                        <tr>
                                            <td style="vertical-align: middle; text-align: center">
                                                SA-{{ str_pad($appointment->svc_sa_number, 6, '0', STR_PAD_LEFT) }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ $appointment->usr_last_name }}, {{ $appointment->usr_first_name }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ $appointment->usr_mobile }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ $appointment->branch_name }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                @if ($appointment->svc_is_termite == 1)
                                                    YES
                                                @else
                                                    NO
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                @if ($appointment->svc_is_package == 1)
                                                    YES
                                                @else
                                                    NO
                                                @endif
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ $appointment->svc_payment_status }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ \Carbon\Carbon::parse($appointment->svca_approved_date)->format('m/d/Y | h:i A') }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ \Carbon\Carbon::parse($appointment->svca_approved_time_from)->format('h:iA') }}
                                                -
                                                {{ \Carbon\Carbon::parse($appointment->svca_approved_time_to)->format('h:iA') }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                {{ $appointment->svc_status }}
                                            </td>
                                            <td style="vertical-align: middle; text-align: center">
                                                <a class="btn btn-primary btn-sm mb-1"
                                                    href="{{ url('service/orders/appointments/assessed/' . $appointment->svc_id) }}">
                                                    <span class="fa fa-eye"></span>
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
<<<<<<< HEAD
=======
    
>>>>>>> 2a548abd798e357264ae261d893a02e31d3fd681
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