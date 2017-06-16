define([

    // Libs
    'lib/view/form/Symfony',

    // models
    'form/ToggleButton',
    'model/MenuCategory'

], function (SymfonyForm, ToggleButton, MenuCategoryModel) {

    Backbone.Form.setTemplates({
        categoryField: '\
            <div class="control-group field-{{key}}" style="margin: 0">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    {{editor}}\
                </div>\
                <span class="help-inline">{{error}}</span>\
            </div>\
        ',
        categoryDiscountPercentField: '\
            <div class="control-group field-{{key}}">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    <div class="input-append" style="margin-right: 60px">\
                        {{editor}}\
                        <span class="add-on">%</span>\
                    </div>\
                    <div class="help-inline">{{error}}</div>\
                </div>\
            </div>\
        '
    });

    /**
     * @class MenuCategoryForm
     * @extends SymfonyForm
     */
    var MenuCategoryForm = SymfonyForm.extend('MenuCategoryForm', {

        model: MenuCategoryModel,

        events: {
            'mousewheel input[type=number]': function (event) {
                event.currentTarget.blur();
            }
        },

        schema: {
            name    : {
                type      : 'Text',
                title     : 'Category name',
                template  : 'categoryField',
                validators: ['required']
            },
            timeFrom: {
                type      : 'Time',
                title     : 'Time from',
                template  : 'categoryField'
            },
            timeTo  : {
                type      : 'Time',
                title     : 'Time to',
                template  : 'categoryField'
            },
            discountPercent: {
                type       : 'Number',
                title      : 'Discount',
                template   : 'categoryDiscountPercentField',
                editorAttrs: {
                    style: 'width: 180px'
                },
                validators : ['required']
            },
            visible: {
                type    : 'ToggleButton',
                template: 'categoryField'
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

    return MenuCategoryForm;

});
