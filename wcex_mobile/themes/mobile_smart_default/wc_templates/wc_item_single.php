<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>
<div id="content" class="two-column">

<?php if (have_posts()) : the_post(); ?>

<div class="ttl"><h1 class="pagetitle"><?php the_title(); ?></h1></div>
<div class="storycontent">
<div id="wc-cutemp">
<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
<?php usces_remove_filter(); ?>
<?php usces_the_item(); ?>

<form action="<?php echo USCES_CART_URL; ?>" method="post">

<!-- // STERT OF 1SKU // -->
<?php if(usces_sku_num() === 1) : usces_have_skus(); ?>
<div class="slit_block">
<div class="clearfix deta_block">
<div class="itemimg_block">
<div class="main_itemimg">
<a href="<?php usces_the_itemImageURL(0); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>><?php usces_the_itemImage(0, 170, 170, $post); ?></a>
</div><!-- end of itemimg -->
<span><a href="<?php usces_the_itemImageURL(0); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>>Zoom</a></span>
</div>
<div class="detail_box">
<h2 class="item_name"><?php usces_the_itemName(); ?></h2>
<h3 class="item_code">(<?php usces_the_itemCode(); ?>)</h3>
<div class="exp">
<p class="zaiko_status"><span><?php _e('stock status', 'usces'); ?></span><em><?php usces_the_itemZaiko(); ?></em></p>
<?php if( $item_custom = usces_get_item_custom( $post->ID, 'list', 'return' ) ) : ?>
<div class="field"><?php echo $item_custom; ?></div>
<?php endif; ?>
<dl class="field">
<?php if( usces_the_itemCprice('return') > 0 ) : ?>
<dt class="field_name"><?php _e('List price', 'usces'); ?><?php usces_guid_tax(); ?></dt>
<dd class="field_cprice"><?php usces_the_itemCpriceCr(); ?></dd>
<?php endif; ?>
<dt class="field_name"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?></dt>
<dd class="field_price"><?php usces_the_itemPriceCr(); ?></dd>
</dl>
</div><!-- end of exp -->
</div>
</div>
<div class="sub_img_block">
<?php $imageid = usces_get_itemSubImageNums(); ?>
<ul class="clearfix">
<?php foreach ( $imageid as $id ) : ?>
<li><div class="line"><a href="<?php usces_the_itemImageURL($id); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>><?php usces_the_itemImage($id, 81, 81, $post); ?></a></div></li>
<?php endforeach; ?>
</ul>
</div>
</div><!-- .slit_block -->
<div class="skuform">
<?php usces_the_itemGpExp(); ?>
<?php if (usces_is_options()) : ?>
<p class="opt_ex"><?php _e('Please appoint an option.', 'usces'); ?></p>
<dl class="item_option">
<?php while (usces_have_options()) : ?>
<dt><?php usces_the_itemOptName(); ?></dt>
<dd><?php usces_the_itemOption(usces_getItemOptName(),''); ?></dd>
<?php endwhile; ?>
</dl>
<?php endif; ?>
<?php if( !usces_have_zaiko() ) : ?>
<div class="zaiko_status_cart"><?php echo apply_filters('usces_filters_single_sku_zaiko_message', __('Sold Out', 'usces')); ?></div>
<?php else : ?>
<p class="into_cart"><span><?php _e('Quantity', 'usces'); ?></span><?php usces_the_itemQuant(); ?><span><?php usces_the_itemSkuUnit(); ?></span><?php usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0); ?></p>
<div class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></div>
<?php endif; ?>
</div><!-- end of skuform -->
<?php echo apply_filters('single_item_single_sku_after_field', NULL); ?>
<!-- // END OF 1SKU // -->

