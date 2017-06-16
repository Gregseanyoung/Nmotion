define([

    // Libs
    'lib/Model'

], function (Model) {

    /**
     * @class BaseUserModel
     * @extends Model
     */
    var BaseUserModel = Model.extend('BaseUserModel', {

        inheritableStatics: {

            /**
             * Dehydrates user`s object and stores it to the browser session storage
             *
             * @static
             * @param {BaseUserModel} user
             */
            sleepToSession: function sleepToSession(user) {
                sessionStorage.setItem(this.className, JSON.stringify(user));
            },

            /**
             * Upon exist in browser session will return hydrated user object
             *
             * @static
             * @return {BaseUserModel}
             */
            awakeFromSession: function awakeFromSession() {
                var dehydratedUser = sessionStorage.getItem(this.className);
                return dehydratedUser ? new this.self(JSON.parse(dehydratedUser)) : null;
            }

        }

    });

    /**
     * @override
     * @return {Model}
     */
    BaseUserModel.extend = function extend() {
        return Model.extend.apply(this, arguments);
    };

    return BaseUserModel;

});
