<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content" class="two-column">

<?php if (have_posts()) : usces_remove_filter(); ?>

<div class="ttl"><h1 class="pagetitle"><?php _e('Completion', 'usces'); ?></h1></div>
<div class="catbox">
<div class="post" id="wc_<?php usces_page_name(); ?>">
<div class="entry">

<div id="cart_completion">
<div class="header_explanation">
<p><?php _e('Thank you for shopping.', 'usces'); ?><br /><?php _e("If you have any questions, please contact us by 'Contact'.", 'usces'); ?></p>
<?php do_action('usces_action_cartcompletion_page_header', $usces_entries, $usces_carts); ?>
</div><!-- header_explanation -->

<?php dlseller_completion_info($usces_carts); ?>

<?php usces_completion_settlement(); ?>

<?php do_action('usces_action_cartcompletion_page_body', $usces_entries, $usces_carts); ?>

<div class="footer_explanation">
<?php do_action('usces_action_cartcompletion_page_footer', $usces_entries, $usces_carts); ?>
</div><!-- footer_explanation -->

<form action="<?php echo home_url(); ?>" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
<div class="send"><input name="top" class="back_to_top_button" type="submit" value="<?php _e('Back to the top page.', 'usces'); ?>" /></div>
<?php do_action('usces_action_cartcompletion_page_inform'); ?>
</form>
<?php echo apply_filters('usces_filter_conversion_tracking', NULL, $usces_entries, $usces_carts); ?>

</div><!-- end of cart_completion -->

</div><!-- end of entry -->
</div><!-- end of post -->
</div><!-- end of catbox -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of content -->

<?php get_sidebar( 'cartmember' ); ?>

<?php get_footer(); ?>
