define([

    // library dependencies
    'lib/controller/Controller',

    // application dependencies
    'form/Restaurant', 'model/Restaurant'

], function (Controller, RestaurantForm, RestaurantModel) {

    /**
     * @class RegisterController
     * @extends Controller
     */
    var RegisterController = Controller.extend('RestaurantController', {

        indexAction: function () {
            var me = this, formView;

            /** @type {RestaurantModel} */
            var restaurant = new RestaurantModel;

            /** @type {Form} */
            formView = new RestaurantForm({model: restaurant});
            formView.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    restaurant.save(null, {
                        success: function (model, resp, options) {
                            me.navigate('restaurant/manage', null, {trigger: true});
                        },
                        error: function(model, response) {
                            formView.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });
            formView.on('onCancel', function () {
                restaurant.destroy();
            });

            me.parentView.setWidget(formView);
        }

    });

    return RegisterController;

});
