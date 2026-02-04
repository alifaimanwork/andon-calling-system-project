@section('scripts')
@parent
<script>
    var autoRedirectDowntime = {
        isDowntimePage: false,
        firstTime: true,
        initialize: function(isDowntimePage = false) {
            this.isDowntimePage = isDowntimePage;
            let _this = this;

            LivePage.listenChanges('downtime-trigger', {
                tag: 1, //machine downtime
                flag: _this.isDowntimePage ? 'all' : 'any'
            }, (cfg, value, summary) => {
                if (_this.firstTime) {
                    _this.firstTime = false;
                    return;
                }

                if (_this.isDowntimePage && !value) {
                    //redirect to progress-status
                    window.location.href = "{{ route('terminal.progress-status.index',[ $plant->uid,$workCenter->uid ]) }}"
                } else if (!_this.isDowntimePage && value) {
                    //redirect to downtime-page
                    window.location.href = "{{ route('terminal.downtime.index',[ $plant->uid,$workCenter->uid ]) }}"
                }

            });
        },
    };
</script>
@endsection