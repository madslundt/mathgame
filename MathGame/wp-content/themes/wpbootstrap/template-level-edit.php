<?php
/*
Template Name: Level edit template
 */
?>

<?php get_header(); ?>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<legend><?php the_title(); ?></legend>
		<?php 
		if (isset($_GET['level'])) {
			get_template_part('level-form');
		} else {
			get_template_part('level-edit');
		}
		endwhile; else: ?>
		<p><?php _e('Sorry, this page does not exist.'); ?></p>
	<?php endif; ?>
<?php get_footer(); ?>