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
			SELECT l . * 
			FROM $wpdb->group_level gl
			INNER JOIN $wpdb->level l ON gl.level_ID = l.ID
			LEFT JOIN $wpdb->level_revision r ON l.ID = r.level_ID
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
    <!--<th><?php _e('Rating', 'wpbootstrap'); ?></th>-->
    </thead>
    <tbody>

    <?php
    foreach ($levels as $level)
    {
        $revisions = $wpdb->get_results($wpdb->prepare(
            "
			SELECT l.*, r.*
			FROM $wpdb->level_revision r
			INNER JOIN $wpdb->level l ON r.level_ID = l.ID
			WHERE r.level_revision = %d
			ORDER BY level_ID	
			", $level->ID
        ));

        $avgRating = $wpdb->get_var($wpdb->prepare(
            "
			SELECT AVG(rating)
			FROM $wpdb->level_rating
			WHERE level_ID = %d
			", $level->ID
        ));
        $bridgeCount = $wpdb->get_var($wpdb->prepare(
            "
			SELECT COUNT(level_ID)
			FROM $wpdb->bridge
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
        <!--<td><div class="ratingRow" data-average="2" data-id="1"></div></td>-->
        </tr>
        <?php
        foreach ($revisions as $revision)
        {

            $bridgeCountr = $wpdb->get_var($wpdb->prepare(
                "
				SELECT COUNT(*)
				FROM $wpdb->bridge
				WHERE level_ID = %d
				", $revision->ID
            ));
            echo '<tr id="rowClick" onClick="document.location = \'' . get_permalink($page->ID) . '&level=' . $revision->level_ID . '\'">';
            ?>
            <td><?php echo $revision->name; ?></td>
            <td><?php echo $revision->car_time; ?></td>
            <td><?php echo $revision->build_time; ?></td>
            <td><?php echo $revision->min_number; ?></td>
            <td><?php echo $revision->max_number; ?></td>
            <td><?php echo $revision->car_speed; ?></td>
            <td><?php echo $revision->bonus_number; ?></td>
            <td><?php echo $revision->number_bubbles; ?></td>
            <td><?php echo $bridgeCountr; ?></td>
            <!--<td></td>-->
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
    /*$(document).ready(function(){
        $(".ratingRow").jRating({
                step:true,
                showRateInfo: false,
                isDisabled: true,
                length: 5,
                rateMax: 5,
                decimalLength: 0
        });
});*/
</script>


<!--<table class="table table-bordered">
        <thead>
                <tr>
                        <th>#</th>
                        <th><?php _e('Name', 'wpbootstrap'); ?></th>
                        <th><?php _e('Car time', 'wpbootstrap'); ?></th>
                        <th><?php _e('Build time', 'wpbootstrap'); ?></th>
                        <th><?php _e('Min. number', 'wpbootstrap'); ?></th>
                        <th><?php _e('Max. number', 'wpbootstrap'); ?></th>
                        <th><?php _e('Car speed', 'wpbootstrap'); ?></th>
                        <th><?php _e('Bonus number', 'wpbootstrap'); ?></th>
                        <th><?php _e('No. of bubbles', 'wpbootstrap'); ?></th>
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