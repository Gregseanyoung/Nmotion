({
	mainConfigFile: './public/js/config.js',
	name: 'bootstrap',
	out: './public/js/lib.js',
	paths: {
		backbone  : 'components/backbone/backbone-min',
		jquery    : 'components/jquery/jquery.min',
		underscore: 'components/underscore/underscore-min',
		json2     : 'components/json2/json2'
	},
	exclude: [
		'css!components/pnotify/jquery.pnotify.default.css'
	],
	include: [
		'components/requirejs/require',
		'lib/Application',
		'lib/Model',
		'lib/model/User',
		'lib/Store',
		'lib/controller/List',
		'lib/view/form/Symfony',
		'lib/view/form/InlineNestedModel',
		'lib/view/Grid',
		'lib/view/layout/HBoxDiv',
		'lib/view/Model'
	],
	findNestedDependencies: true,
	useSourceUrl: false
})