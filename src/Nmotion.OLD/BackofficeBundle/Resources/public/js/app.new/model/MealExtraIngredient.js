define([

    // Libs
    'lib/Console', 'lib/Model', 'lib/view/Form'

], function (console, Model) {

    Backbone.Form.setTemplates({
        mealExtraIngredientField: '\
            <span class="field-{{key}}" style="margin-right:10px">{{editor}}<span class="help-inline">{{error}}</span></span>\
        ',
        mealExtraIngredientPriceField: '\
            <span class="input-prepend field-{{key}}" style="margin-right:10px">\
                <span class="add-on">DKK</span> {{editor}}\
            </span>\
        '
    });

    /**
     * @class MealExtraIngredientModel
     * @extends Model
     */
    var MealExtraIngredientModel = Model.extend('MealExtraIngredientModel', {

        /**
         * @protected
         */
        idAttribute: 'id',

        defaults: {
            id: null
        },

        schema: {
            id          : {
                type: 'Hidden'
            },
            name    : {
                type      : 'Text',
                title     : false,
                template  : 'mealExtraIngredientField',
                editorClass: 'input-medium',
                validators: ['required']
            },
            priceIncludingTax: {
                type       : 'Number',
                title      : false,
                template   : 'mealExtraIngredientPriceField',
                editorClass: 'input-mini',
                validators : ['required']
            }
        }

    });

    return MealExtraIngredientModel;

});
