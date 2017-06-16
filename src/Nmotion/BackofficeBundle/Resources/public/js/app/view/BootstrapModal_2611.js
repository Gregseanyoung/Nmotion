define(['underscore', 'backbone', ],
        function(_, Backbone) {
            var BootstrapModalView = Backbone.View.extend({
                id: 'bootstrap-modal',
                className: 'modal fade hide',
                template: '<div class="modal-dialog">'
                        + '<div class="modal-content">'
                        + '<% if (headerVisibility) { %>'
                        + '<div class="modal-header">'
                        + '<% if (closeCrossButtonVisibility) { %><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><% } %>'
                        + '<h4 class="modal-title"><%= title %></h4>'
                        + '</div>'
                        + '<% } %>'
                        + '<% if (text !== "") { %><div class="modal-body"><%= text %></div><% } %>'
                        + '<% if (footerVisibility) { %>'
                        + '<div class="modal-footer">'
                        + '<%= footerContent %>'
                        + '<% if (closeButtonVisibility) { %><a href="#" class="btn" data-dismiss="modal">Close</a><% } %>'
                        + '</div>'
                        + '<% } %>'
                        + '</div><!-- /.modal-content -->'
                        + '</div><!-- /.modal-dialog -->',
                closeCrossButtonVisibility: true,
                closeButtonVisibility: true,
                headerVisibility: true,
                footerVisibility: true,
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
                    'click button': function(e) {
                        var btn = e.target;
                        if ($(btn).attr('data-click-function')) {
                            this.trigger($(btn).attr('data-click-function'), btn);
                        }
                    },
                    'click .modal-body': function() {
                        this.trigger("onModalBodyClick");
                    }
                },
                show: function() {
                    var str = getUrlVars();
                   
                    //  alert(str);
                    var res = str[0].split("/");
                    var orderId = res[(res.length) - 1];
                    
                    function getUrlVars()
                    {
                        var vars = [], hash;
                        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                        for (var i = 0; i < hashes.length; i++)
                        {
                            hash = hashes[i].split('=');

                            vars.push(hash[0]);
                            vars[hash[0]] = hash[1];

                            // vars[hash[0]] = hash[1];
                        }
                        return vars;
                    }
                    var myhtml = "<div class='panel' style='border:none;box-shadow:none;'><table style='width:600px' class='table' id='ajax_tbl'>";
                    var myhtml1 = "<div class='panel'><div class='panel-heading'>Meal Options</div><table style='width:600px' class='table' id='ajax_tbl_op'>";
                    var myhtml2 = "<div class='panel'><div class='panel-heading'>Comments</div><table style='width:600px' class='table' id='ajax_tbl_cmt'>";
                    var htm="";var htm1="";var htm2="";
                    
                                   
                    var responseData = $.ajax({
                        type: 'POST',
                        url: "../getOrderMealExtraIngredients.php",
                       // contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        data:{orderId:orderId},
                        async:false
                    }).responseText;
                   
                    var responseData = $.parseJSON(responseData);
                    var mealComments = "";
                    var mealOption="";
                    if (responseData.length > 0) {
                        htm += "<thead><tr><th>name</th><th>price</th><th>quantity</th></tr></thead>";
                       var flagmealop=0;
                        $.each(responseData, function(i, e) {
                            var sign = e.currency_sign;
                           
                            if(e.meal_option_name!=''){
                                flagmealop++;
                            }
                            if(flagmealop > 0){
                                  htm1 += "<thead><tr><th>name</th><th>price</th></tr></thead>";
                            }
                            if(e.meal_comment!='' && mealComments==''){
                                mealComments +=e.meal_comment + " ";
                            }else{
                                mealComments = "No comments";
                            }
                            if( e.currency_sign==null || e.currency_sign=='')
                                sign ="";
                            htm += "<tr><td>" + e.name + "</td><td>"+ sign+" "+ Number(e.price).toFixed(2) + "</td><td>"+e.quantity+"</td></tr>";
                            $.each(e.extra, function(ekey, eVal) {
                              htm += "<tr><td style='padding-left:25px;'>" + eVal.name + "</td><td>"+ sign+" "+ Number(eVal.price).toFixed(2) + "</td></tr>";  
                            });
                            
                            if(mealOption != e.meal_option_name){
                                 htm1 += "<tr><td>" + e.meal_option_name + "</td><td>"+ sign+" "+ Number(e.meal_option_price).toFixed(2) + "</td></tr>";
                            }
                            mealOption = e.meal_option_name;
                            
                           
                            htm2 = "<tr><td colspan='2' style='border-top: medium none;'>" + mealComments + "</td></tr>";
                        });
                        
                        if(flagmealop == 0){
                            htm1 = "No meal optons";
                        }

                    } else {
                        htm += "No extra ingredients";
                        htm1 += "No meal optons";
                        htm2 += "No comments";
                    }
                 
                     myhtml += htm+"</table></div>";
                     myhtml1 += htm1+"</table></div>";
                     myhtml2 += htm2+"</table></div>";
                     
                    var modelText = this.getText();

                    var newModelText = modelText.replace("<!-- #MealExtraIngredients #-->", myhtml);
                    newModelText = newModelText.replace("<!-- #MealOptions #-->", myhtml1);
                    newModelText = newModelText.replace("<!-- #MealComments #-->", myhtml2);
                    
                    var me = this;
                    me.modalInstance = $(me.template({
                        title: this.getTitle(),
                        text: newModelText,
                        closeCrossButtonVisibility: me.getCloseCrossButtonVisibility(),
                        closeButtonVisibility: me.getCloseButtonVisibility(),
                        headerVisibility: me.getHeaderVisibility(),
                        footerVisibility: me.getFooterVisibility(),
                        footerContent: me.getFooterContent()
                    }));

                    me.$el.html(me.modalInstance);
                    me.$el.modal();
                    me.$el.on('hide', function(event) {
                        me.trigger('hide');
                        me.trigger('onHide'); // deprecated
                    });
                },
                hide: function() {
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
                getTitle: function() {
                    return this.title;
                },
                /**
                 * @public
                 * @param title
                 */
                setTitle: function(title) {
                    this.title = title;
                },
                /**
                 * @public
                 * @return {string}
                 */
                getText: function() {
                    return this.text;
                },
                /**
                 * @public
                 * @param text
                 */
                setText: function(text) {
                    this.text = text;
                },
                setCloseCrossButtonVisibility: function(flag) {
                    this.closeCrossButtonVisibility = flag;
                },
                getCloseCrossButtonVisibility: function() {
                    return this.closeCrossButtonVisibility;
                },
                setCloseButtonVisibility: function(flag) {
                    this.closeButtonVisibility = flag;
                },
                getCloseButtonVisibility: function() {
                    return this.closeButtonVisibility;
                },
                setHeaderVisibility: function(flag) {
                    this.headerVisibility = flag;
                },
                getHeaderVisibility: function() {
                    return this.headerVisibility;
                },
                setFooterVisibility: function(flag) {
                    this.footerVisibility = flag;
                },
                getFooterVisibility: function() {
                    return this.footerVisibility;
                },
                setFooterContent: function(content) {
                    this.footerContent = content;
                },
                getFooterContent: function() {
                    return this.footerContent;
                }
            });

            return BootstrapModalView;
        });
