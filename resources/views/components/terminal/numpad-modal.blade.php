@section('head')
    @parent
    <style>
        .terminal-modal-content.numpad .terminal-title{
            color: #eed202;
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
        }

        .terminal-modal-content.numpad .result-numpad {
            display: flex;
            gap: 1.25rem;
            align-items: center;
            width: 100%;
            color: #eed202;
            margin-bottom: 0.2rem;
        }

        .terminal-modal-content.numpad .result-numpad .result-numpad-value {
            background-color: #b6034b; 
            color:white; 
            flex: 1;
            border-radius: 0.2rem;
            padding: 0.5rem;
            text-align: end;
        }

        .result-numpad-value.active {
            animation: thing 2s forwards 1;
        }

        @keyframes thing {
            0% {
                transform: translateX(-0.1rem);
            }
            3% {
                transform: translateX(0.2rem);
            }
            4% {
                transform: translateX(-0.2rem);
            }
            10% {
                transform: translateX(0.1rem);
            }
            100% {
                transform: translateX(0);
            }
        }

        .terminal-modal-content.numpad i {
            font-size: 3rem;
            cursor: pointer;
        }

        .terminal-modal-content.numpad i:hover {
            font-size: 3rem;
            cursor: pointer;
            text-shadow: #e9d53da6 0.1rem 0 0.3rem;
            #FC0 ;
        }

        .numpad-keyup {
            margin-top: 1rem;
            display: grid;
            width: 100%;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(4, 1fr);
            grid-gap: 0.5rem;
        }

        .numpad-keyup button.btn-key {
            background-color: #E5025D;
            color: white;
            border-radius: 0.8rem;
            /* height: 4rem;
            width: 4rem; */
            padding: 0.5rem;
        }

        .error-numpad span{
            color: #eed202 !important;
            font-size: 1.2rem !important;
        }
    </style>
@endsection

