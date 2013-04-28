<script>
    $(function() {
        $('#tablesorter').tablesorter();
    });
</script>
<?php
$page = isset($_GET['page']) ? absint($_GET['page']) : 1;
$limit = 5;
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
$xaxis = array();
$yaxis = array();
if ($_GET['view'] == 'group')
{
    
    if ($cur_find > -1)
    { // Specified group
        $group = $wpdb->get_results($wpdb->prepare(
            "
            SELECT s.*, l.name AS lname, u.user_login AS uname
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

        echo '<table class="table table-hover" id="tablesorter">';
        echo '<thead>';
        echo '<th>#</th>';
        echo '<th>' . __('Name', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Points', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Errors', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Time', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Level no.', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Level name', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Date', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Finished', 'wpbootstrap') . '</th>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($group as $g)
        {
            if (strtotime($g->date) < strtotime('-5 days'))
            {
                $date = date(__('Y-m-d', 'bootstrap'), $g->date);
            }
            else
            {
                $date = human_time_diff(strtotime($g->date)) . ' ' . __('ago', 'wpbootstrap');
            }
            echo '<tr>';
            echo '<td><p class="lead">' . $c . '</p></td>';
            echo '<td>' . $g->uname . '</td>';
            echo '<td>' . $g->points . '</td>';
            echo '<td>' . $g->errors . '</td>';
            echo '<td>' . $g->time . '</td>';
            echo '<td>' . $g->level_ID . '</td>';
            echo '<td>' . $g->lname . '</td>';
            echo '<td>' . $date . '</td>';
            echo '<td>' . (($g->finished) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>') . '</td>';
            echo '</tr>';
            $c++;
        }
        echo '</tbody>';
        echo '</table>';        
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
          $date = date(__('Y-m-d', 'bootstrap'), $u->date);
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
            SELECT s.*, l.name AS lname
            FROM $wpdb->score s
            INNER JOIN $wpdb->level l ON s.level_ID = l.ID
            WHERE s.user_ID = %d" . $finish . "
            ORDER BY s.points DESC, s.errors, s.time
            LIMIT %d, %d
            ", $cur_find, $offset, $limit
        ));

        $total = $wpdb->get_var($wpdb->prepare(
            "
            SELECT COUNT(ID)
            FROM $wpdb->score
            WHERE user_ID = %d" . $finish . "
            ", $cur_find
        ));

        $points = array();
        $errors = array();
        foreach ($user as $u) {
            array_push($points, -$u->points);
            array_push($xaxis, $u->lname);
            array_push($errors, $u->errors);
        }
        
        echo '<div id="scoreChart" class="span11"></div>';
    }
    else
    { // ALL users
        /*$users = $wpdb->get_results($wpdb->prepare(
            "
            SELECT MAX(s.points), u.user_login AS uname
            FROM $wpdb->score s
            INNER JOIN $wpdb->level l ON s.level_ID = l.ID
            INNER JOIN $wpdb->users u ON s.user_ID = u.ID
            WHERE 1=1" . $finish . "
            ORDER BY s.points DESC, s.errors, s.time
            LIMIT %d, %d
            ", $offset, $limit
        ));

        foreach ($users as $u) {
            array_push($xaxis, $u->uname);
        }
        $yaxis = __('Points', 'wpbootstrap');

        echo '<div id="allUsers" class="span10"></div>';*/
    }
}
else
{
    if ($cur_find > -1)
    { // Specified level
        $level = $wpdb->get_results($wpdb->prepare(
            "
            SELECT s.*, u.user_login AS uname, t.name AS gname
            FROM $wpdb->level l
            INNER JOIN $wpdb->group_level g ON l.ID = g.level_ID
            INNER JOIN $wpdb->terms t ON g.relationships_term_taxonomy_id = t.term_id
            INNER JOIN $wpdb->score s ON l.ID = s.level_ID
            INNER JOIN $wpdb->users u ON s.user_ID = u.ID
            WHERE l.ID = %d" . $finish . "
            ORDER BY s.points DESC, s.errors, s.time
            LIMIT %d, %d
            ", $cur_find, $offset, $limit
        ));

        $total = $wpdb->get_var($wpdb->prepare(
            "
            SELECT COUNT(s.ID)
            FROM $wpdb->level l
            INNER JOIN $wpdb->group_level g ON l.ID = g.level_ID
            INNER JOIN $wpdb->terms t ON g.relationships_term_taxonomy_id = t.term_id
            INNER JOIN $wpdb->score s ON l.ID = s.level_ID
            INNER JOIN $wpdb->users u ON s.user_ID = u.ID
            WHERE l.ID = %d" 
            . $finish . "
            ", $cur_find
        ));        

        echo '<table class="table table-hover" id="tablesorter">';
        echo '<thead>';
        echo '<th>#</th>';
        echo '<th>' . __('Name', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Points', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Errors', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Time', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Group name', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Date', 'wpbootstrap') . '</th>';
        echo '<th>' . __('Finished', 'wpbootstrap') . '</th>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($level as $l)
        {
            if (strtotime($l->date) < strtotime('-5 days'))
            {
                $date = date(__('Y-m-d', 'bootstrap'), $l->date);
            }
            else
            {
                $date = human_time_diff(strtotime($l->date)) . ' ' . __('ago', 'wpbootstrap');
            }
            echo '<tr>';
            echo '<td><p class="lead">' . $c . '</p></td>';
            echo '<td>' . $l->uname . '</td>';
            echo '<td>' . $l->points . '</td>';
            echo '<td>' . $l->errors . '</td>';
            echo '<td>' . $l->time . '</td>';
            echo '<td>' . $l->gname . '</td>';
            echo '<td>' . $date . '</td>';
            echo '<td>' . (($l->finished) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>') . '</td>';
            echo '</tr>';
            $c++;
        }
        echo '</tbody>';
        echo '</table>';
    }
    else
    { // All levels
        // List each level max point
        echo $table_prefix;
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

$xaxis = json_encode($xaxis);
$yaxis = json_encode($yaxis);
$points = json_encode($points);
$errors = json_encode($errors);
?>

<script>
$(function () {
        $('#scoreChart').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: <?php echo $xaxis; ?>
            },
            yAxis: {
                min: 0,
                labels: {

                    formatter: function() {
                        return Math.abs(this.value);
                    }
                }
            },
            tooltip: {
                formatter: function(){
                    return '<b>'+ this.series.name + ' ' + this.point.category +'</b><br/>'+ Math.abs(this.point.y);
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Points',
                data: <?php echo $points; ?>
    
            }, {
                name: 'Errors',
                data: <?php echo $errors; ?> 
            }]
        });
    });
</script>