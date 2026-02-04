@section('top-nav-bar')
@parent
<?php //TODO: use png logo 
?>
<div class="iposweb-top-title d-flex justify-content-center align-items-center" style="pointer-events: none;">
    <div id="top-nav-bar-title">{{ $workCenter->name ?? '' }}</div>
</div>

<div class="iposweb-top-nav-bar d-flex justify-content-between align-items-center px-2">
    <div class="d-flex align-items-center">
        <div class="iposweb-top-nav-bar-logo"><img src="{{ asset('images/SMI_logo.jpeg') }}"></div>
        <div class="font-mono iposweb-top-nav-clock ms-1" style="width: 14.46rem; word-wrap: break-word; line-height: 0.8rem">SYARIKAT METAL INDUSTRIES <br>OF MALAYSIA SDN BHD</div>
    </div>

    <div class="top-nav-bar-tr-container d-flex align-items-center">
        <div class="flex-fill">
            <div class="font-mono text-end live-clock text-nowrap iposweb-top-nav-clock" data-clock="plant" data-format="dddd DD/MM/YYYY"></div>
            <div class="font-mono text-end live-clock text-nowrap iposweb-top-nav-clock" data-clock="plant" data-format="HH:mm:ss"></div>
        </div>
    </div>
</div>

@endsection
@section('head')
@parent
<style>
    .iposweb-top-nav-clock {
        font-size: 0.9rem;
    }

    .iposweb-top-nav-bar-logo img {
        height: 2.2rem;
    }

    .iposweb-top-title {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 3.1rem;
        z-index: 100;
        font-family: 'Poppins', sans-serif;
        font-size: 2rem;
        color: white;
    }

    .iposweb-top-nav-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        font-family: 'Poppins', sans-serif;
        color: white;
        height: 3.1rem;
        background-color: #171745;
        z-index: 99;
    }

    main {
        margin-top: 3.1rem;
        height: calc(100vh - 3.1rem);
        overflow-y: auto;
    }

    .top-nav-bar-tr-container {
        width: 240px;
    }

    .font-mono {
        font-family: 'Roboto Mono', monospace;
    }
</style>
@endsection
@section('scripts')
@parent
<script>

    $(() => {
        topNavBarClock.initialize();
    });
    topNavBarClock = {

        serverTime: <?php echo (new \DateTime())->getTimestamp() * 1000; ?>,
        plantLocalTimeOffset: <?php echo (isset($plant) ? $plant->getLocalDateTime()->getOffset() : 0) * 1000; ?>,
        serverTimeOffset: 0,
        initialize: function() {
            let now = new Date();
            this.serverTimeOffset = this.serverTime - now.getTime() + now.getTimezoneOffset() * 60000;

            this.updateClock();
            setInterval(this.updateClock, 250);
        },
        updateClock: function() {
            let now = new Date();

            const localTime = now.getTime();
            const serverTime = localTime + topNavBarClock.serverTimeOffset;
            const plantTime = serverTime + topNavBarClock.plantLocalTimeOffset;
            let clocks = {
                local: moment(new Date(localTime)),
                server: moment(new Date(serverTime)),
                plant: moment(new Date(plantTime))
            }

            $('.live-clock').each(function(index, e) {
                let elem = $(e);
                let format = elem.data('format');
                let clockName = elem.data('clock');
                if (typeof(clockName) === 'undefined')
                    clockName = 'local';

                if (!(clockName in clocks))
                    return;

                let clock = clocks[clockName];


                elem.html(clock.format(format))
            });
        }
    }
</script>
@endsection