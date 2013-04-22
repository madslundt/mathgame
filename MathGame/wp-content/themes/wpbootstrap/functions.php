<?php

// AJAX calls
function addScoreToLevelFunction() {
	global $wpdb;
	$wpdb->query( $wpdb->prepare( 
		"
		INSERT INTO $wpdb->score
		( errors, points, finished, time, user_ID, level_ID )
		VALUES ( %d, %d, %d, %f, %d, %d )
		", array( 
			$_POST['error'],
			$_POST['point'],
			$_POST['finish'],
			$_POST['time'],
			get_current_user_id(),
			$_POST['level']
			)
		) );
	die();
}
add_action('wp_ajax_addScoreToLevel', 'addScoreToLevelFunction');  // Only logged in users

function addRatingToLevelFunction() {
	global $wpdb;
	$wpdb->query( $wpdb->prepare( 
		"
		INSERT INTO $wpdb->level_rating
		( rating, user_ID, level_ID )
		VALUES ( %d, %d, %d )
		", array(
			$_POST['rating'],
			get_current_user_id(),
			$_POST['level']
			)
		) );
	die();
}  
  // creating Ajax call for WordPress
  //add_action('wp_ajax_nopriv_deleteLevel', 'deleteLevelFunction'); // For all
  add_action('wp_ajax_addRatingToLevel', 'addRatingToLevelFunction');  // Only logged in users

  function deleteLevelFunction() {
	global $wpdb; // Access to database $level
	$rev = $wpdb->get_col( 
		$wpdb->prepare( 
			"
			SELECT level_ID
			FROM $wpdb->level_revision
			WHERE level_revision = %d
			", $_POST['level']
			));
	
	$wpdb->query(
		$wpdb->prepare( 
			"
			DELETE FROM $wpdb->level_revision
			WHERE level_revision = %d
			", $_POST['level']     
			) ); 
	
	foreach ($rev as $r) {
		$wpdb->query(         
			$wpdb->prepare( 
				"
				DELETE FROM $wpdb->bridge
				WHERE level_ID = %d
				", $r    
				) );
		$wpdb->query(   
			$wpdb->prepare( 
				"
				DELETE FROM $wpdb->score
				WHERE level_ID = %d
				", $r    
				) );
		$wpdb->query(    
			$wpdb->prepare( 
				"
				DELETE FROM $wpdb->group_level
				WHERE level_ID = %d
				", $r     
				) ); 

		$wpdb->query(     
			$wpdb->prepare( 
				"
				DELETE FROM $wpdb->level
				WHERE ID = %d
				", $r    
				) );
	}
	
	
	$wpdb->query(         
		$wpdb->prepare( 
			"
			DELETE FROM $wpdb->bridge
			WHERE level_ID = %d
			", $_POST['level']     
			) );
	$wpdb->query(   
		$wpdb->prepare( 
			"
			DELETE FROM $wpdb->score
			WHERE level_ID = %d
			", $_POST['level']     
			) );
	$wpdb->query(    
		$wpdb->prepare( 
			"
			DELETE FROM $wpdb->group_level
			WHERE level_ID = %d
			", $_POST['level']     
			) ); 

	$wpdb->query(     
		$wpdb->prepare( 
			"
			DELETE FROM $wpdb->level
			WHERE ID = %d
			", $_POST['level']     
			) );	
	die();	
}  
  // creating Ajax call for WordPress
  //add_action('wp_ajax_nopriv_deleteLevel', 'deleteLevelFunction'); // For all
  add_action('wp_ajax_deleteLevel', 'deleteLevelFunction');  // Only logged in users

function addTables() {
  	global $wpdb;
  	$wpdb->level = $wpdb->prefix . '_level';
  	$wpdb->bridge = $wpdb->prefix . '_bridge';
  	$wpdb->group_level = $wpdb->prefix . '_group_level';
  	$wpdb->level_revision = $wpdb->prefix . '_level_revision';
  	$wpdb->fraction = $wpdb->prefix .  '_fraction';
  	$wpdb->level_fraction = $wpdb->prefix .  '_level_fraction';
  	$wpdb->level_rating = $wpdb->prefix .  '_level_rating';
  	$wpdb->score = $wpdb->prefix .  '_score';
  }
  add_action('init', 'addTables');

