<?php
class Sirn_Site_Locker_Front
{
	protected static $_instance = null ;
	public static function instance(){
		if( is_null( self::$_instance) ) self::$_instance = new self();
		return self::$_instance ;
	}
	
	public function __construct(){
		if( is_null( self::$_instance ) ) self::$_instance = $this;
		$this->settings = get_option( 'sirn_site_locker_settings', $this->default_settings );
	}
	

	public static function execute_template(){
		$I = self::instance() ;
		$settings = Sirn_Site_Locker_Settings::instance();
		$template_path = $settings->get_template_path();
		do_action( 'sirn_site_locker_front_load_template', $I, $template_path );
		if( $template_path && file_exists( $template_path ) ) {
			include_once( $template_path );
			do_action( 'sirn_site_locker_execute_template' );
			die();
		}else{
			die( 'Sirmons Site Locker has failed to find the template file'  );
		}
		
	}

	/* the folowing methods are used to display custom css in the templates head <style> section */
	
	/* display_custom_style() is a short way to add all the custom styles inside a <style> section */
	public static function display_custom_style(){
		self::custom_background_color();
		self::custom_background_image();
	}
	
	public static function custom_background_color( $default = '', $echo = true){
		$settings = Sirn_Site_Locker_Settings::instance();
		$default = apply_filters( 'sirn_site_locker_default_background_color', $default, $settings->get_template_path() ) ;
		$color = $settings->get_background_color( $default );
		if( empty( $color ) ) return '';
		else{
			$css = 'body { background-color: ' . $color . ";} \n";
			if( $echo ) echo $css;
			return $css;
		}
	}
	
	public static function custom_background_image( $default = '', $echo = true ){
		$settings = Sirn_Site_Locker_Settings::instance();
		$default = apply_filters( 'sirn_site_locker_default_background_image', $default, $settings->get_template_path() ) ;
		$img = $settings->get_background_image( $default );
		if( empty( $img ) ) return '';
		elseif( $img == 'none' ){
			$css = "body { background-image: none ;} \n";
		}
		else{
			$css = 'body { background-image: url(' . esc_url( $img ) . ");} \n";
		}
		if( $echo ) echo $css;
		return $css;
	}
	
	/* the folowing methods are to be accessed via the templates */
	public static function get_the_logo( $before = '', $after = '', $width = -1, $height = -1 ){
		$settings = Sirn_Site_Locker_Settings::instance();
		$logo_url = $settings->get_logo_url();
		if( empty( $logo_url ) || ( substr( esc_url( $logo_url ), -3 ) == '://' ) ){
			return '';
		}else{
			$img_width	= ( ( (int) $width ) > 0 ) ? ' width="' . absint( $width ) . '" ' :  '';
			$img_height	= ( ( (int) $height ) > 0 ) ? ' height="' . absint( $height ) . '" ' :  '';
			$alt = ( $settings->get_site_name() ) ? get_bloginfo( 'name' ) : $settings->get_title();
			return $before . '<img src="' . esc_url( $logo_url ) . '" alt="' . $alt . '" ' . $img_width . $img_height . ' />' . $after ;
		}
	}
	public static function the_logo( $before = '', $after = '', $width = -1, $height = -1  ){
		echo self::get_the_logo( $before, $after, $width, $height) ;
	}
	
	public static function get_the_title( $before = '', $after = '' ){
		$settings = Sirn_Site_Locker_Settings::instance();
		$title = $settings->get_title();
		if( !empty( $title ) ) {
			return $before . esc_html( $title ) . $after ;
		}else{
			return '';
		}
	}
	
	public static function the_title( $before = '', $after = '' ){
		echo self::get_the_title( $before, $after );
	}
	
	public static function get_the_sub_title( $before = '', $after = ''){
		$settings = Sirn_Site_Locker_Settings::instance();
		$sub_title = $settings->get_sub_title();
		if( !empty( $sub_title ) ) {
			return $before . esc_html( $sub_title ) . $after ;
		}else{
			return '';
		}
	}
	
	public static function the_sub_title( $before = '', $after = '' ){
		echo self::get_the_sub_title( $before, $after );
	}
	
	public static function get_the_message( $before = '', $after = ''){
		$settings = Sirn_Site_Locker_Settings::instance();
		$message = $settings->get_message();
		if( !empty( $message ) ) {
			return $before . esc_html( $message ) . $after ;
		}else{
			return '';
		}
	}
	
	public static function the_message( $before = '', $after = '' ){
		echo self::get_the_message( $before, $after );
	}	
	
	public static function get_the_site_name( $before = '', $after = ''){
		$settings = Sirn_Site_Locker_Settings::instance();
		$site_name = $settings->get_site_name();
		if( $site_name ) {
			return $before . get_bloginfo( 'name' ) . $after ;
		}else{
			return '';
		}
	}
	
	public static function the_site_name( $before = '', $after = '' ){
		echo self::get_the_site_name( $before, $after );
	}
	
	public static function get_the_site_description( $before = '', $after = ''){
		$settings = Sirn_Site_Locker_Settings::instance();
		$site_description = $settings->get_site_description();
		if( $site_description ) {
			return $before . get_bloginfo( 'description' ) . $after ;
		}else{
			return '';
		}
	}
	
	public static function the_site_description( $before = '', $after = '' ){
		echo self::get_the_site_description( $before, $after );
	}
	
	public static function with_login_form(){
		$settings = Sirn_Site_Locker_Settings::instance();
		return (bool) $settings->get_login_form();
	}
}