define([

    // Libs
    'lib/view/Grid', 'jquery-ui',

    './MenuEntityList'

], function (GridView, $UI, RestaurantMenuEntityListView) {

    /**
     * @class MenuCategoryListView
     * @extends {RestaurantMenuEntityListView}
     */
    var MenuCategoryListView = RestaurantMenuEntityListView.extend('MenuCategoryListView', {

        columns: [
            {
                title  : 'Discount, %',
                keyName: 'discountPercent',
                width  : 100
            },
            {
                title        : 'Actions',
                keyName      : null,
                width        : 220,
                style        : {
                    'min-width': '180px'
                },
                valueRenderer: function (value, item) {
                    var btns = [
                        '<a href="#restaurant/meal/index/{%22menuCategoryId%22:{id}}" class="btn btn-small"><i class="icon-align-justify"></i> Meals</a>',
                        '<a href="#restaurant/menu-category/edit/{id}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>',
                        '<a href="#restaurant/menu-category/delete/{id}" class="btn btn-small btn-danger"><i class="icon-remove"></i> Delete</a>'
                    ];
                    return btns.join("&nbsp;").replace(/{id}/g, item.getId());
                }
            }
        ],

        /**
         * @protected
         * @return {string}
         */
        getControllerPath: function getControllerPath() {
            return '#restaurant/menu-category/';
        },

        /**
         * @protected
         * @return {?RestaurantModel}
         */
        getRestaurant: function () {
            if (this.getCollection().restaurant) {
                return this.getCollection().restaurant;
            }
            if (this.getCollection().first()) {
                return this.getCollection().first().get('restaurant');
            }
            throw new Error('restaurant MUST be set');
        },

        /**
         * @protected
         * @return {string}
         */
        getListTitle: function getListTitle() {
            return this.getRestaurant().get('name') + ' menu categories';
        },

        /**
         * @protected
         * @return {string}
         */
        getNavigationBar: function getNavigationBar() {
            var app = require('Nmotion').getInstance();
            if (app.user.isSolutionAdmin()) {
                return '<a href="#/restaurant/manage" class="back">&larr; Back to Restaurants</a>';
            } else if (app.user.isRestaurantAdmin()) {
                return '';
            }
        },

        getToolbarButtons: function getToolbarButtons() {
            var me = this,
                restaurant = me.getRestaurant(),
                params = encodeURI(JSON.stringify({restaurantId: restaurant.getId()})),
                buttons = '\
                    <a href="#restaurant/menu-category/new/' + params + '" class="btn btn-small">Add category</a>\
                ';

            return buttons + this.callParent(arguments);
        }

    });

    return MenuCategoryListView;

});
