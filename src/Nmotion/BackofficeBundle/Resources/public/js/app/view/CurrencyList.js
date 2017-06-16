define([

    // Libs
    'lib/view/Grid'

], function (GridView) {

    /**
     * @class ConfigListView
     * @extends GridView
     */
    var CurrencyListView = GridView.extend('CurrencyListView', {

        columns: [
            {
                title  : 'ID',
                keyName: 'id',
                width  : 20
            },
            {
                title  : 'Sign',
                keyName: 'sign'
            },
            {
                title  : 'Description',
                keyName: 'description'
            },
            {
                title        : 'Actions',
                keyName      : null,
                width        : 140,
                style        : 'min-width: 100px',
                sortable     : false,
                valueRenderer: function (value, item) {
                    var btns = [
                        '<a href="#currency/edit/{id}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>'
                    ];
                    return btns.join('&nbsp;').replace(/{id}/g, item.getId());
                }
            }
        ],

        containerClass: 'table table-bordered table-striped table-hover',

        getToolbarButtons: function getToolbar() {
            return '<a href="#currency/new" class="btn btn-small">Add currency</a>';
        },

        getToolbar: function getToolbar() {
            return '<div class="btn-toolbar">' + this.getToolbarButtons() + '</div>';
        },

        /**
         * @protected
         * @return {string}
         */
        getHeader: function getHeader() {
            return this.getToolbar();
        },

        renderPageHeader: function () {
            var headerId = this.cid + '-header';
            if (! this.el.firstChild || this.el.firstChild.id !== headerId) {
                var target = document.createElement('div');
                target.id = headerId;
                target.innerHTML = this.getHeader();
                this.el.insertBefore(target, this.el.firstChild);
            }
        },

        afterRender: function () {
            this.callParent(arguments);
            this.renderPageHeader();
        }
    });

    return CurrencyListView;

});
