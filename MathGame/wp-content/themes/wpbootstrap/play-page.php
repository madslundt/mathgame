<?php
if (!is_user_logged_in())
{
    wp_redirect(get_option('siteurl'));
    exit;
}
global $current_user;
get_currentuserinfo();
//echo $current_user->user_level; 
?>
<?php
$level = 0;
if (isset($_GET['level']))
{
    $level = $_GET['level'];
    $curlevel = $wpdb->get_row($wpdb->prepare(
                    "
			SELECT *
			FROM $wpdb->level
			WHERE ID = %d
			", $level
            ));
    $curlevelBridge = $wpdb->get_col($wpdb->prepare(
                    "
			SELECT points
			FROM $wpdb->bridge
			WHERE level_ID = %d
			ORDER BY number_pillar
			", $level
            ));
}
else
{
    exit;
}

$user_agent = $_SERVER['HTTP_USER_AGENT'];
$game = "/webplayer/webplayer.unity3d";
if (preg_match('/Chrome/i', $user_agent)) {
    $game = "/chromeWebplayer/chromeWebplayer.unity3d";
}
?>
<script type="text/javascript">
    
    var unityObjectUrl = "http://webplayer.unity3d.com/download_webplayer-3.x/3.0/uo/UnityObject2.js";
    if (document.location.protocol == 'https:')
        unityObjectUrl = unityObjectUrl.replace("http://", "https://ssl-");
    document.write('<script type="text\/javascript" src="' + unityObjectUrl + '"><\/script>');
    
</script>
<script type="text/javascript">
    var config = {
        width: window.innerWidth * 0.8, 
        height: window.innerHeight * 0.8,
        params: { enableDebugging:"0" }
				
    };
    var u = new UnityObject2(config);
    jQuery(function() {

        if (!rated)
            $('#level-rate').hide();
				
        var $missingScreen = jQuery(".unityplayer").find(".missing");
        var $brokenScreen = jQuery(".unityplayer").find(".broken");
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
				
        u.initPlugin(jQuery(".unityplayer")[0], "<?php print THEMEROOT; echo $game; ?>");
    });
    function UnityIsReady()
    {
        // Send to MainCamera car_time, build_time, bonus_number, number_bubbles, bridge
        u.getUnity().SendMessage("MainCamera", "getCarTime", <?php echo $curlevel->car_time; ?>);
        u.getUnity().SendMessage("MainCamera", "getBuildTime", <?php echo $curlevel->build_time; ?>);
        u.getUnity().SendMessage("MainCamera", "getBonusNumber", <?php echo $curlevel->bonus_number; ?>);
        u.getUnity().SendMessage("MainCamera", "getNumberBubbles", <?php echo $curlevel->number_bubbles; ?>);
        u.getUnity().SendMessage("MainCamera", "setBridgeLength", <?php echo count($curlevelBridge); ?>);
        <?php foreach ($curlevelBridge as $b) { ?>
            u.getUnity().SendMessage("MainCamera", "addBridgePillar", <?php echo $b; ?>);
        <?php } ?>
				
        // Send to NumberBubble min_number, max_number, min_speed, max_speed
        u.getUnity().SendMessage("NumberBubble", "setMinSpeed", <?php echo $curlevel->min_speed / 10; ?>);
        u.getUnity().SendMessage("NumberBubble", "setMaxSpeed", <?php echo $curlevel->max_speed / 10; ?>);
        u.getUnity().SendMessage("NumberBubble", "setMinNumber", <?php echo $curlevel->min_number; ?>);
        u.getUnity().SendMessage("NumberBubble", "setMaxNumber", <?php echo $curlevel->max_number; ?>);				

        // Car speed
        u.getUnity().SendMessage("CustomCar", "setCarSpeed", <?php echo $curlevel->car_speed + 2; ?>);
    }
                    function UnityFinished(points, errors, playtime, finished) {

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
                        
                        if (!rated) {
                            $('#level-rate').show();
                        }
                    }
</script>
<style type="text/css">
    .unityplayer {
        position: relative;
        float: left;
        left: 10%;
        margin-left:auto;
        margin-right:auto;
        padding-bottom: 5%;
        height: 70%;
        width: 90%;
    }
</style>
<div class="unityplayer">
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

<div class="span4">
    <p class="header"><span>Unity Web Player | </span>MathGame</p>
    <p class="footer">&laquo; created with <a href="http://unity3d.com/unity/" title="Go to unity3d.com">Unity</a> &raquo;</p>
</div>