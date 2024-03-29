<?php
/*
Template Name: Item category template
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
*/
get_header();
?>

<div id="content" class="two-column">
<?php if (have_posts()) the_post(); ?>
<div class="ttl"><h1 class="pagetitle"><?php the_title(); ?></h1></div>

<div class="pagebox">
<div class="post" id="<?php echo $post->post_name; ?>">
<div class="entry">
<?php the_content(); ?>
</div>
<div id="item_cat_list">
<?php $paged = $wp_query->query_vars['paged']; ?>
<?php $category_name = get_post_meta($post->ID, 'category_slug', true); ?>
<?php $posts_per_page = get_post_meta($post->ID, 'posts_per_page', true); ?>
<?php $order = get_post_meta($post->ID, 'order', true); ?>
<?php query_posts('category_name=' . $category_name . '&status=post&paged=' . $paged . '&posts_per_page=' . $posts_per_page . '&order='. $order); ?>
<div class="navigation clearfix">
<div class="next"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
<div class="prev"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
</div>
<div class="list clearfix">

<?php if (have_posts()) : while (have_posts()) : the_post(); usces_the_item(); ?>

<div class="thumbnail_box">
<div class="thumimg"><a href="<?php the_permalink() ?>"><?php usces_the_itemImage($number = 0, $width = 108, $height = 108 ); ?></a></div>
<div class="thumtitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php usces_the_itemName(); ?>&nbsp;(<?php usces_the_itemCode(); ?>)</a></div>
<?php if (usces_is_skus()) : ?>
<div class="price"><?php usces_crform( usces_the_firstPrice('return'), true, false ); ?><?php usces_guid_tax(); ?></div>
<?php endif; ?>
</div><!-- thumbnail_box -->

<?php //comments_template(); // Get wp-comments.php template ?>

<?php endwhile; else: ?>
<p><?php _e('The article was not found.', 'usces'); ?></p>
<?php endif; ?>
</div><!-- clearfix -->

<div class="navigation clearfix">
<div class="next"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
<div class="prev"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
</div>
</div>
</div>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_sidebar( 'home' ); ?>

<?php get_footer(); ?>
