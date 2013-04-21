<script>
$(function() {
	$('#tablesorter').tablesorter();
});
</script>
<?php
$groups = $wpdb->get_results( $wpdb->prepare( 
	"
	SELECT t.name, t.term_id
	FROM $wpdb->term_taxonomy taxo
	INNER JOIN $wpdb->terms t ON taxo.term_id = t.term_id
	INNER JOIN $wpdb->term_relationships rs ON t.term_id = rs.term_taxonomy_id
	WHERE taxo.taxonomy = 'user-group' AND rs.object_id = %d
	ORDER BY t.term_id
	", get_current_user_id()
	) );	

foreach ($groups as $group) {
	$levels = $wpdb->get_results( $wpdb->prepare( 
		"
		SELECT l . * 
		FROM $wpdb->group_level gl
		INNER JOIN $wpdb->level l ON gl.level_ID = l.ID
		LEFT JOIN $wpdb->level_revision r ON l.ID = r.level_ID
		WHERE r.level_ID IS NULL AND gl.relationships_term_taxonomy_id = %d
		ORDER BY l.ID	
		", $group->term_id
		) );
	
	echo '<h3>' . $group->name . '</h3>';
	echo '<table class="table table-hover">';
	echo '<thead>';
	echo '<th>#</th>';
	echo '<th>' . __('Name','wpbootstrap') . '</th>';
	echo '<th>' . __('Car time','wpbootstrap') . '</th>';
	echo '<th>' . __('Build time','wpbootstrap') . '</th>';
	echo '<th>' . __('Min. number','wpbootstrap') . '</th>';
	echo '<th>' . __('Max. number','wpbootstrap') . '</th>';
	echo '<th>' . __('Car speed','wpbootstrap') . '</th>';
	echo '<th>' . __('Bonus number','wpbootstrap') . '</th>';
	echo '<th>' . __('No. of bubbles','wpbootstrap') . '</th>';
	echo '<th>' . __('Bridge length','wpbootstrap') . '</th>';
	echo '</thead>';
	echo '<tbody>';
	foreach ($levels as $level) {
		$revisions = $wpdb->get_results( $wpdb->prepare( 
			"
			SELECT l.* 
			FROM $wpdb->level_revision r
			INNER JOIN $wpdb->level l ON r.level_ID = l.ID
			WHERE r.level_revision = %d
			ORDER BY level_ID	
			", $level->ID
			) );
		
		$bridgeCount = $wpdb->get_var( $wpdb->prepare( 
			"
			SELECT COUNT(*)
			FROM $wpdb->bridge
			WHERE level_ID = %d
			", $level->ID
			) );
		
		$count = count($revisions) + 1;
		echo '<tr>';
		echo '<td rowspan="' . $count . '"><p class="lead"><a href="' . get_permalink(30) . '&level=' . $level->ID . '">' . $level->ID . '</a></p></td>';
		echo '<td>' . $level->name . '</td>';
		echo '<td>' . $level->car_time . '</td>';
		echo '<td>' . $level->build_time . '</td>';
		echo '<td>' . $level->min_number . '</td>';
		echo '<td>' . $level->max_number . '</td>';
		echo '<td>' . $level->car_speed . '</td>';
		echo '<td>' . $level->bonus_number . '</td>';
		echo '<td>' . $level->number_bubbles . '</td>';
		echo '<td>' . $bridgeCount . '</td>';
		echo '</tr>';
		foreach ($revisions as $revision) {
			
			$bridgeCountr = $wpdb->get_var( $wpdb->prepare( 
				"
				SELECT COUNT(*)
				FROM $wpdb->bridge
				WHERE level_ID = %d
				", $revision->ID
				) );
			
			echo '<tr>';
			echo '<td>' . $revision->name . '</td>';
			echo '<td>' . $revision->car_time . '</td>';
			echo '<td>' . $revision->build_time . '</td>';
			echo '<td>' . $revision->min_number . '</td>';
			echo '<td>' . $revision->max_number . '</td>';
			echo '<td>' . $revision->car_speed . '</td>';
			echo '<td>' . $revision->bonus_number . '</td>';
			echo '<td>' . $revision->number_bubbles . '</td>';
			echo '<td>' . $bridgeCountr . '</td>';
			echo '</tr>';	
		}
	}
	echo '</tbody>';
	echo '</table>';	
}
?>


<!--<table class="table table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th><?php _e('Name','wpbootstrap'); ?></th>
			<th><?php _e('Car time','wpbootstrap'); ?></th>
			<th><?php _e('Build time','wpbootstrap'); ?></th>
			<th><?php _e('Min. number','wpbootstrap'); ?></th>
			<th><?php _e('Max. number','wpbootstrap'); ?></th>
			<th><?php _e('Car speed','wpbootstrap'); ?></th>
			<th><?php _e('Bonus number','wpbootstrap'); ?></th>
			<th><?php _e('No. of bubbles','wpbootstrap'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td rowspan="2">1</td>
			<td>Mark</td>
			<td>Otto</td>
			<td>@mdo</td>
		</tr>
		<tr>
		  <td>Mark</td>
		  <td>Otto</td>
		  <td>@TwBootstrap</td>
		</tr>
		<tr>
		  <td>2</td>
		  <td>Jacob</td>
		  <td>Thornton</td>
		  <td>@fat</td>
		</tr>
		<tr>
		  <td>3</td>
		  <td colspan="2">Larry the Bird</td>
		  <td>@twitter</td>
		</tr>
	</tbody>
	</table>-->