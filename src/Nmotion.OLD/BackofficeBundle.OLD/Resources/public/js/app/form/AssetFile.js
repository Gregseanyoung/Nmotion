define([

    // Libs
    'backbone',
    'lib/Console',
    'lib/view/Form',

    // App
    'model/Asset'

], function (Backbone, console, Form, AssetModel) {

    Backbone.Form.setTemplates({
        assetFileField: '\
            <div class="control-group field-{{key}}">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    <div class="input-prepend input-append">\
                        {{editor}}\
                    </div>\
                    <div class="help-block">{{help}}</div>\
                </div>\
            </div>\
        '
    });

    var editors = Backbone.Form.editors;

    editors.AssetFile = editors.File.extend({

        events: {
            'click button, input#fileFake': function () {
                this.$uploadInput.trigger('click');
            },
            'change :file': 'uploadFile'
        },

        initialize: function (options) {
            editors.File.prototype.initialize.call(this, options);

            if (this.options.schema.template === this.form.options.fieldTemplate) {
                this.options.schema.template = 'assetFileField';
            }
        },

        render: function (options) {
            if ($.browser.msie) {
                editors.File.prototype.render.apply(this, arguments);
            } else {
                this.$uploadInput.hide();

                editors.File.prototype.render.apply(this, arguments);

                this.$el.append('\
                    <span class="add-on"><i class="icon-picture"></i></span>\
                    <input type="text" id="fileFake">\
                    <button type="button" class="btn">Upload</button>\
                ');
            }

            return this;
        },

        uploadFile: function () {
            if (! $.browser.msie) {
                this.$el.find('input#fileFake').val(this.$el.find(':file').val());
            }

            editors.File.prototype.uploadFile.apply(this, arguments);
        },

        getValue: function () {
            return this.asset;
        },

        setValue: function (asset) {
            if (! (asset instanceof AssetModel) && asset !== null) {
                throw new Error('asset must be null or an instance of AssetModel, ' + typeof asset + ' given')
            }
            if (asset !== null) {
                if (this.$el.find('img').length) {
                    this.$el.find('img').prop('src', asset.get('url'));
                } else {
                    this.$el.prepend('<img src="' + asset.get('url') + '"/><br/>');
                }
            }
            this.asset = asset;
            return this;
        }

    });

    // should not be returned as it is been used by backbone-forms as a field type

});
