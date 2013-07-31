<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content">
<?php if (have_posts()) : usces_remove_filter(); ?>
<div class="post" id="wc_<?php usces_page_name(); ?>">

<?php wcmb_change_tag('ご注文一覧', 'h1', 'class="cart_page_title"', 'div', 'style="background:#777; color:#FFF; text-align:center;"'); ?>
<div class="entry">

<div id="inside-cart">

	<div class="header_explanation">
	<?php do_action('usces_action_cart_page_header'); ?>
	</div>
	
	<div class="error_message"><?php usces_error_message(); ?></div>
	<form action="<?php usces_url('cart'); ?>" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
	<?php if( usces_is_cart() ) : ?>
	
	<div id="cart">
		<?php wcmb_get_cart_rows(); ?>
		<table>
		<tr>
			<th scope="row" class="aright"><?php _e('total items','usces'); ?><?php usces_guid_tax(); ?></th>
			<th class="aright"><?php usces_crform(usces_total_price('return'), true, false); ?></th>
		</tr>
		</table>
	</div><!-- #cart -->
	
	<?php else : ?>
	<div class="no_cart"><?php _e('There are no items in your cart.','usces'); ?></div>
	<?php endif; ?>
	
	<?php the_content();?>
		
	<hr />
		
	<div class="send"><?php wcmb_get_cart_button(); ?></div>
	<?php do_action('usces_action_cart_page_inform'); ?>
	</form>
	
	<div class="footer_explanation">
	<?php do_action('usces_action_cart_page_footer'); ?>
	</div>
</div><!-- end of inside-cart -->

		</div><!-- end of entry -->
	</div><!-- end of post -->
<?php else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>
</div><!-- end of content -->

<?php get_footer(); ?>
