<?php wcmb_doctype(); ?>
<html>
<head>
	<meta http-equiv="ContentType" Content="application/xhtml+xml" />
	<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

	<?php if( 1 !== wcmb_get_browser() ): ?>
	<link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" type="text/css">
	<?php endif; ?>

	<?php wcmb_head(); ?>
</head>

<body>
<div class="header">
	<?php ( is_home() || is_front_page() ) ? wcmb_head_telop() : ''; ?>
	<?php ( is_home() || is_front_page() ) ? wcmb_description() : ''; ?>
	<?php $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; ?>
	<<?php echo $heading_tag; ?><?php wcmb_change_style('h_title', 'background:#000; color:#FFF; text-align:center;'); ?>><?php wcmb_logo(); ?></<?php echo $heading_tag; ?>>	
	
<?php if( ( is_home() || is_front_page() ) && ( usces_is_membersystem_state() || usces_is_cart() ) && !wcmb_is_control_page() ): ?>
	<?php if(usces_is_membersystem_state()): ?>
		<div<?php wcmb_change_style('h_member', 'background:#CCC; text-align:center;'); ?>>
		<?php if(usces_is_login()): ?>
			ようこそ、<a href="<?php echo USCES_MEMBER_URL; ?>"><?php printf(__('Mr/Mrs %s', 'usces'), usces_the_member_name()); ?></a><br />
			<?php usces_loginout(); ?>
		<?php else: ?>
			ようこそ、<?php _e('guest','usces'); ?>様<br />
			<?php usces_loginout(); ?>|<a href="<?php echo USCES_NEWMEMBER_URL; ?>">新規登録</a>
		<?php endif; ?>
		</div>
	<?php endif; ?>

<?php endif; ?>

</div><!-- end of header -->
<div id="main">