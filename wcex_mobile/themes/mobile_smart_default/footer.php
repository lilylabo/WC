<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
usces_remove_filter();
?>
<!-- begin footer -->

</div><!-- end of main -->

<div id="footer">

<div id="pageup">
<a href="#wrapper">ページの先頭に戻る</a>
</div>

<div id="footer_menu">
<?php if(function_exists('wp_nav_menu')): ?>
<?php wp_nav_menu(array('menu_class' => 'footernavi clearfix', 'theme_location' => 'smart_footer')); ?>
<?php else: ?>
<ul class="footernavi clearfix">
<li><a href="<?php echo home_url(); ?>"><?php _e('top page','usces'); ?></a></li>
<?php wp_list_pages('title_li=&exclude=' . USCES_MEMBER_NUMBER . ',' . USCES_CART_NUMBER ); ?>
</ul>
<?php endif; ?>
<?php if(usces_is_membersystem_state()): ?>
<ul class="footernavi clearfix">
   <li><?php usces_loginout(); ?></li>
</ul>
<?php endif; ?>
</div>

<div id="copyright">
<address><?php usces_copyright(); ?></address>
<cite>Powered by <a href="http://www.welcart.com/" target="_blank">Welcart</a></cite>
</div>

</div><!-- end of footer -->

</div><!-- end of wrapper -->

<?php wp_footer(); ?>
<?php if (is_home() || is_front_page()) { ?>
<script type="text/javascript">
	jQuery(function($) {
		$("#carousel").roto({ snap: false });
		$("#slideshow").roto();
		$("#listbox").roto({ direction: "v" });
		$("#vertical-multi").roto({ direction: "v" });
		$(".roto").css("visibility", "visible");
	});
	</script>
<?php } ?>
</body>
</html>
<?php
usces_reset_filter();
?>
