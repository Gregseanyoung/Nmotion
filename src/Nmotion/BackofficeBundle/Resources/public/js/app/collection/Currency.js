define([

    // Libs
    'lib/Store',

    // Deps
    'model/Currency'

], function (Store, CurrencyModel) {

    /**
     * @class CurrencyCollection
     * @extends Store
     */
    var CurrencyCollection = Store.extend('CurrencyCollection', {

        /**
         * @type {ConfigModel}
         */
        model: CurrencyModel,

        /**
         * @type {String}
         */
        url: '/backoffice/currencies',

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return CurrencyCollection;

});
