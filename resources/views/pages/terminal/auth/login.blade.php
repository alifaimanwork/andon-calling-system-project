@extends('layouts.terminal_guest')
@section('head')
    @parent
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url("/images/ingress.png");
            background-size: cover;
        }

        .hz {
            padding-top: 1px;
            background-color: white;
            width: 33em;
            height: 22.5rem;
            margin: auto;
            margin-top: 5em;
            border-radius: 9px;
        }

        .cardform {
            margin: auto;
            width: 21em;
            margin-top: 13px;
            font-size: 1rem;
        }

        .logintitle {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-top: 31px;
            margin-bottom: 23px;
            font-size: 1rem;
        }

        .form-control {
            margin-top: 5px;
            font-size: 1rem;
            text-align: center;
            height: 33px;
        }

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
@endsection
@section('body')
    <main>
        <div class="hz">
            <div class="kadlogin">
                <div class="logintitle">
                    <div class="text-center"><b>SMI IPOS TERMINAL</b>
                        <div class="cardform">
                            <form method="POST" action="{{ route('terminal.login', [$plant->uid, $workCenter->uid]) }}">
                                @csrf
                                <div class="mt-3">
                                    <div class="text-center"><label for="username">STAFF NO</label>
                                        <div>
                                            <input id="username" class="form-control" type="text" name="username"
                                                value="{{ old('username', '') }}" required autofocus />
                                        </div>
                                        <div class="mt-3">
                                            <div class="text-center"><label for="password">PASSWORD</label>
                                                <div>
                                                    <input id="password" class="form-control" type="password"
                                                        name="password" value="{{ old('password', '') }}" required>
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="remember"
                                                        value="" id="remember">
                                                    <label class="form-check-label" for="remember">
                                                        Remember Me
                                                    </label>
                                                </div><br>
                                                <div class="text-center"><button class="submitlogin"
                                                        type="submit">Login</button>
                                                    <div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="text-center mt-2">
                        <span class="text-center text-danger">
                            {{ $errors->first() }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </main>
    <div class="iposfooter">
        COPYRIGHT @ 2022 TALENT SYNERGY SDN BHD (350866-U). ALL RIGHT RESERVED
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        <?php
        
        $reportUrl = '';
        if (isset($_COOKIE['monitor_uid'], $plant->uid)) {
            $reportUrl = route('network-client.report', [$plant->uid, $_COOKIE['monitor_uid']]);
        }
        ?>
        var reportUrl = "{{ $reportUrl }}";
        $(() => {
            if (reportUrl.length > 0) {
                sendReport();
                setInterval(sendReport, 30000);
            }
        });

        function sendReport() {
            //report

            let data = {
                state: 2
            }
            $.post(reportUrl, data);
        }
    </script>
@endsection
