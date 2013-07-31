<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
if(!defined('USCES_VERSION')) return;

/***********************************************************
* wcmb_smart_setup
***********************************************************/
add_action( 'after_setup_theme', 'wcmb_smart_setup' );
if ( ! function_exists( 'wcmb_smart_setup' ) ):
function wcmb_smart_setup() {
	
	register_nav_menus( array(
		'smart_header' => __('Smart Header Navigation', 'usces' ),
		'smart_footer' => __('Smart Footer Navigation', 'usces' ),
		'mobile_header' => __('Mobile Header Navigation', 'usces' ),
		'mobile_footer' => __('Mobile Footer Navigation', 'usces' ),
	) );
}
endif;

/***********************************************************
* wcmb_smart_menu_args
***********************************************************/
function wcmb_smart_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'wcmb_smart_menu_args' );

/***********************************************************
* sidebar
***********************************************************/
if ( function_exists('register_sidebar') ) {
	// Area 1, HomeLeft.
	register_sidebar(array(
		'name' => __( 'Home Left', 'uscestheme' ),
		'id' => 'smart_homeleft-widget-area',
		'description' => __( 'home left sidebar widget area', 'uscestheme' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget_title">',
		'after_title' => '</div>',
	));
	// Area 2, HomeRight.
	register_sidebar(array(
		'name' => __( 'Home Right', 'uscestheme' ),
		'id' => 'smart_homeright-widget-area',
		'description' => __( 'home right sidebar widget area', 'uscestheme' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget_title">',
		'after_title' => '</div>',
	));
	// Area 3, OtherLeft.
	register_sidebar(array(
		'name' => __( 'Other Left', 'uscestheme' ),
		'id' => 'smart_otherleft-widget-area',
		'description' => __( 'other left sidebar widget area', 'uscestheme' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<div class="widget_title">',
		'after_title' => '</div>',
	));
	// Area 4, CartMemberLeft.
	register_sidebar(array(
		'name' => __( 'CartMemberLeft', 'uscestheme' ),
		'id' => 'smart_cartmemberleft-widget-area',
		'description' => __( 'cart or member left sidebar widget area', 'uscestheme' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
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
		return 40;
	}
}

if ( ! function_exists( 'welcart_excerpt_length' ) ) {
	function welcart_excerpt_length( $length ) {
		return 40;
	}
}
add_filter( 'excerpt_length', 'welcart_excerpt_length' );

if ( ! function_exists( 'welcart_excerpt_bmlength' ) ) {
	function welcart_excerpt_bmlength( $length ) {
		return 110;
	}
}
add_filter( 'excerpt_mblength', 'welcart_excerpt_bmlength' );

if ( ! function_exists( 'welcart_continue_reading_link' ) ) {
	function welcart_continue_reading_link() {
		return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'uscestheme' ) . '</a>';
	}
}

if ( ! function_exists( 'welcart_auto_excerpt_more' ) ) {
	function welcart_auto_excerpt_more( $more ) {
		return ' &hellip;' . welcart_continue_reading_link();
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
* SSL
***********************************************************/
global $usces;
if( $usces->options['use_ssl'] ){
	add_action('init', 'usces_ob_start');
	function usces_ob_start(){
		global $usces;
		if( $usces->use_ssl && ($usces->is_cart_or_member_page($_SERVER['REQUEST_URI']) || $usces->is_inquiry_page($_SERVER['REQUEST_URI'])) )
			ob_start('usces_ob_callback');
	}
	if ( ! function_exists( 'usces_ob_callback' ) ) {
		function usces_ob_callback($buffer){
			global $usces;
			$pattern = array(
				'|(<[^<]*)href=\"'.get_option('siteurl').'([^>]*)\.css([^>]*>)|', 
				'|(<[^<]*)src=\"'.get_option('siteurl').'([^>]*>)|'
			);
			$replacement = array(
				'${1}href="'.USCES_SSL_URL_ADMIN.'${2}.css${3}', 
				'${1}src="'.USCES_SSL_URL_ADMIN.'${2}'
			);
			$buffer = preg_replace($pattern, $replacement, $buffer);
			return $buffer;
		}
	}
}

?>
