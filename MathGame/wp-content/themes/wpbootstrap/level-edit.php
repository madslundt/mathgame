<script>
    $(function() {
        $('#tablesorter').tablesorter();
    });
</script>
<?php
$page = isset($_GET['page']) ? absint($_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$groups = $wpdb->get_results($wpdb->prepare(
    "
	SELECT t.name, t.term_id
	FROM $wpdb->term_taxonomy taxo
	INNER JOIN $wpdb->terms t ON taxo.term_id = t.term_id
	INNER JOIN $wpdb->term_relationships rs ON t.term_id = rs.term_taxonomy_id
	WHERE taxo.taxonomy = 'user-group' AND rs.object_id = %d
	ORDER BY t.term_id
	", get_current_user_id()
));

foreach ($groups as $group)
{
    $levels = $wpdb->get_results($wpdb->prepare(
        "
		SELECT DISTINCT l.*, u.user_login AS uname 
		FROM $wpdb->group_level gl
		INNER JOIN $wpdb->level l ON gl.level_ID = l.ID
		LEFT JOIN $wpdb->level_revision r ON l.ID = r.level_ID
        INNER JOIN $wpdb->group_level g ON l.ID = g.level_ID
        INNER JOIN $wpdb->users u ON g.relationships_object_id = u.ID
		WHERE r.level_ID IS NULL AND gl.relationships_term_taxonomy_id = %d
		ORDER BY l.ID
		LIMIT %d, %d	
		", $group->term_id, $offset, $limit
    ));

    $total = $wpdb->get_var($wpdb->prepare(
        "
		SELECT COUNT( l.ID ) 
		FROM $wpdb->group_level gl
		INNER JOIN $wpdb->level l ON gl.level_ID = l.ID
		LEFT JOIN $wpdb->level_revision r ON l.ID = r.level_ID
		WHERE r.level_ID IS NULL AND gl.relationships_term_taxonomy_id = %d
		", $group->term_id
    ));
    ?>
    <h3><?php echo $group->name; ?></h3>
    <table class="table table-hover">
    <thead>
    <th>#</th>
    <th><?php _e('Name', 'wpbootstrap'); ?></th>
    <th><?php _e('Car time', 'wpbootstrap'); ?></th>
    <th><?php _e('Build time', 'wpbootstrap'); ?></th>
    <th><?php _e('Min. number', 'wpbootstrap'); ?></th>
    <th><?php _e('Max. number', 'wpbootstrap'); ?></th>
    <th><?php _e('Car speed', 'wpbootstrap'); ?></th>
    <th><?php _e('Bonus number', 'wpbootstrap'); ?></th>
    <th><?php _e('No. of bubbles', 'wpbootstrap'); ?></th>
    <th><?php _e('Bridge length', 'wpbootstrap'); ?></th>
    <th><?php _e('Creator', 'wpbootstrap'); ?></th>
    <th><?php _e('Rating', 'wpbootstrap'); ?></th>
    </thead>
    <tbody>
    <?php
    foreach ($levels as $level)
    {
        $revisions = $wpdb->get_results($wpdb->prepare(
            "
			SELECT DISTINCT l.*, u.user_login AS uname
			FROM $wpdb->level_revision r
			INNER JOIN $wpdb->level l ON r.level_ID = l.ID
            INNER JOIN $wpdb->group_level g ON r.level_ID = g.level_ID
            INNER JOIN $wpdb->users u ON g.relationships_object_id = u.ID
			WHERE r.revision_level = %d
			ORDER BY r.level_ID	
			", $level->ID
        ));

        $bridgeCount = $wpdb->get_var($wpdb->prepare(
            "
			SELECT COUNT(*)
			FROM $wpdb->bridge
			WHERE level_ID = %d
			", $level->ID
        ));

        $avgRating = $wpdb->get_var($wpdb->prepare(
            "
            SELECT AVG(rating)
            FROM $wpdb->level_rating
            WHERE level_ID = %d
            ", $level->ID
        ));

        $count = count($revisions) + 1;
        echo '<tr id="rowClick" onClick="document.location = \'' . get_permalink($page->ID) . '&level=' . $level->ID . '\'">';
        echo '<td rowspan="' . $count . '"><p class="lead"><a href="' . get_permalink($page->ID) . '&level=' . $level->ID . '">' . $level->ID . '</a></p></td>';
        ?>
            <td><?php echo $level->name; ?></td>
            <td><?php echo $level->car_time; ?></td>
            <td><?php echo $level->build_time; ?></td>
            <td><?php echo $level->min_number; ?></td>
            <td><?php echo $level->max_number; ?></td>
            <td><?php echo $level->car_speed; ?></td>
            <td><?php echo $level->bonus_number; ?></td>
            <td><?php echo $level->number_bubbles; ?></td>
            <td><?php echo $bridgeCount; ?></td>
            <td><em><?php echo $level->uname; ?></em></td>
            <td><div class="rating" data-average="<?php echo isset($avgRating) ? $avgRating : 0; ?>" data-id="<?php echo $level->ID; ?>"></div></td>
        </tr>
        <?php
        foreach ($revisions as $revision)
        {

            $bridgeCountr = $wpdb->get_var($wpdb->prepare(
                "
				SELECT COUNT(*)
				FROM $wpdb->bridge
				WHERE level_ID = %d
				", $revision->level_ID
            ));

            $avgRating = $wpdb->get_var($wpdb->prepare(
                "
                SELECT AVG(rating)
                FROM $wpdb->level_rating
                WHERE level_ID = %d
                ", $revision->level_ID
            ));
        ?>
            <tr>
                <td><?php echo $revision->name; ?></td>
                <td><?php echo $revision->car_time; ?></td>
                <td><?php echo $revision->build_time; ?></td>
                <td><?php echo $revision->min_number; ?></td>
                <td><?php echo $revision->max_number; ?></td>
                <td><?php echo $revision->car_speed; ?></td>
                <td><?php echo $revision->bonus_number; ?></td>
                <td><?php echo $revision->number_bubbles; ?></td>
                <td><?php echo $bridgeCountr; ?></td>
                <td><em><?php echo $level->uname; ?></em></td>
                <td><div class="rating" data-average="<?php echo isset($avgRating) ? $avgRating : 0; ?>" data-id="<?php echo $revision->level_ID; ?>"></div></td>
            </tr>
        <?php
        }
    }
    ?>
    </tbody>
    </table>
    <?php
    $num_of_pages = ceil($total / $limit);
    $page_links = paginate_links(array(
        'base' => add_query_arg('page', '%#%'),
        'format' => '',
        'prev_next' => True,
        'prev_text' => __('&laquo;', 'wpbootstrap'),
        'next_text' => __('&raquo;', 'wpbootstrap'),
        'type' => 'list',
        'total' => $num_of_pages,
        'current' => $page
            ));

    if ($page_links)
    {
        echo '<div class="pagination pagination-right">';
        echo '<ul>';
        echo $page_links;
        echo '</ul>';
        echo '</div>';
    }
}
?>

<script>
    $(document).ready(function(){
        $(".rating").jRating({
            step:true,
            showRateInfo: false,
            isDisabled: true,
            length: 5,
            rateMax: 5,
            decimalLength: 0
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