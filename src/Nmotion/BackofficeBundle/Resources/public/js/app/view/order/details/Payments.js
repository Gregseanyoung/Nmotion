define([

    // Lib dependencies
    'lib/Console',
    'lib/view/View',

    'text!./payments.html'

], function (

    console,
    View,

    PaymentsTemplate

) {

    /**
     * @class OrderDetailsPaymentsView
     * @extends View
     */
    var OrderDetailsPaymentsView = View.extend('OrderDetailsPaymentsView', {

        el: '<div class="row-fluid"></div>',

        template: null,

        order: null,

        initialize: function () {
            this.callParent(arguments);
            this.template = _.template(PaymentsTemplate);
        },

        setOrder: function setOrder(order) {
            this.order = order;
        },

        /**
         * @protected
         */
        doRender: function () {
            var me = this, tpl;

            tpl = me.template({
                payments: me.order.toJSON().payments,
                currency: this.order.get("restaurant").currency.sign
            });

            me.$el.html(tpl);
        }

    });

    return OrderDetailsPaymentsView;

});
