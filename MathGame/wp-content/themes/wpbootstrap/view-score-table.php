<script>
	$(function() {
		$('#tablesorter').tablesorter();
	});
</script>
<?php
	$today = time();
	if ($_GET['view'] == 'group') {
		if ($_POST['find'] > -1) { // Specified group
		
		} else { // ALL groups
			
		}
		
		
	} else if ($_GET['view'] == 'user') {
		if ($_POST['find']) { // Specified user
			
		} else { // ALL users
			$users = $wpdb->get_results( $wpdb->prepare(
				"
				SELECT s.*, u.user_login AS uname, l.name AS lname
				FROM $wpdb->score s
				INNER JOIN $wpdb->level l ON s.level_ID = l.ID
				INNER JOIN $wpdb->users u ON s.user_ID = u.ID
				ORDER BY s.points DESC, s.errors, s.time
				LIMIT 10
				"
				)
			); 

			echo '<table class="table table-hover" id="tablesorter">';
				echo '<thead>';
					echo '<th>#</th>';
					echo '<th>' . __('Name','wpbootstrap') . '</th>';
					echo '<th>' . __('Points','wpbootstrap') . '</th>';
					echo '<th>' . __('Errors','wpbootstrap') . '</th>';
					echo '<th>' . __('Time','wpbootstrap') . '</th>';
					echo '<th>' . __('Level no.','wpbootstrap') . '</th>';
					echo '<th>' . __('Level name','wpbootstrap') . '</th>';
					echo '<th>' . __('Date', 'wpbootstrap') . '</th>';
				echo '</thead>';
				echo '<tbody>';
					$c = 1;
					foreach ($users as $u) {
						if (strtotime($u->date) < strtotime('-5 days')) {
							$date = date(__('Y-m-d', 'bootstrap'), $u->date);
						} else {
							$date = human_time_diff( strtotime($u->date)) . ' ' . __('ago', 'wpbootstrap');
						}
						echo '<tr>';
							echo '<td><p class="lead">' . $c . '</p></td>';
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
		}
	} else {
		if ($_POST['find'] > -1) { // Specified level
			echo 'test1';
		} else { // All levels
			echo $table_prefix;
		}
	}
?>

<?php function getUserTable() {

?>
	<p>TEST</p>
<?php } ?>