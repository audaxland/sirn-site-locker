<!DOCTYPE html>
<html>
<head>
	<meta charset="<?php bloginfo( 'charset' ) ; ?>" />
	<style type="text/css">
		body {
			background: url(<?php Sirn_Site_Locker::url( 'images/amazing-sunrise-background.jpg' ) ; ?>);
		}
	
		#stslock-message-box {
			width: 450px;
			margin: 100px auto;
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
</head>
<body>
	<div id="stslock-message-box">
		<h2>Ce site est r&eacute;rerv&eacute; aux membres</h2>
		<h3><em>Vous devez &ecirc;tre connect&eacute; pour acc&eacute au site : </em></h3>
		<h1><?php bloginfo( 'name' ) ;?></h1>
		<h3><?php bloginfo( 'description' ) ;?></h3>
		
		<div id="connexion-box">
			<a href="<?php echo site_url( '/wp-login.php' ) ; ?>" onclick="displayLoginForm(); return false;" id="show-connexion-form">S'identifier</a>
			<a href="#" onclick="hideLoginForm(); return false;" id="hide-connexion-form">Masquer le formulaire</a>
			<div id="connexion-form">
				<?php wp_login_form();?>
			</div>
		</div>
	</div>

	
</body>
</html>
