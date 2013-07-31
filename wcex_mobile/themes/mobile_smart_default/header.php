<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=480", target-densitydpi=device-dpi, initial-scale=1, minimum-scale=1, maximum-scale=1.5, user-scalable=yes">
<meta name="format-detection" content="telephone=no" />
<title><?php
/*
* Print the <title> tag based on what is being viewed.
*/
global $page, $paged;

wp_title( '|', true, 'right' );

// Add the blog name.
bloginfo( 'name' );

// Add the blog description for the home/front page.
$site_description = get_bloginfo( 'description', 'display' );
if ( $site_description && ( is_home() || is_front_page() ) )
echo " | $site_description";

// Add a page number if necessary:
if ( $paged >= 2 || $page >= 2 )
echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php wp_get_archives('type=monthly&format=link'); ?>
<?php wp_head(); ?>

<?php if (is_home() || is_front_page()) { ?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.roto.min.js"></script>
<?php } ?>
</head>

<body <?php body_class(); ?>>
<div id="wrapper">

<div id="header">
<div class="st_backdrop">
<?php $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; ?>
<<?php echo $heading_tag; ?> id="site_title"><a href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></<?php echo $heading_tag; ?>>
</div>
<div class="sd_backdrop">
<?php $heading_tag = ( is_home() || is_front_page() ) ? 'h2' : 'div'; ?>
<<?php echo $heading_tag; ?> id="site_description"><?php bloginfo('description'); ?></<?php echo $heading_tag; ?>>
</div>
<div id="header_navi">
<div class="navi">
<ul class="clearfix">
<li class="home mnavi"><span><a href="<?php echo home_url(); ?>">Home</a></span></li>
<li class="cart mnavi"><span><a href="<?php echo USCES_CART_URL; ?>"><?php _e('Cart','usces') ?></a></span></li>
<?php if(usces_is_membersystem_state()): ?>
<li class="member mnavi">
<span><a href="javascript:void(0)" id="open_member"><?php if( usces_is_login() ){printf(__('Mr/Mrs %s', 'usces'), usces_the_member_name('return'));}else{echo 'guest';} ?></a></span>
</li>
<?php else: ?>
<li class="category mnavi"><span><a href="javascript:void(0)" id="open_category"><?php _e('Category','usces') ?></a></span></li>
<?php endif; ?>
<li class="search mnavi"><span><a href="javascript:void(0)" id="open_search"><?php _e('search','usces') ?></a></span></li>
<li class="calendar mnavi"><span><a href="javascript:void(0)" id="open_calendar"><?php _e('Calendar','usces') ?></a></span></li>
<li class="menu mnavi"><span><a href="javascript:void(0)" id="open_menu"><?php _e('Menu','usces') ?></a></span></li>
</ul>
</div>
</div>
</div><!-- end of header -->

<div id="open_1" style="display:none;"><ul>
<?php if(usces_is_membersystem_state()): ?>
	<?php if(!usces_is_login()): ?><li><a href="<?php echo USCES_NEWMEMBER_URL; ?>" title="<?php _e('New enrollment for membership.','usces') ?>"><?php _e('New enrollment for membership.','usces') ?></a></li><?php endif; ?>
    <li><?php usces_loginout(); ?></li>
    <?php if(usces_is_login()): ?>
    <li><a href="<?php echo USCES_MEMBER_URL; ?>"><?php _e('Membership information','usces') ?></a></li>
    <?php endif; ?>
    <?php if(usces_is_cart()): ?>
    <li><a href="<?php echo USCES_CUSTOMER_URL; ?>"><?php _e('Proceed to checkout','usces') ?></a></li>
    <?php endif; ?>
<?php else: ?>
<?php $cats = get_category_by_slug('itemgenre'); ?>
<?php wp_list_categories('orderby=id&use_desc_for_title=0&child_of='.$cats->term_id.'&title_li='); ?>
<?php endif; ?>
</ul></div>

<div id="open_2" style="display:none;"><ul><li>
<form method="get" id="searchform" action="<?php echo home_url(); ?>" >
<input type="text" value="" name="s" id="s" class="searchtext" /><br />
<input type="submit" id="searchsubmit" value="<?php _e('Search','usces') ?>" />
<p><a href="<?php echo USCES_CART_URL; ?>&page=search_item"><?php _e('An article category keyword search','usces') ?></a></p>
</form>
</li></ul></div>

<div id="open_3" style="display:none;"><ul><li>
<?php usces_the_calendar(); ?>
</li></ul></div>

<div id="open_4" style="display:none;">
<?php if(function_exists('wp_nav_menu')): ?>
<?php wp_nav_menu(array('menu_class' => 'mainnavi clearfix', 'theme_location' => 'smart_header')); ?>
<?php else: ?>
<ul><li><a href="<?php echo home_url(); ?>"><?php _e('top page','usces') ?></a></li>
<?php wp_list_pages('title_li=&exclude=' . USCES_MEMBER_NUMBER . ',' . USCES_CART_NUMBER ); ?></ul>
<?php endif; ?>
</div>

<div id="main" class="clearfix">

<!-- end header -->
<script type='text/javascript'>
(function($) {
	$("#open_category").click(function(){
		$("#open_1").toggle();
		$("#open_2").hide();
		$("#open_3").hide();
		$("#open_4").hide();
	});
	$("#open_member").click(function(){
		$("#open_1").toggle();
		$("#open_2").hide();
		$("#open_3").hide();
		$("#open_4").hide();
	});
	$("#open_search").click(function(){
		$("#open_2").toggle();
		$("#open_1").hide();
		$("#open_3").hide();
		$("#open_4").hide();
	});
	$("#open_calendar").click(function(){
		$("#open_3").toggle();
		$("#open_1").hide();
		$("#open_2").hide();
		$("#open_4").hide();
	});
	$("#open_menu").click(function(){
		$("#open_4").toggle();
		$("#open_1").hide();
		$("#open_2").hide();
		$("#open_3").hide();
	});
})(jQuery);
</script>
