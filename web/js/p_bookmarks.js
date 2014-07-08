jQuery( document ).ready(function() {

	if ( jQuery( "#tag-autocomplete" ).attr('id') ) {
		jQuery( "#tag-autocomplete" ).autocomplete({ 
			delay:500
			,minLength:3
			,source: function(request, response) {
	// console.log('.source');
				// jQuery( "#tag-autocomplete" ).autocomplete( "disable" );
				// jQuery( "ul.ui-autocomplete" ).empty();
				// TODO: inject spinner
				
				// ajax request
				var ajaxresponse = ['java','foo','javascript'];
				
				// jQuery( "#tag-autocomplete" ).autocomplete( "enable" );
				response( ajaxresponse );
			}
		});
	}
	
});