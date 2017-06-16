define([

	// Libs
	'./List'

], function (ListView) {

	/**
	 * @class TabsView
	 * @extends ListView
	 */
	var TabsView = ListView.extend('TabsView', {

		/**
		 * Must be defined in concrete view
		 */
		containerTemplate: '<ul class="nav nav-tabs nav-stacked"><%= items %></ul>',

		/**
		 * Must be defined in concrete view
		 */
		itemTemplate: '<li><a href="#other"><%= name %></a></li>'

	});

	return TabsView;

});
