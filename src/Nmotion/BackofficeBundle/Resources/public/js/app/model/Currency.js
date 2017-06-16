define([

    // Libs
    'lib/Model'

], function (Model) {

    /**
     * @class CurrencyModel
     * @extends Model
     */
    var CurrencyModel = Model.extend('CurrencyModel', {

        idAttribute: 'id',
        
        /**
         * @type {String}
         */
        urlRoot: '/backoffice/currencies',
                
        schema: {
            id : {
                type: 'Hidden'
            },
            sign: {
                type: 'Select',
                validators: ['required']
            },
            description: {
                type: 'Text',
                validators: ['required']
            }
        },

        initialize: function () {
        },
                
        toString: function(){
            return this.attributes.sign;
        }

    });

    return CurrencyModel;

});
