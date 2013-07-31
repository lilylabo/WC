<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
		<h1 class="single_title"><?php the_title(); ?></h1>
		<?php if(!usces_is_item()): ?>
		<?php the_date('','<span class="storydate">','</span>'); ?>
		<div class="storymeta"><?php _e("Filed under:"); ?> <?php the_category(',') ?> &#8212; <?php the_tags(__('Tags: '), ', ', ' &#8212; '); ?> <?php the_author() ?> @ <?php the_time() ?></div>
		<?php endif; ?>
		
		<hr />
		
		<div class="storycontent">
			<?php the_content(__('(more...)')); ?>
		</div>
	</div>
<?php endwhile; else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>

<?php posts_nav_link(' &#8212; ', __('&laquo; Newer Posts'), __('Older Posts &raquo;')); ?>

</div><!-- #content -->

<?php get_footer(); ?>
