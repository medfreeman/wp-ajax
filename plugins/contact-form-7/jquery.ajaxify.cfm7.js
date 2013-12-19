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
			var params = {has_contact: ($('body').find('.wpcf7-form').length > 0)};
			return params;
		},
		process: function(result) {
			if (typeof result.has_contact != 'undefined' && result.has_contact) {
				$.cachedScript( wpAjaxContactForm7.script ).done(function( script, textStatus ) {
					
				});
			}
		},
	});
})( jQuery );
