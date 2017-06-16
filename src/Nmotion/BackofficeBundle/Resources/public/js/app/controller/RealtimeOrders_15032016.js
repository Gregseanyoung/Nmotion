define([

    // library dependencies
    'lib/controller/List',

    // application dependencies, collection of new orders and completed orders
    'collection/NewOrders',
    'collection/CompletedOrders',
    
    'model/Order',
    
    /* listing views of new orders and completed orders */
    'view/order/NewOrdersList',
    'view/order/CompletedOrdersList',
    
    /* bootstrap modal view */
    'view/BootstrapModal',
    
    /* template html files for displaying detail of order */
    'text!view/order/details/general_info.html',
    'text!view/order/details/meals.html',
    'text!view/order/details/payments.html'

], function (

    ListController,

    NewOrdersCollection,
    CompletedOrdersCollection,
    
    OrderModel,

    NewOrdersListView,
    CompletedOrdersListView,
    BootstrapModal,
    
    GeneralInfoTemplate,
    MealsTemplate,
    PaymentsTemplate
) {

    /**
     * @class RealtimeOrdersController
     * @extends ListController
     */
    var RealtimeOrdersController = ListController.extend('RealtimeOrdersController', {

        /**
         * @type {NmotionApp}
         */
        app: undefined,
                
        /**
         * @type {String}
         */
        completedOrdersPeriod: 'd',
                
        /**
         * @type {OrderCollection}
         */
        collection: NewOrdersCollection,
        
        /**
         * @type {int}
         */    
        newOrdersCount: 0,
        
        /**
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
            this.app = application;
        },

        /**
         * @method
         * @protected
         * @return {OrderListView}
         */
        createNewOrdersListView: function () {
            var me = this, view;
            
            $NewOrdersCollectionObj = new NewOrdersCollection;
            me.setCollection($NewOrdersCollectionObj);
            var newOrdersLastSyncTime = localStorage.getItem('newOrdersLastSyncTime');
            
            //default time of 2000-1-1 00:00:00
            newOrdersLastSyncTime = newOrdersLastSyncTime==null? 946684800 : newOrdersLastSyncTime;
            
            me.collection.setExtraParam("newOrdersLastSyncTime",newOrdersLastSyncTime);
            me.collection.setRemoteSort(true).sortBy('updatedAt', 'DESC');
            
            /** @type {OrderListView} */
            view = new NewOrdersListView();
            view.setCollection(me.collection);
            view.on('newOrdersPageChange', function(page){
                me.changeCollection($NewOrdersCollectionObj); 
                me.onNewOrdersPageChange(page);
            });
            view.on('navigateBack', function () {
                me.navigate('', null, {trigger: true});
            });
            
            me.parentView.setWidget(view);

            return view;
        },
                
        /**
         * @method
         * @protected
         * @return {OrderListView}
         */
        createCompletedOrdersListView: function () {
            var me = this, view;
            $CompletedOrdersCollectionObj = new CompletedOrdersCollection;
            me.setCollection($CompletedOrdersCollectionObj);
            me.collection.setRemoteSort(true).sortBy('updatedAt', 'DESC');
            me.collection.setExtraParam('period', this.completedOrdersPeriod);
            
            /** @type {OrderListView} */
            view = new CompletedOrdersListView();
            view.setCollection(me.collection);
            view.on('completedOrdersPageChange', function(page){
                me.changeCollection($CompletedOrdersCollectionObj); 
                me.onCompletedOrdersPageChange(page);
            });
            view.on('periodChange', function (newPeriod) {
                me.changeCollection($CompletedOrdersCollectionObj); 
                me.collection.setExtraParam('period', newPeriod).fetch();
            });
            
            me.parentView.appendWidget(view);
            
            view.setPeriod(this.completedOrdersPeriod);
            
            return view;
        },

        /**
         * @public
         * @param {Object} params
         */
        indexAction: function (params) {
            var me = this;
            
            delete($NewOrdersCollectionObj);
            delete($CompletedOrdersCollectionObj);
            delete($NewOrdersListView);
            delete($CompletedOrdersListView);
            delete($this);

            $this = this;
            
            $NewOrdersListView = me.createNewOrdersListView();
            if (me.collection.isEmpty()) {
                me.getData();
            }
            
            // $CompletedOrdersListView = me.createCompletedOrdersListView();
            // if (me.collection.isEmpty()) {
            //     me.getData();
            // }
            
            me.subscribeToCollectionEvents();
            me.setUpAutoRefresh();
        },

        changeCollection: function(collectionToChange){
            this.setCollection(collectionToChange);
            if(collectionToChange == $NewOrdersCollectionObj){
                this.collection.setExtraParam("newOrdersLastSyncTime",localStorage.getItem('newOrdersLastSyncTime'));
            }
            this.subscribeToCollectionEvents();
        },
        
        subscribeToCollectionEvents: function subscribeToCollectionEvents(){
            var me = this;
            
            me.off('onCollectionSync').on('onCollectionSync', function (updatedCollection) {
                
                NewOrdersCollectionData = $NewOrdersCollectionObj.getData();
                if(NewOrdersCollectionData && typeof NewOrdersCollectionData.newSynced != "undefined" && NewOrdersCollectionData.newOrdersLastSyncTime > localStorage.getItem("newOrdersLastSyncTime")) {
                    localStorage.setItem("newOrdersLastSyncTime", NewOrdersCollectionData.newOrdersLastSyncTime);
                    
					if(NewOrdersCollectionData.newSynced > 0) {
                    	if($(".newOrderPopup").length == 0 || ( $(".newOrderPopup").length && !$(".newOrderPopup").is(":visible") ) ) {
	                    	
	                    	if($(".newOrderPopup").length) $(".newOrderPopup").remove();

	                        this.newOrdersCount = NewOrdersCollectionData.newSynced;
	                        var modalView = new BootstrapModal({className:"modal fade hide newOrderPopup"});
	                        var popup_html = "<center><h1><span id='newOrdersCount'>" + NewOrdersCollectionData.newSynced + "</span> New orders received </h1><br>"
	                                    +"<img src='/bundles/nmotionbackoffice/images/confirm_order_icon.png'><br><br><h1>Please Confirm</h1></center>";
	                        modalView.setText(popup_html);
	                        modalView.setHeaderVisibility(false);
	                        modalView.setFooterVisibility(false);
	                        modalView.on("onModalBodyClick", function(){
	                            modalView.hide();
	                        });
	                        modalView.on("onHide", function(){
	                            this.newOrdersCount = 0;
	                        });
	                        modalView.show();
                    	} else if($("#newOrdersCount").length){
                        	$("#newOrdersCount").text(parseInt($("#newOrdersCount").text()) + NewOrdersCollectionData.newSynced);
                    	}
                    }
                }
                
                if(updatedCollection == $NewOrdersCollectionObj){
                    $NewOrdersListView.render();

                    if(typeof $CompletedOrdersListView == "undefined") {
                        $CompletedOrdersListView = me.createCompletedOrdersListView();
                        if (me.collection.isEmpty()) {
                            me.getData();
                        }
                    } else {
                        me.completedOrdersPeriod = "d";
                        me.changeCollection($CompletedOrdersCollectionObj); 
                        me.collection.setExtraParam('period', me.completedOrdersPeriod);
                        $CompletedOrdersListView.setPeriod(me.completedOrdersPeriod);
                        me.onCompletedOrdersPageChange(1);
                    }
                }
                else
                    $CompletedOrdersListView.render();
                
            });

            me.off('onCollectionChange').on('onCollectionChange', function (updatedCollection) {
                if(updatedCollection == $NewOrdersCollectionObj && typeof $NewOrdersListView != "undefined") $NewOrdersListView.render();
                else if(typeof $CompletedOrdersListView != "undefined") $CompletedOrdersListView.render();
            });

            me.off('onCollectionReset').on('onCollectionReset', function (updatedCollection) {
                if(updatedCollection == $NewOrdersCollectionObj && typeof $NewOrdersListView != "undefined") $NewOrdersListView.render();
                else if(typeof $CompletedOrdersListView != "undefined") $CompletedOrdersListView.render();
            });
        },
                
        /**
         * @method
         * @protected
         * @param {Number} page
         */
        onNewOrdersPageChange: function onNewOrdersPageChange(page) {
            this.collection.fetchPage(page);
        },
        
        /**
         * @method
         * @protected
         * @param {Number} page
         */
        onCompletedOrdersPageChange: function onCompletedOrdersPageChange(page) {
            this.collection.fetchPage(page);
        },
        
        /**
         * to auto refresh listing
         * @protected
         */
        setUpAutoRefresh: function setUpAutoRefresh(){

            var refresh_seconds = 20;
            
            current_user = sessionStorage.getItem("UserModel");
            
            if(current_user) { 
                current_user = $.parseJSON(sessionStorage.getItem("UserModel"))
                
                if($.inArray("ROLE_SOLUTION_ADMIN", current_user.roles)==-1 && typeof $order_timer == "undefined" && $can_create_timer){
                    $can_create_timer = false;
                    
                    $order_timer = setInterval(function(){
                        $this.refreshListing();
                    }, refresh_seconds * 1000);
                }
            }
        },
        
        refreshListing:function(){
            $this.changeCollection($NewOrdersCollectionObj); 
            $this.onNewOrdersPageChange(1);
            
            // $this.completedOrdersPeriod = "d";
            // $this.changeCollection($CompletedOrdersCollectionObj); 
            // $this.onCompletedOrdersPageChange(1);
        },
                
        detailsAction: function(params){
            var me = this;
            
            var order = OrderModel.findOrCreate(params.id);
            if (order === null) {
                order = new OrderModel({id: params.id});
            }

            order.fetch({
                async: false,
                // spike-fix
                url: order.urlRoot + '/' + order.getId()
            });
                    
            var orderUser = order.get("user");
            var order_html = "<h5>Guest: " + orderUser.firstName + " " + orderUser.lastName + "</h5>";
            
            if(order.get("contactPhoneNumber")){
                order_html += "<h5>Contact Number: " + order.get("contactPhoneNumber") + "</h5>";
            }
            
            order_html += _.template(GeneralInfoTemplate)({order: order.toJSON()});
            order_html += _.template(MealsTemplate)({meals: order.toJSON().orderMeals, currency: order.get("restaurant").currency.sign});
            order_html += _.template(PaymentsTemplate)({payments: order.toJSON().payments, currency: order.get("restaurant").currency.sign});
            
            var modalView = new BootstrapModal({className:"modal fade hide orderDetailPopup"});
            modalView.setTitle("Order Number " + params.id);
            modalView.setText(order_html);
            
            var order_status = order.attributes.orderStatus.id;
            
            if(order_status == 6 || order_status == 7){
                
                var footer_content = '';
                if(order_status == 6){
                    footer_content = '<button class="btn btn-success" data-status="accepted" data-status-text="accepted" data-click-function="orderStatusUpdates">Accept Order</button>'
                                        +'<button class="btn btn-success" data-status="acceptAndClose" data-status-text="accepted and closed" data-click-function="orderStatusUpdates">Accept and Close Order</button>';
                } else if(order_status == 7){
                    footer_content = '<button class="btn btn-success" data-status="readyForPickup" data-status-text="ready for pickup" data-click-function="orderStatusUpdates">Ready for Pickup</button>'
                                        +'<button class="btn btn-success" data-status="readyForServe" data-status-text="ready for serve" data-click-function="orderStatusUpdates">Order Ready</button>';
                }
                
                modalView.setFooterContent(footer_content);
                modalView.setFooterVisibility(true);
                modalView.setCloseButtonVisibility(false);
                modalView.on('orderStatusUpdates', function(btn){
                    var status_name = $(btn).attr("data-status");
                    order.save({orderStatus:{name:status_name},patchType:"statusChange"},{
                        success: function (model, response, options) {
                            alert("Order status update to " + $(btn).attr("data-status-text"));
                            modalView.hide();
                        },
                        error: function(model, response) {
                            modalView.hide();
                            alert("Unable to update status");
                        },
                        wait: true,
                        patch: true
                    });
                });
            } else{
                modalView.setFooterVisibility(false);
            }
            
            
            modalView.on('onHide', function () {
                me.navigate('realtime-orders', null, {trigger: true});
            });
            modalView.show();
        }

    });

    return RealtimeOrdersController;

});
