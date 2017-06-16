define([

    // Libs
    './User'

], function (UserModel) {

    /**
     * @class StaffModel
     * @extends UserModel
     */
    var StaffModel = UserModel.extend('StaffModel', {

        /**
         * @protected
         * @type {function(): string}
         */
        urlRoot: function () {
            if (this.isNew()) {
                if (! this.has('restaurant')) {
                    throw new Error('restaurant must be set for new restaurant staff user');
                }
                return '/backoffice/restaurants/' + this.get('restaurant').getId() + '/staff';
            } else {
                return '/backoffice/staff';
            }
        }

    });

    return StaffModel;

});
