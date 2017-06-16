define([

	// Libs
	'./List'

], function (ListView) {

	/**
	 * @class GridView
	 * @extends ListView
	 */
	var GridView = ListView.extend('GridView', {

		/**
		 * @event sortChange
		 * Triggered when user has changed sort direction on a column.
		 * @param {String} keyName column keyName
		 * @param {String} order new sort order (ASC or DESC)
		 */

		/**
		 * @property {Array.<Object>} columns Allows various configurations to be set for each column and field in the grid.
		 * @property {String} columns.title Column title.
		 * @property {String} columns.keyName Name of the field in a model to get data from.
		 * @property {Number} columns.width Column width.
		 * @property {Boolean} columns.sortable Enables sorting on the column.
		 * @property {Function} columns.headerRenderer Callback function which is used to render column header.
		 * @property {Function} columns.valueRenderer Callback function which is used to render cell data.
		 *
		 * @type {Array.<Object>}
		 */
		columns: [],

		containerTemplate: '\
			<table cellpadding="0" cellspacing="0" border="0" class="<%= className %>">\
				<%= header %>\
				<tbody>\
					<%= items %>\
				</tbody>\
			</table>\
		',

		containerClass: '',

		itemTemplate: '<td><%= value %></td>',

		events: {
			'click th.sorting,th.sorting_asc,th.sorting_desc': function (event) {
				var th = event.currentTarget,
					keyName = th.getAttribute('data-key-name'),
					order = 'ASC';

				if (th.className === 'sorting_asc') {
					order = 'DESC';
				}

				th.className = 'sorting_' + order.toLowerCase();

				this.trigger('sortChange', keyName, order);
			}
		},

		/**
		 * @protected
		 * @param {Object} item
		 * @param {Object} column
		 * @return {*}
		 */
		getItemValue: function (item, column) {
			var value = column.keyName && item.has(column.keyName) ? item.get(column.keyName) : null;

			if (column.valueRenderer) {
				if (_.isFunction(column.valueRenderer)) {
					value = column.valueRenderer.call(this, value, item);
				} else if (value !== null) {
					switch (column.valueRenderer) {
						case 'link':
							value = value.link(value);
						break;

						case 'email':
							value = value.link('mailto:' + value);
						break;

						case 'date':
							value = new Date(value * 1000).format('longDate');
						break;
					}
				}
			}

			return value;
		},

		/**
		 * @protected
		 * @return {string}
		 */
		renderHeader: function () {
			var me = this,
				columns = me.columns,
				row = $('<tr></tr>'),
				sortKeyName = null,
				sortOrder = null;

			if (me.collection.sorters[0] && me.collection.sorters[0].name) {
				sortKeyName = me.collection.sorters[0].name;
				sortOrder = me.collection.sorters[0].order;
			}

			columns.forEach(function (column) {
				var td = $('<th></th>'),
					title = column.title;

				if (_.isFunction(column.headerRenderer)) {
					title = column.headerRenderer(title);
				}

				td.html(title);

				if (column.keyName) {
					td.attr('data-key-name', column.keyName);
				}

				if (column.width) {
					td.attr('width', column.width);
				}

				if (column.style) {
					if (_.isObject(column.style)) {
						_.forEach(column.style, function (ruleValue, ruleName) {
							td.css(ruleName, ruleValue);
						});
					}
					else {
						td.attr('style', column.style);
					}
				}

				if (column.sortable) {
					if (sortKeyName === column.keyName) {
						td.addClass('sorting_' + sortOrder.toLowerCase());
					} else {
						td.addClass('sorting');
					}
				}

				row.append(td);
			});

			return '<thead>' + row[0].outerHTML + '</thead>';
		},

		/**
		 * @protected
		 * @param items
		 */
		renderContainer: function (items) {
			var me = this;
			return me.containerTemplate({
				header   : me.renderHeader(),
				items    : items,
				className: me.containerClass
			});
		},

		/**
		 * @method
		 * @protected
		 * @param {Model} item
		 */
		renderItem: function (item) {
			var me = this, columns = me.columns, row = $('<tr></tr>');

			row.attr('data-id', item.getId());
			row.attr('data-cid', item.cid);

			columns.forEach(function (column) {
				var td = $('<td></td>');
				td.html(me.getItemValue(item, column));
				row.append(td);
			});

			return row[0].outerHTML;
		},

		/**
		 * @method
		 * @param {Store} collection
		 */
		renderList: function (collection) {
			var me = this;

			collection = collection || me.collection;

			if (!me.columns.length && collection.length) {
				_(collection.first().toJSON()).each(function(value, key, item) {
					me.columns.push({
						title  : key,
						keyName: key
					});
				});
			}

			me.callParent(arguments);
		}

	});

	return GridView;

});
