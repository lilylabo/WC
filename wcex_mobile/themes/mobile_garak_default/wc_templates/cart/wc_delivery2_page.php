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
<?php do_action('wcmb_action_delivery2_page_header'); ?>
	</div>
	
	<div class="error_message"><?php usces_error_message(); ?></div>
	<form action="<?php usces_url('cart'); ?>" method="post">
	<table class="customer_form" id="delivery_table">
<?php echo wcmb_addressform( 'delivery', $usces_entries ); ?>
	</table>
	
	<hr />

	<input name="offer[cus_id]" type="hidden" value="" />
	<div class="send">
		<input name="deliveryinfo3" type="submit" class="to_deliveryinfo_button" value="<?php _e(' Next ', 'usces'); ?>"<?php echo apply_filters('wcmb_filter_deliveryinfo2_nextbutton', NULL); ?> /><br />
		<input name="backdeliverycountry" type="submit" class="back_to_delivery_button" value="<?php _e('Back', 'usces'); ?>"<?php echo apply_filters('wcmb_filter_deliveryinfo2_prebutton', NULL); ?> />
	</div>
<?php do_action('wcmb_action_delivery2_page_inform'); ?>
	</form>

	<div class="footer_explanation">
<?php do_action('wcmb_action_delivery2_page_footer'); ?>
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
