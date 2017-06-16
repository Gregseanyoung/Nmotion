define([

	// Libs
	'./BaseClass', './AppCache'

], function (BaseClass, AppCache) {

	/**
	 * @class Application
	 * @extends BaseClass
	 * @author Sergey Shupylo <sshu@ciklum.com>
	 */
	var Application = BaseClass.define('Application', {

		name: 'Application',

		/**
		 * @private
		 */
		docRoot: null,

		/**
		 * @type {AppCache}
		 */
		cache: AppCache,

		/**
		 * @type {View} instance of View
		 */
		mainView: null,

		/**
		 * @constructor
		 */
		constructor: function () {
			if (this.cache.isClass) {
				this.cache = new this.cache;
			}
		},

		run: function run() {
		},

		/**
		 * @method
		 * @param {String}  url
		 * @param {*?}      params
		 * @param {Object?} options
		 */
		navigate: function (url, params, options) {
			if (params) {
				url = url.replace(/\/$/, '') + '/'
					+ (_.isObject(params) ? encodeURI(JSON.stringify(params)) : params.toString());
			}
			Backbone.history.navigate(url, options);
		}

	});

	return Application;

});
