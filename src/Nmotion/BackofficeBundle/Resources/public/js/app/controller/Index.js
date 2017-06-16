define([

    // library dependencies
    'lib/Console',
    'lib/controller/Controller'

], function (

    console,
    Controller

) {

    /**
     * @class IndexController
     * @extends Controller
     */
    var IndexController = Controller.extend('IndexController', {

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
            var user = this.app.getUser();
            if (user.isSolutionAdmin()) {
                this.navigate('/restaurant/manage');
            } else {
                this.navigate('/restaurant/menu-category');
            }
        }

    });

    return IndexController;

});
