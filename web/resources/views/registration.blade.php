<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <title>Register | MCM EyeCafe</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="CQA Review Center">
        <meta name="author" content="Infinit Soloutions">
        <link rel="shortcut icon" href="{{ asset('images/accounts/icon/icon.png') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ asset('login/css/style.css') }}">
    </head>
    <body>
        @include('sweetalert::alert')
        <section class="ftco-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 col-lg-10">
                        <div class="wrap d-md-flex">
                            <div class="img" style="background-image: url({{ asset('images/accounts/banner/sidebanner.jpg') }});">
                            </div>
                            <div class="login-wrap p-4 p-md-4">
                                <div class="d-flex justify-content-center">
                                    <img src="{{ asset('images/accounts/logo/logo.png') }}" class="img-circle elevation-2" style="width:80px;height:80px;" alt="">
                                </div>
                                <div class="d-flex justify-content-center">
                                    <h5 class="text-center" style="color:gray;">MCM EyeCafe</h5>
                                </div>
                                <p><small>CREATE AN ACCOUNT</small></p>
                                @if(session('errorMessage'))
                                    <p style="color:red;"><small>{{ session('errorMessage') }}</small></p>
                                @endif
                               
                                <form method="POST" action="{{ action('App\Http\Controllers\UserController@register') }}">
                                {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="usr_first_name">First Name <span style="color:red;">*</span></label>
                                            <input class="form-control" type="text" name="usr_first_name" required/>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="usr_middle_name">Middle Name</label>
                                            <input class="form-control" type="text" name="usr_middle_name" />
                                        </div>
                                        <div class="col-md-12">
                                            <label for="usr_last_name">Last Name <span style="color:red;">*</span></label>
                                            <input class="form-control" type="text" name="usr_last_name" required/>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="usr_mobile">Mobile No. (format: 09109005555)<span style="color:red;">*</span></label>
                                            <input class="form-control" type="text" name="usr_mobile" required/>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="usr_email">E-Mail <span style="color:red;">*</span></label>
                                            <input class="form-control" type="email" name="usr_email" required/>
                                        </div>
                                        <div class="col-md-12 col-sm-12 mb-2 mt-2">
                                            <button class="btn btn-secondary btn-block" style="background-color:#313131;color:white;" type="submit"><span class="fa fa-save"></span> Register</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 mb-2">
                                            <p class="text-center"><a href="{{ action('App\Http\Controllers\MainController@main') }}" style="color:#ff6c00"><span class="fa fa-arrow-left"></span> Back to login</a></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="{{ asset('login/js/jquery.min.js') }}"></script>
        <script src="{{ asset('login/js/popper.js') }}"></script>
        <script src="{{ asset('login/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('login/js/main.js') }}"></script>
    </body>
</html>
