define([

    // library dependencies
    'lib/controller/Controller',

    // application dependencies
    'view/ConfirmModal', 'model/Restaurant'

], function (Controller, ConfirmModalView, RestaurantModel) {

    /**
     * @class RestaurantDeleteController
     * @extends Controller
     */
    var RestaurantDeleteController = Controller.extend('RestaurantDeleteController', {

        indexAction: function (params) {
            var restaurant, view;
            view = new ConfirmModalView();
            view.setTitle('Restaurant delete confirmation');
            view.on('onConfirm', function () {
                restaurant = RestaurantModel.findOrCreate({id: params.id});
                restaurant.destroy({async: false});
                view.hide();
            });
            view.on('onHide', function () {
                Backbone.history.navigate('restaurant/manage', true);
            });
            view.render();
        }

    });

    return RestaurantDeleteController;

});
