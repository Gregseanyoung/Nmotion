define([

    // Libs
    'lib/Store',

    // Deps
    'model/MenuCategory'

], function (Store, MenuCategory) {

    /**
     * @class MenuCategoryCollection
     * @extends Store
     */
    var MenuCategoryCollection = Store.extend('MenuCategoryCollection', {

        /**
         * @type {MenuCategoryModel}
         */
        model: MenuCategory,

        /**
         * @type {function(): string}
         */
        url: function () {
            if (! this.restaurant) {
                throw new Error('restaurant MUST be set');
            }
            return '/backoffice/restaurants/' + this.restaurant.getId() + '/menucategories.json';
        },

        /**
         * @type {Number}
         */
        pageSize: 20,

        initialize: function (models, options) {
            this.callParent(arguments);
        }

    });

    return MenuCategoryCollection;

});
