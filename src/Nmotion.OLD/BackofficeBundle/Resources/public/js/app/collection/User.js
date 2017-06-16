define([

    // Libs
    'lib/Store',

    // Deps
    'model/User'

], function (Store, UserModel) {

    /**
     * @class UserCollection
     * @extends Store
     */
    var UserCollection = Store.extend('UserCollection', {

        /**
         * @type {UserModel}
         */
        model: UserModel,

        /**
         * @type {String}
         */
        url: '/backoffice/users',

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return UserCollection;

});
