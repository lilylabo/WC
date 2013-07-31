<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();

?>

<div id="header_image">
<h2><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/image_top.jpg" alt="<?php bloginfo('name'); ?>" width="100%" /></h2>
</div>

<div id="content" class="three-column">

<div id="home_recommend">
<div class="ttl clearfix">
<h2><?php _e('Items recommended','usces') ?></h2>
<span><a href="<?php echo get_category_link('4'); ?>"><?php _e('list', 'usces'); ?> &raquo;</a></span>
</div>
<div class="line_block">
<div id="carousel" class="roto">
<ul class="main clearfix">
<?php $reco_ob = new wp_query(array('category_name'=>'itemreco', 'posts_per_page'=>12, 'post_status'=>'publish')); ?>
<?php if ($reco_ob->have_posts()) : while ($reco_ob->have_posts()) : $reco_ob->the_post(); usces_the_item(); ?>
<li><a href="<?php the_permalink() ?>"><span class="thumb"><?php usces_the_itemImage($number = 0, $width = 116, $height = 116 ); ?></span>
<strong><?php usces_the_itemName(); ?>&nbsp;(<?php usces_the_itemCode(); ?>)</strong>
<?php if (usces_is_skus()) : ?>
<span class="price"><?php usces_crform( usces_the_firstPrice('return'), true, false ); ?><?php usces_guid_tax(); ?></span>
<?php endif; ?></a>
</li>
<?php endwhile; else: ?>
<li><div class="nothing"><?php _e('Sorry, no posts matched your criteria.'); ?></div></li>
<?php endif; wp_reset_query(); ?>
</ul>
</div>
<div class="navi clearfix">
<button id="carousel-prev">&laquo; <?php _e('previous item', 'usces'); ?></button>
<button id="carousel-next"><?php _e('Next item', 'usces'); ?> &raquo;</button>
</div>
</div>
</div>
	
<div id="home_new">
<div class="ttl clearfix">
<h2><?php _e('New items','usces') ?></h2>
<span><a href="<?php echo get_category_link('5'); ?>"><?php _e('list', 'usces'); ?> &raquo;</a></span>
</div>
<div class="line_block">
<div id="listbox" class="roto">
<ul class="main clearfix">
<?php $reco_ob = new wp_query(array('category_name'=>'itemnew', 'posts_per_page'=>12, 'post_status'=>'publish')); ?>
<?php if ($reco_ob->have_posts()) : while ($reco_ob->have_posts()) : $reco_ob->the_post(); usces_the_item(); ?>
<li><a href="<?php the_permalink() ?>"><span class="img_block"><span class="thumb"><?php usces_the_itemImage($number = 0, $width = 72, $height = 72 ); ?></span></span>
<strong><?php usces_the_itemName(); ?>&nbsp;(<?php usces_the_itemCode(); ?>)</strong>
<?php if (usces_is_skus()) : ?>
<span class="price"><?php usces_crform( usces_the_firstPrice('return'), true, false ); ?><?php usces_guid_tax(); ?></span></a>
<?php endif; ?>
</li>
<?php endwhile; else: ?>
<li><div class="nothing"><?php _e('Sorry, no posts matched your criteria.'); ?></div></li>
<?php endif; wp_reset_query(); ?>
</ul>
</div>
<div class="navi clearfix">
<button id="listbox-prev">&laquo; <?php _e('previous item', 'usces'); ?></button>
<button id="listbox-next"><?php _e('Next item', 'usces'); ?> &raquo;</button>
</div>
</div>
</div>

</div><!-- end of content -->

<?php get_sidebar('home'); ?>

<?php get_footer(); ?>
