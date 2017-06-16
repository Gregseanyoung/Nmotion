define([

    // library dependencies
    'lib/Console',
    'lib/controller/List',

    // application dependencies
    'collection/Config',
    'model/Config',
    'form/Config',
    'view/ConfigList',
    'view/ConfirmModal'

], function (

    console,
    ListController,

    ConfigCollection,
    ConfigModel,
    ConfigForm,
    ConfigListView,
    ConfirmModalView

) {

    /**
     * @class ConfigController
     * @extends ListController
     */
    var ConfigController = ListController.extend('ConfigController', {

        /**
         * @type {ConfigCollection}
         */
        collection: ConfigCollection,

        /**
         * @protected
         * @constructor
         */
        constructor: function (application) {
            this.callParent(arguments);
        },

        /**
         * @return {ConfigListView}
         */
        createListView: function () {
            var me = this, view;

            /** @type {ConfigListView} */
            view = new ConfigListView();
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
            var me = this, form, config;

            /** @type {ConfigModel} */
            config = new ConfigModel();

            /** @type {Form} */
            form = new ConfigForm({model: config});
            form.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    config.save(null, {
                        success: function (model, resp, options) {
                            me.collection.add(config);
                            Backbone.history.navigate('config', true);
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
            var me = this, formView, config;

            config = ConfigModel.findOrCreate(params.id);
            if (config === null) {
                config = new ConfigModel({id: params.id});
                config.fetch({async: false});
            }

            /** @type {Form} */
            formView = new ConfigForm({model: config});
            formView.on('onSubmit', function (form) {
                var errors = form.commit();
                if (_.isUndefined(errors)) {
                    config.save(null, {
                        success: function (model, response, options) {
                            Backbone.history.navigate('config', true);
                        },
                        error: function(model, response) {
                            form.handleServerValidationErrors(model, response);
                        },
                        wait: true
                    });
                }
            });

            me.parentView.setWidget(formView);
        },

        deleteAction: function (params) {
            var modal = new ConfirmModalView();
            modal.setTitle('Config delete confirmation');
            modal.on('onConfirm', function () {
                var config = ConfigModel.findOrCreate({id: params.id});
                config.destroy({async: false, wait: true});
                modal.hide();
            });
            modal.on('onHide', function () {
                Backbone.history.navigate('config', {trigger: true});
            });
            modal.render();
        }

    });

    return ConfigController;

});
