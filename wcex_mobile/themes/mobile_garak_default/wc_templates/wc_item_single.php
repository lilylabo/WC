<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content">

<?php if (have_posts()) : the_post(); ?>

<div id="item" <?php post_class() ?>>
<h1><?php the_title(); ?></h1>

<?php usces_remove_filter(); ?>
<?php usces_the_item(); ?>

<?php usces_the_itemImage(0, 240, 360, $post); ?>

<?php $imageid = usces_get_itemSubImageNums(); $i = 0; ?>
<?php if ( $imageid == true ) : ?>
<table class="subimg">
<tr>
<?php foreach ( $imageid as $id ) : ?>
<?php if($i == 4) { echo "</tr>\n<tr>"; $i = 0; } $i++; ?>
<td><a href="<?php usces_the_itemImageURL($id); ?>" <?php echo apply_filters('usces_itemimg_anchor_rel', NULL); ?>><?php usces_the_itemImage($id, 55, 55, $post); ?></a></td>
<?php endforeach; ?>
<?php while( $i < 4 ){ echo "<td>&nbsp;</td>\n"; $i++; } ?>
</tr>
</table><!-- .subimg -->
<?php endif; ?>

<?php if(usces_sku_num() === 1) : usces_have_skus(); ?>
<!--1SKU-->
	<div class="name"><?php usces_the_itemName(); ?></div>
	<div class="code">(<?php usces_the_itemCode(); ?>)</div>
	
	<?php if( usces_the_itemCprice('return') > 0 ) : ?>
	<div class="cprice"><?php _e('List price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php usces_the_itemCpriceCr(); ?></div>
	<?php endif; ?>
	<div class="price"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php usces_the_itemPriceCr(); ?></div>
	
	<div class="tocart"><a href="#shopping">↓今すぐ購入↓</a></div>
	
	<?php if( $item_custom = wcmb_get_item_custom( $post->ID, 'list', 'return' ) ) : ?>
	<?php echo $item_custom; ?>
	<?php endif; ?>
	
	<hr />
	
	<?php the_content(); ?>
	
	<hr />
	
	<form action="<?php echo USCES_CART_URL; ?>" method="post">
	<a name="shopping"></a>
	<div class="name"><?php usces_the_itemName(); ?></div>
	<div class="code">(<?php usces_the_itemCode(); ?>)</div>
	
	<?php if( usces_the_itemCprice('return') > 0 ) : ?>
	<div class="cprice"><?php _e('List price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php usces_the_itemCpriceCr(); ?></div>
	<?php endif; ?>
	<div class="price"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php usces_the_itemPriceCr(); ?></div>
	
	<?php wcmb_the_itemGpExp(); ?>

	<?php if (usces_is_options()) : ?>
	<table class="option">
	<?php while (usces_have_options()) : ?>
		<tr><th><?php usces_the_itemOptName(); ?></th></tr>
		<tr><td><?php wcmb_the_itemOption(usces_getItemOptName(),''); ?></td></tr>
	<?php endwhile; ?>
	</table>
	<?php endif; ?>

	<?php if( !usces_have_zaiko() ) : ?>
		<div class="soldout"><?php echo apply_filters('usces_filters_single_sku_zaiko_message', esc_html(usces_get_itemZaiko( 'name' ))); ?></div>
	<?php else : ?>
		<div class="cartbutton">
		<?php _e('Quantity', 'usces'); ?><?php wcmb_the_itemQuant(); ?>
		<?php usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0); ?>
		</div>
		<div class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></div>
	<?php endif; ?>

	<?php echo apply_filters('single_item_single_sku_after_field', NULL); ?>
	<?php do_action('usces_action_single_item_inform'); ?>
	</form>
	<?php do_action('usces_action_single_item_outform_garak'); ?>

<?php elseif(usces_sku_num() > 1) : usces_have_skus(); ?>
<!--some SKU-->
	<hr />
	
	<div class="name"><?php usces_the_itemName(); ?></div>
	<div class="code">(<?php usces_the_itemCode(); ?>)</div>
	
	<hr />

	<?php if( $item_custom = wcmb_get_item_custom( $post->ID, 'list', 'return' ) ) : ?>
	<?php echo $item_custom; ?>
	<?php endif; ?>
	
	<hr />
	
	<?php the_content(); ?>
	
	<form action="<?php echo USCES_CART_URL; ?>" method="post">
	<?php do { ?>
	
	<div class="sku_name">
		<div class="name"><?php usces_the_itemSkuDisp(); ?></div>
		<div class="code">(<?php usces_the_itemSku(); ?>)</div>
	</div>
	
	<?php if( usces_the_itemCprice('return') > 0 ) : ?>
	<div class="cprice"><?php _e('List price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php usces_the_itemCpriceCr(); ?></div>
	<?php endif; ?>
	<div class="price"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php usces_the_itemPriceCr(); ?></div>

	<?php wcmb_the_itemGpExp(); ?>

	<?php if (usces_is_options()) : ?>
	<table class="option">
	<?php while (usces_have_options()) : ?>
		<tr><th><?php usces_the_itemOptName(); ?></th></tr>
		<tr><td><?php wcmb_the_itemOption(usces_getItemOptName(),''); ?></td></tr>
	<?php endwhile; ?>
	</table>
	<?php endif; ?>

	<?php if( !usces_have_zaiko() ) : ?>
		<div class="soldout"><?php echo apply_filters('usces_filters_single_sku_zaiko_message', esc_html(usces_get_itemZaiko( 'name' ))); ?></div>
	<?php else : ?>
		<div class="cartbutton">
		<?php _e('Quantity', 'usces'); ?><?php wcmb_the_itemQuant(); ?>
		<?php usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0); ?>
		</div>
		<div class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></div>
	<?php endif; ?>

	<?php } while (usces_have_skus()); ?>

	<?php echo apply_filters('single_item_multi_sku_after_field', NULL); ?>
	<?php do_action('usces_action_single_item_inform'); ?>
	</form>
	<?php do_action('usces_action_single_item_outform_garak'); ?>
<?php endif; ?>

<?php wcmb_assistance_item( $post->ID, __('An article concerned', 'usces') ); ?>

</div><!-- .post -->

<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>

</div><!-- #content -->

<?php get_footer(); ?>