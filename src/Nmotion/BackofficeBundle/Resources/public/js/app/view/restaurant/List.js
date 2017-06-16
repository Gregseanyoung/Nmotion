define([

    // Libs
    'lib/view/Grid', 'jquery.pagination'

], function (GridView) {

    /**
     * @class RestaurantListView
     * @extends GridView
     *
     * @event pageChange
     * @event pageSizeChange
     * @event searchChange
     * @event batchVisibilityChange
     */
    var RestaurantListView = GridView.extend('RestaurantListView', {

        events: {
            'click #pageSize>button': function (event) {
                this.trigger('pageSizeChange', event.currentTarget.value);
            },
            'keydown #search>input': function (event) {
                if (event.keyCode !== 13) {
                    return;
                }
                event.preventDefault();
                this.trigger('searchChange', event.currentTarget.value);
            },
            'click #search>button': function (event) {
                this.trigger('searchChange', event.currentTarget.previousSibling.value);
            },
            'click #check-all-switch': function (event) {
                $('.entry-check').each(function () {
                    this.checked = event.currentTarget.checked;
                });

                // emulate change event
                $('.entry-check:first').trigger('change');
            },
            'change .entry-check': function (event) {
                if ($('.entry-check:checked').length) {
                    $('#selection-btn-group').fadeIn('fast');
                } else {
                    $('#selection-btn-group').fadeOut('fast');
                }
            },
            'click #btn-hide-selected,#btn-show-selected': function (event) {
                var ids = this.getCheckedEntriesIds(),
                    visible = (event.currentTarget.id === 'btn-show-selected');
                if (ids.length) {
                    this.trigger('batchVisibilityChange', this, ids, visible);
                    $('#selection-btn-group').fadeOut('fast');
                }
            }
        },

        columns: [
            {
                title         : ' ',
                keyName       : null,
                width         : 16,
                sortable      : false,
                headerRenderer: function (title) {
                    return '<input type="checkbox" id="check-all-switch">';
                },
                valueRenderer : function (value, item) {
                    return '<input type="checkbox" class="entry-check" data-id="' + item.getId() + '">';
                }
            },
            {
                title   : 'ID',
                keyName : 'id',
                width   : 30,
                sortable: true
            },
            {
                title        : 'Name',
                keyName      : 'name',
                sortable     : true
                /*valueRenderer: function (value, item) {
                    return value.link('#restaurant/profile/' + item.get('id'));
                }*/
            },
            {
                title        : 'Address line',
                keyName      : 'address',
                sortable     : false,
                valueRenderer: function (value, item) {
                    return value.get('addressLine1');
                }
            },
            {
                title        : 'City',
                keyName      : 'address',
                sortable     : false,
                valueRenderer: function (value, item) {
                    return value.get('city');
                }
            },
            {
                title        : 'Postal code',
                keyName      : 'address',
                sortable     : false,
                valueRenderer: function (value, item) {
                    return value.get('postalCode');
                }
            },
            {
                title        : 'Contact person phone',
                keyName      : 'contactPersonPhone',
                sortable     : false
            },
            {
                title        : 'Website',
                keyName      : 'siteUrl',
                sortable     : false,
                valueRenderer: function (value, item) {
                    if (value === null) {
                        return '';
                    } else {
                        return '<a href="' + value + '" target="_blank">site</a>';
                    }
                }
            },
            {
                title        : 'Admin email',
                keyName      : 'adminUser',
                sortable     : false,
                valueRenderer: function (value, item) {
                    return value.get('email');
                }
            },
            {
                title        : 'Registration date',
                keyName      : 'createdAt',
                width        : 150,
                sortable     : true,
                valueRenderer: 'date'
            },
            {
                title        : 'TA member',
                keyName      : 'taMember',
                width        : 80,
                valueRenderer: function (value, item) {
                    return value ? 'yes' : 'no';
                }
            },
            {
                title        : 'Visibility',
                keyName      : 'visible',
                width        : 80,
                valueRenderer: function (value, item) {
                    return value ? 'visible' : 'hidden';
                }
            },
            {
                title        : 'Actions',
                keyName      : null,
                width        : 120,
                sortable     : false,
                valueRenderer: function (value, item) {
                    var btns =
                        '<div class="btn-group">\
                            <a href="#restaurant/menu-category/index/{%22restaurantId%22:{id}}" class="btn">\
                                <i class="icon-align-justify"></i> Menu\
                            </a>\
                            <button class="btn dropdown-toggle" data-toggle="dropdown">\
                                <span class="caret"></span>\
                            </button>\
                            <ul class="dropdown-menu pull-right">\
                                <li>\
                                    <a href="#restaurant/profile/edit/{id}">\
                                        <i class="icon-edit"></i> Edit\
                                    </a>\
                                </li>\
                                <li>\
                                    <a href="#restaurant/income/index/{id}">\
                                        <i class="icon-2dollar"></i> Income\
                                    </a>\
                                </li>\
                                <li>\
                                    <a href="#restaurant/staff/list/{id}">\
                                        <i class="icon-user"></i> Staff\
                                    </a>\
                                </li>\
                                <li>\
                                    <a href="#restaurant/delete/index/{id}">\
                                        <i class="icon-remove"></i> Delete\
                                    </a>\
                                </li>\
                            </ul>\
                        </div>';

                    return btns.replace(/{id}/g, item.getId());
                }
            }
        ],

        containerClass: 'table table-bordered table-striped table-hover',

        getCheckedEntriesIds: function () {
            var ids = [];
            $('.entry-check:checked').each(function () {
                ids.push(parseInt(this.getAttribute('data-id')));
            });
            return ids;
        },

        setupPagination: function setupPagination() {
            var me = this;

            if (! $('.pagination', this.$el).length) {
                me.$el.append('<div class="pagination pagination-centered"></div>');
            }

            $('.pagination', me.$el).pagination(
                me.collection.getTotalCount(),
                {
                    items_per_page : me.collection.getPageSize(),
                    current_page   : me.collection.currentPage - 1,
                    num_edge_entries: 1,
                    renderer       : "bootstrapRenderer",
                    load_first_page: false,
                    callback       : function (page, component) {
                        me.trigger('pageChange', page + 1); // +1 because page is zero-based
                    }
                }
            );

            return me;
        },

        getToolbarButtons: function getToolbarButtons() {
            return '\
                <div class="input-append" id="search">\
                    <input type="text" placeholder="search by id, name or admin email" class="input-xlarge">' +
                    '<button class="btn" type="button">Search</button>\
                </div>\
                <div class="btn-group" style="margin: 0 0 10px 10px; display: none" id="selection-btn-group">\
                    <button class="btn" id="btn-hide-selected">Hide selected</button>\
                    <button class="btn" id="btn-show-selected">Show selected</button>\
                </div>\
            ';
        },

        getToolbar: function getToolbar() {
            return '\
                <div class="btn-toolbar pull-left" style="margin: 0">\
                ' + this.getToolbarButtons() + '\
                </div>\
                <div class="btn-toolbar pull-right" style="margin: 0" data-toggle="buttons-radio">\
                    <div class="btn-group" id="pageSize">\
                        <button value="20" class="btn active">20</button>\
                        <button value="50" class="btn">50</button>\
                        <button value="100" class="btn">100</button>\
                    </div>\
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

        afterRender: function () {
            this.callParent(arguments);
            this.setupPagination();
            this.renderPageHeader();
        }

    });

    return RestaurantListView;

});
