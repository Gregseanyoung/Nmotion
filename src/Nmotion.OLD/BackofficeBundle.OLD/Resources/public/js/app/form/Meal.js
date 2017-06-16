define([

    // Libs
    'lib/view/form/Symfony',
    'lib/view/form/InlineNestedModel',

    // App
    'form/AssetFile',
    'form/ToggleButton',
    'model/Asset',
    'model/Meal',
    'model/MealExtraIngredient',
    'model/MealOption'

], function (SymfonyForm, InlineNestedModel, AssetFile, ToggleForm, AssetModel, MealModel, MealExtraIngredientModel, MealOptionModel) {

    Backbone.Form.setTemplates({
        mealField: '\
            <div class="control-group field-{{key}}" style="margin: 0">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    {{editor}}\
                </div>\
                <span class="help-inline">{{error}}</span>\
            </div>\
        ',
        priceField: '\
            <div class="control-group field-{{key}}">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    <div class="input-prepend">\
                        <span class="add-on">DKK</span>\
                        {{editor}}\
                    </div>\
                    <div class="help-inline">{{error}}</div>\
                </div>\
            </div>\
        ',
        discountPercentField: '\
            <div class="control-group field-{{key}}">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    <div class="input-append">\
                        {{editor}}\
                        <span class="add-on">%</span>\
                    </div>\
                    <div class="help-inline">{{error}}</div>\
                </div>\
            </div>\
        ',
        mealExtraIngredientsList: '\
            <div class="bbf-list">\
                <ul class="unstyled clearfix">{{items}}</ul>\
                <button class="btn bbf-add" data-action="add"><i class="icon-plus"></i> Add</button>\
            </div>\
        ',
        mealExtraIngredientsListItem: '\
            <li class="clearfix">\
                <div class="pull-left">{{editor}}</div>\
                <button type="button" class="btn bbf-del" data-action="remove""><i class="icon-remove"></i></button>\
            </li>\
        ',
        mealOptionsList: '\
            <div class="bbf-list">\
                <ul class="unstyled clearfix">{{items}}</ul>\
                <button class="btn bbf-add" data-action="add"><i class="icon-plus"></i> Add</button>\
            </div>\
        ',
        mealOptionsListItem: '\
            <li class="clearfix">\
                <input class="radio inline pull-left" type="radio" name="defaultMealOption" style="margin-right: 10px"/>\
                <div class="pull-left">{{editor}}</div>\
                <button type="button" class="btn bbf-del" data-action="remove""><i class="icon-remove"></i></button>\
            </li>\
        '
    });

    /**
     * @class MealForm
     * @extends SymfonyForm
     */
    var MealForm = SymfonyForm.extend('MealForm', {

        /**
         * @type {MealModel}
         */
        model: MealModel,

        events: {
            'mouseenter li>button.bbf-del': function (event) {
                var btn = event.currentTarget;
                if (! btn.classList.contains('btn-danger')) {
                    btn.classList.add('btn-danger');
                    btn.firstChild.classList.add('icon-white');
                }
            },
            'mouseleave li>button.bbf-del': function (event) {
                var btn = event.currentTarget;
                if (btn.classList.contains('btn-danger')) {
                    btn.classList.remove('btn-danger');
                    btn.firstChild.classList.remove('icon-white');
                }
            },
            'mousewheel input[type=number]': function (event) {
                event.currentTarget.blur();
            }
        },

        schema: {
            name: {
                type       : 'Text',
                editorClass: 'span0',
                template   : 'mealField',
                validators : ['required']
            },
            description: {
                type       : 'Text',
                editorClass: 'span0',
                template   : 'mealField',
                validators : ['required']
            },
            logoAsset: {
                type  : 'AssetFile',
                action: '/upload/file.json',
                title : 'Photo',
                help  : 'Image dimension recommended to be within 300px x 300px'
            },
            thumbLogoAsset: {
                type  : 'AssetFile',
                action: '/upload/file.json',
                title : 'Thumbnail',
                help  : 'Image dimension recommended to be within 50px x 50px'
            },
            priceIncludingTax: {
                type      : 'Number',
                title     : 'Price inc. tax',
                template  : 'priceField',
                validators: ['required']
            },
            discountPercent: {
                type       : 'Number',
                title      : 'Discount percent',
                editorClass: 'input-small',
                template   : 'discountPercentField',
                validators : ['required']
            },
            timeFrom: {
                type       : 'Time',
                title      : 'Time from',
                editorClass: 'span0',
                template   : 'mealField'
            },
            timeTo: {
                type       : 'Time',
                title      : 'Time to',
                editorClass: 'span0',
                template   : 'mealField'
            },
            visible: {
                type    : 'ToggleButton',
                template: 'mealField'
            },
            mealExtraIngredients: {
                type            : 'List',
                itemType        : 'InlineNestedModel',
                title           : 'Additional ingredients',
                template        : 'mealField',
                listTemplate    : 'mealExtraIngredientsList',
                listItemTemplate: 'mealExtraIngredientsListItem',
                model           : MealExtraIngredientModel
            },
            mealOptions: {
                type            : 'List',
                itemType        : 'InlineNestedModel',
                title           : 'Options',
                template        : 'mealField',
                listTemplate    : 'mealOptionsList',
                listItemTemplate: 'mealOptionsListItem',
                model           : MealOptionModel
            }
        },

        formActionsTemplate: '\
            <div class="form-actions form-horizontal">\
                <button type="submit" class="btn btn-primary">Save</button>\
                <button type="button" class="btn" id="cancel">Cancel</button>\
            </div>\
        ',

        initialize: function () {
            this.callParent(arguments);
            this.on('onSubmit', this.onSubmit);
        },

        getPriceWithoutTax: function getPriceWithoutTax(modelWithPriceIncludingTax) {
            return parseFloat(
                (Math.round(modelWithPriceIncludingTax.get('priceIncludingTax') / 1.25 * 100) / 100).toFixed(2)
            );
        },

        /**
         *
         * @param {Form} form
         * @param {Object} event
         */
        onSubmit: function onSubmit(form, event) {
            var me = this, errors;

            errors = form.commit();

            var field = me.fields['mealOptions'],
                editor = field.editor,
                defaultOptionRadio = field.$el.find(':radio:checked');

            if (editor.items.length && ! defaultOptionRadio.length) {
                (errors = errors || {}).mealOptions = 'Default option is not selected';
                field.setError(errors.mealOptions);
            }

            if (_.isUndefined(errors)) {
                var optionFieldEl = defaultOptionRadio.parent().get(0);

                me.model.set('price', me.getPriceWithoutTax(me.model));
                me.model.getOptions().each(function (mealOption) {
                    mealOption.set('price', me.getPriceWithoutTax(mealOption));
                });
                me.model.getExtraIngredients().each(function (mealExtraIngredient) {
                    mealExtraIngredient.set('price', me.getPriceWithoutTax(mealExtraIngredient));
                });

                if (editor.items.length) {
                    editor.items.forEach(function (field, i) {
                        if (field.el === optionFieldEl) {
                            if (field.value instanceof MealOptionModel) {
                                me.model.set('mealOptionDefaultId', field.value.getId());
                            } else {
                                me.model.set('mealOptionDefaultId', null);

                                var mealOptions = me.model.getOptions();
                                mealOptions.map(function(model) {
                                    model.unset('isDefault');
                                });
                                mealOptions.at(i).set('isDefault', true);
                            }
                        }
                    });
                } else {
                    me.model.set('mealOptionDefaultId', null);
                }

                this.trigger('submitReady', form, event);
            }
        },

        afterRender: function afterRender() {
            this.callParent(arguments);

            this.$el.width(600);

            this.on('logoAsset:onUploadFile', function (form, editor, response) {
                var asset = AssetModel.findOrCreate(response.entries[0]);
                editor.setValue(asset);
            });
            this.on('thumbLogoAsset:onUploadFile', function (form, editor, response) {
                var asset = AssetModel.findOrCreate(response.entries[0]);
                editor.setValue(asset);
            });

            var editor = this.fields['mealOptions'].editor,
                defaultOptionId = this.model.get('mealOptionDefaultId');

            editor.items.forEach(function (field) {
                var mealOption = field.editor.value;
                if (mealOption.getId() == defaultOptionId) {
                    field.$el.find(':radio').prop('checked', 'checked');
                }
            });
        }

    });

    return MealForm;

});
