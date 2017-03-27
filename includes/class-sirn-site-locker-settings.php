<?php

/* Sirn_Site_Locker_Settings reads the settings from the database 
 * and makes them accessible from the plugin's tempate files or the admin page
 */

class Sirn_Site_Locker_Settings
{
	protected static $_instance = null;
	public static function instance(){
		if( is_null( self::$_instance ) ) self::$_instance = new self();
		return self::$_instance;
	}
	
	public function __construct(){
		if( is_null( self::$_instance ) ) self::$_instance = $this;
		$this->set_default_settings();
		$this->settings = get_option( 'sirn_site_locker_settings', $this->default_settings );
		if( is_array( $this->settings ) ){
			$this->settings = array_merge( $this->default_settings, $this->settings );
		}else{
			$this->settings = $this->default_settings;
		}
	}
	
	protected $settings;
	protected $default_settings = array();
	
	protected function set_default_settings(){
		$this->default_settings = array( 
				'deny_option'	=>	'template',
				'template_path'	=>	Sirn_Site_Locker::get_path( '/templates/sirnslock-deny-page.php' ),
				'redirection_url'	=>	'',
				'background_color'	=>	'',
				'background_image'	=>	'default',
				'logo'			=>	'',
				'title'			=>	__( 'This Website is currently under construction', 'sirnslocktd' ),
				'sub_title'		=>	__( 'Comming soon, on this page : ', 'sirnslocktd' ),
				'message'		=>	'',
				'site_name'		=>	true,
				'site_description'	=>	true,
				'login_form'	=>	true,
			);
	}
	
	public function get_deny_option(){
		return $this->settings[ 'deny_option' ];
	}
	
	public function get_template_path(){
		if( file_exists( $this->settings[ 'template_path' ] ) ){
			$template_path = $this->settings[ 'template_path' ];
		}elseif( file_exists( $this->default_settings[ 'template_path' ] ) ){
			$template_path = $this->default_settings[ 'template_path' ];
		}else{
			$template_path = FALSE;
		}
		return apply_filters( 'sirn_site_locker_template_path', $template_path );
	}
	
	public function get_redirection_url(){
		return $this->settings[ 'redirection_url' ];
	}
	
	public function get_logo(){
		return $this->settings[ 'logo' ];
	}
	
	public function get_logo_url(){
		return $this->get_logo();
	}
	
	public function get_title(){
		return $this->settings[ 'title' ] ;
	}
	
	public function get_sub_title(){
		return $this->settings[ 'sub_title' ] ;
	}
	
	public function get_message(){
		return $this->settings[ 'message' ] ;
	}
	
	public function get_site_name(){
		return (bool) $this->settings[ 'site_name' ] ;
	}
	
	public function get_site_description(){
		return (bool) $this->settings[ 'site_description' ];
	}
	
	public function get_login_form(){
		return (bool) $this->settings[ 'login_form' ];
	}
	
	public function get_background_color( $default_color = '' ){
		if( preg_match( '/^#?[a-f0-9]{6}$/i', $this->settings[ 'background_color' ] ) ){
			return $this->settings[ 'background_color' ];
		}else{
			return $default_color;
		}
	}
	
	public function get_background_image( $default = '' ){
		if( empty( $this->settings[ 'background_image' ] ) || ( $this->settings[ 'background_image' ] == 'default') ){
			return $default;
		}else{
			return $this->settings[ 'background_image' ];
		}
	}
	
	public function get_default( $setting ){
		if( isset( $this->default_settings[ $setting ] ) ) {
			return $this->default_settings[ $setting ];
		}else{
			return '';
		}
	}
}