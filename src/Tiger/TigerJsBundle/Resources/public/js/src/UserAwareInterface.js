define([

], function () {

    /**
     * UserAwareInterface should be implemented by classes that depends on a User.
     *
     * @interface
     * @author Sergey Shupylo <sshu@ciklum.com>
     */
    function UserAwareInterface() {
    }

    /**
     * Sets the User
     *
     * @param {BaseUserModel} user
     */
    UserAwareInterface.prototype.setUser = function setUser(user) {
    };

    /**
     * Gets the User
     *
     * @return {BaseUserModel}
     */
    UserAwareInterface.prototype.getUser = function getUser() {
    };

    return UserAwareInterface;

});