@section('modals')
    @parent
    {{-- modal numpad keyboard --}}
    <div class="modal fade" id="numpad-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content terminal-modal-content numpad">
                <div class="d-flex flex-column w-100 align-items-center p-4">
                    {{-- title --}}
                    <span id="numpad-modal-title" class="terminal-title align-self-start"></span>

                    <div class="result-numpad">
                        <span class="result-numpad-value">0</span>
                    </div>

                    <div class="error-numpad">
                        <span class="error-numpad-value"></span>
                    </div>
                    <div class="numpad-keyup">
                        <button type="button" class="btn btn-key num-key">1</button>
                        <button type="button" class="btn btn-key num-key">2</button>
                        <button type="button" class="btn btn-key num-key">3</button>
                        <button type="button" class="btn btn-key clear-key" style="background-color: red">CLR</button>
                        <button type="button" class="btn btn-key delete-key" style="grid-row: 2/4; grid-column:4/5;background-color: #c2034f">DEL</button>
                        <button type="button" class="btn btn-key num-key">4</button>
                        <button type="button" class="btn btn-key num-key">5</button>
                        <button type="button" class="btn btn-key num-key">6</button>
                        <button type="button" class="btn btn-key num-key">7</button>
                        <button type="button" class="btn btn-key num-key">8</button>
                        <button type="button" class="btn btn-key num-key">9</button>
                        <button type="button" class="btn btn-key cancel-key" style="grid-column: 1/2; grid-row: 4/5; background-color: #c2034f">CANCEL</button>
                        <button type="button" class="btn btn-key num-key" style="grid-column: 2/3; grid-row: 4/5">0</button>
                        <button type="button" class="btn btn-key set-key" style="grid-column: 3/5; grid-row: 4/5; background-color: #dd2f75">SET</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('scripts')
    @parent
    <script>

        // CHECK CODE TYPES
        const PAGE_DIE_CHANGE = 0;
        const PAGE_FIRST_PRODUCT_CONFIRMATION = 1;
        const PAGE_REJECT = 2;
        const PAGE_PENDING = 3;
        const PAGE_REWORK = 4;

        function showNumpadModal(ref, title, check){
            let value = $(ref).html();
            // console.log(target.html(), title);
            return new Promise(function(resolve,reject){
                let modal = $('#numpad-modal').clone();
                modal.find('#numpad-modal-title').html(title);
                modal.find('.result-numpad-value').html(value);

                modal.on('click', '.num-key', function(e){
                    let currentValue = parseInt(modal.find('.result-numpad-value').html());
                    let targetNum = parseInt($(e.target).html());
                    

                    let result = null;
                    if(currentValue == '0'){
                        result = targetNum;
                    } else {
                        result = String(currentValue) + String(targetNum);
                    }

                    modal.find('.result-numpad-value').html(result);
                    // let currentValue = modal.find('.result-numpad-value').html();
                    
                    // let num = (currentValue !== '0' ? currentValue : '') + $(e.target).html();
                    // console.log(num);
                    // modal.find('.result-numpad-value').html(num);
                }).on('click', '.clear-key', function(){
                    modal.find('.result-numpad-value').html('0');
                }).on('click', '.delete-key', function(){
                    let currentValue = modal.find('.result-numpad-value').html();
                    let num = currentValue.substring(0, currentValue.length - 1);

                    modal.find('.result-numpad-value').html(parseInt(num) ? num : '0');
                }).on('click', '.cancel-key', function(){
                    modal.hide();
                    $('.modal-backdrop').remove();
                    modal.remove();
                    resolve(true);
                }).on('click', '.set-key', function(){

                    switch(check){
                        case PAGE_DIE_CHANGE:
                            $(ref).html(modal.find('.result-numpad-value').html());

                            closeNumpadModal(modal);
                            resolve(true);
                            break;
                        case PAGE_FIRST_PRODUCT_CONFIRMATION:

                            let tag = $(ref).data('tag');
                            if (!LivePage.tabCurrentProductionLine || !tag)
                                break;
                            
                            if (!pendingPayloads[LivePage.tabCurrentProductionLine.id])
                                pendingPayloads[LivePage.tabCurrentProductionLine.id] = {};

                            if (!pendingPayloads[LivePage.tabCurrentProductionLine.id][tag])
                                pendingPayloads[LivePage.tabCurrentProductionLine.id][tag] = 0;

                            let otherUncommitted = 0;
                            Object.entries(pendingPayloads[LivePage.tabCurrentProductionLine.id]).forEach(([key, value]) => {
                                if (key != tag)
                                    otherUncommitted += value;
                            });
                            
                            let okPart = LivePage.tabCurrentProductionLine.actual_output - LivePage.tabCurrentProductionLine.reject_count - otherUncommitted;
                            let numCount = parseInt(modal.find('.result-numpad-value').html());
                            
                            if(numCount > okPart){
                                numpadError(modal,"Please Input less than or equal to " + okPart + ".");
                                break;
                            };

                            pendingPayloads[LivePage.tabCurrentProductionLine.id][tag] = numCount;
                            updateUncommitRejectSettingsCounter();

                            closeNumpadModal(modal);
                            resolve(true);

                            break;

                        case PAGE_REJECT:

                            let rejectTypeId = ref.data('reject-type-id');
                            let currentProductionLineId = ref.data('production-line-id');

                            if (!pageData.uncommitRejectData[currentProductionLineId])
                                pageData.uncommitRejectData[currentProductionLineId] = {};

                            if (!pageData.uncommitRejectData[currentProductionLineId][rejectTypeId])
                                pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] = 0;

                            if (pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] <= 0)
                                pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] = 0;

                            let otherUncommittedReject = 0;
                            Object.entries(pageData.uncommitRejectData[currentProductionLineId]).forEach(([key, value]) => {
                                if (key != rejectTypeId)
                                    otherUncommittedReject += value;
                            });

                            let okPartReject = LivePage.tabCurrentProductionLine.actual_output - LivePage.tabCurrentProductionLine.reject_count - LivePage.tabCurrentProductionLine.pending_count - otherUncommittedReject;
                            let numCountReject = parseInt(modal.find('.result-numpad-value').html());
                            
                            if(numCountReject > okPartReject){
                                numpadError(modal,"Please Input less than or equal to " + okPartReject + ".");
                                break;
                            };

                            pageData.uncommitRejectData[currentProductionLineId][rejectTypeId] = numCountReject;

                            updateUncommitedRejectCount();

                            closeNumpadModal(modal);
                            resolve(true);
                            // console.log(otherUncommittedReject);
                            break;
                        case PAGE_PENDING:

                            let productionLineIdPending = $(ref).data('production-line-id');
                            if (!productionLineIdPending)
                                break;

                            let productionLinePending = LivePage.getProductionLineById(productionLineIdPending);
                            if (!uncommitPendingCount[productionLineIdPending])
                                uncommitPendingCount[productionLineIdPending] = 0;

                            let okPartPending = LivePage.tabCurrentProductionLine.actual_output - LivePage.tabCurrentProductionLine.reject_count - LivePage.tabCurrentProductionLine.pending_count;
                            let numCountPending = parseInt(modal.find('.result-numpad-value').html());

                            if(numCountPending > okPartPending){
                                numpadError(modal,"Please Input less than or equal to " + okPartPending + ".");
                                break;
                            };

                            uncommitPendingCount[productionLineIdPending] = numCountPending;
                            updateUncommitPendingCount();

                            closeNumpadModal(modal);
                            resolve(true);
                            break;
                        case PAGE_REWORK:
                            console.log('page rework');

                            let reworkType = $(ref).data('type');
                            let productionLineIdRework = $(ref).data('production-line-id');
                            
                            if (!uncommitRework[productionLineIdRework])
                                uncommitRework[productionLineIdRework] = {
                                    ok: 0,
                                    ng: 0
                                };

                            let otherUncommitedRework = 0;
                            if(reworkType == 'ok'){
                                otherUncommitedRework = uncommitRework[productionLineIdRework].ng;
                            } else if(reworkType == 'ng'){
                                otherUncommitedRework = uncommitRework[productionLineIdRework].ok;
                            }

                            let pendingRework = (productionLines[productionLineIdRework] ? productionLines[productionLineIdRework].data.pending_count - productionLines[productionLineIdRework].data.pending_ok - productionLines[productionLineIdRework].data.pending_ng - otherUncommitedRework : 0);
                            
                            let numCountRework = parseInt(modal.find('.result-numpad-value').html());

                            if(numCountRework > pendingRework){
                                numpadError(modal,"Please Input less than or equal to " + pendingRework + ".");
                                break;
                            };

                            if(reworkType == 'ok'){
                                uncommitRework[productionLineIdRework]['ok'] = numCountRework;
                            } else if (reworkType == 'ng'){
                                uncommitRework[productionLineIdRework]['ng'] = numCountRework;
                            }

                            updateUncommitCounter();

                            closeNumpadModal(modal);
                            resolve(true);
                            break;
                    };

                });

                modal.modal('show');
            });
        }

        function numpadError(modal, message = 'Invalid value'){
            modal.find('.result-numpad-value').addClass('active');

            setTimeout(function(){
                modal.find('.result-numpad-value').removeClass('active');
            }, 1000);

            modal.find('.error-numpad-value').html(message);
        }

        function closeNumpadModal(modal){
            modal.hide();
            $('.modal-backdrop').remove();
            modal.remove();
        }
    </script>
@endsection