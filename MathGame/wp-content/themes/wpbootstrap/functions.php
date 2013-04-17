<?php

function addScoreToLevelFunction() {
	global $wpdb;
	$wpdb->query( $wpdb->prepare( 
		"
			INSERT INTO $wpdb->_score
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
		INSERT INTO $wpdb->_level_rating
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
			FROM $wpdb->_level_revision
			WHERE level_revision = %d
			", $_POST['level']
		));
	
	$wpdb->query(
	 	$wpdb->prepare( 
			"
	       	DELETE FROM $wpdb->_level_revision
			WHERE level_revision = %d
			", $_POST['level']     
      ) ); 
	
	foreach ($rev as $r) {
		$wpdb->query(         
		    $wpdb->prepare( 
				"
		         DELETE FROM $wpdb->_bridge
				 WHERE level_ID = %d
				", $r    
	        ) );
		$wpdb->query(   
	        $wpdb->prepare( 
				"
		         DELETE FROM $wpdb->_score
				 WHERE level_ID = %d
				", $r    
	        ) );
		$wpdb->query(    
		    $wpdb->prepare( 
				"
		         DELETE FROM $wpdb->_group_level
				 WHERE level_ID = %d
				", $r     
	        ) ); 
	        
		$wpdb->query(     
		     $wpdb->prepare( 
				"
		         DELETE FROM $wpdb->_level
				 WHERE ID = %d
				", $r    
	        ) );
	}
	
	
		$wpdb->query(         
		    $wpdb->prepare( 
				"
		         DELETE FROM $wpdb->_bridge
				 WHERE level_ID = %d
				", $_POST['level']     
	        ) );
		$wpdb->query(   
	        $wpdb->prepare( 
				"
		         DELETE FROM $wpdb->_score
				 WHERE level_ID = %d
				", $_POST['level']     
	        ) );
		$wpdb->query(    
		    $wpdb->prepare( 
				"
		         DELETE FROM $wpdb->_group_level
				 WHERE level_ID = %d
				", $_POST['level']     
	        ) ); 
	        
		$wpdb->query(     
		     $wpdb->prepare( 
				"
		         DELETE FROM $wpdb->_level
				 WHERE ID = %d
				", $_POST['level']     
	        ) );	
	die();
  }  
  // creating Ajax call for WordPress
  //add_action('wp_ajax_nopriv_deleteLevel', 'deleteLevelFunction'); // For all
  add_action('wp_ajax_deleteLevel', 'deleteLevelFunction');  // Only logged in users

function jqueryui_script() {
	wp_register_script( 'jqueryui', get_template_directory_uri() . '/Scripts/jquery-ui.js' );
	wp_enqueue_script( 'jqueryui' );
}
add_action( 'wp_enqueue_scripts', 'jqueryui_script');

function wpbootstrap_scripts_with_jquery()
{
	wp_register_script( 'custom-script', get_template_directory_uri() . '/bootstrap/js/bootstrap.js', array( 'jquery' ) );
	wp_enqueue_script( 'custom-script' );	
}
add_action( 'wp_enqueue_scripts', 'wpbootstrap_scripts_with_jquery' );

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
function foobar_func( $atts ){
	return "foo and bar";
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


class Bootstrap_Walker extends Walker_Nav_Menu 
{     
 
	/* Start of the <ul> 
	 * 
	 * Note on $depth: Counterintuitively, $depth here means the "depth right before we start this menu".  
	 *                   So basically add one to what you'd expect it to be 
	 */         
	function start_lvl(&$output, $depth) 
	{
		$tabs = str_repeat("\t", $depth); 
		// If we are about to start the first submenu, we need to give it a dropdown-menu class 
		if ($depth == 0 || $depth == 1) { //really, level-1 or level-2, because $depth is misleading here (see note above) 
			$output .= "\n{$tabs}<ul class=\"dropdown-menu\">\n"; 
		} else { 
			$output .= "\n{$tabs}<ul>\n"; 
		} 
		return;
	} 
	 
	/* End of the <ul> 
	 * 
	 * Note on $depth: Counterintuitively, $depth here means the "depth right before we start this menu".  
	 *                   So basically add one to what you'd expect it to be 
	 */         
	function end_lvl(&$output, $depth)  
	{
		if ($depth == 0) { // This is actually the end of the level-1 submenu ($depth is misleading here too!) 
			 
			// we don't have anything special for Bootstrap, so we'll just leave an HTML comment for now 
			$output .= '<!--.dropdown-->'; 
		} 
		$tabs = str_repeat("\t", $depth); 
		$output .= "\n{$tabs}</ul>\n"; 
		return; 
	}
			 
	/* Output the <li> and the containing <a> 
	 * Note: $depth is "correct" at this level 
	 */         
	function start_el(&$output, $item, $depth, $args)  
	{    
		global $wp_query; 
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : ''; 
		$class_names = $value = ''; 
		$classes = empty( $item->classes ) ? array() : (array) $item->classes; 

		/* If this item has a dropdown menu, add the 'dropdown' class for Bootstrap */ 
		if ($item->hasChildren) { 
			$classes[] = 'dropdown'; 
			// level-1 menus also need the 'dropdown-submenu' class 
			if($depth == 1) { 
				$classes[] = 'dropdown-submenu'; 
			} 
		} 

		/* This is the stock Wordpress code that builds the <li> with all of its attributes */ 
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ); 
		$class_names = ' class="' . esc_attr( $class_names ) . '"'; 
		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';             
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : ''; 
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : ''; 
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : ''; 
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : ''; 
		$item_output = $args->before; 
					 
		/* If this item has a dropdown menu, make clicking on this link toggle it */ 
		if ($item->hasChildren && $depth == 0) { 
			$item_output .= '<a'. $attributes .' class="dropdown-toggle" data-toggle="dropdown">'; 
		} else { 
			$item_output .= '<a'. $attributes .'>'; 
		} 
		 
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after; 

		/* Output the actual caret for the user to click on to toggle the menu */             
		if ($item->hasChildren && $depth == 0) { 
			$item_output .= '<b class="caret"></b></a>'; 
		} else { 
			$item_output .= '</a>'; 
		} 

		$item_output .= $args->after; 
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args ); 
		return; 
	}
	
	/* Close the <li> 
	 * Note: the <a> is already closed 
	 * Note 2: $depth is "correct" at this level 
	 */         
	function end_el (&$output, $item, $depth, $args)
	{
		$output .= '</li>'; 
		return;
	} 
	 
	/* Add a 'hasChildren' property to the item 
	 * Code from: http://wordpress.org/support/topic/how-do-i-know-if-a-menu-item-has-children-or-is-a-leaf#post-3139633  
	 */ 
	function display_element ($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) 
	{ 
		// check whether this item has children, and set $item->hasChildren accordingly 
		$element->hasChildren = isset($children_elements[$element->ID]) && !empty($children_elements[$element->ID]); 

		// continue with normal behavior 
		return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output); 
	}         
} 

?>