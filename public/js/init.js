requirejs.config({
	baseUrl: '/js',
	paths: {
		'jquery':                   'jquery.min',
		'bootstrap':                'bootstrap.min'
	},
	shim: {
		bootstrap: {
			deps: ['jquery']
		}
	}
});

require(['jquery', 'bootstrap'], function($) {
	if (module = $('script[src$="require.js"]').data('module')) {
		require([module]);
	}
});
