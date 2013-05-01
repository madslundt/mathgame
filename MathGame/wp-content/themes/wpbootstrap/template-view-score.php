<?php
/*
Template Name: View score template
 */
?>

<?php get_header(); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <legend>
        	<?php the_title(); ?> 
            <div class="pull-right">
                <a class="btn" type="button" href="<?php echo the_permalink(); ?>&view=level"><?php _e('Level', 'wpbootstrap'); ?></a>
                <a class="btn" type="button" href="<?php echo the_permalink(); ?>&view=group"><?php _e('Group', 'wpbootstrap'); ?></a>
                <a class="btn" type="button" href="<?php echo the_permalink(); ?>&view=user"><?php _e('User', 'wpbootstrap'); ?></a>
            </div>
        </legend>
        <div class="row"><?php get_template_part('view-score'); ?></div>
    <?php 
	endwhile;
	else: ?>
    	<p><?php _e('Sorry, this page does not exist.'); ?></p>
	<?php endif; ?>

<?php get_footer(); ?>