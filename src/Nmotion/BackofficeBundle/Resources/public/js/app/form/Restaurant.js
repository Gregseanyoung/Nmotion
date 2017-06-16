define([

    // Libs
    'lib/view/form/Symfony',
    'lib/view/form/NestedForm',
    'lib/view/form/InlineNestedModel',

    // App
    'form/AssetFile',
    'form/ToggleButton',
    'form/User',
    'model/Asset',
    'model/Restaurant',
    'model/RestaurantAddress',
    'model/Currency',
    'model/RestaurantOperationTime',
    'model/User',

    'collection/Currency',
    
    // Used plugins
    'jquery.addresspicker'

], function (

    SymfonyForm,
    NestedForm,
    InlineNestedModel,

    AssetFile,
    ToggleButton,
    UserForm,
    AssetModel,
    RestaurantModel,
    RestaurantAddressModel,
    CurrencyModel,
    RestaurantOperationTimeModel,
    UserModel,

    CurrencyCollection

) {

    var app = require('Nmotion').getInstance();

    Backbone.Form.setTemplates({
        field      : '\
            <div class="control-group field-{{key}}" style="margin: 0">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    {{editor}}\
                    <div class="help-inline">{{error}}</div>\
                    <div class="help-block">{{help}}</div>\
                </div>\
            </div>\
        ',
        nestedField: '\
            <div class="control-group field-{{key}}" style="margin: 0">\
                <label class="control-label" for="{{id}}" style="text-align: left">{{title}}</label>\
                <div class="controls">\
                    {{editor}}\
                    <div class="help-inline">{{error}}</div>\
                    <div class="help-block">{{help}}</div>\
                </div>\
            </div>\
        ',
        list: '\
          <div class="bbf-list">\
            <ul class="unstyled clearfix">{{items}}</ul>\
          </div>\
        ',
        listItem: '\
          <li class="clearfix">\
            <label class="control-label" style="text-align: left"></label>\
            <div style="margin-left: 180px">{{editor}}</div>\
          </li>\
        '
    });

    /**
     * @class RestaurantForm
     * @extends SymfonyForm
     */
    var RestaurantForm = SymfonyForm.extend('RestaurantForm', {

        /**
         * @type {RestaurantModel}
         */
        model: RestaurantModel,

        schema: {
            id                : {
                type: 'Hidden'
            },
            name              : {
                type      : 'Text',
                title     : 'Restaurant Name',
                validators: ['required']
            },
            fullDescription   : {
                type      : 'TextArea',
                validators: ['required']
            },
            logoAsset         : {
                type            : 'AssetFile',
                action          : '/upload/file.json',
                title           : 'Logo',
                help            : 'Image dimension recommended to be within 160px x 90px',
                allowedMimeTypes: []
            },
            facebookPlaceId   : {
                type: 'Text'
            },
            feedbackUrl       : {
                type      : 'Text',
                title     : 'Feedback URL',
                validators: ['url']
            },
            videoUrl          : {
                type      : 'Text',
                title     : 'Video URL',
                validators: ['url']
            },
            phone             : {
                type      : 'Text',
                validators: ['required']
            },
            email             : {
                type      : 'Text',
                title     : 'Email address',
                dataType  : 'email',
                validators: ['required', 'email']
            },
            siteUrl           : {
                type      : 'Text',
                title     : 'Site URL',
                validators: ['url']
            },
            contactPersonName : {
                type: 'Text'
            },
            contactPersonEmail: {
                type      : 'Text',
                dataType  : 'email',
                validators: ['email']
            },
            contactPersonPhone: {
                type: 'Text'
            },
            legalEntity       : {
                type: 'Text'
            },
            checkOutTime      : {
                type      : 'Number',
                validators: ['required']
            },
            invoicingPeriod   : {
                type      : 'Select',
                options   : ['monthly', 'weekly', '14 days'],
                fieldAttrs: {style: 'margin: 0 0 20px'},
                validators: ['required']
            },
            currency   : {
                type      : 'Select',
                options   : CurrencyCollection.getInstance(),
                fieldAttrs: {style: 'margin: 0 0 20px'},
                validators: ['required']
            },
            vatNo             : {
                type      : 'Text',
                validators: ['required']
            },
            visible           : {
                type      : 'ToggleButton',
                fieldAttrs: {style: 'margin: 0 0 15px'}
            },
            inHouse          : {
                type      : 'ToggleButton',
                fieldAttrs: {style: 'margin: 0 0 15px'}
            },
            takeaway          : {
                type      : 'ToggleButton',
                fieldAttrs: {style: 'margin: 0 0 15px'}
            },
            roomService       : {
                type      : 'ToggleButton',
                fieldAttrs: {style: 'margin: 0 0 15px'}
            },
            taMember          : {
                type : 'ToggleButton',
                title: 'TA member'
            },
            regNo             : {
                type      : 'Text',
                validators: ['required']
            },
            kontoNo           : {
                type      : 'Text',
                validators: ['required']
            },
            address           : {
                type      : 'NestedModel',
                title     : false,
                model     : RestaurantAddressModel,
                fieldClass: 'nested-form'
            },
            adminUser         : {
                type      : 'NestedForm',
                title     : false,
                form      : UserForm,
                fieldClass: 'nested-form'
            },
            operationTimes    : {
                type      : 'List',
                title     : false,
                itemType  : 'InlineNestedModel',
                model     : RestaurantOperationTimeModel,
                validators: ['required'],
                fieldClass: 'nested-form',
                sealed    : true
            }
        },

        fieldsets: [
            {
                legend: 'Restaurant info',
                fields: [
                    'name',
                    'fullDescription',
                    'logoAsset',
                    'facebookPlaceId',
                    'feedbackUrl',
                    'videoUrl',
                    'phone',
                    'email',
                    'siteUrl',
                    'contactPersonName',
                    'contactPersonEmail',
                    'contactPersonPhone',
                    'legalEntity',
                    'checkOutTime',
                    'invoicingPeriod',
                    'currency',
                    'vatNo',
                    'regNo',
                    'kontoNo',
                    'visible',
                    'taMember'
                ]
            },
            {
                legend: 'Restaurant services',
                fields: [
                    'inHouse',
                    'takeaway',
                    'roomService',
                ]
            },
            {
                legend: 'Restaurant address',
                fields: ['address']
            },
            {
                legend: 'Restaurant operation time',
                fields: ['operationTimes']
            },
            {
                legend: 'Restaurant admin',
                fields: ['adminUser']
            }
        ],

        formActionsTemplate: '<div class="form-actions form-horizontal">\
                <button type="submit" class="btn btn-primary">Save</button>\
                <button type="button" class="btn" id="cancel">Cancel</button>\
                </div>',

        readOnlyFieldsForRadmin: [
            'legalEntity',
            'invoicingPeriod',
            'vatNo',
            'currency',
            'regNo',
            'kontoNo',
            'visible',
            'taMember',
            'adminUser'
        ],

        initialize: function () {
            var me = this;
            if (app.getUser().isRestaurantAdmin()) {
                me.readOnlyFieldsForRadmin.forEach(function (fieldName) {
                    if (! ('editorAttrs' in me.schema[fieldName])) {
                        me.schema[fieldName].editorAttrs = {};
                    }
                    me.schema[fieldName].editorAttrs.readonly = 'readonly';
                })
            }
            this.callParent(arguments);
        },

        setupAddressForm: function () {
            var me = this, $el,
                $fieldset = $('fieldset:has([name=address])'),
                addressFormContainer = $fieldset.find('div.field-address'),
                manualAddressForm = addressFormContainer.children(1),
                getManualAddressInput,
                mapAddressForm, mapAddressPicker,
                setGMapsAddressFromManualAddress;

            getManualAddressInput = function (name) {
                return manualAddressForm.find('input[name="' + name + '"]');
            };

            mapAddressForm = $('\
                <div class="control-group" style="display: none">\
                    <label style="text-align: left" for="addressPicker" class="control-label">Address</label>\
                    <div class="controls">\
                        <input id="addressPicker" type="text" class="span0"/>\
                        <div style="height:370px;margin:20px 0 0 0" class="span0">\
                            <div id="mapCanvas" style="width:100%; height:100%"></div>\
                            <div id="location" class=""></div>\
                        </div>\
                    </div>\
                </div>\
            ');
            addressFormContainer.append(mapAddressForm);

            mapAddressPicker = mapAddressForm.find('#addressPicker').addresspicker(
                {
                    regionBias    : 'de',
                    map           : mapAddressForm.find('#mapCanvas'),
                    typeaheaddelay: 1000,
                    mapOptions    : {
                        zoom  : 16,
                        center: new google.maps.LatLng(55.67654, 12.5683)
                    }
                }
            );

            setGMapsAddressFromManualAddress = function () {
                /** @var {AddressPicker} addressPicker */
                var addressPicker = mapAddressPicker.data('addresspicker'),
                    addressComponents = [],
                    geocoder = addressPicker.geocoder,
                    geoCoderRequest = {region: 'de'};

                if (getManualAddressInput('latitude').val() && getManualAddressInput('longitude').val()) {
                    geoCoderRequest.latLng = new google.maps.LatLng(
                        getManualAddressInput('latitude').val(),
                        getManualAddressInput('longitude').val()
                    );
                } else {
                    ['addressLine1', 'postalCode', 'city'].forEach(function(name) {
                        if (getManualAddressInput(name).val()) {
                            addressComponents.push(getManualAddressInput(name).val());
                        }
                    });

                    geoCoderRequest.address = addressComponents.join(', ') + " ";
                }

                geocoder.geocode(geoCoderRequest, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var pickedAddress = results[0];
                        mapAddressPicker.trigger('addressChanged', pickedAddress);
                        mapAddressForm.find('#addressPicker').val(pickedAddress.formatted_address);
                    }
                });
            };

            mapAddressPicker.on("addressChanged", function(evt, address) {
                var lat = address.geometry.location.lat().toFixed(6),
                    lng = address.geometry.location.lng().toFixed(6),
                    addressLine = [];

                getManualAddressInput('latitude').val(lat);
                getManualAddressInput('longitude').val(lng);

                address.address_components.forEach(function (cmp) {
                    // "street_number"  route
                    if (cmp.types.indexOf('street_number') !== -1 || cmp.types.indexOf('route') !== -1) {
                        addressLine.push(cmp.long_name);
                    }
                    if (cmp.types.indexOf('locality') !== -1) {
                        getManualAddressInput('city').val(cmp.long_name);
                    }
                    if (cmp.types.indexOf('administrative_area_level_1') !== -1) {
                        getManualAddressInput('state').val(cmp.long_name);
                    }
                    if (cmp.types.indexOf('postal_code') !== -1) {
                        getManualAddressInput('postalCode').val(cmp.long_name);
                    }
                });
                getManualAddressInput('addressLine1').val(addressLine.reverse().join(', '));

            });

            mapAddressPicker.on("positionChanged", function (evt, markerPosition) {
                markerPosition.getAddress(function (address) {
                    //address is a Google Geocoder result
                    if (address) {
                        mapAddressForm.find('#addressPicker').val(address.formatted_address);
                    }
                })
            });

            $el = $('<div class="pull-right" style="margin-top: 5px;"><input type="checkbox" checked="checked"></div>');

            $fieldset.find('legend').append($el);

            $el.toggleButtons({
                font : {
                    'font-size': '14px'
                },
                label: {
                    enabled : 'Manual',
                    disabled: 'Map'
                },
                style: {
                    enabled : "info",
                    disabled: "success"
                },
                width: 150,
                transitionspeed: 0.25,
                onChange: function ($el, isManual, e) {
                    if (isManual) {
                        mapAddressForm.fadeOut(function () {
                            manualAddressForm.fadeIn();
                        });
                    } else {
                        manualAddressForm.fadeOut(function () {
                            mapAddressForm.fadeIn(function () {
                                mapAddressPicker.addresspicker('reloadPosition');
                            });
                            setGMapsAddressFromManualAddress();
                            mapAddressPicker.addresspicker('updatePosition');
                        });
                    }
                }
            });
        },

        afterRender: function () {
            this.$el.find('input,textarea').attr('class', 'span0');
            this.$el.width(600);
            
            this.callParent(arguments);
            
            this.on('logoAsset:onUploadFile', function (form, editor, response) {
                var asset = AssetModel.findOrCreate(response.entries[0]);
                editor.setValue(asset);
            });

            var dayTitles = $('[name=operationTimes] li>label', this.el);
            $('[name=operationTimes] fieldset', this.el).each(function (index, el) {
                var dayOfTheWeek;
                switch (++index) {
                    case 1: dayOfTheWeek = 'Monday'; break;
                    case 2: dayOfTheWeek = 'Tuesday'; break;
                    case 3: dayOfTheWeek = 'Wednesday'; break;
                    case 4: dayOfTheWeek = 'Thursday'; break;
                    case 5: dayOfTheWeek = 'Friday'; break;
                    case 6: dayOfTheWeek = 'Saturday'; break;
                    case 7: dayOfTheWeek = 'Sunday'; break;
                }
                dayTitles[index-1].innerHTML = dayOfTheWeek;
            });
            $('[name=operationTimes] input', this.el).width(50);

            this.setupAddressForm();
            
            // to set restaurant currency selected in dropdown
            var restaurantCurrency = this.model.get("currency");
            if(restaurantCurrency){
                setTimeout(function(){
                    $("[name=currency]").val(restaurantCurrency.id)
                },1000);
            }
        }

    });

    return RestaurantForm;

});
