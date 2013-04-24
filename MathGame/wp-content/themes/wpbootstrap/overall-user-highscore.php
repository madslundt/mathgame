<script>
    $(function() {
        $('.table').tablesorter();
    });
</script>

<?php
$today = time();
$users = $wpdb->get_results($wpdb->prepare(
                "
		SELECT s.*, u.user_login AS uname, l.name AS lname
		FROM $wpdb->score s
		INNER JOIN $wpdb->level l ON s.level_ID = l.ID
		INNER JOIN $wpdb->users u ON s.user_ID = u.ID
		ORDER BY s.points DESC, s.errors, s.time
		LIMIT 5
		"
        )
);

echo '<table class="table table-condensed" id="tablesorter">';
echo '<thead>';
echo '<th>#</th>';
echo '<th>' . __('Name', 'wpbootstrap') . '</th>';
echo '<th>' . __('Points', 'wpbootstrap') . '</th>';
echo '<th>' . __('Errors', 'wpbootstrap') . '</th>';
echo '<th>' . __('Time', 'wpbootstrap') . '</th>';
echo '<th>' . __('Level no.', 'wpbootstrap') . '</th>';
echo '<th>' . __('Level name', 'wpbootstrap') . '</th>';
echo '<th>' . __('Date', 'wpbootstrap') . '</th>';
echo '</thead/>';
echo '<tbody>';
$c = 1;
foreach ($users as $u)
{
    if (strtotime($u->date) < strtotime('-5 days'))
    {
        $date = date(__('Y-m-d', 'bootstrap'), $u->date);
    }
    else
    {
        $date = human_time_diff(strtotime($u->date)) . ' ' . __('ago', 'wpbootstrap');
    }
    echo '<tr>';
    echo '<td><p>' . $c . '</p></td>';
    echo '<td>' . $u->uname . '</td>';
    echo '<td>' . $u->points . '</td>';
    echo '<td>' . $u->errors . '</td>';
    echo '<td>' . $u->time . '</td>';
    echo '<td>' . $u->level_ID . '</td>';
    echo '<td>' . $u->lname . '</td>';
    echo '<td>' . $date . '</td>';
    echo '</tr>';
    $c++;
}
echo '</tbody>';
echo '</table>';
?>