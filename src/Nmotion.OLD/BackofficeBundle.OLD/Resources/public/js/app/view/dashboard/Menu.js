define([

    // Libs
    'jquery', 'lib/view/View'

], function ($, View) {

    /** @type {NmotionApp} */
    var app = require('Nmotion').getInstance();

    /**
     * @class MenuView
     * @extends View
     */
    var MenuView = View.extend('MenuView', {

        el: '<div class="sidebar-nav"></div>',

        /**
         * @protected
         */
        initialize: function () {
            var me = this;
            me.callParent(arguments);
        },

        getSolutionAdminMenuConfig: function () {
            return [
                { html: '<a href="/backoffice/"><i class="icon-home"></i> Dashboard</a>' },
                { className: 'divider' },
                { html: '<a href="#config">Configs list</a>' },
                { className: 'nav-header', text: 'Restaurants Management' },
                { html: '<a href="#restaurant/register">Add restaurant</a>' },
                { html: '<a href="#restaurant/manage">List all restaurants</a>' },
                { className: 'nav-header', text: 'Users Management' },
                { html: '<a href="#user"></i> List all users</a>' },
                { className: 'nav-header', text: 'Orders Management' },
                { html: '<a href="#order"></i> List all orders</a>' },
                { className: 'divider' },
                { html: '<a href="#logout"><i class="icon-share"></i> Logout</a>' }
            ];
        },

        getRestaurantAdminMenuConfig: function () {
            return [
                { html: '<a href="/backoffice/"><i class="icon-home"></i> Dashboard</a>' },
                { className: 'nav-header', text: 'Restaurant Management' },
                { html: '<a href="#restaurant/menu-category">Menu categories</a>' },
                { className: 'divider' },
                { html: '<a href="#logout"><i class="icon-share"></i> Logout</a>' }
            ];
        },

        /**
         * Render menu using specified menu config
         *
         * Implemented in VanillaJS for best performance
         *
         * @param {Array} menuConfig
         * @return {HTMLElement}
         */
        getMenu: function (menuConfig) {
            var menuContainer, menuItem;

            menuContainer = document.createElement('ul');
            menuContainer.className = 'nav nav-list';

            menuConfig.forEach(function (menuItemConfig) {
                menuItem = document.createElement('li');

                if (menuItemConfig.html) {
                    menuItem.innerHTML = menuItemConfig.html;
                }
                else if (menuItemConfig.text) {
                    menuItem.appendChild(
                        document.createTextNode(menuItemConfig.text)
                    );
                }

                if (menuItemConfig.className) {
                    menuItem.className = menuItemConfig.className;
                }

                menuContainer.appendChild(menuItem);
            });

            return menuContainer;
        },

        render: function () {
            var me = this, menuConfig, logo, innerDiv, outerDiv;

            if (app.getUser().isSolutionAdmin()) {
                menuConfig = me.getSolutionAdminMenuConfig();
            } else if (app.getUser().isRestaurantAdmin()) {
                menuConfig = me.getRestaurantAdminMenuConfig();
            }

            logo = document.createElement('img');
            logo.id = 'logo';
            logo.src = '/bundles/nmotionbackoffice/images/logo.gif';
            logo.style.marginLeft = '20px';
            me.el.appendChild(logo);

            innerDiv = document.createElement('div');
            innerDiv.className = 'well';
            innerDiv.style.padding = '8px 0';
            innerDiv.appendChild(
                me.getMenu(menuConfig)
            );

            outerDiv = document.createElement('div');
            outerDiv.id = 'mainmenu';
            outerDiv.appendChild(innerDiv);

            me.el.appendChild(outerDiv);

            return me;
        }

    });

    return MenuView;

});
