;(function ( $, undefined ) {
	$('body').ajaxify('addPlugin', {
		firstCaching: function() {
			var params = {postParam: 'test'};
			return params;
		},
		beforeLoad: function() {
			
		},
		postParams: function() {
			var params = {postParam: 'test'};
			return params;
		},
		process: function(result) {
			
		}
	});
})( jQuery );
