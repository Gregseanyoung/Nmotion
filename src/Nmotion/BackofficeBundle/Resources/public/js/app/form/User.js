define([

    // Libs
    'lib/view/form/Symfony',

    // models
    'form/ToggleButton',
    'model/User'

], function (SymfonyForm, ToggleButton, UserModel) {

    Backbone.Form.setTemplates({
        userField: '\
            <div class="control-group field-{{key}}" style="margin: 0">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    {{editor}}\
                    <div class="help-inline">{{error}}</div>\
                    <div class="help-block">{{help}}</div>\
                </div>\
            </div>\
        '
    });

    /**
     * @class UserForm
     * @extends SymfonyForm
     */
    var UserForm = SymfonyForm.extend('UserForm', {

        model: UserModel,

        schema: {
            id       : {
                type: 'Hidden'
            },
            email    : {
                type      : 'Text',
                title     : 'Email address',
                template  : 'userField',
                validators: ['required', 'email']
            },
            firstName: {
                type      : 'Text',
                title     : 'First name',
                template  : 'userField',
                validators: ['required']
            },
            lastName : {
                type      : 'Text',
                title     : 'Last name',
                template  : 'userField',
                validators: ['required']
            }
        }

    });

    return UserForm;

});
