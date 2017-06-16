define([

    // Libs
    'lib/view/Grid', 'jquery.pagination', 'bootstrap.datepicker'

], function (GridView) {

    /**
     * @class NewOrdersListView
     * @extends GridView
     */
    var NewOrdersListView = GridView.extend('NewOrdersListView', {

        events: {
            'click .back': function (event) {
                this.trigger('navigateBack');
            }
        },
                
        containerTemplate: '\
			<div class="fixedHeightList"><table cellpadding="0" cellspacing="0" border="0" class="<%= className %>">\
				<%= header %>\
				<tbody>\
					<%= items %>\
				</tbody>\
			</table></div>\
		',
            
        /**
         * @protected
         */
        paginationBarVisible: true,

        /**
         * @protected
         */
        mastersOnly: false,

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
                        '<a href="#realtime-orders/details/{id}" class="btn">\
                            <i class="icon-book"></i> Details\
                        </a>';

                    return btns.replace(/{id}/g, item.getId());
                }
            }
        ],

        containerClass: 'table table-bordered table-striped table-hover',

        /**
         * @protected
         * @return {string}
         */
        getNavigationBar: function getNavigationBar() {
            return '\
                <a href="javascript:;" class="back" style="display:block;margin-bottom:5px">\
                    &larr; Back to Home\
                </a>';
        },
                
        /**
         * @protected
         * @return {string}
         */
        getHeader: function getHeader() {
            var me = this,
                headerTitle = '\
                <div id="headerTitle" style="margin-bottom: 10px" class="well well-small">\
                    <h3 style="margin: 0">New Orders</h3>\
                </div>';

            return this.getNavigationBar() + headerTitle;
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
                
        /**
         * @protected
         * @returns this
         */
        setupPagination: function setupPagination() {
            var me = this;

            if (!me.paginationBarVisible) {
                $('.newOrderPagination .pagination', me.$el).remove();
                return me;
            }

            if (! $('.newOrderPagination', me.$el).length) {
                me.$el.append('<div class="newOrderPagination pagination pagination-centered"></div>');
            }

            $('.newOrderPagination', me.$el).pagination(
                me.collection.getTotalCount(),
                {
                    items_per_page  : me.collection.getPageSize(),
                    current_page    : me.collection.currentPage - 1,
                    num_edge_entries: 1,
                    renderer        : "bootstrapRenderer",
                    load_first_page : false,
                    callback        : function (page, component) {
                        me.trigger('newOrdersPageChange', page + 1); // +1 because page is zero-based
                    }
                }
            );

            return me;
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
                
        /**
         * @protected
         */
        afterRender: function () {
            this.callParent(arguments);
            this.renderPageHeader();
            this.setupPagination();
        }

    });

    return NewOrdersListView;

});