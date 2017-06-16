define([

	// Libs
	'./Console', './BaseClass'

], function (console, BaseClass) {

	/**
	 * @class AppCache
	 * @extends BaseClass
	 * @author Sergey Shupylo <sshu@ciklum.com>
	 */
	var AppCache = BaseClass.define('AppCache', {

		enabled: false,

		/**
		 * @protected
		 */
		updateInterval: 60,

		/**
		 * @constructor
		 */
		constructor: function () {
			if (!this.isSupported()) {
				return;
			}

			var appCache = window.applicationCache, logEvent;

			logEvent = function (eventName) {
				appCache.addEventListener(eventName, function () {
					console.log('APPCACHE: ' + eventName.toUpperCaseFirst());
				});
			};

			if (!('chrome' in window)) {
				logEvent('cached');
				logEvent('checking');
				logEvent('noupdate');
				logEvent('updateready');
				logEvent('obsolete');
				logEvent('downloading');
				logEvent('error');
			}

			appCache.addEventListener('updateready', function () {
				if (appCache.status === appCache.UPDATEREADY) {
					appCache.swapCache();
					if (confirm('A new version is available. Reload application?')) {
						window.location.reload();
					}
				}
			});

			setInterval(
				function() {
					if (appCache.status === appCache.IDLE) {
						appCache.update();
					}
				},
				this.updateInterval * 1000
			);
		},

		/**
		 * @method
		 * @returns {Boolean}
		 */
		isSupported: function isSupported () {
			return typeof window.applicationCache !== 'undefined'
				&& document.documentElement.hasAttribute('manifest');
		}

	});

	return AppCache;

});
