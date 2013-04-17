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
?>
<div class="span12">
<form name="viewscore" method="POST" action="">
	<fieldset>
		
		<?php if ($_GET['view'] == 'group') { 
			$view = 'group';
		?>
			<h3><?php _e('Group highscore', 'wpbootstrap'); ?></h3>
			<div class="row">
				<div class="span2">
					<?php
						echo '<select class="span2" name="find">';
							echo '<option value="">' . __('All groups', 'wpbootstrap') . '</option>';
							foreach ($groups as $group) {
								echo '<option value="' . $group->term_id . '">' . $group->name . '</option>';
							}
						echo '</select>';
					?>
				</div>
		<?php } else if ($_GET['view'] == 'user') { 
			$view = 'user';	
		?>
			<h3><?php _e('User highscore', 'wpbootstrap'); ?></h3>
			<div class="row">
				<div class="span2">
					<?php
						echo '<select class="span2" name="find">';
							echo '<option value="">' . __('All users', 'wpbootstrap') . '</option>';
							foreach ($groups as $group) {
								$users = $wpdb->get_results( $wpdb->prepare( 
									"
									SELECT DISTINCT u.ID, u.user_login
									FROM $wpdb->term_relationships rs
									INNER JOIN $wpdb->users u ON rs.object_id = u.ID
									WHERE rs.term_taxonomy_id = %d
									ORDER BY u.ID
									", $group->term_id
								));
								echo '<option value="">' . $group->name . '</options>';
								foreach ($users as $user) {
									echo '<option value="' . $user->ID . '"> - ' . $user->user_login . '</option>';	
								}
							}
						echo '</select>';
					?>
				</div>
		<?php } else { 
			$view = 'level';	
		?>
			<h3><?php _e('Level highscore', 'wpbootstrap'); ?></h3>
			<div class="row">
				<div class="span2">
					<?php	
						echo '<select class="span2" name="find">';
							echo '<option value="">' . __('All levels', 'wpbootstrap') . '</option>';
							foreach ($groups as $group) {
								$levels = $wpdb->get_results( $wpdb->prepare( 
									"
									SELECT DISTINCT l.ID, l.name
									FROM $wpdb->_group_level gl
									INNER JOIN $wpdb->_level l ON gl.level_ID = l.ID
									LEFT JOIN $wpdb->_level_revision r ON l.ID = r.level_ID
									WHERE r.level_ID IS NULL AND gl.relationships_term_taxonomy_id = %d
									ORDER BY l.ID	
									", $group->term_id
								) );
								foreach ($levels as $level) {
									$revisions = $wpdb->get_results( $wpdb->prepare( 
												"
												SELECT l.ID, l.name 
												FROM $wpdb->_level_revision r
												INNER JOIN $wpdb->_level l ON r.level_ID = l.ID
												WHERE r.level_revision = %d
												ORDER BY level_ID	
												", $level->ID
										) );
									echo '<option value="' . $level->ID . '">' . $level->name . '</option>';
									foreach ($revisions as $revision) {
										echo '<option value="' . $revision->ID . '"> - ' . $revision->name . '</option>';	
									}
								}
							}		
						echo '</select>';
					?>
				</div>
		<?php } ?>
				<div class="span2">
					<!--<select class="span2" name="highscoreby">
						<option value="points"><?php _e('Points', 'wpbootstrap'); ?></option>
						<option value="errors"><?php _e('Errors', 'wpbootstrap'); ?></option>
					</select>-->
				</div>
				
				<div class="span1 pull-right">
					<input type="submit" name="submit" id="submit" class="span1 pull-right btn btn-primary" value="<?php _e('View', 'wpbootstrap'); ?>">
				</div>
			
				<div class="span2 pull-right">
					<p class="span2"><input type="checkbox" id="onlyfinished" checked> <?php _e('Only finished games', 'wpbootstrap'); ?></p>
				</div>
			</div>
	</fieldset>
</form>
	<div class="row">
		<div class="span3 pull-right">
			<div class="row">
				<div class="span3">
					<ul id="myTab" class="nav nav-tabs">
		            <li class="active"><a href="#table" data-toggle="tab"><i class="icon-align-justify"></i><?php _e('Table', 'wpbootstrap'); ?></a></li>
		            <li><a href="#chart" data-toggle="tab"><i class="icon-tasks"></i><?php _e('Chart', 'wpbootstrap'); ?></a></li>
		            </ul>
		        </div>
					<!--<div class="btn-group pull-right">
						<button class="btn"><i class="icon-align-justify"></i><?php _e('Table', 'wpbootstrap'); ?></button>
						<button class="btn"><i class="icon-tasks"></i><?php _e('Graph', 'wpbootstrap'); ?></button>
					</div>-->
				</div>
			</div>
		</div>
	<div class="row">
		<div class="span12">
			<div id="myTabContent" class="tab-content">
				<div class="tab-pane fade in active" id="table">
					<?php get_template_part('view-score-table'); ?>	
				</div>
				<div class="tab-pane fade" id="chart">
					<?php get_template_part('view-score-chart'); ?>	
				</div>
             </div>
		</div>
	</div>
</div>