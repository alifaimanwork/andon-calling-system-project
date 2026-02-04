@include('components.commons.websocket')
@include('snippets.temp-live-terminal')
<script>
    $(() => {
        LivePage.initializeLiveProduction()
            .initializeEcho();
        //Websocket

    });
    var LivePage = {
        terminalsData: <?php echo json_encode($terminalsData); ?>,
        livePages: {},
        initializeLiveProduction() {
            let _this = this;
            let onLoadServerTime = "{{ date('c') }}"
            _this.terminalsData.forEach(terminalData => {
                let workCenterId = terminalData.workCenter.id;
                _this.livePages[workCenterId] = new LiveTerminal(onLoadServerTime, terminalData);
                _this.livePages[workCenterId].setStrictWorkCenterUpdate().initializeLiveProduction();
            });

            _this.forceUpdate();
            return _this;

        },
        initializeEcho() {
            let _this = this;
            _this.terminalsData.forEach(terminalData => {
                let workCenterUid = terminalData.workCenter.uid;
                let workCenterId = terminalData.workCenter.id;
                if (_this.livePages[workCenterId]) {
                    Echo.channel(`terminal.{{ $plant->uid }}.${workCenterUid}`)
                        .listen('.terminal.data-updated', (e) => {
                            _this.livePages[workCenterId].terminalDataUpdatedHandler(e);
                        })
                        .listen('.terminal.downtime-state-changed', (e) => {
                            _this.livePages[workCenterId].terminalDowntimeStateChangedHandler(e);
                        });
                }
            });
            return _this;
        },
        forceUpdate() {
            let _this = this;
            Object.entries(_this.livePages).forEach(([workCenterId, livePage]) => {
                livePage.forceUpdate();
            });
            return _this;
        },
        //Websocket / Polling Event Handler
        terminalDataUpdatedHandler: function(e) {
            let _this = this;

            if (e) {
                let workCenter = e.workCenter;
                if (workCenter && livePages[workCenter.id])
                    livePages[workCenter.id].terminalDataUpdatedHandler(e);
            }
        },

        listenAnyChanges(workCenterId, callback) {
            let _this = this;
            let livePage = _this.livePages[workCenterId];
            if (!livePage)
                return _this;

            livePage.listenAnyChanges(callback);
            return _this;
        },
        listenChanges(workCenterId, className, data, callback) {

            let _this = this;
            if (workCenterId == '*') {
                //listen to all
                Object.entries(_this.livePages).forEach(([workCenterId, livePage]) => {
                    livePage.listenChanges(className, data, callback);
                });

                return _this;
            }


            let livePage = _this.livePages[workCenterId];
            if (!livePage)
                return _this;

            livePage.listenChanges(className, data, callback);
            return _this;
        }
    }
    var updatingTerminalData = false;



    function updateTerminalData() {
        <?php /* TODO: manual update terminals data
        if (updatingTerminalData)
            return;

        updatingTerminalData = true;

        $.post("{{ route('terminal.get.data',[ $plant->uid, $workCenter->uid ]) }}", {
            _token: window.csrf.getToken()
        }, function(data, status, xhr) {
            if (window.LivePage && typeof(window.LivePage) == 'object' && typeof(window.LivePage.terminalDataUpdatedHandler) == 'function')
                window.LivePage.terminalDataUpdatedHandler(data);
        }).always(() => {
            updatingTerminalData = false;
        }); */ ?>
    }
</script>