<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php wp_title('|',1,'right'); bloginfo('name'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<meta name='robots' content='noindex,nofollow' />

    <!-- Le styles -->
    <link href="<?php bloginfo('stylesheet_url');?>" rel="stylesheet">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

	<?php wp_enqueue_script("jquery"); ?>
	<?php wp_head();
	global $current_user;
	get_currentuserinfo();
	?>
	
    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="../assets/ico/favicon.png">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="<?php echo site_url(); ?>"><?php bloginfo('name'); ?></a>
          <div class="nav-collapse collapse">
            <ul class="nav" style="z-index: 10;">
				<?php
				$menu = 'guest';
				if (is_user_logged_in()) {
					switch ($current_user->user_level)
					{
						case 0:
						case 1:
						case 2:
							$menu = 'user';
							break;
						case 3:
						case 4:
						case 5:
						case 6:
						case 7:
						case 8:
						case 9:
						case 10:
						default:
							$menu = 'teacher';
							break;
					}					
				}
				$args = array(
					'theme_location' => 'top-bar',
					'depth'		 => 0,
					'container'	 => false,
					'menu_class'	 => 'nav',
					'theme_location' => $menu,
					'walker'	 => new BootstrapNavMenuWalker()
				);
 
				wp_nav_menu($args);				
				?>
			</ul>
			<ul class="nav pull-right">
				<?php if (is_user_logged_in()) : ?>
					<?php  ?>
					<p class="navbar-form pull-right">
						<a type="submit" class="btn" href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><i class="icon-user"></i><?php echo $current_user->user_login; ?></a>.
						<a type="submit" class="btn" href="<?php echo wp_logout_url(get_option('siteurl')); ?>"><?php _e('Log out','wpbootstrap'); ?></a>
					</p>
				<?php else : ?>
					<form name="loginform" id="loginform" class="navbar-form pull-right" action="<?php echo get_option('home'); ?>/wp-login.php" method="post">
					    <input type="text" class="input" name="log" id="user_login" placeholder="<?php _e('Name', 'wpbootstrap'); ?>" value="" size="20"/> 
					    <input type="password" class="input" name="pwd" id="user_pass" placeholder="Password" value="" size="20"/>
					    <input type="submit" name="wp-submit" id="wp-submit" class="btn" value="<?php _e('Log in', 'wpbootstrap'); ?>" />
					    <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
					    <input type="hidden" name="testcookie" value="1" />
					</form>
				<?php endif; ?>
			</ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
	
	<?php
		$access = false;
		$menu_items = wp_get_nav_menu_items($menu);
		//foreach ( (array) $menu_items as $key => $menu_item ) {
			if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu ] ) ) {
				$menus = wp_get_nav_menu_object( $locations[ $menu ] );

				$menu_items = wp_get_nav_menu_items($menus->term_id);
				foreach ( (array) $menu_items as $key => $menu_item ) {
					if ($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] == explode('://', $menu_item->url)[1]) {
						$access = true;
						//break;
					}
				}
			}
		
		if (!$access && !is_front_page()) {
			_e('No access!', 'wpbootstrap');
			exit();
		}
	?>
	
<noscript>
	<meta http-equiv="refresh" content="0; URL=<?php echo home_url() . '/404' ?>">
		<?php 
			_e('You need javascript to access the rest of the homepage.', 'wpbootstrap');
		?>
</noscript>
<div class="container">