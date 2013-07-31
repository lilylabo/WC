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

<div class="ttl"><h1 class="pagetitle"><?php _e('Membership', 'usces'); ?></h1></div>
<div class="catbox">
<div class="post" id="wc_<?php usces_page_name(); ?>">
<div class="entry">

<div id="memberpages">

<div class="whitebox">
<div id="memberinfo">

<div class="header_explanation">
<?php do_action('wcad_action_autodelivery_history_page_header'); ?>
</div>

<h3><?php _e('Regular purchase information', 'autodelivery'); ?></h3>
<div class="currency_code"><?php _e('Currency','usces'); ?> : <?php usces_crcode(); ?></div>
<div class="error_message"><?php usces_error_message(); ?></div>
<form action="<?php usces_url('member'); ?>#autodelivery" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
<div class="slit">
<?php wcad_autodelivery_history(); ?>
</div>
<div class="send">
<input name="back" type="button" value="<?php _e('Back to the member page.', 'autodelivery'); ?>" onclick="location.href='<?php echo USCES_MEMBER_URL; ?>'" />
<input name="top" type="button" value="<?php _e('Back to the top page.', 'usces'); ?>" onclick="location.href='<?php echo home_url(); ?>'" />
</div>
<?php do_action('wcad_action_autodelivery_history_page_inform'); ?>
</form>

<div class="footer_explanation">
<?php do_action('wcad_action_autodelivery_history_page_footer'); ?>
</div>
</div><!-- end of memberinfo -->
</div><!-- end of whitebox -->
</div><!-- end of memberpages -->

</div><!-- end of entry -->
</div><!-- end of post -->
</div><!-- end of catbox -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of content -->

<?php get_sidebar( 'cartmember' ); ?>

<?php get_footer(); ?>
