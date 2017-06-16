define([

    // Libs
    'lib/view/form/Symfony',

    // models
    'model/Currency'

], function (SymfonyForm, CurrencyModel) {

    Backbone.Form.setTemplates({
        configField: '\
            <div class="control-group field-{{key}}" style="margin: 0">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    {{editor}}\
                </div>\
                <span class="help-inline">{{error}}</span>\
            </div>\
        '
    });

    /**
     * @class CurrencyForm
     * @extends SymfonyForm
     */
    var CurrencyForm = SymfonyForm.extend('CurrencyForm', {

        model: CurrencyModel,

        schema: {
            sign    : {
                type      : 'Text',
                title     : 'Sign',
                template  : 'configField',
                validators: ['required']
            },
            description: {
                type      : 'Text',
                template  : 'configField'
            }
        },

        formActionsTemplate: '\
            <div class="form-actions form-horizontal">\
                <button type="submit" class="btn btn-primary">Save</button>\
                <button type="button" class="btn" id="cancel">Cancel</button>\
            </div>\
        ',

        afterRender: function () {
            this.$el.width(500);
            this.callParent(arguments);
        }

    });

    return CurrencyForm;

});
