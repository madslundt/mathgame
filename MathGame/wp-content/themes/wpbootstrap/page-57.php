<?php get_header(); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <legend>
        	<?php the_title(); ?> <a style="margin:5px;" class="btn pull-right" type="button" href="<?php echo the_permalink(); ?>&view=group"><?php _e('Group', 'wpbootstrap'); ?></a>
            <a style="margin:5px;" class="btn pull-right" type="button" href="<?php echo the_permalink(); ?>&view=user"><?php _e('User', 'wpbootstrap'); ?></a>
            <a style="margin:5px;" class="btn pull-right" type="button" href="<?php echo the_permalink(); ?>&view=level"><?php _e('Level', 'wpbootstrap'); ?></a>
        </legend>
        <div class="row"><?php get_template_part('view-score'); ?></div>
    <?php 
	endwhile;
	else: ?>
    	<p><?php _e('Sorry, this page does not exist.'); ?></p>
	<?php endif; ?>

<?php get_footer(); ?>