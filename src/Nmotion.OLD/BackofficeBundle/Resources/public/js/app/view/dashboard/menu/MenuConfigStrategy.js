define([

    // Libs
    'lib/BaseClass'

], function (BaseClass) {

    /**
     * @abstract
     * @class MenuConfigStrategy
     */
    var MenuConfigStrategy = BaseClass.define('MenuConfigStrategy', /** @lends MenuConfigStrategy.prototype */ {

        constructor: function () {
        },

        /**
         * @abstract
         * @public
         * @returns {Array}
         */
        getMenuConfig: function () {
            throw new Error('must be implemented by subclass!');
        }

    });

    return MenuConfigStrategy;

});