function disableTopToolBar() {
	if (!current_user_can('administrator')) { 
    	show_admin_bar(false); 
    }    
}
add_action('init', 'disableTopToolBar', 9);

function redirect_login() {
	wp_redirect(home_url());
}
add_action( 'login_form_login', 'redirect_login' );

function custom_scripts() {
  	wp_register_script( 'bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.js', array( 'jquery' ) );
  	wp_enqueue_script( 'bootstrap' );
  	wp_register_script( 'jqueryui', get_template_directory_uri() . '/Scripts/jquery-ui.js', array( 'jquery' )  );
  	wp_enqueue_script( 'jqueryui' );
  	wp_register_script('tablesorter', get_template_directory_uri() . '/Scripts/jquery.tablesorter.min.js', array( 'jquery' ) );
  	wp_enqueue_script('tablesorter');	
}
add_action( 'wp_enqueue_scripts', 'custom_scripts');

function register_my_menus() {
  	register_nav_menus(array(
  		'teacher' => __('Teacher menu', 'wpbootstrap'),
  		'user' => __('User menu', 'wpbootstrap'),
  		'guest' => __('Guest menu', 'wpbootstrap')
  		));
}
add_action('init', 'register_my_menus');

function custom_theme_setup() {
     // Retrieve the directory for the localization files
  	$lang_dir = get_template_directory() . '/languages';

    // Set the theme's text domain using the unique identifier from above
  	load_theme_textdomain('wpbootstrap', $lang_dir);

} // end custom_theme_setup
add_action('after_setup_theme', 'custom_theme_setup');

function get_ID_by_slug($page_slug) {
	$page = get_page_by_path($page_slug);
	if ($page) {
		return $page->ID;
	} else {
		return -1;
	}
}

	if (function_exists('register_sidebar'))
	{
		register_sidebar(array(
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
	}


// Testing shortcodes
function foobar_func( $atts ) {
	return 'foo and bar';
}
add_shortcode( 'foobar', 'foobar_func' );

function user_game_info($atts = 'row', $content = null) {
	extract( shortcode_atts( array(
		'class' => 'class'
		), $atts ) );

	return '<div class="' . esc_attr($class) . '">' . do_shortcode($content) . '</div>';
}
add_shortcode('user_game', 'user_game_info');

function user_game_span( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'class' => 'class'
		), $atts ) );

	return '<div class="' . esc_attr($span) . '">' . $content . '</div>';
}
add_shortcode('user_span', 'user_game_span' );


function user_level ($atts) {
	return __('Level', 'wpbootstrap') . $level;
}
add_shortcode('user_level', 'user_level' );

/**
 * Extended Walker class for use with the
 * Twitter Bootstrap toolkit Dropdown menus in Wordpress.
 * Edited to support n-levels submenu.
 * @author johnmegahan https://gist.github.com/1597994, Emanuele 'Tex' Tessore https://gist.github.com/3765640
 */
class BootstrapNavMenuWalker extends Walker_Nav_Menu {


	function start_lvl( &$output, $depth ) {

		$indent = str_repeat( "\t", $depth );
		$submenu = ($depth > 0) ? ' sub-menu' : '';
		$output	   .= "\n$indent<ul class=\"dropdown-menu$submenu depth_$depth\">\n";

	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {


		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$li_attributes = '';
		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		
		// managing divider: add divider class to an element to get a divider before it.
		$divider_class_position = array_search('divider', $classes);
		if($divider_class_position !== false){
			$output .= "<li class=\"divider\"></li>\n";
			unset($classes[$divider_class_position]);
		}
		
		$classes[] = ($args->has_children) ? 'dropdown' : '';
		$classes[] = ($item->current || $item->current_item_ancestor) ? 'active' : '';
		$classes[] = 'menu-item-' . $item->ID;
		if($depth && $args->has_children){
			$classes[] = 'dropdown-submenu';
		}


		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$attributes .= ($args->has_children) 	    ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= ($depth == 0 && $args->has_children) ? ' <b class="caret"></b></a>' : '</a>';
		$item_output .= $args->after;


		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	

	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
		//v($element);
		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];

		//display this element
		if ( is_array( $args[0] ) )
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
		else if ( is_object( $args[0] ) )
			$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);

	}

}

?>