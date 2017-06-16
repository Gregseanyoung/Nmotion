define([

    // Libs
    './MenuConfigStrategy'

], function (MenuConfigStrategy) {

    /**
     * @class RestaurantAdminMenuConfigStrategy
     * @extends MenuConfigStrategy
     */
    var RestaurantAdminMenuConfigStrategy = MenuConfigStrategy.extend('RestaurantAdminMenuConfigStrategy', {

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
                { html: '<a href="#restaurant/income">Income</a>' },
                { html: '<a href="#restaurant/staff/list">Staff</a>' },
                { html: '<a href="#restaurant/profile/edit">Edit Profile</a>' },
                { className: 'divider' },
                { html: '<a href="#logout"><i class="icon-share"></i> Logout</a>' }
            ];
        }

    });

    return RestaurantAdminMenuConfigStrategy;

});
