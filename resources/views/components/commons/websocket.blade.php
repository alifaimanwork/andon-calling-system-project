@section('head')
    @parent
    <style>
        .ws-error-message-container {
            pointer-events: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: grid;
            place-items: end end;
        }

        .ws-message-fade-out {
            opacity: 0;
            transition-delay: 1500ms;
            transition-duration: 500ms;
            transition-property: opacity;
        }
    </style>
@endsection

@section('body')
    @parent
    <div class="ws-error-message-container d-none">
        <div id="ws-error-message" class="alert m-2 p-2" role="alert">
            &nbsp;
        </div>
    </div>
@endsection

@section('scripts')
    <?php
    
    $reportUrl = '';
    if (isset($_COOKIE['monitor_uid'], $plant->uid)) {
        $reportUrl = route('network-client.report', [$plant->uid, $_COOKIE['monitor_uid']]);
    }
    ?>
    <script src="{{ asset(mix('js/ws.js')) }}"></script>
    <script>
        var disconnectCount = 0;
        var maxDisconnectCount = 10;
        var reportUrl = "{{ $reportUrl }}";

        $(() => {
            aggresiveMode();
        });

        function aggresiveMode() {
            setInterval(e => {
                let state = window.Echo.connector.pusher.connection.state;
                let ngState = ['unavailable', 'failed', 'disconnected'];
                if (ngState.indexOf(state) < 0)
                    return;

                //retry connect
                if (state == 'unavailable' || state == 'failed')
                    window.Echo.connector.pusher.disconnect();

                disconnectCount++;
                if (disconnectCount > maxDisconnectCount) {
                    window.location.reload();
                }
                window.Echo.connector.pusher.connect();

            }, 1000);
            if (reportUrl.length > 0) {
                sendReport();
                setInterval(sendReport, 30000);
            }
        }

        function sendReport() {
            //report

            let data = {
                state: getWsCode(window.Echo.connector.pusher.connection.state)
            }
            $.post(reportUrl, data);
        }

        function getWsCode(input) {
            if (input == 'connected')
                return 1;
            else
                return 0;
        }
        var wsState = -1;
        window.Echo.connector.pusher.connection.bind('connecting', (payload) => {

            /**
             * All dependencies have been loaded and Channels is trying to connect.
             * The connection will also enter this state when it is trying to reconnect after a connection failure.
             */

            console.log('connecting...');

        });

        window.Echo.connector.pusher.connection.bind('connected', (payload) => {

            /**
             * The connection to Channels is open and authenticated with your app.
             */
            if (wsState == 0) {
                $('#ws-error-message').html('IPOS Server Connected!').removeClass('alert-danger').addClass(
                    'alert-success').addClass('ws-message-fade-out');
                $('.ws-error-message-container').removeClass('d-none');
                setTimeout(function() {
                    $('.ws-error-message-container').addClass('d-none');
                    $('#ws-error-message').removeClass('ws-message-fade-out');
                }, 2000);
            }
            wsState = 1;
            console.log('connected!', payload);
            if (typeof(updateTerminalData) === 'function')
                updateTerminalData();
        });

        window.Echo.connector.pusher.connection.bind('unavailable', (payload) => {

            /**
             *  The connection is temporarily unavailable. In most cases this means that there is no internet connection.
             *  It could also mean that Channels is down, or some intermediary is blocking the connection. In this state,
             *  pusher-js will automatically retry the connection every 15 seconds.
             */
            $('#ws-error-message').html('IPOS Server Connection Failed!').removeClass('alert-success').addClass(
                'alert-danger');
            $('.ws-error-message-container').removeClass('d-none');

            wsState = 0;
            console.log('unavailable', payload);
        });

        window.Echo.connector.pusher.connection.bind('failed', (payload) => {

            /**
             * Channels is not supported by the browser.
             * This implies that WebSockets are not natively available and an HTTP-based transport could not be found.
             */

            $('#ws-error-message').html('IPOS Server Connection Failed!').removeClass('alert-success').addClass(
                'alert-danger');
            $('.ws-error-message-container').removeClass('d-none');
            wsState = 0;
            console.log('failed', payload);

        });

        window.Echo.connector.pusher.connection.bind('disconnected', (payload) => {

            /**
             * The Channels connection was previously connected and has now intentionally been closed
             */
            wsState = 0;
            console.log('disconnected', payload);

        });

        window.Echo.connector.pusher.connection.bind('message', (payload) => {

            /**
             * Ping received from server
             */

            console.log('message', payload);
        });
    </script>
    @parent
@endsection
