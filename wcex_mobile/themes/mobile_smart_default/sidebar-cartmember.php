<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 * 
 * Only Cart page and Member page are displayed. 
 */
global $usces;
?>
<div id="home-widget-area" class="sidebar">
<ul id="home_widget">
<?php if ( ! dynamic_sidebar( 'smart_cartmemberleft-widget-area' ) ): ?>
<li id="welcart_category-3" class="widget widget_welcart_category">
<div class="widget_title"><?php _e('Item Category','usces') ?></div>
<ul class="welcart_widget_body">
<?php $cats = get_category_by_slug('itemgenre'); ?>
<?php wp_list_categories('orderby=id&use_desc_for_title=0&child_of='.$cats->term_id.'&title_li='); ?>
</ul>
</li>
<li id="welcart_featured-3" class="widget widget_welcart_featured">
<div class="widget_title"><?php _e('Items recommended','usces') ?></div>
<ul class="welcart_featured_body welcart_widget_body">
<?php
$offset = usces_posts_random_offset(get_posts('category='.usces_get_cat_id( 'itemreco' )));
$myposts = get_posts('numberposts=2&offset='.$offset.'&category='.usces_get_cat_id( 'itemreco' ));
foreach($myposts as $post) : usces_the_item();
?>
<li>
<div class="thumimg"><a href="<?php the_permalink() ?>"><?php usces_the_itemImage($number = 0, $width = 150, $height = 150 ); ?></a></div>
<div class="thumtitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php usces_the_itemName(); ?>&nbsp;(<?php usces_the_itemCode(); ?>)</a></div>
</li>
<?php endforeach; ?>
</ul>
</li>
<li id="welcart_bestseller-3" class="widget widget_welcart_bestseller">
<div class="widget_title"><?php _e('best seller','usces') ?></div>
<ul class="welcart_widget_body"> 
<?php usces_list_bestseller(10); ?>
</ul> 
</li>
<?php endif; ?>
</ul>
</div>
