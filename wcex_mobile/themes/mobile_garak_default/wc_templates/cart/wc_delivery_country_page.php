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
<?php do_action('wcmb_action_delivery_country_page_header'); ?>
	</div>
	
	<div class="error_message"><?php usces_error_message(); ?></div>
	<form action="<?php echo USCES_CART_URL; ?>" method="post">
	<table class="customer_form" id="delivery_table">
		<tr>
		<th scope="row"><?php echo usces_get_essential_mark('country') . __('Country', 'usces'); ?></th>
		<td colspan="2"><?php echo uesces_get_target_market_form( 'delivery', $usces_entries['delivery']['country'] ); ?></td>
		</tr>
	</table>

	<input name="offer[cus_id]" type="hidden" value="" />
	<div class="send">
		<input name="deliveryinfo2" type="submit" class="to_deliveryinfo_button" value="<?php _e(' Next ', 'usces'); ?>" /><br />
		<input name="backDelivery" type="submit" class="back_to_delivery_button" value="<?php _e('Back', 'usces'); ?>" />
	</div>
<?php do_action('wcmb_action_delivery_country_page_inform'); ?>
	</form>

	<div class="footer_explanation">
<?php do_action('wcmb_action_delivery_country_page_footer'); ?>
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
