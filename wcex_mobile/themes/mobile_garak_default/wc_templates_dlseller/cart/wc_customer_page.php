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

<h1 class="cart_page_title"><?php _e('Customer Information', 'usces'); ?></h1>
<div class="entry">

<div id="customer-info">
	
	<div class="header_explanation">
<?php do_action('usces_action_customer_page_header'); ?>
	</div><!-- end of header_explanation -->
	
	<div class="error_message"><?php usces_error_message(); ?></div>

<?php if( usces_is_membersystem_state() ) : ?>
	<form action="<?php echo USCES_CART_URL; ?>" method="post" name="customer_form" onKeyDown="if (event.keyCode == 13) {return false;}">
	<table border="0" cellpadding="0" cellspacing="0" class="customer_form">
		<tr><th scope="row" colspan="2"><span class="em"><?php _e('*', 'usces'); ?></span><?php _e('e-mail adress', 'usces'); ?></th></tr>
		<tr><td colspan="2"><input name="customer[mailaddress1]" id="mailaddress1" type="text" value="<?php echo esc_attr($usces_entries['customer']['mailaddress1']); ?>"<?php wcmb_set_istyle( WCMB_ISTYLE_ALP ) ?> /></td></tr>
		<tr><th scope="row" colspan="2"><span class="em"><?php _e('*', 'usces'); ?></span><?php _e('e-mail adress', 'usces'); ?>(<?php _e('Re-input', 'usces'); ?>)</th></tr>
		<tr><td colspan="2"><input name="customer[mailaddress2]" id="mailaddress2" type="text" value="<?php echo esc_attr($usces_entries['customer']['mailaddress2']); ?>"<?php wcmb_set_istyle( WCMB_ISTYLE_ALP ) ?> /></td></tr>
		<tr><th scope="row" colspan="2"><span class="em"><?php _e('*', 'usces'); ?></span><?php _e('password', 'usces'); ?></th></tr>
		<tr><td colspan="2"><input name="customer[password1]" style="width:100px" type="password" value="<?php echo esc_attr($usces_entries['customer']['password1']); ?>"<?php wcmb_set_istyle( WCMB_ISTYLE_ALP ) ?> /></td></tr>
		<tr><th scope="row" colspan="2"><span class="em"><?php _e('*', 'usces'); ?></span><?php _e('Password (confirm)', 'usces'); ?></th></tr>
		<tr><td colspan="2"><input name="customer[password2]" style="width:100px" type="password" value="<?php echo esc_attr($usces_entries['customer']['password2']); ?>"<?php wcmb_set_istyle( WCMB_ISTYLE_ALP ) ?> /></td></tr>
		<?php wcmb_addressform( 'customer', $usces_entries, 'echo' ); ?>
	</table>
	<input name="member_regmode" type="hidden" value="newmemberform" />
		
	<hr />
		
	<div class="send">
	<?php wcmb_get_customer_button(); ?>
	</div>
<?php do_action('usces_action_customer_page_inform'); ?>
	</form>
<?php endif; ?>

	<div class="footer_explanation">
<?php do_action('usces_action_customer_page_footer'); ?>
	</div><!-- end of footer_explanation -->
</div><!-- end of customer-info -->

		</div><!-- end of entry -->
	</div><!-- end of post -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_footer(); ?>
