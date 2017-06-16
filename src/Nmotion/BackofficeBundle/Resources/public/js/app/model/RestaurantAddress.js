define([

    // Libs
    'lib/Model'

], function (Model) {

    /**
     * @class RestaurantAddressModel
     * @extends Model
     */
    var RestaurantAddressModel = Model.extend('RestaurantAddressModel', {

        idAttribute: 'id',

        default: {
            id: null
        },

        schema: {
            id          : {
                type: 'Hidden'
            },
            latitude    : {
                type      : 'Text',
                validators: ['required', /\d{1,3}.\d{1,6}/]
            },
            longitude   : {
                type      : 'Text',
                editorAttrs: {
                    placeholder: 'Longitude'
                },
                validators: ['required', /\d{1,3}.\d{1,6}/]
            },
            addressLine1: {
                type      : 'Text',
                title     : 'Address line 1',
                validators: ['required']
            },
            city        : {
                type      : 'Text',
                title     : 'City',
                validators: ['required']
            },
            state       : {
                type : 'Text',
                title: 'State'
            },
            postalCode  : {
                type      : 'Text',
                title     : 'Postal code',
                validators: ['required']
            }
        },

        initialize: function () {
        }

    });

    return RestaurantAddressModel;

});
