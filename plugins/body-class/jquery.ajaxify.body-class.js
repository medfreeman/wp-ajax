;(function ( $, undefined ) {
	$('body').ajaxify('addPlugin', {
		firstCaching: function() {
			var params = {body_classes: $('body').attr('class').split(' ')};
			return params;
		},
		process: function(result) {
			if(typeof result.body_classes !== 'undefined') {
				$body = $('body');
				var old_classes = $body.attr('class').split(' ');
				var new_classes = result.body_classes;
				
				var body_classes_to_remove = new Array();
				var body_classes_to_add = new Array();
				
				for(var i=0;i<old_classes.length;i++) {
					for(var j in new_classes) {
						if(old_classes[i] !== new_classes[j]) {
							 body_classes_to_remove.push(old_classes[i]);
						}
					}
				}
				
				for(var k in new_classes) {
					if(new_classes.hasOwnProperty(k)) {
						if($.inArray(new_classes[k], old_classes) < 0) {
							body_classes_to_add.push(new_classes[k]);
						}
					}
				}
				
				$body.removeClass(body_classes_to_remove.join(' '));
				$body.addClass(body_classes_to_add.join(' '));
			}
		}
	});
})( jQuery );
