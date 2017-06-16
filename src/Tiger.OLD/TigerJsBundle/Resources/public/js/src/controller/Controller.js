define([

	// Libs
	'backbone', 'lib/Console', 'lib/BaseClass'

], function (Backbone, console, BaseClass) {

	Backbone.Events.prototype = Backbone.Events;

	/**
	 * Processes and responds to events, typically user actions, and invokes changes on the model and the view.
	 *
	 * @class Controller
	 * @extends BaseClass
	 * @mixins Backbone.Events
	 *
	 * @requires Backbone
	 */
	var Controller = BaseClass.define('Controller', /** @lends {Controller.prototype} @ignore */ {

		mixins: [
			Backbone.Events
		],

		/**
		 * @protected
		 * @type {Application}
		 */
		application: null,

		/**
		 * @protected
		 * @type {View}
		 */
		parentView: null,

		/**
		 * @constructor
		 * @param {Application} application
		 */
		constructor: function (application) {
			console.logMethodInvoked();
			this.application = application;
			this.parentView = application.mainView;
		},

		/**
		 * Interceptor method
		 *
		 * @protected
		 */
		beforeAction: function beforeAction() {
		},

		/**
		 * Interceptor method
		 *
		 * @protected
		 */
		afterAction: function afterAction() {
		},

		/**
		 * @param {String} action
		 * @param {Object} params
		 * @memberOf Controller.prototype
		 */
		invokeAction: function invokeAction(action, params) {
			var methodName;

			action = (action ? action.toLowerCase() : 'index');

			if (action.indexOf('-') !== -1) {
				action = action.split('-').map(function (str) {
					return str.toUpperCaseFirst();
				}).join('').toLowerCaseFirst();
			}

			methodName = action + 'Action';

			if (!(methodName in this)) {
				throw new Error('Action "' + action + '" was not found on controller "' + this.getClassName() + '"');
			}

			this.beforeAction();
			console.debug('The ' + methodName + ' method of the controller ' + this.getClassName() + ' is going to be invoked with params:', params);
			this[methodName].call(this, params);
			this.afterAction();
		},

		/**
		 * Proxy-method to Application#navigate with changed behaviour a bit in place of
		 * default value for `trigger` option
		 *
		 * @param {String} url
		 * @param {Object=|Number=|String=} params
		 * @param {Object=} options
		 *
		 * @see Application#navigate
		 */
		navigate: function navigate(url, params, options) {
			options = options || {trigger: true};
			this.application.navigate(url, params, options);
		}

	});

	/**
	 * @static
	 * @return {Controller}
	 */
	Controller.getInstance = function () {
		var self = this.self;
		if (!self.instance) {
			self.instance = new self(arguments[0], arguments[1]); // dirty hack :(
		}
		return self.instance;
	};

	return Controller;

});
