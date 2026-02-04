<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Terminal Network Monitor</title>
    <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
    <style>
        body {
            font-family: 'Roboto Mono';
            background-color: #1e1e1e;
            color: #ddd;
            font-size: 9pt;
        }

        .timestamp {
            color: #90a4ae;
            margin-right: 0.5em;
        }

        .group {
            margin-right: 0.5em;
        }

        .level-info {
            color: #ddd;
        }

        .level-success {
            color: #81c784;
        }

        .level-message {
            color: #64b5f6;
        }

        .level-danger {
            color: #e57373;
        }

        #panel {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            height: 32px;
            background-color: #2e2e2e;
            display: flex;
            align-items: center;
        }

        .websocket-state-container {
            padding-left: 1em;
            padding-right: 1em;
            min-width: 240px;
        }

        .websocket-state.connecting {
            color: #ff9100;
        }

        .websocket-state.connected {
            color: #81c784;
        }

        .websocket-state.unavailable,
        .websocket-state.failed,
        .websocket-state.disconnected {
            color: #e57373;
        }

        .download-log-button {
            border: none;
            background-color: #4e4e4e;
            color: #fff;
        }

        .button-container {
            padding-right: 1em;
        }

        #console {
            padding-bottom: 32px;
        }
    </style>

</head>



