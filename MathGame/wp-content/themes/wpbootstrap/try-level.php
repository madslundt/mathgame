<?php
if (!is_user_logged_in())
{
    wp_redirect(get_option('siteurl'));
    exit;
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
    var config = {
        width: window.innerWidth * 0.75, 
        height: window.innerHeight * 0.6,
        params: { enableDebugging:"0" }
                
    };
    var u = new UnityObject2(config);
    jQuery(function() {                
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
                
        u.initPlugin(jQuery("#unityPlayer")[0], "<?php print THEMEROOT; ?>/webplayer/webplayer.unity3d");
    });
    function UnityIsReady()
    {
        // Send to MainCamera car_time, build_time, bonus_number, number_bubbles, bridge
        u.getUnity().SendMessage("MainCamera", "getCarTime", parseInt($( "#slider-car-timer" ).slider( "value" )));
        u.getUnity().SendMessage("MainCamera", "getBuildTime", parseInt($( "#slider-build-timer" ).slider( "value" )));
        u.getUnity().SendMessage("MainCamera", "getBonusNumber", parseInt($( "#slider-bonus-number" ).slider( "value" )));
        u.getUnity().SendMessage("MainCamera", "getNumberBubbles", parseInt($( "#slider-number-bubbles" ).slider( "value" )));
        var bridgeLength = parseInt($( "#slider-bridge-pillar" ).slider( "value" ));
        u.getUnity().SendMessage("MainCamera", "setBridgeLength", bridgeLength);
        for (var i = 1; i <= bridgeLength; i++) {
            u.getUnity().SendMessage("MainCamera", "addBridgePillar", parseInt($('#bridgePoint' + i).val()));
        }

        // Send to NumberBubble min_number, max_number, min_speed, max_speed
        u.getUnity().SendMessage("NumberBubble", "setMinSpeed", parseInt($( "#slider-bubble-speed" ).slider( "values", 0 )));
        u.getUnity().SendMessage("NumberBubble", "setMaxSpeed", parseInt($( "#slider-bubble-speed" ).slider( "values", 1 )));
        u.getUnity().SendMessage("NumberBubble", "setMinNumber", parseInt($( "#slider-number-range" ).slider( "values", 0 )));
        u.getUnity().SendMessage("NumberBubble", "setMaxNumber", parseInt($( "#slider-number-range" ).slider( "values", 1 )));             

        // Car speed
        u.getUnity().SendMessage("CustomCar", "setCarSpeed", (parseInt($( "#slider-car-speed" ).slider( "value" )) + 2));
    }

    function UnityFinished(points, errors, playtime, finished) {
    }
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