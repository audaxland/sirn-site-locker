<?php

defined( 'ABSPATH' ) or die( 'Mille milliard de mille sabornes! Encore un gugus qui grille les URL !!' );

class Sirn_Sirn_Locker_Admin
{
	/* implementation of a singleton behavior
	 */
	protected static $_instance = null;
	public static function instance(){
		if( is_null( self::$_instance ) ) self::$_instance = new self();
		return self::$_instance;
	}
	
	public function __construct(){
		if( is_null( self::$_instance ) ) self::$_instance = $this;
		$this->template_list = $this->get_template_list() ;
		$this->background_image_files		=	$this->get_background_image_list();
	}
	
	
	
	/* the loader() method is executer during the 'after_setup_theme' action hook and only if the user is in the admin
	 */
	public static function loader(){
		$I = self::instance();
		add_action( 'admin_menu', array( $I, 'register_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $I, 'admin_enqueue_scripts' ) );
	}
	
	public function register_admin_page(){
		add_options_page( 'Sirmons Site Locker', 'Sirmons Site Locker', 'manage_options', 'sirn_site_locker', array( $this, 'admin_page' ) );
		$this->setting_page_hook = register_setting( 'sirn_site_locker', 'sirn_site_locker_settings', array( $this, 'sanitize_settings') );
	}
	
	/* admin_enqueue_scripts() method is executed at the 'admin_enqueue_scripts' action hook
	 */
	public function admin_enqueue_scripts( $hook ){
		if( $hook == 'settings_page_sirn_site_locker' ){
			wp_register_style( 'sirn_site_locker_admin_css', Sirn_Site_Locker::get_url( '/css/admin-style.css' ), false, '1.0.' . time() );
       		wp_enqueue_style( 'sirn_site_locker_admin_css' );
       		wp_enqueue_script( 'sirn_site_locker_admin_js', Sirn_Site_Locker::get_url( '/javascript/admin-scripts.js' ), array( 'wp-color-picker' ), '1.0.' . time(), true );
       		
       		// we add the support of the color picker API
       		wp_enqueue_style( 'wp-color-picker' );
       		do_action( 'sirn_site_locker_admin_enqueue_scripts' );
		}
	}
	
	/* template_list() return the list of possible templates that the admin can choose from 
	 * the template here is the page non logged-in visitors to the site will get when landing on the website
	 * the template are either the .php files in the 'template' directory of the plugin, either the sirn-site-locker.php
	 * of the parent theme or the child theme or it can ba a template added by another plugin or a theme function.php file 
	 * using the filter hook
	 */
	public function get_template_list(){
		$templates = array();
		// the names of the template can be defined in the template file as a header field named 'Site Locker Name:'
		$header_field = array( 'template' => 'Site Locker Name' ) ;
		$plugin_template_files = scandir( Sirn_Site_Locker::get_path( 'templates/' ) );
		foreach( $plugin_template_files as $file ){
			if( ( substr( $file, -4 ) == '.php' ) && ( 'index.php' != $file ) ){
				$full_path = Sirn_Site_Locker::get_path( 'templates/' . $file ) ;
				$header_info = get_file_data( $full_path, $header_field );
				$name = empty( $header_info[ 'template' ] ) ? $file : __( $header_info[ 'template' ], 'sirnslocktd' ) ;
				$templates[ $name ] = $full_path ;
			}
		}
		if( get_template_directory() == get_stylesheet_directory() ){
			// the site is not using a child theme
			if( file_exists( get_template_directory() . '/sirn-site-locker.php' ) ){
				$header_info = get_file_data( get_template_directory() . '/sirn-site-locker.php', $header_field );
				$name = empty( $header_info[ 'template' ] ) ?  __( 'Theme template', 'sirnslocktd' )  : __( $header_info[ 'template' ], 'sirnslocktd' ) . ' (' .  __( 'Theme template', 'sirnslocktd' ) . ')' ;
				$templates[ $name ] = get_template_directory() . '/sirn-site-locker.php';
			}
		}else{
			// the site is using a child theme
			if( file_exists( get_template_directory() . '/sirn-site-locker.php' ) ){
				$header_info = get_file_data( get_template_directory() . '/sirn-site-locker.php', $header_field );
				$name = empty( $header_info[ 'template' ] ) ?  __( 'Parent theme tempate', 'sirnslocktd' )  : __( $header_info[ 'template' ], 'sirnslocktd' ) . ' (' .  __( 'Parent theme', 'sirnslocktd' ) . ')' ;
				$templates[ $name ] = get_template_directory() . '/sirn-site-locker.php';
			}
			if( file_exists( get_stylesheet_directory() . '/sirn-site-locker.php' ) ){
				$header_info = get_file_data( get_stylesheet_directory() . '/sirn-site-locker.php', $header_field );
				$name = empty( $header_info[ 'template' ] ) ?  __( 'Child theme tempate', 'sirnslocktd' )  : __( $header_info[ 'template' ], 'sirnslocktd' ) . ' (' .  __( 'Child theme', 'sirnslocktd' ) . ')' ;
				$templates[ $name ] = get_stylesheet_directory() . '/sirn-site-locker.php';
			}
		}
		return $templates ;
	}

	protected $template_list = array();
	
	/* get_background_image_list() returns a list of images found in the 'images' directory of the plugin
	 * 
	 */
	public function get_background_image_list(){
		$image_files = array(
				__( 'Default image', 'sirnslocktd' )		=>	'default',
				__( 'No background image', 'sirnslocktd' ) 	=> 	'none',
			);
		$files = scandir( Sirn_Site_Locker::get_path( '/images/' ) );
		foreach( $files as $file ){
			if( in_array( substr( $file, -4), array( '.jpg', '.png' ) ) || ( substr( $file, -5 ) == '.jpeg' ) ){
				$image_files[ $file ] = Sirn_Site_Locker::get_url( '/images/' . $file );
			}
		}
		return $image_files;
	}
	
	protected $background_image_files = array();
	
	public function sanitize_settings( $settings ){
		$sanitized = array();
		$current = Sirn_Site_Locker_Settings::instance();
		// sanitize deny_option
		if( !empty( $settings[ 'deny_option' ] ) && in_array( $settings[ 'deny_option' ] , array( 'template', 'redirect') ) ) {
			$sanitized[ 'deny_option' ] = $settings[ 'deny_option' ];
		}else{
			$sanitized[ 'deny_option' ] = $current->get_default( 'deny_option' );
		}
		// sanitize template
		if( !empty( $settings[ 'template_path' ] ) && in_array( $settings[ 'template_path' ] , $this->template_list ) ) {
			$sanitized[ 'template_path' ] = $settings[ 'template_path' ];
		}else{
			$sanitized[ 'template_path' ] = $current->get_default( 'template_path' );
		}
		// sanitize redirection_url
		if( !empty( $settings[ 'redirection_url' ] ) && !( substr( $settings[ 'redirection_url' ], -3 ) == '://' ) ){
			$sanitized[ 'redirection_url' ] = esc_url( $settings[ 'redirection_url' ] );
		}else{
			$sanitized[ 'redirection_url' ] = $current->get_default( 'redirection_url' );
		}
		// sanitize background color
		if( !empty( $settings[ 'background_color' ] ) && preg_match( '/^#?[a-f0-9]{6}$/i', $settings[ 'background_color' ] ) ){
			$sanitized[ 'background_color' ] = $settings[ 'background_color' ];
			if( substr( $sanitized[ 'background_color' ], 0, 1 ) != '#' ){
				$sanitized[ 'background_color' ] = '#' . $sanitized[ 'background_color' ];
			}
		}else{
			$sanitized[ 'background_color' ] = $current->get_default( 'background_color' );
		}
		// sanitize background image
		if( !empty( $settings[ 'background_image' ] ) && in_array( $settings[ 'background_image' ], $this->background_image_files ) ){
			$sanitized[ 'background_image' ] = $settings[ 'background_image' ] ;
		}else{
			$sanitized[ 'background_image' ] = $current->get_default( 'background_image' );
		}
		// sanitize logo url
		if( empty( $settings[ 'logo' ] ) || ( substr( trim( $settings[ 'logo' ] ) , -3 ) == '://' ) ){
			$sanitized[ 'logo' ] = '';
		}else{
			$sanitized[ 'logo' ] = esc_url( $settings[ 'logo' ] ) ;
		}
		// sanitize title
		if( !empty( $settings[ 'title' ] ) ){
			$sanitized[ 'title' ] = sanitize_text_field( $settings[ 'title' ] );
		}else{
			$sanitized[ 'title' ] = $current->get_default( 'title'  );
		}
		// sanitize sub-title
		if( !empty( $settings[ 'sub_title' ] ) ){
			$sanitized[ 'sub_title' ] = sanitize_text_field( $settings[ 'sub_title' ] );
		}else{
			$sanitized[ 'sub_title' ] = $current->get_default( 'sub_title');
		}
		// sanitize message
		if( !empty( $settings[ 'message' ] ) ){
			$sanitized[ 'message' ] = sanitize_text_field( $settings[ 'message' ] );
		}else{
			$sanitized[ 'message' ] = $current->get_default( 'message' );
		}
		// sanitize display site name
		if( !empty( $settings[ 'site_name' ] ) && ($settings[ 'site_name'] == 'no') ){
			$sanitized[ 'site_name' ] = FALSE;
		}else{
			$sanitized[ 'site_name' ] = $current->get_default( 'site_name' );
		}
		// sanitize display site description
		if( !empty( $settings[ 'site_description' ] ) && ($settings[ 'site_description'] == 'no') ){
			$sanitized[ 'site_description' ] = FALSE;
		}else{
			$sanitized[ 'site_description' ] = $current->get_default( 'site_description' );
		}
		// sanitize display login form
		if( !empty( $settings[ 'login_form' ] ) && ($settings[ 'login_form'] == 'no') ){
			$sanitized[ 'login_form' ] = FALSE;
		}else{
			$sanitized[ 'login_form' ] = $current->get_default( 'login_form' );
		}
		// allow external themes and plugins to add and sanitize other options
		$sanitized = apply_filters( 'sirn_site_locker_sanitize_settings', $sanitized, $settings );
		return $sanitized;
	}
	
	public function admin_page(){
		?>
			<div class="wrap">
				<h2><?php _e( 'Settings for the Sirmons Site Locker plugin', 'sirnslocktd' ) ; ?></h2>
				<form method="post" id="sirn_site_locker_settings" action="options.php">
					<?php 
						settings_fields( 'sirn_site_locker' );
						$settings = Sirn_Site_Locker_Settings::instance();
					?>
					<p class="submit">
						<input type="submit" value="<?php esc_attr_e( 'Save' ); ?>" class="button-primary" />
					</p>
					<div>
						<h3><?php _e( 'What page to display for non logged-in visitors ?', 'sirnslocktd') ; ?></h3>
						<div id="sirn-site-locker-deny-option-template">
						<?php 
							// this options will use a template form the 'template' directory of the plugin 
							// or the sirn-site-locker.php file in your theme
							// or a template added through the 'sirn_site_locker_templates' filter hook
						?>
							<input type="radio" name="sirn_site_locker_settings[deny_option]" id="sirn-site-locker-deny-option-template-radio"
								value="template" <?php checked( 'template', $settings->get_deny_option() ) ;?> class="sirn-site-locker-deny-option-radio" />
							<label for="sirn-site-locker-deny-option-template-radio" class="sirn-site-locker-deny-option-radio" >
								<?php _e( 'Display a template form the Sirmons Site Locker plugin or from the sirn-site-locker.php file in your theme', 'sirnslocktd' ) ; ?>
							</label>
							<div id="sirn-site-locker-template-details" class="sirn-site-locker-deny-option-details">
								<table>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Select a template : ', 'sirnslocktd' ) ; ?>
											</label>
										</th>
										<td>
											<select name="sirn_site_locker_settings[template_path]" >
												<?php $current_template = $settings->get_template_path() ; ?>
												<?php foreach( $this->template_list as $name => $path ) : ?>
													<option value="<?php echo $path ; ?>" <?php selected( $path, $current_template ) ; ?> >
														<?php echo $name  ; ?>
													</option>
												<?php endforeach; ?>
											</select>	
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Choose a background Color : ', 'sirnslocktd' ) ; ?>
											</label>
										</th>
										<td>
											<input type="text" name="sirn_site_locker_settings[background_color]" 
												value="<?php echo $settings->get_background_color( apply_filters( 'sirn_site_locker_default_background_color', '', $settings->get_template_path() ) ); ?>"
												id="sirn-site-locker-settings-background-color"  />	
										</td>
										<td>

										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Select a Background Image : ', 'sirnslocktd' ) ; ?>
											</label>
										</th>
										<td>
											<select name="sirn_site_locker_settings[background_image]" >
												<?php foreach( $this->background_image_files as $name => $path ) :?>
													<option value="<?php echo $path ;?>" <?php selected( $path, $settings->get_background_image() ) ;?>>
														<?php echo $name ; ?>
													</option>
												<?php endforeach; ?>
											</select>	
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Logo url : ', 'sirnslocktd' ) ; ?>
											</label>
										</th>
										<td>
											<input type="text" name="sirn_site_locker_settings[logo]" value="<?php echo $settings->get_logo() ;?>" />
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Title : ', 'sirnslocktd' ); ?>
											</label>
										</th>
										<td>
											<input type="text" name="sirn_site_locker_settings[title]" value="<?php esc_html_e( $settings->get_title() );  ?>" />
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Sub-title : ', 'sirnslocktd' ); ?>
											</label>
										</th>
										<td>
											<input type="text" name="sirn_site_locker_settings[sub_title]" value="<?php esc_html_e( $settings->get_sub_title() );  ?>" />
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Message : ', 'sirnslocktd' ); ?>
											</label>
										</th>
										<td>
											<textarea name="sirn_site_locker_settings[message]"><?php esc_html_e( $settings->get_message() );  ?></textarea>
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Display site name : ', 'sirnslocktd' ); ?>
											</label>
										</th>
										<td>
											<label>
												<input type="radio" name="sirn_site_locker_settings[site_name]" value="yes" <?php checked( true, $settings->get_site_name() ); ?> />
												<?php _e( 'Yes' ) ;?>
											</label>
											<label>
												<input type="radio" name="sirn_site_locker_settings[site_name]" value="no" <?php checked( false, $settings->get_site_name() ); ?> />
												<?php _e( 'No' ) ;?>
											</label>
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Display site description : ', 'sirnslocktd' ); ?>
											</label>
										</th>
										<td>
											<label>
												<input type="radio" name="sirn_site_locker_settings[site_description]" value="yes" <?php checked( true, $settings->get_site_description() ); ?> />
												<?php _e( 'Yes' ) ;?>
											</label>
											<label>
												<input type="radio" name="sirn_site_locker_settings[site_description]" value="no" <?php checked( false, $settings->get_site_description() ); ?> />
												<?php _e( 'No' ) ;?>
											</label>
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label>
												<?php _e( 'Display login form : ', 'sirnslocktd' ); ?>
											</label>
										</th>
										<td>
											<label>
												<input type="radio" name="sirn_site_locker_settings[login_form]" value="yes" <?php checked( true, $settings->get_login_form() ); ?> />
												<?php _e( 'Yes' ) ;?>
											</label>
											<label>
												<input type="radio" name="sirn_site_locker_settings[login_form]" value="no" <?php checked( false, $settings->get_login_form() ); ?> />
												<?php _e( 'No' ) ;?>
											</label>
										</td>
										<td>
											&nbsp;
										</td>
									</tr>
									<?php do_action( 'sirn_site_locker_admin_template_table', $settings ) ;?>
								</table>	
							</div><!-- #sirn-site-locker-template-details -->
						</div><!-- #sirn-site-locker-deny-option-template -->
						
						<div>
						<?php // this option will redirect visitors to a specefic url ?>
							<input type="radio" name="sirn_site_locker_settings[deny_option]" id="sirn-site-locker-deny-option-redirect" 
								value="redirect" <?php checked( 'redirect', $settings->get_deny_option() ) ;?>  class="sirn-site-locker-deny-option-radio" />
							<label for="sirn-site-locker-deny-option-redirect" class="sirn-site-locker-deny-option-radio" >
								<?php _e( 'Redirect the visitors to a specific url', 'sirnslocktd' ) ; ?>
							</label>
							<div id="sirn-site-locker-redirection-details" class="sirn-site-locker-deny-option-details" >
								<table>
									<tr>
										<th scope="row">
											<label for="sirn-site-locker-redirect-url">
												<?php _e( 'Redirection url : ', 'sirnslocktd' ); ?>
											</label>
										</th>
										<td>
											<input type="text" name="sirn_site_locker_settings[redirection_url]" id="sirn-site-locker-redirect-url" value="<?php echo esc_url( $settings->get_redirection_url() ) ; ?>" />
											
										</td>
									</tr>
								</table>
							</div><!-- #sirn-site-locker-redirection-details -->							
						</div>
					</div>
					<p class="submit">
						<input type="submit" value="<?php esc_attr_e( 'Save' ); ?>" class="button-primary" />
					</p>
				</form>
			</div>
			<?php 
	}
	

		
}