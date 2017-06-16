define([

    // Libs
    'lib/view/Grid', 'jquery-ui',

    './MenuEntityList'

], function (GridView, $UI, RestaurantMenuEntityListView) {

    /**
     * @class MenuCategoryListViewForStaff
     * @extends {RestaurantMenuEntityListView}
     */
    var MenuCategoryListViewForStaff = RestaurantMenuEntityListView.extend('MenuCategoryListViewForStaff', {

        sortable: false,

        columns: [
            {
                title        : 'Actions',
                keyName      : null,
                width        : 100,
                valueRenderer: function (value, item) {
                    var btns = [
                        '<a href="#restaurant/meal/index/{%22menuCategoryId%22:{id}}" class="btn btn-small"><i class="icon-align-justify"></i> Meals</a>'
                    ];
                    return btns.join("&nbsp;").replace(/{id}/g, item.getId());
                }
            }
        ],

        initialize: function initialize() {
            this.essentialColumns = this.essentialColumns.slice(2);
            this.callParent(arguments);
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
         * @inheritDoc
         */
        getNavigationBar: function getNavigationBar() {
            return '';
        },

        /**
         * @inheritDoc
         */
        getToolbar: function getToolbar() {
            return '';
        }

    });

    return MenuCategoryListViewForStaff;

});
