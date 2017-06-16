define([

    // Libs
    'lib/view/Grid'

], function (GridView) {

    /**
     * @class IncomeListView
     * @extends GridView
     */
    var IncomeListView = GridView.extend('IncomeListView', /** @lends IncomeListView.prototype */ {

        /**
         * @protected
         */
        backBtnEnabled: true,

        events: {
            'click #periodSelect>button': function (event) {
                this.trigger('periodChange', event.currentTarget.value);
            },
            'click button.details': function (event) {
                this.trigger('incomeDetails', $(event.currentTarget).parents('tr').attr('data-cid'));
            }
        },

        columns: [
            {
                title   : '#',
                keyName : 'SN',
                width   : 20,
                sortable: true
            },
            {
                title  : 'Range',
                keyName: 'dateFrom',
                width  : 120,
                valueRenderer: function (value, item) {
                    return new Date(value * 1000).format('mediumDate')
                        + ' - '
                        + new Date(item.get('dateTo') * 1000).format('mediumDate');
                }
            },
            {
                title  : 'Orders',
                keyName: 'numberOfOrders',
                width  : 40
            },
            {
                title  : 'Order total',
                keyName: 'orderTotal',
                width  : 80,
                valueRenderer: function (value, item) {
                    return value + ' DKK';
                }
            },
            {
                title  : 'Product total',
                keyName: 'productTotal',
                width  : 100,
                valueRenderer: function (value, item) {
                    return value + ' DKK';
                }
            },
            {
                title  : 'Discount',
                keyName: 'discount',
                width  : 80,
                valueRenderer: function (value, item) {
                    return value + ' DKK';
                }
            },
            {
                title  : 'Tax',
                keyName: 'salesTax',
                width  : 80,
                valueRenderer: function (value, item) {
                    return value + ' DKK';
                }
            },
            {
                title  : 'Actions',
                keyName: null,
                width  : 50,
                valueRenderer: function (value, item) {
                    return '<button class="btn btn-small details"><i class="icon-align-justify"></i> Orders</button>';
                }
            }
        ],

        containerClass: 'table table-bordered table-striped table-hover',

        /**
         * @protected
         * @method
         * @return {?RestaurantModel}
         */
        getRestaurant: function getRestaurant() {
            if (this.getCollection().restaurant) {
                return this.getCollection().restaurant;
            }
            if (this.getCollection().first()) {
                return this.getCollection().first().get('restaurant');
            }
            throw new Error('restaurant MUST be set');
        },

        /**
         * @method
         */
        disableBackBtn: function disableBackBtn() {
            this.backBtnEnabled = false;
        },

        /**
         *
         * @param {String} period
         * @param {Boolean=} silently Be default is true
         */
        setPeriod: function (period, silently) {
            var periodBtns = $('#periodSelect>button');
            periodBtns.removeClass('active');
            periodBtns.filter('[value=' + period + ']').addClass('active');
        },

        /**
         * @protected
         * @return {string}
         */
        getListTitle: function getListTitle() {
            return this.getRestaurant().get('name') + ' income';
        },

        /**
         * @protected
         * @return {string}
         */
        getNavigationBar: function getNavigationBar() {
            if (this.backBtnEnabled) {
                return '<a href="#/restaurant/manage" class="back">&larr; Back to Restaurants</a>';
            } else {
                return '';
            }
        },

        getToolbar: function getToolbar() {
            return '\
                <div class="btn-toolbar pull-right" data-toggle="buttons-radio">\
                    <div class="btn-group" id="periodSelect">\
                        <button value="d" class="btn">Day</button>\
                        <button value="w" class="btn">Week</button>\
                        <button value="2w" class="btn">14 days</button>\
                        <button value="m" class="btn">Month</button>\
                        <button value="y" class="btn">Year</button>\
                    </div>\
                </div>\
            ';
        },

        /**
         * @protected
         * @return {string}
         */
        getHeader: function getHeader() {
            var me = this,
                headerTitle = '\
                <div id="headerTitle" style="margin-bottom: 10px" class="well well-small">\
                    <h3 style="margin: 0">' + me.getListTitle() + '</h3>\
                </div>';

            return headerTitle + ' ' + me.getNavigationBar() + ' ' + me.getToolbar();
        },

        renderPageHeader: function () {
            var headerId = this.cid + '-header';
            if (! this.el.firstChild || this.el.firstChild.id !== headerId) {
                var target = document.createElement('div');
                target.id = headerId;
                target.className = 'clearfix';
                target.innerHTML = this.getHeader();
                this.el.insertBefore(target, this.el.firstChild);
            }
        },

        afterRender: function () {
            this.callParent(arguments);
            this.renderPageHeader();
        }

    });

    return IncomeListView;

});
