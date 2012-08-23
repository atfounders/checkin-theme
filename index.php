<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php bloginfo('name'); ?></title>
	<meta name="description" content="">

	<meta name="viewport" content="width=device-width">
	<meta name="apple-mobile-web-app-capable" content="yes">

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	
	<?php wp_head(); ?>

</head>
<body>

	<div id="wrapper">
	
		<h1 id="logo"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
		<p id="description"><?php bloginfo('description'); ?></p>
	
		<?php if ( is_page( 'app' ) ) {
		
			app_output();
		
		} else {
		
			checkin_action();
			checkin_show_users();
		
		} ?>
		
		<?php //checkin_login_check(); ?>
		
<!--		<p><a href="http://chat.atfounders.com" title="Back to Founders Chat">&larr; Back to Founders Chat</a></p>-->
	
	</div>
	
	<div id="footer">
		<?php wp_footer(); ?>
	</div><!-- #footer -->

</body>
</html>