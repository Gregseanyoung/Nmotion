define(['underscore', 'backbone', ], 
function(_, Backbone) {
  var BootstrapModalView = Backbone.View.extend({
        
        id: 'bootstrap-modal',
        
        className: 'modal fade hide',
        
        template: '<div class="modal-dialog">'
                    +'<div class="modal-content">'
                        +'<% if (headerVisibility) { %>'
                            +'<div class="modal-header">'
                                +'<% if (closeCrossButtonVisibility) { %><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><% } %>'
                                +'<h4 class="modal-title"><%= title %></h4>'
                            +'</div>'
                        +'<% } %>'
                        +'<% if (text !== "") { %><div class="modal-body"><%= text %></div><% } %>'
                        +'<% if (footerVisibility) { %>'
                            +'<div class="modal-footer">'
                                +'<%= footerContent %>'
                                +'<% if (closeButtonVisibility) { %><a href="#" class="btn" data-dismiss="modal">Close</a><% } %>'
                            +'</div>'
                        +'<% } %>'
                    +'</div><!-- /.modal-content -->'
                    +'</div><!-- /.modal-dialog -->',
            
        closeCrossButtonVisibility: true,
        closeButtonVisibility: true,
        headerVisibility:true,
        footerVisibility:true,
        
        events: {
          'hidden': 'teardown'
        },
        
        /**
         * @private
         * @type {String}
         */
        title: 'Title',

        /**
         * @private
         * @type {String}
         */
        text: 'Modal content',
        
        footerContent: '',
        
        initialize: function() {
            this.template = _.template(this.template);
            
            _(this).bindAll();
        },
        
        events:{
            'click button' :  function(e){
               var btn = e.target;
               if($(btn).attr('data-click-function')){
                   this.trigger($(btn).attr('data-click-function'),btn);
               }
            }, 
            'click .modal-body' :  function(){
               this.trigger("onModalBodyClick");
            }
        },
            
        show: function() {
            var me = this;
            me.modalInstance = $(me.template({
                title: this.getTitle(),
                text : this.getText(),
                closeCrossButtonVisibility: me.getCloseCrossButtonVisibility(),
                closeButtonVisibility: me.getCloseButtonVisibility(),
                headerVisibility: me.getHeaderVisibility(),
                footerVisibility: me.getFooterVisibility(),
                footerContent: me.getFooterContent()
            }));
                 
            me.$el.html(me.modalInstance);
            me.$el.modal();
            me.$el.on('hide', function (event) {
                me.trigger('hide');
                me.trigger('onHide'); // deprecated
            });
        },
                
        hide: function () {
            if (this.$el) {
                this.$el.modal('hide');
            } else {
                this.trigger('hide');
                this.trigger('onHide'); // deprecated
            }
        },
 
        teardown: function() {
          this.$el.data('modal', null);
          this.remove();
        },
        
        /**
         * @public
         * @return {string}
         */
        getTitle: function () {
            return this.title;
        },

        /**
         * @public
         * @param title
         */
        setTitle: function (title) {
            this.title = title;
        },

        /**
         * @public
         * @return {string}
         */
        getText: function () {
            return this.text;
        },

        /**
         * @public
         * @param text
         */
        setText: function (text) {
            this.text = text;
        },
        
        setCloseCrossButtonVisibility: function(flag){
            this.closeCrossButtonVisibility = flag;
        },
                
        getCloseCrossButtonVisibility : function(){
            return this.closeCrossButtonVisibility;
        },
                
        setCloseButtonVisibility: function(flag){
            this.closeButtonVisibility = flag;
        },
                
        getCloseButtonVisibility : function(){
            return this.closeButtonVisibility;
        },
                
        setHeaderVisibility: function(flag){
            this.headerVisibility = flag;
        },
                
        getHeaderVisibility : function(){
            return this.headerVisibility;
        },
        
        setFooterVisibility: function(flag){
            this.footerVisibility = flag;
        },
                
        getFooterVisibility : function(){
            return this.footerVisibility;
        },
                
        setFooterContent: function(content){
            this.footerContent = content;
        },
                
        getFooterContent : function(){
            return this.footerContent;
        }
     });
     
  return BootstrapModalView;
});