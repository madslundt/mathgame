<?php
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
?>
<div class="span12">
    <form name="viewscore" method="POST" action="">
        <fieldset>

            <?php if ($_GET['view'] == 'group')
            { ?>
                <h3><?php _e('Group highscore', 'wpboostrap'); ?></h3>
                <div class="row">
                    <div class="span2">
                        <?php
                        echo '<select class="span2" name="group">';
                        echo '<option value="-1">' . __('All groups', 'wpbootstrap') . '</option>';
                        foreach ($groups as $group)
                        {
                            echo '<option value="' . $group->term_id . '">' . $group->name . '</option>';
                        }
                        echo '</select>';
                        ?>
                    </div>
<?php }
else if ($_GET['view'] == 'user')
{ ?>
                    <h3><?php _e('User highscore', 'wpboostrap'); ?></h3>
                    <div class="row">
                        <div class="span2">
                            <?php
                            echo '<select class="span2" name="user">';
                            echo '<option value="-1">' . __('All users', 'wpbootstrap') . '</option>';
                            foreach ($groups as $group)
                            {
                                $users = $wpdb->get_results($wpdb->prepare(
                                    "
									SELECT DISTINCT u.ID, u.user_login
									FROM $wpdb->term_relationships rs
									INNER JOIN $wpdb->users u ON rs.object_id = u.ID
									WHERE rs.term_taxonomy_id = %d
									ORDER BY u.ID
									", $group->term_id
                                ));

                                echo '<option value="">' . $group->name . '</options>';
                                foreach ($users as $user)
                                {
                                    echo '<option value="' . $user->ID . '"> - ' . $user->user_login . '</option>';
                                }
                            }
                            echo '</select>';
                            ?>
                        </div>
                            <?php }
                            else
                            { ?>
                        <h3><?php _e('Level highscore', 'wpboostrap'); ?></h3>
                        <div class="row">
                            <div class="span2">
                                <?php
                                echo '<select class="span2" name="level">';
                                echo '<option value="-1">' . __('All levels', 'wpbootstrap') . '</option>';
                                foreach ($groups as $group)
                                {
                                    $levels = $wpdb->get_results($wpdb->prepare(
                                        "
    									SELECT DISTINCT l.ID, l.name
    									FROM $wpdb->group_level gl
    									INNER JOIN $wpdb->level l ON gl.level_ID = l.ID
    									LEFT JOIN $wpdb->level_revision r ON l.ID = r.level_ID
    									WHERE r.level_ID IS NULL AND gl.relationships_term_taxonomy_id = %d
    									ORDER BY l.ID	
    									", $group->term_id
                                    ));

                                    foreach ($levels as $level)
                                    {
                                        $revisions = $wpdb->get_results($wpdb->prepare(
                                            "
											SELECT l.ID, l.name 
											FROM $wpdb->level_revision r
											INNER JOIN $wpdb->level l ON r.level_ID = l.ID
											WHERE r.level_revision = %d
											ORDER BY level_ID	
											", $level->ID
                                        ));
                                        
                                        echo '<option value="' . $level->ID . '">' . $level->name . '</option>';
                                        foreach ($revisions as $revision)
                                        {
                                            echo '<option value="' . $revision->ID . '"> - ' . $revision->name . '</option>';
                                        }
                                    }
                                }
                                echo '</select>';
                                ?>
                            </div>
<?php } ?>
                        <div class="span2">
                            <select class="span2" name="highscoreby">
                                <option value="points"><?php _e('Points', 'wpbootstrap'); ?></option>
                                <option value="errors"><?php _e('Errors', 'wpbootstrap'); ?></option>
                            </select>
                        </div>

                        <div class="span1 pull-right">
                            <input type="submit" name="submit" id="submit" value="<?php _e('View', 'wpbootstrap'); ?>" class="span1 btn btn-primary">
                        </div>

                        <div class="span2 pull-right">
                            <p class="span2"><input type="checkbox" id="onlyfinished" checked> <?php _e('Only finished games', 'wpbootstrap'); ?></p>
                        </div>
                    </div>
                    </fieldset>
                    </form>
                    <div class="row">
                        <div class="span2 pull-right">
                            <div class="row">
                                <div class="span2">
                                    <div class="btn-group">
                                        <button class="btn"><i class="icon-align-justify"></i><?php _e('Table', 'wpbootstrap'); ?></button>
                                        <button class="btn"><i class="icon-tasks"></i><?php _e('Graph', 'wpbootstrap'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span12">
                            <?php
                            if ($_GET['view'] == 'group')
                            {
                                $type = 'group';
                                if ($_POST['group'] > -1)
                                { // Specified group
                                    $type += 'all';
                                }
                                else
                                { // ALL groups
                                    $type += $_POST['group'];
                                }
                                echo 'Group ID: ' . $_POST['group'] . '<br />';
                                echo 'Viewing by: ' . $_POST['highscoreby'];
                            }
                            else if ($_GET['view'] == 'user')
                            {
                                $type = 'user';
                                if ($_POST['user'])
                                { // Specified user
                                    $type += 'all';
                                }
                                else
                                { // ALL users
                                    $type += $_POST['user'];
                                }
                                echo 'User ID: ' . $_POST['user'] . '<br />';
                                echo 'Viewing by: ' . $_POST['highscoreby'];
                            }
                            else
                            {
                                $type = 'level=';
                                if ($_POST['level'] > -1)
                                { // Specified level
                                    $type += 'all';
                                }
                                else
                                { // All levels
                                    $type += $_POST['level'];
                                }
                                echo 'Level ID: ' . $_POST['level'] . '<br />';
                                echo 'Viewing by: ' . $_POST['highscoreby'];
                            }
                            ?>
                        </div>
                    </div>
                </div>