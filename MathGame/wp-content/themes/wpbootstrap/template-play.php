<?php
/*
Template Name: Play game template
 */
get_header();
$avgRating = 0;
$ajax_nonce = wp_create_nonce("asdk121das%€!");
if ($_GET['level'])
{
    $level = $_GET['level'];
}

if (have_posts()) : while (have_posts()) : the_post();
        if (!isset($_GET['level'])) {
        ?>
        <legend><?php the_title(); ?></legend>
        <?php } the_content(); ?>

        <?php
        if ($level > 0)
        {
            $curlevel = $wpdb->get_row($wpdb->prepare(
                "
                SELECT *
                FROM $wpdb->level
                WHERE ID = %d
                ", $level
            ));

            $avgRating = $wpdb->get_var($wpdb->prepare(
                    "
                    SELECT AVG(rating)
                    FROM $wpdb->level_rating
                    WHERE level_ID = %d
                    ", $level
                ));

                $userRating = $wpdb->get_var($wpdb->prepare(
                    "
                    SELECT rating
                    FROM $wpdb->level_rating
                    WHERE level_ID = %d AND user_ID = %d
                    ", $level, get_current_user_id()
                ));
            ?>
            <?php
            if ($userRating > 0) { ?>
                <script>var rated = true;</script>
            <?php 
            } else { ?>
                <script>var rated = false;</script>
            <?php } ?>
            <legend>
                <a href="<?php echo the_permalink(); ?>" id="nolink">&laquo; <?php the_title(); ?></a>

                <div class="pull-right">
                    <div class="span3 pull-right">
                        <?php echo __('Numbers', 'wpbootstrap') . ': <strong>' . $curlevel->min_number . ' - ' . $curlevel->max_number . '</strong>'; ?>
                    </div>
                    <div class="span3 pull-right">
                        <?php echo __('Car speed', 'wpbootstrap') . ': <strong>' . $curlevel->car_speed . '</strong>'; ?>
                    </div>
                    <div class="span3 pull-right">
                        <?php echo __('Bonus number', 'wpbootstrap') . ': <strong>' . $curlevel->bonus_number . '</strong>'; ?>
                    </div>
                    <div class="span2">
                        <div class="rating" id="level-rate" data-average="<?php echo ($avgRating == null) ? 0 : $avgRating; ?>" data-id="1"></div>
                    </div>
                </div>              
            </legend>
            <div class="row">
                <?php
                get_template_part('play-page');
        }
        else
        {
            get_template_part('level-choose');
        }
        ?>
    <?php endwhile;
else: ?>
        <p><?php _e('Sorry, this page does not exist.'); ?></p>
<?php endif; ?>	
</div>

