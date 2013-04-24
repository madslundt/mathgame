<?php
$level = -1;
$page = 1;
if ($_GET['level']) {
	$level = $_GET['level'];		
}

if ($level > 0) {
	$groups = $wpdb->get_results( $wpdb->prepare( 
		"
		SELECT t.term_id, t.name
		FROM $wpdb->group_level gl
		INNER JOIN $wpdb->terms t ON gl.relationships_term_taxonomy_id = t.term_id
		WHERE level_ID = %d
		", $level
		) );
	
	$userid = $wpdb->get_var( $wpdb->prepare( 
		"
		SELECT DISTINCT rs.term_taxonomy_id
		FROM $wpdb->term_relationships rs
		INNER JOIN $wpdb->group_level gl ON rs.term_taxonomy_id = gl.relationships_term_taxonomy_id AND gl.level_ID = %d
		WHERE rs.object_id = %d
		", $level, get_current_user_id()
		) );
	if ($userid == null || $userid < 0) {	
		_e('You are not a part of the group this level was created for', 'wpbootstrap');			
		exit();
	}
		// GET LEVEL DATA
	$levelData = $wpdb->get_row( $wpdb->prepare( 
		"
		SELECT *
		FROM  $wpdb->level
		WHERE ID = %d
		", $level
		) );
	$brigePillarPoint = $wpdb->get_results( $wpdb->prepare( 
		"
		SELECT number_pillar, points
		FROM $wpdb->bridge
		WHERE level_ID = %d
		ORDER BY number_pillar
		", $level
		) );
	
	$cur_name = $levelData->name;
	
	$cur_carSpeed = $levelData->car_speed;	
	
	$cur_carTimer = $levelData->car_time;
	
	$cur_buildTimer = $levelData->build_time;
	
	$cur_bridgePillar = count($brigePillarPoint);
	
	$cur_numberBubbles = $levelData->number_bubbles;
	
	$cur_numberRange1 = $levelData->min_number;
	$cur_numberRange2 = $levelData->max_number;
	
	$cur_minSpeed = $levelData->min_speed;
	$cur_maxSpeed = $levelData->max_speed;
	
	$cur_bonusNumber = $levelData->bonus_number;
	
	$cur_bridgePoints = array();
	
	for ($i = 0; $i < $cur_bridgePillar; $i++) {
		$cur_bridgePoints[$i] = $brigePillarPoint[$i]->points;
	}
	
} else {
	$cur_name = $_POST['levelName'];
	
	$cur_carSpeed = absint($_POST['carSpeed']);
	
	$cur_carTimer = absint($_POST['carTimer']);
	
	$cur_buildTimer = absint($_POST['buildTimer']);
	
	$cur_bridgePillar = ($_POST['bridgePillar'] < 2) ? 2 : absint($_POST['bridgePillar']);
	
	$cur_numberBubbles = absint($_POST['numberBubbles']);
	
	$numberRange = explode(':', $_POST['numbersBetween']);

	$cur_numberRange1 = absint($numberRange[0]);
	$cur_numberRange2 = absint($numberRange[1]);
	
	$cur_minSpeed = 1;
	$cur_maxSpeed = 3;
	
	$cur_bonusNumber = absint($_POST['bonusNumber']);	
	
	$cur_bridgePoints = array();

	for ($i = 0; $i < $cur_bridgePillar; $i++) {
		$cur_bridgePoints[$i] = $_POST['bridgePointName' . ($i + 1)];
	}	
}

$min_carSpeed = 1;
$max_carSpeed = 10;

$min_carTimer = 3;
$max_carTimer = 90;

$min_buildTimer = 3;
$max_buildTimer = 30;

$min_bridgePillar = 2;
$max_bridgePillar = 20;

$min_numberBubbles = 1;
$max_numberBubbles = 10;

$min_numberRange = -30;
$max_numberRange = 30;

$min_points = 0;
$max_points = 100000;

$min_speed = 0;
$max_speed = 10;

