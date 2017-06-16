define([

    // Lib dependencies
    'lib/Console',
    'lib/view/View',
    'lib/view/layout/HBoxDiv',

    // App dependencies
    './menu/Menu',
    './menu/SolutionAdminMenuConfigStrategy',
    './menu/RestaurantAdminMenuConfigStrategy',
    './menu/RestaurantStaffMenuConfigStrategy'

], function (

    console,
    View,
    HBoxDivLayout,

    MenuView,
    SolutionAdminMenuConfigStrategy,
    RestaurantAdminMenuConfigStrategy,
    RestaurantStaffMenuConfigStrategy

) {

    /**
     * @private
     * @type MenuView
     */
    var menu;

    /**
     * @private
     */
    var main;

    /**
     * @class DashboardView
     * @extends HBoxDivLayout
     */
    var DashboardView = HBoxDivLayout.extend('DashboardView', {

        renderTo: 'body > #container',

        el: '<div class="row-fluid"></div>',

        width: '100%',

        layoutConfig: [
            {
                className: 'span2'
            },
            {
                className: 'span10',
                style    : 'padding: 10px 20px 0 0'
            }
        ],

        widget: false,

        initialize: function () {
            this.callParent(arguments);

            /** @type {NmotionApp} */
            var app = require('Nmotion').getInstance();

            var user = app.getUser(), menuConfigStrategy;

            if (user.isSolutionAdmin()) {
                menuConfigStrategy = new SolutionAdminMenuConfigStrategy;
            } else if (user.isRestaurantAdmin()) {
                menuConfigStrategy = new RestaurantAdminMenuConfigStrategy;
            } else if (user.isRestaurantStaff()) {
                menuConfigStrategy = new RestaurantStaffMenuConfigStrategy;
            }

            menu = new MenuView(menuConfigStrategy);
            main = View.factory();

            this.addItem(menu).addItem(main);
        },

        setWidget: function (widget) {
            var me = this;

            console.logMethodInvoked();

            if (! (widget instanceof View)) {
                throw new Error('widget must inherit from View');
            }

            if (me.widget) {
                me.widget.remove();
            }

            me.widget = widget;
            main.$el.html(widget.render().el);
        }

    });

    return DashboardView;
});
