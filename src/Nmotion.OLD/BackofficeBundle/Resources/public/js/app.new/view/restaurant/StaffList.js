define([

    // Libs
    'lib/view/Grid'

], function (GridView) {

    /**
     * @class RestaurantStaffListView
     * @extends GridView
     */
    var RestaurantStaffListView = GridView.extend('RestaurantStaffListView', {

        events: {
            'click button#batch-delete': function (event) {
                var idsArray = [];
                $(':checkbox:checked').each(function() {
                    idsArray.push(parseInt(this.getAttribute('data-id')));
                });
                if (idsArray.length > 0) {
                    this.trigger('batchDeleteClick', this, idsArray);
                }
            }
        },

        columns: [
            {
                title         : ' ',
                keyName       : null,
                width         : 16,
                valueRenderer : function (value, item) {
                    return '<input type="checkbox" data-id="' + item.getId() + '">';
                }
            },
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
                title        : 'Actions',
                keyName      : null,
                width        : 180,
                valueRenderer: function (value, item) {
                    var btns = [
                        '<a href="#restaurant/staff/edit/{id}" class="btn btn-small"><i class="icon-edit"></i> Edit</a>',
                        '<a href="#restaurant/staff/delete/{id}" class="btn btn-small btn-danger"><i class="icon-remove"></i> Delete</a>'
                    ];
                    return btns.join("&nbsp;").replace(/{id}/g, item.getId());
                }
            }
        ],

        containerClass: 'table table-bordered table-striped table-hover',

        /**
         * @protected
         * @return {?RestaurantModel}
         */
        getRestaurant: function getRestaurant() {
            if (this.getCollection().restaurant) {
                return this.getCollection().restaurant;
            }
            if (this.getCollection().first()) {
                return this.getCollection().first().get('restaurant');
            }
            throw new Error('restaurant MUST be set');
        },

        /**
         * @protected
         * @return {string}
         */
        getListTitle: function getListTitle() {
            return this.getRestaurant().get('name') + ' staff';
        },

        /**
         * @protected
         * @return {string}
         */
        getNavigationBar: function getNavigationBar() {
            var app = require('Nmotion').getInstance();
            if (app.user.isSolutionAdmin()) {
                return '<a href="#/restaurant/manage" class="back">&larr; Back to Restaurants</a>';
            } else if (app.user.isRestaurantAdmin()) {
                return '';
            }
        },

        getToolbarButtons: function getToolbar() {
            return '<a href="#restaurant/staff/new/' + this.getRestaurant().getId() + '" class="btn btn-small">Add staff</a>\
                    <button id="batch-delete" class="btn btn-small btn-danger">Delete</button>';
        },

        getToolbar: function getToolbar() {
            return '<div class="btn-toolbar">' + this.getToolbarButtons() + '</div>';
        },

        /**
         * @protected
         * @return {string}
         */
        getHeader: function getHeader() {
            var me = this,
                headerTitle = '\
                <div id="headerTitle" style="margin-bottom: 10px" class="well well-small">\
                    <h3 style="margin: 0">' + me.getListTitle() + '</h3>\
                </div>';

            return headerTitle + ' ' + me.getNavigationBar() + ' ' + me.getToolbar();
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
            this.renderPageHeader();
        }

    });

    return RestaurantStaffListView;

});
