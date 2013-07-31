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

<div class="slit_block">
<div class="clearfix deta_block">
<div class="itemimg_block">
<div class="main_itemimg">
<a href="<?php usces_the_itemImageURL(0); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>><?php usces_the_itemImage(0, 170, 170, $post); ?></a>
</div><!-- end of itemimg -->
<span><a href="<?php usces_the_itemImageURL(0); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>>Zoom</a></span>
</div>
<div class="detail_box">
<?php usces_have_skus(); ?>
<h2 class="item_name"><?php usces_the_itemName(); ?></h2>
<h3 class="item_code">(<?php usces_the_itemCode(); ?>)</h3>
<div class="exp">
<?php if( $item_custom = usces_get_item_custom( $post->ID, 'list', 'return' ) ) : ?>
<div class="field"><?php echo $item_custom; ?></div>
<?php endif; ?>
<dl class="field">
<?php if( usces_the_itemCprice('return') > 0 ) : ?>
<dt class="field_name"><?php _e('List price', 'usces'); ?><?php usces_guid_tax(); ?></dt>
<dd class="field_cprice"><?php usces_the_itemCpriceCr(); ?><?php if( 'continue' == dlseller_get_charging_type( $post->ID ) ) : ?>(<?php dlseller_frequency_name($post->ID, 'amount'); ?>)<?php endif; ?></dd>
<?php endif; ?>
<dt class="field_name"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?></dt>
<dd class="field_price"><?php usces_the_itemPriceCr(); ?><?php if( 'continue' == dlseller_get_charging_type( $post->ID ) ) : ?>(<?php dlseller_frequency_name($post->ID, 'amount'); ?>)<?php endif; ?></dd>
</dl>
<div class="field">
<table summary="dlseller" class="dlseller">
<tr>
<th><?php _e('dlValidity(days)', 'dlseller'); ?></th>
<td><?php esc_html_e(usces_dlseller_validity($post)); ?></td>
</tr>
<tr>
<th><?php _e('File Name', 'dlseller'); ?></th>
<td><?php esc_html_e(usces_dlseller_filename($post)); ?></td>
</tr>
<tr>
<th><?php _e('Release Date', 'dlseller'); ?></th>
<td><?php esc_html_e(usces_get_itemMeta('_dlseller_date', $post->ID, 'return')); ?></td>
</tr>
<tr>
<th><?php _e('Version', 'dlseller'); ?></th>
<td><?php esc_html_e(usces_get_itemMeta('_dlseller_version', $post->ID, 'return')); ?></td>
</tr>
<tr>
<th><?php _e('Author', 'dlseller'); ?></th>
<td><?php esc_html_e(usces_get_itemMeta('_dlseller_author', $post->ID, 'return')); ?></td>
</tr>
</table>
</div>
<?php if( 'continue' == dlseller_get_charging_type( $post->ID ) ) : ?>
<!-- Charging Type Continue shipped -->
<div class="field">
<table summary="dlseller" class="dlseller">
<tr>
<td><?php _e('First Withdrawal Date', 'dlseller'); ?></td>
<td><?php echo dlseller_first_charging( $post->ID ); ?></td>
</tr>
<?php if( 0 < (int)$usces_item['dlseller_interval'] ) : ?><tr>
<td><?php _e('Contract Period', 'dlseller'); ?></td>
<td><?php echo $usces_item['dlseller_interval']; ?><?php _e('Month（Automatic Renewal）', 'dlseller'); ?></td>
</tr>
<?php endif; ?>
</table>
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

<div class="skuform">
<?php usces_the_itemGpExp(); ?>
<?php if (usces_is_options()) : ?>
<p class="opt_ex"><?php _e('Please appoint an option.', 'usces'); ?></p>
<dl class='item_option'>
<?php while (usces_have_options()) : ?>
<dt><?php usces_the_itemOptName(); ?></dt>
<dd><?php usces_the_itemOption(usces_getItemOptName(),''); ?></dd>
<?php endwhile; ?>
</dl>
<?php endif; ?>
<div class="dlseller_data_button"><?php usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0); ?></div>
</div><!-- end of skuform -->
<?php echo apply_filters('single_item_single_sku_after_field', NULL); ?>
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
