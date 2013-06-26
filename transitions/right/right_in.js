$(wpAjax.container).css("left", $(window).width());
$(wpAjax.container).animate({left: 0}, 1100, function(){
	$("body").css("overflow-x","auto");
});