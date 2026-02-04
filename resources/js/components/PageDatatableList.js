export class PageDatatableList {
    constructor(config) {
        this.config = config;
    };
    config = null;

    search = null;
    dataTableObject = null;

    initializeDatatable(dataTableId) {

        let self = this;

        //default datatable config
        let dtConfig = {
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "searching": false,
            "ajax": {
                url: self.config.dataUrl,
                type: "POST",
                data: function (d) {
                    d._token = self.config._token;
                    d.search = self.search;
                    d.cols = ['id'];
                    self.config.datatableColumns.forEach(e => {
                        d.cols.push(e.data);
                    });
                }
            },
            "columns": self.config.datatableColumns
        };

        if (typeof (self.config.datatableConfig) === 'object')
            Object.assign(dtConfig, self.config.datatableConfig);

        this.dataTableObject = $(dataTableId).DataTable(dtConfig);
        return this;
    };

    initializeSearchFields(searchContainerId) {
        //create search field elements
        var owner = this;

        this.config.searchFields.forEach(searchField => {
            let inputType = searchField.type;
            var e;
            if (inputType == 'select') {
                e = owner.createSearchFieldSelect(searchField.title, searchField.data, searchField.options, searchField.afterRender);
            } else {
                e = owner.createSearchFieldText(searchField.title, searchField.data, searchField.options, searchField.afterRender);
            }
            $(searchContainerId).append(e);

        });

        //attach search functions
        $('.search-field').on('input', function () {
            owner.onSearchFieldChanging(this);
        })
            .on('keypress', function (e) {
                if (e.which === 13) {
                    owner.onSearchSubmit();
                }
            });

        $('.search-submit').on('click', function () {
            owner.onSearchSubmit();
        });

        return this;
    };
    onSearchSubmit() {
        //get all search parameters
        let search = [];
        $('.search-field-enable').each(function (idx, e) {
            let field = $(e).data('field');
            if (typeof field === 'undefined')
                return;

            if ($(e).is(':checked')) {
                let parameter = $('.search-field[data-field="' + field + '"]').val();
                if (typeof parameter === 'undefined' || parameter.length <= 0)
                    return;

                //add to search
                search.push({
                    field: field,
                    parameter: parameter
                })
            }
        });
        this.search = search;
        this.dataTableObject.ajax.reload();
    };
    onSearchFieldChanging(sender) {

        let field = $(sender).data('field');

        if (typeof field === 'undefined')
            return;

        let selector = '.search-field-enable[data-field="' + field + '"]';

        if (sender.value.length > 0)
            $(selector).attr('checked', 'true');
        else
            $(selector).removeAttr('checked');


    };
    createSearchFieldText(label, field, options = null, afterRender = null) {
        var e = $($('#template-search-field-text').html());

        e.find('.search-field-enable').each(function (idx, element) {
            element.dataset.field = field;
        });

        e.find('.search-field').each(function (idx, element) {
            element.dataset.field = field;
        });

        e.find('.search-field-label').each(function (idx, element) {
            $(element).html(label);
        });

        return e;
    };
    createSearchFieldSelect(label, field, options = null, afterRender = null) {
        var e = $($('#template-search-field-select').html());



        e.find('.search-field-enable').each(function (idx, element) {
            element.dataset.field = field;
        });
        e.find('.search-field').each(function (idx, element) {

            element.dataset.field = field;

            if (options !== null) {
                options.forEach(e => {

                    $(element).append($('<option></option').val(e.value).html(e.text));
                });
            }

            if (typeof afterRender === 'function')
                afterRender(element);
        });

        e.find('.search-field-label').each(function (idx, element) {
            $(element).html(label);
        });

        return e;
    };
}