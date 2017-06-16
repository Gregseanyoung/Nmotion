define([

    // library dependencies
    'lib/Console', 'lib/controller/Controller', 'lib/view/Form', 'lib/view/Model',

    // application dependencies
    'form/Restaurant', 'model/Restaurant'

], function (console, Controller, Form, ModelView, RestaurantForm, RestaurantModel) {

    /**
     * @class RestaurantProfileController
     * @extends Controller
     */
    var RestaurantProfileController = Controller.extend('RestaurantProfileController', {

        /**
         * @type {NmotionApp}
         */
        app: undefined,

        /**
         * @protected
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
            this.app = application;
        },

        indexAction: function (params) {
            var me = this, restaurant, view = new ModelView();

            restaurant = RestaurantModel.findOrCreate(params.id);

            if (restaurant !== null) {
                view.setModel(restaurant);
                me.parentView.setWidget(view);
            } else {
                restaurant = RestaurantModel.findOrCreate({id: params.id});
                restaurant.fetch({
                    success: function () {
                        view.setModel(restaurant);
                        me.parentView.setWidget(view);
                    }
                });
            }
        },

        editAction: function (params) {
            var me = this, formView, restaurant,
                user = this.app.getUser(),
                backUrl = 'restaurant/' + (user.isSolutionAdmin() ? 'manage' : 'menu-category');

            restaurant = RestaurantModel.findOrCreate(params.id);
            if (restaurant === null) {
                restaurant = new RestaurantModel({id: params.id});
                restaurant.fetch({async: false});
            }

            /** @type {Form} */
            formView = new RestaurantForm({model: restaurant});
            formView.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    restaurant.save(null, {
                        success: function (model, resp, options) {
                            Backbone.history.navigate(backUrl, {trigger: true});
                        },
                        error: function(model, response) {
                            formView.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });

            me.parentView.setWidget(formView);
            formView.$el.width(600);
        }

    });

    return RestaurantProfileController;

});