?>
<script>
$(function() {	   
	$("#delete_button").on("click", function() {
		jQuery.ajax({  
			type: 'POST',
			cache: false,  
			url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",  
			data: {  
				action: 'deleteLevel',  
				level: <?php echo $level; ?>
			},
			success: function(data, textStatus, XMLHttpRequest) {
				window.location.href = "<?php echo get_permalink(32); ?>";
			},  
			error: function(MLHttpRequest, textStatus, errorThrown) {
				alert("<?php _e('There was an error deleting the level. Level was not removed.', 'wpbootstrap'); ?>");  
			}  
		});  		
	});

	$( "#slider-number-range" ).slider({
		range: true,
		min: <?php echo $min_numberRange; ?>,
		max: <?php echo $max_numberRange; ?>,
		values: [ <?php echo $cur_numberRange1; ?>, <?php echo $cur_numberRange2; ?> ],
		slide: function( event, ui ) {
			$( "#numbersBetween" ).val(ui.values[ 0 ] + " : " + ui.values[ 1 ] );
			$( "#numbersBetweenLabel" ).text(ui.values[ 0 ] + " : " + ui.values[ 1 ] );
			$( "#slider-bonus-number" ).slider("option", "min", $( "#slider-number-range" ).slider( "values", 0 ));
			$( "#slider-bonus-number" ).slider("option", "max", $( "#slider-number-range" ).slider( "values", 1 ));
			if ($( "#slider-bonus-number" ).slider("value") < $( "#slider-number-range" ).slider( "values", 0 )) {
				$( "#slider-bonus-number" ).slider({ value: $( "#slider-number-range" ).slider( "values", 0 ) });
			} else if ($( "#slider-bonus-number" ).slider("value") > $( "#slider-number-range" ).slider( "values", 1 )) {
				$( "#slider-bonus-number" ).slider({ value: $( "#slider-number-range" ).slider( "values", 1 ) });
			}
			$( "#bonusNumber" ).val($( "#slider-bonus-number" ).slider( "value" ));
			$( "#bonusNumberLabel" ).text($( "#slider-bonus-number" ).slider( "value" ));
		}
	});
$( "#numbersBetween" ).val( $( "#slider-number-range" ).slider( "values", 0 ) +
	" : " + $( "#slider-number-range" ).slider( "values", 1 ) );
$( "#numbersBetweenLabel" ).text( $( "#slider-number-range" ).slider( "values", 0 ) +
	" : " + $( "#slider-number-range" ).slider( "values", 1 ) );

$( "#slider-bubble-speed" ).slider({
	range: true,
	min: <?php echo $min_speed; ?>,
	max: <?php echo $max_speed; ?>,
	values: [ <?php echo $cur_minSpeed; ?>, <?php echo $cur_maxSpeed; ?> ],
	slide: function( event, ui ) {
		$( "#bubbleSpeed" ).val(ui.values[ 0 ] + " : " + ui.values[ 1 ] );
		$( "#bubbleSpeedLabel" ).text(ui.values[ 0 ] + " : " + ui.values[ 1 ] );
	}
});
$( "#bubbleSpeed" ).val( $( "#slider-bubble-speed" ).slider( "values", 0 ) +
	" : " + $( "#slider-bubble-speed" ).slider( "values", 1 ) );
$( "#bubbleSpeedLabel" ).text( $( "#slider-bubble-speed" ).slider( "values", 0 ) +
	" : " + $( "#slider-bubble-speed" ).slider( "values", 1 ) );

$( "#slider-car-speed" ).slider({
	value: <?php echo $cur_carSpeed; ?>,
	min: <?php echo $min_carSpeed; ?>,
	max: <?php echo $max_carSpeed; ?>,
	slide: function( event, ui ) {
		$( "#carSpeed" ).val( ui.value );
		$( "#carSpeedLabel" ).text( ui.value );
	}
});
$( "#carSpeed" ).val($( "#slider-car-speed" ).slider( "value" ) );
$( "#carSpeedLabel" ).text($( "#slider-car-speed" ).slider( "value" ) );

$( "#slider-car-timer" ).slider({
	value: <?php echo $cur_carTimer; ?>,
	min: <?php echo $min_carTimer; ?>,
	max: <?php echo $max_carTimer; ?>,
	slide: function( event, ui ) {
		$( "#carTimer" ).val( ui.value + ' <?php _e('seconds', 'wpbootstrap'); ?>');
		$( "#carTimerLabel" ).text( ui.value + ' <?php _e('seconds', 'wpbootstrap'); ?>');
	}
});
$( "#carTimer" ).val($( "#slider-car-timer" ).slider( "value" ) + ' <?php _e('seconds', 'wpbootstrap'); ?>');
$( "#carTimerLabel" ).text($( "#slider-car-timer" ).slider( "value" ) + ' <?php _e('seconds', 'wpbootstrap'); ?>');

$( "#slider-build-timer" ).slider({
	value: <?php echo $cur_buildTimer; ?>,
	min: <?php echo $min_buildTimer; ?>,
	max: <?php echo $max_buildTimer; ?>,
	slide: function( event, ui ) {
		$( "#buildTimer" ).val( ui.value + ' <?php _e('seconds', 'wpbootstrap'); ?>');
		$( "#buildTimerLabel" ).text( ui.value + ' <?php _e('seconds', 'wpbootstrap'); ?>');
	}
});
$( "#buildTimer" ).val($( "#slider-build-timer" ).slider( "value" ) + ' <?php _e('seconds', 'wpbootstrap'); ?>');
$( "#buildTimerLabel" ).text($( "#slider-build-timer" ).slider( "value" ) + ' <?php _e('seconds', 'wpbootstrap'); ?>');

$( "#slider-bridge-pillar" ).slider({
	value: <?php echo $cur_bridgePillar; ?>,
	min: <?php echo $min_bridgePillar; ?>,
	max: <?php echo $max_bridgePillar; ?>,
	slide: function( event, ui ) {
		$( "#bridgePillar" ).val( ui.value);
		$( "#bridgePillarLabel" ).text( ui.value);
		
		for (var i = 1; i <= <?php echo $max_bridgePillar; ?>; i++) {
			if (i <= ui.value) {
				$('#bridgepointclass' + i).show();
			} else {
				$('#bridgepointclass' + i).hide();	
			}
		}
	}
});
$( "#bridgePillar" ).val($( "#slider-bridge-pillar" ).slider( "value" ));
$( "#bridgePillarLabel" ).text($( "#slider-bridge-pillar" ).slider( "value" ));

var bridges = <?php echo json_encode((array) $cur_bridgePoints); ?>

for (var i = 1; i <= <?php echo $max_bridgePillar; ?>; i++) {
	if (i <= <?php echo $cur_bridgePillar; ?>) {
		$('#bridgepointclass' + i).show();   		
	} else {
		$('#bridgepointclass' + i).hide();	
	}
}
for (i = 1; i <= <?php echo $cur_bridgePillar; ?>; i++) {    	
	$('#bridgePoint' + i).val(bridges[i-1]);
}


$( "#slider-bonus-number" ).slider({
	value: <?php echo $cur_bonusNumber; ?>,
	min: $( "#slider-number-range" ).slider( "values", 0 ),
	max: $( "#slider-number-range" ).slider( "values", 1 ),
	slide: function( event, ui ) {
		$( "#bonusNumber" ).val( ui.value);
		$( "#bonusNumberLabel" ).text( ui.value);
		$( "#slider-bonus-number" ).slider("option", "min", $( "#slider-number-range" ).slider( "values", 0 ));
		$( "#slider-bonus-number" ).slider("option", "max", $( "#slider-number-range" ).slider( "values", 1 ));
	}
});
$( "#bonusNumber" ).val($( "#slider-bonus-number" ).slider( "value" ));
$( "#bonusNumberLabel" ).text($( "#slider-bonus-number" ).slider( "value" ));

$( "#slider-number-bubbles" ).slider({
	value: <?php echo $cur_numberBubbles; ?>,
	min: <?php echo $min_numberBubbles; ?>,
	max: <?php echo $max_numberBubbles; ?>,
	slide: function( event, ui ) {
		$( "#numberBubbles" ).val( ui.value);
		$( "#numberBubblesLabel" ).text( ui.value);
	}
});
$( "#numberBubbles" ).val($( "#slider-number-bubbles" ).slider( "value" ));
$( "#numberBubblesLabel" ).text($( "#slider-number-bubbles" ).slider( "value" ));

});
</script>
<div class="row">
	<div class="span8">
		<div id="alertMessage">

		</div>
		<!-- action="<?php echo get_permalink(32); ?>" -->
		<form name="levelcreate" id="form" method="POST" action="">
			<div style="visibility:hidden; height:0px; width:0px;">
				<input type="text" name="carSpeed" id="carSpeed">
				<input type="text" name="carTimer" id="carTimer">
				<input type="text" name="buildTimer" id="buildTimer">
				<input type="text" name="numbersBetween" id="numbersBetween">
				<input type="text" name="bonusNumber" id="bonusNumber">
				<input type="text" name="numberBubbles" id="numberBubbles">
				<input type="text" name="bubbleSpeed" id="bubbleSpeed">
				<input type="text" name="bridgePillar" id="bridgePillar">	
			</div>			
			<fieldset>
				<div class="row">
					<div class="span6">
						<div class="row">
							<div class="span4">
								<label for="levelName"><?php _e('Name', 'wpbootstrap'); ?></label>
								<input id="levelName" name="levelName" value="<?php echo $cur_name; ?>" class="span3" type="text">
							</div>
							<?php if ($level > -1): ?>
							<div class="span1">
								<label>ID</label>
								<span id="levelidfield" class="span1 uneditable-input"><?php echo '#' . $level; ?></span>
							</div>
						<?php endif; ?>
					</div>
					<label for="slider-car-speed"><?php _e('Speed of the car', 'wpbootstrap'); ?></label>
					<div class="span2" style="margin-left: 0; top: 5px;" id="slider-car-speed"></div>
					<div class="span2"><label type="text" name="carSpeedLabel" id="carSpeedLabel" style="top: 0px; width: 70px; border: 0; font-weight: bold;"></label></div><br /><br />

					<label for="slider-car-timer"><?php _e('Time for car to start', 'wpbootstrap'); ?></label>
					<div class="span2" style="margin-left: 0; top: 5px;" id="slider-car-timer"></div>
					<div class="span2"><label type="text" name="carTimerLabel" id="carTimerLabel" style="top: 0px; width: 90px; border: 0; font-weight: bold;"></label></div><br /><br />

					<label for="slider-build-timer"><?php _e('Building time', 'wpbootstrap'); ?></label>
					<div class="span2" style="margin-left: 0; top: 5px;" id="slider-build-timer"></div>
					<div class="span2"><label type="text" name="buildTimerLabel" id="buildTimerLabel" style="top: 0px; width: 90px; border: 0; font-weight: bold;"></label></div><br /><br />

					<label for="slider-number-range"><?php _e('Numbers in game', 'wpbootstrap'); ?></label>
					<div class="span2" style="margin-left: 0; top: 5px;" id="slider-number-range"></div>
					<div class="span2"><label type="text" name="numbersBetweenLabel" id="numbersBetweenLabel" style="top: 0px; width: 70px; border: 0; font-weight: bold;" /></div><br /><br />

					<label for="slider-bonus-number"><?php _e('Bonus number', 'wpbootstrap'); ?></label>
					<div class="span2" style="margin-left: 0; top: 5px;" id="slider-bonus-number"></div>
					<div class="span2"><label type="text" name="bonusNumberLabel" id="bonusNumberLabel" style="top: 0px; width: 70px; border: 0; font-weight: bold;" /></div><br /><br />

					<label for="slider-number-bubbles"><?php _e('Number bubbles', 'wpbootstrap'); ?></label>
					<div class="span2" style="margin-left: 0; top: 5px;" id="slider-number-bubbles"></div>
					<div class="span2"><label type="text" name="numberBubblesLabel" id="numberBubblesLabel" style="top: 0px; width: 70px; border: 0; font-weight: bold;"></label></div><br /><br />

					<label for="slider-bubble-speed"><?php _e('Speed of the bubbles', 'wpbootstrap'); ?></label>
					<div class="span2" style="margin-left: 0; top: 5px;" id="slider-bubble-speed"></div>
					<div class="span2"><label type="text" name="bubbleSpeedLabel" id="bubbleSpeedLabel" style="top: 0px; width: 70px; border: 0; font-weight: bold;" /></div><br /><br />


					<label for="fractions"><?php _e('Fractions', 'wpbootstrap'); ?></label>
					<div class="btn-group" data-toggle="buttons-checkbox" id="fractions" name="fractions">
						<?php
						$fractions = $wpdb->get_col( $wpdb->prepare( 
							"
							SELECT denominator
							FROM $wpdb->fraction
							"
							) );

						foreach ($fractions as $fraction) {
							echo '<button type="button" class="btn" value="' . $fraction . '" name="fraction[]" disabled><sup>x</sup>&frasl;<sub>' . $fraction . '</sub></button>';
						}
						?>
					</div>
					<br /><br />
					<label for="groups"><?php _e('Groups', 'wpbootstrap'); ?></label>
					<?php
					if ($level > -1) {
						$group_ids = $wpdb->get_col( $wpdb->prepare( 
							"
							SELECT relationships_term_taxonomy_id
							FROM $wpdb->group_level
							WHERE level_ID = %d
							", $level
							) );
					}

					$postids = $wpdb->get_results( $wpdb->prepare( 
						"
						SELECT DISTINCT t.term_id, t.name
						FROM $wpdb->term_taxonomy taxo
						INNER JOIN $wpdb->terms t ON taxo.term_id = t.term_id
						INNER JOIN $wpdb->term_relationships rs ON t.term_id = rs.term_taxonomy_id
						WHERE taxo.taxonomy = 'user-group' AND rs.object_id = " . get_current_user_id() . "
						ORDER BY t.term_id
						"
						) );
					if ($level > -1) {
						echo '<select name="groups[]" id="groups" multiple="multiple" disabled>';
					} else {
						echo '<select name="groups[]" id="groups" multiple="multiple">';
					}
					foreach ($postids as $group) {
						echo '<option value="' . $group->term_id . '"';
						if ($level > -1) {
							foreach ($group_ids as $gr) {
								if ($gr == $group->term_id) {
									echo ' selected';
									break;	
								}
							}	
						}
						echo '>';
						echo $group->name;
						echo '</option>';
					}
					echo '</select>'; 
					?>
					<br /><br />

					<label for="slider-bridge-pillar"><?php _e('Length of the bridge', 'wpbootstrap'); ?></label>
					<div class="span2" style="margin-left: 0; top: 5px;" name="bridgePillarInput" id="slider-bridge-pillar"></div>
					<div class="span2"><label type="text" name="bridgePillarLabel" id="bridgePillarLabel" style="top: 0px; width: 70px; border: 0; font-weight: bold;"></label></div><br /><br />

					<label for="bridgePoints"><?php _e('Points for every bridge pillar to build', 'wpbootstrap'); ?></label>
					<div id="bridgePoints">
						<?php
						for ($i = 1; $i <= $max_bridgePillar; $i++) {
							if ($i % 5 == 1) {
								echo '<p>';
							}
							echo '<div class="input-prepend" id="bridgepointclass' . $i . '">';
							echo '<span class="add-on">' . $i . '</span>';
							echo '<input class="span1" name="bridgePointName' . $i . '" id="bridgePoint' . $i . '" type="text"></div>';

							if ($i % 5 == 0) {
								echo '</p>';
							}	
						}
						?>
					</div>
					<br />
				</div>
				<div class="span2 pull-right">
					<input type="submit" name="submit" id="submit" value="<?php _e('Save', 'wpbootstrap'); ?>" class="span1 btn btn-primary pull-right" style="margin: 10px;">
					<?php if ($level > -1): ?>
					<a type="text" class="btn btn-danger pull-right" style="margin: 10px;" id="delete-level" href="#myModal" data-toggle="modal"><i class="icon-trash icon-white"></i></a>
				<?php endif; ?>
				<div class="row">
					<div class="span2 pull-right">
						<a type="text" style="margin: 10px;" class="btn btn-info pull-right" href="#playModal" data-toggle="modal"><?php _e('Try', 'wpbootstrap'); ?></a>
					</div>
				</div>		
			</div>
		</fieldset>
	</form>

	<!-- Delete modal -->
		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo __('Deleting level', 'wpbootstrap') . ' ' . $level; ?></h3>
			</div>
			<div class="modal-body" id="myModalBody">
				<p><?php echo __('This will delete the level and related to this level as scores and revisions.', 'wpbootstrap') . '<br /><br />' . __('This can not be restored.', 'wpbootstrap'); ?></p>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true"><?php _e('Cancel', 'wpbootstrap'); ?></button>
				<button class="btn btn-danger" id="delete_button"><?php _e('Delete', 'wpbootstrap'); ?></button>
			</div>
		</div>
	</div>
