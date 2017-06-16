define([

    // Libs
    'lib/Store',

    // Deps
    'model/Staff'

], function (Store, StaffModel) {

    /**
     * @class StaffCollection
     * @extends Store
     */
    var StaffCollection = Store.extend('StaffCollection', {

        /**
         * @type {StaffModel}
         */
        model: StaffModel,

        /**
         * @type {function(): string}
         */
        url: function () {
            if (! this.restaurant) {
                throw new Error('restaurant MUST be set');
            }
            return '/backoffice/restaurants/' + this.restaurant.getId() + '/staff';
        },

        /**
         * @type {Number}
         */
        pageSize: 100,

        initialize: function () {
            this.callParent(arguments);
        }

    });

    return StaffCollection;

});
