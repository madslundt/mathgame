<script>
	$(function() {
		$('#tablesorter').tablesorter();
	});
</script>
<?php
	$today = time();
	if (isset($_POST['onlyfinished'])) {
		$finish = " AND s.finished = 1";
	} else {
		$finish = "";
	}
	if ($_GET['view'] == 'group') {
		if ($_POST['find'] > -1) { // Specified group
			$group = $wpdb->get_results( $wpdb->prepare(
					"
					SELECT s.*, l.name AS lname, u.user_login AS uname
					FROM $wpdb->group_level
					INNER JOIN $wpdb->score s ON relationships_object_id = s.user_ID
					INNER JOIN $wpdb->level l ON s.level_ID = l.ID
					INNER JOIN $wpdb->users u ON s.user_ID = u.ID
					WHERE relationships_term_taxonomy_id = %d" . $finish . "
					ORDER BY s.points DESC, s.errors, s.time
					LIMIT 10
					", $_POST['find']
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
					echo '<th>' . __('Finished', 'wpbootstrap') . '</th>';
				echo '</thead>';
				echo '<tbody>';
					$c = 1;
					foreach ($group as $g) {
						if (strtotime($g->date) < strtotime('-5 days')) {
							$date = date(__('Y-m-d', 'bootstrap'), $g->date);
						} else {
							$date = human_time_diff( strtotime($g->date)) . ' ' . __('ago', 'wpbootstrap');
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

		} else { // ALL groups
			// List all groups max point
			/*$groups = $wpdb->get_results( $wpdb->prepare( 
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
					$c = 1;			
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
			}*/			
		}		
	} else if ($_GET['view'] == 'user') {
		if ($_POST['find']) { // Specified user

			$user = $wpdb->get_results( $wpdb->prepare(
				"
				SELECT s.*, l.name AS lname
				FROM $wpdb->score s
				INNER JOIN $wpdb->level l ON s.level_ID = l.ID
				WHERE s.user_ID = %d" . $finish . "
				ORDER BY s.points DESC, s.errors, s.time
				LIMIT 10
				", $_POST['find'], $finish
				)
			);
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
					$c = 1;
					foreach ($user as $u) {
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
					}
				echo '</tbody>';
			echo '</table>';
		} else { // ALL users
			$users = $wpdb->get_results( $wpdb->prepare(
				"
				SELECT s.*, u.user_login AS uname, l.name AS lname
				FROM $wpdb->score s
				INNER JOIN $wpdb->level l ON s.level_ID = l.ID
				INNER JOIN $wpdb->users u ON s.user_ID = u.ID
				WHERE 1=1" . $finish . "
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
					echo '<th>' . __('Finished', 'wpbootstrap') . '</th>';
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
							echo '<td>' . (($u->finished) ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>') . '</td>';
						echo '</tr>';
						$c++;	
					}
				echo '</tbody>';
			echo '</table>';
		}
	} else {
		if ($_POST['find'] > -1) { // Specified level
			$level = $wpdb->get_results( $wpdb->prepare(
					"
					SELECT s.*, u.user_login AS uname, t.name AS gname
					FROM $wpdb->level l
					INNER JOIN $wpdb->group_level g ON l.ID = g.level_ID
					INNER JOIN $wpdb->terms t ON g.relationships_term_taxonomy_id = t.term_id
					INNER JOIN $wpdb->score s ON l.ID = s.level_ID
					INNER JOIN $wpdb->users u ON s.user_ID = u.ID
					WHERE l.ID = %d" . $finish . "
					ORDER BY s.points DESC, s.errors, s.time
					LIMIT 10
					", $_POST['find']
				)
			);
			echo '<table class="table table-hover" id="tablesorter">';
				echo '<thead>';
					echo '<th>#</th>';
					echo '<th>' . __('Name','wpbootstrap') . '</th>';
					echo '<th>' . __('Points','wpbootstrap') . '</th>';
					echo '<th>' . __('Errors','wpbootstrap') . '</th>';
					echo '<th>' . __('Time','wpbootstrap') . '</th>';
					echo '<th>' . __('Group name','wpbootstrap') . '</th>';
					echo '<th>' . __('Date', 'wpbootstrap') . '</th>';
					echo '<th>' . __('Finished', 'wpbootstrap') . '</th>';
				echo '</thead>';
				echo '<tbody>';
					$c = 1;
					foreach ($level as $l) {
						if (strtotime($l->date) < strtotime('-5 days')) {
							$date = date(__('Y-m-d', 'bootstrap'), $l->date);
						} else {
							$date = human_time_diff( strtotime($l->date)) . ' ' . __('ago', 'wpbootstrap');
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
		} else { // All levels
			// List each level max point
			echo $table_prefix;
		}
	}
?>

<?php function getUserTable() {

?>
	<p>TEST</p>
<?php } ?>