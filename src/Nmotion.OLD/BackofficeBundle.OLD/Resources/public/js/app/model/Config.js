define([

    // Libs
    'lib/Model'

], function (Model) {

    /**
     * @class ConfigModel
     * @extends Model
     */
    var ConfigModel = Model.extend('ConfigModel', {

        /**
         * @type {String}
         */
        idAttribute: 'id',

        /**
         * @type {String}
         */
        urlRoot: '/backoffice/configs',

        initialize: function () {
        }

    });

    return ConfigModel;

});
