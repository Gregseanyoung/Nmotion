define([

	// Libs
	'lib/Console', 
        'lib/BaseClass', 'backbone'

], function (console,BaseClass, Backbone) {

    /**
     * @class AppRouter
     *
     * @extends Backbone.Router
     * @mixins BaseClass
     * @mixins Backbone.Events
     */
    var AppRouter = BaseClass.define('AppRouter', {

        extend: Backbone.Router,

        routes: {
            ""                                    : 'indexRoute',
            "logout"                              : 'logout',
            "config(/:action)"                    : 'configRoute',
            "config/:action/:id"                  : 'configRoute',
            "currency(/:action)"                  : 'currencyRoute',
            "currency/:action/:id"                : 'currencyRoute',
            "user(/:action)"                      : 'userRoute',
            "order(/:action)(/*json)"             : 'orderRoute',
            "realtime-orders(/:action)(/*json)"      : 'realtimeOrdersRoute',
            ":namespace/:controller(/:action)"    : 'defaultRoute',
            ":namespace/:controller/:action/:id"  : 'defaultRoute',
            ":namespace/:controller/:action/*json": 'defaultRoute',
            "*actions"                            : 'fallbackRoute'
        },

        initialize: function () {
            // :namespace/:controller/:id
            //this.route(/^([^\/]+)\/([^\/]+)\/(\d+)$/, 'indexWithIdRoute');

            // Start Backbone history a necessary step for bookmarkable URL's
            Backbone.history.start();
        },

        /**
         * @protected
         * @param {String} namespace
         * @param {String} controller
         * @returns {String}
         */
        getControllerPath: function (namespace, controller) {
            if (controller.indexOf('-') !== -1) {
                controller = controller.split('-').map(function (str) {
                    return str.toUpperCaseFirst();
                }).join('');
            }

            return 'controller/'
                + (namespace ? namespace + '/' : '')
                + controller.toUpperCaseFirst();
        },

        /**
         * @protected
         */
        parseUrlParams: function (rawParams) {
            return rawParams[0] === '{' ? JSON.parse(decodeURI(rawParams)) : {id: rawParams};
        },

        indexRoute: function () {
            this.defaultRoute('', 'index');
        },

        logout: function () {
            localStorage.clear();
            sessionStorage.clear();
            document.location.href = '/login';
        },

        configRoute: function (action, id) {
            this.defaultRoute('', 'config', action, id);
        },
                
        currencyRoute: function (action, id) {
            this.defaultRoute('', 'currency', action, id);
        },

        userRoute: function (action) {
            this.defaultRoute('', 'user', action, '');
        },

        orderRoute: function (action, rawParams) {
            this.defaultRoute('', 'order', action, rawParams);
        },

        realtimeOrdersRoute: function (action, rawParams) {
            this.defaultRoute('', 'realtimeOrders', action, rawParams);
        },
                
        indexWithIdRoute: function (namespace, controller, id) {
            this.defaultRoute(namespace, controller, 'index', id);
        },

        /**
         *
         * @param {String} namespace
         * @param {String} controller
         * @param {String=} action
         * @param {String=|Object=} rawParams
         */
        defaultRoute: function (namespace, controller, action, rawParams) {
            
            if(!(controller == "realtimeOrders" && action==null) && typeof $order_timer != "undefined"){
                clearInterval($order_timer);
                delete $order_timer;
            }
            
            if(controller == "realtimeOrders" && action==null){
                $can_create_timer = $is_order_listing = true;
            } else {
                $can_create_timer = $is_order_listing = false;
            }
            
            var me = this;
            require([me.getControllerPath(namespace, controller)], function (ControllerClass) {
                var params = {}, app;

                if (rawParams) {
                    params = me.parseUrlParams(rawParams);
                }

                try {
                    app = require('Nmotion').getInstance();
                    ControllerClass.callStatic('getInstance', [app]).invokeAction(action || null, params);
                    if(controller == "realtimeOrders"){
                        $("#container > .row-fluid > .span2").addClass("hidden");
                        $("#container > .row-fluid > .span10").removeClass("span10").addClass("span12");
                    } else {
                        $("#container > .row-fluid > .span2").removeClass("hidden");
                        $("#container > .row-fluid > .span12").removeClass("span12").addClass("span10");
                    }
                }
                catch (e) {
                    console.exception(e);
                }
            });
        },

        fallbackRoute: function (actions) {
            if (!actions) {
                return;
            }

            alert('Not implemented yet!');
        }

    });

    return AppRouter;

});
