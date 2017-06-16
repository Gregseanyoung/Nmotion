require(['./config'], function () {

    function dirname(path) {
        return path.replace(/\\/g, '/').replace(/\/[^\/]*$/, '');
    }

    var frameworkRoot = dirname(require.toUrl('.')),
        frameworkPaths = {
            lib   : frameworkRoot + '/src',
            vendor: frameworkRoot + '/vendor',
            components: frameworkRoot + '/components'
        };

    var regExp,
        config = require.s.contexts._.config,
        paths = config.paths,
        packages = config.pkgs;

    for (var frameworkPathDef in frameworkPaths) {
        regExp = new RegExp('^' + frameworkPathDef);

        // paths is an object, and so is passed by reference
        for (var path in paths) {
            if (paths[path].match(regExp)) {
                paths[path] = paths[path].replace(regExp, frameworkPaths[frameworkPathDef]);
            }
        }

        // pkgs is an object, and so is passed by reference
        for (var packageName in packages) {
            if (packages[packageName].location.match(regExp)) {
                packages[packageName].location = packages[packageName].location.replace(regExp, frameworkPaths[frameworkPathDef]);
            }
        }
    }

    require({paths: frameworkPaths});

    // foolish the compiler because of bug
    var mainCss = 'css!../css/main.css';

    require([

        './ext/underscore.extendFn',
        './ext/ecma.ext',
        './ext/ecma.fixes',

        './components/date.format/date.format',

        mainCss

    ], function () {

        require['config']({
            baseUrl: dirname(require.toUrl('app'))
        });

        // trick the compiler to not parse app bootstrap
        var app = ['app'];
        require(app);

    });

});