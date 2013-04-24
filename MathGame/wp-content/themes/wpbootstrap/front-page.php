<?php get_header(); ?>
	
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php //$recent_posts = wp_get_recent_posts(array('numberposts' => '5' ));  ?>
			<!--<div id="myCarousel" class="carousel slide">
				<ol class="carousel-indicators">
					<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
					<?php
						//for ($i = 1; $i < count($recent_posts); $i++) {
						//	echo '<li data-target="#myCarousel" data-slide-to="'.$i.'"></li>';
						//}
					?>
				</ol>
				<div class="carousel-inner">
				  <div class="item active">
					<div class="carousel-caption">
					  <h4><?php //echo esc_attr($recent_posts[0]["post_title"]); ?></h4>
					  <p><?php //echo $recent_posts[0]["post_content"]; ?></p>
					</div>
				  </div>
				  <?php 
					/*for ($i = 1; $i < count($recent_posts); $i++) {
						echo '<div class="item">';
						echo '<div class="carousel-caption">';
						echo '<h4>' . esc_attr($recent_posts[0]["post_title"]) . '</h4>';
						echo '<p>' . $recent_posts[0]["post_content"] . '</p>';
						echo '</div>';
						echo '</div>';
					}*/
				  ?>
				</div>
				<a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
				<a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
			</div>-->
		<?php the_content(); ?>
	<?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>
<?php get_footer(); ?>