define([

    // Libs
    'lib/Model'

], function (Model) {

    /**
     * @class AssetModel
     * @extends Model
     */
    var AssetModel = Model.extend('AssetModel', {

        idAttribute: 'id',

        schema: {
            id : {
                type: 'Hidden'
            },
            url: {
                type: 'File'
            }
        },

        initialize: function () {
        }

    });

    return AssetModel;

});
