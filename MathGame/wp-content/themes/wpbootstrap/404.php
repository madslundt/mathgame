<?php get_header(); ?>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php _e('The page you were looking for do not exists', 'wpbootstrap'); ?>
		<?php endwhile; else: ?>
			<p><?php _e('Sorry, this page does not exist.'); ?></p>
		<?php endif; ?>
	
	
<?php get_footer(); ?>