<script>
    $(document).ready(function(){
        $(".rating").jRating({
            step:true,
            showRateInfo: false,
            isDisabled: rated,
            length: 5,
            rateMax: 5,
            decimalLength: 0,
            onSuccess : function(){
                jSuccess('Success : your rate has been saved :)',{
                    HorizontalPosition:'center',
                    VerticalPosition:'top'
                });
            },
            onError : function(){
                jError('Error : please retry');
            }
        });
    });


    (function($) {
    $.fn.jRating = function(op) {
        var defaults = {
            /** String vars **/
            bigStarsPath : "<?php print IMAGES; ?>/stars.png", // path of the icon stars.png
            smallStarsPath : "<?php print IMAGES; ?>/small.png", // path of the icon small.png
            //phpPath : "<?php echo get_template_directory_uri() . '/jRating.php'; ?>", // path of the php file jRating.php
            type : 'big', // can be set to 'small' or 'big'

            /** Boolean vars **/
            step:false, // if true,  mouseover binded star by star,
            isDisabled:false,
            showRateInfo: true,
            canRateAgain : false,

            /** Integer vars **/
            length:5, // number of star to display
            decimalLength : 0, // number of decimals.. Max 3, but you can complete the function 'getNote'
            rateMax : 20, // maximal rate - integer from 0 to 9999 (or more)
            rateInfosX : -45, // relative position in X axis of the info box when mouseover
            rateInfosY : 5, // relative position in Y axis of the info box when mouseover
            nbRates : 1,

            /** Functions **/
            onSuccess : null,
            onError : null
        }; 

        if(this.length>0)
            return this.each(function() {
                /*vars*/
                var opts = $.extend(defaults, op),    
                newWidth = 0,
                starWidth = 0,
                starHeight = 0,
                bgPath = '',
                hasRated = false,
                globalWidth = 0,
                nbOfRates = opts.nbRates;

                if($(this).hasClass('jDisabled') || opts.isDisabled)
                    var jDisabled = true;
                else
                    var jDisabled = false;

                getStarWidth();
                $(this).height(starHeight);

                var average = parseFloat($(this).attr('data-average')), // get the average of all rates
                idBox = parseInt($(this).attr('data-id')), // get the id of the box
                widthRatingContainer = starWidth*opts.length, // Width of the Container
                widthColor = average/opts.rateMax*widthRatingContainer, // Width of the color Container

                quotient = 
                    $('<div>', 
                {
                    'class' : 'jRatingColor',
                    css:{
                        width:widthColor
                    }
                }).appendTo($(this)),

            average = 
                $('<div>', 
            {
                'class' : 'jRatingAverage',
                css:{
                    width:0,
                    top:- starHeight
                }
            }).appendTo($(this)),

            jstar =
                $('<div>', 
            {
                'class' : 'jStar',
                css:{
                    width:widthRatingContainer,
                    height:starHeight,
                    top:- (starHeight*2),
                    background: 'url('+bgPath+') repeat-x'
                }
            }).appendTo($(this));
    

            $(this).css({width: widthRatingContainer,overflow:'hidden',zIndex:1,position:'relative'});

            if(!jDisabled)
                $(this).unbind().bind({
                    mouseenter : function(e){
                        var realOffsetLeft = findRealLeft(this);
                        var relativeX = e.pageX - realOffsetLeft;
                        if (opts.showRateInfo)
                            var tooltip = 
                            $('<p>',{
                            'class' : 'jRatingInfos',
                            html : getNote(relativeX)+' <span class="maxRate">/ '+opts.rateMax+'</span>',
                            css : {
                                top: (e.pageY + opts.rateInfosY),
                                left: (e.pageX + opts.rateInfosX)
                            }
                        }).appendTo('body').show();
                },
                mouseover : function(e){
                    $(this).css('cursor','pointer');    
                },
                mouseout : function(){
                    $(this).css('cursor','default');
                    if(hasRated) average.width(globalWidth);
                    else average.width(0);
                },
                mousemove : function(e){
                    var realOffsetLeft = findRealLeft(this);
                    var relativeX = e.pageX - realOffsetLeft;
                    if(opts.step) newWidth = Math.floor(relativeX/starWidth)*starWidth + starWidth;
                    else newWidth = relativeX;
                    average.width(newWidth);                    
                    if (opts.showRateInfo)
                        $("p.jRatingInfos")
                    .css({
                        left: (e.pageX + opts.rateInfosX)
                    })
                    .html(getNote(newWidth) +' <span class="maxRate">/ '+opts.rateMax+'</span>');
                },
                mouseleave : function(){
                    $("p.jRatingInfos").remove();
                },
                click : function(e){
                    var element = this;
            
                    /*set vars*/
                    hasRated = true;
                    globalWidth = newWidth;
                    nbOfRates--;
            
                    if(!opts.canRateAgain || parseInt(nbOfRates) <= 0) $(this).unbind().css('cursor','default').addClass('jDisabled');
            
                    if (opts.showRateInfo) $("p.jRatingInfos").fadeOut('fast',function(){$(this).remove();});
                    e.preventDefault();
                    var rate = getNote(newWidth);
                    average.width(newWidth);
                    <?php if (isset($level)) { ?>
                        jQuery.ajax({  
                            type: 'POST',
                            cache: false,  
                            url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",  
                            data: {  
                                action: 'addRatingToLevel',
                                security: '<?php echo $ajax_nonce; ?>',
                                level: <?php echo isset($level) ? $level : 0; ?>,
                                rating: rate
                            },
                            success: function(data, textStatus, XMLHttpRequest) {
                    
                            },  
                            error: function(MLHttpRequest, textStatus, errorThrown) {
                                alert("<?php _e('Could not rate the level.', 'wpbootstrap'); ?>");  
                            }  
                        });
                    <?php } ?>
                }
            });

            function getNote(relativeX) {
                var noteBrut = parseFloat((relativeX*100/widthRatingContainer)*opts.rateMax/100);
                switch(opts.decimalLength) {
                    case 1 :
                        var note = Math.round(noteBrut*10)/10;
                        break;
                    case 2 :
                        var note = Math.round(noteBrut*100)/100;
                        break;
                    case 3 :
                        var note = Math.round(noteBrut*1000)/1000;
                        break;
                    default :
                        var note = Math.round(noteBrut*1)/1;
                }
                return note;
            };

            function getStarWidth(){
                switch(opts.type) {
                    case 'small' :
                        starWidth = 12; // width of the picture small.png
                        starHeight = 10; // height of the picture small.png
                        bgPath = opts.smallStarsPath;
                        break;
                    default :
                        starWidth = 23; // width of the picture stars.png
                        starHeight = 20; // height of the picture stars.png
                        bgPath = opts.bigStarsPath;
                }
            };

            function findRealLeft(obj) {
                if( !obj ) return 0;
                return obj.offsetLeft + findRealLeft( obj.offsetParent );
            };
        });

    }
})(jQuery);
</script>	
<?php get_footer(); ?>