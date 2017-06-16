define([

    // Libs
    'lib/Model'

], function (Model) {

    /**
     * @class OrderModel
     * @extends Model
     */
    var OrderModel = Model.extend('OrderModel', {

        idAttribute: 'id',

        urlRoot: '/backoffice/orders',

        initialize: function () {
        }

    });

    return OrderModel;

});
