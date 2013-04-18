<?php
require_once( plugin_dir_path( __FILE__ ) . '/custom_widgets.php' );
/*
Plugin Name: User score widgets
Plugin URI:
Description: View user scores in table and graphs
Version: 0.1
Author: Mads Lundt
Author URI:
License:
*/
function remove_dashboard_widgets() {
    global $remove_defaults_widgets;
 
    foreach ( $remove_defaults_widgets as $widget_id => $options ) {
        remove_meta_box( $widget_id, $options['page'], $options['context'] );
    }
}

function add_score_widgets() {
    global $custom_dashboard_widgets;
 
    foreach ( $custom_dashboard_widgets as $widget_id => $options ) {
        wp_add_dashboard_widget(
            $widget_id,
            $options['title'],
            $options['callback']
        );
    }
}

class Score_Widgets {
 
    function __construct() {
        add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
    }
 
    function remove_dashboard_widgets() {
 
    }
 
    function add_dashboard_widgets() {
 
    }
 
}
$wdw = new Score_Widgets();

$remove_defaults_widgets = array(
    'dashboard_incoming_links' => array(
        'page'    => 'dashboard',
        'context' => 'normal'
    ),
    'dashboard_right_now' => array(
        'page'    => 'dashboard',
        'context' => 'normal'
    ),
    'dashboard_recent_drafts' => array(
        'page'    => 'dashboard',
        'context' => 'side'
    ),
    'dashboard_quick_press' => array(
        'page'    => 'dashboard',
        'context' => 'side'
    ),
    'dashboard_plugins' => array(
        'page'    => 'dashboard',
        'context' => 'normal'
    ),
    'dashboard_primary' => array(
        'page'    => 'dashboard',
        'context' => 'side'
    ),
    'dashboard_secondary' => array(
        'page'    => 'dashboard',
        'context' => 'side'
    ),
    'dashboard_recent_comments' => array(
        'page'    => 'dashboard',
        'context' => 'normal'
    )
);

$user_score_widgets = array(
    'my-dashboard-widget' => array(
        'title' => 'User scores table',
        'callback' => 'userScoreTableWidget'
    )
);

function userScoreTableWidget() {
	get_template_part('overall-user-highscore');
}

?>