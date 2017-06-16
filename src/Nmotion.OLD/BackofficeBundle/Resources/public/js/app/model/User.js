define([

    // Libs
    'lib/model/User'

], function (BaseUserModel) {

    /**
     * @class UserModel
     * @extends BaseUserModel
     */
    var UserModel = BaseUserModel.extend('UserModel', {

        idAttribute: 'id',

        default: {
            id: null
        },

        fetchMe: function () {
            return this.fetch({
                url       : '/api/v2/users/me.json',
                async     : false,
                statusCode: {
                    401: function () {
                        throw Error('Unauthorized');
                    },
                    403: function () {
                        throw Error('Forbidden');
                    }
                },
                error     : function (obj, response, options) {
                    if (response.status != 401 && response.status != 403) {
                        console.log(response);
                    }
                }
            });
        },

        /**
         * @return {Array}
         */
        getRoles: function getRoles() {
            return this.get('roles');
        },

        /**
         * Check if user has specified role
         *
         * @public
         * @param {String} role to check against user's roles
         */
        hasRole: function hasRole(role) {
            return this.getRoles().indexOf(role) !== -1;
        },

        /**
         * Shortcut method to check if user has role of a solution admin
         *
         * @public
         * @return {Boolean}
         */
        isSolutionAdmin: function isSolutionAdmin() {
            return this.hasRole('ROLE_SOLUTION_ADMIN');
        },

        /**
         * Shortcut method to check if user has role of a restaurant admin
         *
         * @public
         * @return {Boolean}
         */
        isRestaurantAdmin: function isRestaurantAdmin() {
            return this.hasRole('ROLE_RESTAURANT_ADMIN') && ! this.isSolutionAdmin();
        },

        /**
         * Shortcut method to check if user has role of a restaurant staff
         *
         * @public
         * @return {Boolean}
         */
        isRestaurantStaff: function isRestaurantStaff() {
            return this.hasRole('ROLE_RESTAURANT_STAFF');
        },

        /**
         * @public
         * @return {Boolean}
         */
        canUseBackoffice: function canUseBackoffice() {
            return this.isSolutionAdmin()
                || this.isRestaurantAdmin()
                || this.isRestaurantStaff();
        }

    });

    return UserModel;

});
