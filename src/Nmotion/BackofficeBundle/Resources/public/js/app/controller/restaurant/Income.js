define([

    // library dependencies
    'lib/Console',
    'lib/controller/List',

    // application dependencies
    'collection/Income',
    'collection/Order',
    'model/Income',
    'model/Order',
    'model/Restaurant',
    'view/restaurant/IncomeList',
    'view/restaurant/income/OrderList'

], function (

    console,
    ListController,

    IncomeCollection,
    OrderCollection,
    IncomeModel,
    OrderModel,
    RestaurantModel,
    IncomeListView,
    IncomeOrderListView

) {

    /**
     * @class RestaurantIncomeController
     * @extends ListController
     */
    var RestaurantIncomeController = ListController.extend('RestaurantIncomeController', {

        /**
         * @type {NmotionApp}
         */
        app: undefined,

        /**
         * @type {RestaurantModel}
         */
        restaurant: null,

        /**
         * @type {String}
         */
        period: 'd',

        /**
         * @type {IncomeCollection}
         */
        collection: IncomeCollection,

        /**
         * @type {OrderCollection}
         */
        orderCollection: OrderCollection,

        /**
         * @protected
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
            this.app = application;
        },

        /**
         * @return {IncomeListView}
         */
        createListView: function () {
            var me = this,
                /** @type {IncomeListView} */ view,
                user = this.app.getUser();

            me.collection.setExtraParam('period', this.period);

            view = new IncomeListView();
            view.setCollection(me.collection);
            view.on('periodChange', function (newPeriod) {
                me.collection.setExtraParam('period', newPeriod).fetch();
            });
            view.on('incomeDetails', function (cid) {
                var params = {
                    restaurantId: me.restaurant.getId(),
                    period      : me.collection.getExtraParam('period'),
                    sn          : me.collection.get(cid).getId()
                };
                me.navigate('restaurant/income/detail', params, {trigger: true});
            });
            view.on('sortChange', function (field, direction) {
                me.collection.sortBy(field, direction).sort();
            });

            if (user.isRestaurantAdmin()) {
                view.disableBackBtn();
            }

            me.parentView.setWidget(view);

            view.setPeriod(this.period);

            return view;
        },

        /**
         * @return {IncomeOrderListView}
         */
        createOrdersListView: function (income) {
            var me = this, view;

            me.orderCollection = new OrderCollection;
            me.orderCollection.on('sync', function () { view.render(); });
            me.orderCollection.on('sort', function () { view.render(); });

            /** @type {IncomeOrderListView} */
            view = new IncomeOrderListView();
            view.setCollection(me.orderCollection);
            view.setIncome(income);
            view.on('navigateBack', function () {
                var params = {
                    id    : me.restaurant.getId(),
                    period: me.collection.getExtraParam('period')
                };
                me.navigate('restaurant/income/index', params, {trigger: true});
            });
            view.on('sortChange', function (field, direction) {
                me.orderCollection.sortBy(field, direction).sort();
            });

            me.parentView.setWidget(view);

            return view;
        },

        setRestaurant: function (restaurantOrId) {
            var me = this, restaurant, incomes;

            if (! (restaurantOrId instanceof RestaurantModel)) {
                restaurant = RestaurantModel.findOrCreate(restaurantOrId);
                if (restaurant === null) {
                    restaurant = new RestaurantModel({id: restaurantOrId});
                    restaurant.fetch({async: false});
                }
            } else {
                restaurant = restaurantOrId;
            }
            me.restaurant = restaurant;

            // set period to be used when requesting incomes records
            restaurant.get('incomes').setExtraParam('period', this.period);

            incomes = restaurant.getIncomes();
            incomes.sortBy('SN', 'DESC').sort();
            me.setCollection(incomes);
        },

        /**
         * Index action used to present aggregated income data for requested restaurant
         * @param {Object} params
         */
        indexAction: function (params) {
            var me = this, view;

            if ('period' in params) {
                this.period = params.period;
            }

            me.setRestaurant(params.id);

            view = me.createListView();

            me.off('onCollectionSync').on('onCollectionSync', function (collection, response, options) {
                view.render();

                if (collection.isEmpty()) {
                    $.pnotify({
                        text : 'No orders found',
                        type : 'info',
                        opacity: .85,
                        sticker: false
                    });
                }
            });

            ['onCollectionChange', 'onCollectionReset', 'onCollectionSort'].forEach(function (eventName) {
                me.off(eventName)
                    .on(eventName, function () { view.render(); });
            });

            if (me.collection.isEmpty()) {
                me.getData();
            }
        },

        /**
         * Detail action used to present orders for requested income period
         * @param params
         */
        detailAction: function (params) {
            var me = this, view, income;

            if (!params || !params.restaurantId || !params.sn) {
                return;
            }

            if ('period' in params) {
                this.period = params.period;
            }

            me.setRestaurant(params.restaurantId);
            income = me.collection.get(params.sn);

            if (! (income instanceof IncomeModel)) {
                $.pnotify({
                    text   : 'Income record not found',
                    type   : 'info',
                    opacity: .85,
                    sticker: false
                });
                return;
            }

            view = this.createOrdersListView(income);

            me.orderCollection.fetchMultiple(income.get('orderIds'));
            me.orderCollection.sortBy('id', 'DESC').sort();
        }

    });

    return RestaurantIncomeController;

});
