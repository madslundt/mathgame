<script>
    $(function() {
        $('#tablesorter').tablesorter();
    });
</script>
<?php
$page = isset($_GET['page']) ? absint($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$cur_find = !empty($_SESSION['find' . $_GET['view']]) ? $_SESSION['find' . $_GET['view']] : -1;
$cur_finish = !empty($_SESSION['onlyfinished' . $_GET['view']]) ? $_SESSION['onlyfinished' . $_GET['view']] : 0;

$today = time();
if ($cur_finish)
{
    $finish = " AND s.finished = 1";
}
else
{
    $finish = "";
}
$c = 1 + $offset;
if ($_GET['view'] == 'group')
{
    
    if ($cur_find > -1)
    { // Specified group
        $group = $wpdb->get_results($wpdb->prepare(
            "
			SELECT DISTINCT s.*, l.name AS lname, u.user_login AS uname
			FROM $wpdb->group_level
			INNER JOIN $wpdb->score s ON relationships_object_id = s.user_ID
			INNER JOIN $wpdb->level l ON s.level_ID = l.ID
			INNER JOIN $wpdb->users u ON s.user_ID = u.ID
			WHERE relationships_term_taxonomy_id = %d" . $finish . "
			ORDER BY s.points DESC, s.errors, s.time
			LIMIT %d, %d
			", $cur_find, $offset, $limit
        ));

        $total = $wpdb->get_var($wpdb->prepare(
            "
            COUNT(s.ID)
            FROM $wpdb->group_level
            INNER JOIN $wpdb->score s ON relationships_object_id = s.user_ID
            INNER JOIN $wpdb->level l ON s.level_ID = l.ID
            INNER JOIN $wpdb->users u ON s.user_ID = u.ID
            WHERE relationships_term_taxonomy_id = %d" . $finish . "
            ", $cur_find
        ));
        ?>
        <table class="table table-hover" id="tablesorter">
        <thead>
        <th>#</th>
        <th><?php _e('Name', 'wpbootstrap'); ?></th>
        <th><?php _e('Points', 'wpbootstrap'); ?></th>
        <th><?php _e('Errors', 'wpbootstrap'); ?></th>
        <th><?php _e('Time', 'wpbootstrap'); ?></th>
        <th><?php _e('Level ID', 'wpbootstrap'); ?></th>
        <th><?php _e('Level', 'wpbootstrap'); ?></th>
        <th><?php _e('Date', 'wpbootstrap'); ?></th>
        <th><?php _e('Finished', 'wpbootstrap'); ?></th>
        </thead>
        <tbody>
        <?php
        foreach ($group as $g)
        {
            if (strtotime($g->date) < strtotime('-5 days'))
            {
                $date = date(get_option('date_format'), strtotime($g->date));
            }
            else
            {
                $date = human_time_diff(strtotime($g->date)) . ' ' . __('ago', 'wpbootstrap');
            }
        ?>
            <tr>
            <td><p class="lead"><?php echo $c; ?></p></td>
            <td><?php echo $g->uname; ?></td>
            <td><?php echo $g->points; ?></td>
            <td><?php echo $g->errors; ?></td>
            <td><?php echo gmdate('i:s', $g->time); ?></td>
            <td><?php echo $g->level_ID; ?></td>
            <td><?php echo $g->lname; ?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo (($g->finished) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>'); ?></td>
            </tr>
            <?php $c++;
        } ?>
        </tbody>
        </table>        
    <?php
    }
    else
    { // ALL groups
        // List all groups max point
        /* $groups = $wpdb->get_results( $wpdb->prepare( 
          "
          SELECT t.name, t.term_id
          FROM $wpdb->term_taxonomy taxo
          INNER JOIN $wpdb->terms t ON taxo.term_id = t.term_id
          INNER JOIN $wpdb->term_relationships rs ON t.term_id = rs.term_taxonomy_id
          WHERE taxo.taxonomy = 'user-group' AND rs.object_id = %d
          ORDER BY t.term_id
          ", get_current_user_id()
          ) );
          echo '<table class="table table-hover" id="tablesorter">';
          echo '<thead>';
          echo '<th>#</th>';
          echo '<th>' . __('Points','wpbootstrap') . '</th>';
          echo '<th>' . __('Errors','wpbootstrap') . '</th>';
          echo '<th>' . __('Time','wpbootstrap') . '</th>';
          echo '<th>' . __('Level no.','wpbootstrap') . '</th>';
          echo '<th>' . __('Level name','wpbootstrap') . '</th>';
          echo '<th>' . __('Date', 'wpbootstrap') . '</th>';
          echo '<th>' . __('Finished', 'wpbootstrap') . '</th>';
          echo '</thead>';
          echo '<tbody>';
          foreach ($groups as $g) {
          if (strtotime($u->date) < strtotime('-5 days')) {
          $date = date(get_option('date_format'), $u->date);
          } else {
          $date = human_time_diff( strtotime($u->date)) . ' ' . __('ago', 'wpbootstrap');
          }
          echo '<tr>';
          echo '<td><p class="lead">' . $c . '</p></td>';
          echo '<td>' . $u->points . '</td>';
          echo '<td>' . $u->errors . '</td>';
          echo '<td>' . $u->time . '</td>';
          echo '<td>' . $u->level_ID . '</td>';
          echo '<td>' . $u->lname . '</td>';
          echo '<td>' . $date . '</td>';
          echo '<td>' . (($u->finished) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>') . '</td>';
          echo '</tr>';
          $c++;
          } */
    }
}
else if ($_GET['view'] == 'user')
{
    if ($cur_find > -1)
    { // Specified user
        $user = $wpdb->get_results($wpdb->prepare(
            "
			SELECT DISTINCT s.*, l.name AS lname
			FROM $wpdb->score s
			INNER JOIN $wpdb->level l ON s.level_ID = l.ID
			WHERE s.user_ID = %d" . $finish . "
			ORDER BY s.points DESC, s.errors, s.time
			LIMIT %d, %d
			", $cur_find, $offset, $limit
        ));

        $total = $wpdb->get_var($wpdb->prepare(
            "
            SELECT COUNT(s.ID)
            FROM $wpdb->score s
            INNER JOIN $wpdb->level l ON s.level_ID = l.ID
            WHERE s.user_ID = %d" . $finish . "
            ", $cur_find
        ));        
        ?>
        <table class="table table-hover" id="tablesorter">
        <thead>
        <th>#</th>
        <th><?php _e('Points', 'wpbootstrap'); ?></th>
        <th><?php _e('Errors', 'wpbootstrap'); ?></th>
        <th><?php _e('Time', 'wpbootstrap'); ?></th>
        <th><?php _e('Level ID', 'wpbootstrap'); ?></th>
        <th><?php _e('Level', 'wpbootstrap'); ?></th>
        <th><?php _e('Date', 'wpbootstrap'); ?></th>
        <th><?php _e('Finished', 'wpbootstrap'); ?></th>
        </thead>
        <tbody>
        <?php
        foreach ($user as $u)
        {
            if (strtotime($u->date) < strtotime('-5 days'))
            {
                $date = date(get_option('date_format'), strtotime($u->date));
            }
            else
            {
                $date = human_time_diff(strtotime($u->date)) . ' ' . __('ago', 'wpbootstrap');
            }
        ?>
            <tr>
            <td><p class="lead"><?php echo $c; ?></p></td>
            <td><?php echo $u->points; ?></td>
            <td><?php echo $u->errors; ?></td>
            <td><?php echo gmdate('i:s', $u->time); ?></td>
            <td><?php echo $u->level_ID; ?></td>
            <td><?php echo $u->lname; ?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo (($u->finished) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>'); ?></td>
            </tr>
            <?php $c++; 
        } ?>
        </tbody>
        </table>
    <?php
    }
    else
    { // ALL users
        $users = $wpdb->get_results($wpdb->prepare(
            "
			SELECT DISTINCT s.*, u.user_login AS uname, l.name AS lname
			FROM $wpdb->score s
			INNER JOIN $wpdb->level l ON s.level_ID = l.ID
			INNER JOIN $wpdb->users u ON s.user_ID = u.ID
			WHERE 1=1" . $finish . "
			ORDER BY s.points DESC, s.errors, s.time
			LIMIT %d, %d
			", $offset, $limit
        ));

        $total = $wpdb->get_var($wpdb->prepare(
            "
            SELECT COUNT(s.ID)
            FROM $wpdb->score s
            INNER JOIN $wpdb->level l ON s.level_ID = l.ID
            INNER JOIN $wpdb->users u ON s.user_ID = u.ID
            WHERE 1=1" . $finish . "
            "
        ));        
        ?>
        <table class="table table-hover" id="tablesorter">
        <thead>
        <th>#</th>
        <th><?php echo _e('Name', 'wpbootstrap'); ?></th>
        <th><?php echo _e('Points', 'wpbootstrap'); ?></th>
        <th><?php echo _e('Errors', 'wpbootstrap'); ?></th>
        <th><?php echo _e('Time', 'wpbootstrap'); ?></th>
        <th><?php echo _e('Level ID', 'wpbootstrap'); ?></th>
        <th><?php echo _e('Level', 'wpbootstrap'); ?></th>
        <th><?php echo _e('Date', 'wpbootstrap'); ?></th>
        <th><?php echo _e('Finished', 'wpbootstrap'); ?></th>
        </thead>
        <tbody>
        <?php
        foreach ($users as $u)
        {
            if (strtotime($u->date) < strtotime('-5 days'))
            {
                $date = date(get_option('date_format'), strtotime($u->date));
            }
            else
            {
                $date = human_time_diff(strtotime($u->date)) . ' ' . __('ago', 'wpbootstrap');
            }
            ?>
            <tr>
            <td><p class="lead"><?php echo $c; ?></p></td>
            <td><?php echo $u->uname; ?></td>
            <td><?php echo $u->points; ?></td>
            <td><?php echo $u->errors; ?></td>
            <td><?php echo gmdate('i:s', $u->time); ?></td>
            <td><?php echo $u->level_ID; ?></td>
            <td><?php echo $u->lname; ?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo (($u->finished) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>'); ?></td>
            </tr>
            <?php $c++;
        } ?>
        </tbody>
        </table>
    <?php
    }
}
else
{
    if ($cur_find > -1)
    { // Specified level
        $level = $wpdb->get_results($wpdb->prepare(
            "
			SELECT DISTINCT s.*, u.user_login AS uname
			FROM $wpdb->level l
			INNER JOIN $wpdb->group_level g ON l.ID = g.level_ID
			INNER JOIN $wpdb->score s ON l.ID = s.level_ID
			INNER JOIN $wpdb->users u ON s.user_ID = u.ID
			WHERE l.ID = %d" . $finish . "
			ORDER BY s.points DESC, s.errors, s.time
			LIMIT %d, %d
			", $cur_find, $offset, $limit
        ));

        $total = $wpdb->get_var($wpdb->prepare(
            "
            SELECT DISTINCT COUNT(s.ID)
            FROM $wpdb->level l
            INNER JOIN $wpdb->score s ON l.ID = s.level_ID
            WHERE l.ID = %d" . $finish
			, $cur_find
        ));
		
		echo "total " . $total;
        ?>
        <table class="table table-hover" id="tablesorter">
        <thead>
        <th>#</th>
        <th><?php _e('Name', 'wpbootstrap'); ?></th>
        <th><?php _e('Points', 'wpbootstrap'); ?></th>
        <th><?php _e('Errors', 'wpbootstrap'); ?></th>
        <th><?php _e('Time', 'wpbootstrap'); ?></th>
        <th><?php _e('Date', 'wpbootstrap'); ?></th>
        <th><?php _e('Finished', 'wpbootstrap'); ?></th>
        </thead>
        <tbody>
        <?php
        foreach ($level as $l)
        {
            if (strtotime($l->date) < strtotime('-5 days'))
            {
                $date = date(get_option('date_format'), strtotime($l->date));
            }
            else
            {
                $date = human_time_diff(strtotime($l->date)) . ' ' . __('ago', 'wpbootstrap');
            }
        ?>
            <tr>
            <td><p class="lead"><?php echo $c; ?></p></td>
            <td><?php echo $l->uname; ?></td>
            <td><?php echo $l->points; ?></td>
            <td><?php echo $l->errors; ?></td>
            <td><?php echo gmdate('i:s', $l->time); ?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo (($l->finished) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>'); ?></td>
            </tr>
            <?php $c++;
        } ?>
        </tbody>
        </table>
    <?php
    }
    else
    { // All levels
        // List each level max point
    }
}

$num_of_pages = ceil($total / $limit);
$page_links = paginate_links(array(
    'base' => add_query_arg('page', '%#%'),
    'format' => '',
    'prev_next' => True,
    'prev_text' => __('&laquo;', 'wpbootstrap'),
    'next_text' => __('&raquo;', 'wpbootstrap'),
    'type' => 'list',
    'total' => $num_of_pages,
    'current' => $page
));

if ($page_links)
{
    echo '<div class="pagination pagination-right">';
    echo '<ul>';
    echo $page_links;
    echo '</ul>';
    echo '</div>';
}
?>

<?php

function getUserTable()
{
    ?>
    <p>TEST</p>
<?php } ?>