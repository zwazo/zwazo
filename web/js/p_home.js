jQuery( document ).ready(function() {
	var unslider = jQuery('.banner').unslider({
		speed: 0,                 //  The speed to animate each slide (in milliseconds)
		delay: 2500,              //  The delay between slide animations (in milliseconds)
		// delay:1000,
		complete: function() {},  //  A function that gets called after every slide animation
		keys: true,               //  Enable keyboard (left, right) arrow shortcuts
		dots: true,               //  Display dot navigation
		fluid: false              //  Support responsive design. May break non-responsive designs
	}), data = unslider.data('unslider');
	
	jQuery('.unslider-arrow').click(function() {
		var fn = this.className.split(' ')[1];

		//  Either do unslider.data('unslider').next() or .prev() depending on the className
		unslider.data('unslider')[fn]();
	});
	
	jQuery( unslider ).mouseover(function() {
		if ( jQuery( '#play-pause' ).hasClass('play') ) {
			/* do nothing */
		} else if ( !jQuery( '#play-pause' ).hasClass('pause') ) {
			jQuery( '#play-pause' ).addClass('pause');
			jQuery( '#play-pause' ).attr('title','pause');
		}
	}); 
	
	jQuery( unslider ).mouseout(function() {
		if ( jQuery( '#play-pause' ).hasClass('pause') ) {
			jQuery( '#play-pause' ).removeClass('pause');
		}
	});

	/*jQuery( '#play-pause' ).click(function(){
		if ( jQuery( '#play-pause' ).hasClass('pause') ) {
			jQuery( '#play-pause' ).removeClass('pause');
			jQuery( '#play-pause' ).addClass('play');
			jQuery( '#play-pause' ).attr('title','play');
			data.stop();
		} else if ( jQuery( '#play-pause' ).hasClass('play') ) {
			jQuery( '#play-pause' ).removeClass('play');
			jQuery( '#play-pause' ).addClass('pause');
			jQuery( '#play-pause' ).attr('title','pause');
			data.start();
		}
	});*/
	
});
