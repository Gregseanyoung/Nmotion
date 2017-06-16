define([

    // Libs
    'lib/Store',

    // Deps
    'model/MealOption'

], function (Store, MealOptionModel) {

    /**
     * @class MealOptionCollection
     * @extends Store
     */
    var MealOptionCollection = Store.extend('MealOptionCollection', {

        /**
         * @type {MealOptionModel}
         */
        model: MealOptionModel,

        /**
         * @type {function(): string}
         */
        url: function () {
            if (! this.meal) {
                throw new Error('meal MUST be set');
            }
            return '/backoffice/meals/' + this.meal.getId() + '/options.json';
        },

        /**
         * @type {Number}
         */
        pageSize: 20,

        initialize: function (models, options) {
            this.callParent(arguments);
        }

    });

    return MealOptionCollection;

});
