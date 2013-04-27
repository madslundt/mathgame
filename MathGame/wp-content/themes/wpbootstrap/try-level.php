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
        height: window.innerHeight * 0.7,
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
        var carTimer = 1;
        var buildTimer = 3;
        var bonusNumber = 2;
        var numberBubbles = 2;
        var bridgeLength = 2;
        var minSpeed = 1;
        var maxSpeed = 2;
        var minNumber = 1;
        var maxNumber = 3;
        var carSpeed = 2;

        if (!isNaN($( "#slider-car-timer" ).slider( "value" )) || $( "#slider-car-timer" ).slider( "value" ) != null || $( "#slider-car-timer" ).slider( "value" ) != "") {
            carTimer = parseInt($( "#slider-car-timer" ).slider( "value" ));
        }
        if (!isNaN($( "#slider-build-timer" ).slider( "value" )) || $( "#slider-build-timer" ).slider( "value" ) != null || $( "#slider-build-timer" ).slider( "value" ) != "") {
            buildTimer = parseInt($( "#slider-build-timer" ).slider( "value" ));
        }
        if (!isNaN($( "#slider-bonus-number" ).slider( "value" )) || $( "#slider-bonus-number" ).slider( "value" ) != null || $( "#slider-bonus-number" ).slider( "value" ) != "") {
            bonusNumber = parseInt($( "#slider-bonus-number" ).slider( "value" ));
        }
        if (!isNaN($( "#slider-number-bubbles" ).slider( "value" )) || $( "#slider-number-bubbles" ).slider( "value" ) != null || $( "#slider-number-bubbles" ).slider( "value" ) != "") {
            numberBubbles = parseInt($( "#slider-number-bubbles" ).slider( "value" ));
        }
        if (!isNaN($( "#slider-bridge-pillar" ).slider( "value" )) || $( "#slider-bridge-pillar" ).slider( "value" ) != null || $( "#slider-bridge-pillar" ).slider( "value" ) != "") {
            bridgeLength = parseInt($( "#slider-bridge-pillar" ).slider( "value" ));
        }
        if (!isNaN($( "#slider-bubble-speed" ).slider( "values", 0 )) || $( "#slider-bubble-speed" ).slider( "values", 0 ) != null || $( "#slider-bubble-speed" ).slider( "values", 0 ) != "") {
            minSpeed = parseInt($( "#slider-bubble-speed" ).slider( "values", 0 ));
        }
        if (!isNaN($( "#slider-bubble-speed" ).slider( "values", 1 )) || $( "#slider-bubble-speed" ).slider( "values", 1 ) != null || $( "#slider-bubble-speed" ).slider( "values", 1 ) != "") {
            maxSpeed = parseInt($( "#slider-bubble-speed" ).slider( "values", 1 ));
        }
        if (!isNaN($( "#slider-number-range" ).slider( "values", 0 )) || $( "#slider-number-range" ).slider( "values", 0 ) != null || $( "#slider-number-range" ).slider( "values", 0 ) != "") {
            minNumber = parseInt($( "#slider-number-range" ).slider( "values", 0 ));
        }
        if (!isNaN($( "#slider-number-range" ).slider( "values", 1 )) || $( "#slider-number-range" ).slider( "values", 1 ) != null || $( "#slider-number-range" ).slider( "values", 1 ) != "") {
            maxNumber = parseInt($( "#slider-number-range" ).slider( "values", 1 ));
        }
        if (!isNaN($( "#slider-car-speed" ).slider( "value" )) || $( "#slider-car-speed" ).slider( "value" ) != null || $( "#slider-car-speed" ).slider( "value" ) != "") {
            carSpeed = parseInt($( "#slider-car-speed" ).slider( "value" ));
        }
        console.log(carTimer);
        console.log(buildTimer);
        console.log(bonusNumber);
        console.log(numberBubbles);
        console.log(bridgeLength);
        console.log(minSpeed);
        console.log(maxSpeed);
        console.log(minNumber);
        console.log(maxNumber);
        console.log(carSpeed);
        
        // Send to MainCamera car_time, build_time, bonus_number, number_bubbles, bridge
        u.getUnity().SendMessage("MainCamera", "getCarTime", carTimer);
        u.getUnity().SendMessage("MainCamera", "getBuildTime", buildTimer);
        u.getUnity().SendMessage("MainCamera", "getBonusNumber", bonusNumber);
        u.getUnity().SendMessage("MainCamera", "getNumberBubbles", numberBubbles);

        u.getUnity().SendMessage("MainCamera", "setBridgeLength", bridgeLength);
        for (var i = 1; i <= bridgeLength; i++) {
            if (isNaN($('#bridgePoint' + i).val()) || $('#bridgePoint' + i).val() == null || $('#bridgePoint' + i).val() == "") {
                u.getUnity().SendMessage("MainCamera", "addBridgePillar", 1);
            } else {
                u.getUnity().SendMessage("MainCamera", "addBridgePillar", parseInt($('#bridgePoint' + i).val()));
            }
        }

        // Send to NumberBubble min_number, max_number, min_speed, max_speed
        u.getUnity().SendMessage("NumberBubble", "setMinSpeed", minSpeed);
        u.getUnity().SendMessage("NumberBubble", "setMaxSpeed", maxSpeed);
        u.getUnity().SendMessage("NumberBubble", "setMinNumber", minNumber);
        u.getUnity().SendMessage("NumberBubble", "setMaxNumber", maxNumber);             

        // Car speed
        u.getUnity().SendMessage("CustomCar", "setCarSpeed", carSpeed + 2);
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