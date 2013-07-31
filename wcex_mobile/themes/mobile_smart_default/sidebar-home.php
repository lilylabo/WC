<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
global $usces;
?>

<div id="home-widget-area" class="sidebar">
<ul id="home_widget">
<?php if ( ! dynamic_sidebar( 'smart_homeleft-widget-area' ) ): ?>
<li id="welcart_search-3" class="widget widget_welcart_search">
<div class="widget_title"><?php _e('keyword search','usces') ?></div>
<ul class="welcart_search_body welcart_widget_body">
<li>
<form method="get" id="searchform" action="<?php echo home_url(); ?>" >
<input type="text" value="" name="s" id="s" class="searchtext" /><input type="submit" id="searchsubmit" value="<?php _e('Search','usces') ?>" />
<p><a href="<?php echo USCES_CART_URL . $usces->delim; ?>page=search_item"><?php _e('An article category keyword search','usces') ?></a></p>
</form>
</li>
</ul>
</li>
<li id="welcart_category-3" class="widget widget_welcart_category">
<div class="widget_title"><?php _e('Item Category','usces') ?></div>
<ul class="welcart_widget_body">
<?php $cats = get_category_by_slug('itemgenre'); ?>
<?php wp_list_categories('orderby=id&use_desc_for_title=0&child_of='.$cats->term_id.'&title_li='); ?>
</ul>
</li>
<li id="welcart_post-3" class="widget widget_welcart_post">
<div class="widget_title"><?php _e('Information','usces') ?></div>
<ul class="welcart_widget_body">
<?php usces_list_post(__('information'),3); ?>
</ul>
</li>
<li id="welcart_calendar-3" class="widget widget_welcart_calendar">
<div class="widget_title"><?php _e('Business Calendar','usces') ?></div>
<ul class="welcart_calendar_body welcart_widget_body"><li>
<?php usces_the_calendar(); ?>
</li></ul>
</li>
<?php endif; ?>
</ul>
</div>

<!-- end right sidebar -->
