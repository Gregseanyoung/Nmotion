define([

    // Lib dependencies
//    'lib/Console',
    'lib/view/View',

    'text!./meals.html'

], function (

  //  console,
    View,

    MealsTemplate

) {

    /**
     * @class OrderDetailsMealsView
     * @extends View
     */
    var OrderDetailsMealsView = View.extend('OrderDetailsMealsView', {

        el: '<div class="row-fluid"></div>',

        template: null,

        order: null,

        initialize: function () {
            this.callParent(arguments);
            this.template = _.template(MealsTemplate);
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
                meals: me.order.toJSON().orderMeals,
                currency: this.order.get("restaurant").currency.sign
            });

            me.$el.html(tpl);
        }

    });

    return OrderDetailsMealsView;

});
