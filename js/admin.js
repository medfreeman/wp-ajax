jQuery(function($){
	if ($.browser.mozilla)
         $("form").attr("autocomplete", "off");
	$("input[readonly], textarea[readonly]").css("background-color","#CCCCCC");
	var wrapped = $(".wrap h3").wrap("<div class=\"ui-tabs-panel\">");
	wrapped.each(function() {
		$(this).parent().append($(this).parent().nextUntil("div.ui-tabs-panel"));
	});
	$(".ui-tabs-panel").each(function(index) {
		var str = $(this).children("h3").text().replace(/\s/g, "_");
		$(this).attr("id", str.toLowerCase());
		if (index > 0)
			$(this).addClass("ui-tabs-hide");
	});
	$(".ui-tabs").tabs({ fx: { opacity: "toggle", duration: "fast" }, select: function(event, ui) { $("input#wp-ajax_last_tab").val(ui.index) }});
	$(".ui-tabs").tabs( "select" , parseInt($("input#wp-ajax_last_tab").val()));
    var error_msg = $("#message p[class='setting-error-message']");  
    // look for admin messages with the "setting-error-message" error class  
    if (error_msg.length != 0) {  
        // get the title  
        var error_setting = error_msg.attr('title');  
  
        // look for the label with the "for" attribute=setting title and give it an "error" class (style this in the css file!)  
        $("label[for='" + error_setting + "']").addClass('error');  
  
        // look for the input with id=setting title and add a red border to it.  
        $("input[id='" + error_setting + "']").attr('style', 'border-color: red');  
    }
	$('#wp-ajax_loading_graphics').change(function() {
		$("#wp-ajax_loading_graphics option:selected").each(function () {
				var loading = $(this).val();
				if (loading != 'custom') {
					$('#wp-ajax_loading_container_wrapper').attr("readonly","readonly");
					$('#wp-ajax_loading_css').attr("readonly","readonly");
					$('#wp-ajax_loading_js').attr("readonly","readonly");
					$("input[readonly], textarea[readonly]").css("background-color","#CCCCCC");
					$('#wp-ajax_loading_container_wrapper').load(wpAjaxSettings.plugin_url+'/skins/'+loading+'/'+loading+'.html');
					$('#wp-ajax_loading_css').load(wpAjaxSettings.plugin_url+'/skins/'+loading+'/'+loading+'.css');
					$('#wp-ajax_loading_js').load(wpAjaxSettings.plugin_url+'/skins/'+loading+'/'+loading+'.js');
				} else {
					$('#wp-ajax_loading_container_wrapper').removeAttr("readonly");
					$('#wp-ajax_loading_css').removeAttr("readonly");
					$('#wp-ajax_loading_js').removeAttr("readonly");
					$('#wp-ajax_loading_container_wrapper').css("background-color","");
					$('#wp-ajax_loading_css').css("background-color","");
					$('#wp-ajax_loading_js').css("background-color","");
				}
              });
	});
	$('#wp-ajax_loading_graphics').change();
	/*$('#wp-ajax_transition_graphics').change(function() {
		$("#wp-ajax_transition_graphics option:selected").each(function () {
				var transition = $(this).val();
				if (transition != 'custom') {
					$('#wp-ajax_transition_js').attr("readonly","readonly");
					$("#wp-ajax_transition_js").css("background-color","#CCCCCC");
					$('#wp-ajax_transition_js').load(wpAjaxSettings.plugin_url+'/transitions/'+transition+'/'+transition+'_out.js');
					$('#wp-ajax_transition_js_in').attr("readonly","readonly");
					$("#wp-ajax_transition_js_in").css("background-color","#CCCCCC");
					$('#wp-ajax_transition_js_in').load(wpAjaxSettings.plugin_url+'/transitions/'+transition+'/'+transition+'_in.js');
				} else {
					$('#wp-ajax_transition_js').removeAttr("readonly");
					$('#wp-ajax_transition_js').css("background-color","");
					$('#wp-ajax_transition_js_in').removeAttr("readonly");
					$('#wp-ajax_transition_js_in').css("background-color","");
				}
              });
	});
	$('#wp-ajax_transition_graphics').change();*/
});
jQuery(document).ready(function($) {
    /*if ($('#wp-ajax_transition_graphics option:selected').val() == 'custom') {
		$('#wp-ajax_transition_js').removeAttr("readonly");
		$('#wp-ajax_transition_js_in').removeAttr("readonly");
	}*/
	if ($('#wp-ajax_loading_graphics option:selected').val() == 'custom') {
		$('#wp-ajax_loading_container_wrapper').removeAttr("readonly");
		$("#wp-ajax_loading_container_wrapper").css("background-color","");
		$('#wp-ajax_loading_css').removeAttr("readonly");
		$("#wp-ajax_loading_css").css("background-color","");
		$('#wp-ajax_loading_js').removeAttr("readonly");
		$("#wp-ajax_loading_js").css("background-color","");
	}
});
