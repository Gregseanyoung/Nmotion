define([

    // library dependencies
    'lib/controller/List',

    // application dependencies
    'collection/Currency',
    'model/Currency',
    'form/Currency',
    'view/CurrencyList'

], function (

    ListController,

    CurrencyCollection,
    CurrencyModel,
    CurrencyForm,
    CurrencyListView

) {

    /**
     * @class CurrencyController
     * @extends ListController
     */
    var CurrencyController = ListController.extend('CurrencyController', {

        /**
         * @type {ConfigCollection}
         */
        collection: CurrencyCollection,

        /**
         * @protected
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
        },

        /**
         * @return {CurrencyListView}
         */
        createListView: function () {
            var me = this, view;

            /** @type {ConfigListView} */
            view = new CurrencyListView();
            view.setCollection(me.collection);

            me.parentView.setWidget(view);

            return view;
        },

        indexAction: function (params) {
            var me = this, view;
            
            view = me.createListView();

            me.off('onCollectionSync').on('onCollectionSync', function () {
                console.debug('onCollectionSync event is being fired', arguments);
                view.render();
            });

            me.off('onCollectionChange').on('onCollectionChange', function () {
                console.debug('onCollectionChange event is being fired', arguments);
                view.render();
            });

            me.off('onCollectionReset').on('onCollectionReset', function () {
                console.debug('onCollectionReset event is being fired', arguments);
                view.render();
            });

            if (me.collection.isEmpty()) {
                me.getData();
            }
        },

        newAction: function (params) {
            var me = this, form, currency;

            /** @type {CurrencyModel} */
            currency = new CurrencyModel();

            /** @type {Form} */
            form = new CurrencyForm({model: currency});
            form.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    currency.save(null, {
                        success: function (model, resp, options) {
                            me.collection.add(currency);
                            Backbone.history.navigate('currency', true);
                        },
                        error: function(model, response) {
                            form.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });

            me.parentView.setWidget(form);
        },

        editAction: function (params) {
            var me = this, formView, currency;

            currency = CurrencyModel.findOrCreate(params.id);
            if (currency === null) {
                currency = new CurrencyModel({id: params.id});
                currency.fetch({async: false});
            }

            /** @type {Form} */
            formView = new CurrencyForm({model: currency});
            formView.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    currency.save(null, {
                        success: function (model, response, options) {
                            Backbone.history.navigate('currency', true);
                        },
                        error: function(model, response) {
                            form.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });

            me.parentView.setWidget(formView);
        }

    });

    return CurrencyController;

});
