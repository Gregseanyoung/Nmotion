require.config({

    paths: {
        'css.path'                 : '../../css',
        'vendor.path'              : '../vendor',
        'components.path'          : '../components',

        'twitter.bootstrap'        : '../components/bootstrap/docs/assets/js/bootstrap.min',
        'jquery-ui'                : '../vendor/jquery-ui-1.10.3.custom.min',
        'jquery.pagination'        : '../vendor/jquery.pagination',
        'jquery.toggleButtons'     : '../vendor/jquery/bootstrap-toggle-buttons/jquery.toggle.buttons',
        'jquery.addresspicker'     : '../vendor/jquery/address-picker/jquery.addresspicker',
        'bootstrap.datepicker'     : '../components/bootstrap-datepicker/js/bootstrap-datepicker',
        'async'                    : '../components/requirejs-plugins/src/async'
    },

    shim : {
        'twitter.bootstrap'   : [
            'jquery',
            'css!components.path/bootstrap/docs/assets/css/bootstrap.css',
            'css!css.path/bootstrap-ext.css'
        ],
        'jquery.toggleButtons': [
            'jquery',
            'css!vendor.path/jquery/bootstrap-toggle-buttons/bootstrap-toggle-buttons.css'
        ],
        'jquery.addresspicker': [
            'jquery',
            'vendor.path/jquery/address-picker/bootstrap-typeahead',
            'async!http://maps.googleapis.com/maps/api/js?sensor=false&language=en'
        ],
        'bootstrap.datepicker': [
            'jquery',
            'twitter.bootstrap',
            'css!components.path/bootstrap-datepicker/css/datepicker.css'
        ]
    },

    deps : ['twitter.bootstrap']

});