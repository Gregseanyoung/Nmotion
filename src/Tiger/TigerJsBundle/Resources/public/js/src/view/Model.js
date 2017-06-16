define([

	// Libs
	'./View'

], function (View) {

	/**
	 * @class ModelView
	 * @extends View
	 */
	var ModelView = View.extend('ModelView', {

		/**
		 * @property {Model}
		 */
		model: null,

		/**
		 * Allows various configurations to be set for each property.

		 * @property
		 * @type {Array.<Object>}
		 */
		propertiesConfig: [],

		/**
		 * @param {Model} model
		 */
		setModel: function (model) {
			this.model = model;
			return this;
		},

		/**
		 * @protected
		 * @param container
		 * @param value
		 * @param key
		 */
		renderProperty: function (container, key, value) {
			var me = this, dt, dd, div;

			dt = $('<dt></dt>');
			dd = $('<dd></dd>');
			container.append(dt);
			container.append(dd);

			dt.text(key);

			if (_.isArray(value)) {
				dd.addClass('well');
				value.forEach(function (arrayItem) {
					if (typeof arrayItem === 'string') {
						dd.append('<div>' + arrayItem + '</div>');
					}
					else if (_.isObject(value)) {
						div = $('<div style="padding-bottom: 10px"></div>');
						dd.append(div);
						_(arrayItem).each(function (subValue, subKey) {
							me.renderProperty(div, subKey, subValue);
						});
					}
				});
			}
			else if (_.isObject(value)) {
				dd.addClass('well');
				_(value).each(function (subValue, subKey) {
					me.renderProperty(dd, subKey, subValue);
				});
			}
			else {
				dd.text(value);
			}

		},

		/**
		 * @public
		 */
		doRender: function () {
			var me = this,
				properties = me.model.toJSON(),
				container = $('<dl class="dl-horizontal"></dl>');

			_(properties).each(function (value, key) {
				me.renderProperty(container, key, value);
			});

			me.$el.html(container);

			return this.callParent(arguments);
		}

	});

	return ModelView;

});
