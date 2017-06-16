define([

    // Libs
    'lib/view/Grid', 'jquery-ui',

    './MenuEntityList'

], function (GridView, $UI, RestaurantMenuEntityListView) {

    /**
     * @class MealListViewForStaff
     * @extends {RestaurantMenuEntityListView}
     */
    var MealListViewForStaff = RestaurantMenuEntityListView.extend('MealListViewForStaff', {

        id: 'mealList',

        sortable: false,

        events: {
            'click button#hideBtn': function (event) {
                var id = parseInt($(event.currentTarget).parents('tr').attr('data-id'));
                this.trigger('hideBtnClick', this, id);
            },
            'click button#showBtn': function (event) {
                var id = parseInt($(event.currentTarget).parents('tr').attr('data-id'));
                this.trigger('showBtnClick', this, id);
            }
        },

        columns: [
            {
                title        : 'Actions',
                keyName      : null,
                width        : 100,
                valueRenderer: function (value, item) {
                    var btn;
                    if (item.get('visible')) {
                        btn = '<button id="hideBtn" class="btn btn-small btn-inverse">Hide</button>';
                    } else {
                        btn = '<button id="showBtn" class="btn btn-small btn-success">Show</button>';
                    }
                    return btn;
                }
            }
        ],

        initialize: function initialize() {
            this.essentialColumns = this.essentialColumns.slice(2);
            this.callParent(arguments);
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
            return '';
        }

    });

    return MealListViewForStaff;

});
