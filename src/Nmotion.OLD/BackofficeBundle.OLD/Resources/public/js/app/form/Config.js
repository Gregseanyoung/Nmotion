define([

    // Libs
    'lib/view/form/Symfony',

    // models
    'form/ToggleButton',
    'model/Config'

], function (SymfonyForm, ToggleButton, ConfigModel) {

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
     * @class ConfigForm
     * @extends SymfonyForm
     */
    var ConfigForm = SymfonyForm.extend('ConfigForm', {

        model: ConfigModel,

        schema: {
            name    : {
                type      : 'Text',
                title     : 'Config name',
                template  : 'configField',
                validators: ['required']
            },
            description: {
                type      : 'Text',
                template  : 'configField'
            },
            value: {
                type      : 'Text',
                template  : 'configField',
                validators: ['required']
            },
            system: {
                type    : 'ToggleButton',
                template: 'configField'
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

    return ConfigForm;

});
