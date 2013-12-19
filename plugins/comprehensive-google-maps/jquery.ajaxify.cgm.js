;(function ( $, undefined ) {
	jQuery.cachedScript = function( url, options ) {
	  // Allow user to set any option except for dataType, cache, and url
	  options = $.extend( options || {}, {
		dataType: "script",
		cache: true,
		url: url
	  });

	  return jQuery.ajax( options );
	};

	$('body').ajaxify('addPlugin', {
		firstCaching: function() {
			var params = {has_map: ($('body').find('.google-map-placeholder').length > 0)};
			return params;
		},
		process: function(result) {
			if (typeof result.has_map != 'undefined' && result.has_map) {
				$.cachedScript( comprehensiveGoogleMaps.cgm_script ).done(function( script, textStatus ) {
					
				});
			}
		},
	});
})( jQuery );
