<style>
    .hover-effect {
        transition: color 0.3s ease, transform 0.3s ease;
        display: inline-block;
    }

    .hover-effect:hover {
        color: darkslategray !important;
        transform: translateY(-2px) !important;
    }
</style>

<div class="container-fluid">
    <section id="pos">
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="small-box bg-info">
                    <div class="overlay" id="spinner1">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <div class="inner">
                        <h3 class="mb-0">150</h3>
                        <p>Products delivered today</p>
                        <span>Total amount: ₱ 1,700.00</span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <a href="" class="small-box-footer">
                        Create New Delivery <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="small-box bg-success">
                    <div class="overlay" id="spinner2">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <div class="inner">
                        <h3 class="mb-0">150</h3>
                        <p>New Purchase(s) today</p>
                        <span>Total amount: ₱ 1,700.00</span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href=""
                        class="small-box-footer">
                        Create New Purchase <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="small-box bg-secondary">
                    <div class="overlay" id="spinner3">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <div class="inner">
                        <h3 class="mb-0">150</h3>
                        <p>Damage(s) today</p>
                        <span>Total amount: ₱ 1,700.00</span>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chain-broken"></i>
                    </div>
                    <a href=""
                        class="small-box-footer">
                        Record New Damages <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="soa">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info card-outline card-info">
                    <div class="overlay" id="soaspin">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <div class="card-header border-bottom-0 mb-n3 pb-n3">
                        <h3 class="card-title mr-2" style="font-weight: 600"><i class="fas fa-history mr-2"></i>State Of
                            Account </h3>
                        <div class="time-label">
                            <span class="badge bg-green">For August 2024</span>
                        </div>
                        <div class="card-tools">
                            {{-- <span data-toggle="tooltip" title="3 New Messages" class="badge badge-light">3</span>
                            --}}
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            {{-- <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts"
                                data-widget="chat-pane-toggle">
                                <i class="fas fa-comments"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove"><i
                                    class="fas fa-times"></i>
                            </button> --}}
                        </div>
                    </div>
                    <div class="header">
                        <div class="row mt-2">
                            <div class="col-md-6 text-center">
                                <center>
                                    <h6 class="text-bold ml-2"> CLIENTS</h6>
                                </center>
                            </div>
                            <div class="col-md-6 text-center">
                                <center>
                                    <h6 class="text-bold"> SUPPLIER</h6>
                                </center>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-gradient-info">
                                            <span class="info-box-icon"><i class="fa fa-clipboard-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Transaction</span>
                                                <span class="info-box-number">13,648</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 border-right">
                                        <div class="info-box bg-gradient-info">
                                            <span class="info-box-icon"><i class="fa fa-glasses"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Products Sold</span>
                                                <span class="info-box-number">13,648</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-gradient-info">
                                            <span class="info-box-icon"><i class="fa fa-users-between-lines"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Clients Served</span>
                                                <span class="info-box-number">13,648</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 border-right">
                                        <div class="info-box bg-gradient-info">
                                            <span class="info-box-icon"><i class="fa fa-hand-holding-dollar"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Client Purchase Total</span>
                                                <span class="info-box-number">13,648</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <center>
                                    <a href="#" class="small-box-footer text-navy hover-effect">
                                        SOA Client Report <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </center>
                            </div>

                            {{-- ? --}}
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-box bg-gradient-olive">
                                            <span class="info-box-icon"><i class="fa fa-clipboard-list"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Transaction</span>
                                                <span class="info-box-number">13,648</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 border-right">
                                        <div class="info-box bg-gradient-olive">
                                            <span class="info-box-icon"><i class="fa fa-glasses"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Products Received</span>
                                                <span class="info-box-number">13,648</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box bg-gradient-olive">
                                            <span class="info-box-icon"><i class="fa fa-truck-fast"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Active Supplier</span>
                                                <span class="info-box-number">13,648</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 border-right">
                                        <div class="info-box bg-gradient-olive">
                                            <span class="info-box-icon"><i class="fa fa-hand-holding-dollar"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Delivery Expense</span>
                                                <span class="info-box-number">13,648</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <center>
                                    <a href="#" class="small-box-footer text-navy hover-effect">
                                        SOA Supplier Report <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="ic">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-info">
                    <div class="overlay" id="inventoryspin">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <div class="card-header border-bottom-0">
                        <h3 class="card-title" style="font-weight: 600"><i class="fas fa-history"></i> Inventory</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="header">
                        <div class="row mt-1">
                            <div class="col-md-12 text-center">
                                <center>
                                    <h6 class="text-bold"> SUMARRY</h6>
                                </center>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-hover table-striped table-fluid">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Item 0</th>
                                            <th class="text-center">Item 1</th>
                                            <th class="text-center">Item 2</th>
                                            <th class="text-center">Item 3</th>
                                            <th class="text-center">Item 4</th>
                                            <th class="text-center">Item 5</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>ohaha meow</td>
                                            <td>ohaha meow</td>
                                            <td>ohaha meow</td>
                                            <td>ohaha meow</td>
                                            <td>ohaha meow</td>
                                            <td>ohaha meow</td>
                                        </tr>
                                        <tr>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                        </tr>
                                        <tr>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                            <td>brrt brt</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="reports">
        <div class="row mt-3">
            <div class="col-md-4" id="inventory">
                <div class="card-header border-bottom-0 bg-teal">
                    <h3 class="card-title" style="color:white"><i class="fas fa-history"></i> Inventory Summary
                    </h3>
                </div>
                <div class="info-box">
                    <div class="overlay" id="inv-summary">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <span class="info-box-icon bg-teal"><i class="fa fa-cubes-stacked"></i></span>
                    <div class="info-box-content">
                        {{-- <span class="info-box-text">Summary</span> --}}
                        <div class="d-flex">
                            <span class="">Products Received: 41,410 pcs.</span>
                            <span class="ml-auto info-box-number">P 4,410</span>
                        </div>
                        <div class="d-flex">
                            <span class="">Products Sold: 41,410 pcs.</span>
                            <span class="ml-auto info-box-number">P 4,410</span>
                        </div>
                        <div class="d-flex">
                            <span class="">Total Damaged: 4,410 pcs.</span>
                            <span class="ml-auto info-box-number">P 4,410</span>
                        </div>
                        {{-- <div class="progress">
                            <div class="progress-bar bg-info"></div>
                        </div>
                        <span class="progress-description">
                            70% Increase in 30 Days
                        </span> --}}

                    </div>
                </div>
            </div>

            <div class="col-md-4" id="stocks">
                <div class="card-header border-bottom-0 bg-olive">
                    <h3 class="card-title" style="color:white"><i class="fas fa-history"></i> Stock Volume
                    </h3>
                </div>
                <div class="info-box">
                    <div class="overlay" id="volume">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <span class="info-box-icon bg-olive"><i class="fa fa-boxes-packing"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Dispensed this month</span>
                        <span class="info-box-number">1,410 pcs.</span>
                        <div class="progress">
                            <div class="progress-bar bg-info"></div>
                        </div>
                        <span class="progress-description">
                            13,482 total stocks left
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4" id="payroll">
                <div class="card-header border-bottom-0 bg-info">
                    <h3 class="card-title" style="color:white"><i class="fa fa-history"></i> Payrolls
                    </h3>
                </div>
                <div class="info-box">
                    <div class="overlay" id="payrolls">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <span class="info-box-icon bg-info"><i class="fa fa-money-bills"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text mt-0">Employees</span>
                        <span class="info-box-number mb-0">20</span>
                        <div class="progress">
                            <div class="progress-bar bg-info"></div>
                        </div>
                        <span class="progress-description">
                            ₱ 1,700 net salary released last month
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var randomTime1 = Math.floor(Math.random() * (1000 - 250 + 1)) + 100;
        var randomTime2 = Math.floor(Math.random() * (1000 - 250 + 1)) + 100;
        var randomTime3 = Math.floor(Math.random() * (1000 - 250 + 1)) + 100;
        var soaspin = Math.floor(Math.random() * (1000 - 250 + 1)) + 100;
        var inventoryspin = Math.floor(Math.random() * (1000 - 250 + 1)) + 100;

        setTimeout(function () {
            document.getElementById('spinner1').style.display = 'none';
        }, randomTime1);

        setTimeout(function () {
            document.getElementById('spinner2').style.display = 'none';
        }, randomTime2);

        setTimeout(function () {
            document.getElementById('spinner3').style.display = 'none';
        }, randomTime3);

        setTimeout(function () {
            document.getElementById('soaspin').style.display = 'none';
        }, soaspin);

        setTimeout(function () {
            document.getElementById('inventoryspin').style.display = 'none';
        }, inventoryspin);

        // ?

        var reports1 = Math.floor(Math.random() * (2000 - 1000 + 1)) + 1000;
        var reports2 = Math.floor(Math.random() * (2000 - 1000 + 1)) + 1000;
        var reports3 = Math.floor(Math.random() * (2000 - 1000 + 1)) + 1000;

        setTimeout(function () {
            document.getElementById('inv-summary').style.display = 'none';
        }, reports1);

        setTimeout(function () {
            document.getElementById('volume').style.display = 'none';
        }, reports2);

        setTimeout(function () {
            document.getElementById('payrolls').style.display = 'none';
        }, reports3);
    });
</script>