;(function ( $, undefined ) {
	$('body').ajaxify('addPlugin', {
		postParams: function() {
			var params = {"ngg_id":4};
			return params;
		},
		process: function (result) {
			console.log(result.body_class);
		}
	});
})( jQuery );
