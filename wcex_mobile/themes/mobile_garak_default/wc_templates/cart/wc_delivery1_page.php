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
<?php do_action('wcmb_action_delivery1_page_header'); ?>
	</div>
	
	<div class="error_message"><?php usces_error_message(); ?></div>
	<form action="<?php usces_url('cart'); ?>" method="post">
	<table class="customer_form" id="time">
		<tr><th scope="row"><?php _e('shipping option', 'usces'); ?></th></tr>
		<tr><td><?php usces_the_delivery_method( $usces_entries['order']['delivery_method']); ?></td></tr>
	</table>
	<table class="customer_form">
		<tr><th scope="row"><?php _e('shipping address', 'usces'); ?></th></tr>
		<tr><td><input name="delivery[delivery_flag]" type="radio" id="delivery_flag1" value="0"<?php if($usces_entries['delivery']['delivery_flag'] == 0) echo ' checked'; ?> onKeyDown="if (event.keyCode == 13) {return false;}" /> <label for="delivery_flag1"><?php _e('same as customer information', 'usces'); ?></label></td></tr>
		<tr><td><input name="delivery[delivery_flag]" type="radio" id="delivery_flag2" value="1"<?php if($usces_entries['delivery']['delivery_flag'] == 1) echo ' checked'; ?> onKeyDown="if (event.keyCode == 13) {return false;}" /> <label for="delivery_flag2"><?php _e('Chose another shipping address.', 'usces'); ?></label></td></tr>
	</table>
	
	<hr />

	<input name="offer[cus_id]" type="hidden" value="" />
	<div class="send">
		<input name="deliverycountry" type="submit" class="to_delivery_button" value="<?php _e(' Next ', 'usces'); ?>"<?php echo apply_filters('wcmb_filter_deliveryinfo1_nextbutton', NULL); ?> /><br />
		<input name="backCart" type="submit" class="back_to_cart_button" value="<?php _e('Back', 'usces'); ?>"<?php echo apply_filters('wcmb_filter_deliveryinfo1_prebutton', NULL); ?> />
	</div>
<?php do_action('wcmb_action_delivery1_page_inform'); ?>
	</form>

	<div class="footer_explanation">
<?php do_action('wcmb_action_delivery1_page_footer'); ?>
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
