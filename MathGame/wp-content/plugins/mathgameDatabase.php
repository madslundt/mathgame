<?php
    /*
    Plugin Name: Math Game database
    */
 
function mathgameDatabase_activate() {
    global $wpdb;

    // Level
    $table_name = $wpdb->prefix . '_level';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE  TABLE IF NOT EXISTS " . $table_name . "(
              `ID` BIGINT(20) UNSIGNED NOT NULL ,
              `name` VARCHAR(45) NULL ,
              `car_time` INT NOT NULL ,
              `build_time` INT NOT NULL ,
              `min_number` INT NOT NULL ,
              `max_number` INT NOT NULL ,
              `car_speed` INT NOT NULL ,
              `bonus_number` INT NOT NULL ,
              PRIMARY KEY (`ID`) )
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_level_database_version', '1.0');
    }

    // Bridge
    $table_name = $wpdb->prefix . '_bridge';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE  TABLE IF NOT EXISTS " . $table_name . "(
              `number_pillar` INT NOT NULL ,
              `points` INT NOT NULL ,
              `level_ID` BIGINT(20) UNSIGNED NOT NULL ,
              INDEX `fk_" . $table_name . "_" . $wpdb->prefix . "_level1_idx` (`" . $wpdb->prefix . "_level_ID` ASC) ,
              CONSTRAINT `fk_" . $table_name . "_" . $wpdb->prefix . "_level1`
                FOREIGN KEY (`level_ID` )
                REFERENCES `" . $wpdb->prefix . "_level` (`ID` )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_bridge_database_version', '1.0');
    }

    // Group-level
    $table_name = $wpdb->prefix . '_group_level';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE  TABLE IF NOT EXISTS " . $table_name . "(
              `relationships_object_id` BIGINT(20) UNSIGNED NOT NULL ,
              `relationships_term_taxonomy_id` BIGINT(20) UNSIGNED NOT NULL ,
              `level_ID` BIGINT(20) UNSIGNED NOT NULL ,
              PRIMARY KEY (`relationships_object_id`, `relationships_term_taxonomy_id`, `level_ID`) ,
              INDEX `fk_" . $wpdb->prefix . "term_relationships_has_" . $wpdb->prefix . "_level_wp_m_idx` (`level_ID` ASC) ,
              INDEX `fk_" . $wpdb->prefix . "term_relationships_has_" . $wpdb->prefix . "_level_wp_m_idx1` (`relationships_object_id` ASC, `relationships_term_taxonomy_id` ASC) ,
              CONSTRAINT `fk_" . $wpdb->prefix . "term_relationships_has_" . $wpdb->prefix . "_level_wp_mat`
                FOREIGN KEY (`relationships_object_id` , `relationships_term_taxonomy_id` )
                REFERENCES `" . $wpdb->prefix . "term_relationships` (`object_id` , `term_taxonomy_id` )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_" . $wpdb->prefix . "term_relationships_has_" . $wpdb->prefix . "_level_wp_mat1`
                FOREIGN KEY (`level_ID` )
                REFERENCES `" . $wpdb->prefix . "_level` (`ID` )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_group-level_database_version', '1.0');
    }

    // Score
    $table_name = $wpdb->prefix . '_score';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE  TABLE IF NOT EXISTS " . $table_name . "(
              `ID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
              `errors` INT UNSIGNED NULL ,
              `points` INT UNSIGNED NULL ,
              `finished` TINYINT(1) NULL ,
              `time` FLOAT UNSIGNED NULL ,
              `level_ID` BIGINT(20) UNSIGNED NOT NULL ,
              `user_ID` BIGINT(20) UNSIGNED NOT NULL ,
              PRIMARY KEY (`ID`, `level_ID`, `user_ID`) ,
              INDEX `fk_" . $table_name . "_" . $wpdb->prefix . "_level1_idx` (`level_ID` ASC) ,
              CONSTRAINT `fk_" . $table_name . "_" . $wpdb->prefix . "_level1`
                FOREIGN KEY (`level_ID` )
                REFERENCES `MathGame`.`" . $wpdb->prefix . "_level` (`ID` )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_score_database_version', '1.0');
    }

    // Fraction
    $table_name = $wpdb->prefix . '_fraction';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE  TABLE IF NOT EXISTS " . $table_name . "(
                `ID` INT UNSIGNED NOT NULL ,
                  `denominator` INT UNSIGNED NULL ,
                  PRIMARY KEY (`ID`) )
                ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_fraction_database_version', '1.0');
    }

    // Level-rating
    $table_name = $wpdb->prefix . '_level_rating';
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE  TABLE IF NOT EXISTS " . $table_name . "(
                `rating` INT NOT NULL ,
              `level_ID` BIGINT(20) UNSIGNED NOT NULL ,
              `user_ID` BIGINT(20) NOT NULL ,
              PRIMARY KEY (`level_ID`, `user_ID`) ,
              CONSTRAINT `fk_" . $wpdb->prefix . "_level_rating_" . $wpdb->prefix . "_level1`
                FOREIGN KEY (`level_ID` )
                REFERENCES `" . $wpdb->prefix . "_level` (`ID` )
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('mathgame_level-rating_database_version', '1.0');
    }
}

register_activation_hook( __FILE__, 'mathgameDatabase_activate' );
?>