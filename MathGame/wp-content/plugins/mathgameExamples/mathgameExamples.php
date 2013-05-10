<?php
    /*
    Plugin Name: Math Game test levels
	Plugin URI: 
	Description: Creating test levels
	Version: 1.0
	Author: Mads Lundt
	Author URI: 
	License:
    */
 
function mathgameExamples_activate() {
	if (!is_plugin_active('mathgameDatabase/mathgameDatabase.php')) {
		br2_trigger_error(__("You need to activate plugin 'mathgameDatabase'", "wpbootstrap"), E_USER_ERROR);
		return;
	}
    global $wpdb;

    $groups = $wpdb->get_results($wpdb->prepare(
        "
        SELECT t.name, t.term_id
        FROM $wpdb->term_taxonomy taxo
        INNER JOIN $wpdb->terms t ON taxo.term_id = t.term_id
        INNER JOIN $wpdb->term_relationships rs ON t.term_id = rs.term_taxonomy_id
        WHERE taxo.taxonomy = 'user-group' AND rs.object_id = %d
        ORDER BY t.term_id
        ", get_current_user_id()
    ));

    $wpdb->query($wpdb->prepare(
        "
        INSERT INTO $wpdb->level
        ( name, car_time, build_time, min_number, max_number, min_speed, max_speed, car_speed, bonus_number, number_bubbles )
        VALUES ( %s, %d, %d, %d, %d, %d, %d, %d, %d, %d )
        ", 
        array(
            'Test 1',
            30,
            10,
            1,
            12,
            1,
            3,
            2,
            10,
            5
        )
    ));

    $lastid = $wpdb->insert_id;

    for ($i = 1; $i <= 5; $i++)
    {
        $wpdb->query($wpdb->prepare(
            "
            INSERT INTO $wpdb->bridge
            ( level_ID, number_pillar, points )
            VALUES ( %d, %d, %d )
            ", 
            array(
                $lastid,
                $i - 1,
                50 * i
            )
        ));
    }

    foreach ($groups as $g)
    {
        $wpdb->query($wpdb->prepare(
            "
            INSERT INTO $wpdb->group_level
            ( relationships_object_id, relationships_term_taxonomy_id, level_ID )
            VALUES ( %d, %d, %d )
            ", 
            array(
                get_current_user_id(),
                $g->term_id,
                $lastid
            )
        ));
    }
}

function br2_trigger_error($message, $errno) {
 
    if(isset($_GET['action']) && $_GET['action'] == 'error_scrape') {
        echo '<strong>' . $message . '</strong>';
        exit;
    } else {
        trigger_error($message, $errno);
    }
 
}

register_activation_hook( __FILE__, 'mathgameExamples_activate' );
?>