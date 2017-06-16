define([

    // Libs
    './MenuConfigStrategy'

], function (MenuConfigStrategy) {

    /**
     * @class SolutionAdminMenuConfigStrategy
     * @extends MenuConfigStrategy
     */
    var SolutionAdminMenuConfigStrategy = MenuConfigStrategy.extend('SolutionAdminMenuConfigStrategy', {

        /**
         * @public
         * @override
         * @returns {Array}
         */
        getMenuConfig: function getMenuConfig () {
            return [
                { html: '<a href="/backoffice/"><i class="icon-home"></i> Dashboard</a>' },
                { className: 'divider' },
                { html: '<a href="#config">Configs list</a>' },
                { html: '<a href="#currency">Currencies list</a>' },
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
        }

    });

    return SolutionAdminMenuConfigStrategy;

});
