define([

    // library dependencies
    'lib/Console',
    'lib/controller/List',

    // application dependencies
    'collection/Order',
    'model/Order',
    'view/order/List',
    'view/order/Details'

], function (

    console,
    ListController,

    OrderCollection,
    OrderModel,
    OrderListView,
    OrderDetailsView

) {

    /**
     * @class OrderController
     * @extends ListController
     */
    var OrderController = ListController.extend('OrderController', {

        /**
         * @type {NmotionApp}
         */
        app: undefined,

        /**
         * @type {OrderCollection}
         */
        collection: OrderCollection,

        /**
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
            this.app = application;
            this.collection.setRemoteSort(true).sortBy('id', 'DESC');
        },

        /**
         * @method
         * @protected
         * @return {OrderListView}
         */
        createListView: function () {
            var me = this, view;

            /** @type {OrderListView} */
            view = new OrderListView();
            view.setCollection(me.collection);
            view.on('export', function (dateFrom, dateTo) {
                me.onExport(this, dateFrom, dateTo);
            });
            view.on('filterByPeriodChange', function (dateFrom, dateTo) {
                me.onFilterByPeriodChange(this, dateFrom, dateTo);
            });
            view.on('pageChange', me.onPageChange);
            view.on('searchChange', me.onSearchChange);
            view.on('searchReset', me.onSearchReset);

            me.parentView.setWidget(view);

            return view;
        },

        /**
         * Event-handler for export
         * @method
         * @protected
         * @param {OrderListView} view
         * @param {Date} dateFrom
         * @param {Date} dateTo
         */
        onExport: function onExport(view, dateFrom, dateTo) {
            var promise = $.ajax({
                url    : '/nmtn/oblivion/',
                headers: { Auth: this.app.getAuthToken() }
            });
            promise.success(function () {
                window.location = '/nmtn/ordersexcel/' + dateFrom.format('isoDate') + ';' + dateTo.format('isoDate');
            });
            promise.error(function (jqXHR, textStatus, errorThrown) {
                $.pnotify({
                    title: 'Error',
                    text : 'Something went wrong, please try to relogin or contact support.',
                    type : 'error',
                    hide : false,
                    icon : false
                });
            });
        },

        /**
         * Event-handler for filterByPeriodChange
         * @method
         * @protected
         * @param {OrderListView} view
         * @param {Date} dateFrom
         * @param {Date} dateTo
         */
        onFilterByPeriodChange: function onFilterByPeriodChange(view, dateFrom, dateTo) {
            this.collection
                .filterBy('dateFrom', dateFrom.format('isoDate'))
                .filterBy('dateTo', dateTo.format('isoDate'))
                .fetchPage(1);
        },

        /**
         * @method
         * @protected
         * @param {Number} page
         */
        onPageChange: function onPageChange(page) {
            this.collection.fetchPage(page);
        },

        /**
         * Event-handler for filterByPeriodChange
         * @method
         * @protected
         * @param orderId
         */
        onSearchChange: function (orderId) {
            var me = this, order;

            if (!_.isArray(orderId)) {
                orderId = [orderId];
            }

            if (orderId.length === 1) {
                // if order has been loaded
                order = OrderModel.findOrCreate(_.first(orderId));
                if (order !== null) {
                    me.collection.reset(order);
                    return;
                }

                // if order is to be loaded
                order = new OrderModel({id: orderId});
                order.fetch(
                    {
                        async: false,
                        statusCode: {
                            200: function () {
                                me.collection.reset(order);
                            },
                            404: function () {
                                $.pnotify({
                                    text   : 'Order not found',
                                    type   : 'info',
                                    opacity: .85,
                                    sticker: false
                                });
                                order.unset('id').destroy();
                            }
                        }
                    }
                );
            } else {
                me.collection.fetchMultiple(orderId);
            }
        },

        /**
         * @protected
         */
        onSearchReset: function () {
            this.collection
                .resetFilters()
                .fetchPage(1);
        },

        /**
         * @public
         * @param {Object} params
         */
        indexAction: function (params) {
            var me = this, view;

            view = me.createListView();

            me.off('onCollectionSync').on('onCollectionSync', function () {
                view.render();
            });

            me.off('onCollectionChange').on('onCollectionChange', function () {
                view.render();
            });

            me.off('onCollectionReset').on('onCollectionReset', function () {
                view.render();
            });

            if (params && params.orderId) {
                me.onSearch(params.orderId);
            } else {
                if (me.collection.isEmpty()) {
                    me.getData();
                }
            }
        },

        /**
         * @method
         * @param {Object} params
         */
        detailsAction: function (params) {
            var me = this, formView, order;

            order = OrderModel.findOrCreate(params.id);
            if (order === null) {
                order = new OrderModel({id: params.id});
            }

            order.fetch({
                async: false,
                // spike-fix
                url: order.urlRoot + '/' + order.getId()
            });

            /** @type {OrderDetailsView} */
            var view = new OrderDetailsView;
            view.setOrder(order);

            me.parentView.setWidget(view);
        }

    });

    return OrderController;

});
