<script>
    $(function() {
        $('#tablesorter').tablesorter();
    });
</script>
<?php
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
$title = '';
$points = array();
$errors = array();
$time = array();
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
            ", $cur_find
        ));
        if (count($group) > 1) {
            foreach ($group as $u) {
                array_push($points, absint($u->points));
                array_push($xaxis, $u->uname . ' / ' . $u->lname);
                array_push($errors, absint($u->errors));
                array_push($time, round(floatval($u->time), 2));
            }
            $title = __('Group highscore', 'wpbootstrap');
            echo '<div id="scoreChart" class="span11"></div>';
        } else {
            _e('Not enough data', 'wpbootstrap');
        }                
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
            ", $cur_find
        ));

        if (count($user) > 1) {
            foreach ($user as $u) {
                array_push($points, absint($u->points));
                array_push($xaxis, $u->lname);
                array_push($errors, absint($u->errors));
                array_push($time, round(floatval($u->time), 2));
            }
            $title = __('User highscore', 'wpbootstrap');
            echo '<div id="scoreChart" class="span11"></div>';
        } else {
            _e('Not enough data', 'wpbootstrap');
        }
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

        if (count($level) > 1) {
            foreach ($level as $u) {
                array_push($points, absint($u->points));
                array_push($xaxis, $u->uname);
                array_push($errors, absint($u->errors));
                array_push($time, round(floatval($u->time), 2));
            }
            $title = __('Level highscore', 'wpbootstrap');
            echo '<div id="scoreChart" class="span11"></div>'; 
        } else {
            _e('Not enough data', 'wpbootstrap');
        }               

    }
    else
    { // All levels
        // List each level max point
    }
}

$xaxis = json_encode($xaxis);
$points = json_encode($points);
$errors = json_encode($errors);
$time = json_encode($time);
?>

<script>
$(function () {
        $('#scoreChart').highcharts({
            chart: {
                zoomType: 'xy',
                spacingRight: 20
            },
            title: {
                text: "<?php echo $title; ?>"
            },
            xAxis: {
                categories: <?php echo $xaxis; ?>
            },
            yAxis: [{ // Time yAxis
                allowDecimals: false,
                min: 0,
                labels: {
                    format: '{value} <?php _e("seconds", "wpbootstrap"); ?>',
                    style: {
                        color: '#89A54E'
                    },
                    allowDecimals: false
                },
                title: {
                    text: '<?php _e("Time", "wpbootstrap"); ?>',
                    style: {
                        color: '#89A54E'
                    }
                },
                opposite: true

            }, { // Points yAxis
                allowDecimals: false,
                min: 0,    
                labels: {
                    format: '{value} <?php _e("points", "wpbootstrap"); ?>',
                    style: {
                        color: '#4572A7'
                    }
                },
                title: {
                    text: '<?php _e("Points", "wpbootstrap"); ?>',
                    style: {
                        color: '#4572A7'
                    }
                }

            }, { // Error yAxis
                allowDecimals: false,
                gridLineWidth: 0,
                min: 0,
                title: {
                    text: '<?php _e("Errors", "wpbootstrap"); ?>',
                    style: {
                        color: '#AA4643'
                    },
                    allowDecimals: false
                },
                labels: {
                    formatter: function() {
                        return this.value +' <?php _e("errors", "wpbootstrap"); ?>';
                    },
                    style: {
                        color: '#AA4643'
                    }
                },
                opposite: true
            }],
            tooltip: {
                crosshairs: true,
                shared: true
            },
            series: [{
                name: '<?php _e("Points", "wpbootstrap"); ?>',
                color: '#4572A7',
                type: 'column',
                yAxis: 1,
                data: <?php echo $points; ?>,
                tooltip: {
                    valueSuffix: ' <?php _e("points", "wpbootstrap"); ?>'
                }
            }, {
                name: '<?php _e("Errors", "wpbootstrap"); ?>',
                color: '#AA4643',
                yAxis: 2,
                type: 'spline',
                data: <?php echo $errors; ?>,
                tooltip: {
                    valueSuffix: ' <?php _e("errors", "wpbootstrap"); ?>'
                }            
            }, {
                name: '<?php _e("Time", "wpbootstrap"); ?>',
                color: '#89A54E',
                type: 'spline',
                dashStyle: 'shortdot',
                data: <?php echo $time; ?>,
                tooltip: {
                    valueSuffix: ' <?php _e("seconds", "wpbootstrap"); ?>'
                }
            }]
        });
    });
</script>