define(['jquery'], function ($) {

	/**
	 * Singleton
	 * @class Console
	 */
	var Console = {};

	var methods = [
		'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'exception',
		'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
		'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
		'timeStamp', 'trace', 'warn',

		'logClass', 'dumpObject', 'logMethodInvoked'
	];

	methods.forEach(function (method) {
		Console[method] = function nop() {
		};
	});

	if (! (window.location.host.match(/\.local$/) || localStorage.getItem('TigerJS.debug'))) {
		return Console;
	}

	var console = (window.console = window.console || {});

	methods.forEach(function (method) {
		if (console[method]) {
			if ($.browser.webkit || ($.browser.msie && $.browser.version == 10)) {
				Console[method] = function () {
					// fix for Google Chrome, which throws TypeError: Illegal invocation if context isn't console
					console[method].apply(console, arguments);
				}
			}
			else {
				Console[method] = console[method];
			}
		}
	});

	if (! Console['exception']) {
		Console.exception = function (exception) {
			Console.log(exception);
		};
	}

	Console.logClass = function logClass(classDef) {
		var className = classDef.className || classDef.name || classDef.$name;
		Console.groupCollapsed('class ' + className);
		Console.timeEnd(className);
		Console.debug(classDef);
		Console.dir(classDef);
		Console.groupEnd();
	};

	Console.dumpObject = function dumpObject(grandObj) {
		function logObject(obj, title, recursionCounter, groupCollapsed) {
			if (++recursionCounter > 3) {
				return;
			}

			if (groupCollapsed) {
				Console.groupCollapsed(title);
			}
			else {
				Console.group(title);
			}

			if (_.isObject(obj) || _.isArray(obj)) {
				for (var prop in obj) {
					if (_.isObject(obj[prop]) || _.isArray(obj[prop])) {
						logObject(obj[prop], prop, recursionCounter, recursionCounter < 2);
					}
					else {
						Console.debug(obj[prop]);
					}
				}
			}
			else {
				Console.debug(obj);
			}

			Console.groupEnd();
		}

		logObject(grandObj, grandObj.name || grandObj.constructor.className, 0, false);
	};

	Console.logMethodInvoked = function () {
		if (!Console.logMethodInvoked) {
			Console.error('console.logMethodInvoked() failed to evaluate caller');
			return;
		}
		var className = Console.logMethodInvoked.caller.$owner.getClassName(),
			methodName = Console.logMethodInvoked.caller.$name;
		Console.debug(className + '->' + methodName + ' being invoked');
	};

	return Console;

});
