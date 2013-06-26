function animatePreloader() {
	$('.preloader1').animate({rotate: '-=360deg'}, 850, 'linear', animatePreloader);
	$('.preloader2').animate({rotate: '+=360deg'}, 850, 'linear');
	$('.preloader3').animate({rotate: '-=360deg'}, 850, 'linear');
}