<?php

/*
 * Plugin Name: Sirmons Site Locker
 * Plugin URI: http://www.sirmons.fr
 * Description: This plugin prevents non logged-in users to access your website, usefull while your site is under construction or for a private website
 * Version: 1.0
 * Author: Nathanael SIRMONS
 * Author URI: http://www.sirmons.fr
 * Text Domain: sirnslocktd
 * Domain Path: /languages/
 * Licence: GPLv2 or later version
 */

/*  Copyright 2015 Nathanael SIRMONS  (email : sirn-site-locker@sirmons.fr)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2 or
(at your option) any later version, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


defined( 'ABSPATH' ) or die( 'Mille milliard de mille sabornes! Encore un gugus qui grille les URLs !!' );


/* Sirn_Site_locker is the main object of this plugin
 * Other objects are defined in the includes directory
 */
class Sirn_Site_Locker
{
	protected $settings; // is a Sirn_Site_Locker_Settings object
	
	/* implementation of a singleton behavior
	 */
	protected static $_instance = null;
	public static function instance(){
		if( is_null( self::$_instance ) ) self::$_instance = new self();
		return self::$_instance;
	}
	
	public function __construct(){
		if( is_null( self::$_instance ) ) {
			self::$_instance = $this;
			load_plugin_textdomain( 'sirnslocktd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}
		include_once( self::get_path( '/includes/class-sirn-site-locker-settings.php' ) ) ;
		$this->settings = Sirn_Site_Locker_Settings::instance();
	}

	/* the loader() method is a setup method for the plugin, it sets up other hooks
	 * this method is called during the 'after_setup_theme' action hook
	 */
	public static function loader(){
		$I = self::instance();
		add_action( 'init', array( $I, 'init'), 0 );
		
		if( is_admin() ){
			//loads the admin page for the plugin
			include_once self::get_path( '/includes/class-sirn-site-locker-admin.php' ) ;
			Sirn_Sirn_Locker_Admin::loader();
		}
		
		do_action( 'sirn_site_locker_loader', $I );
	}

	/* init() method is called during the 'init' action hook
	 * this is the main part of this plugin
	 */
	public function init(){

		if( is_user_logged_in() 
				|| 'wp-login.php' == $GLOBALS['pagenow'] 
				|| 'xmlrpc.php' == $GLOBALS['pagenow'] ) {
			// does nothing, the user has access to the requested page
		}else{
			if( $this->settings->get_deny_option() == 'template' ){
				include_once( self::get_path( '/includes/class-sirn-site-locker-front.php' ) );
				Sirn_Site_Locker_Front::execute_template();
				die();
			}elseif( $this->settings->get_deny_option() == 'redirect' ){
				$redirection_url = $this->settings->get_redirection_url();
				if( empty( $redirection_url ) ){
					// no redirection url is defined, so we execute a template insted
					include_once( self::get_path( '/includes/class-sirn-site-locker-front.php' ) );
					Sirn_Site_Locker_Front::execute_template();
					die();
				}else{
					header("Location: " . esc_url( $this->settings->get_redirection_url() ) );
					die();
				}

			}else{
				do_action( 'sirn_site_locker_other_deny_options' );
				die( 'Sirmons Site Locker has failed to find the proper content' );
			}
			
		}
	}
	
	
	/* the methods get_path(), path(), get_url() and url() give the paths or url relative to the plugin's direcpory
	 */
	public static function get_path( $path = '' ){
		return self::proper_slashing( dirname( __FILE__ ),  $path ) ;
	}
	
	public static function path( $path = '' ){
		echo self::proper_slashing( dirname( __FILE__ ), $path ) ;
	}
	
	public static function get_url( $path = '' ){
		return self::proper_slashing( plugin_dir_url( __FILE__ ), $path ) ;
	}
	
	public static function url( $path = '' ){
		echo self::proper_slashing( plugin_dir_url( __FILE__ ),  $path ) ;
	}
	
	protected static function proper_slashing( $first, $second ){
		$first = untrailingslashit( $first );
		if( !empty( $second ) && ( substr( $second, 0, 1 ) != '/' ) ){
			$second = '/' . $second;
		}
		return $first . $second ;
	}
	
	
	
	
}

add_action( 'after_setup_theme', array( 'Sirn_Site_Locker', 'loader' ) );
