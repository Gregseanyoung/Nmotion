define([

    // Libs
    'lib/Store',

    // Deps
    'model/Order'

], function (Store, OrderModel) {

    /**
     * @class CompletedOrdersCollection
     * @extends Store
     */
    var CompletedOrdersCollection = Store.extend('CompletedOrdersCollection', {

        /**
         * @type {OrderModel}
         */
        model: OrderModel,

        /**
         * @type {String}
         */
        url: '/backoffice/orders?orderStatusType=completed',

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return CompletedOrdersCollection;

});