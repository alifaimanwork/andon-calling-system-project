export class DataTableLoader {
    config = {}
    parameters = {}
    dataTableObject = null
    initialize = function (tableId, requestUrl, config, parameters) {
        let parent = this;
        this.config = config;
        this.parameters = parameters;

        let fullConfig = {
            processing: true,
            serverSide: true,
            scrollX: true,
            searching: false,
            ajax: {
                url: requestUrl,
                type: "POST",
                data: function (d) {
                    parent.datatableGetPayloadData(d);
                }
            }
        }

        Object.assign(fullConfig, config);

        this.dataTableObject = $(`#${tableId}`).DataTable(fullConfig);
        return this;
    }
    datatableGetPayloadData = function (d) {

        let parent = this;

        Object.keys(parent.parameters).forEach(key => {
            d[key] = parent.parameters[key];
        });

        d.cols = [];
        d.column_names = {};
        parent.config.columns.forEach(e => {
            d.cols.push(e.data);
            d.column_names[e.data] = e.title;
        });

        return this;
    }
    reload = function () {
        this.dataTableObject.ajax.reload();
    }
}