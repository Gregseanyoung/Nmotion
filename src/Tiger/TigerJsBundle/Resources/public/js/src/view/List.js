define([

	// Libs
    'lib/Console', './View'

], function (console, View) {

	/**
	 * @class ListView
	 * @extends View
	 * @abstract
	 */
	var ListView = View.extend('ListView', /** @lends {ListView.prototype} @ignore */ {

		/**
		 * @protected
		 * @type {Store}
		 * Must be defined in concrete view
		 */
		collection: null,

		/**
		 * @protected
		 * Must be defined in concrete view
		 */
		containerTemplate: null,

		/**
		 * @protected
		 * Must be defined in concrete view
		 */
		itemTemplate: null,

		/**
		 * @protected
		 */
		initialize: function () {
			var me = this;

			if (! me.containerTemplate) {
				throw new Error('Container template must be defined');
			}

			if (! me.itemTemplate) {
				throw new Error('Item template must be defined');
			}

			if (_.isFunction(me.containerTemplate)) {
				me.containerTemplate = me.containerTemplate();
			}

			if (_.isFunction(me.itemTemplate)) {
				me.itemTemplate = me.itemTemplate();
			}

			me.containerTemplate = _.template(me.containerTemplate);
			me.itemTemplate = _.template(me.itemTemplate);
		},

		/**
		 * @protected
		 * @param items
		 */
		renderContainer: function (items) {
			return this.containerTemplate({items: items});
		},

		/**
		 * @protected
		 * @param item
		 */
		renderItem: function (item) {
			return this.itemTemplate({name: item.get('name')});
		},

		/**
		 * Actually, collection arg is a hack and therefore should be refactored!
		 *
		 * @protected
		 */
		renderList: function (collection) {
			console.logMethodInvoked();

			var me = this, tpl, tplItems = '', target, containerId = me.cid + '-list';

			collection = collection || me.collection;

			collection.forEach(function (item) {
				tplItems += me.renderItem(item);
			});

			tpl = me.renderContainer(tplItems);

			target = document.getElementById(containerId);
			if (!target) {
				target = document.createElement('div');
				target.id = containerId;
				me.el.appendChild(target);
			}

			target.innerHTML = tpl;
		},

		/**
		 * @param {Store} collection
		 */
		setCollection: function (collection) {
			this.collection = collection;
			return this;
		},

		/**
		 * @return {Store}
		 */
		getCollection: function () {
			return this.collection;
		},

		/**
		 * @protected
		 */
		doRender: function () {
			console.logMethodInvoked();
			this.renderList();
		}

	});

	return ListView;

});
