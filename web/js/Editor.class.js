var Editor = (function() { // namespace
	
// START Editor Definition
	__Editor = function() {
	// ------------------------
	// privates
	// ------------------------
		var _enabled = false;
		var _baseurl = '/';
		var _http    = [];
		
	// ------------------------
	// public vars and functions
	// ------------------------

		/**
		 *
		 */
		this.onDomReady = function () {
			if ('editor' != jQuery('#editor').attr('id')) {
				return;
			}
			
			_enabled = true;
			if ('127.0.0.1' == jQuery(location).attr('hostname')) {
				_baseurl = '/zwazo/';
			}
			_http = jQuery(location).attr('pathname').replace(_baseurl, '').split('/');
		}

		/**
		 *
		 */
		this.oc = function() {
			if (!_enabled){ return; }
			if ( jQuery('#editor').hasClass('open') ) {
				jQuery('#editor').removeClass('open');
				//jQuery('#editor iframe').attr('src', null );
			} else {
				jQuery('#editor').addClass('open');
				var src = jQuery('#editor iframe').attr('src');
				jQuery('#editor iframe').attr('src', _baseurl+_http[0]+'/list' );
			}
		}
	
	}
// END Editor Definition

// Dependencies validation
// + DOM Ready starter
	var DependeciesSatisfied = false;
	try {
		if ( 'undefined' !== typeof(jQuery) && '1.11.1' <= jQuery().jquery) {
			DependeciesSatisfied = true;
		}
	} catch (e) {
		DependeciesSatisfied = false;
	}

	if (true === DependeciesSatisfied) {
		jQuery( document ).ready(function() {
			Editor.onDomReady();
		});
	} else {
		alert('Editor dependecies are not fully satisfied.');
	}

	return new __Editor();
})();