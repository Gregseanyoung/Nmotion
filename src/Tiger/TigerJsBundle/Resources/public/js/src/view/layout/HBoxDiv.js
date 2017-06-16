define([

    // Libs
    'lib/Console', './Layout'

], function (console, Layout) {

    /**
     * @class HBoxDivLayout
     * @extends Layout
     */
    var HBoxDivLayout = Layout.extend('HBoxDivLayout', {

        layoutConfig: [],

        initialize: function () {
            console.logMethodInvoked();
            this.callParent(arguments);
        },

        doRender: function () {
            var me = this;

            me.getItems().forEach(function (item, i) {
                var el = $('<div></div>');

                if (me.layoutConfig[i]) {
                    if (me.layoutConfig[i].className) {
                        el.attr('class', me.layoutConfig[i].className);
                    }
                    if (me.layoutConfig[i].style) {
                        el.attr('style', me.layoutConfig[i].style);
                    }
                }

                el.appendTo(me.el)
                    .append(item.render().$el);
            });
        }

    });

    return HBoxDivLayout;

});
