import moment from "moment";

export class LiveClock {
    serverTime = null;
    serverTimeOffset = null;
    plantTimeZone = null;
    intervalId = null;

    clocks = [];

    constructor(serverTimeOnLoad, plantTimeZone) {
        let _this = this;
        _this.serverTime = new Date(serverTimeOnLoad);
        _this.serverTimeOffset = (_this.serverTime.getTime() / 1000) - (new Date()).getTime() / 1000;
        _this.plantTimeZone = plantTimeZone;
        let tick = () => {
            _this.updateClock();
        };
        _this.intervalId = setInterval(tick, 250);

        tick();
        return this;
    };

    updateClock() {
        let _this = this;

        /*  ClassName: live-clock
            data-clock: 
             - local (local PC)
             - plant (plant Time)
             - server (server Time (UTC)) 
        */
        let now = new Date();
        const localTime = now.getTime();
        const serverTime = localTime + _this.serverTimeOffset * 1000;

        let clocks = {
            local: moment(new Date(localTime)),
            server: moment(new Date(serverTime)),
            plant: moment(new Date(serverTime)).tz(_this.plantTimeZone)
        };

        $('.live-clock').each(function (index, e) {

            let elem = $(e);
            let format = elem.data('format');
            let clockName = elem.data('clock');
            if (typeof (clockName) === 'undefined')
                clockName = 'local';

            if (!(clockName in clocks))
                return;

            let clock = clocks[clockName];
            _this.updateDomContent(elem, clock.format(format));
        });
    };
    toPlantClock(date)
    {
        return moment(date).tz(this.plantTimeZone);
    };
    updateDomContent(obj, newValue) {

        let jobj = $(obj);
        let oldValue = jobj.html();
        if (oldValue != newValue) //update content if only different from old
            jobj.html(newValue);
    };
}