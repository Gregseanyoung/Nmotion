define([

    // Libs
    './Layout'

], function (Layout) {

    /**
     * @class HBoxLayout
     * @extends Layout
     */
    var HBoxLayout = Layout.extend('HBoxLayout', {

        container: '<table cellpadding="0" cellspacing="0"><tbody><tr></tr></tbody></table>',

        verticalAlign: 'top',

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

            this.container = this.$el.find('tbody>tr');
        },

        doRender: function () {
            var me = this;
            _.each(me.getItems(), function (item) {
                $('<td></td>')
                    .css('vertical-align', me.verticalAlign)
                    .appendTo(me.container)
                    .append(item.render().$el);
            });
        }

    });

    return HBoxLayout;

});
