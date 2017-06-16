define([

	'backbone', 'lib/Console'

], function (Backbone, console) {

	/**
	 * @typedef {function(Object=)}
	 * @private
	 * @ignore
	 */
	var _ = window._;

	console.time('BaseClass');

	/**
	 * @class BaseClass
	 * @constructor
	 */
	function BaseClass() {
		console.logMethodInvoked();
	}

	/**
	 * @static
	 * @type {Object}
	 */
	BaseClass.self = BaseClass;

	/**
	 * @static
	 * @type {String}
	 */
	BaseClass.className = 'BaseClass';

	/**
	 * Helper method for class extending
	 * This one is a difficult method and should not be modified without senior review.
	 *
	 * @param {Object} protoProps Prototype properties and methods
	 * @param {Object} staticProps Static properties and methods
	 * @param {Object} child
	 * @return {Object} New class
	 * @ignore
	 */
	var extend = function (protoProps, staticProps, child) {
		var parent = this;

		if (! child) {
			// The constructor function for the new subclass is either defined by you
			// (the "constructor" property in your `extend` definition), or defaulted
			// by us to simply call the parent's constructor.
			if (protoProps && _.has(protoProps, 'constructor')) {
				child = protoProps.constructor;
			} else {
				child = function () {
					parent.apply(this, arguments);
				};
			}
		}

		// Add static properties to the constructor function, if supplied.
		_.extend(child, staticProps);

		// Set the prototype chain to inherit from `parent`, without calling
		// `parent`'s constructor function.
		function Surrogate() {
			this.constructor = child;
		}
		Surrogate.prototype = parent.prototype;
		child.prototype = new Surrogate;

		// Set self to refer to the current class.
		child.self = child.prototype.self = child;

		// Add prototype properties (instance properties) to the subclass, if supplied.
		if (protoProps) {
			_.extend(child.prototype, protoProps);

			if (_.has(child.prototype, 'mixins')) {
				BaseClass.mixin.call(child, child.prototype.mixins);
			}
		}

		// Set a convenience property in case the parent's prototype is needed later.
		child.__super__ = parent.prototype;

		return child;
	};

	/**
	 * Defines a class.
	 * This one is a difficult method and should not be modified without senior review.
	 *
	 * @static
	 * @param {String} className The name of the class to be created.
	 * @param {Object} classBody The key - value pairs of properties and methods to be applied to the class.
	 * @return {Object} Newly created class
	 * @inheritable
	 */
	BaseClass.define = function define(className, classBody) {
		/** @type {Object} newClass @ignore */
		var newClass, parent = BaseClass, ancestor, statics = {}, constructorCode;

		console.time(className);

		// look for extend property down the object's prototype chain
		if ('extend' in classBody) {
			parent = classBody.extend;
		}

		// if class declaration has `implement` than we must verify that upcoming class does provide
		// implementation for all specified interfaces
		if ('implement' in classBody && classBody['implement']) {
			classBody['implement'].forEach(function (interfaceObject) {
				_.each(interfaceObject.prototype, function (dummy, methodName) {
					if (! (methodName in classBody)) {
						throw new TypeError(className + ' does not implement interface member \'' + methodName +'()\'');
					}
				});
			});
		}

		if (_(classBody).has('constructor')) {
			// 1. we need to preserve scope for `classBody.constructor`, that is why we need to nest its execution into
			//    class constructor
			classBody.__constructor = classBody.constructor;
			delete classBody.constructor;

			// 2. we want our class to have a name, and that is why we have to create it using `eval`
			newClass = eval('function ' + className + ' () { this.__constructor.apply(this, arguments) };' + className);
		} else {
			newClass = eval('function ' + className + ' () { parent.apply(this, arguments) };' + className);
		}

		/** should be reversed @ignore */
		ancestor = parent;
		while ('getParent' in ancestor && ancestor.getParent()) {
			ancestor = ancestor.getParent();
			if (_(ancestor).has('inheritableStatics')) {
				_.extend(statics, ancestor.inheritableStatics);
			}
		}

		if (_(parent).has('inheritableStatics')) {
			_.extend(statics, parent.inheritableStatics);
		}

		if (_(classBody).has('inheritableStatics')) {
			newClass.inheritableStatics = classBody.inheritableStatics;
			delete classBody.inheritableStatics;
			_.extend(statics, newClass.inheritableStatics);
		}

		// methods from static have higher priority than methods from inheritableStatics
		if (_(classBody).has('statics')) {
			_.extend(statics, classBody.statics);
			delete classBody.statics;
		}

		// BaseClass static methods have the highest priority
		_.extend(statics, BaseClass);
		delete statics['$name'];
		delete statics['$owner'];

		// inherit new class from parent, extend prototype with classBody object and add statics from BaseClass
		extend.call(parent, classBody, statics, newClass);

		newClass.className = className;
		newClass.isClass = true;

		// finally mix in BaseClass
		BaseClass.mixin.call(newClass, [BaseClass]);

		console.logClass(newClass);

		return newClass;
	};

	/**
	 * @static
	 * @param {String} className
	 * @param {Object} classBody
	 * @return {Object} Newly created class
	 * @inheritable
	 */
	BaseClass.extend = function extend(className, classBody) {
		classBody.extend = this;
		return BaseClass.define.call(this, className, classBody);
	};

	/**
	 * @protected
	 * @static
	 * @param {Array.<Object>} mixins
	 * @inheritable
	 */
	BaseClass.mixin = function mixin(mixins) {
		if (! this.self) {
			console.trace();
		}

		var self = this.self,
			prototype = self.prototype,
			excludeFromBeingMixed = _.keys(prototype);

		_(mixins).forEach(function (mixin) {
			// extend prototype with mixin but don't override existing
			_.extend(prototype, _(mixin.prototype).omit(excludeFromBeingMixed));
		});
	};

	/**
	 * Looks for requested static method down the Class's inheritance chain and invoke if success,
	 * otherwise throw an error.
	 *
	 * @static
	 * @param {String} methodName The name of the static method to be called.
	 * @param {Array|Arguments=} args
	 * @return {Object} Returns the result of calling the static method
	 * @inheritable
	 */
	BaseClass.callStatic = function callStatic(methodName, args) {
		var self = this.self || this;

		do
		{
			if (methodName in self) {
				return self[methodName].apply(this, args || []);
			}
		}
		while (self = self.__super__.self);

		console.trace();
		throw new Error("callStatic() was called but there's no such method (" + methodName + ") found in the inheritance chain");
	};

	/**
	 * Factory-way of creating instance of this class.
	 *
	 * @static
	 * @return {Object}
	 * @inheritable
	 */
	BaseClass.factory = function factory() {
		return new this.self;
	};

	/**
	 * @static
	 * @return {Object} returns singleton instance of a class
	 * @inheritable
	 */
	BaseClass.getInstance = function () {
		var self = this.self;
		if (!self.instance) {
			self.instance = new self(arguments[0], arguments[1]); // dirty hack :(
		}
		return self.instance;
	};

	/**
	 * @static
	 * @return {Object} Returns the parent object
	 */
	BaseClass.getParent = function getParent() {
		return '__super__' in this ? this.__super__.constructor : undefined;
	};

	_.extend(BaseClass.prototype, /** @lends {BaseClass.prototype} @ignore */ {

		/**
		 * @protected
		 * @inheritable
		 */
		callStatic: function () {
			BaseClass.callStatic.apply(this, arguments);
		},

		/**
		 * @protected
		 * @return {Object} Returns the parent object
		 * @inheritable
		 */
		getParent: function getParent() {
			var parentClass = this.getParent.caller.$owner.constructor.__super__;

			if (! parentClass) {
				console.trace();
				throw new Error("failed to get parent class");
			}

			return parentClass;
		},

		/**
		 * Call the "parent" method of the current method. That is the method previously
		 * overridden by inheritage.
		 *
		 * @protected
		 * @param {Array=|Arguments=} args
		 * @return {Object} Returns the result of calling the parent method
		 * @inheritable
		 */
		callParent: function callParent(args) {
			var parentClass, methodName, caller = this.callParent.caller;

			// tip: anonymous function has no value in the name property
			methodName = caller.name || caller.$name;
			parentClass = caller.$owner.constructor.__super__;

			if (! parentClass) {
				console.trace();
				throw new Error("failed to get parent class");
			}

			if (methodName in parentClass) {
				console.trace();
				return parentClass[methodName].apply(this, args || []);
			}

			// check if call is being made from a constructor
			if (methodName === '__constructor') {
				return parentClass.constructor.apply(this, args || []);
			}

			console.trace();
			throw new Error("this.callParent() was called but there's no such method (" + methodName + ") found in the parent class");
		},

		/**
		 * Get the current class' name in string format.
		 *
		 * @return {String}
		 * @inheritable
		 */
		getClassName: function () {
			return this.constructor.name || this.self.className;
		},

		/**
		 * @return {self}
		 * @inheritable
		 */
		getSelf: function () {
			if (! this.getSelf.caller.$owner) {
				console.trace();
			}
			return this.getSelf.caller.$owner.self;
		},

		/**
		 * Only supports check for interface implementation
		 *
		 * @return {Boolean}
		 * @inheritable
		 */
		instanceOf: function (classOrInterface) {
			return ('implement' in this && this.implement.indexOf(classOrInterface) !== - 1);
		}
	});

	console.logClass(BaseClass);

	return BaseClass;

});
