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
<div class="slit">
<dl class="clearfix">
<dt><?php _e('member number', 'usces'); ?></dt>
<dd><?php usces_memberinfo( 'ID' ); ?></dd>
<dt><?php _e('Strated date', 'usces'); ?></dt>
<dd><?php usces_memberinfo( 'registered' ); ?></dd>
<dt><?php _e('Full name', 'usces'); ?></dt>
<dd><?php esc_html_e(sprintf(__('Mr/Mrs %s', 'usces'), usces_localized_name( usces_memberinfo( 'name1', 'return' ), usces_memberinfo( 'name2', 'return' ), 'return' ))); ?></dd>
<?php if(usces_is_membersystem_point()) : ?>
<dt><?php _e('The current point', 'usces'); ?></dt>
<dd><?php usces_memberinfo( 'point' ); ?></dd>
<?php else : ?>
<dt> – </dt>
<dd> – </dd>
<?php endif; ?>
<dt><?php _e('e-mail adress', 'usces'); ?></dt>
<dd><?php usces_memberinfo('mailaddress1'); ?></dd>
</dl>
</div>
<div class="gotoedit">
<a href="#edit"><?php _e('To member information editing', 'usces'); ?></a>
</div>
<div class="header_explanation">
<?php do_action('usces_action_memberinfo_page_header'); ?>
</div>

<h3><?php _e('Purchase history', 'usces'); ?></h3>
<div class="currency_code"><?php _e('Currency','usces'); ?> : <?php usces_crcode(); ?></div>
<div class="slit">
<?php usces_member_history(); ?>
</div>
<h3 id="edit"><?php _e('Member information editing', 'usces'); ?></h3>
<div class="error_message"><?php usces_error_message(); ?></div>
<form action="<?php usces_url('member'); ?>#edit" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
<div class="slit">
<table class="customer_form">
<?php uesces_addressform( 'member', usces_memberinfo(NULL), 'echo' ); ?>
<tr>
<th scope="row"><?php _e('e-mail adress', 'usces'); ?></th>
<td colspan="2"><input name="member[mailaddress1]" id="mailaddress1" type="text" value="<?php usces_memberinfo('mailaddress1'); ?>" /></td>
</tr>
<tr>
<th scope="row"><?php _e('password', 'usces'); ?></th>
<td colspan="2"><input name="member[password1]" id="password1" type="password" value="<?php usces_memberinfo('password1'); ?>" /><br />
<?php _e('Leave it blank in case of no change.', 'usces'); ?></td>
</tr>
<tr>
<th scope="row"><?php _e('Password (confirm)', 'usces'); ?></th>
<td colspan="2"><input name="member[password2]" id="password2" type="password" value="<?php usces_memberinfo('password2'); ?>" /><br />
<?php _e('Leave it blank in case of no change.', 'usces'); ?></td>
</tr>
</table>
</div>
<input name="member_regmode" type="hidden" value="editmemberform" />
<input name="member_id" type="hidden" value="<?php usces_memberinfo('ID'); ?>" />
<div class="send">
<input name="editmember" type="submit" value="<?php _e('update it', 'usces'); ?>" />
<input name="deletemember" type="submit" value="<?php _e('delete it', 'usces'); ?>" onclick="return confirm('<?php _e('All information about the member is deleted. Are you all right?', 'usces'); ?>');" />
</div>
<?php do_action('usces_action_memberinfo_page_inform'); ?>
</form>

<div class="footer_explanation">
<?php do_action('usces_action_memberinfo_page_footer'); ?>
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
