define([

    // library dependencies
    'lib/Console',
    'lib/controller/List',

    // application dependencies
    'collection/MenuCategory',
    'collection/Restaurant',
    'model/Restaurant',
    'model/MenuCategory',
    'form/MenuCategory',
    'view/restaurant/MenuCategoryList',
    'view/restaurant/MenuCategoryListForStaff',
    'view/ConfirmModal'

], function (

    console,
    ListController,

    MenuCategoryCollection,
    RestaurantCollection,
    RestaurantModel,
    MenuCategoryModel,
    MenuCategoryForm,
    MenuCategoryListView,
    MenuCategoryListViewForStaff,
    ConfirmModalView

) {

    /**
     * @class MenuCategoryController
     * @extends ListController
     */
    var MenuCategoryController = ListController.extend('MenuCategoryController', {

        /**
         * @type {NmotionApp}
         */
        app: undefined,

        /**
         * @type {MenuCategoryCollection}
         */
        collection: MenuCategoryCollection,

        /**
         * @type {RestaurantModel}
         */
        restaurant: null,

        /**
         * @protected
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
            this.app = application;
        },

        /**
         * @protected
         */
        reloadCollection: function () {
            var me = this, collection;

            collection = me.restaurant.getMenuCategories();
            collection.sortBy('position', 'asc').sort({silent: true});

            me.setCollection(collection);
        },

        /**
         * @return {MenuCategoryListView}
         */
        createListView: function () {
            var me = this, view, user = this.app.getUser();

            if (user.isSolutionAdmin() || user.isRestaurantAdmin()) {
                /** @type {MenuCategoryListView} */
                view = new MenuCategoryListView();
                view.on('onPositionChange', function (eventPayload) {
                    var model = me.collection.get(eventPayload.cid);
                    model.set('position', eventPayload.newPosition, {silent: true});
                    model.save(null, {silent: true, wait: true});
                });
            } else {
                /** @type {MenuCategoryListViewForStaff} */
                view = new MenuCategoryListViewForStaff();
            }
            view.setCollection(me.collection);

            me.parentView.setWidget(view);
            return view;
        },

        /**
         * @return {RestaurantModel}
         */
        getFirstAvailableRestaurant: function () {
            var restaurantCollection = new RestaurantCollection;
            restaurantCollection.fetch({async: false, limit: 1});

            return restaurantCollection.first();
        },

        setRestaurant: function (restaurantParam) {
            var me = this, restaurant;

            if (! (restaurantParam instanceof RestaurantModel)) {
                restaurant = RestaurantModel.findOrCreate(restaurantParam);
                if (restaurant === null) {
                    restaurant = new RestaurantModel({id: restaurantParam});
                    restaurant.fetch({async: false});
                }
            } else {
                restaurant = restaurantParam;
            }

            me.restaurant = restaurant;
            me.reloadCollection();
        },

        indexAction: function (params) {
            var me = this, view;

            if (params.restaurantId) {
                me.setRestaurant(params.restaurantId);
            } else {
                var restaurantId = me.getFirstAvailableRestaurant().getId();
                me.navigate('restaurant/menu-category/index', {restaurantId: restaurantId});
                return;
            }

            view = me.createListView();

            me.off('onCollectionSync').on('onCollectionSync', function () {
                console.debug('onCollectionSync event is being fired', arguments);
                view.render();
            });

            me.off('onCollectionChange').on('onCollectionChange', function () {
                console.debug('onCollectionChange event is being fired', arguments);
                view.render();
            });

            me.off('onCollectionReset').on('onCollectionReset', function () {
                console.debug('onCollectionReset event is being fired', arguments);
                view.render();
            });

            if (me.collection.isEmpty()) {
                me.getData();
            }
        },

        newAction: function (params) {
            var me = this, form, menuCategory;

            if (params.restaurantId) {
                me.setRestaurant(params.restaurantId);
            }

            if (me.restaurant === null) {
                throw new EvalError('valid existing restaurant is required');
            }

            /** @type {MenuCategoryModel} */
            menuCategory = new MenuCategoryModel();
            menuCategory.set('restaurant', me.restaurant);

            /** @type {Form} */
            form = new MenuCategoryForm({model: menuCategory});
            form.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    menuCategory.save(null, {
                        success: function (model, response, options) {
                            var params = {restaurantId: me.restaurant.getId()};
                            me.navigate('restaurant/menu-category/index', params, {trigger: true});
                        },
                        error: function(model, response) {
                            form.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });
            form.on('onCancel', function () {
                menuCategory.destroy();
            });

            me.parentView.setWidget(form);
        },

        editAction: function (params) {
            var me = this, formView, menuCategory;

            menuCategory = MenuCategoryModel.findOrCreate(params.id);
            if (menuCategory === null) {
                menuCategory = new MenuCategoryModel({id: params.id});
                menuCategory.fetch({async: false});
            }

            if (menuCategory.has('restaurant')
                && menuCategory.get('restaurant') instanceof RestaurantModel
            ) {
                me.setRestaurant(menuCategory.get('restaurant'));
            }

            if (me.restaurant === null) {
                throw new EvalError('valid existing restaurant is required');
            }

            /** @type {Form} */
            formView = new MenuCategoryForm({model: menuCategory});
            formView.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    menuCategory.save(null, {
                        success: function (model, response, options) {
                            var url = 'restaurant/menu-category/index/{"restaurantId":{id}}';
                            url = url.replace(/{id}/g, me.restaurant.getId());
                            Backbone.history.navigate(url, true);
                        },
                        error: function(model, response) {
                            form.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });

            me.parentView.setWidget(formView);
            formView.$el.width(600);
        },

        deleteAction: function (params) {
            var me = this, modal;

            modal = new ConfirmModalView();
            modal.setTitle('Menu category delete confirmation');
            modal.on('onConfirm', function () {
                var menuCategory = MenuCategoryModel.findOrCreate({id: params.id});
                menuCategory.destroy({async: false, wait: true});
                modal.hide();
            });
            modal.on('onHide', function () {
                me.navigate('restaurant/menu-category/index', {restaurantId: me.restaurant.getId()}, {trigger: true});
            });
            modal.render();
        },

        batchHideAction: function (params) {
            var me = this, ids = params.ids || [], url;
            if (me.collection.length) {
                var restaurantId = me.collection.first().get('restaurant').getId();
                url = 'restaurant/menu-category/index/{%22restaurantId%22:' + restaurantId + '}';
            }
            ids.forEach(function (id) {
                var menuCategory = MenuCategoryModel.findOrCreate(id);
                menuCategory.save({visible: false}, {async: false, wait: true});
            });
            if (url) {
                Backbone.history.navigate(url, {trigger: true});
            }
        },

        batchDeleteAction: function (params) {
            var me = this, ids = params.ids || [], modal;

            modal = new ConfirmModalView();
            modal.setTitle('Menu categories delete confirmation');
            modal.on('onConfirm', function () {
                ids.forEach(function (id) {
                    var menuCategory = MenuCategoryModel.findOrCreate(id);
                    menuCategory.destroy({async: false, wait: true});
                });
                modal.hide();
            });
            modal.on('onHide', function () {
                me.navigate('restaurant/menu-category/index', {restaurantId: me.restaurant.getId()}, {trigger: true});
            });
            modal.render();
        }

    });

    return MenuCategoryController;

});
