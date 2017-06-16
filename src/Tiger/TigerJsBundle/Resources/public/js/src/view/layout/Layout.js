define([

	// Libs
    'lib/Console', '../View'

], function (console, View) {

	/**
	 * @class Layout
	 * @extends View
	 */
	var Layout = View.extend('Layout', /** @lends {Layout.prototype} @ignore */ {

		/**
		 * @type {?Array.<View>}
		 */
		items: null,

		initialize: function () {
			console.logMethodInvoked();
			this.callParent(arguments);
			this.items = [];
			if (this.width) {
				this.$el.width(this.width);
			}
		},

		remove: function () {
			this.purge();
			this.callParent(arguments);
		},

		purge: function () {
			_.each(this.items, function (item) {
				item.remove();
			});
			this.items.length = 0;
			return this;
		},

		/**
		 * @param {View} item
		 * @return this
		 */
		addItem: function (item) {
			item.parentView = this;
			this.items.push(item);
			return this;
		},

		/**
		 * @param {Number} index
		 * @return {View}
		 */
		getItem: function (index) {
			return this.items[index];
		},

		/**
		 * @return {Array.<View>}
		 */
		getItems: function () {
			return this.items;
		},

		/**
		 *
		 * @param {String} className
		 * @return {View}
		 */
		findViewByClassName: function (className) {
			return _.filter(this.getItems(), function (item) {
				return item.self.className === className;
			}, this);
		},

		/**
		 * @override
		 * @param {String} events
		 * @param {Function} callback
		 * @param {Function|Object} context
		 * @return this
		 */
		on: function(events, callback, context) {
			_.each(this.items, function(item) {
				item.on(events, callback, context);
			});
			return this;
		},

		doRender: function () {
			var me = this;
			_.each(me.getItems(), function (item) {
				$(item.render().$el).appendTo(me.el);
			});
		}

	});

	return Layout;

});
