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
<?php do_action('wcmb_action_customer_login_page_header'); ?>
	</div><!-- end of header_explanation -->
	
	<div class="error_message"><?php usces_error_message(); ?></div>
	<form action="<?php usces_url('cart'); ?>" method="post" name="customer_loginform" onKeyDown="if (event.keyCode == 13) {return false;}">
	<p>
	<label><?php _e('e-mail adress', 'usces'); ?><br />
	<input type="text" name="loginmail" id="loginmail" class="loginmail" value="<?php echo esc_attr($usces_entries['customer']['mailaddress1']); ?>" size="20"<?php wcmb_set_istyle( WCMB_ISTYLE_ALP ) ?> /></label>
	</p>
	<p>
	<label><?php _e('password', 'usces'); ?><br />
	<input type="password" name="loginpass" id="loginpass" class="loginpass" size="20"<?php wcmb_set_istyle( WCMB_ISTYLE_ALP ) ?> /></label>
	</p>
	<input name="member_regmode" type="hidden" value="<?php echo $member_regmode; ?>" />
	<input name="member_id" type="hidden" value="<?php echo usces_memberinfo('ID'); ?>" />
		
	<hr />
		
	<div class="send">
		<input name="customerinfologin2" type="submit" class="to_customerinfo_button" value="<?php _e(' Next ', 'usces'); ?>" /><br />
		<input name="backCart" type="submit" class="back_cart_button" value="<?php _e('Back', 'usces'); ?>" />
	</div>
<?php do_action('wcmb_action_customer_login_page_inform'); ?>
	</form>

	<div class="footer_explanation">
<?php do_action('wcmb_action_customer_login_page_footer'); ?>
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