</div>

<!-- Play modal -->
<div id="playModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel"><?php _e('Try level', 'wpbootstrap'); ?></h3>
	</div>
	<div class="modal-body" id="myModalBody">
		<?php get_template_part('try-level'); ?>
	</div>
	<div class="modal-footer">
		<p class="pull-left">Info</p>
	</div>
</div>


<?php
if (isset($_POST['submit'])) {
	if ($_POST['numberBubbles'] < $min_numberBubbles || $_POST['numberBubbles'] > $max_numberBubbles) {	
		echo "fejl: " . $_POST['numberBubbles'];
		$error = true;
	}
	
	if ($_POST['carSpeed'] < $min_carSpeed || $_POST['carSpeed'] > $max_carSpeed) {
		$error = true;
	}
	
	if ($_POST['carTimer'] < $min_carTimer || $_POST['carTimer'] > $max_carTimer) {
		$error = true;	
	}
	
	if ($_POST['buildTimer'] < $min_buildTimer || $_POST['buildTimer'] > $max_buildTimer) {
		$error = true;
	}
	
	$numbersBetween = explode(':', $_POST['numbersBetween']);
	if ($numbersBetween[0] < $min_numberRange || $numbersBetween[0] > $max_numberRange) {
		$error = true;	
	}
	if ($numbersBetween[1] < $min_numberRange || $numbersBetween[1] > $max_numberRange) {
		$error = true;	
	}
	
	if ($numbersBetween[1] > $numbersBetween[0]) {
		$error = true;	
	}
	
	if ($_POST['bonusNumber'] < $min_numberRange || $_POST['bonusNumber'] > $max_numberRange) {
		$error = true;	
	}
	
	if ($_POST['numberBubbles'] < $min_numberBubbles || $_POST['numberBubbles'] > $max_numberBubbles) {
		$error = true;	
	}
	
	$speed = explode(':', $_POST['bubbleSpeed']);
	if ($speed[0] < $min_speed || $speed[0] > $max_speed) {
		$error = true;	
	}
	if ($speed[1] < $min_speed || $speed[1] > $max_speed) {
		$error = true;	
	}
	
	if (get_current_user_id() <= 0) {
		$error = true;	
	}
	
	if ($level == -1 && $_POST['groups'] == null) {
		$error = true;
	}
	
	if ($_POST['bridgePillar'] < $min_bridgePillar || $_POST['bridgePillar'] > $max_bridgePillar) {
		$error = true;	
	}

	for($i = 1; $i <= $_POST['bridgePillar']; $i++) {
		if (!preg_match('/^\d+$/', $_POST['bridgePointName' . $i]) || $_POST['bridgePointName' . $i] < $min_points || $_POST['bridgePointName' . $i] > $max_points) {
			$error = true;
			break;
		}
	}
	
	if ($level == -1 && count($_POST['groups']) < 1) {
		$error = true;	
	}

	if (strlen($_POST['levelName']) < 1) {
		$error = true;
	}
	
	if ($error) {
		?>
		<script>
			$('#alertMessage').html(function() {
			 	return '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong><?php _e("There was an error creating the level. Please try again", "wpbootstrap"); ?></strong></div>'
		 	});	
		</script>
		<?php		
		_e('There was an error creating the level. Please try again', 'wpbootstrap');	
		
	} else if ($level == -1) {
		$wpdb->query( $wpdb->prepare( 
			"
			INSERT INTO $wpdb->level
			( name, car_time, build_time, min_number, max_number, min_speed, max_speed, car_speed, bonus_number, number_bubbles )
			VALUES ( %s, %d, %d, %d, %d, %d, %d, %d, %d, %d )
			", array( 
				$_POST['levelName'], 
				$_POST['carTimer'], 
				$_POST['buildTimer'],
				$numbersBetween[0],
				$numbersBetween[1],
				$speed[0],
				$speed[1],
				$_POST['carSpeed'],
				$_POST['bonusNumber'],
				$_POST['numberBubbles']
				)
			) );
		
		$lastid = $wpdb->insert_id;
		
		for ($i = 1; $i <= $_POST['bridgePillar']; $i++) {		
			$wpdb->query( $wpdb->prepare( 
				"
				INSERT INTO $wpdb->bridge
				( level_ID, number_pillar, points )
				VALUES ( %d, %d, %d )
				", array(
					$lastid,
					$i - 1,
					$_POST['bridgePointName' . $i]
					)
				) );
		}
		
		foreach ($_POST['groups'] as $group) {
			$wpdb->query( $wpdb->prepare( 
				"
				INSERT INTO $wpdb->group_level
				( relationships_object_id, relationships_term_taxonomy_id, level_ID )
				VALUES ( %d, %d, %d )
				", array(
					get_current_user_id(),
					$group,
					$lastid
					)
				) );
		}
		?>
		<script>
			$('#alertMessage').html(function() {
			 	return '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><strong><?php _e("Level created", "wpbootstrap"); ?></strong></div>'
			 });	
		</script>
		<?php
		_e("Level created", "wpbootstrap");		
	} else {
		$wpdb->query( $wpdb->prepare( 
			"
			INSERT INTO $wpdb->level
			( name, car_time, build_time, min_number, max_number, car_speed, bonus_number, number_bubbles )
			VALUES ( %s, %d, %d, %d, %d, %d, %d, %d )
			", array( 
				$_POST['levelName'], 
				$_POST['carTimer'], 
				$_POST['buildTimer'],
				$numbersBetween[0],
				$numbersBetween[1],
				$_POST['carSpeed'],
				$_POST['bonusNumber'],
				$_POST['numberBubbles']
				)
			) );
		
		$lastid = $wpdb->insert_id;
		
		$wpdb->query( $wpdb->prepare( 
			"
			INSERT INTO $wpdb->level_revision
			( level_revision, level_ID )
			VALUES ( %d, %d )
			", array( 
				$level,
				$lastid
				)
			) );
		
		for ($i = 1; $i <= $_POST['bridgePillar']; $i++) {		
			$wpdb->query( $wpdb->prepare( 
				"
				INSERT INTO $wpdb->bridge
				( level_ID, number_pillar, points )
				VALUES ( %d, %d, %d )
				", array(
					$lastid,
					$i - 1,
					$_POST['bridgePointName' . $i]
					)
				) );
		}
		
		foreach ($group_ids as $group) {
			$wpdb->query( $wpdb->prepare( 
				"
				INSERT INTO $wpdb->group_level
				( relationships_object_id, relationships_term_taxonomy_id, level_ID )
				VALUES ( %d, %d, %d )
				", array(
					get_current_user_id(),
					$group,
					$lastid
					)
				) );
		}
?>
		<script>
			$('#alertMessage').html(function() {
			 	return '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><strong><?php printf(__("An revision of level %d was added.", "wpbootstrap"), $level); ?></strong></div>'
			 });	
		</script>
		<?php
		printf(__("An revision of level %d was added.", "wpbootstrap"), $level);
	}
}
?>