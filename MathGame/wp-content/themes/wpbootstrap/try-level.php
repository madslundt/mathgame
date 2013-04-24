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
        width: 900, 
        height: 600,
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
        u.getUnity().SendMessage("MainCamera", "getCarTime", 30);
        u.getUnity().SendMessage("MainCamera", "getBuildTime", 30);
        u.getUnity().SendMessage("MainCamera", "getBonusNumber", 10);
        u.getUnity().SendMessage("MainCamera", "getNumberBubbles", 5);
        u.getUnity().SendMessage("MainCamera", "setBridgeLength", 5);
				
        u.getUnity().SendMessage("MainCamera", "addBridgePillar", 10);
        u.getUnity().SendMessage("MainCamera", "addBridgePillar", 10);
        u.getUnity().SendMessage("MainCamera", "addBridgePillar", 10);
        u.getUnity().SendMessage("MainCamera", "addBridgePillar", 10);
        u.getUnity().SendMessage("MainCamera", "addBridgePillar", 10);
				
        // Send to NumberBubble min_number, max_number, min_speed, max_speed

        //u.getUnity().SendMessage("MainCamera", "getLevel", <?php echo $level ?>);
        //u.getUnity().SendMessage("MainCamera", "getUserId", <?php echo get_current_user_id(); ?>);
			
    }
    function UnityFinished(points, errors, playtime, finished) {

        console.log("Points: " + points + "\nErrors: " + errors + "\nTime: " + playtime + "\nFinished: " + finished);
        /*jQuery.ajax({  
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
        }*/
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