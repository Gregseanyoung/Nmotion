define([

    // Libs
    'backbone',
    'lib/Console',
    'lib/view/Form',

    // App
    'jquery.toggleButtons'

], function (Backbone, console) {

    var editors = Backbone.Form.editors;

    editors.ToggleButton = editors.Checkbox.extend({

        render: function (options) {
            editors.Checkbox.prototype.render.apply(this, arguments);

            if (this.el.hasAttribute('readonly')) {
                this.el.disabled = true;
            }

            this.$el.wrap('<div></div>');
            this.setElement(this.$el.parent());
            this.$el.toggleButtons(
                {
                    label: {
                        enabled : 'Yes',
                        disabled: 'No'
                    }
                }
            );

            return this;
        },

        getValue: function () {
            return this.$el.find('input').prop('checked') || false;
        }

    });

    // should not be returned as it is been used by backbone-forms as a field type

});
