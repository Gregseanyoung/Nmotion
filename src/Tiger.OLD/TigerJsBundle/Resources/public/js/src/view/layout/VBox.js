define([

	// Libs
	'lib/Console', './Layout'

], function (console, Layout) {

	/**
	 * @class VBoxLayout
	 * @extends Layout
	 */
	var VBoxLayout = Layout.extend('VBoxLayout', {

		container: '<table cellpadding="0" cellspacing="0"><tbody></tbody></table>',

		initialize: function () {
			console.logMethodInvoked();

			this.callParent(arguments);

			if (typeof this.container == 'string') {
				this.container = Backbone.$(this.container);
			}

			if (this.width) {
				this.container.width(this.width);
			}

			this.$el.append(this.container);

			this.container = this.$el.find('tbody');
		},

		doRender: function () {
			var me = this;
			_.each(me.getItems(), function (item) {
				$('<tr><td></td></tr>')
					.appendTo(me.container)
					.find('td')
					.append(item.render().$el);
			});
		}

	});

	return VBoxLayout;

});
