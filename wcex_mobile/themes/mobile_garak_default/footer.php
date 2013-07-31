<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
?>
<hr />
<?php if(function_exists('wp_nav_menu')): ?>
<?php wp_nav_menu(array('menu_class' => 'footernavi', 'theme_location' => 'mobile_footer', 'items_wrap' => '<div id="%1$s" class="%2$s">%3$s</div>')); ?>
<?php endif; ?>
</div>

<div id="footer">
	<p class="copyright"><?php usces_copyright(); ?></p>
</div><!-- end of footer -->

<?php wp_footer(); ?>
</body>
</html>
