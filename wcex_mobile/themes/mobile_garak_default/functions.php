<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
 
/***********************************************************
* initial 
***********************************************************/
define('ITEM_RESTRICTION', 50);//購入数制限

/***********************************************************
* wcmb_garak_menu_args
***********************************************************/
function wcmb_garak_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wcmb_garak_menu_args' );

/***********************************************************
* sidebar：for mobile 
***********************************************************/
if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => __( 'Home Upper Area', 'uscestheme' ),
		'id' => 'home-upper-area',
		'description' => __( 'home upper widget area', 'uscestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '',
		'after_title' => '',
	));
	register_sidebar(array(
		'name' => __( 'Home Lower Area', 'uscestheme' ),
		'id' => 'home-lower-area',
		'description' => __( 'home lower widget area', 'uscestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="widget_title">',
		'after_title' => '</div>',
	));
}

/***********************************************************
* excerpt
***********************************************************/
if ( ! function_exists( 'welcart_assistance_excerpt_length' ) ) {
	function welcart_assistance_excerpt_length( $length ) {
		return 10;
	}
}

if ( ! function_exists( 'welcart_assistance_excerpt_mblength' ) ) {
	function welcart_assistance_excerpt_mblength( $length ) {
		return 20;
	}
}

if ( ! function_exists( 'welcart_excerpt_length' ) ) {
	function welcart_excerpt_length( $length ) {
		return 20;
	}
}
add_filter( 'excerpt_length', 'welcart_excerpt_length' );

if ( ! function_exists( 'welcart_excerpt_mblength' ) ) {
	function welcart_excerpt_mblength( $length ) {
		return 40;
	}
}
add_filter( 'excerpt_mblength', 'welcart_excerpt_mblength' );

if ( ! function_exists( 'welcart_continue_reading_link' ) ) {
	function welcart_continue_reading_link() {
		return ' <a href="'. get_permalink() . '">(続きを読む...)</a>';
	}
}

if ( ! function_exists( 'welcart_auto_excerpt_more' ) ) {
	function welcart_auto_excerpt_more( $more ) {
		return welcart_continue_reading_link();
	}
}
add_filter( 'excerpt_more', 'welcart_auto_excerpt_more' );

if ( ! function_exists( 'welcart_custom_excerpt_more' ) ) {
	function welcart_custom_excerpt_more( $output ) {
		if ( has_excerpt() && ! is_attachment() ) {
			$output .= welcart_continue_reading_link();
		}
		return $output;
	}
}
add_filter( 'get_the_excerpt', 'welcart_custom_excerpt_more' );

/***********************************************************
* filter：bestseller：usces_filter_bestseller
***********************************************************/
add_filter('usces_filter_bestseller', 'my_bestseller_func', 10, 3);

function my_bestseller_func() {
	global $usces;
	
	$args = func_get_args();
	list($list, $ids[$i], $i) = $args;
	$post = get_post($ids[$i]);
	
	$list = '<tr>' . "\n";
	$list .= '<td class="rank_num rank_' . ($i+1) . '">' . ($i+1) . '位</td>' . "\n";
	$list .= '<td class="rank_img rank_' . ($i+1) . '">' . usces_the_itemImage(0,50,50,$post,'return') . '</td>' . "\n";
	$list .= '<td class="rank_tlt rank_' . ($i+1) . '">' . "\n";
	$list .= '<a href="' . get_permalink($ids[$i]) . '">' . usces_the_itemName('return',$post) . '</a>' . "\n";
	if (usces_is_skus()) {
	$list .= '<div class="price">' . usces_crform( usces_the_firstPrice('return',$post), true, false, 'return' ) . '</div>' . "\n";
	}
	$list .= '</td>' . "\n";
	$list .= '</tr>' . "\n";
	return $list;
}

/***********************************************************
* filter：usces_list_post：usces_filter_widget_post
***********************************************************/
add_filter('usces_filter_widget_post', 'my_widget_post_func', 10, 3);

function my_widget_post_func() {
	$args = func_get_args();
	list($list, $post, $slug) = $args;

	$list = '<tr>' . "\n";
	$list .= '<td class="date">' . get_the_date('Y/m/d') . '</td>' . "\n";
	$list .= '<td class="title"><a href="' . get_permalink($post->ID) . '">' . get_the_title() . '</a></td>' . "\n";
	$list .= '</tr>' . "\n";
	
	return $list;
}

?>
