<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content">
<div class="catbox">

<?php if (have_posts()) : usces_remove_filter(); ?>

<div class="post" id="wc_<?php usces_page_name(); ?>">

<h1 class="cart_page_title"><?php _e('Confirmation', 'usces'); ?></h1>
<div class="entry">

<div id="info-confirm">

	<div class="header_explanation">
<?php do_action('wcmb_action_confirm_page_header'); ?>
	</div><!-- end of header_explanation -->

	<div class="error_message"><?php usces_error_message(); ?></div>
	<div id="cart">
		<div class="currency_code"><?php _e('Currency','usces'); ?> : <?php usces_crcode(); ?></div>
		<table cellspacing="0" id="cart_table">
			<?php wcmb_get_confirm_rows(); ?>
		</table>
		<table cellspacing="0" id="cart_table">
			<tr>
				<th class="aright"><?php _e('total items', 'usces'); ?></th>
				<th class="aright"><?php usces_crform($usces_entries['order']['total_items_price'], true, false); ?></th>
			</tr>
<?php if( usces_is_member_system() && usces_is_member_system_point() && !empty($usces_entries['order']['usedpoint']) ) : ?>
			<tr>
				<td class="aright"><?php _e('Used points', 'usces'); ?></td>
				<td class="aright" style="color:#FF0000"><?php echo number_format($usces_entries['order']['usedpoint']); ?></td>
			</tr>
<?php endif; ?>
<?php if( !empty($usces_entries['order']['discount']) ) : ?>
			<tr>
				<td class="aright"><?php echo apply_filters('usces_confirm_discount_label', __('Campaign disnount', 'usces')); ?></td>
				<td class="aright" style="color:#FF0000"><?php usces_crform($usces_entries['order']['discount'], true, false); ?></td>
			</tr>
<?php endif; ?>
			<tr>
				<td class="aright"><?php _e('Shipping', 'usces'); ?></td>
				<td class="aright"><?php usces_crform($usces_entries['order']['shipping_charge'], true, false); ?></td>
			</tr>
<?php if( !empty($usces_entries['order']['cod_fee']) ) : ?>
			<tr>
				<td class="aright"><?php echo apply_filters('usces_filter_cod_label', __('COD fee', 'usces')); ?></td>
				<td class="aright"><?php usces_crform($usces_entries['order']['cod_fee'], true, false); ?></td>
			</tr>
<?php endif; ?>
<?php if( !empty($usces_entries['order']['tax']) ) : ?>
			<tr>
				<td class="aright"><?php _e('consumption tax', 'usces'); ?></td>
				<td class="aright"><?php usces_crform($usces_entries['order']['tax'], true, false); ?></td>
			</tr>
<?php endif; ?>
			<tr>
				<th class="aright"><?php _e('Total Amount', 'usces'); ?></th>
				<th class="aright"><?php usces_crform($usces_entries['order']['total_full_price'], true, false); ?></th>
			</tr>
		</table>
		<table id="confirm_table">
			<tr><td class="aright"><a href="<?php echo USCES_CART_URL; ?>">注文内容を変更する</a></td></tr>
		</table>
<?php if( usces_is_member_system() && usces_is_member_system_point() &&  usces_is_login() && !dlseller_have_continue_charge() ) : ?>
		<form action="<?php usces_url('cart'); ?>" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
		<div class="error_message"><?php usces_error_message(); ?></div>
		<table cellspacing="0" id="point_table">
			<tr>
				<td><?php _e('The current point', 'usces'); ?></td>
				<td><span class="point"><?php echo $usces_members['point']; ?></span>pt</td>
			</tr>
			<tr>
				<td><?php _e('Points you are using here', 'usces'); ?></td>
				<td><input name="offer[usedpoint]" class="used_point" type="text" value="<?php echo esc_attr($usces_entries['order']['usedpoint']); ?>" size="8" istyle="4"<?php wcmb_set_istyle( WCMB_ISTYLE_NUM ) ?> />pt</td>
			</tr>
			<tr>
				<td colspan="2"><input name="use_point" type="submit" class="use_point_button" value="<?php _e('Use the points', 'usces'); ?>" /></td>
			</tr>
		</table>
		<?php do_action('wcmb_action_confirm_page_point_inform'); ?>
		</form>
<?php endif; ?>
 
	</div>
	<table id="confirm_table">
		<tr class="ttl"><td><h3><?php _e('Customer Information', 'usces'); ?></h3></td></tr>
		<tr><th><?php _e('e-mail adress', 'usces'); ?></th></tr>
		<tr><td><?php echo esc_html($usces_entries['customer']['mailaddress1']); ?></td></tr>
		<?php wcmb_addressform( 'confirm', $usces_entries, 'echo' ); ?>
		<tr><td class="ttl"><h3><?php _e('Others', 'usces'); ?></h3></td></tr>
<?php if( dlseller_have_shipped() ) : ?>
		<tr><th><?php _e('shipping option', 'usces'); ?></th></tr>
		<tr><td><?php echo esc_html(usces_delivery_method_name( $usces_entries['order']['delivery_method'], 'return' )); ?></td></tr>
		<tr><td class="aright"><a href="<?php echo USCES_CART_URL; ?>&backdelivery1=1">配送方法を変更する</a></td></tr>
		<tr><th><?php _e('Delivery date', 'usces'); ?></th></tr>
		<tr><td><?php echo esc_html($usces_entries['order']['delivery_date']); ?></td></tr>
		<tr><th><?php _e('Delivery Time', 'usces'); ?></th></tr>
		<tr><td><?php echo esc_html(urldecode($usces_entries['order']['delivery_time'])); ?></td></tr>
		<tr><td class="aright"><a href="<?php echo USCES_CART_URL; ?>&backdelivery3=1">配送日を変更する</a></td></tr>
<?php endif; ?>
		<tr><th><?php _e('payment method', 'usces'); ?></th></tr>
		<tr><td><?php echo esc_html($usces_entries['order']['payment_name'] . usces_payment_detail($usces_entries)); ?></td></tr>
		<tr><td class="aright"><a href="<?php echo USCES_CART_URL; ?>&backdelivery3=1">支払方法を変更する</a></td></tr>
		<?php wcmb_custom_field_info($usces_entries, 'order', ''); ?>
		<tr><th><?php _e('Notes', 'usces'); ?></th></tr>
		<tr><td><?php echo nl2br(esc_html($usces_entries['order']['note'])); ?></td></tr>
<?php if( dlseller_have_dlseller_content() ) : ?>
		<tr><th><?php _e('Terms of Use', 'dlseller'); ?></th></tr>
		<tr><td><?php echo ($usces_entries['order']['terms'] ? '同意する' : ''); ?></td></tr>
<?php endif; ?>
	</table>

<?php usces_purchase_button(); ?>

	<div class="footer_explanation">
<?php do_action('wcmb_action_confirm_page_footer'); ?>
	</div><!-- end of footer_explanation -->

</div><!-- end of info-confirm -->

		</div><!-- end of entry -->
	</div><!-- end of post -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_footer(); ?>
