define([

    // Lib dependencies
//    'lib/Console',
    'lib/view/View',

    'text!./meal_extraIngredient.html'

], function (

  //  console,
    View,

    MealExtraIngredientTemplate

) {

    /**
     * @class OrderDetailsMealExtraIngredientView
     * @extends View
     */
    var OrderDetailsMealExtraIngredientView = View.extend('OrderDetailsMealExtraIngredientView', {

        el: '<div class="row-fluid"></div>',

        template: null,

        order: null,

        initialize: function () {
            this.callParent(arguments);
            this.template = _.template(MealExtraIngredientTemplate);
        },

        setOrder: function setOrder(order) {
            this.order = order;
        },

        /**
         * @protected
         */
        doRender: function () {
           
           // var me = this, tpl;
            var me = this, tpl;

            tpl = me.template({
                orderId:me.order.id,
                meals: me.order.toJSON().orderMeals,
                currency: this.order.get("restaurant").currency.sign
            });

            me.$el.html(tpl);
        }

    });

    return OrderDetailsMealExtraIngredientView;

});
