define([

    // Lib dependencies
    'lib/Console',
    'lib/view/View',
    'lib/view/layout/VBox',

    './details/GeneralInfo',
    './details/Meals',
    './details/Payments'

], function (

    console,
    View,
    VBoxLayout,

    OrderDetailsGeneralInfoView,
    OrderDetailsMealsView,
    OrderDetailsPaymentsView

) {

    /** @type {NmotionApp} */
    var app = require('Nmotion').getInstance();

    /**
     * @class OrderDetailsView
     * @extends VBoxLayout
     */
    var OrderDetailsView = VBoxLayout.extend('OrderDetailsView', {

        el: '<div class="row-fluid"></div>',

        order: null,

        initialize: function () {
            this.callParent(arguments);

            this.addItem(new OrderDetailsGeneralInfoView);
            this.addItem(new OrderDetailsMealsView);
            this.addItem(new OrderDetailsPaymentsView);
        },

        setOrder: function setOrder(order) {
            this.order = order;
            this.getItems().forEach(function (nestedView) {
                nestedView.setOrder(order);
            })
        }

    });

    return OrderDetailsView;
});
