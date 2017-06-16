define([

    // Libs
    'backbone',
    'lib/Console',
    'lib/view/Form'

], function (Backbone, console, Form) {

    var editors = Backbone.Form.editors;

    editors.List.InlineNestedModel = editors.NestedModel.extend({

        initialize: function (options) {
            editors.NestedModel.prototype.initialize.call(this, options);

/*            this.on('add', function () {
                this.trigger('readyToAdd');
            });*/
        },

        render: function () {
            editors.NestedModel.prototype.render.call(this);
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
        }

    }, {

        validatable: true

    });

    // no need to be returned as it is been used by backbone-forms as a field type

});
