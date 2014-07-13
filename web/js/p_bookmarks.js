jQuery( document ).ready(function() {

	if ( jQuery( "#tag-autocomplete" ).attr('id') ) {
		jQuery.ui.autocomplete.prototype._renderItem = function( ul, item ) {
// console.log(item);
			return jQuery( "<li>" )
				.attr( "value", item.id )
				.append( jQuery( "<a>" ).text( item.label ) )
				.appendTo( ul );
		};
	
	
		var autotag = jQuery( "#tag-autocomplete" ).autocomplete({ 
			delay:500,
			minLength:3,
			
			source: function(request, response) {
				// TODO: inject spinner
				
				var _baseurl = '/';
				if ('127.0.0.1' == jQuery(location).attr('hostname')) {
					_baseurl = '/zwazo/';
				}
				
				jQuery.ajax({
					url: _baseurl + 'tags/complete',
					dataType : 'json',
					data: {
						srch: request.term
					},
					success: function(jsdata) {	
						response( jsdata );
					}
				});
			},
			
			select: function( event, ui ) {
				// console.log(ui.item.id);
				
				// ajax to make joint
				// on ajax.success => create tag zone
				
			}
		});
	}
	
});