<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();

get_sidebar( 'home' );
?>

<div id="content" class="three-column">
	<div class="top_image"><img src="<?php bloginfo('template_url'); ?>/images/image_top.jpg" alt="<?php bloginfo('name'); ?>" width="560" height="300" /></div>

	<?php /* Section of Recommended Products *******************************/ ?>
	<div class="title"><?php _e('Items recommended','usces') ?></div>
	<div class="clearfix">
	<?php query_posts( array('category_name'=>'itemreco', 'posts_per_page'=>8) ); ?>
	<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); usces_the_item(); ?>
	<div class="thumbnail_box">
		<div class="thumimg"><a href="<?php the_permalink() ?>"><?php usces_the_itemImage(0, 108, 108); ?></a></div>
		<div class="thumtitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php usces_the_itemName(); ?>&nbsp;(<?php usces_the_itemCode(); ?>)</a></div>
		<div class="price"><?php usces_crform( usces_the_firstPrice('return'), true, false ); ?><?php usces_guid_tax(); ?></div>
	</div>
	<?php endwhile; ?>
	<?php else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; wp_reset_query(); ?>
	</div>
	<?php /******************************************************************/ ?>

</div><!-- end of content -->

<?php //get_sidebar( 'home' ); ?>

<?php get_footer(); ?>
