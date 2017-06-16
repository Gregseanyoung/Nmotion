define([

    // Libs
    './MenuConfigStrategy'

], function (MenuConfigStrategy) {

    /**
     * @class RestaurantStaffMenuConfigStrategy
     * @extends MenuConfigStrategy
     */
    var RestaurantStaffMenuConfigStrategy = MenuConfigStrategy.extend('RestaurantStaffMenuConfigStrategy', {

        /**
         * @public
         * @override
         * @returns {Array}
         */
        getMenuConfig: function getMenuConfig () {
            return [
                { html: '<a href="/backoffice/"><i class="icon-home"></i> Dashboard</a>' },
                { className: 'nav-header', text: 'Restaurant Management' },
                { html: '<a href="#restaurant/menu-category">Menu</a>' },
                { className: 'divider' },
                { html: '<a href="#logout"><i class="icon-share"></i> Logout</a>' }
            ];
        }

    });

    return RestaurantStaffMenuConfigStrategy;

});
