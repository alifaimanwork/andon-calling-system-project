@section('terminal-break-button')
    @parent

    <button type="button" class="btn btn-terminal d-none terminal-break-button" style="background-color: #800080"
        onclick="TerminalBreak.breakProduction()">
        <div class="d-flex gap-2 align-items-center"><i style="font-size: 1rem; margin-right: 0.5rem;"
                class="fa-duotone fa-mug-hot"></i><span style="letter-spacing: 0.10417rem;" class="flex-fill">BREAK</span>
        </div>
    </button>
@endsection

@section('modals')
    @parent
    {{-- modal break --}}
    <div class="modal fade" id="break-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content terminal-modal-content">
                <div class="d-flex flex-column w-100 align-items-center p-4">
                    {{-- icon --}}
                    <i style="color:#eed202" class="fa-duotone fa-mug-hot"></i>
                    {{-- message --}}
                    <div class="d-flex flex-column">
                        <span class="terminal-title-text">BREAK TIME!</span>
                        <span class="terminal-text">TO CONTINUE PRODUCTION PLEASE PRESS<br>THE BUTTON BELOW</span>
                    </div>

                    {{-- button --}}
                    <div class="d-flex justify-content-center w-100 mt-3">
                        <button type="button" style="background-color: #E5025D;" class="btn p-2 modal-button"
                            onclick="TerminalBreak.resumeProduction()">RESUME</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmation-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content terminal-modal-content">
                <div class="d-flex flex-column w-100 align-items-center p-4">
                    {{-- icon --}}
                    <i style="color:#eed202" class="fa-regular fa-triangle-exclamation"></i>

                    {{-- message --}}
                    <div class="modal-body text-center text-justify">
                        <span></span>
                    </div>


                    {{-- button --}}
                    <div class="d-flex justify-content-around w-100 mt-3">
                        <button type="button" style="background-color: #E5025D;" class="btn p-2 modal-button dialog-result"
                            data-dialog-result="1" data-bs-dismiss="modal">YES</button>
                        <button type="button" style="background-color: #A8466E" class="btn p-2 modal-button dialog-result"
                            data-dialog-result="0" data-bs-dismiss="modal">NO</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent

    <script>
        // Call Modal Dialog with custom text
        function showConfirmationModal(message) {
            return new Promise(function(resolve, reject) {
                //duplicate the modal template and fill in the text
                var modal = $('#confirmation-modal').clone();
                modal.find('.modal-title').html(message);
                modal.find('.modal-body span').html(message);
                modal.modal('show');
                //wait for the user to click a button
                modal.on('click', '.modal-button', function(e) {
                    var result = $(e.target).data('dialog-result');
                    //delete clone modal and resolve promise
                    modal.remove();
                    if (result == 1)
                        resolve(true);
                    else
                        resolve(false);
                });
            });
        }
    </script>

    <script>
        $(() => {
            TerminalBreak.initialize();
        });
        var TerminalBreak = {
            initialize: function() {
                let _this = this;
                LivePage.listenChanges(
                    'live-downtime-timer', //class
                    {
                        tag: 'plan_break',
                        subtag: 'is_running'
                    }, //Config
                    _this.updateBreakModalVisibility //callback
                ).listenChanges(
                    'schedule-break-time', //class,
                    {},
                    _this.updateBreakButtonVisibility //callback
                );
            },
            updateBreakModalVisibility: function(config, value, summary) {
                // console.log("updateBreakModalVisibility", value);
                if (value) //currently on break
                {
                    $('#break-modal').modal('show');
                } else {
                    $('#break-modal').modal('hide');
                }
            },

            updateBreakButtonVisibility: function(config, value, summary) {
                // console.log("updateBreakButtonVisibility", value);
                if (value) //during break time
                {
                    $('.terminal-break-button').removeClass('d-none');
                } else {
                    $('.terminal-break-button').addClass('d-none');
                }
            },
            resumeProduction: function() {
                //TODO: confirmation using modal
                showConfirmationModal("Confirm to resume break?").then((result) => {
                    if (!result) {
                        return;
                    } else {
                        $.post("{{ route('terminal.progress-status.set.resume-production', [$plant->uid, $workCenter->uid]) }}", {
                            _token: window.csrf.getToken()
                        }, function(data, status, xhr) {
                            //result code
                            const RESULT_OK = 0;
                            const RESULT_INVALID_STATUS = -1;
                            const RESULT_INVALID_PARAMETERS = -2;

                            //TODO: display error message in modal
                            if (data.result === RESULT_OK) {
                                //stopped,try refresh page
                            } else {
                                alert(data.message);
                            }
                        });
                    }
                });

            },
            breakProduction: function() {
                //TODO: confirmation using modal
                showConfirmationModal("Confirm to break?").then((result) => {
                    if (!result) {
                        return;
                    } else {
                        $.post("{{ route('terminal.progress-status.set.break-production', [$plant->uid, $workCenter->uid]) }}", {
                            _token: window.csrf.getToken()
                        }, function(data, status, xhr) {
                            //result code
                            const RESULT_OK = 0;
                            const RESULT_INVALID_STATUS = -1;
                            const RESULT_INVALID_PARAMETERS = -2;

                            //TODO: display error message in modal
                            if (data.result === RESULT_OK) {
                                //stopped,try refresh page
                            } else {
                                alert(data.message);
                            }
                        });
                    }
                });
            },
        }
    </script>
@endsection
