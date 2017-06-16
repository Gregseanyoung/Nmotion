// This file extends standard built-in objects with new cool functionality

'use strict';

if (!('toUpperCaseFirst' in String.prototype)) {
	/**
	 * Make a string's first character uppercase
	 * @return {string}
	 */
	String.prototype.toUpperCaseFirst = function () {
		return this.charAt(0).toUpperCase() + this.substring(1);
	}
}

if (!('toLowerCaseFirst' in String.prototype)) {
	/**
	 * Make a string's first character lowercase
	 * @return {string}
	 */
	String.prototype.toLowerCaseFirst = function () {
		return this.charAt(0).toLowerCase() + this.substring(1);
	}
}

// @todo Add trimLeft
// @todo Add trimRight