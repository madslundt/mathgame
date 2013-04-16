<?php
	if (!is_user_logged_in()) {
		wp_redirect(get_option('siteurl')); exit;
	}
	global $current_user;
	get_currentuserinfo();
	//echo $current_user->user_level; 
?>
<?php 
	$level = 0;
	if ($_GET['level']) {
		$level = $_GET['level'];
		$curlevel = $wpdb->get_row( $wpdb->prepare( 
			"
			SELECT *
			FROM $wpdb->_level
			WHERE ID = %d
			", $level
		) );
		$curlevelBridge = $wpdb->get_col( $wpdb->prepare( 
			"
			SELECT points
			FROM $wpdb->_bridge
			WHERE level_ID = %d
			ORDER BY number_pillar
			", $level
		) );
	}
?>
	<script type="text/javascript">
		<!--
		var unityObjectUrl = "http://webplayer.unity3d.com/download_webplayer-3.x/3.0/uo/UnityObject2.js";
		if (document.location.protocol == 'https:')
			unityObjectUrl = unityObjectUrl.replace("http://", "https://ssl-");
		document.write('<script type="text\/javascript" src="' + unityObjectUrl + '"><\/script>');
		-->
		</script>
		<script type="text/javascript">
		<!--
			var config = {
				width: window.innerWidth * 0.8, 
				height: window.innerHeight * 0.7,
				params: { enableDebugging:"0" }
				
			};
			var u = new UnityObject2(config);
			jQuery(function() {

				if (!rated)
					$('#level-rate').hide();
				
				var $missingScreen = jQuery("#unityPlayer").find(".missing");
				var $brokenScreen = jQuery("#unityPlayer").find(".broken");
				$missingScreen.hide();
				$brokenScreen.hide();
				
				u.observeProgress(function (progress) {
					switch(progress.pluginStatus) {
						case "broken":
							$brokenScreen.find("a").click(function (e) {
								e.stopPropagation();
								e.preventDefault();
								u.installPlugin();
								return false;
							});
							$brokenScreen.show();
						break;
						case "missing":
							$missingScreen.find("a").click(function (e) {
								e.stopPropagation();
								e.preventDefault();
								u.installPlugin();
								return false;
							});
							$missingScreen.show();
						break;
						case "installed":
							$missingScreen.remove();
						break;
						case "first":
						break;
					}
				});
				
				u.initPlugin(jQuery("#unityPlayer")[0], "<?php bloginfo('template_url'); ?>/webplayer/webplayer.unity3d");
			});
			function UnityIsReady()
			{
				// Send to MainCamera car_time, build_time, bonus_number, number_bubbles, bridge
				u.getUnity().SendMessage("MainCamera", "getCarTime", <?php echo $curlevel->car_time ?>);
				u.getUnity().SendMessage("MainCamera", "getBuildTime", <?php echo $curlevel->build_time ?>);
				u.getUnity().SendMessage("MainCamera", "getBonusNumber", <?php echo $curlevel->bonus_number ?>);
				u.getUnity().SendMessage("MainCamera", "getNumberBubbles", <?php echo $curlevel->number_bubbles ?>);
				u.getUnity().SendMessage("MainCamera", "setBridgeLength", <?php echo count($curlevelBridge); ?>);
				<?php foreach ($curlevelBridge as $b) { ?>
					u.getUnity().SendMessage("MainCamera", "addBridgePillar", <?php echo $b; ?>);
				<?php } ?>
				
				// Send to NumberBubble min_number, max_number, min_speed, max_speed

				u.getUnity().SendMessage("MainCamera", "getLevel", <?php echo $level ?>);
				u.getUnity().SendMessage("MainCamera", "getUserId", <?php echo get_current_user_id(); ?>);
			
}
			function UnityFinished(points, errors, playtime, finished) {

				console.log("Points: " + points + "\nErrors: " + errors + "\nTime: " + playtime + "\nFinished: " + finished);
				jQuery.ajax({  
					type: 'POST',
					cache: false,  
					url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",  
					data: {  
						action: 'addScoreToLevel',  
						level: <?php echo $level; ?>,
						point: points,
						error: errors,
						time: playtime,
						finish: finished
					},
					success: function(data, textStatus, XMLHttpRequest) {
					
					},  
					error: function(MLHttpRequest, textStatus, errorThrown) {
						alert("<?php _e('Could not upload score.', 'wpbootstrap'); ?>");  
					}  
				 });
				if (!rated)
					$('#level-rate').show();
			}
		-->
		</script>
		<style type="text/css">
		<!--
		.container-game {
			margin: 0;
			padding: 0;
		}
		p.header {
			font-size: small;
		}
		p.header span {
			font-weight: bold;
		}
		p.footer {
			font-size: x-small;
		}
		div.content {
			margin: auto;
			width: 960px;
		}
		div.broken,
		div.missing {
			margin: auto;
			position: relative;
			top: 50%;
			width: 193px;
		}
		div.broken a,
		div.missing a {
			height: 63px;
			position: relative;
			top: -31px;
		}
		div.broken img,
		div.missing img {
			border-width: 0px;
		}
		div.broken {
			display: none;
		}
		#unityPlayer {
			cursor: default;
			left: 5%;
			float: left;
			margin: 0;
			margin-left: 10px;
		}
		-->
		</style>
		<div class="container-game pull-left">
			<div id="unityPlayer">
				<div class="missing">
					<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!">
						<img alt="Unity Web Player. Install now!" src="http://webplayer.unity3d.com/installation/getunity.png" width="193" height="63" />
					</a>
				</div>
				<div class="broken">
					<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now! Restart your browser after install.">
						<img alt="Unity Web Player. Install now! Restart your browser after install." src="http://webplayer.unity3d.com/installation/getunityrestart.png" width="193" height="63" />
					</a>
				</div>
			</div>
		</div>

		<p class="header"><span>Unity Web Player | </span>MathGame</p>
		<p class="footer">&laquo; created with <a href="http://unity3d.com/unity/" title="Go to unity3d.com">Unity</a> &raquo;</p>