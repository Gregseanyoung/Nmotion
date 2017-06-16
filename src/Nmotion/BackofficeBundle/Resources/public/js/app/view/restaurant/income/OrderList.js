define([

    // Libs
    'view/order/List'

], function (OrderListView) {

    /**
     * @class IncomeOrderListView
     * @extends OrderListView
     */
    var IncomeOrderListView = OrderListView.extend('IncomeOrderListView', {

        events: {
            'click .back': function (event) {
                this.trigger('navigateBack');
            }
        },

        /**
         * @type {Boolean}
         */
        paginationBarVisible: false,

        mastersOnly: true,

        /**
         * @type {IncomeModel}
         */
        income: null,

        initialize: function initialize() {
            this.columns.forEach(function (column) {
                if (column.keyName === 'id' || column.keyName === 'updatedAt') {
                    column.sortable = true;
                }
            });
            this.callParent(arguments);
        },

        /**
         * @param {IncomeModel} income
         */
        setIncome: function setIncome(income) {
            this.income = income;
            return this;
        },

        /**
         * @protected
         * @return {string}
         */
        getListTitle: function getListTitle() {
            var title,
                dateFrom = new Date(this.income.get('dateFrom') * 1000).format('mediumDate'),
                dateTo = new Date(this.income.get('dateTo') * 1000).format('mediumDate'),
                restaurantName = this.income.get('restaurant').get('name');

            if (dateFrom === dateTo) {
                title = 'Orders in {restaurantName} during {dateFrom}';
            } else {
                title = 'Orders in {restaurantName} during period {dateFrom} to {dateTo}';
            }

            return title.replace('{dateFrom}', dateFrom)
                .replace('{dateTo}', dateTo)
                .replace('{restaurantName}', restaurantName);
        },

        /**
         * @protected
         * @return {string}
         */
        getNavigationBar: function getNavigationBar() {
            return '\
                <a href="javascript:;" class="back" style="display:block;margin-bottom:5px">\
                    &larr; Back to Restaurant Income\
                </a>';
        },

        /**
         * @protected
         * @return {string}
         */
        getHeader: function getHeader() {
            var headerTitle = '\
                <div id="headerTitle" style="margin-bottom: 10px" class="well well-small">\
                    <h3 style="margin: 0">' + this.getListTitle() + '</h3>\
                </div>';

            return headerTitle + ' ' + this.getNavigationBar();
        }

    });

    return IncomeOrderListView;

});
