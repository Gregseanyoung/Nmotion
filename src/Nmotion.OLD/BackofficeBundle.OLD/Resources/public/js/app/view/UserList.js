define([

    // Libs
    'lib/view/Grid', 'jquery.pagination'

], function (GridView) {

    /**
     * @class UserListView
     * @extends GridView
     */
    var UserListView = GridView.extend('UserListView', {

        /**
         * @event filterByUserTypeChange
         * Triggered when user has changed user role type.
         * @param {String} type user role type to be used as filter criterion
         */
        /**
         * @event pageChange
         * Triggered when user has requested another page.
         * @param {String} page page to be loaded
         */
        /**
         * @event searchChange
         * Triggered when user has changed search query in the search input.
         * @param {String} searchQuery new search query
         */
        /**
         * @event searchReset
         * Triggered when user clicked on reset button.
         */

        events: {
            'keydown #searchInput': function (event) {
                if (event.keyCode !== 13) {
                    return;
                }
                event.preventDefault();

                if (!event.currentTarget.value) {
                    return;
                }

                $('#filterByUserType').val('all');

                this.trigger('searchChange', event.currentTarget.value);
            },
            'click #searchBtn': function (event) {
                var searchInput = $('#searchInput')[0];
                if (!searchInput.value) {
                    return;
                }
                $('#filterByUserType').val('all');
                this.trigger('searchChange', searchInput.value);
            },
            'click #searchResetBtn': function (event) {
                $('#searchInput')[0].value = '';
                $('#filterByUserType').val('all');
                this.trigger('searchReset');
            },
            'change #filterByUserType': function (event) {
                $('#searchInput')[0].value = '';
                var type = (event.currentTarget.value == 'all' ? null : event.currentTarget.value);
                this.trigger('filterByUserTypeChange', type);
            }
        },

        columns: [
            {
                title  : 'ID',
                keyName: 'id',
                width  : 20
            },
            {
                title  : 'Email',
                keyName: 'email'
            },
            {
                title  : 'First name',
                keyName: 'firstName'
            },
            {
                title  : 'Last name',
                keyName: 'lastName'
            },
            {
                title        : 'Role',
                keyName      : 'roles',
                width        : 130,
                valueRenderer: function (value, item) {
                    if (value.indexOf('ROLE_SOLUTION_ADMIN') !== -1) {
                        return 'Solution Admin';
                    } else if (value.indexOf('ROLE_RESTAURANT_ADMIN') !== -1) {
                        return 'Restaurant Admin';
                    } else if (value.indexOf('ROLE_RESTAURANT_STAFF') !== -1) {
                        return 'Restaurant Staff';
                    } else {
                        return 'Guest';
                    }
                }
            },
            {
                title        : 'Registered',
                keyName      : 'registered',
                width        : 80,
                valueRenderer: function (value, item) {
                    return value ? 'yes' : 'no';
                }
            }
        ],

        containerClass: 'table table-bordered table-striped table-hover',

        getToolbarButtons: function getToolbarButtons() {
            return '\
                <div class="input-append" id="search">\
                    <input type="text" placeholder="search by name" class="input-xlarge" id="searchInput">\
                    <button class="btn" type="button" id="searchBtn">Search</button>\
                    <button class="btn" type="button" id="searchResetBtn"><i class="icon-remove"></i></button>\
                </div>\
                \
                <select style="margin-left: 10px" id="filterByUserType">\
                    <option value="all">All</option>\
                    <option value="ROLE_SOLUTION_ADMIN">Solution Admin</option>\
                    <option value="ROLE_RESTAURANT_ADMIN">Restaurant Admin</option>\
                    <option value="ROLE_RESTAURANT_STAFF">Restaurant Staff</option>\
                    <option value="ROLE_RESTAURANT_GUEST">Guest</option>\
                </select>\
            ';
        },

        getToolbar: function getToolbar() {
            return '\
                <div class="btn-toolbar pull-left" style="margin: 0">\
                ' + this.getToolbarButtons() + '\
                </div>\
            ';
        },

        /**
         * @protected
         * @return {string}
         */
        getHeader: function getHeader() {
            return this.getToolbar();
        },

        renderPageHeader: function () {
            var headerId = this.cid + '-header';
            if (! this.el.firstChild || this.el.firstChild.id !== headerId) {
                var target = document.createElement('div');
                target.id = headerId;
                target.className = 'clearfix';
                target.innerHTML = this.getHeader();
                this.el.insertBefore(target, this.el.firstChild);
            }
        },

        setupPagination: function setupPagination() {
            var me = this;

            if (! $('.pagination', this.$el).length) {
                me.$el.append('<div class="pagination pagination-centered"></div>');
            }

            $('.pagination', me.$el).pagination(
                me.collection.getTotalCount(),
                {
                    items_per_page  : me.collection.getPageSize(),
                    current_page    : me.collection.currentPage - 1,
                    num_edge_entries: 1,
                    renderer        : "bootstrapRenderer",
                    load_first_page : false,
                    callback        : function (page, component) {
                        me.trigger('pageChange', page + 1); // +1 because page is zero-based
                    }
                }
            );

            return me;
        },

        afterRender: function () {
            this.callParent(arguments);
            this.renderPageHeader();
            this.setupPagination();
        }

    });

    return UserListView;

});
