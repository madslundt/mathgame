<?php
    /*
    Plugin Name: Math Game database
	Plugin URI: 
	Description: Create tables for Math Game
	Version: 1.0
	Author: Mads Lundt
	Author URI: 
	License:
    */
 
function mathgameDatabase_activate() {
	if (!is_plugin_active('user-groups/user-groups.php')) {
		br_trigger_error(__("You need to activate plugin 'user-groups'", "wpbootstrap"), E_USER_ERROR);
		return;
	}
    global $wpdb;

    // Level
    $table_name = $wpdb->prefix . 'level';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE TABLE " . $table_name . "(
              ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
              name VARCHAR(45) NULL ,
              car_time INT NOT NULL ,
              build_time INT NOT NULL ,
              min_number INT NOT NULL ,
              max_number INT NOT NULL ,
              min_speed INT UNSIGNED NOT NULL ,
              max_speed INT UNSIGNED NOT NULL ,
              car_speed INT NOT NULL ,
              bonus_number INT NOT NULL ,
              number_bubbles INT NOT NULL ,
              PRIMARY KEY (ID) )
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_level_database_version', '1.0');
    }

    // Group-level
    $table_name = $wpdb->prefix . 'group_level';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
              relationships_object_id BIGINT(20) UNSIGNED NOT NULL ,
              relationships_term_taxonomy_id BIGINT(20) UNSIGNED NOT NULL ,
              level_ID BIGINT(20) UNSIGNED NOT NULL ,
              PRIMARY KEY (relationships_object_id, relationships_term_taxonomy_id, level_ID) ,
              INDEX fk_" . $wpdb->prefix . "term_relationships_has_" . $wpdb->prefix . "level_wp_m_idx (level_ID ASC) ,
              INDEX fk_" . $wpdb->prefix . "term_relationships_has_" . $wpdb->prefix . "level_wp_m_idx1 (relationships_object_id ASC, relationships_term_taxonomy_id ASC) ,
              CONSTRAINT fk_" . $wpdb->prefix . "term_relationships_has_" . $wpdb->prefix . "level_wp_mat
                FOREIGN KEY (relationships_object_id , relationships_term_taxonomy_id )
                REFERENCES " . $wpdb->prefix . "term_relationships (object_id , term_taxonomy_id )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT fk_" . $wpdb->prefix . "term_relationships_has_" . $wpdb->prefix . "level_wp_mat1
                FOREIGN KEY (level_ID )
                REFERENCES " . $wpdb->prefix . "level (ID )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_group-level_database_version', '1.0');
    }

    // Fraction
    $table_name = $wpdb->prefix . 'fraction';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
                ID INT UNSIGNED NOT NULL ,
                  denominator INT UNSIGNED NULL ,
                  PRIMARY KEY (ID) )
                ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_fraction_database_version', '1.0');
    }

    // Level-rating
    $table_name = $wpdb->prefix . 'level_rating';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
                rating INT NOT NULL ,
              level_ID BIGINT(20) UNSIGNED NOT NULL ,
              user_ID BIGINT(20) NOT NULL ,
              PRIMARY KEY (level_ID, user_ID) ,
              CONSTRAINT fk_" . $table_name . "_" . $wpdb->prefix . "level1
                FOREIGN KEY (level_ID )
                REFERENCES " . $wpdb->prefix . "level (ID )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_level-rating_database_version', '1.0');
    }
	
	// Bridge 
    $table_name = $wpdb->prefix . 'bridge';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE TABLE " . $table_name . "(
              number_pillar INT NOT NULL ,
              points INT NOT NULL ,
              level_ID BIGINT(20) UNSIGNED NOT NULL ,
              INDEX fk_" . $table_name . "_" . $wpdb->prefix . "level1_idx (level_ID ASC) ,
              CONSTRAINT fk_" . $table_name . "_" . $wpdb->prefix . "level1
                FOREIGN KEY (level_ID )
                REFERENCES " . $wpdb->prefix . "level (ID )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_bridge_database_version', '1.0');
    }
	
	// Score
    $table_name = $wpdb->prefix . 'score';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
              ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
              errors INT UNSIGNED NULL ,
              points INT UNSIGNED NULL ,
              finished TINYINT(1) NULL ,
              time FLOAT UNSIGNED NULL ,
              level_ID BIGINT(20) UNSIGNED NOT NULL ,
              user_ID BIGINT(20) UNSIGNED NOT NULL ,
              date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
              PRIMARY KEY (ID, level_ID, user_ID) ,
              INDEX fk_" . $table_name . "_" . $wpdb->prefix . "level1_idx (level_ID ASC) ,
              CONSTRAINT fk_" . $table_name . "_" . $wpdb->prefix . "level1
                FOREIGN KEY (level_ID )
                REFERENCES " . $wpdb->prefix . "level (ID )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_score_database_version', '1.0');
    }

    // Level revision
    $table_name = $wpdb->prefix . 'level_revision';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
              revision_level BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
              level_ID BIGINT(20) UNSIGNED NOT NULL ,
              PRIMARY KEY (revision_level, level_ID) ,
              INDEX fk_" . $table_name . "_" . $wpdb->prefix . "level1_idx (level_ID ASC) ,
              CONSTRAINT fk_" . $table_name . "_" . $wpdb->prefix . "level1
                FOREIGN KEY (level_ID )
                REFERENCES " . $wpdb->prefix . "level (ID )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_level-revision_database_version', '1.0');
    }

    // Level fraction
    $table_name = $wpdb->prefix . 'level_fraction';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
              fraction_ID INT(10) UNSIGNED NOT NULL ,
              level_ID BIGINT(20) UNSIGNED NOT NULL ,
              PRIMARY KEY (fraction_ID, level_ID) ,
              INDEX fk_" . $table_name . "_" . $wpdb->prefix . "lev_idx (level_ID ASC) ,
              INDEX fk_" . $table_name . "_" . $wpdb->prefix . "fr_idx (fraction_ID ASC) ,
              CONSTRAINT fk_" . $table_name . "_" . $wpdb->prefix . "frac1
                FOREIGN KEY (fraction_ID )
                REFERENCES " . $wpdb->prefix . "fraction (ID )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT fk_" . $table_name . "_" . $wpdb->prefix . "level1
                FOREIGN KEY (level_ID )
                REFERENCES " . $wpdb->prefix . "level (ID )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_level-fraction_database_version', '1.0');
    }

}

function br_trigger_error($message, $errno) {
 
    if(isset($_GET['action']) && $_GET['action'] == 'error_scrape') {
        echo '<strong>' . $message . '</strong>';
        exit;
    } else {
        trigger_error($message, $errno);
    }
 
}

register_activation_hook( __FILE__, 'mathgameDatabase_activate' );
?>