<?php
/*
Template Name: Item category template
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
*/
get_header();
?>

<div id="content">
	<?php if (have_posts()) the_post(); ?>
	<div class="post" id="<?php echo $post->post_name; ?>">
	<h1 class="page_title"><?php the_title(); ?></h1>
	
<div id="category">
	<?php the_content(); ?>
	
	<?php $paged = $wp_query->query_vars['paged']; ?>
	<?php $category_name = get_post_meta($post->ID, 'category_slug', true); ?>
	<?php $posts_per_page = get_post_meta($post->ID, 'posts_per_page', true); ?>
	<?php $order = get_post_meta($post->ID, 'order', true); ?>
	<?php query_posts('category_name=' . $category_name . '&status=post&paged=' . $paged . '&posts_per_page=' . $posts_per_page . '&order='. $order); ?>
	
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div class="navigation"><?php posts_nav_link(' &#8212; ', __("&laquo; Previous page", 'usces'), __("next page &raquo;", 'usces')); ?></div>
	<?php endif; ?>

	<?php if (have_posts()) : usces_remove_filter(); ?>
		<table class="itemlist">
		<?php while (have_posts()) : the_post(); usces_the_item(); ?>
		<tr>
			<td class="thumimg"><?php usces_the_itemImage($number = 0, $width = 70, $height = 70 ); ?></td>
			<td>
			<h2><a href="<?php the_permalink() ?>"><?php usces_the_itemName(); ?></a></h2>
			<?php if (usces_is_skus()) : ?>
			<div class="price"><?php usces_crform( usces_the_firstPrice('return'), true, false ); ?></div>
			<?php endif; ?>
			<div class="entry"><?php the_excerpt() ?></div>
			</td>
		</tr>
		<?php endwhile; ?>
		</table>
	<?php else: ?>
		<p><?php _e('The article was not found.', 'usces'); ?></p>
	<?php endif; ?>
	
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div class="navigation"><?php posts_nav_link(' &#8212; ', __("&laquo; Previous page", 'usces'), __("next page &raquo;", 'usces')); ?></div>
	<?php endif; ?>
</div><!-- #category -->
	
	</div><!-- .post -->
</div><!-- #content -->

<?php get_footer(); ?>
