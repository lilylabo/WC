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

<div class="ttl"><h1 class="pagetitle"><?php _e('Change password', 'usces'); ?></h1></div>
<div class="catbox">
<div class="post" id="wc_<?php usces_page_name(); ?>">
<div class="entry">
		
<div id="memberpages">

	<div class="header_explanation">
	<?php do_action('usces_action_changepass_page_header'); ?>
	</div>

	<div class="error_message"><?php usces_error_message(); ?></div>
	<div class="loginbox">
	<form name="loginform" id="loginform" action="<?php usces_url('member'); ?>" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
	<p>
		<label><?php _e('password', 'usces'); ?><br />
		<input type="password" name="loginpass1" id="loginpass1" class="loginpass" value="" size="20" /></label>
	</p>
	<p>
		<label><?php _e('Password (confirm)', 'usces'); ?><br />
		<input type="password" name="loginpass2" id="loginpass2" class="loginpass" value="" size="20" /></label>
	</p>
	<p class="submit">
		<input type="submit" name="changepassword" id="member_login" value="<?php _e('Register', 'usces'); ?>" />
	</p>
	<?php do_action('usces_action_changepass_page_inform'); ?>
	</form>
	</div>
	<div class="footer_explanation">
	<?php do_action('usces_action_changepass_page_footer'); ?>
	</div>

</div><!-- end of memberpages -->
<script type="text/javascript">
try{document.getElementById('loginpass1').focus();}catch(e){}
</script>

</div><!-- end of entry -->
</div><!-- end of post -->
</div><!-- end of catbox -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of content -->

<?php get_sidebar( 'other' ); ?>

<?php get_footer(); ?>
