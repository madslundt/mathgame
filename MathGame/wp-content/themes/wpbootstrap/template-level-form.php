<?php
/*
Template Name: Level form template
 */
?>

<?php get_header(); ?>
	<div class="row">
		<div class="span12">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<legend><?php the_title(); ?></legend>
				<?php get_template_part('level-form'); ?>
			<?php endwhile; else: ?>
				<p><?php _e('Sorry, this page does not exist.'); ?></p>
			<?php endif; ?>
		</div>
	</div>	
<?php get_footer(); ?>