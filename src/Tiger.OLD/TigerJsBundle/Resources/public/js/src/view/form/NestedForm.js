define([

    // Libs
    'backbone',
    'lib/Console',
    'lib/view/Form'

], function (Backbone, console, Form) {

    var editors = Backbone.Form.editors;

    editors.NestedForm = editors.Object.extend({

        initialize: function (options) {
            editors.Base.prototype.initialize.call(this, options);

            if (! options.schema.form) {
                throw 'Missing required "schema.form" option for NestedForm editor';
            }
        },

        render: function () {
            var data = this.value || {},
                formClass = this.schema.form,
                nestedFormModel = formClass.prototype.model;

            //Wrap the data in a model if it isn't already a model instance
            var modelInstance = (data.constructor === nestedFormModel) ? data : new nestedFormModel(data);

            this.form = new formClass({
                model              : modelInstance,
                idPrefix           : this.id + '_',
                // kinda hack
                formActionsTemplate: ''
            });

            if (this.el.hasAttribute('readonly')) {
                _(this.form.schema).each(function (field) {
                    if (! ('editorAttrs' in field)) {
                        field.editorAttrs = {};
                    }
                    field.editorAttrs.readonly = 'readonly';
                })
            }

            this._observeFormEvents();

            //Render form
            this.$el.html(this.form.render().el);

            if (this.hasFocus) this.trigger('blur', this);

            return this;
        },

        getValue: function () {
            if (this.form) {
                return this.form.getValue();
            }

            return this.value;
        },

        setValue: function (value) {
            this.value = value;

            this.render();
        },

        validate: function () {
            return this.form.validate();
        },

        /**
         * Update the embedded model, checking for nested validation errors and pass them up
         * Then update the main model if all OK
         *
         * @return {Error|null} Validation error or null
         */
        commit: function() {
          var error = this.form.commit();
          if (error) {
            this.$el.addClass('error');
            return error;
          }

          return editors.Object.prototype.commit.call(this);
        }

    });

    // should not be returned as it is been used by backbone-forms as a field type

});
