;(function ( $, undefined ) {
	$('body').ajaxify('addPlugin', {
		postParams: function() {
			var params = {_ajax_nonce: wpAjaxNonce.nonce};
			return params;
		}
	});
})( jQuery );
