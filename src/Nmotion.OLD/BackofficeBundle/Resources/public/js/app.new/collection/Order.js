define([

    // Libs
    'lib/Store',

    // Deps
    'model/Order'

], function (Store, OrderModel) {

    /**
     * @class OrderCollection
     * @extends Store
     */
    var OrderCollection = Store.extend('OrderCollection', {

        /**
         * @type {OrderModel}
         */
        model: OrderModel,

        /**
         * @type {String}
         */
        url: '/backoffice/orders',

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return OrderCollection;

});
