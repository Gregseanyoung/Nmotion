define([

	// Libs
	'backbone', 'backbone.relational', './BaseClass', 'jquery.pnotify'

], function (Backbone, BackboneRelational, BaseClass) {

	/**
	 * @class Model
	 *
	 * The domain-specific representation of the information on which the application operates.
	 * The model is another name for the domain layer. Domain logic adds meaning to raw data
	 * (e.g., calculating if today is the user's birthday, or the totals, taxes and shipping
	 * charges for shopping cart items).
	 *
	 * @extends Backbone.RelationalModel
	 * @mixins Backbone.Model
	 * @mixins BaseClass
	 * @requires Backbone
	 * @requires Backbone.RelationalModel
	 * @author Sergey Shupylo <sshu@ciklum.com>
	 */
	var Model = BaseClass.define('Model', /** @lends {Model.prototype} @ignore */ {

		extend: Backbone.RelationalModel,

		inheritableStatics: Backbone.RelationalModel,

		constructor: function () {
			this.constructor.initializeModelHierarchy = Backbone.RelationalModel.initializeModelHierarchy;
			Backbone.RelationalModel.apply(this, arguments);
		},

		/**
		 * Return the first entry in 'data.entries' if it exists, or else just plain 'data'.
         *
		 * @protected
		 * @param {Object} data
		 * @return {Array.<Object>}
		 */
		parse: function (data) {
			if (data && data.entries) {
				return data.entries[0];
			} else {
				return data;
			}
		},

		/**
		 * @return {Number}
		 * @inheritable
		 */
		getId: function getId() {
			return this.get(this.idAttribute);
		},

		destroy: function destroy(options) {
			options = options || {};
			if (!options.hasOwnProperty('error')) {
				options.error = function (model, responce, options) {
					var message = responce.responseText;
					if (message[0] === '{') {
						message = JSON.parse(message).message;
					}

					$.pnotify({
						title: 'Error',
						text : message,
						type : 'error',
						hide : false,
						icon : false
					});
				}
			}

			this.callParent([options]);
		}

	});

	/**
	 * @override
	 * @return {Model}
	 */
	Model.extend = function extend() {
		var child = BaseClass.extend.apply(this, arguments);

		Model.setup.call(child, this);

		return child;
	};

	return Model;

});
