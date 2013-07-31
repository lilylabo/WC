<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content">
<h1 class="page_title">検索結果</h1>

<div id="category">
<?php if (have_posts()) : ?>
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div class="navigation">
	<div class="alignleft"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
	<div class="alignright"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
	</div>
	<?php endif; ?>
	
	<?php while (have_posts()) : the_post(); ?>
	<div class="post">
		<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
		<div class="entry"><?php the_excerpt() ?></div>
		<?php if(!usces_is_item()): ?>
		<div class="entry-footer"><?php the_time('Y/n/j G:i'); ?></div>
		<?php endif; ?>
	</div>
	<?php endwhile; ?>
	
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div class="navigation">
	<div class="alignleft"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
	<div class="alignright"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
	</div>
	<?php endif; ?>
<?php else : ?>

	<p><?php echo __('No posts found.', 'usces'); ?></p>
	
<?php endif; ?>
</div><!-- #category -->
</div><!-- #content -->

<?php get_footer(); ?>
