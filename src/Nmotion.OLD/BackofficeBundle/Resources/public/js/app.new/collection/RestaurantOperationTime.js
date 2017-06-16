define([

    // Libs
    'lib/Store',

    // Deps
    'model/RestaurantOperationTime'

], function (Store, RestaurantOperationTimeModel) {

    /**
     * @class RestaurantOperationTimeCollection
     * @extends Store
     */
    var RestaurantOperationTimeCollection = Store.extend('RestaurantOperationTimeCollection', {

        /**
         * @type {RestaurantOperationTimeModel}
         */
        model: RestaurantOperationTimeModel,

        /**
         * @type {String}
         */
        url: function () {
            throw new Error('not implemented');
        },

        initialize: function (models, options) {
            this.callParent(arguments);
        }

    });

    return RestaurantOperationTimeCollection;

});
