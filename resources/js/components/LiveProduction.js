export class LiveProduction {

    /* --- Production Data --- */
    serverTimeOffset = 0;

    terminalData = null;

    // timers = {
    //     downtimes: {
    //         unplan_human: {
    //             blocks: [],
    //             duration: 0,
    //             ongoing: null
    //         },
    //         unplan_machine: {
    //             blocks: [],
    //             duration: 0,
    //             ongoing: null
    //         },
    //         unplan: {
    //             blocks: [],
    //             duration: 0,
    //             ongoing: null
    //         },
    //         plan: {
    //             blocks: [],
    //             duration: 0,
    //             ongoing: null
    //         },
    //         all: {
    //             blocks: [],
    //             duration: 0,
    //             ongoing: null
    //         }
    //     },
    //     runtimes: {
    //         plan: {
    //             blocks: [],
    //             duration: 0,
    //             ongoing: null
    //         }
    //     }
    // };

    indicatorAverage = {};
    lastSummary = null;
    currentSummary = null;

    production = null;

    /* --- Production Line Data --- */
    //Key value pair (Key = line no)
    productionLines = {};
    /*
    {
        actualOutput = 0;
        rejectCount = 0;
        cycleTime = null;
    }
    */
    intervalId = null;

    //callbacks
    // onStandardOutputChangedCallback = null;
    // onDowntimeUpdatedCallback = null;
    onAnyChangesCallback = null;


    constructor(serverTimeOnLoad, terminalData, autostart = true) {
        let serverTime = new Date(serverTimeOnLoad);
        this.serverTimeOffset = (serverTime.getTime() / 1000) - (new Date()).getTime() / 1000;
        this.terminalData = terminalData;

        if (autostart)
            this.start();
    };

    // onStandardOutputChanged(callback) {
    //     this.onStandardOutputChangedCallback = callback;
    //     return this;
    // }
    // onDowntimeUpdated(callback) {
    //     this.onDowntimeUpdatedCallback = callback;
    //     return this;
    // }
    listenAnyChanges(callback) {
        this.onAnyChangesCallback = callback;
        return this;
    };
    start() {
        let _this = this;
        this.intervalId = setInterval(() => { _this.tick(); }, 250);
        _this.tick();
        return this;
    };
    stop() {
        clearInterval(this.intervalId);
        return this;
    };
    tick() {
        let _this = this;
        _this.currentSummary = _this.getSummary();

        // console.log(_this.currentSummary);
        //check any difference
        if (!this.lastSummary || JSON.stringify(this.lastSummary) != JSON.stringify(_this.currentSummary)) {
            if (typeof (this.onAnyChangesCallback) === 'function') {
                this.onAnyChangesCallback(_this.currentSummary);
            }
        }
        //TODO: segmented callback

        this.lastSummary = _this.currentSummary;
    };
    /*
    updateProductionData(productionData) {
        this.production = productionData;

        console.log("LiveProduction: updateProductionData", productionData);
        //update runtime_summary
        if (!(!productionData || !productionData.runtime_summary)) {
            //downtimes
            if (productionData.runtime_summary.downtimes)
                this.updateDowntimeSummary(productionData.runtime_summary.downtimes);

            if (productionData.runtime_summary.runtimes)
                this.updateRuntimeSummary(productionData.runtime_summary.runtimes);
        }

    };

    updateDowntimeSummary(downtimeSummary) {
        let _this = this;
        console.log("LiveProduction: updateDowntimeSummary", downtimeSummary);
        //Sync live downtime counter
        Object.entries(downtimeSummary).forEach(([key, value]) => {
            _this.timers.downtimes[key] = value;
        });

    };
    updateRuntimeSummary(runtimeSummary) {
        
        // {
        //     "plan_runtimes": [1],
        //     "total_plan_runtime": 1,
        //     "ongoing": 1655712828
        // }
     
        let _this = this;
        console.log("LiveProduction: updateRuntimeSummary", runtimeSummary);
        //Sync live downtime counter
        Object.entries(runtimeSummary).forEach(([key, value]) => {
            _this.timers.runtimes[key] = value;
        });

    };
    */

    getServerNow() {
        return ((new Date()).getTime() / 1000) + this.serverTimeOffset;
    };

    getProductionLine(lineNo) {
        let _this = this;
        if (!_this.terminalData || !_this.terminalData.productionLine || !Array.isArray(_this.terminalData.productionLines))
            return null;

        for (let index = 0; index < _this.terminalData.productionLines.length; index++) {
            const productionLine = _this.terminalData.productionLines[index];
            if (productionLine.line_no == lineNo)
                return productionLine;
        }

        return null;
    };

    getTimerByTag(group, tag, subTag) {

        let _this = this;

        if (!_this.terminalData ||
            !_this.terminalData.production ||
            !_this.terminalData.production.runtime_summary ||
            !_this.terminalData.production.runtime_summary[group] ||
            !_this.terminalData.production.runtime_summary[group][tag] ||
            (subTag && !_this.terminalData.production.runtime_summary[group][tag][subTag]))
            return null;

        if (subTag)
            return _this.terminalData.production.runtime_summary[group][tag][subTag];

        return _this.terminalData.production.runtime_summary[group][tag];
    };

    //cycleTime: part Cycle Time, timer: goodRuntime
    getStandardOutput(cycleTime, timer) {
        let _this = this;

        if (!timer)
            return 0;

        if (cycleTime <= 0)
            return 0;

        let accumulated = 0;

        //calculate standard output by good running time block.
        /*
        if (Array.isArray(timer.blocks)) {
            timer.blocks.forEach(runtime => {
                accumulated += Math.floor(runtime / cycleTime);
            });
        }
        */
        accumulated = Math.floor(timer.duration / cycleTime);

        if (timer.ongoing) {
            // currently in good running timeblock, calculate the rest
            let runtime = this.getServerNow() - timer.ongoing;
            accumulated += Math.floor(runtime / cycleTime);
        }

        return accumulated;

    };
    formatTimer(duration) {
        let totalSeconds = duration;
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
    };
    calculateTimerDuration(timer) {
        let _this = this;
        if (!timer)
            return 0;
        let duration = 0;

        if (timer.duration)
            duration = timer.duration;

        if (timer.ongoing)
            duration += _this.getServerNow() - timer.ongoing;

        return duration;
    };

    getProductionLineHourlyData(production, productionLine) {
        let _this = this;
        let production_hourly = production.hourly_summary;
        let productionLine_hourly = productionLine.hourly_summary;
        if (!productionLine_hourly || !production_hourly)
            return [];
        let hourlyBlocks = [];

        let cycleTime = 0;
        if (!!productionLine.part_data &&
            !!productionLine.part_data.cycle_time) //part data exist
            cycleTime = productionLine.part_data.cycle_time;




        //merge block
        Object.entries(productionLine_hourly).forEach(([key, lineBlock]) => {
            let prodBlock = production_hourly[key];
            if (!prodBlock)
                return;

            hourlyBlocks.push({
                start: prodBlock.start,
                end: prodBlock.end,

                runtime_summary: prodBlock.runtime_summary,
                reject_count: lineBlock.reject_count,

                ok_count: lineBlock.ok_count,
                pending_count: lineBlock.pending_count,
                plan_quantity: lineBlock.plan_quantity,
                //plan_variance: lineBlock.plan_variance,


                actual_output: lineBlock.actual_output,
                actual_output_accumulate: lineBlock.actual_output_accumulate,

                reject_summary: lineBlock.reject_summary,
                reject_summary_accumulate: lineBlock.reject_summary,


                //second pass calculation
                standard_output: 0,
                standard_output_accumulate: 0,

                availability: 0,
                performance: 0,
                quality: 0,
                oee: 0,
                variance: 0,

            });
        });

        //sort by start time
        hourlyBlocks.sort(function (a, b) {
            let aTime = new Date(a.start);
            let bTime = new Date(b.start);
            return aTime.getTime() - bTime.getTime();
        });



        let standard_output_accumulate = 0;
        for (let n = 0; n < hourlyBlocks.length; n++) {
            const block = hourlyBlocks[n];

            //Standard output
            let goodRuntime = null;

            if (!!block.runtime_summary &&
                !!block.runtime_summary.runtimes &&
                !!block.runtime_summary.runtimes.good) //good runtime timer data exist
                goodRuntime = block.runtime_summary.runtimes.good;

            let planRuntime = null;
            if (!!block.runtime_summary &&
                !!block.runtime_summary.runtimes &&
                !!block.runtime_summary.runtimes.plan) //good runtime timer data exist
                planRuntime = block.runtime_summary.runtimes.plan;


            block.standard_output = _this.getStandardOutput(cycleTime, goodRuntime);
            standard_output_accumulate += block.standard_output;

            block.standard_output_accumulate = standard_output_accumulate;

            //variance
            block.variance =  block.ok_count - block.standard_output;

            //availability
            block.availability = _this.getAvailability(planRuntime, goodRuntime);

            //performance
            block.performance = _this.getPerformance(block.actual_output, block.standard_output);

            //quality
            block.quality = _this.getQuality(block.actual_output, block.reject_count);

            //oee
            block.oee = _this.getOee(block.availability, block.performance, block.quality);

        }

        return hourlyBlocks;
    };


    getProductionHourlyData(productionLines) {
        //must run after processing productionlineHourlydata
        let _this = this;

        let hourlyBlocks = [];

        let productionLinesHourly = [];

        productionLines.forEach(productionLine => {
            productionLinesHourly.push(productionLine._hourly)
        });

        if (productionLinesHourly.length >= 0) {

            for (let i = 0; i < productionLinesHourly[0].length; i++) {
                let block = {
                    start: productionLinesHourly[0][i].start,
                    end: productionLinesHourly[0][i].end,
                    average_availability: 0,
                    average_performance: 0,
                    average_quality: 0,
                    average_oee: 0,
                    runtime_summary: productionLinesHourly[0][i].runtime_summary,
                };
                for (let n = 0; n < productionLinesHourly.length; n++) {
                    const productionLineHourlyBlock = productionLinesHourly[n][i];
                    block.average_availability += productionLineHourlyBlock.availability;
                    block.average_performance += productionLineHourlyBlock.performance;
                    block.average_quality += productionLineHourlyBlock.quality;
                    block.average_oee += productionLineHourlyBlock.oee;

                }
                if (productionLinesHourly.length > 0) {
                    block.average_availability /= productionLinesHourly.length;
                    block.average_performance /= productionLinesHourly.length;
                    block.average_quality /= productionLinesHourly.length;
                    block.average_oee /= productionLinesHourly.length;
                }

                hourlyBlocks.push(block);
            }
        }
        return hourlyBlocks;
    };
    getSummary() {
        let _this = this;
        let result =
        {
            runtime_summary: [],
            runtimes: {

            },
            downtimes: {

            },
            work_center: {},
            production: {},
            production_lines: []
        };

        { //Live Runtimes Summary
            if (!(!_this.terminalData ||
                !_this.terminalData.production ||
                !_this.terminalData.production.runtime_summary ||
                !_this.terminalData.production.runtime_summary.summary ||
                !_this.terminalData.production.runtime_summary.summary.data)) {
                //Update Live Runtimes 
                const runtimeSummary = _this.terminalData.production.runtime_summary.summary;
                let summary = [];
                let lastState = 0;
                runtimeSummary.data.forEach(e => {
                    let dt;
                    if (isNaN(Number(e.time)))
                        dt = new Date(e.time);
                    else
                        dt = new Date(e.time * 1000);

                    summary.push({
                        time: dt,
                        state: e.state
                    });
                    lastState = e.state;
                });

                if (runtimeSummary.ongoing && runtimeSummary.data.length > 0) {
                    //ongoing, persist last state
                    summary.push({
                        time: new Date(),
                        state: lastState
                    });
                };

                result.runtime_summary = summary;
            }
        }

        { //Live Runtimes
            if (!(!_this.terminalData ||
                !_this.terminalData.production ||
                !_this.terminalData.production.runtime_summary ||
                !_this.terminalData.production.runtime_summary.runtimes)) {
                //Update Live Runtimes 
                let runtimes = _this.terminalData.production.runtime_summary.runtimes;

                Object.entries(runtimes).forEach(([timerName, timer]) => {
                    let duration = _this.calculateTimerDuration(timer);
                    let timerData = {
                        total: duration,
                        is_running: (!!timer.ongoing) ? 1 : 0,
                    }
                    result.runtimes[timerName] = timerData;
                });

                //Add remaining timer

                result.runtimes['remaining'] = {
                    total: (new Date(_this.terminalData.production.stopped_at)).getTime() / 1000 - _this.getServerNow(),
                    is_running: true,
                };
            }
        }

        { //Live Downtimes
            if (!(!_this.terminalData ||
                !_this.terminalData.production ||
                !_this.terminalData.production.runtime_summary ||
                !_this.terminalData.production.runtime_summary.downtimes)) {
                //Update Live Downtime 
                let downtimes = _this.terminalData.production.runtime_summary.downtimes;

                Object.entries(downtimes).forEach(([timerName, timer]) => {
                    if (timerName == 'by_id') {
                        Object.entries(timer).forEach(([downtimeId, subtimer]) => {
                            let duration = _this.calculateTimerDuration(subtimer);
                            let timerData = {
                                total: duration,
                                is_running: (!!subtimer.ongoing) ? 1 : 0,
                            }
                            if (!result.downtimes[timerName])
                                result.downtimes[timerName] = {};

                            result.downtimes[timerName][downtimeId] = timerData;
                        });

                    }
                    else {
                        let duration = _this.calculateTimerDuration(timer);
                        let timerData = {
                            total: duration,
                            is_running: (!!timer.ongoing) ? 1 : 0,
                        }
                        result.downtimes[timerName] = timerData;
                    }
                });
            }
        }

        { //Work Center Data
            if (!(!_this.terminalData ||
                !_this.terminalData.workCenter)) {
                //workcenter combined data
                _this.terminalData.workCenter._status = { status: _this.terminalData.workCenter.status, downtime_state: _this.terminalData.workCenter.downtime_state };
                result.work_center = _this.terminalData.workCenter;
            }
        }


        let liveProductionData = {
            average_oee: null,
            average_availability: null,
            average_performance: null,
            average_quality: null,
        };

        let planTimer = null;
        if (!!_this.terminalData &&
            !!_this.terminalData.production &&
            !!_this.terminalData.production.runtime_summary &&
            !!_this.terminalData.production.runtime_summary.runtimes &&
            !!_this.terminalData.production.runtime_summary.runtimes.plan)
            planTimer = _this.terminalData.production.runtime_summary.runtimes.plan;

        let goodTimer = null;

        if (!!_this.terminalData &&
            !!_this.terminalData.production &&
            !!_this.terminalData.production.runtime_summary &&
            !!_this.terminalData.production.runtime_summary.runtimes &&
            !!_this.terminalData.production.runtime_summary.runtimes.good)
            goodTimer = _this.terminalData.production.runtime_summary.runtimes.good;


        let workCenterAvailability = _this.getAvailability(planTimer, goodTimer);

        liveProductionData.average_availability = workCenterAvailability;

        { // Live Production Line Data
            let activeProductionLineIds = [];
            if (!(!_this.terminalData ||
                !_this.terminalData.productionLines)) {

                //Update Live Production Line Data    
                _this.terminalData.productionLines.forEach(productionLine => {

                    activeProductionLineIds.push(productionLine.id);

                    let cycleTime = 0;
                    let goodRuntime = null;
                    if (!!productionLine &&
                        !!productionLine.part_data &&
                        !!productionLine.part_data.cycle_time) //part data exist
                        cycleTime = productionLine.part_data.cycle_time;

                    if (!!_this.terminalData &&
                        !!_this.terminalData.production &&
                        !!_this.terminalData.production.runtime_summary &&
                        !!_this.terminalData.production.runtime_summary.runtimes &&
                        !!_this.terminalData.production.runtime_summary.runtimes.good) //good runtime timer data exist
                        goodRuntime = _this.terminalData.production.runtime_summary.runtimes.good;


                    let standardOutput = _this.getStandardOutput(cycleTime, goodRuntime);

                    let availability = workCenterAvailability;
                    let performance = _this.getPerformance(productionLine.actual_output, standardOutput);
                    let quality = _this.getQuality(productionLine.actual_output, productionLine.reject_count);
                    let oee = _this.getOee(availability, performance, quality);







                    let plan_quantity = productionLine.plan_quantity ? productionLine.plan_quantity : productionLine.production_order.plan_quantity;
                    let ok_count = productionLine.actual_output - productionLine.reject_count;
                    let variance = ok_count - standardOutput;
                    let plan_variance = plan_quantity - ok_count;

                    //hourly data
                    productionLine._hourly = _this.getProductionLineHourlyData(_this.terminalData.production, productionLine);


                    // let prevHourlyBlock = null;
                    // let currentHourlyBlock = null;
                    // if (productionLine._hourly && productionLine._hourly.length >= 2) {
                    //     currentHourlyBlock = productionLine._hourly[productionLine._hourly.length - 1];
                    //     prevHourlyBlock = productionLine._hourly[productionLine._hourly.length - 2];
                    // }

                    //indicator
                    let indicators = {
                        indicator_variance: variance,
                        indicator_reject_count: productionLine.reject_count,
                        indicator_oee: oee,
                        indicator_availability: availability,
                        indicator_performance: performance,
                        indicator_quality: quality,
                    };
                    let indicatorAverage = _this.indicatorAverage[productionLine.id];
                    if (typeof (indicatorAverage) === 'undefined') {
                        indicatorAverage = {
                            indicator_variance: variance,
                            indicator_reject_count: productionLine.reject_count,
                            indicator_oee: oee,
                            indicator_availability: availability,
                            indicator_performance: performance,
                            indicator_quality: quality,
                            _count: 1
                        };
                        _this.indicatorAverage[productionLine.id] = indicatorAverage;
                    }
                    else {
                        indicatorAverage._count++;
                        if (indicatorAverage._count > 240)
                            indicatorAverage._count = 240;

                        Object.keys(indicatorAverage).forEach(k => {
                            if (k == '_count')
                                return;
                            indicatorAverage[k] = (indicatorAverage[k] * (indicatorAverage._count - 1) + indicators[k]) / indicatorAverage._count;
                        });
                    }
                    Object.keys(indicators).forEach(k => {
                        indicators[k] -= indicatorAverage[k];
                        indicators[k] = Math.round(indicators[k] * 1000);
                        indicators[k] = (indicators[k] < 0) ? -1 : (indicators[k] > 0 ? 1 : 0);
                    })
                    // if (!!prevHourlyBlock) {
                    //     indicators.indicator_variance = currentHourlyBlock.variance - prevHourlyBlock.variance;
                    //     indicators.indicator_reject_count = currentHourlyBlock.reject_count - prevHourlyBlock.reject_count;
                    //     indicators.indicator_oee = currentHourlyBlock.oee - prevHourlyBlock.oee;
                    //     indicators.indicator_availability = currentHourlyBlock.availability - prevHourlyBlock.availability;
                    //     indicators.indicator_performance = currentHourlyBlock.performance - prevHourlyBlock.performance;
                    //     indicators.indicator_quality = currentHourlyBlock.quality - prevHourlyBlock.quality;


                    //     Object.keys(indicators).forEach(k => {
                    //         indicators[k] = (indicators[k] < 0) ? -1 : (indicators[k] > 0 ? 1 : 0);
                    //     })
                    // }


                    productionLine._live = {
                        actual_output: productionLine.actual_output,
                        reject_count: productionLine.reject_count,
                        reject_percentage: productionLine.reject_percentage,

                        ok_count: ok_count,
                        pending_count: productionLine.pending_count,
                        plan_quantity: plan_quantity,
                        variance: variance,

                        standard_output: standardOutput,
                        availability: availability,
                        performance: performance,
                        quality: quality,
                        oee: oee,
                        plan_variance: plan_variance,
                        ...indicators
                    };


                    liveProductionData.average_oee += productionLine._live.oee / _this.terminalData.productionLines.length;
                    liveProductionData.average_performance += productionLine._live.performance / _this.terminalData.productionLines.length;
                    liveProductionData.average_quality += productionLine._live.quality / _this.terminalData.productionLines.length;



                    result.production_lines.push(productionLine);
                });
            }
            Object.keys(_this.indicatorAverage).forEach(k => {
                // console.log(activeProductionLineIds, k, activeProductionLineIds.indexOf(parseInt(k)));
                if (activeProductionLineIds.indexOf(parseInt(k)) == -1)
                    _this.indicatorAverage[k] = undefined; //remove unused record
            });
        }


        { // Live Production Data
            if (!(!_this.terminalData ||
                !_this.terminalData.production)) {

                //hourly data
                if (!(!_this.terminalData ||
                    !_this.terminalData.productionLines)) {
                    _this.terminalData.production._hourly = _this.getProductionHourlyData(_this.terminalData.productionLines); //TODO
                }
                else {
                    _this.terminalData.production._hourly = [];
                }

                //Update Live Production Data
                _this.terminalData.production._live = liveProductionData;
                result.production = _this.terminalData.production;
            }
        }



        return result;
    };


    getAvailability(planTimer, goodTimer) {
        let _this = this;

        let totalRunningTime = _this.calculateTimerDuration(planTimer);

        let totalGoodRunningTime = _this.calculateTimerDuration(goodTimer);


        if (totalRunningTime > 0)
            return totalGoodRunningTime / totalRunningTime;
        else
            return 0;

    };
    getPerformance(actual_output, standard_output) {

        //actual / (operating Time (Downtime - plan runtime) / cycle time)



        // let _this = this;
        // // if (standardOutput == null) //null or undefined, calculate standardOutput
        // //     standardOutput = _this.getStandardOutput(productionLine);


        // let performance = 1;
        // let runtime = _this.calculateTimerDuration(_this.terminalData.production.runtime_summary.runtimes.plan) - _this.calculateTimerDuration(_this.terminalData.production.runtime_summary.downtimes.unplan);


        //  if (runtime > 0 && productionLine.part_data.cycle_time > 0) {
        //     performance = productionLine.actual_output / (runtime / productionLine.part_data.cycle_time);
        // }

        if (standard_output <= 0)
            return 0;

        let performance = actual_output / standard_output;

        if (performance > 1)
            performance = 1;
        return performance;
    };
    getQuality(actual_output, reject_count) {
        if (actual_output <= 0)
            return 0;
        let quality = (actual_output - reject_count) / actual_output;

        if (quality < 0)
            quality = 0;
        else if (quality > 1)
            quality = 1;
        return quality;
    };
    getOee(availability, performance, quality) {
        return availability * performance * quality;
    };
}