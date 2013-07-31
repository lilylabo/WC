<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Mobile Garak Default Theme
 */
?>
<?php wcmb_doctype(); ?>
<html>
<head>
	<meta http-equiv="ContentType" Content="application/xhtml+xml" />
	<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
	<link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" type="text/css">
	<?php wcmb_head(); ?>
</head>
<body>
<div style="background:#000; color:#FFF; text-align:center;"><?php bloginfo('name'); ?></div>
<br /><br /><br />
<div style="">
<?php bloginfo('name'); ?>にｱｸｾｽをいただきありがとうございます。<br /><br />
申し訳ございませんがお客様のご利用機種は当ｻｲﾄに対応しておりません。
</div>
<br /><br /><br />
<hr />
<div style="color:#999;"><?php usces_copyright(); ?></div>
<?php wp_footer(); ?>
</body>
</html>
