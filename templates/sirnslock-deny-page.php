<?php 
/*
 * Site Locker Name: Default template
 */
?><!DOCTYPE html>
<html>
<head>
	<meta charset="<?php bloginfo( 'charset' ) ;?>" />
	<style type="text/css">
		body {
			background: url(<?php Sirn_Site_Locker::url( 'images/green-background.jpg' ) ; ?>);
		}
	
		#wrapper {
		width: 500px;
		margin: 30px auto;
		}
	
		#logo_wrap {
			text-align: center;
			min-height: 70px;
		}
		
		#logo_wrap img {
			height: 100px;
			witdth: auto;
			max-with: 400px;
			max-height: 100px;
			
		}
		
		#sirnslock-message-box {
			width: 450px;
			margin: 10px auto;
			background: wheat;
			color: DarkOliveGreen;
			padding: 40px 30px 20px;
			border: 1px solid;
			border-radius: 15px;
			text-align: center;
		}
		
		#connexion-box {
			text-align: center;
			margin: 30px 10px 5px;
			font-style: italic;
			font-size: 0.7em;
		}
		#connexion-box a {
			color: DarkOliveGreen;
			
		}
		#connexion-form {
			display: none;
		}
		.login-username, .login-password {
			text-align: right;
			width: 85%;
		}
		#hide-connexion-form {
			display: none;
		}
		.message {
			font-size: small;
			font-style: italic;
			text-align: center;
		}
	</style>
	<script type="text/javascript">
		function displayLoginForm()
		{
			document.getElementById( "connexion-form" ).style.display = "block";
			document.getElementById( "show-connexion-form" ).style.display = "none";
			document.getElementById( "hide-connexion-form" ).style.display = "inline";
			return false;
		}
		function hideLoginForm()
		{
			document.getElementById( "connexion-form" ).style.display = "none";
			document.getElementById( "show-connexion-form" ).style.display = "inline";
			document.getElementById( "hide-connexion-form" ).style.display = "none";
			return false;
		}
	</script>
	<style type="text/css">
		<?php Sirn_Site_Locker_Front::display_custom_style() ;?>
	</style>
</head>
<body>
	<div id="wrapper">
		<p id="logo_wrap" >
			<?php Sirn_Site_Locker_Front::the_logo() ;?>
		</p>
		<div id="sirnslock-message-box">
			<?php Sirn_Site_Locker_Front::the_title( '<h2>', '</h2>') ;?>
			<?php Sirn_Site_Locker_Front::the_sub_title( '<h3><em>', '</em></h3>') ;?>
			<?php Sirn_Site_Locker_Front::the_site_name( '<h1>', '</h1>') ;?>
			<?php Sirn_Site_Locker_Front::the_site_description( '<h3>', '</h3>' ) ;?>
			<?php Sirn_Site_Locker_Front::the_message( '<p class="message">', '</p>') ;?>
			
			<?php if( Sirn_Site_Locker_Front::with_login_form() ) : ?>
				<div id="connexion-box">
					<a href="<?php echo site_url( '/wp-login.php' ) ; ?>" onclick="displayLoginForm(); return false;" id="show-connexion-form"><?php  _e( 'Login', 'sirnslocktd' ) ;?></a>
					<a href="#" onclick="hideLoginForm(); return false;" id="hide-connexion-form"><?php  _e( 'Hide login form', 'sirnslocktd' ) ;?></a>
					<div id="connexion-form">
						<?php wp_login_form();?>
					</div>
				</div>
			<?php endif; ?>
		</div>	
	</div>


	
</body>
</html>