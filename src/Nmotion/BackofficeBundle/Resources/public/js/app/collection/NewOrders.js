define([

    // Libs
    'lib/Store',

    // Deps
    'model/Order'

], function (Store, OrderModel) {

    /**
     * @class NewOrdersCollection
     * @extends Store
     */
    var NewOrdersCollection = Store.extend('NewOrdersCollection', {

        /**
         * @type {OrderModel}
         */
        model: OrderModel,

        /**
         * @type {String}
         */
        url: '/backoffice/orders?orderStatusType=new',

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return NewOrdersCollection;

});