<body>

    <div id="console">
    </div>
    <div id="panel" class="d-flex">
        <div class="websocket-state-container">Websocket Status: <span class="websocket-state"></span></div>
        <div class="message-filter-container d-flex">
            <div class="d-flex align-items-center me-1"><input id="vis-info" type="checkbox" class="me-1" checked data-group="info" onchange="toggleMessageVisibility(this)"><label for="vis-info">Info</label></div>
            <div class="d-flex align-items-center me-1"><input id="vis-message" type="checkbox" class="me-1" checked data-group="message" onchange="toggleMessageVisibility(this)"><label for="vis-message">Message</label></div>
            <div class="d-flex align-items-center me-1"><input id="vis-success" type="checkbox" class="me-1" checked data-group="success" onchange="toggleMessageVisibility(this)"><label for="vis-success">Success</label></div>
            <div class="d-flex align-items-center"><input id="vis-danger" type="checkbox" class="me-1" checked data-group="danger" onchange="toggleMessageVisibility(this)"><label for="vis-danger">Critical</label></div>
        </div>
        <div class="button-container flex-fill d-flex justify-content-end">
            <div class="d-flex align-items-center me-1"><input id="autoscroll" type="checkbox" class="me-1" checked onchange="toggleAutoScroll(this)"><label for="autoscroll">Auto-Scroll</label></div>
            <button class="download-log-button" onclick="saveLog()">Save Log</button>
        </div>
    </div>
    <script src="{{ asset(mix('js/app.js')) }}"></script>
    <script src="{{ asset(mix('js/ws.js')) }}"></script>
    <script src="{{ url('js/sha1.js') }}"></script>
    <script>
        let visibility = {
            info: true,
            success: true,
            danger: true,
            message: true,
            test: true,
        }
        let autoscroll = true;
        let debugChannelUid = "";
        let logLines = [];

        function saveLog() {
            let filename = moment(new Date()).format('YYYYMMDDHHmmss') + "_{{ $plant->uid }}_" + "{{ $workCenter->uid }}.log";
            let content = "";
            logLines.forEach(e => {
                content = content.concat(e, "\r\n");
            });

            let sha = hex_sha1(btoa(content)).toLowerCase();

            content += sha;
            downloadContent(filename, content);
        }

        function toggleAutoScroll(sender) {
            autoscroll = $(sender).prop('checked');
        }

        function downloadContent(fileName, content, datatype = "text/plain") {
            var element = document.createElement('a');
            element.setAttribute('href', `data:${datatype};charset=utf-8,` + encodeURIComponent(content));
            element.setAttribute('download', fileName);

            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();

            document.body.removeChild(element);
        };

        function getTimestamp() {
            return moment(new Date()).format('YYYY-MM-DD HH:mm:ss');
        }

        function logMessage(message, group = null, level = "info", addTimestamp = true) {
            let rawMessageLine = "";
            let messageBlock = $(`<div class="level-${level}">`);
            if (addTimestamp) {
                let timestamp = getTimestamp();
                rawMessageLine += "[" + timestamp + "] ";
                messageBlock.append($('<span class="timestamp">').html("[" + timestamp + "]"));
            }
            if (group != null) {
                rawMessageLine += "[" + group + "] ";
                messageBlock.append($(`<span class="group">`).html("[" + group + "]"));
            }
            rawMessageLine += message;
            messageBlock.append($('<span class="message">').html(message));
            logLines.push(rawMessageLine);
            if (!visibility[level])
                messageBlock.addClass('d-none');
            $('#console').append(messageBlock);

            if (autoscroll)
                window.scrollTo(0, document.body.scrollHeight);
        }

        function printCurrentWsState() {
            logMessage(window.Echo.connector.pusher.connection.state, "WS");
        }

        function toggleMessageVisibility(sender) {
            let group = $(sender).data('group');
            let className = `.level-${group}`;

            visibility[group] = $(sender).prop('checked');

            if (visibility[group])
                $(className).removeClass('d-none');
            else
                $(className).addClass('d-none');

        }

        function makeid(length) {
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() *
                    charactersLength));
            }
            return result;
        }

        function aggresiveMode() {
            setInterval(e => {
                let state = window.Echo.connector.pusher.connection.state;
                let ngState = ['unavailable', 'failed', 'disconnected'];
                if (ngState.indexOf(state) < 0)
                    return;

                //retry connect
                if (state == 'unavailable' || state == 'failed')
                    window.Echo.connector.pusher.disconnect();

                window.Echo.connector.pusher.connect();

            }, 1000);
        }
        $(() => {
            aggresiveMode();
            debugChannelUid = makeid(32);

            let http_host = '{{ $serverData["HTTP_HOST"] }}';
            let server_addr = '{{ $serverData["SERVER_ADDR"] }}';
            let server_port = '{{ $serverData["SERVER_PORT"] }}';
            let remote_addr = '{{ $serverData["REMOTE_ADDR"] }}';

            if (http_host.length > 0)
                logMessage(`Host Name: ${http_host}`, null, 'info', false);

            if (server_addr.length > 0)
                logMessage(`Host Address: ${server_addr}`, null, 'info', false);

            if (server_port.length > 0)
                logMessage(`Host Port: ${server_port}`, null, 'info', false);

            if (remote_addr.length > 0)
                logMessage(`Client Address: ${remote_addr}`, null, 'info', false);
            logMessage('Plant: {{ $plant->uid }}', null, 'info', false);
            logMessage('Work Center: {{ $workCenter->uid }}', null, 'info', false);

            logMessage("STARTING...");

            Echo.channel('terminal.{{ $plant->uid }}.{{$workCenter->uid}}');

            Echo.channel('debug.{{ $plant->uid }}.{{ $workCenter->uid }}.' + debugChannelUid)
                .listen('.debug.{{ $plant->uid }}.{{ $workCenter->uid }}.' + debugChannelUid, (e) => {
                    echoTestResult.state = 'stopped';
                    echoTestResult.time_ws_received = new Date();
                    let ms = echoTestResult.time_ws_received.getTime() - echoTestResult.time_send.getTime();
                    logMessage(`WS Echo Received [${ms}ms]`, 'ECHO', 'success');
                });

            printCurrentWsState();

            setInterval(() => {
                let state = window.Echo.connector.pusher.connection.state;

                $('.websocket-state').each((idx, e) => {
                    if ($(e).html() != state) {
                        $(e).removeClass('initialized connecting connected unavailable failed disconnected');
                        $(e).addClass(state);
                        $(e).html(state);
                    }
                });
            }, 500);
            setInterval(() => {
                beginEchoTest();
            }, 60000);
        });

        var echoTestResult = {
            state: 'stopped',
            time_send: null,
            time_received: null,
            time_ws_received: null,
        };

        function beginEchoTest() {

            echoTestResult.state = 'started';


            echoTestResult.time_send = new Date();

            echoTestResult.time_received = null;
            echoTestResult.time_ws_received = null;

            let now = moment(echoTestResult.time_send);
            let state = window.Echo.connector.pusher.connection.state;
            if (state == 'connected') {
                logMessage("XHR Send", 'ECHO', 'info');
            } else
                logMessage("XHR Send without WS connection", 'ECHO', 'danger');


            $.post("{{ route('debug.echo-test',[ $plant->uid, $workCenter->uid ]) }}", {
                _token: window.csrf.getToken(),
                test_size: 10240,
                data: {
                    start_time: now.format('YYYY-MM-DD HH:mm:ss')
                },
                debug_channel: debugChannelUid,
            }, function(data, status, xhr) {
                echoTestResult.state = 'responded';
                echoTestResult.time_received = new Date();
                let ms = echoTestResult.time_received.getTime() - echoTestResult.time_send.getTime();
                logMessage(`XHR Completed [${ms}ms]`, 'ECHO', 'success');
                if (echoTestResult.time_ws_received == null)
                    setTimeout(function() {
                        logMessage(`WS Timed-out! Echo not received!`, 'ECHO', 'danger');
                    }, 5000);


            }).fail(function(e) {
                echoTestResult.state = 'failed';
                echoTestResult.time_received = new Date();
                let ms = echoTestResult.time_received.getTime() - echoTestResult.time_send.getTime();
                logMessage(`XHR Failed (StatusCode: ${e.status},Message: ${e.statusText}) [${ms}ms]`, 'ECHO', 'danger');
            });
        }

        window.Echo.connector.pusher.connection.bind('state_change', (states) => {

            /**
             * All dependencies have been loaded and Channels is trying to connect.
             * The connection will also enter this state when it is trying to reconnect after a connection failure.
             */
            // logMessage("state_change: " + states.previous + " > " + states.current, "WS");
            // console.log('state_change', states);

        });
        window.Echo.connector.pusher.connection.bind('connecting', (payload) => {

            /**
             * All dependencies have been loaded and Channels is trying to connect.
             * The connection will also enter this state when it is trying to reconnect after a connection failure.
             */
            logMessage("connecting", "WS", 'danger');
            // console.log('connecting...');

        });

        window.Echo.connector.pusher.connection.bind('connected', (payload) => {

            /**
             * The connection to Channels is open and authenticated with your app.
             */
            logMessage("connected!", "WS", 'success');
            // console.log('connected!', payload);

        });

        window.Echo.connector.pusher.connection.bind('unavailable', (payload) => {

            /**
             *  The connection is temporarily unavailable. In most cases this means that there is no internet connection.
             *  It could also mean that Channels is down, or some intermediary is blocking the connection. In this state,
             *  pusher-js will automatically retry the connection every 15 seconds.
             */
            logMessage("unavailable", "WS", 'danger');
            // console.log('unavailable', payload);
        });

        window.Echo.connector.pusher.connection.bind('failed', (payload) => {

            /**
             * Channels is not supported by the browser.
             * This implies that WebSockets are not natively available and an HTTP-based transport could not be found.
             */
            logMessage("failed", "WS", 'danger');
            // console.log('failed', payload);

        });

        window.Echo.connector.pusher.connection.bind('disconnected', (payload) => {

            /**
             * The Channels connection was previously connected and has now intentionally been closed
             */
            logMessage("disconnected", "WS", 'danger');
            // console.log('disconnected', payload);

        });
        window.Echo.connector.pusher.connection.bind('message', (payload) => {

            /**
             * Ping received from server
             */
            snippedPayload = {
                event: payload.event,
                channel: payload.channel,
                data: '[...]'
            };
            logMessage("message" + JSON.stringify(snippedPayload), "WS", 'message');
            // console.log('message', payload);
        });
    </script>
</body>

</html>