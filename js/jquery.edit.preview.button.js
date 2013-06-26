var baseurl = wpAjax.baseurl;
(function($) {
   $.alter_links = function(selector) {
		$(selector).each(function(){
			url = $(this).attr("href");
			if(url) {
				hash2_start = url.indexOf(baseurl);
				if (hash2_start!=-1) {
					hash = url.substring(hash2_start + baseurl.length,url.length);
					$(this).attr("href",baseurl+"#/"+hash);
				}
			}
		});
	};
})(jQuery);
jQuery(document).ready(function($) {
    $.alter_links($("#view-post-btn").find("a"));
	$("#shortlink").next("a").hide();
});