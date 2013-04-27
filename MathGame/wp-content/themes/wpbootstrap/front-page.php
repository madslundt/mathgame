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
		<div class="hero-unit">
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</div>
	<?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>
	<div class="row">
        <div class="span4">
          	<?php get_sidebar( 'front-footer-1' ); ?>
        </div>
        <div class="span4">
          	<?php get_sidebar( 'front-footer-2' ); ?>
       	</div>
        <div class="span4">
          	<?php get_sidebar( 'front-footer-3' ); ?>
        </div>
  	</div>
<?php get_footer(); ?>