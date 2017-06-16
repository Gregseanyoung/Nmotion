({
    name: 'app',
    out: './public/js/app/app.min.js',
    mainConfigFile: './public/js/app/config.js',
    map: {
        '*': {
            css: '../../../../../../Tiger/TigerJsBundle/Resources/public/js/components/require-css/css'
        }
    },
    paths: {
        // lib path overrides
        lib                   : 'empty:',
        backbone              : 'empty:',
        jquery                : 'empty:',
        underscore            : 'empty:',
        'jquery.addresspicker': 'empty:',
        text: '../../../../../../Tiger/TigerJsBundle/Resources/public/js/components/requirejs-text/text'
    },
    exclude: [
    ],
    include: [
        'controller/restaurant/Delete',
        'controller/restaurant/Manage',
        'controller/restaurant/Income',
        'controller/restaurant/Meal',
        'controller/restaurant/MenuCategory',
        'controller/restaurant/Profile',
        'controller/restaurant/Register',
        'controller/Config',
        'controller/Order',
        'controller/User',
    ],
    findNestedDependencies: true,
    useSourceUrl: false
})