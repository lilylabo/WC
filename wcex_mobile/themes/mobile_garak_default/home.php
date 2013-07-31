<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>
<div id="content">
<!--ウィジェットエリア-->
	<?php dynamic_sidebar('home-upper-area'); ?>
	
<!--メインナビ-->
	<?php if(function_exists('wp_nav_menu')): ?>
	<?php wp_nav_menu(array('menu_class' => 'mainnavi', 'theme_location' => 'mobile_header', 'items_wrap' => '<div id="%1$s" class="%2$s">%3$s</div>')); ?>
	<?php endif; ?>
	
<!--お勧め商品-->
	<div<?php wcmb_change_style('widget_title', 'background:#666; color:#FFF; text-align:center;'); ?>><?php _e('Items recommended','usces') ?></div>
	<?php $reco_ob = new wp_query(array('category_name'=>'itemreco', 'posts_per_page'=>6, 'post_status'=>'publish')); ?>
	<?php if ($reco_ob->have_posts()) : ?>
	<div class="widget_body wb_reco">
	<table class="topreco">
	<tr>
	<?php $rr = 0; while ($reco_ob->have_posts()) : $reco_ob->the_post(); usces_the_item(); ?>
	<?php if($rr == 3) { echo "</tr>\n<tr>"; $rr = 0; } $rr++; ?>
		<td>
			<div class="thumimg"><?php usces_the_itemImage($number = 0, $width = 74, $height = 74 ); ?></div>
			<div class="thumtitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php usces_the_itemName(); ?></a></div>
			<?php if (usces_is_skus()) : ?>
			<div class="price"><?php usces_crform( usces_the_firstPrice('return'), true, false ); ?></div>
			<?php endif; ?>
		</td>
	<?php endwhile; ?>
	<?php while( $rr < 3 ){ echo "<td>&nbsp;</td>\n"; $rr++; } ?>
	</tr>
	</table>
	</div>
	<?php else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; wp_reset_query(); ?>
	
<!--ベストセラー-->
	<div<?php wcmb_change_style('widget_title', 'background:#666; color:#FFF; text-align:center;'); ?>><?php _e('best seller','usces') ?></div>
	<div class="widget_body wb_best">
	<table class="topbest">
	<?php usces_list_bestseller(5); ?>
	</table>
	</div> 
	
<!--キーワード検索-->
	<div<?php wcmb_change_style('widget_title', 'background:#666; color:#FFF; text-align:center;'); ?>><?php _e('keyword search','usces') ?></div>
	<div class="widget_body wb_search">
	<form method="get" id="searchform" action="<?php echo home_url(); ?>" >
	<input type="text" value="" name="s" id="s" class="searchtext" />
	<input type="submit" id="searchsubmit" value="<?php _e('Search','usces') ?>" />
	<div><a href="<?php echo USCES_CART_URL; ?>&page=search_item"><?php _e('An article category keyword search','usces') ?>&gt;</a></div>
	</form>
	</div>

<!--インフォメーション-->
	<div<?php wcmb_change_style('widget_title', 'background:#666; color:#FFF; text-align:center;'); ?>><?php _e('Information','usces') ?></div>
	<div class="widget_body wb_post">
	<table class="toppost">
	<?php usces_list_post('information',3); ?>
	</table>
	</div>
	
<!--カテゴリー一覧-->
	<div<?php wcmb_change_style('widget_title', 'background:#666; color:#FFF; text-align:center;'); ?>><?php _e('Item Category','usces') ?></div>
	<div class="widget_body wb_cat">
	<table<?php wcmb_change_style('topcat', 'width:100%;'); ?>>
	<?php wcmb_list_category(); ?>
	</table>
	</div>

<!--ウィジェットエリア-->
	<?php dynamic_sidebar('home-lower-area'); ?>
</div><!-- #content -->

<?php get_footer(); ?>
