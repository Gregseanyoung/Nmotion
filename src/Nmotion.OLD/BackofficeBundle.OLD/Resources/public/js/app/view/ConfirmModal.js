define([

    'twitter.bootstrap',

    // Libs
    'lib/view/View'

], function (TB, View) {

    /**
     * @class ConfirmModalView
     * @extends View
     */
    var ConfirmModalView = View.extend('ConfirmModalView', {

        /**
         * @protected
         */
        template: '\
            <div class="modal hide fade">\
                <div class="modal-header">\
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>\
                    <h3><%= title %></h3>\
                </div>\
                <div class="modal-body">\
                    <p><h3><%= text %></h3></p>\
                </div>\
                <div class="modal-footer">\
                    <button class="btn" data-dismiss="modal">Cancel</a>\
                    <button class="btn btn-danger" id="confirm">Confirm</a>\
                </div>\
            </div>',

        /**
         * @private
         * @type {String}
         */
        title: 'Confirmation',

        /**
         * @private
         * @type {String}
         */
        text: 'Are you sure?',

        /**
         * @protected
         */
        events: {
            /**
             * @event onConfirmBtnClick Triggered when user click's on Confirm button
             */
            'click button#confirm': function (event) {
                this.trigger('confirm');
                this.trigger('onConfirm'); // deprecated
            }
        },

        /**
         * @protected
         */
        initialize: function () {
            this.template = _.template(this.template);
            this.callParent(arguments);
            this.delegateEvents();
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

        /**
         * @protected
         */
        getModal: function () {
            var me = this;

            me.modalInstance = $(me.template({
                title: me.getTitle(),
                text : me.getText()
            }));
            me.$el.html(me.modalInstance);
            me.modalInstance.modal();
            me.modalInstance.on('hidden', function (event) {
                me.onModalClose(event, this);
            });
            me.modalInstance.on('hide', function (event) {
                me.trigger('hide');
                me.trigger('onHide'); // deprecated
            });
        },

        hide: function () {
            if (this.modalInstance) {
                this.modalInstance.modal('hide');
            } else {
                this.trigger('hide');
                this.trigger('onHide'); // deprecated
            }
        },

        /**
         * @protected
         */
        doRender: function () {
            this.callParent(arguments);
            this.getModal();
        },

        onModalClose: function (event, el) {
            this.remove();
        }

    });

    return ConfirmModalView;

});