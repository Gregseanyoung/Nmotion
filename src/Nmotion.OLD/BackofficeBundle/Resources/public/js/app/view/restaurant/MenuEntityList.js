define([

    // Libs
    'lib/Console', 'lib/view/Grid', 'jquery-ui'

], function (console, GridView) {

    /**
     * @class RestaurantMenuEntityListView
     * @extends GridView
     */
    var RestaurantMenuEntityListView = GridView.extend('RestaurantMenuEntityListView', {

        /**
         * @protected
         */
        sortable: true,

        events: {
            'change input:checkbox': function (event) {
                var me = this,
                    ids = [],
                    href = me.getControllerPath();

                me.$el.find('input:checkbox:checked').each(function () {
                    ids.push(parseInt(this.getAttribute('data-id')));
                });

                if (ids) {
                    ids = JSON.stringify({ids: ids});
                    me.$el.find('#batch-hide').attr('href', href + 'batch-hide/' + ids);
                    me.$el.find('#batch-delete').attr('href', href + 'batch-delete/' + ids);
                }
            }
        },

        essentialColumns: [
            {
                title         : ' ',
                keyName       : null,
                width         : 16,
                valueRenderer : function (value, item) {
                    return '<div class="dd-handle" data-cid="' + item.cid + '" data-position="' + item.get('position') + '">&nbsp;</div>';
                }
            },
            {
                title         : ' ',
                keyName       : null,
                width         : 16,
                sortable      : false,
                valueRenderer : function (value, item) {
                    return '<input type="checkbox" data-id="' + item.getId() + '">';
                }
            },
            {
                title        : 'Name',
                keyName      : 'name'
            },
            {
                title        : 'Time From',
                keyName      : 'timeFrom',
                width        : 100,
                valueRenderer: function (value, item) {
                    return value === null ? 'n/a' : this.secondsToTime(value);
                }
            },
            {
                title        : 'Time To',
                keyName      : 'timeTo',
                width        : 100,
                valueRenderer: function (value, item) {
                    return value === null ? 'n/a' : this.secondsToTime(value);
                }
            },
            {
                title        : 'Visibility',
                keyName      : 'visible',
                width        : 100,
                valueRenderer: function (value, item) {
                    return value ? 'visible' : 'hidden';
                }
            }
        ],

        containerClass: 'table table-bordered table-striped table-hover',

        initialize: function initialize() {
            this.columns = _.union(this.essentialColumns, this.columns);
            this.callParent(arguments);
        },

        /**
         * @protected
         * @return {string}
         */
        getControllerPath: function getControllerPath() {
            throw new Error('getControllerPath() must be overridden in inherited controller');
        },

        /**
         * @protected
         * @return {string}
         */
        getListTitle: function getListTitle() {
            throw new Error('getListTitle() must be overridden in inherited controller');
        },

        /**
         * @protected
         * @return {string}
         */
        getNavigationBar: function getNavigationBar() {
            return '';
        },

        getToolbarButtons: function getToolbar() {
            return '<a href="' + this.getControllerPath() + '" id="batch-hide" class="btn btn-small">Hide selected</a> \
                    <a href="' + this.getControllerPath() + '" id="batch-delete" class="btn btn-small btn-danger">Delete</a>';
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

        /**
         * @protected
         * @return {string}
         */
        secondsToTime: function secondsToTime(value) {
            var hours, minutes, seconds;

            // evaluate hours and minutes
            seconds = Math.floor(value / 60);
            hours = Math.floor(seconds / 60);
            minutes = seconds % 60;

            // apply leading zero
            hours = (hours < 10 ? '0' : '') + hours;
            minutes = (minutes < 10 ? '0' : '') + minutes;

            // set time in format hh:mm
            return hours + ':' + minutes;
        },

        /**
         * @protected
         */
        setupSortable: function () {
            var me = this;

            me.$el.find('table tbody').sortable(
                {
                    axis       : "y",
                    containment: "parent",
                    helper     : function (e, tr) {
                        var $originals = tr.children();
                        var $helper = tr.clone();
                        $helper.children().each(function (index) {
                            // Set helper cell sizes to match the original sizes
                            $(this).width($originals.eq(index).width())
                        });
                        return $helper;
                    },
                    update     : function (event, ui) {
                        var oldPosition, newPosition, cid;

                        cid = ui.item.find('.dd-handle').attr('data-cid');

                        // fixate old position
                        oldPosition = ui.item.find('.dd-handle').attr('data-position');
                        // reevaluate items position
                        ui.item.parent().find('.dd-handle').each(function (index, el) {
                            this.setAttribute('data-position', index);
                        });
                        // fixate new position
                        newPosition = ui.item.find('.dd-handle').attr('data-position');

                        if (oldPosition !== newPosition) {
                            me.trigger('onPositionChange', {
                                cid         : cid,
                                oldPosition: oldPosition,
                                newPosition: newPosition
                            });
                        }
                    },
                    tolerance  : "pointer"
                }
            ).disableSelection();
        },

        renderPageHeader: function () {
            var headerId = this.cid + '-header';
            if (! this.el.firstChild || this.el.firstChild.id !== headerId) {
                var target = document.createElement('div');
                target.id = headerId;
                target.innerHTML = this.getHeader();
                this.el.insertBefore(target, this.el.firstChild);
            }
        },

        afterRender: function () {
            this.callParent(arguments);
            this.renderPageHeader();
            if (this.sortable) {
                this.setupSortable();
            }
        }

    });

    return RestaurantMenuEntityListView;

});
