/*!
 * Wordpress wp ajax plugin
 * Author: mehdi.lahlou@free.fr
 * Licensed under GPLv2 license
 */

;(function ( $, undefined ) {
    $('body').ajaxify(wpAjaxify, function(cssTransitions, cssAnimations){
		scripts = [];
		
		if(!cssTransitions) {
			alert('no transitions');
			scripts.push(wpAjax.js_fallback_url + '/transitionsHelper.js')
		}
		if(!cssAnimations) {
			alert('no animations');
			scripts.push(wpAjax.js_fallback_url + '/animationsHelper.js')
		}
		
		scripts.push(wpAjax.js_fallback_url + '/jsfallback-master.js');
		
		loadScripts(scripts);
	});
	
	function loadScripts(scripts, index) {
		if (typeof(index) == 'undefined') {
			index = scripts.length - 1;
		}
		
		var type_of = typeof(scripts);
		
		if(type_of == 'object') {
			if(index < 0) {
				return;
			}
			
			$.getScript(scripts.slice(index, index + 1), loadScripts(scripts, index - 1));
		} else if (type_of == 'string') {
			$.getScript([scripts]);
			return;
		} else {
			return;
		}
	}
})( jQuery );
