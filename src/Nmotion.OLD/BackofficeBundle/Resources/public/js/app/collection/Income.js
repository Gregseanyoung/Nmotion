define([

    // Libs
    'lib/Store',

    // Deps
    'model/Income'

], function (Store, IncomeModel) {

    /**
     * @class IncomeCollection
     * @extends Store
     */
    var IncomeCollection = Store.extend('IncomeCollection', {

        /**
         * @type {IncomeModel}
         */
        model: IncomeModel,

        /**
         * @type {function(): string}
         */
        url: function () {
            if (! this.restaurant) {
                throw new Error('restaurant MUST be set');
            }

            return '/backoffice/restaurants/' + this.restaurant.getId() + '/income';
        },

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return IncomeCollection;

});
