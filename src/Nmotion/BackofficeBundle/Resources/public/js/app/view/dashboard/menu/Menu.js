define([

    // Libs
    'jquery', 'lib/view/View'

], function ($, View) {

    /**
     * @class MenuView
     * @extends View
     */
    var MenuView = View.extend('MenuView', /** @lends MenuView.prototype */ {

        el: '<div class="sidebar-nav"></div>',

        /**
         * @type MenuConfigStrategy
         */
        menuConfigStrategy: null,

        /**
         * @constructor
         * @param menuConfigStrategy
         */
        constructor: function (menuConfigStrategy) {
            this.callParent();
            this.menuConfigStrategy = menuConfigStrategy;
        },

        /**
         * @protected
         * @returns {Array}
         */
        getMenuConfig: function getMenuConfig() {
            return this.menuConfigStrategy.getMenuConfig();
        },

        /**
         * Render menu using specified menu config
         *
         * Implemented in VanillaJS for best performance
         *
         * @protected
         * @param {Array<Object>} menuConfig
         * @return {HTMLElement}
         */
        getMenu: function getMenu(menuConfig) {
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
            var me = this, logo, innerDiv, outerDiv;

            logo = document.createElement('img');
            logo.id = 'logo';
            logo.src = '/bundles/nmotionbackoffice/images/logo_vector.png';
            me.el.appendChild(logo);

            innerDiv = document.createElement('div');
            innerDiv.className = 'well';
            innerDiv.style.padding = '8px 0';
            innerDiv.appendChild(
                me.getMenu(me.getMenuConfig())
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
