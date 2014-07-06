$( document ).ready(function() {
	var unslider = $('.banner').unslider({
		speed: 0,                 //  The speed to animate each slide (in milliseconds)
		delay: 5000,              //  The delay between slide animations (in milliseconds)
		complete: function() {},  //  A function that gets called after every slide animation
		keys: true,               //  Enable keyboard (left, right) arrow shortcuts
		dots: true,               //  Display dot navigation
		fluid: false              //  Support responsive design. May break non-responsive designs
	});
	
	$('.unslider-arrow').click(function() {
		var fn = this.className.split(' ')[1];

		//  Either do unslider.data('unslider').next() or .prev() depending on the className
		unslider.data('unslider')[fn]();
	});
});