<!-- // STERT OF SOME SKU // -->
<?php elseif(usces_sku_num() > 1) : usces_have_skus(); ?>
<div class="slit_block">
<div class="clearfix deta_block">
<div class="itemimg_block">
<div class="main_itemimg">
<a href="<?php usces_the_itemImageURL(0); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>><?php usces_the_itemImage(0, 170, 170, $post); ?></a>
</div><!-- end of itemimg -->
<span><a href="<?php usces_the_itemImageURL(0); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>>Zoom</a></span>
</div>
<div class="detail_box">
<h2 class="item_name"><?php usces_the_itemName(); ?></h2>
<h3 class="item_code">(<?php usces_the_itemCode(); ?>)</h3>
<div class="exp">
<?php if( $item_custom = usces_get_item_custom( $post->ID, 'table', 'return' ) ) : ?>
<div class="field">
<?php echo $item_custom; ?>
</div>
<?php endif; ?>
</div><!-- end of exp -->
</div>
</div>
<div class="sub_img_block">
<?php $imageid = usces_get_itemSubImageNums(); ?>
<ul class="clearfix">
<?php foreach ( $imageid as $id ) : ?>
<li><div class="line"><a href="<?php usces_the_itemImageURL($id); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>><?php usces_the_itemImage($id, 81, 81, $post); ?></a></div></li>
<?php endforeach; ?>
</ul>
</div>
</div><!-- .slit_block -->
<div id="accordion" class="skuform">
<div class="skumulti accordion" id="slider">
<?php $sku_index = 0; do { 	$sku_index++; ?>
<h3 id="skutitle-<?php esc_attr_e($sku_index); ?>"><?php usces_the_itemSkuDisp(); ?> (<?php usces_the_itemSku(); ?>)</h3>
<div id="skuform-<?php esc_attr_e($sku_index); ?>">
<p class="zaiko_status"><span><?php _e('stock status', 'usces'); ?></span><em><?php usces_the_itemZaiko(); ?></em></p>
<p class="field">
<?php if( usces_the_itemCprice('return') > 0 ) : ?>
<span class="field_name"><?php _e('List price', 'usces'); ?><?php usces_guid_tax(); ?></span>
<span class="field_cprice"><?php usces_the_itemCpriceCr(); ?></span>
<?php endif; ?>
<span class="field_name"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?></span>
<strong class="field_price"><?php usces_the_itemPriceCr(); ?></strong>
</p>
<?php usces_the_itemGpExp(); ?>
<?php if (usces_is_options()) : ?>
<p class="opt_ex"><?php _e('Please appoint an option.', 'usces'); ?></p>
<dl class="item_option">
<?php while (usces_have_options()) : ?>
<dt class="optname"><?php usces_the_itemOptName(); ?></dt>
<dd class="optlist"><?php usces_the_itemOption(usces_getItemOptName(),''); ?></dd>
<?php endwhile; ?>
</dl>
<?php endif; ?>
<?php if( !usces_have_zaiko() ) : ?>
<p class="zaiko_status_cart"><?php echo apply_filters('usces_filters_single_sku_zaiko_message', __('Sold Out', 'usces')); ?></p>
<?php else : ?>
<p class="into_cart"><span></span><?php usces_the_itemQuant(); ?><span><?php usces_the_itemSkuUnit(); ?></span><?php usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0); ?></p>
<p class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></p>
<?php endif; ?>
</div>
<?php } while (usces_have_skus()); ?>
</div>
</div><!-- end of skuform -->
<?php echo apply_filters('single_item_multi_sku_after_field', NULL); ?>
<?php endif; ?>
<!-- // END OF SOME SKU // -->
<?php do_action('usces_action_single_item_inform'); ?>
</form>
<?php do_action('usces_action_single_item_outform_smart'); ?>
<div id="notic">
<h2><?php _e('Product Information', 'usces'); ?></h2>
<div class="slit_block">
<?php the_content(); ?>
</div>
</div>
<?php usces_assistance_item( $post->ID, __('An article concerned', 'usces') ); ?>
</div><!-- post_class -->

<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of wc-cutemp -->
</div><!-- end of storycontent -->
</div><!-- end of content -->

<?php get_sidebar( 'other' ); ?>

<?php get_footer(); ?>
