define([

	// Libs
	'backbone', './BaseClass'

], function (Backbone, BaseClass) {

	/**
	 * @class Store
	 * @extends Backbone.Collection
	 * @mixins BaseClass
	 * @author Sergey Shupylo <sshu@ciklum.com>
	 */
	var Store = BaseClass.define('Store', /** @lends {Store.prototype} @ignore */ {

		extend: Backbone.Collection,

		/**
		 * @private
		 * @property {Number} pageSize
		 */
		pageSize: 20,

		/**
		 * @private
		 * @property {Number} currentPage
		 * The page that the Collection has most recently loaded (see {@link Store#fetchPage})
		 */
		currentPage: 1,

		/**
		 * @private
		 */
		totalCount: undefined,

		/**
		 * @property (Object) filters
		 * Filters to be sent with request to backend (see {@link Store#filterBy})
		 */
		filters: {},

		/**
		 * True to defer any sorting operation to the server. If false, sorting is done locally on the client.
		 */
		remoteFilter: true,

		/**
		 * @property (Array) sorters
		 * Sorters to be sent with request to backend (see {@link Store#sortBy})
		 */
		sorters: [],

		/**
         * @private
		 * True to defer any sorting operation to the server. If false, sorting is done locally on the client.
		 */
		remoteSort: false,

		extraParams: {},

		initialize: function() {
			// reassign to create this properties in concrete instance
			this.filters = {};
			this.remoteFilter = false;
			this.sorters = [];
			this.extraParams = {};

            // call parent method
			this.callParent(arguments);
		},

		/**
		 * @return {Number}
		 */
		getPageSize: function () {
			return this.pageSize;
		},

		/**
		 * @return {Number}
		 */
		setPageSize: function (val) {
			this.pageSize = val;
            return this;
		},

		/**
		 * @return {Number}
		 */
		getCount: function () {
			return this.length;
		},

		/**
		 * @return {Number}
		 */
		getTotalCount: function () {
			return this.totalCount;
		},

		getExtraParam: function getExtraParam(name) {
			return this.extraParams[name];
		},

		setExtraParam: function setExtraParam(name, value) {
			if (! (name in this.extraParams) || this.extraParams[name] !== value) {
				this.extraParams[name] = value;
				this.reset();
			}
			return this;
		},

		/**
		 *
		 * @protected
		 * @param {Object} resp
		 * @param {Object} xhr
		 * @return {Array.<Object>}
		 */
		parse: function (resp, xhr) {
			this.totalCount = resp.total;
			return resp.entries;
		},

		/**
		 * Returns URL for getting collection composed of desired ids.
		 * Override this method if url schema is different from default.
		 * @method
		 * @param {Array.<Number>} ids
		 * @returns {String}
		 */
		urlForMultipleIds: function (ids) {
			return _.result(this, 'url') + '/' + ids.join(';');
		},

		/**
		 * @chainable
		 * @param {String} name
		 * @param {String} value
		 * @return this
		 */
		filterBy: function (name, value) {
			if (value === null) {
				delete this.filters[name];
			} else {
				this.filters[name] = value;
			}
			return this;
		},

		/**
		 * @return this
		 */
		resetFilters: function () {
			this.filters = [];
			return this;
		},

		/**
		 * @method
		 * @returns {boolean}
		 */
		getRemoteSort: function getRemoteSort() {
			return this.remoteSort;
		},

		/**
		 * @method
		 * @returns {boolean}
		 */
		setRemoteSort: function setRemoteSort(remoteSort) {
			this.remoteSort = remoteSort;

			if (!this.remoteSort) {
				this.comparator = null;
			}

			return this;
		},

		/**
		 * @override
		 * @return this
		 */
		sortLocal: function () {
			var me = this;

			if (!me.comparator) {
				throw new Error('Cannot sort a set without a comparator');
			}

			if (_.isString(me.comparator) || me.comparator.length === 1) {
				var iterator = function (model, index, list) {
					return model.get(me.comparator);
				};
				me.models = _.sortBy(me.models, iterator, me);
			}
			else {
				me.models.sort(_.bind(me.comparator, me));
			}

			return me;
		},

		/**
		 *
		 * @param {Object=} options
		 * @returns this
		 */
		sortRemote: function (options) {
			this.fetch(options);
			return this;
		},

		/**
		 * @param {Object=} options
		 * @returns {*}
		 */
		sort: function (options) {
			var me = this;

			options = options || {};

			if (me.remoteSort) {
				return me.sortRemote(options);
			} else {
				if (!me.sorters[0]) {
					throw new Error('Cannot sort a set without any sorters defined');
				}
				me.comparator = me.sorters[0].name;
				me.sortLocal(options);
				if (me.sorters[0].order !== 'ASC') {
					me.models.reverse();
				}
				if (!options.silent) {
					me.trigger('sort', me, options);
				}
			}
		},

		/**
		 * @override
		 * @param {String} name
		 * @param {String} order
		 * @return this
		 */
		sortBy: function (name, order) {
			this.sorters[0] = {
				name : name,
				order: order.toUpperCase()
			};
			return this;
		},

		/**
		 *
		 * @param {Object=} options
		 * @return {*}
		 */
		fetch: function fetch(options) {
			var filterOptions = [], page, data;

			options = options || {};

			for (var key in this.filters) {
				filterOptions.push({
					property: key,
					value   : this.filters[key]
				});
			}

			page = options.page || this.currentPage;

			data = {
				filter: filterOptions,
				page  : page,
				start : (options.start !== undefined) ? options.start : (page - 1) * this.pageSize,
				limit : options.limit || this.pageSize
			};

			if (this.remoteSort && this.sorters[0]) {
				data['sort'] = this.sorters[0].name;
				data['order'] = this.sorters[0].order;
			}

			data = _.extend(data, this.extraParams);

			delete options.page;
			delete options.start;
			delete options.limit;

			options = _.extend(options, {data: data});

			if (!('error' in options)) {
				options.error = function (collection, response) {
					if (response.status === 404) {
						// TODO: handle 404 Not Found
					}
					else {
						var parsedResponse, notification;

						try {
							parsedResponse = JSON.parse(response.responseText);
						}
						catch (e) {
							if (e instanceof SyntaxError) {
								throw e;
							}
						}

						notification = {
							title: 'Error',
							text : '',
							type : 'error',
							hide : false,
							icon : false
						};

						if (response.status === 403) {
							notification.title = 'Forbidden';
							notification.text = parsedResponse.message;
						}
						else {
							console.debug(parsedResponse.debug);
							notification.text = parsedResponse.debug.message;
						}

						$.pnotify(notification);
					}
				}
			}

			return this.callParent([options]);
		},

		/**
		 * @method
		 * @param {Array}   ids
		 * @param {Object=} options
		 * @return {*}
		 */
		fetchMultiple: function fetchMultiple(ids, options) {
			options = options || {};
			options.url = this.urlForMultipleIds(ids);
			this.fetch(options);
		},

		/**
		 * Loads a given 'page' of data.
		 * @method
		 * @param {Number} page The number of the page to load.
		 * @param {Object} [options]
		 */
		fetchPage: function (page, options) {
			this.currentPage = page;
			this.fetch(options);
		}

	});

	return Store;

});
