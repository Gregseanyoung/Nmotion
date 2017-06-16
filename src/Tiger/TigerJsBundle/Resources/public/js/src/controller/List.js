define([

	// Libs
	'lib/Console', './Controller'

], function (console, Controller) {

	/**
	 * @class ListController
	 * @extends Controller
	 * @event onCollectionAdd
	 * @event onCollectionSync
	 * @event onCollectionChange
	 * @event onCollectionDestroy
	 * @event onCollectionReset
	 * @event onCollectionSort
	 */
	var ListController = Controller.extend('ListController', {

		/**
		 * @type {Store}
		 */
		collection: null,

		/**
		 * @protected
		 * @constructor
		 * @param {Application} application
		 */
		constructor: function (application) {
			console.logMethodInvoked();

			var me = this;

			me.callParent(arguments);

			if (!me.collection) {
				throw new Error('Collection must be defined');
			}

			if (me.collection.isClass) {
				me.collection = new me.collection;
			}

			me.setCollection(me.collection);
		},

		setCollection: function (collection) {
			var me = this,
				eventsToWrap = ['sync', 'change', 'add', 'reset', 'destroy', 'sort'];

			me.collection.off();
			me.collection = collection;

			eventsToWrap.forEach(function (event) {
				me.collection.on(event, function () {
					arguments = _.toArray(arguments);
					arguments.unshift('onCollection' + event.toUpperCaseFirst());
					me.trigger.apply(me, arguments);
				});
			});
		},

		/**
		 * @protected
		 * @param {Function=} callback
		 */
		getData: function (callback) {
			this.collection.fetch({
				success: function () {
					if (_(callback).isFunction()) {
						callback();
					}
				}
			});
		}

	});

	return ListController;

});
