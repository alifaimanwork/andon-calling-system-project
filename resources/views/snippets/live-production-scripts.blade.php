@include('components.commons.websocket')
@include('snippets.temp-live-terminal')
<script>
    $(() => {
        LivePage.initializeLiveProduction();
    });
    var LivePage = new LiveTerminal("{{ date('c') }}", {
        plant: <?php echo json_encode($plant); ?>,
        workCenter: <?php echo json_encode($workCenter); ?>,
        production: <?php echo json_encode($production); ?>,
        productionLines: <?php echo json_encode($productionLines); ?>,

        activeDowntimeEvents: <?php echo (isset($activeDowntimeEvents) ? json_encode($activeDowntimeEvents) : '[]'); ?>,
        workCenterDowntimes: <?php echo (isset($workCenterDowntimes) ? json_encode($workCenterDowntimes) : '[]'); ?>,
        machineDowntimes: <?php echo (isset($machineDowntimes) ? json_encode($machineDowntimes) : '[]'); ?>,
        humanDowntimes: <?php echo (isset($humanDowntimes) ? json_encode($humanDowntimes) : '[]'); ?>,
        downtimes: <?php echo (isset($downtimes) ? json_encode($downtimes) : '[]'); ?>,
    });

    var updatingTerminalData = false;

    function updateTerminalData() {

        if (updatingTerminalData)
            return;

        updatingTerminalData = true;

        $.post("{{ $updateTerminalUrl ?? route('terminal.get.data',[ $plant->uid, $workCenter->uid ]) }}", {
            _token: window.csrf.getToken()
        }, function(data, status, xhr) {
            if (window.LivePage && typeof(window.LivePage) == 'object' && typeof(window.LivePage.terminalDataUpdatedHandler) == 'function')
                window.LivePage.terminalDataUpdatedHandler(data);
        }).always(() => {
            updatingTerminalData = false;
        });
    }
</script>