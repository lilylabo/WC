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

<h1 class="cart_page_title"><?php _e('Shipping / Payment options', 'usces'); ?></h1>
<div class="entry">
		
<div id="delivery-info">

	<div class="header_explanation">
<?php do_action('wcmb_action_delivery3_page_header'); ?>
	</div>
	
	<div class="error_message"><?php usces_error_message(); ?></div>
	<form action="<?php usces_url('cart'); ?>" method="post">
	<table class="customer_form" id="time">
		<?php wcmb_delivery_field_input(); ?>
		<tr><th scope="row"><span class="em"><?php _e('*', 'usces'); ?></span><?php _e('payment method', 'usces'); ?></th></tr>
		<tr><td><?php usces_the_payment_method( $usces_entries['order']['payment_name']); ?></td></tr>
	</table>

<?php $meta = usces_has_custom_field_meta('order'); ?>
<?php if(!empty($meta) and is_array($meta)) : ?>
	<table class="customer_form" id="custom_order">
	<?php wcmb_custom_field_input($usces_entries, 'order', ''); ?>
	</table>
<?php endif; ?>

<?php $entry_order_note = empty($usces_entries['order']['note']) ? apply_filters('usces_filter_default_order_note', NULL) : $usces_entries['order']['note']; ?>
	<table class="customer_form" id="notes_table">
		<tr><th scope="row"><?php _e('Notes', 'usces'); ?></th></tr>
		<tr><td><textarea name="offer[note]" id="note" class="notes"<?php wcmb_set_istyle( WCMB_ISTYLE_KN1 ) ?>><?php echo esc_html($entry_order_note); ?></textarea></td></tr>
	</table>
	
	<hr />

	<input name="offer[cus_id]" type="hidden" value="" />
	<div class="send">
		<input name="deliveryinfo4" type="submit" class="to_deliveryinfo_button" value="<?php _e(' Next ', 'usces'); ?>"<?php echo apply_filters('wcmb_filter_deliveryinfo3_nextbutton', NULL); ?> /><br />
		<input name="backdelivery2" type="submit" class="back_to_delivery_button" value="<?php _e('Back', 'usces'); ?>"<?php echo apply_filters('wcmb_filter_deliveryinfo3_prebutton', NULL); ?> />
	</div>
<?php do_action('wcmb_action_delivery3_page_inform'); ?>
	</form>

	<div class="footer_explanation">
<?php do_action('wcmb_action_delivery3_page_footer'); ?>
	</div>
</div>

		</div><!-- end of entry -->
	</div><!-- end of post -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_footer(); ?>
