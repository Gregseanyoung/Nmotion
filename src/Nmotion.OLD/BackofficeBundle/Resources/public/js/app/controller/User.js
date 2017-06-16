define([

    // library dependencies
    'lib/Console',
    'lib/controller/List',

    // application dependencies
    'collection/User',
    'model/User',
    'view/UserList'

], function (

    console,
    ListController,

    UserCollection,
    UserModel,
    UserListView

) {

    /**
     * @class UserController
     * @extends ListController
     */
    var UserController = ListController.extend('UserController', {

        /**
         * @type {UserCollection}
         */
        collection: UserCollection,

        /**
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
            this.collection.setRemoteSort(true);
        },

        /**
         * @return {UserListView}
         */
        createListView: function () {
            var me = this, view;

            /** @type {UserListView} */
            view = new UserListView();
            view.setCollection(me.collection);
            view.on('pageChange', me.onPageChange);
            view.on('searchChange', me.onSearch);
            view.on('searchReset', me.onSearchReset);
            view.on('filterByUserTypeChange', me.onFilterByUserTypeChange);

            me.parentView.setWidget(view);

            return view;
        },

        /**
         * @protected
         * @param {Number} page
         */
        onPageChange: function onPageChange(page) {
            this.collection.fetchPage(page);
        },

        onFilterByUserTypeChange: function onFilterByUserTypeChange(type) {
            this.collection
                .resetFilters()
                .filterBy('userType', type)
                .fetchPage(1);
        },

        /**
         * @protected
         * @param {String} searchQuery
         */
        onSearch: function (searchQuery) {
            this.collection
                .resetFilters()
                .filterBy('search', searchQuery.trim())
                .fetchPage(1);
        },

        /**
         * @protected
         */
        onSearchReset: function () {
            this.collection
                .resetFilters()
                .fetchPage(1);
        },

        indexAction: function (params) {
            var me = this, view;

            view = me.createListView();

            me.off('onCollectionSync').on('onCollectionSync', function (collection, response, options) {
                if (!(collection instanceof UserCollection)) {
                    return;
                }

                view.render();

                if (collection.isEmpty()) {
                    $.pnotify({
                        text : 'No users found',
                        type : 'info',
                        opacity: .85,
                        sticker: false
                    });
                }
            });

            me.off('onCollectionChange').on('onCollectionChange', function () {
                view.render();
            });

            me.off('onCollectionReset').on('onCollectionReset', function () {
                view.render();
            });

            if (me.collection.isEmpty()) {
                me.getData();
            }
        }

    });

    return UserController;

});
