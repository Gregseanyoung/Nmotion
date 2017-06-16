define([

    // Libs
    'lib/view/form/Symfony',

    // models
    'form/ToggleButton',
    'model/Staff'

], function (SymfonyForm, ToggleButton, StaffModel) {

    Backbone.Form.setTemplates({
        staffField: '\
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
     * @class StaffForm
     * @extends SymfonyForm
     */
    var StaffForm = SymfonyForm.extend('StaffForm', {

        model: StaffModel,

        schema: {
            email: {
                type      : 'Text',
                title     : 'Email address',
                template  : 'staffField',
                validators: ['required', 'email']
            },
            firstName    : {
                type      : 'Text',
                title     : 'First name',
                template  : 'staffField',
                validators: ['required']
            },
            lastName    : {
                type      : 'Text',
                title     : 'Last name',
                template  : 'staffField',
                validators: ['required']
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

    return StaffForm;

});
