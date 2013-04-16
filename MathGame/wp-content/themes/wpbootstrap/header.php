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
				<?php //wp_list_pages(array('title_li' => '', 'exclude' => 4));
				$menu = 'Visitor';
				if (is_user_logged_in()) {
					switch ($current_user->user_level)
					{
						case 0:
						case 1:
						case 2:
							$menu = 'User';
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
							$menu = 'Teacher';
							break;
					}					
				}
				wp_nav_menu(array('title_li' => '', 'items_wrap' => '<li class="%2$s">%3$s</li>', 'container' => 'li', 'menu' => $menu, 'walker' => new Bootstrap_Walker()));
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
					<?php echo header_login_form(); ?>
				<?php endif; ?>
			</ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
	
	<?php
		$access = true;
		$menu_items = wp_get_nav_menu_items($menu);
		foreach ( (array) $menu_items as $key => $menu_item ) {
			$tmpmenu = explode('://', $menu_item->url);
			/*if (preg_match($tmpmenu[1], $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '/[&|?]?[a-zA-Z=0-9]+/i')) {
				$access = true;
				break;
			}*/
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