function scrollto(selector) {
	jQuery('html, body').animate({
		scrollTop: jQuery( selector ).offset().top
	}, 1000);
}

jQuery( document ).ready(function() {
	
});