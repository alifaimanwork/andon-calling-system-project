@extends('layouts.guest')

@section('head')
    @parent
    <style>
        body {
            font: 14px sans-serif;
            background: url("/images/ingress.png");
            background-size: cover;
        }

        .hz {
            padding-top: 1px;
            background-color: white;
            width: 33em;
            height: 285px;
            margin: auto;
            margin-top: 5em;
            border-radius: 9px;
        }

        .cardform {
            margin: auto;
            width: 21em;
            margin-top: 13px;
            font-size: 12px;
        }

        .logintitle {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-top: 31px;
            margin-bottom: 23px;
            font-size: 17px;
        }

        /* .form-control {
            margin-top: 5px;
            font-size: 13px;
            text-align: center;
            height: 33px;
        } */

        .submitlogin {
            background-color: #000080;
            border: none;
            padding: 7px 30px 6px 30px;
            color: white;
            border-radius: 7px;
        }

        .iposfooter {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            color: white;
            text-align: center;
            font-size: 12px;
            margin-bottom: 11px;
        }
    </style>

    <style>
        .login-box-container {
            margin-top: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .login-box-container img {
            width: 360px; 
            height: auto;
        }

        .login-box-container .card {
            width: 360px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 16px;

        }

        .login-box-container .card label {
            color: #7e7d7e !important;
            font-weight: 600;
        }

        #visibility-password {
            cursor: pointer;
        }
    </style>
@endsection

@section('body')
    <main>
        {{-- <div class="hz"> --}}
            {{-- <div class="kadlogin">
                <div class="logintitle">
                    <div class="text-center"><b>INGRESS IPOS WEB</b></div>
                </div>
                <div class="cardform">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mt-2">
                            <div class="text-center"><label for="username">STAFF NO</label></div>
                            <input id="username" class="form-control" type="text" name="username"
                                value="{{ old('username', '') }}" required autofocus />
                        </div>
                        <div class="mt-2">
                            <div class="text-center"><label for="password">PASSWORD</label></div>
                            <input id="password" class="form-control" type="password" name="password"
                                value="{{ old('password', '') }}" required>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remember" value="" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember Me
                            </label>
                        </div><br>
                        <div class="text-center"><button class="submitlogin" type="submit">LOGIN</button></div>
                    </form>
                </div>
            </div> --}}



            <div class="container-fluid">
                <div class="login-box-container">
                    <img src="{{ asset('images/iposLogoWhite.png') }}" style=""/>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mt-3">
                                    <div class="text-center"><label for="username">STAFF NO</label></div>
                                    <input id="username" class="form-control mt-2" type="text" name="username"
                                        value="{{ old('username', '') }}" required autofocus />
                                </div>
                                <div class="mt-3">
                                    <div class="text-center"><label for="password">PASSWORD</label></div>
                                    <div class="input-group mt-2">
                                        <input id="password" class="form-control" type="password" name="password"
                                        value="{{ old('password', '') }}" required>
                                        <span class="input-group-text" id="visibility-password" onclick="toggleVisibilityPassword()"><i class="fa-duotone fa-eye-slash"></i></span>
                                    </div>

                                </div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="remember" value="" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember Me
                                    </label>
                                </div><br>
                                <div class="text-center"><button class="submitlogin" type="submit">LOGIN</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-4">
                    {{ $errors->first()}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        {{-- </div> --}}
        <div class="iposfooter">
            COPYRIGHT @ 2024 TALENT SYNERGY SDN BHD (350866-U). ALL RIGHT RESERVED
        </div>
    </main>
@endsection

@section('scripts')
    @parent
    <script>
        function toggleVisibilityPassword() {
            let passwordInput = $('#password');
            let passwordIcon = $('#visibility-password');
            if (passwordInput.attr('type') === "password") {
                passwordInput.attr('type', "text");
                passwordIcon.html('<i class="fa-duotone fa-eye">');
            } else {
                passwordInput.attr('type', "password");
                passwordIcon.html('<i class="fa-duotone fa-eye-slash"></i>');
            }
        }
    </script>
@endsection


