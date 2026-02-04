<script>
    class LiveTerminal {

        constructor(onLoadServerTime, terminalData) {
            this.onLoadServerTime = onLoadServerTime;
            this.terminalData = terminalData;

            //TEMP
            // let serverTime = new Date(onLoadServerTime);
            // this.serverTimeOffset = (serverTime.getTime() / 1000) - (new Date()).getTime() / 1000;

        };
        onLoadServerTime = "";
        terminalData = {};
        liveProduction = null;
        availableRejectTypes = {};
        liveDataChangedCallback = null;
        callbacks = {};

        strictWorkCenterUpdate = false; //all tag must have work-center-uid

        listenAnyChanges(callback) {
            let _this = this;
            _this.liveDataChangedCallback = callback;
            _this.liveProduction.lastSummary = null;

            return _this;
        };
        listenChanges(className, data, callback) {
            let _this = this;
            if (!_this.callbacks[className])
                _this.callbacks[className] = [];

            _this.callbacks[className].push({
                data: data,
                prev: null,
                callback: callback
            });
            return _this;
        };
        setStrictWorkCenterUpdate(enabled = true) {
            this.strictWorkCenterUpdate = (enabled ? this.terminalData.workCenter : false);
            return this;
        };
        initializeLiveProduction() {
            let _this = this;
            this.liveProduction = new LiveProduction(this.onLoadServerTime, this.terminalData);
            this.liveProduction.listenAnyChanges((e) => {
                _this.liveDataChanged();
                if (typeof(_this.liveDataChangedCallback) === 'function') {
                    _this.liveDataChangedCallback(e);
                }
            });
            this.reloadAvailableRejectTypes();
            return this;
        };
        reloadAvailableRejectTypes() {
            let _this = this;
            _this.availableRejectTypes = {};
            if (Array.isArray(_this.terminalData.productionLines)) {
                _this.terminalData.productionLines.forEach(productionLine => {
                    if (!productionLine.part_data || !productionLine.part_data.part_reject_types)
                        return;
                    productionLine.part_data.part_reject_types.forEach(rejectType => {
                        if (!_this.availableRejectTypes[rejectType.id])
                            _this.availableRejectTypes[rejectType.id] = rejectType;
                    });
                });
            }
        };
        forceUpdate() {
            let _this = this;
            _this.liveProduction.lastSummary = null;
            _this.liveProduction.tick();
        };
        liveDataChanged(e) {
            let _this = this;
            _this.updateLiveData();
        };
        formatValue(e, value) {
            let _this = this;
            let format = $(e).data('format');
            let process = $(e).data('process');

            let output = value;
            if (process && _this.processor[process] && typeof(_this.processor[process]) == 'function')
                output = _this.processor[process](e, output);

            if (format && _this.formatter[format] && typeof(_this.formatter[format]) == 'function')
                output = _this.formatter[format](e, output);

            return output;
        };
        formatCallbackValue(data, value) {
            let _this = this;
            let format = data['format'];
            let process = data['process'];

            let output = value;
            if (process && _this.processor[process] && typeof(_this.processor[process]) == 'function')
                output = _this.processor[process](e, output);

            if (format && _this.formatter[format] && typeof(_this.formatter[format]) == 'function')
                output = _this.formatter[format](e, output);

            return output;
        };
        renderer = { //add more renderer here
            "caret-indicator-positive": function(e, value, summary) {
                let indicator = $(e).find('i');
                if (indicator.length <= 0) {
                    //indicator not exist, create one
                    indicator = $('<i>').appendTo($(e));
                }
                if (value > 0) {
                    //caret up
                    indicator.removeClass('fa-caret-down fa-equals indicator-negative indicator-none moving-down')
                        .addClass('fa-solid fa-caret-up indicator-positive moving-up');
                } else if (value < 0) {
                    //caret down
                    indicator.removeClass('fa-caret-up fa-equals indicator-positive indicator-none moving-up')
                        .addClass('fa-solid fa-caret-down indicator-negative moving-down');
                } else {
                    //no caret
                    indicator.removeClass('fa-caret-up fa-caret-down indicator-positive indicator-negative moving-up moving-down')
                        .addClass('fa-solid fa-equals indicator-none');
                }

                return undefined;
            },
            "caret-indicator-negative": function(e, value, summary) {
                let indicator = $(e).find('i');
                if (indicator.length <= 0) {
                    //indicator not exist, create one
                    indicator = $('<i>').appendTo($(e));
                }
                if (value > 0) {
                    //caret up
                    indicator.removeClass('fa-caret-down fa-equals indicator-positive indicator-none moving-down')
                        .addClass('fa-solid fa-caret-up indicator-negative moving-up');
                } else if (value < 0) {
                    //caret down
                    indicator.removeClass('fa-caret-up fa-equals indicator-negative indicator-none moving-up')
                        .addClass('fa-solid fa-caret-down indicator-positive moving-down');
                } else {
                    //no caret
                    indicator.removeClass('fa-caret-up fa-caret-down indicator-negative indicator-positive moving-up moving-down')
                        .addClass('fa-solid fa-equals indicator-none');
                }

                return undefined;
            },
        };
        processor = { //add more processor here
            "countdown": function(e, value) {
                let countdownValue = $(e).data('countdown');
                if (!countdownValue)
                    countdownValue = 0;

                if (typeof(value === 'number') && isFinite(value)) {
                    return countdownValue - value;
                } else
                    return value;
            }
        };
        formatter = { //add more formatter here
            "total_hours_floor": function(e, value) {
                let hour = (Math.floor(value / 3600) % 60);
                if (hour < 10)
                    return `0${hour.toFixed(0)}`;
                else
                    return `${hour.toFixed(0)}`;
            },
            "duration_minutes": function(e, value) {
                //value = total duration
                let min = (Math.floor(value / 60) % 60);
                if (min < 10)
                    return `0${min.toFixed(0)}`;
                else
                    return `${min.toFixed(0)}`;
            },
            "duration_seconds": function(e, value) {
                let sec = (Math.floor(value) % 60);
                if (sec < 10)
                    return `0${sec.toFixed(0)}`;
                else
                    return `${sec.toFixed(0)}`;
            },
            "timer_full": function(e, value) {
                if (!(typeof(value === 'number') && isFinite(value)))
                    return value;

                let totalSeconds = value;
                let hours = Math.floor(totalSeconds / 3600);
                totalSeconds -= hours * 3600;

                let minutes = Math.floor(totalSeconds / 60);
                totalSeconds -= minutes * 60;
                let seconds = Math.floor(totalSeconds);

                let result = "";
                if (hours < 10)
                    result += `0${hours.toFixed(0)}:`;
                else
                    result += `${hours.toFixed(0)}:`;

                if (minutes < 10)
                    result += `0${minutes.toFixed(0)}:`;
                else
                    result += `${minutes.toFixed(0)}:`;

                if (seconds < 10)
                    result += `0${seconds.toFixed(0)}`;
                else
                    result += `${seconds.toFixed(0)}`;
                return result;
            },
            "percentage_rounded": function(e, value) {
                if (typeof(value === 'number') && isFinite(value)) {
                    let output = (value * 100).toFixed(0);
                    return output;
                }
                return value;
            },
            "percentage_variance_rounded": function(e, value) {
                if (typeof(value === 'number') && isFinite(value)) {
                    let output = ((1 - value) * 100).toFixed(0);
                    return output;
                }
                return value;
            }
        };
        getProductionLineByLineNo(lineNo, productionLines = null) {
            let _this = this;

            if (!productionLines)
                productionLines = _this.terminalData.productionLines;


            for (let index = 0; index < productionLines.length; index++) {
                const productionLine = productionLines[index];
                if (productionLine.line_no == lineNo)
                    return productionLine;
            };
            return null;
        };
        getProductionLineById(productionLineId, refProductionLines = null) {
            let _this = this;

            let productionLines = refProductionLines;
            if (!productionLines) {
                productionLines = _this.terminalData.productionLines;
            }


            for (let index = 0; index < productionLines.length; index++) {
                let productionLine = productionLines[index];

                if (productionLine.id == productionLineId) {
                    return productionLine;
                }
            };

            return null;

        };
        getProductionLineByElement(e) {
            let _this = this;
            var summary = _this.liveProduction.currentSummary;
            let lineNo = $(e).data('line-no');
            let lineId = $(e).data('production-line-id');
            let tag = $(e).data('tag');
            let format = $(e).data('format');

            let productionLine = null;
            if (lineId)
                productionLine = _this.getProductionLineById(lineId, summary.production_lines);


            if (!productionLine)
                productionLine = _this.getProductionLineByLineNo(lineNo, summary.production_lines);

            return productionLine;
        };
        getProductionLineByData(data) {
            let _this = this;
            var summary = _this.liveProduction.currentSummary;
            let lineNo = data['line-no'];
            let lineId = data['production-line-id'];
            let tag = data['tag'];
            let format = data['format'];

            let productionLine = null;
            if (lineId)
                productionLine = _this.getProductionLineById(lineId, summary.production_lines);


            if (!productionLine)
                productionLine = _this.getProductionLineByLineNo(lineNo, summary.production_lines);

            return productionLine;
        };
        getRejectTypeByTag(tag) {
            let _this = this;


            for (const [rejectTypeId, rejectType] of Object.entries(_this.availableRejectTypes)) {

                if (rejectType.tag == tag) {

                    return rejectType;
                }
            }

            return null;
        };
        clearCallbackPrevValue() {
            let _this = this;
            Object.entries(_this.callbacks).forEach(([key, value]) => {
                value.forEach(e => {
                    e.prev = null;
                });
            });

        };
        selectElements(className) {
            let _this = this;

            if (_this.strictWorkCenterUpdate)
                return $(`${className}[data-work-center-uid="${_this.strictWorkCenterUpdate.uid}"`);
            else
                return $(`${className}`);
        };
        updateLiveData() {
            let _this = this;
            let summary = _this.liveProduction.currentSummary;


            /**
             * ClassName: live-downtime-timer
             * Data: tag (Downtime timer tag) (by_id, all, unplan, plan, unplan_human, unplan_machine, plan_die_change, unplan_die_change)
             * Data: downtime-id (Downtime id, for data-tag = tag_id)
             */
            let liveDowntimeTimers = summary.downtimes;
            _this.selectElements('.live-downtime-timer')
                .each((idx, e) => {
                    let tag = $(e).data('tag');
                    let subtag = $(e).data('subtag');
                    if (subtag == null)
                        subtag = 'total';
                    let timer = liveDowntimeTimers[tag];

                    if (tag == 'by_id') {
                        let downtime_id = $(e).data('downtime-id');
                        if (timer)
                            timer = timer[downtime_id];
                    }
                    let value = 0
                    if (timer && timer[subtag])
                        value = timer[subtag];

                    _this.updateDomContent(e, _this.formatValue(e, value), summary);

                });

            if (_this.callbacks['live-downtime-timer']) {
                _this.callbacks['live-downtime-timer'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let subtag = e.data['subtag'];
                    if (subtag == null)
                        subtag = 'total';
                    let timer = liveDowntimeTimers[tag];

                    if (timer && tag == 'by_id') {
                        let downtime_id = e.data['downtime-id'];
                        timer = timer[downtime_id];
                    }
                    let value = 0
                    if (timer && timer[subtag])
                        value = timer[subtag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };
            /**
             * ClassName: live-runtime-timer
             * Data: tag (Runtime timer tag) (good, plan)
             */
            let liveRuntimeTimers = summary.runtimes;

            _this.selectElements('.live-runtime-timer')
                .each((idx, e) => {
                    let tag = $(e).data('tag');
                    let subtag = $(e).data('subtag');
                    if (subtag == null)
                        subtag = 'total';
                    let timer = liveRuntimeTimers[tag];

                    let value = 0
                    if (timer && timer[subtag])
                        value = timer[subtag];

                    _this.updateDomContent(e, _this.formatValue(e, value), summary);

                });

            if (_this.callbacks['live-runtime-timer']) {
                _this.callbacks['live-runtime-timer'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let subtag = e.data['subtag'];
                    if (subtag == null) //null or undefined
                        subtag = 'total';

                    let timer = liveRuntimeTimers[tag];

                    let value = 0
                    if (timer && timer[subtag])
                        value = timer[subtag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };

            /**
             * ClassName: schedule-break-time
             * Data: -
             */

            _this.selectElements('.schedule-break-time').each((idx, e) => {

                let value = 0;
                if (_this.terminalData.production &&
                    _this.terminalData.production.schedule_data &&
                    Array.isArray(_this.terminalData.production.breaks)) {

                    let now = new Date();
                    _this.terminalData.production.schedule_data.breaks.forEach(breakSlot => {
                        if (!breakSlot.start_time || !breakSlot.end_time)
                            return;

                        let start = new Date(breakSlot.start_time);
                        let end = new Date(breakSlot.end_time);
                        if (now >= start && now < end)
                            value = 1;
                    });
                };

                _this.updateDomContent(e, _this.formatValue(e, value), summary);

            });

            if (_this.callbacks['schedule-break-time']) {
                _this.callbacks['schedule-break-time'].forEach(e => {
                    //e
                    /* nodata
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let value = 0;

                    if (_this.terminalData.production &&
                        _this.terminalData.production.schedule_data &&
                        Array.isArray(_this.terminalData.production.schedule_data.breaks)) {

                        let now = new Date();

                        _this.terminalData.production.schedule_data.breaks.forEach(breakSlot => {

                            if (!breakSlot.start_time || !breakSlot.end_time)
                                return;

                            let start = new Date(breakSlot.start_time);
                            let end = new Date(breakSlot.end_time);
                            if (now >= start && now < end)
                                value = 1;
                        });
                    };

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };

            /**
             * ClassName: live-production-line-data
             * Data: tag (Production Line Properties) ()
                actual_output: 0
                availability: 0
                oee: 0
                performance: 1
                plan_quantity: 960
                quality: 1
                reject_count: 0
                standard_output: 33
                variance: 33
                plan_variance
             */
            _this.selectElements('.live-production-line-data').each((idx, e) => {

                let tag = $(e).data('tag');
                let productionLine = _this.getProductionLineByElement(e);
                let value = null;
                if (productionLine && productionLine._live)
                    value = productionLine._live[tag];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);
            });

            if (_this.callbacks['live-production-line-data']) {
                _this.callbacks['live-production-line-data'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let productionLine = _this.getProductionLineByData(e.data);
                    let value = null;
                    if (productionLine && productionLine._live)
                        value = productionLine._live[tag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };


            /**
             * ClassName: production-data
             * Data: tag (Production Properties) ()

             */
            _this.selectElements('.production-data').each((idx, e) => {


                let tag = $(e).data('tag');
                let production = _this.terminalData.production;
                let value = null;
                if (production)
                    value = production[tag];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);
            });

            if (_this.callbacks['production-data']) {
                _this.callbacks['production-data'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let production = _this.terminalData.production;
                    let value = null;
                    if (production)
                        value = production[tag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };

            /**
             * ClassName: live-production-data
             * Data: tag (Production Properties) ()
                    average_oee
                    average_availability
                    average_performance
                    average_quality
             */
            _this.selectElements('.live-production-data').each((idx, e) => {


                let tag = $(e).data('tag');
                let production = _this.terminalData.production;
                let value = null;
                if (production && production._live)
                    value = production._live[tag];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);
            });

            if (_this.callbacks['live-production-data']) {
                _this.callbacks['live-production-data'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let production = _this.terminalData.production;
                    let value = null;
                    if (production && production._live)
                        value = production._live[tag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };

            /**
             * ClassName: work-center-data
             * Data: tag (Work Center Properties) ()
                break_schedule_id: 1
                created_at: "2022-07-13T00:49:54.000000Z"
                current_production_id: 4
                dashboard_layout_id: 2
                downtime_state: -3
                enabled: 1
                factory_id: 2
                id: 3
                name: "R2G"
                production_line_count: "2"
                status: 1
                _status: { status: <status>, downtime_state: <downtime_state> }
                uid: "r2g"
                updated_at: "2022-07-13T10:04:47.000000Z"
             */

            _this.selectElements('.work-center-data').each((idx, e) => {
                let tag = $(e).data('tag');
                let workCenter = _this.terminalData.workCenter;

                let value = null;
                if (workCenter)
                    value = workCenter[tag];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);

            });

            if (_this.callbacks['work-center-data']) {
                _this.callbacks['work-center-data'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let workCenter = _this.terminalData.workCenter;
                    let value = null;
                    if (workCenter)
                        value = workCenter[tag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };





            /**
             * ClassName: production-line-data
             * Data: tag (Production Line Properties) ()
                actual_output: 0
                availability: null
                created_at: "2022-07-01T09:50:46.000000Z"
                id: 1
                line_no: 1
                ng_count: 0
                oee: null
                ok_count: 0
                part: {id: 3, plant_id: 1, work_center_id: 3, part_no: 'N1WB-E21468-AA_', line_no: 1, …}
                part_data: {id: 3, plant_id: 1, work_center_id: 3, part_no: 'N1WB-E21468-AA_', line_no: 1, …}
                pending_count: 0
                performance: null
                production_id: 1
                production_order: {id: 1, plant_id: 1, part_id: 3, work_center_id: 3, order_no: '241106339416', …}
                production_order_id: 1
                quality: null
                reject_count: 0
                reject_summary: null
                result_dpr: null
                result_oee: null
                result_productivity: null
                result_quality: null
                standard_output: 1320
                updated_at: "2022-07-01T09:50:46.000000Z"
             */

            _this.selectElements('.production-line-data').each((idx, e) => {
                let tag = $(e).data('tag');
                let productionLine = _this.getProductionLineByElement(e);

                let value = null;
                if (productionLine)
                    value = productionLine[tag];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);

            });

            if (_this.callbacks['production-line-data']) {
                _this.callbacks['production-line-data'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let productionLine = _this.getProductionLineByData(e.data);
                    let value = null;
                    if (productionLine)
                        value = productionLine[tag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };


            /*
                actual_output: 0
                created_at: "2022-07-01T12:54:57.000000Z"
                id: 1
                line_no: 1
                ng_count: 0
                ok_count: 0
                order_no: "241106339416"
                part: {id: 3, plant_id: 1, work_center_id: 3, part_no: 'N1WB-E21468-AA_', line_no: 1, …}
                part_id: 3
                pending_count: 0
                plan_finish: "2022-06-27 19:00:00"
                plan_quantity: 960
                plan_start: "2022-06-27 08:00:00"
                plant_id: 1
                pps_factory: "2"
                pps_filehash: "a5d91e2e3103f090825225bd50f19c155e5d0420c5612a25b5ffc4a8029e1133"
                pps_filename: "PPS240720221400.csv"
                pps_line: "R2G"
                pps_part_name: "CHAN FRT DR WDO GL, RH"
                pps_part_no: "N1WB-E21468-AA_"
                pps_plant: "2401"
                pps_seq: 1
                pps_shift: "D/S"
                pps_status: "REL"
                status: 2
                unit_of_measurement: "PCS"
                updated_at: "2022-07-01T12:55:00.000000Z"
                work_center_id: 3
            */
            _this.selectElements('.production-order-data').each((idx, e) => {

                let tag = $(e).data('tag');
                let productionLine = _this.getProductionLineByElement(e);

                let value = null;
                if (productionLine && productionLine.production_order)
                    value = productionLine.production_order[tag];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);
            });


            if (_this.callbacks['production-order-data']) {
                _this.callbacks['production-order-data'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let productionLine = _this.getProductionLineByData(e.data);
                    let value = null;
                    if (productionLine && productionLine.production_order)
                        value = productionLine.production_order[tag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };


            /*
                created_at: "2022-07-01T12:54:52.000000Z"
                cycle_time: 30
                enabled: 1
                id: 3
                laravel_through_key: 1
                line_no: 1
                name: "CHAN FRT DR WDO GL, RH"
                opc_part_id: 11
                packaging: 100
                part_no: "N1WB-E21468-AA_"
                part_reject_types: (13) [{…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}, {…}]
                plant_id: 1
                reject_target: 0.1
                setup_time: 600
                side: "RH"
                updated_at: "2022-07-01T12:54:52.000000Z"
                work_center_id: 3
            */
            _this.selectElements('.part-data').each((idx, e) => {

                let tag = $(e).data('tag');
                let productionLine = _this.getProductionLineByElement(e);

                let value = null;
                if (productionLine && productionLine.part_data)
                    value = productionLine.part_data[tag];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);
            });

            if (_this.callbacks['part-data']) {
                _this.callbacks['part-data'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let productionLine = _this.getProductionLineByData(e.data);
                    let value = null;
                    if (productionLine && productionLine.part_data)
                        value = productionLine.part_data[tag];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };


            _this.selectElements('.reject-item-count').each((idx, e) => {
                let tag = $(e).data('tag');
                let rejectTypeId = $(e).data('reject-type-id');
                let rejectType = null;
                if (rejectTypeId && _this.availableRejectTypes[rejectTypeId]) {
                    rejectType = _this.availableRejectTypes[rejectTypeId];
                } else if (tag) {
                    rejectType = _this.getRejectTypeByTag(tag);
                }

                if (!rejectType)
                    return; //invalid reject type id

                let rejectGroupId = rejectType.reject_group_id;
                rejectTypeId = rejectType.id;

                if (!rejectGroupId || !rejectTypeId)
                    return; //invalid no group

                let productionLine = _this.getProductionLineByElement(e);


                let value = 0;
                if (productionLine.reject_summary && productionLine.reject_summary[rejectGroupId] && productionLine.reject_summary[rejectGroupId][rejectTypeId])
                    value = productionLine.reject_summary[rejectGroupId][rejectTypeId];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);
            });

            if (_this.callbacks['reject-item-count']) {
                _this.callbacks['reject-item-count'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let rejectTypeId = e.data['reject-type-id'];
                    let rejectType = null;
                    if (rejectTypeId && _this.availableRejectTypes[rejectTypeId]) {
                        rejectType = _this.availableRejectTypes[rejectTypeId];
                    } else if (tag) {
                        rejectType = _this.getRejectTypeByTag(tag);
                    }

                    if (!rejectType)
                        return; //invalid reject type id

                    let rejectGroupId = rejectType.reject_group_id;
                    rejectTypeId = rejectType.id;

                    if (!rejectGroupId || !rejectTypeId)
                        return; //invalid no group

                    let productionLine = _this.getProductionLineByData(e.data);


                    let value = 0;
                    if (productionLine.reject_summary && productionLine.reject_summary[rejectGroupId] && productionLine.reject_summary[rejectGroupId][rejectTypeId])
                        value = productionLine.reject_summary[rejectGroupId][rejectTypeId];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };

            _this.selectElements('.reject-group-count').each((idx, e) => {
                let rejectGroupId = $(e).data('reject-group-id');

                if (!rejectGroupId || !rejectGroupId)
                    return; //invalid no group

                let productionLine = _this.getProductionLineByElement(e);


                let value = 0;
                if (productionLine && productionLine.reject_summary && productionLine.reject_summary[rejectGroupId] && productionLine.reject_summary[rejectGroupId]['total'])
                    value = productionLine.reject_summary[rejectGroupId]['total'];

                _this.updateDomContent(e, _this.formatValue(e, value), summary);
            });

            if (_this.callbacks['reject-group-count']) {
                _this.callbacks['reject-group-count'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let rejectGroupId = e.data['reject-group-id'];

                    if (!rejectGroupId || !rejectGroupId)
                        return; //invalid no group

                    let productionLine = _this.getProductionLineByData(e.data);


                    let value = 0;
                    if (productionLine.reject_summary && productionLine.reject_summary[rejectGroupId] && productionLine.reject_summary[rejectGroupId]['total'])
                        value = productionLine.reject_summary[rejectGroupId]['total'];

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };


            /**
             * ClassName: live-production-line-hourly-data
             * Data: tag (Production Properties) ()
                    actual_output: 0
                    actual_output_accumulate: 0
                    availability: 0.7514940226713639
                    end: "2022-07-21T02:04:14+00:00"
                    oee: 0
                    ok_count: 0
                    pending_count: 0
                    performance: 0
                    plan_quantity: 960
                    quality: 1
                    reject_count: 0
                    reject_summary: {1: {…}, 2: {…}, 3: {…}}
                    reject_summary_accumulate: {1: {…}, 2: {…}, 3: {…}}
                    runtime_summary: {runtimes: {…}, downtimes: {…}}
                    standard_output: 20
                    standard_output_accumulate: 20
                    start: "2022-07-21T02:02:00+00:00"
                    variance: 20
             */
            _this.selectElements('.live-production-line-hourly-data').each((idx, e) => {
                let tag = $(e).data('tag');
                let blockIndex = Number.parseInt($(e).data('block-index'));
                let productionLine = _this.getProductionLineByElement(e);

                let value = null;
                if (isNaN(blockIndex)) {
                    //get all block as array
                    value = [];
                    if (productionLine && productionLine._hourly) {
                        productionLine._hourly.forEach(hourly => {
                            blocks.push(hourly[tag]);
                        })
                    };
                } else {
                    if (productionLine && productionLine._hourly && blockIndex >= 0 && blockIndex < productionLine._hourly.length) {
                        value = productionLine._hourly[blockIndex][tag];
                    };
                }

                _this.updateDomContent(e, _this.formatValue(e, value), summary);

            });

            if (_this.callbacks['live-production-line-hourly-data']) {
                _this.callbacks['live-production-line-hourly-data'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let tag = e.data['tag'];
                    let blockIndex = Number.parseInt(e.data['block-index']);

                    let productionLine = _this.getProductionLineByData(e.data);


                    let value = null;
                    if (isNaN(blockIndex)) {
                        //get all block as array

                        value = [];
                        if (productionLine && productionLine._hourly) {
                            productionLine._hourly.forEach(hourly => {
                                let block = {};
                                block['start'] = hourly['start'];
                                block['end'] = hourly['end'];
                                if (Array.isArray(tag)) {
                                    tag.forEach(item => {
                                        block[item] = hourly[item];
                                    });
                                } else {
                                    block[tag] = hourly[tag];
                                }
                                value.push(block);
                            })
                        };
                    } else {
                        if (productionLine && productionLine._hourly && blockIndex >= 0 && blockIndex < productionLine._hourly.length) {
                            let block = {};
                            let hourly = productionLine._hourly[blockIndex];
                            block['start'] = hourly['start'];
                            block['end'] = hourly['end'];
                            if (Array.isArray(tag)) {
                                tag.forEach(item => {
                                    block[item] = hourly[item];
                                });
                            } else {
                                block[tag] = hourly[tag];
                            }
                            value = block;
                        };
                    }

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };

            /**
             * ClassName: live-runtime-summary
             * Data: none
             */
            _this.selectElements('.live-runtime-summary').each((idx, e) => {
                let value = summary.runtime_summary;
                _this.updateDomContent(e, _this.formatValue(e, value), summary);

            });

            if (_this.callbacks['live-runtime-summary']) {
                _this.callbacks['live-runtime-summary'].forEach(e => {
                    //e
                    /*
                    {
                        data: data,
                        prev: null,
                        callback: callback
                    }
                    */
                    let value = summary.runtime_summary;

                    if (!_this.isSimilar(value, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            e.callback(e.data, _this.formatCallbackValue(e.data, value), summary);
                        }
                        e.prev = value;
                    }
                });
            };


            /**
             * ClassName: downtime-trigger
             * Data: tag {event_type}
                       *: ALL EVENT
                       1: MACHINE
                       2: HUMAN
                    


             */
            if (_this.callbacks['downtime-trigger']) {
                _this.callbacks['downtime-trigger'].forEach(e => {
                    let tag = e.data['tag'];
                    let flag = e.data['flag'];
                    //must have activeDowntimeEvents optional data
                    //console.log(liveDowntimeTimers['by_id']);

                    let downtimes = [];
                    if (!_this.terminalData.downtimes) {
                        return;
                    }
                    if (liveDowntimeTimers && liveDowntimeTimers['by_id']) {
                        Object.entries(liveDowntimeTimers['by_id']).forEach(([idx, downtimer]) => {

                            if (!downtimer.is_running)
                                return;

                            if (tag == '*') {
                                downtimes.push(idx);
                                return;
                            }

                            //find downtime type
                            let downtimeDef = _this.terminalData.downtimes.find((downtime) => {
                                return downtime.id = idx;
                            });

                            if (downtimeDef && tag == downtimeDef.downtime_type_id)
                                downtimes.push(idx);

                        });
                    }
                    if (!_this.isSimilar(downtimes, e.prev)) {
                        if (typeof(e.callback) == 'function') {
                            let triggered = 0;

                            if (e.prev == null) { //null or undefined
                                e.prev = [];
                            }
                            if (flag == 'all') {
                                triggered = downtimes.length > 0 ? 1 : 0;
                            } else { //any
                                triggered = 0;
                                downtimes.forEach(downtimeId => {
                                    if (!e.prev.includes(downtimeId))
                                        triggered = 1;
                                });
                            }

                            e.callback(e.data, _this.formatCallbackValue(e.data, triggered), summary);
                        }
                        e.prev = downtimes;
                    }
                });
            };
        };
        isSimilar(a, b) {
            let aC = a;
            let bC = b;
            if (typeof(a) === 'object')
                aC = JSON.stringify(a);

            if (typeof(b) === 'object')
                bC = JSON.stringify(b);

            return aC == bC;
        };
        updateDomContent(obj, newValue, summary = null) {

            let jobj = $(obj);
            let compareNewValue;
            if (typeof(newValue) === 'object')
                compareNewValue = JSON.stringify(newValue);
            else
                compareNewValue = newValue;

            let renderCallback = jobj.data('render');
            if (typeof(renderCallback) !== 'function')
                renderCallback = this.renderer[jobj.data('renderer')]; //renderer data set? try load

            let oldValue = jobj.data('display-value');
            if (oldValue !== compareNewValue) //update content if only different from old
            {


                let displayValue = newValue;
                if (typeof(renderCallback) == 'function') {
                    displayValue = renderCallback(obj, newValue, summary);
                }

                jobj.html(displayValue);
                jobj.data('display-value', compareNewValue);
            }
        };

        //Page Utilities
        //Production Line Tab
        initializeProductionLineTab(tabChangedCallback) {
            let _this = this;
            _this.tabChangedCallback = tabChangedCallback;
            _this.tabCurrentProductionLine = _this.terminalData.productionLines[0];
            if (typeof(_this.tabChangedCallback) === 'function')
                _this.tabChangedCallback(_this.tabCurrentProductionLine);

            return _this;
        };
        tabCurrentProductionLine = null;
        switchProductionLineTab(e) {
            //switch tab by sender element (by production line id or by line no), 
            // By ID:  data-production-line-id
            // By LineNo: data-line-no

            let _this = this;

            let productionLineId = $(e).data('production-line-id');
            let productionLineNo = $(e).data('line-no');

            if (productionLineId)
                return _this.switchProductionLineTabById(productionLineId);
            else
                return _this.switchProductionLineTabByLineNo(productionLineNo);
        };
        switchProductionLineTabById(id) {
            let _this = this;
            let productionLine = _this.getProductionLineById(id);
            if (!productionLine || (_this.tabCurrentProductionLine && productionLine.id == _this.tabCurrentProductionLine.id))
                return false;

            _this.tabCurrentProductionLine = productionLine;
            if (typeof(_this.tabChangedCallback) === 'function')
                _this.tabChangedCallback(_this.tabCurrentProductionLine);

            return true;
        };
        switchProductionLineTabByLineNo(lineNo) {
            let _this = this;
            productionLine = _this.getProductionLineByLineNo(lineNo);

            if (!productionLine || productionLine.id == _this.tabCurrentProductionLine.id)
                return false;

            _this.tabCurrentProductionLine = productionLine;
            if (typeof(_this.tabChangedCallback) === 'function')
                _this.tabChangedCallback(_this.tabCurrentProductionLine);

            return true;
        };
        tabChangedCallback = null;

        //Websocket / Polling Event Handler
        terminalDataUpdatedHandler(e) {
            let _this = this;
            if (e) {
                _this.terminalData.workCenter = e.workCenter;
                _this.terminalData.production = e.production;
                _this.terminalData.productionLines = e.productionLines;
            }
            if (_this.tabCurrentProductionLine) {
                _this.terminalData.productionLines.forEach(productionLine => {
                    if (productionLine.id == _this.tabCurrentProductionLine.id)
                        _this.tabCurrentProductionLine = productionLine;
                })
            }
        };

        terminalDowntimeStateChangedHandler(e) {
            let _this = this;
            if (e) {
                _this.terminalData.workCenterDowntimes = e.workCenterDowntimes;
                _this.terminalData.activeDowntimeEvents = e.activeDowntimeEvents;
            }
        };

    };
</script>