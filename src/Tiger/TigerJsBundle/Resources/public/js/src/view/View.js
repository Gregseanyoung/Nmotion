define([

	// Libs
	'backbone', '../BaseClass'

], function (Backbone, BaseClass) {

	/**
	 * @class View
	 *
	 * View usually presents the model/collection and provides the UI events.
	 * The controller is supposed to be attached to these events to handle the
	 * user interaction.
	 *
	 * Example:
	 *
	 *     define([
	 *
	 *         // Libs
	 *         'backbone', 'tigerjs/view/View'
	 *
	 *     ], function (Backbone, View) {
	 *
	 *         var SomeView = View.extend('SomeView', {
	 *
	 *             renderTo: 'body > div#container',
	 *
	 *             render: function () {
	 *                 // some render logic
	 *                 return this;
	 *             }
	 *
	 *         });
	 *
	 *         return SomeView;
	 *
	 *     });
	 *
	 * @extends BaseClass
	 * @mixins Backbone.View
	 * @author Sergey Shupylo <sshu@ciklum.com>
	 */
	var View = BaseClass.define('View', {

		extend: Backbone.View,

		/**
		 * The jQuery selector of the element, a HTMLElement or an jQuery object that this view will be
		 * rendered into.
		 * @property {String|HTMLElement|jQuery}
		 */
		renderTo: 'body',

		/**
		 * @property {Number} width
		 */
		width: false,

		/**
		 * @protected
		 * @method
		 */
		initialize: function () {
			this.callParent(arguments);

			if (typeof this.renderTo == 'string') {
				this.renderTo = Backbone.$(this.renderTo);
			}

			if (this.width) {
				this.$el.width(this.width);
			}

			this.$el.appendTo(this.renderTo);
		},

		/**
		 * @protected
		 * @override
		 * @method
		 */
		delegateEvents: function (events) {
			if (!events) {
				var instance = this;
				events = {};
				while (instance = Object.getPrototypeOf(instance)) {
					if (instance.events) {
						_.extend(events, instance.events);
					}
				}
			}
			this.callParent([events]);
		},

		/**
		 * @protected
		 * @method
		 */
		beforeRender: function () {
		},

		/**
		 * @protected
		 * @method
		 */
		afterRender: function () {
		},

		/**
		 * @abstract
		 * @protected
		 * @method
		 */
		doRender: function () {
		},

		/**
		 * @method
		 * @chainable
		 */
		render: function () {
			this.beforeRender();
			this.doRender();
			this.afterRender();

			return this;
		}

	});

	return View;

});
