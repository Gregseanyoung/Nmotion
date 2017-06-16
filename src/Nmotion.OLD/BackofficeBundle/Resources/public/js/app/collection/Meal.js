define([

    // Libs
    'lib/Store',

    // Deps
    'model/Meal'

], function (Store, Meal) {

    /**
     * @class MealCollection
     * @extends Store
     */
    var MealCollection = Store.extend('MealCollection', {

        /**
         * @type {MealModel}
         */
        model: Meal,

        /**
         * @type {function(): string}
         */
        url: function () {
            if (! this.menuCategory) {
                throw new Error('menuCategory MUST be set');
            }

            return '/backoffice/menucategories/' + this.menuCategory.getId() + '/meals';
        },

        /**
         * @type {Number}
         */
        pageSize: 20,

        initialize: function (models, options) {
            this.callParent(arguments);
        }

    });

    return MealCollection;

});
