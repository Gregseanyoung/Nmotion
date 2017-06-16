define([

    // library dependencies
//    'lib/Console',
    'lib/controller/List',

    // application dependencies
    'model/Restaurant',
    'model/Staff',
    'collection/Staff',
    'collection/Restaurant',
    'form/Staff',
    'view/restaurant/StaffList',
    'view/ConfirmModal'

], function (

    //console,
    ListController,

    RestaurantModel,
    StaffModel,
    StaffCollection,
    RestaurantCollection,
    StaffForm,
    RestaurantStaffListView,
    ConfirmModalView

) {

    /**
     * @class StaffController
     * @extends ListController
     */
    var StaffController = ListController.extend('StaffController', {

        /**
         * @type {StaffCollection}
         */
        collection: StaffCollection,

        /**
         * @protected
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
        },

        /**
         * @return {RestaurantStaffListView}
         */
        createListView: function () {
            var me = this, view;

            /** @type {RestaurantStaffListView} */
            view = new RestaurantStaffListView();
            view.setCollection(me.collection);
            view.on('batchDeleteClick', me.batchDelete);

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

        setRestaurant: function (restaurantOrId) {
            var me = this, restaurant;

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

            me.setCollection(restaurant.get('staff'));
        },

        /**
         * @protected
         * @param {RestaurantStaffListView} view
         * @param {Array} idsArray
         */
        batchDelete: function (view, idsArray) {
            var me = this, modal;

            modal = new ConfirmModalView();
            modal.setTitle('Staff delete confirmation');
            modal.on('confirm', function () {
                idsArray.forEach(function (id) {
                    var staff = StaffModel.findOrCreate({id: id});
                    staff.destroy({async: false, wait: true});
                });
                modal.hide();
            });
            modal.on('hide', function () {
                me.trigger('onCollectionChange');
            });
            modal.render();
        },

        indexAction: function (params) {

        },

        listAction: function (params) {
            var me = this, view;

            if (params.id) {
                me.setRestaurant(params.id);
            } else {
                me.navigate('restaurant/staff/list', me.getFirstAvailableRestaurant().getId(), {trigger: true});
                return;
            }

            view = me.createListView();

            me.off('onCollectionSync').on('onCollectionSync', function () {
                //console.debug('onCollectionSync event is being fired', arguments);
                view.render();
            });

            me.off('onCollectionChange').on('onCollectionChange', function () {
                //console.debug('onCollectionChange event is being fired', arguments);
                view.render();
            });

            me.off('onCollectionReset').on('onCollectionReset', function () {
                //console.debug('onCollectionReset event is being fired', arguments);
                view.render();
            });

            if (me.collection.isEmpty()) {
                me.getData();
            }
        },

        newAction: function (params) {
            var me = this, form, staff;

            if (params.id) {
                me.setRestaurant(params.id);
            }

            if (me.restaurant === null) {
                throw new EvalError('valid existing restaurant is required');
            }

            /** @type {StaffModel} */
            staff = new StaffModel();
            staff.set('restaurant', me.restaurant);

            /** @type {Form} */
            form = new StaffForm({model: staff});
            form.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    staff.save(null, {
                        success: function (model, response, options) {
                            me.navigate('restaurant/staff/list', me.restaurant.getId(), {trigger: true});
                        },
                        error: function(model, response) {
                            form.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });
            form.on('onCancel', function () {
                staff.destroy();
            });

            me.parentView.setWidget(form);
        },

        editAction: function (params) {
            var me = this, form, staff;

            staff = StaffModel.findOrCreate(params.id);
            if (staff === null) {
                staff = new StaffModel({id: params.id});
                staff.fetch({async: false});
            }

            /** @type {Form} */
            form = new StaffForm({model: staff});
            form.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    staff.save(null, {
                        success: function (model, response, options) {
                            me.navigate('restaurant/staff/list', me.restaurant.getId(), {trigger: true});
                        },
                        error: function(model, response) {
                            form.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });

            me.parentView.setWidget(form);
        },

        deleteAction: function (params) {
            var me = this, modal;

            modal = new ConfirmModalView();
            modal.setTitle('Restaurant staff delete confirmation');
            modal.on('onConfirm', function () {
                var menuCategory = StaffModel.findOrCreate({id: params.id});
                menuCategory.destroy({async: false, wait: true});
                modal.hide();
            });
            modal.on('onHide', function () {
                me.navigate('restaurant/staff/list', me.restaurant.getId(), {trigger: true});
            });
            modal.render();
        }

    });

    return StaffController;

});