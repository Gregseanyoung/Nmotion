define([

    // Libs
    'lib/Store',

    // Deps
    'model/Restaurant'

], function (Store, RestaurantModel) {

    /**
     * @class RestaurantCollection
     * @extends Store
     */
    var RestaurantCollection = Store.extend('RestaurantCollection', {

        /**
         * @type {RestaurantModel}
         */
        model: RestaurantModel,

        /**
         * @type {String}
         */
        url: '/backoffice/restaurants.json',

        /**
         * @type {Number}
         */
        pageSize: 20,

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return RestaurantCollection;

});
