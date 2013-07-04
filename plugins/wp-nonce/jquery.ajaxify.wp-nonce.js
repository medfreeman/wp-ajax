;(function ( $, window, undefined ) {
	$('body').ajaxify('addPlugin', {
		postParams: function() {
			var params = {_ajax_nonce: wpAjaxNonce.nonce};
			return params;
		},
		process: function(result) {
			if (typeof result.refresh !== 'undefined' && result.refresh) {
				window.location.href = window.location.href;
			}
		}
	});
})( jQuery, window );
