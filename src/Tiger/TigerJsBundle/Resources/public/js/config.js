require.config({

    // script loading timeout
    waitSeconds: 15,

    paths: {
        lib                  : 'src',
        vendor               : 'vendor',
        components           : 'components',

        // Vendor libraries
        'backbone'           : 'components/backbone/backbone',
        //'backbone.relational': 'components/backbone-relational/backbone-relational',
        'backbone.relational': 'vendor/backbone-relational',
        'underscore'         : 'components/underscore/underscore',
        'json2'              : 'components/json2/json2',
        'jquery'             : 'components/jquery/jquery',

        // Plugins
        'backbone.forms'     : 'vendor/backbone-forms/backbone-forms',
        'bf.bootstrap'       : 'vendor/backbone-forms/templates/bootstrap',
        'bf.editor.list'     : 'vendor/backbone-forms/editors/list',
        'text'               : 'components/requirejs-text/text',

        'jquery.pnotify'     : 'components/pnotify/jquery.pnotify.min'
    },

    shim: {
        "backbone": {
            //These script dependencies should be loaded before loading backbone.js
            deps   : ['underscore', 'json2', 'jquery'],

            //Once loaded, use the global 'Backbone' as the module value.
            exports: 'Backbone'
        },

        'underscore': {
            exports: '_'
        },

        'backbone.forms': {
            deps: [
                'backbone',
                'components/jquery-iframe-transport/jquery.iframe-transport',
                'components/jquery.maskedinput/dist/jquery.maskedinput'
            ]
        },

        'bf.bootstrap': {
            deps: ['backbone.forms']
        },

        'backbone.relational': {
            deps: ['backbone']
        },

        'underscore.extendFn': {
            deps: ['underscore']
        },

        'jquery.pnotify': {
            deps: ['jquery', 'css!components/pnotify/jquery.pnotify.default.css']
        }
    },

    map: {
        '*': {
            'backbone-forms': 'backbone.forms',
            'css': 'components/require-css/css'
        }
    }

});
