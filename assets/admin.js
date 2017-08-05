jQuery(document).ready(function($) {

	jQuery('#button-site-reset').click(function(event) {
		event.preventDefault();

		if ( 'reset' === jQuery('#site-reset-confirm').val() ) {

			if ( confirm( siteReset.warning ) ) {
				jQuery('#site-reset-form').submit();
			} else {
				jQuery('#site-reset').val('false');
			}

		} else {
			alert( siteReset.invalid );
		}
	});

});