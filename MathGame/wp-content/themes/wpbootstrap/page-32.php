<?php get_header(); ?>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<legend><?php the_title(); ?></legend>
			<?php get_template_part('level-edit'); ?>
		<?php endwhile; else: ?>
			<p><?php _e('Sorry, this page does not exist.'); ?></p>
		<?php endif; ?>
<?php get_footer(); ?>