define([

    // Libs
    'lib/Store',

    // Deps
    'model/MealExtraIngredient'

], function (Store, MealExtraIngredientModel) {

    /**
     * @class MealExtraIngredientCollection
     * @extends Store
     */
    var MealExtraIngredientCollection = Store.extend('MealExtraIngredientCollection', {

        /**
         * @type {MealExtraIngredientModel}
         */
        model: MealExtraIngredientModel,

        /**
         * @type {function(): string}
         */
        url: function () {
            if (! this.meal) {
                throw new Error('meal MUST be set');
            }
            return '/backoffice/meals/' + this.meal.getId() + '/extraingredients.json';
        },

        /**
         * @type {Number}
         */
        pageSize: 20,

        initialize: function (models, options) {
            this.callParent(arguments);
        }

    });

    return MealExtraIngredientCollection;

});
