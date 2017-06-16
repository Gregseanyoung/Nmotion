define([

    // Libs
    'lib/view/Grid', 'jquery.pagination', 'bootstrap.datepicker'

], function (GridView) {

    /**
     * @class OrderListView
     * @extends GridView
     */
    var OrderListView = GridView.extend('OrderListView', {

        /**
         * @event filterByPeriodChange
         * Triggered when user has changed period filter. Applies only when both dates has been selected.
         * @param {Date} dateFrom
         * @param {Date} dateTo
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

        /**
         * @protected
         */
        paginationBarVisible: true,

        /**
         * @protected
         */
        mastersOnly: false,

        events: {
            'keydown #searchInput': function (event) {
                if (event.keyCode !== 13) {
                    return;
                }
                event.preventDefault();
                if (!event.currentTarget.value) {
                    return;
                }
                this.paginationBarVisible = false;
                this.trigger('searchChange', event.currentTarget.value);
            },
            'click #searchBtn': function (event) {
                var searchInput = $('#searchInput')[0];
                if (!searchInput.value) {
                    return;
                }
                this.paginationBarVisible = false;
                this.trigger('searchChange', searchInput.value);
            },
            'click #searchResetBtn': function (event) {
                $('#searchInput,#filterFrom,#filterTo').val('');
                this.paginationBarVisible = true;
                this.trigger('searchReset');
            },
            'click #exportToExcel': function (event) {
                var filterFrom = $('#filterFrom'),
                    filterTo = $('#filterTo'),
                    dateFrom = filterFrom.datepicker('getDate'),
                    dateTo = filterTo.datepicker('getDate');

                if (filterFrom.val() && filterTo.val()) {
                    this.trigger('export', dateFrom, dateTo);
                }
            },
            'changeDate #filterFrom': function (event) {
                /*$('#filterFrom').datepicker('hide');
                $('#filterTo').datepicker('show');*/
            },
            'hide #filterFrom,#filterTo': function (event) {
                var filterFrom = $('#filterFrom'),
                    filterTo = $('#filterTo'),
                    dateFrom = filterFrom.datepicker('getDate'),
                    dateTo = filterTo.datepicker('getDate');

                if (filterFrom.val() && filterTo.val()) {
                    this.trigger('filterByPeriodChange', dateFrom, dateTo);
                }
            },
            'click [id^=commonPeriod]': function (event) {
                var dateFrom, dateTo, daysSinceWeekStart,
                    _24h = 86400 * 1000,
                    now = Date.now(), date = new Date(),
                    commonPeriod = event.currentTarget.id.substring(12);

                switch (commonPeriod) {
                    case 'Today':
                        dateFrom = dateTo = date;
                        break;
                    case 'Yesterday':
                        dateFrom = dateTo = new Date(now - _24h);
                        break;
                    case 'ThisWeek':
                        daysSinceWeekStart = (date.getDay() === 0 ? 6 : date.getDay() - 1);
                        dateFrom = new Date(now - daysSinceWeekStart * _24h);
                        dateTo = date;
                        break;
                    case 'ThisMonth':
                        dateFrom = new Date(date.getFullYear(), date.getMonth());
                        dateTo = date;
                        break;
                    case 'ThisYear':
                        dateFrom = new Date(date.getFullYear(), 0);
                        dateTo = date;
                        break;
                    case 'LastWeek':
                        daysSinceWeekStart = (date.getDay() === 0 ? 6 : date.getDay() - 1);
                        dateFrom = new Date(now - (daysSinceWeekStart + 7) * _24h);
                        dateTo = new Date(now - (daysSinceWeekStart + 1) * _24h);
                        break;
                    case 'LastFortnight':
                        daysSinceWeekStart = (date.getDay() === 0 ? 6 : date.getDay() - 1);
                        dateFrom = new Date(now - (daysSinceWeekStart + 14) * _24h);
                        dateTo = new Date(now - (daysSinceWeekStart + 1) * _24h);
                        break;
                    case 'LastMonth':
                        dateFrom = new Date(date.getFullYear(), date.getMonth() - 1);
                        dateTo = new Date(new Date(date.getFullYear(), date.getMonth()).getTime() - _24h);
                        break;
                    case 'LastYear':
                        dateFrom = new Date(date.getFullYear() - 1, 0);
                        dateTo = new Date(new Date(date.getFullYear(), 0).getTime() - _24h);
                        break;
                }

                $('#filterFrom').datepicker('setDate', dateFrom);
                $('#filterTo').datepicker('setDate', dateTo);

                this.trigger('filterByPeriodChange', dateFrom, dateTo);
            }
        },

        columns: [
            {
                title  : 'ID',
                keyName: 'id',
                width  : 20
            },
            {
                title  : 'Restaurant',
                keyName: 'restaurant',
                valueRenderer: function (value, item) {
                    return value.name.toUpperCaseFirst();
                }
            },
            {
                title        : 'Service type',
                keyName      : 'serviceType',
                width        : 100,
                valueRenderer: function (value, item) {
                    return value.name.toUpperCaseFirst();
                }
            },
            {
                title  : 'Table number<br>Pickup time',
                keyName: 'tableNumber',
                width  : 95,
                valueRenderer: function (value, item) {
                    if (item.get('serviceType').name === 'takeaway') {
                        return new Date(item.get('takeawayPickupTime') * 1000).format('default');
                    } else {
                        return value;
                    }
                }
            },
            {
                title  : 'Product total',
                keyName: 'productTotal',
                width  : 100,
                valueRenderer: function (value, item) {
                    if (this.mastersOnly) {
                        value = item.get('consolidatedProductTotal');
                    }
                    
                    var restaurantCurrency = item.get("restaurant").currency;
                    if(typeof restaurantCurrency != "undefined" && typeof restaurantCurrency.sign != "undefined") {
                        return restaurantCurrency.sign + " " + value;
                    } else {
                        return value;
                    }
                }
            },
            {
                title  : 'Discount',
                keyName: 'discount',
                width  : 80,
                valueRenderer: function (value, item) {
                    if (this.mastersOnly) {
                        value = item.get('consolidatedDiscount');
                    }
                    
                    var restaurantCurrency = item.get("restaurant").currency;
                    if(typeof restaurantCurrency != "undefined" && typeof restaurantCurrency.sign != "undefined") {
                        return restaurantCurrency.sign + " " + value;
                    } else {
                        return value;
                    }
                }
            },
            {
                title  : 'Tax',
                keyName: 'salesTax',
                width  : 80,
                valueRenderer: function (value, item) {
                    if (this.mastersOnly) {
                        value = item.get('consolidatedSalesTax');
                    }
                    
                    var restaurantCurrency = item.get("restaurant").currency;
                    if(typeof restaurantCurrency != "undefined" && typeof restaurantCurrency.sign != "undefined") {
                        return restaurantCurrency.sign + " " + value;
                    } else {
                        return value;
                    }
                }
            },
            {
                title  : 'Tips',
                keyName: 'tips',
                width  : 60,
                valueRenderer: function (value, item) {
                    if (this.mastersOnly) {
                        value = item.get('consolidatedTips');
                    }
                    
                    var restaurantCurrency = item.get("restaurant").currency;
                    if(typeof restaurantCurrency != "undefined" && typeof restaurantCurrency.sign != "undefined") {
                        return restaurantCurrency.sign + " " + value;
                    } else {
                        return value;
                    }
                }
            },
            {
                title  : 'Order total',
                keyName: 'orderTotal',
                width  : 80,
                valueRenderer: function (value, item) {
                    if (this.mastersOnly) {
                        value = item.get('consolidatedOrderTotal');
                    }
                    
                    var restaurantCurrency = item.get("restaurant").currency;
                    if(typeof restaurantCurrency != "undefined" && typeof restaurantCurrency.sign != "undefined") {
                        return restaurantCurrency.sign + " " + value;
                    } else {
                        return value;
                    }
                }
            },
            {
                title        : 'Status',
                keyName      : 'orderStatus',
                width        : 110,
                valueRenderer: function (value, item) {
                    return value.name.toUpperCaseFirst();
                }
            },
            {
                title        : 'Order date',
                keyName      : 'updatedAt',
                width        : 180,
                valueRenderer: function (value, item) {
                    return new Date((value ? value : item.get('createdAt')) * 1000).format('default');
                }
            },
            {
                title        : 'Actions',
                keyName      : null,
                width        : 100,
                sortable     : false,
                valueRenderer: function (value, item) {
                    var btns =
                        '<a href="#order/details/{id}" class="btn">\
                            <i class="icon-book"></i> Details\
                        </a>';

                    return btns.replace(/{id}/g, item.getId());
                }
            }
        ],

        containerClass: 'table table-bordered table-striped table-hover',

        /**
         * @protected
         * @returns {string}
         */
        getToolbarButtons: function getToolbarButtons() {
            return '\
                <div class="input-append" id="search">\
                    <input type="text" placeholder="search by order ID" class="input-large" id="searchInput">\
                    <button class="btn" type="button" id="searchBtn">Search</button>\
                    <button class="btn" type="button" id="searchResetBtn"><i class="icon-remove"></i></button>\
                </div>\
                \
                <div class="input-prepend input-append" style="margin-left: 10px">\
                    <span class="add-on"><i class="icon-calendar"></i></span>\
                    <input type="text" id="filterFrom" name="filterFrom" class="input-small" placeholder="from" style="text-align: center"/>\
                </div>\
                <div class="input-prepend input-append btn-group">\
                    <input type="text" id="filterTo" name="filterTo" class="input-small" placeholder="to" style="border-left: 0px none; text-align: center"/>\
                    <button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>\
                    <ul class="dropdown-menu">\
                        <li><a href="javascript:;" id="commonPeriodToday">Today</a></li>\
                        <li><a href="javascript:;" id="commonPeriodYesterday">Yesterday</a></li>\
                        <li><a href="javascript:;" id="commonPeriodThisWeek">This week</a></li>\
                        <li><a href="javascript:;" id="commonPeriodThisMonth">This month</a></li>\
                        <li><a href="javascript:;" id="commonPeriodThisYear">This year</a></li>\
                        <li><a href="javascript:;" id="commonPeriodLastWeek">Last week</a></li>\
                        <li><a href="javascript:;" id="commonPeriodLastFortnight">Last fortnight</a></li>\
                        <li><a href="javascript:;" id="commonPeriodLastMonth">Last month</a></li>\
                        <li><a href="javascript:;" id="commonPeriodLastYear">Last year</a></li>\
                    </ul>\
                </div>\
                \
                <button class="btn btn-success" style="vertical-align: top; margin-left: 10px" type="button" id="exportToExcel">Export to Excel</button>\
            ';
        },

        /**
         * @protected
         * @returns {string}
         */
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

        /**
         * @protected
         */
        renderPageHeader: function renderPageHeader() {
            var headerId = this.cid + '-header';
            if (! this.el.firstChild || this.el.firstChild.id !== headerId) {
                var target = document.createElement('div');
                target.id = headerId;
                target.className = 'clearfix';
                target.innerHTML = this.getHeader();
                this.el.insertBefore(target, this.el.firstChild);
            }
        },

        /**
         * @protected
         * @returns this
         */
        setupPagination: function setupPagination() {
            var me = this;

            if (!me.paginationBarVisible) {
                $('.pagination', me.$el).remove();
                return me;
            }

            if (! $('.pagination', me.$el).length) {
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

        /**
         * @protected
         */
        setupDatePickers: function setupDatePickers() {
            $('#filterFrom,#filterTo').datepicker({weekStart: 1, format: 'dd-mm-yyyy'});
        },

        /**
         * @protected
         */
        renderList: function renderList() {
            if (this.mastersOnly) {
                this.callParent(
                    [ this.collection.where( {isMaster: true} ) ]
                );
            } else {
                this.callParent();
            }
        },
               
//        /**
//         * to auto refresh listing
//         * @protected
//         */
//        setUpAutoRefresh: function(){
//            //console.log(this.collection)
//            var refresh_seconds = 20;
//            
//            current_user = sessionStorage.getItem("UserModel");
//            
//            if(current_user) { 
//                current_user = $.parseJSON(sessionStorage.getItem("UserModel"))
//                
//                if($.inArray("ROLE_SOLUTION_ADMIN", current_user.roles)==-1 && typeof $order_timer == "undefined" && $can_create_timer){
//                    $can_create_timer = false;
//                    $this = this;
//                    $order_timer = setInterval(function(){
//                        $this.trigger('searchReset');
//                    }, refresh_seconds * 1000);
//                }
//            }
//        }, 
//        
//        highlighRows: function(){
//            current_user = sessionStorage.getItem("UserModel");
//            
//            if(current_user && $is_order_listing) { 
//                current_user = $.parseJSON(sessionStorage.getItem("UserModel"))
//                
//                if($.inArray("ROLE_SOLUTION_ADMIN", current_user.roles)==-1 && typeof this.collection.models != "undefined"){
//                    var highlight_classes = {1:"error",2:"info",3:"info",4:"warning",5:"warning",6:"success"};
//                    var table_rows = this.$el.find("table tbody tr");
//                    
//                    for(i=0; i< table_rows.length;i++){
//                        if(typeof this.collection.models[i] != "undefined"){
//                            
//                            $(table_rows[i]).addClass(highlight_classes[this.collection.models[i].attributes.orderStatus.id]);
//                        }
//                    }
//                }
//            }
//        },
                
        /**
         * @protected
         */
        afterRender: function () {
            this.callParent(arguments);
            this.renderPageHeader();
            this.setupPagination();
            this.setupDatePickers();
            
//            this.setUpAutoRefresh();
//            this.highlighRows();
//            
        }

    });

    return OrderListView;

});