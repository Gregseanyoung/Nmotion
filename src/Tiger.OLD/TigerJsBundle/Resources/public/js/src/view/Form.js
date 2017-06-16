define([

	// Libs
	'lib/Console', './View',

	// vendor dependencies
	'backbone.forms', 'bf.bootstrap', 'bf.editor.list'

], function (console, View) {

	/**
	 * @private
	 */
	var formEl;

	/**
	 * @class Form
	 *
	 * Form is a component for defining forms, rendering and binding related models.
	 *
	 * @extends View
	 * @mixes Backbone.Form
	 * @author Sergey Shupylo <sshu@ciklum.com>
	 */
	var Form = View.extend('Form', {

		mixins: {
			bf: Backbone.Form
		},

		events: {
			'submit form': function (event) {
				event.preventDefault();
				this.trigger('onSubmit', this, event);
			},
			'click button#cancel': function (event) {
				this.trigger('onCancel', this, event);
				history.back()
			}
		},

		bfTemplate: 'bootstrap',

		formActionsTemplate: '\
			<div class="form-actions form-horizontal">\
				<button type="submit" class="btn btn-primary">Save changes</button>\
				<button type="button" class="btn" id="cancel">Cancel</button>\
			</div>\
		',

		initialize: function () {
			var options = arguments[0] || {};
			if (this.schema) {
				options.schema = this.schema;
			}
			if (this.fieldsets) {
				options.fieldsets = this.fieldsets;
			}
			if ('formActionsTemplate' in options) {
				this.formActionsTemplate = options.formActionsTemplate;
			}
			this.formActionsTemplate = _.template(this.formActionsTemplate);
			this.callParent(arguments);
			this.mixins.bf.prototype.initialize.call(this, options);
		},

		/**
		 * @override
		 * DO NOT DELETE, UNLESS YOU KNOW WHAT YOU ARE DOING!
		 */
		delegateEvents: function (events) {
			this.callParent(arguments);
		},

		doRender: function () {
			// store view element to be able to restore it
			var this$El = this.$el;

			// render form itself and put it to the current view element
			formEl = this.mixins.bf.prototype.render.apply(this, arguments).$el;
			this$El.html(formEl);

			// restore view element
			this.setElement(this$El);

			// render form actions
			formEl.append(this.formActionsTemplate());
		},

		afterRender: function () {
			this.delegateEvents();
		},

		/**
		 * @override
		 * @return {Object}
		 * DO NOT DELETE, UNLESS YOU KNOW WHAT YOU ARE DOING!
		 */
		render: function () {
			return this.callParent(arguments);
		}

	});

	var editors = Backbone.Form.editors;

	editors.Checkbox = editors.Checkbox.extend({
		/**
		 * Changing method behavior to return false if not checked
		 *
		 * @return {Boolean}
		 */
		getValue: function () {
			return this.$el.prop('checked') || false;
		}
	});

	editors.File = editors.Text.extend({
		tagName: 'div',

		events: {
			'change :file': 'uploadFile'
		},

		initialize: function (options) {
			editors.Text.prototype.initialize.call(this, options);
			this.$input = $('<input type="hidden" name="' + this.key + '" />');
			this.$uploadInput = $('<input type="file" name="file"/>'); // multiple="multiple"
			this.$loader = $('<p class="upload-status"><span class="loader"></span> Uploading&hellip;</p>');
			this.$error = $('<p class="upload-error error">Error</p>');
		},

		render: function (options) {
			editors.Text.prototype.render.apply(this, arguments);

			this.$el.append(this.$loader.hide());
			this.$el.append(this.$input);
			this.$el.append(this.$uploadInput);
			this.$el.append(this.$error.hide());

			return this;
		},

		uploadFile: function () {
			var me = this;
			me.$loader.show();

			$.ajax(this.schema.action, {
				files   : me.$el.find(':file'),
				iframe  : true,
				type    : 'POST',
				dataType: "json"
			})
				.always(function () {
					me.$loader.hide();
					me.$error.hide();
					me.$uploadInput.val('');
				})
				.success(function (data, status, response) {
					me.trigger('onUploadFile', me, data);
				});
		},

		getValue: function () {
			return this.$input.val();
		},

		setValue: function (value) {
			this.$input.val(value);
			return this;
		}
	});

	editors.MaskedInput = editors.Text.extend({

		initialize: function (options) {
			editors.Text.prototype.initialize.call(this, options);
			var field = this.form.fields[this.key],
				maskedInputConfig = {
					placeholder: "_",
					completed  : function () { field.validate() }
				};
			this.$el.mask(options.mask, maskedInputConfig);
		},

		getValue: function () {
			return this.$el.val();
		},

		setValue: function (value) {
			this.$el.val(value);

			return this;
		}

	});

	editors.Time = editors.MaskedInput.extend({

		initialize: function (options) {
			options.mask = "99:99";
			editors.MaskedInput.prototype.initialize.call(this, options);
		},

		getValue: function () {
			if (!this.$el.val()) {
				return null;
			}

			var parts, hours, minutes, seconds;

			parts = this.$el.val().split(':');
			hours = parts[0].trim();
			minutes = parts[1].trim();
			seconds = (parseInt(hours, 10) * 60 + parseInt(minutes, 10)) * 60;

			return hours.length && minutes.length ? seconds : null;
		},

		setValue: function (value) {
			if (value === '' || value === null) {
				return this;
			}

			var hours, minutes, seconds;

			// evaluate hours and minutes
			seconds = Math.floor(value / 60);
			hours = Math.floor(seconds / 60);
			minutes = seconds % 60;

			// apply leading zero
			hours = (hours < 10 ? '0' : '') + hours;
			minutes = (minutes < 10 ? '0' : '') + minutes;

			// set time in format hh:mm
			this.$el.val(hours + ':' + minutes);

			return this;
		},

		validate: function () {
			var error = editors.MaskedInput.prototype.validate.call(this);
			if (error) return error;

			if (!this.$el.val()) {
				// ignore empty value
				return null;
			}

			var parts = this.$el.val().split(':'),
				hours = parts[0].trim(),
				minutes = parts[1].trim();

			if (hours.length && minutes.length) {
				hours = parseInt(hours, 10);
				minutes = parseInt(minutes, 10);

				if (hours > 23 || minutes > 59) {
					return {
						message: 'Invalid time format'
					};
				}
			}

			return null;
		}

	});

	return Form;

});
