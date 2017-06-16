define([

    // Libs
    'lib/Console', 'lib/Model', 'lib/view/Form'

], function (console, Model) {

    Backbone.Form.setTemplates({
        mealOptionField: '\
            <span class="field-{{key}}" style="margin-right:10px">{{editor}}<span class="help-inline">{{error}}</span></span>\
        ',
        mealOptionPriceField: '\
            <span class="input-prepend field-{{key}}" style="margin-right:10px">\
                <span class="add-on">DKK</span> {{editor}}\
            </span>\
        '
    });

    /**
     * @class MealOptionModel
     * @extends Model
     */
    var MealOptionModel = Model.extend('MealOptionModel', {

        /**
         * @protected
         */
        idAttribute: 'id',

        defaults: {
            id: null
        },

        schema: {
            id: {
                type: 'Hidden'
            },
            name: {
                type       : 'Text',
                title      : false,
                template   : 'mealOptionField',
                editorClass: 'input-medium',
                validators : ['required']
            },
            priceIncludingTax: {
                type       : 'Number',
                title      : false,
                template   : 'mealOptionPriceField',
                editorClass: 'input-mini',
                validators : ['required']
            }
        }

    });

    return MealOptionModel;

});
