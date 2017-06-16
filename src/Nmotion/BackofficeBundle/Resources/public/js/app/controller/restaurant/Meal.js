define([

    // library dependencies
    'lib/Console',
    'lib/controller/List',

    // application dependencies
    'collection/MenuCategory',
    'collection/Meal',
    'model/Restaurant',
    'model/MenuCategory',
    'model/Meal',
    'form/Meal',
    'view/restaurant/MealList',
    'view/restaurant/MealListForStaff',
    'view/ConfirmModal'

], function (

    console,
    ListController,

    MenuCategoryCollection,
    MealCollection,
    RestaurantModel,
    MenuCategoryModel,
    MealModel,
    MealForm,
    MealListView,
    MealListViewForStaff,
    ConfirmModalView

) {

    /**
     * @class MealController
     * @extends ListController
     */
    var MealController = ListController.extend('MealController', {

        /**
         * @type {NmotionApp}
         */
        app: undefined,

        /**
         * @type {MealCollection}
         */
        collection: MealCollection,

        /**
         * @type {MenuCategoryModel}
         */
        menuCategory: null,

        /**
         * @protected
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
            this.app = application;
        },

        reloadCollection: function () {
            var me = this, collection;

            collection = me.menuCategory.getMeals();
            collection.sortBy('position', 'asc').sort({silent: true});

            me.setCollection(collection);
        },

        setMenuCategory: function (menuCategoryParam) {
            var me = this, menuCategory;

            if (! (menuCategoryParam instanceof MenuCategoryModel)) {
                menuCategory = MenuCategoryModel.findOrCreate(menuCategoryParam);
                if (menuCategory === null) {
                    menuCategory = new MenuCategoryModel({id: menuCategoryParam});
                    menuCategory.fetch({async: false});
                }
            } else {
                menuCategory = menuCategoryParam;
            }

            me.menuCategory = menuCategory;
            me.reloadCollection();
        },

        onHideBtnClick: function onHideBtnClick(view, mealId) {
            var meal = MealModel.findOrCreate({id: mealId});
            meal.save({visible: false}, {wait: true});
        },

        onShowBtnClick: function onShowBtnClick(view, mealId) {
            var meal = MealModel.findOrCreate({id: mealId});
            meal.save({visible: true}, {wait: true});
        },

        /**
         * @return {MealListView}
         */
        createListView: function () {
            var me = this, view, user = this.app.getUser();

            if (user.isSolutionAdmin() || user.isRestaurantAdmin()) {
                /** @type {MealListView} */
                view = new MealListView();
                view.on('onPositionChange', function (eventPayload) {
                    var model = me.collection.get(eventPayload.cid);
                    model.set('position', eventPayload.newPosition, {silent: true});
                    model.save(null, {
                        success: function () {},
                        wait: true,
                        silent: true
                    });
                });
            } else {
                /** @type {MealListViewForStaff} */
                view = new MealListViewForStaff();
                view.on('hideBtnClick', me.onHideBtnClick);
                view.on('showBtnClick', me.onShowBtnClick);
            }
            view.setCollection(me.collection);

            me.parentView.setWidget(view);
            return view;
        },

        indexAction: function (params) {
            var me = this, view;

            if (params.menuCategoryId) {
                me.setMenuCategory(params.menuCategoryId);
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

            me.off('onCollectionSort').on('onCollectionSort', function () {
                console.debug('onCollectionReset event is being fired', arguments);
                view.render();
            });

            if (me.collection.isEmpty()) {
                me.getData();
            }
        },

        newAction: function (params) {
            var me = this, formView, meal;

            if (params.menuCategoryId) {
                me.setMenuCategory(params.menuCategoryId);
            }

            /** @type {MealModel} */
            meal = new MealModel();
            meal.set('menuCategory', me.menuCategory);

            /** @type {Form} */
            formView = new MealForm({model: meal});
            formView.on('submitReady', function (form) {
                if (meal.get('timeFrom') == null && meal.get('timeTo') == null) {
                    meal.unset('timeFrom').unset('timeTo');
                }
                meal.save(null, {
                    success: function (model, resp, options) {
                        var params = {menuCategoryId: me.menuCategory.getId()};
                        me.navigate('restaurant/meal/index', params, {trigger: true});
                    },
                    error: function(model, response) {
                        formView.handleServerValidationErrors(model, response);
                    },
                    wait: true
                });
            });
            formView.on('onCancel', function () {
                meal.destroy();
            });

            me.parentView.setWidget(formView);
        },

        editAction: function (params) {
            var me = this, formView, meal;

            meal = MealModel.findOrCreate(params.id);
            if (meal === null) {
                meal = new MealModel({id: params.id});
                meal.fetch({async: false});
            }
            meal.ensureRelationsLoaded();

            /** @type {MealForm} */
            formView = new MealForm({model: meal});
            formView.on('submitReady', function (form) {
                meal.save(null, {
                    success: function (model, resp, options) {
                        var url = 'restaurant/meal/index/{%22menuCategoryId%22:{id}}';
                        url = url.replace(/{id}/g, meal.get('menuCategory').getId());
                        Backbone.history.navigate(url, true);
                    },
                    error: function(model, response) {
                        formView.handleServerValidationErrors(model, response);
                    },
                    wait: true
                });
            });

            me.parentView.setWidget(formView);
        },

        deleteAction: function (params) {
            var modal = new ConfirmModalView(),
                meal = MealModel.findOrCreate({id: params.id}),
                menuCategoryId = meal.get('menuCategory').getId();

            modal.setTitle('Meal delete confirmation');
            modal.on('onConfirm', function () {
                meal.destroy({async: false, wait: true});
                modal.hide();
            });
            modal.on('onHide', function () {
                var url = 'restaurant/meal/index/{%22menuCategoryId%22:' + menuCategoryId + '}';
                Backbone.history.navigate(url, {trigger: false});
            });
            modal.render();
        },

        batchHideAction: function (params) {
            var me = this, ids = params.ids || [], url;
            if (me.collection.length) {
                var menuCategoryId = me.collection.first().get('menuCategory').getId();
                url = 'restaurant/meal/index/{%22menuCategoryId%22:' + menuCategoryId + '}';
            }
            ids.forEach(function (id) {
                var meal = MealModel.findOrCreate({id: id});
                meal.save({visible: false}, {async: false, wait: true});
            });
            if (url) {
                Backbone.history.navigate(url, {trigger: true});
            }
        },

        batchDeleteAction: function (params) {
            var me = this, ids = params.ids || [], modal, url;
            if (me.collection.length) {
                var menuCategoryId = me.collection.first().get('menuCategory').getId();
                url = 'restaurant/meal/index/{%22menuCategoryId%22:' + menuCategoryId + '}';
            }
            modal = new ConfirmModalView();
            modal.setTitle('Meals delete confirmation');
            modal.on('onConfirm', function () {
                ids.forEach(function (id) {
                    var meal = MealModel.findOrCreate({id: id});
                    meal.destroy({async: false, wait: true});
                });
                modal.hide();
            });
            modal.on('onHide', function () {
                if (url) {
                    Backbone.history.navigate(url, {trigger: true});
                }
            });
            modal.render();
        }

    });

    return MealController;

});
