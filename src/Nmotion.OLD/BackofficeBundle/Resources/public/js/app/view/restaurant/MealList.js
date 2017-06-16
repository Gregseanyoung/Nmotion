define([

    // Libs
    'lib/view/Grid', 'jquery-ui',

    './MenuEntityList'

], function (GridView, $UI, RestaurantMenuEntityListView) {

    /**
     * @class MealListView
     * @extends {RestaurantMenuEntityListView}
     */
    var MealListView = RestaurantMenuEntityListView.extend('MealListView', {

        id: 'mealList',

        columns: [
            {
                title  : 'Description',
                keyName: 'description'
            },
            {
                title  : 'Price inc. tax',
                keyName: 'priceIncludingTax',
                width  : 100
            },
            {
                title  : 'Discount, %',
                keyName: 'discountPercent',
                width  : 100
            },
            {
                title        : 'Actions',
                keyName      : null,
                width        : 140,
                valueRenderer: function (value, item) {
                    var btns = '\
                        <a href="#restaurant/meal/edit/{id}" class="btn btn-small"><i class="icon-edit"></i> Edit</a> \
                        <a href="#restaurant/meal/delete/{id}" class="btn btn-small btn-danger"><i class="icon-remove"></i> Delete</a>';
                    return btns.replace(/{id}/g, item.get('id'));
                }
            }
        ],

        /**
         * @protected
         * @return {string}
         */
        getControllerPath: function getControllerPath() {
            return '#restaurant/meal/';
        },

        /**
         * @protected
         * @return {MenuCategoryModel}
         */
        getMenuCategory: function () {
            if (this.getCollection().menuCategory) {
                return this.getCollection().menuCategory;
            }
            throw new Error('menu category MUST be set');
        },

        /**
         * @protected
         * @return {string}
         */
        getListTitle: function getListTitle() {
            return 'Meals in ' + this.getMenuCategory().get('name') + ' menu category';
        },

        /**
         * @protected
         * @return {string}
         */
        getNavigationBar: function getNavigationBar() {
            var restaurantId = this.getMenuCategory().getRestaurant().getId(),
                url = '#restaurant/menu-category/index/{%22restaurantId%22:' + restaurantId + '}';
            return '<a href="' + url + '" class="back">&larr; Back to Сategories</a>';
        },

        getToolbarButtons: function getToolbarButtons() {
            var me = this,
                menuCategory = me.getMenuCategory(),
                params = encodeURI(JSON.stringify({menuCategoryId: menuCategory.getId()})),
                buttons = '\
                    <a href="#restaurant/meal/new/' + params + '" class="btn btn-small">Add meal</a>\
                ';

            return buttons + this.callParent(arguments);
        }

    });

    return MealListView;

});
