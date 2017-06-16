define([

    // Lib dependencies
    'lib/Console',
    'lib/view/View',

    'text!./general_info.html'

], function (

    console,
    View,

    GeneralInfoTemplate

) {

    /**
     * @class OrderDetailsGeneralInfoView
     * @extends View
     */
    var OrderDetailsGeneralInfoView = View.extend('OrderDetailsGeneralInfoView', {

        el: '<div class="row-fluid"></div>',

        template: null,

        order: null,

        initialize: function () {
            this.callParent(arguments);
            this.template = _.template(GeneralInfoTemplate);
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
                order: me.order.toJSON()
            });

            me.$el.html(tpl);
        }

    });

    return OrderDetailsGeneralInfoView;

});
