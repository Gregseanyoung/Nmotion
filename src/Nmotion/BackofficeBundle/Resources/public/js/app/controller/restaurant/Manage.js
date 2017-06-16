define([

    // library dependencies
    'lib/controller/List',

    // application dependencies
    'collection/Restaurant', 'model/Restaurant', 'view/restaurant/List'

], function (ListController, RestaurantCollection, RestaurantModel, RestaurantListView) {

    /**
     * @class RestaurantManageController
     * @extends ListController
     */
    var RestaurantManageController = ListController.extend('RestaurantController', {

        /**
         * @type {RestaurantCollection}
         */
        collection: RestaurantCollection,

        /**
         * @protected
         * @constructor
         */
        constructor: function () {
            this.callParent(arguments);
            this.collection.setRemoteSort(true);
        },

        /**
         * @protected
         * @param {RestaurantListView} view
         * @param {Number[]} idsArray
         * @param {Boolean} visible
         */
        batchVisibilityChange: function (view, idsArray, visible) {
            idsArray.forEach(function (id) {
                var restaurant = RestaurantModel.findOrCreate(id);
                restaurant.save({visible: visible}, {async: false, wait: true});
            });
        },

        /**
         * @return {RestaurantListView}
         */
        createListView: function () {
            var me = this, view;

            /** @type {RestaurantListView} */
            view = new RestaurantListView();
            view.setCollection(me.collection);
            view.on('pageChange', function (page) {
                me.collection.fetchPage(page);
            });
            view.on('pageSizeChange', function (pageSize) {
                me.collection.setPageSize(pageSize).fetch();
            });
            view.on('sortChange', function (field, direction) {
                me.collection.sortBy(field, direction).sort();
            });
            view.on('searchChange', function (query) {
                me.collection.filterBy('search', query.trim()).fetchPage(1);
            });
            view.on('batchVisibilityChange', me.batchVisibilityChange);

            me.parentView.setWidget(view);

            return view;
        },

        indexAction: function () {
            var me = this, view;

            view = me.createListView();

            me.off('onCollectionSync').on('onCollectionSync', function (collection, response, options) {
                if (!(collection instanceof RestaurantCollection)) {
                    return;
                }

                view.render();

                if (collection.isEmpty()) {
                    $.pnotify({
                        text : 'No restaurants found',
                        type : 'info',
                        opacity: .85,
                        sticker: false
                    });
                }
            });

            me.off('onCollectionSort').on('onCollectionSort', function () {
                view.render();
            });

            me.off('onCollectionChange').on('onCollectionChange', function () {
                view.render();
            });

            if (me.collection.isEmpty()) {
                me.getData();
            }
        }

    });

    return RestaurantManageController;

});
