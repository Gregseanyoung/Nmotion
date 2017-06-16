define([

    // Libs
    'lib/Model'

], function (Model) {

    /**
     * @class IncomeModel
     * @extends Model
     */
    var IncomeModel = Model.extend('IncomeModel', {

        /**
         * @type {String}
         */
        idAttribute: 'SN',

        /**
         * @type {String}
         */
        urlRoot: '/backoffice/income',

        initialize: function () {
        }

    });

    return IncomeModel;

});
