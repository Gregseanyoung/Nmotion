require([

	// Libs
	'underscore'

], function (underscore) {

	/**
	 * @typedef {function(Object=)}
	 * @private
	 */
	var _ = window._;

	// Extend a given object with all the properties in passed-in object(s).
	_.extend = function (obj) {
		_.each(_.rest(arguments), function (source) {
			for (var prop in source) {
                if (prop == 'prototype') {
                    continue;
                }
				obj[prop] = source[prop];
				if (_.isFunction(obj[prop]) && !obj[prop].isClass) {
					obj[prop].$owner = obj;
					obj[prop].$name = prop;
				}
			}
		});
		return obj;
	};

});
