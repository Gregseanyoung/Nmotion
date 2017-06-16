define([

    // Library dependencies
    'lib/Console', 'lib/Application',

    // Application dependencies
    'Router', 'model/User', 'view/dashboard/Dashboard'

], function (console, Application, AppRouter, UserModel, DashboardView) {

    /**
     * @class NmotionApp
     * @extends Application
     * @author Sergey Shupylo <sshu@ciklum.com>
     */
    var NmotionApp = Application.extend('NmotionApp', {

        name: 'Nmotion',

        /**
         * @private
         * @type {UserModel}
         */
        user: undefined,
	counter: 0,
        /**
         * @constructor
         */
        constructor: function () {
	    //this.callParent(arguments);
        },

        /**
         * @protected
         * @return {String|Null}
         */
        getAuthToken: function () {
            var authToken = localStorage.getItem('authToken');
            return authToken ? authToken : null;
        },

        /**
         * @protected
         */
        loadingSetup: function () {
            var loading = $('<div id="loading"><img src="/bundles/nmotionbackoffice/images/loading.gif"/></div>');
            loading.appendTo(document.body);
            loading
                .ajaxStart(function () { $(this).show(); })
                .ajaxStop(function () { $(this).hide(); });
        },

        /**
         * @protected
         */
        authSetup: function () {
            var authToken = this.getAuthToken();
            if (!authToken) {
                return;
            }

            $.ajaxSetup({
                headers: { Auth: authToken }
            });
        },

        /**
         * @protected
         * @returns {UserModel|null}
         */
        initializeUser: function () {
            /** @type UserModel */
            var user = UserModel.awakeFromSession();
            if (user) {
                return user;
            }

            if (!this.getAuthToken()) {
                return null;
            }

            user = new UserModel;

            try {
                user.fetchMe();
            } catch (e) {
                return null;
            }

            // cache user for use during browser session
            UserModel.sleepToSession(user);

            return user;
        },

        /**
         * @public
         * @returns {UserModel}
         */
        getUser: function () {
            if (typeof this.user === 'undefined') {
                this.user = this.initializeUser();
            }
            return this.user;
        },

        run: function run() {
            this.loadingSetup();
            this.authSetup();

            if (! this.getUser() || ! this.getUser().canUseBackoffice()) {
                localStorage.clear();
                sessionStorage.clear();
                document.location.href = '/login';
                return;
            }

            // Initiate the router
            this.router = new AppRouter;

            // Initiate the dashboard
            this.mainView = new DashboardView;
            this.mainView.render();
        }

    });

    return NmotionApp;

});
