define([

    // Libs
    'lib/Store',

    // Deps
    'model/Config'

], function (Store, ConfigModel) {

    /**
     * @class ConfigCollection
     * @extends Store
     */
    var ConfigCollection = Store.extend('ConfigCollection', {

        /**
         * @type {ConfigModel}
         */
        model: ConfigModel,

        /**
         * @type {String}
         */
        url: '/backoffice/configs',

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return ConfigCollection;

});
