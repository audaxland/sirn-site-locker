function sirn_site_locker_admin_deny_option_display_toggle(){
	var templateDetails = document.getElementById( 'sirn-site-locker-template-details' );
	var redirectDetails = document.getElementById( 'sirn-site-locker-redirection-details' );
	if( document.getElementById( 'sirn-site-locker-deny-option-template-radio' ).checked ){
		templateDetails.style.display = 'block';
	}else{
		templateDetails.style.display = 'none';
	}
	if( document.getElementById( 'sirn-site-locker-deny-option-redirect' ).checked  ){
		redirectDetails.style.display = 'block';
	}else{
		redirectDetails.style.display = 'none';
	}
}
document.getElementById( 'sirn-site-locker-deny-option-template-radio' ).onchange = sirn_site_locker_admin_deny_option_display_toggle ;
document.getElementById( 'sirn-site-locker-deny-option-redirect' ).onchange = sirn_site_locker_admin_deny_option_display_toggle ;
sirn_site_locker_admin_deny_option_display_toggle() ;


/* adds the support of the color picker */
(function( $ ) {
	 
    // Add Color Picker to all inputs that have 'color-field' class
    $(function() {
        $('#sirn-site-locker-settings-background-color').wpColorPicker();
    });
     
})( jQuery );

