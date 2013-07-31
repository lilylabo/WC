<?php
/*
Plugin Name: WCEX Mobile
Plugin URI: http://www.welcart.com/
Description: このプラグインはWelcart専用の拡張プラグインです。Welcart本体と一緒にご利用下さい。
Version: 1.2.1
Author: Collne Inc.
Author URI: http://www.welcart.com/
*/

if ( !defined('USCES_EX_PLUGIN') )
	define('USCES_EX_PLUGIN', 1);

define('WCEX_MOBILE', true);
define('WCEX_MOBILE_VERSION', "1.2.1.1307071");
define('WCEX_MOBILE_VIEW', false);
define('DOCOMO', 1);
define('SOFTBANK', 2);
define('KDDI', 3);
define('SMARTPHONE', 10);
define('PC', 0);
define('WCMB_IMGPATH', '/wp-content/uploads/usces_cache/');

define('WCMB_ISTYLE_KN1', 1);
define('WCMB_ISTYLE_KN2', 2);
define('WCMB_ISTYLE_ALP', 3);
define('WCMB_ISTYLE_NUM', 4);

if ( defined('USCES_VERSION') ):
	global $usces, $wcmb, $wcmb_options;

	load_plugin_textdomain('mobile', false, dirname(plugin_basename(__FILE__)).'/languages');

	$wcmb = array();
	require(WP_PLUGIN_DIR."/wcex_mobile/bandwidth.php");
	require(WP_PLUGIN_DIR."/wcex_mobile/functions/function.php");
	require(WP_PLUGIN_DIR."/wcex_mobile/functions/template_func.php");

	$wcmb_options = get_option('wcmb');

	add_action('plugins_loaded', 'wcmb_setup');

	register_activation_hook( __FILE__, 'wcmb_activate' );

endif;

function wcmb_setup(){
	global $usces, $wcmb, $wcmb_options;

	if( empty($wcmb_options['garak_template']) )
		$wcmb_options['garak_template'] = 'mobile_garak_default';
	if( empty($wcmb_options['garak_telop']) )
		$wcmb_options['garak_telop'] = '';
	if( empty($wcmb_options['garak_logo']) )
		$wcmb_options['garak_logo'] = 'allimage';
	if( empty($wcmb_options['garak_logo_uri']) )
		$wcmb_options['garak_logo_uri'] = WP_CONTENT_URL . '/themes/' . $wcmb_options['garak_template'] . '/images/image_top.jpg';
	if( empty($wcmb_options['garak_ssl']) )
		$wcmb_options['garak_ssl'] = 0;
	if( !isset($wcmb_options['garak_description']) )
		$wcmb_options['garak_description'] = 0;
	if( !isset($wcmb_options['garak_referer_check']) )
		$wcmb_options['garak_referer_check'] = 1;
	if( !isset($wcmb_options['garak_rejection']) )
		$wcmb_options['garak_rejection'] = 0;
	if( empty($wcmb_options['smart_template']) )
		$wcmb_options['smart_template'] = 'mobile_smart_default';
	if( empty($wcmb_options['smart_logo']) )
		$wcmb_options['smart_logo'] = 'allimage';
	if( empty($wcmb_options['smart_logo_uri']) )
		$wcmb_options['smart_logo_uri'] = WP_CONTENT_URL . '/themes/' . $wcmb_options['smart_template'] . '/images/image_top.jpg';
	if( !isset($wcmb_options['smart_ssl']) )
		$wcmb_options['smart_ssl'] = 0;
	if( !isset($wcmb_options['smart_remote_address']) )
		$wcmb_options['smart_remote_address'] = 1;
	if( !isset($wcmb_options['smart_pc_theme']) )
		$wcmb_options['smart_pc_theme'] = 0;
	if( !isset($wcmb_options['smart_theme_switch']) )
		$wcmb_options['smart_theme_switch'] = 0;

	do_action( 'wcmb_setup_pre' );

	$wcmb['device_div'] = wcmb_get_device_div();
	$wcmb['device_name'] = wcmb_get_device_name($wcmb['device_div']);
	$wcmb['browser'] = wcmb_get_browser();


	add_action('admin_menu', 'wcmb_add_admin_pages');
	register_nav_menus( array(
		'mobile_header' => __('Mobile Header Navigation', 'usces' ),
		'mobile_footer' => __('Mobile Footer Navigation', 'usces' ),
		'smart_header' => __('Smart Header Navigation', 'usces' ),
		'smart_footer' => __('Smart Footer Navigation', 'usces' )
	) );
	add_filter('usces_filter_mail_line', 'wcmb_filter_mail_line', 10, 3);

	if(is_admin()) return;

	if( DOCOMO === $wcmb['device_div'] || SOFTBANK === $wcmb['device_div'] || KDDI === $wcmb['device_div'] ) {
		// garaK
		if($wcmb_options['garak_rejection']){
			die();
		}
		add_filter( 'wp_nav_menu', 'wcmb_nav_menu', 10, 2 );
		add_filter( 'the_content', 'wcmb_filter_content_image', 30);
		add_filter('home_url', 'wcmb_home_url');
		add_filter('post_link', 'wcmb_post_link');
		add_filter('page_link', 'wcmb_post_link');
		add_filter('term_link', 'wcmb_post_link');
		if(KDDI === $wcmb['device_div']){
			wcmb_slugtolower();
		}

	}elseif( SMARTPHONE === $wcmb['device_div'] ){

		add_filter( 'usces_filter_cart_row', 'wcmb_cart_row_of_smartphone', 10, 3);
		add_filter( 'usces_filter_confirm_row', 'wcmb_confirm_row_of_smartphone', 10, 3);
		
		add_action( 'setup_theme', 'wcmb_action_theme_switcher');

		if( !$wcmb_options['smart_remote_address'] ){
			add_action('usces_sessid_flag', 'wcmb_sessid_flag');
			add_action('usces_sessid_force', 'wcmb_sessid_flag');
		}

		if( $wcmb_options['smart_ssl'] )
			$usces->use_ssl = 0;

		return;

	}else{
		// PC etc.
		if( ! WCEX_MOBILE_VIEW ) return;
	}

	if( $wcmb_options['garak_ssl'] )
		$usces->use_ssl = 0;
	$usces->use_js = 0;//javascript off

	add_filter('usces_filter_cookie', 'wcmb_filter_cookie');
	add_action('sanitize_comment_cookies', 'wcmb_convert_encodings');
	add_filter('get_pagenum_link', 'wcmb_get_pagenum_link');
	add_action('init', 'wcmb_init', 8);
	add_action('init', 'wcmb_remove_filter', 90);
	add_action('usces_sessid_flag', 'wcmb_sessid_flag');
	add_action('usces_sessid_force', 'wcmb_sessid_flag');
	add_action('usces_main', 'wcmb_main');
	add_action('usces_after_cart_instant', 'wcmb_after_cart_instant', 9);
	add_filter('template', 'wcmb_mobile_template');
	add_filter('stylesheet', 'wcmb_mobile_stylesheet');
	add_action('template_redirect', 'wcmb_output', 8);
	if( defined( 'USCES_VERSION' ) and version_compare( USCES_VERSION, '1.2.2', '>=' ) ) {
		add_filter('usces_filter_template_redirect', 'wcmb_garak_template_redirect', 1);
	} else {
		add_filter('usces_action_template_redirect', 'wcmb_garak_template_redirect', 1);
	}
	add_filter('usces_filter_confirm_inform', 'wcmb_filter_confirm_inform', 10, 5);
	add_filter('usces_filter_single_item_inform', 'wcmb_filter_single_item_inform');
	add_action('usces_action_single_item_inform', 'wcmb_action_single_item_inform');
	add_filter('usces_fiter_the_payment_method', 'wcmb_fiter_the_payment_method');
	add_action('usces_action_essential_mark', 'wcmb_action_essential_mark', 15);
	add_filter('usces_filter_tax_guid', 'wcmb_filter_tax_guid');
	add_filter('wpcf7_display_message', 'wcmb_filter_wpcf7_display_message', 10, 2);
//	add_filter('template', 'wcmb_garak_template');
//	add_filter('stylesheet', 'wcmb_garak_stylesheet');
	add_action('usces_pre_reg_orderdata', 'wcmb_pre_reg_orderdata');
	if( defined('WCEX_DLSELLER') ) {
		add_filter('usces_filter_newmember_button', 'wcmb_filter_newmember_button');
		add_filter('wcmb_filter_history_item_name', 'wcmb_filter_history_item_name_dlseller', 10, 4);
	}
	if( defined('WCEX_AUTO_DELIVERY') ) {
		add_filter('usces_filter_newmember_button', 'wcmb_filter_newmember_button');
	}
	add_action('usces_action_cart_page_footer', 'wcmb_action_cart_clear');
	add_action('wcmb_action_confirm_page_point_inform', 'wcmb_action_confirm_page_point_inform_zeus', 9);
	add_filter('usces_filter_delim', 'wcmb_filter_delim');
	add_filter('usces_filter_incart_check', 'wcmb_filter_incart_check', 10, 3);
}

function wcmb_remove_filter(){
	remove_filter( 'usces_filter_js_intoCart', 'widgetcart_filter_js_intoCart', 10, 3 );
	remove_filter( 'usces_filter_direct_intocart_button', 'widgetcart_filter_direct_intocart_button', 10, 5 );
}

function wcmb_filter_single_item_inform($html){
	$html .= '<input name="usces_force" type="hidden" value="incart" />';
	return $html;
}

function wcmb_action_single_item_inform(){
	echo '<input name="usces_force" type="hidden" value="incart" />';
}

function wcmb_filter_cookie(){
	global $wcmb, $wcmb_options, $usces;

	$wcmb['target'] = true;
	$referer_check = $wcmb_options['garak_referer_check'];

	$rckid = NULL;
	$cookie = $usces->get_cookie();

	$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
	$sslid = isset($cookie['sslid']) ? $cookie['sslid'] : NULL;
	if( isset($_GET['uscesid']) && $_GET['uscesid'] != '' ){
		$sessid = base64_decode(urldecode($_GET['uscesid']));
		list($sess, $addr, $rckid, $none) = explode('_', $sessid, 4);
	}else{
		$rckid = NULL;
	}
	$option = get_option('usces');
	$parsed = parse_url(home_url());
	$home = $parsed['host'] . $parsed['path'];
	$parsed = parse_url($option['ssl_url']);
	$sslhome = $parsed['host'] . $parsed['path'];

	//usces_log('request : '.print_r($_SERVER['REQUEST_URI'],true), 'acting_transaction.log');
	if( !isset($_GET['uscesid']) || $_GET['uscesid'] == '' ){

		if( !isset($cookie['id']) || $cookie['id'] == '' ) {
			$values = array(
						'id' => md5(uniqid(rand(), true)),
						'name' => '',
						'rme' => ''
						);
			$usces->set_cookie($values);
			$_SESSION['usces_cookieid'] = $values['id'];
		} else {
			if( !isset($_SESSION['usces_cookieid']) || $_SESSION['usces_cookieid'] != $cookie['id'])
				$_SESSION['usces_cookieid'] = $cookie['id'];
		}

	}else{

		if( !$referer_check ){
			if( $usces->use_ssl && ($usces->is_cart_or_member_page($_SERVER['REQUEST_URI']) || $usces->is_inquiry_page($_SERVER['REQUEST_URI']))){
				$values = array(
							'id' => $rckid,
							'sslid' => $rckid,
							'name' => '',
							'rme' => ''
							);
				if( 'acting' !== $rckid )
					$usces->set_cookie($values);


			}else{
				$values = array(
							'id' => md5(uniqid(rand(), true)),
							'name' => '',
							'rme' => ''
							);
			}
			$wcmb['target'] = true;
			return true;

		}else{

			if( $usces->use_ssl && ($usces->is_cart_or_member_page($_SERVER['REQUEST_URI']) || $usces->is_inquiry_page($_SERVER['REQUEST_URI']))){
				if( empty($refer) || (false === strpos($refer, $home) && false === strpos($refer, $sslhome)) ){
					if( !empty($sslid) && !empty($rckid) && $sslid === $rckid ){
						$wcmb['target'] = true;
					}else{
						$wcmb['target'] = false;
					}
				}else{
					if( !empty($sslid) && $sslid !== $rckid ){
						$wcmb['target'] = false;
					}else{
						$wcmb['target'] = true;
					}
				}

				if( $wcmb['target'] ){
					$values = array(
								'id' => $rckid,
								'sslid' => $rckid,
								'name' => '',
								'rme' => ''
								);
					if( 'acting' !== $rckid )
						$usces->set_cookie($values);
				}else{
					unset($_SESSION['usces_member'] );
					$_SESSION['usces_cart'] = array();
					$_SESSION['usces_entry'] = array();
				}

			}else{

				if( empty($refer) || (false === strpos($refer, $home) && false === strpos($refer, $sslhome)) ){
					$wcmb['target'] = false;
				}else{
					$wcmb['target'] = true;
				}

				if( $wcmb['target'] ){
					$values = array(
								'id' => md5(uniqid(rand(), true)),
								'name' => '',
								'rme' => ''
								);
					$usces->set_cookie($values);
					$_SESSION['usces_cookieid'] = $values['id'];
				}else{
					unset($_SESSION['usces_member'] );
					$_SESSION['usces_cart'] = array();
					$_SESSION['usces_entry'] = array();
				}
			}
		}
	}
	if( false === $wcmb['check_garak'] )
		$wcmb['target'] = false;

	return true;
}

function wcmb_filter_wpcf7_form_elements($arg){
	//usces_log('arg : '.print_r($arg,true), 'acting_transaction.log');
	return $arg;
}

function wcmb_slugtolower(){
	preg_match_all('/(%[a-zA-Z0-9][a-zA-Z0-9])+/', $_SERVER['REQUEST_URI'], $matches);
	$link = '';
	foreach( $matches[0] as $slug ){
		$newslug = strtolower($slug);
		$link = str_replace($slug, $newslug, $_SERVER['REQUEST_URI']);
	}
	if( $link )
		$_SERVER['REQUEST_URI'] = $link;
}

function wcmb_check_refere(){
	global $wcmb_options;
	$referer_check = $wcmb_options['garak_referer_check'];

	if( is_home() )
		return false;

	if( !isset($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_REFERER']) ){
		if( $referer_check ) 
			$_SESSION = array();
	}
}

function wcmb_convert_encodings(){
	if(isset($_GET['s']) && !empty($_GET['s'])) {
		//mb_convert_variables('UTF-8', "SJIS", $_GET);
		$s = mb_convert_encoding($_GET['s'], 'UTF-8', "SJIS");
		$_GET['s'] = $s;
	}
}

function wcmb_get_pagenum_link( $link ){
	global $usces;

	if(isset($_GET['s']) && !empty($_GET['s'])) {
		$link = $link.'&s='.$_GET['s'];
	}

	$uscesid = '/?uscesid='.$usces->get_uscesid();
	$pos = strpos( $link, $uscesid );
	if( $pos !== false ) {
		$link = str_replace( $uscesid, "", $link );
		$permalink_structure = get_option( 'permalink_structure' );
		if( $permalink_structure ) {
			$category = strpos( $link, 'cat' );
			if( $category !== false ) {
				$link = $link.'/?uscesid='.$usces->get_uscesid();
			} else {
				$link = $link.'&uscesid='.$usces->get_uscesid();
			}
		} else {
			if( is_category() and false === strpos( $link, 'cat' ) ) {
				$cat = get_query_var( 'cat' );
				$pos = strpos( $link, 'paged' );
				if( $pos !== false ) {
					$paged = substr( $link, $pos-1 );
					$link = substr( $link, 0, $pos-1 );
					$link = $link.'/?cat='.$cat[0].'&uscesid='.$usces->get_uscesid().$paged;
				} else {
					$link = $link.'/?cat='.$cat[0].'&uscesid='.$usces->get_uscesid();
				}
			} else {
				$link = $link.'&uscesid='.$usces->get_uscesid();
			}
		}
	}

	return $link;
}

function wcmb_activate(){

	wcmb_set_default_theme();

	$opt_dir = ABSPATH . rtrim(WCMB_IMGPATH, '/');
	if( !file_exists($opt_dir) )
		mkdir($opt_dir, 0775);
}

function wcmb_init(){
	global $usces, $wcmb, $wcmb_options;

	remove_action('template_redirect', 'redirect_canonical');
	add_action('template_redirect', 'redirect_canonical', 13);

	usces_register_action('customerinfologin', 'post', 'customerinfologin', NULL, 'wcmb_customerinfologin');
	usces_register_action('customerinfologin2', 'post', 'customerinfologin2', NULL, 'wcmb_customerinfologin2');
	usces_register_action('customercountry', 'post', 'customercountry', NULL, 'wcmb_customercountry');
	usces_register_action('customerinfo2', 'request', 'customerinfo2', NULL, 'wcmb_customerinfo2');
	usces_register_action('deliverycountry', 'post', 'deliverycountry', NULL, 'wcmb_deliverycountry');
	usces_register_action('deliveryinfo2', 'request', 'deliveryinfo2', NULL, 'wcmb_deliveryinfo2');
	usces_register_action('deliveryinfo3', 'post', 'deliveryinfo3', NULL, 'wcmb_deliveryinfo3');
	usces_register_action('deliveryinfo4', 'post', 'deliveryinfo4', NULL, 'wcmb_deliveryinfo4');
	usces_register_action('confirm2', 'post', 'confirm2', NULL, 'wcmb_confirm2');
	usces_register_action('backcustomercountry', 'post', 'backcustomercountry', NULL, 'wcmb_backcustomercountry');
	usces_register_action('backdeliverycountry', 'post', 'backdeliverycountry', NULL, 'wcmb_backdeliverycountry');
	usces_register_action('backdelivery1', 'get', 'backdelivery1', NULL, 'wcmb_backdelivery1');
	usces_register_action('backdelivery2', 'post', 'backdelivery2', NULL, 'wcmb_backdelivery2');
	usces_register_action('backdelivery3', 'request', 'backdelivery3', NULL, 'wcmb_backdelivery3');
	usces_register_action('regmemberdl', 'request', 'regmemberdl', NULL, 'wcmb_regmemberdl');
	usces_register_action('go2top', 'post', 'go2top', NULL, 'wcmb_go2top');
	usces_register_action('previous', 'post', 'previous', NULL, 'wcmb_previous');
	usces_register_action('cartclear', 'request', 'cartclear', NULL, 'wcmb_cart_clear');
	if( KDDI === $wcmb['device_div'] ) {
		usces_register_action('mupButton', 'post', 'mupButton', NULL, 'wcmb_upButton');
		usces_register_action('mdelButton', 'post', 'mdelButton', NULL, 'wcmb_delButton');
	}

	$permalink_structure = get_option('permalink_structure');
	if( $usces->use_ssl ) {//0000303
		if( $permalink_structure ){
			$home_perse = parse_url(get_option('home'));
			$home_path = $home_perse['host'].$home_perse['path'];
			$ssl_perse = parse_url($usces->options['ssl_url']);
			$ssl_path = $ssl_perse['host'].$ssl_perse['path'];
			if( $home_perse['path'] != $ssl_perse['path'] ){

				if( ! defined('USCES_CUSTOMER_URL') )
					define('USCES_CUSTOMER_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_CART_NUMBER . '&customerinfo=1&uscesid=' . $usces->get_uscesid('mobile'));
				if( ! defined('USCES_CART_URL') )
					define('USCES_CART_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_CART_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile'));
				if( ! defined('USCES_SSL_CART_URL') )
					define('USCES_SSL_CART_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_CART_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile'));
				if( ! defined('USCES_LOSTMEMBERPASSWORD_URL') )
					define('USCES_LOSTMEMBERPASSWORD_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile') . '&page=lostmemberpassword');
				if( ! defined('USCES_NEWMEMBER_URL') )
					define('USCES_NEWMEMBER_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile') . '&page=newmember');
				if( ! defined('USCES_LOGIN_URL') )
					define('USCES_LOGIN_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile') . '&page=login');
				if( ! defined('USCES_LOGOUT_URL') )
					define('USCES_LOGOUT_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile') . '&page=logout');
				if( ! defined('USCES_MEMBER_URL') )
					define('USCES_MEMBER_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile'));
				$inquiry_url = empty( $usces->options['inquiry_id'] ) ? '' : $usces->options['ssl_url'] . '/index.php?page_id=' . $usces->options['inquiry_id'] . '&uscesid=' . $usces->get_uscesid('mobile');
				if( ! defined('USCES_INQUIRY_URL') )
					define('USCES_INQUIRY_URL', $inquiry_url);
				if( ! defined('USCES_CART_NONSESSION_URL') )
					define('USCES_CART_NONSESSION_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_CART_NUMBER);
				if( ! defined('USCES_PAYPAL_NOTIFY_URL') )
					define('USCES_PAYPAL_NOTIFY_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_CART_NUMBER . '&acting=paypal_ipn&uscesid=' . $usces->get_uscesid(false));
				define('USCES_MEMBER_HISTORY_URL', $usces->options['ssl_url'] . '/index.php?page_id=' . USCES_MEMBER_NUMBER . '&page=history&uscesid=' . $usces->get_uscesid('mobile'));
			}else{
				$ssl_plink_member = str_replace('http://','https://', str_replace( $home_path, $ssl_path, get_page_link(USCES_MEMBER_NUMBER) ));
				$ssl_plink_cart = str_replace('http://','https://', str_replace( $home_path, $ssl_path, get_page_link(USCES_CART_NUMBER) ));
				if( ! defined('USCES_CUSTOMER_URL') )
					define('USCES_CUSTOMER_URL', $ssl_plink_cart . '&customerinfo=1');
				if( ! defined('USCES_CART_URL') )
					define('USCES_CART_URL', $ssl_plink_cart);
				if( ! defined('USCES_SSL_CART_URL') )
					define('USCES_SSL_CART_URL', $ssl_plink_cart);
				if( ! defined('USCES_LOSTMEMBERPASSWORD_URL') )
					define('USCES_LOSTMEMBERPASSWORD_URL', $ssl_plink_member . '&page=lostmemberpassword');
				if( ! defined('USCES_NEWMEMBER_URL') )
					define('USCES_NEWMEMBER_URL', $ssl_plink_member . '&page=newmember');
				if( ! defined('USCES_LOGIN_URL') )
					define('USCES_LOGIN_URL', $ssl_plink_member . '&page=login');
				if( ! defined('USCES_LOGOUT_URL') )
					define('USCES_LOGOUT_URL', $ssl_plink_member . '&page=logout');
				if( ! defined('USCES_MEMBER_URL') )
					define('USCES_MEMBER_URL', $ssl_plink_member);
				if( !isset($usces->options['inquiry_id']) || !( (int)$usces->options['inquiry_id'] ) ){
					$inquiry_url = home_url();
				}else{
//					$ssl_plink_inquiry = str_replace('http://','https://', str_replace( $home_path, $ssl_path, get_page_link($usces->options['inquiry_id']) ));
//					$inquiry_url = empty( $usces->options['inquiry_id'] ) ? '' : $ssl_plink_inquiry;
					$inquiry_url = empty( $usces->options['inquiry_id'] ) ? '' : $usces->options['ssl_url'] . '/index.php?page_id=' . $usces->options['inquiry_id'] . '&uscesid=' . $usces->get_uscesid('mobile');
				}
				if( ! defined('USCES_INQUIRY_URL') )
					define('USCES_INQUIRY_URL', $inquiry_url);
				if( ! defined('USCES_CART_NONSESSION_URL') )
					define('USCES_CART_NONSESSION_URL', $ssl_plink_cart);
				if( ! defined('USCES_PAYPAL_NOTIFY_URL') )
					define('USCES_PAYPAL_NOTIFY_URL', $ssl_plink_cart . '&acting=paypal_ipn');
				define('USCES_MEMBER_HISTORY_URL', $ssl_plink_member . '&page=history');
			}
		}else{
			if( ! defined('USCES_CUSTOMER_URL') )
				define('USCES_CUSTOMER_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_CART_NUMBER . '&customerinfo=1&uscesid=' . $usces->get_uscesid('mobile'));
			if( ! defined('USCES_CART_URL') )
				define('USCES_CART_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_CART_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile'));
			if( ! defined('USCES_SSL_CART_URL') )
				define('USCES_SSL_CART_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_CART_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile'));
			if( ! defined('USCES_LOSTMEMBERPASSWORD_URL') )
				define('USCES_LOSTMEMBERPASSWORD_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile') . '&page=lostmemberpassword');
			if( ! defined('USCES_NEWMEMBER_URL') )
				define('USCES_NEWMEMBER_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile') . '&page=newmember');
			if( ! defined('USCES_LOGIN_URL') )
				define('USCES_LOGIN_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile') . '&page=login');
			if( ! defined('USCES_LOGOUT_URL') )
				define('USCES_LOGOUT_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile') . '&page=logout');
			if( ! defined('USCES_MEMBER_URL') )
				define('USCES_MEMBER_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_MEMBER_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile'));
			$inquiry_url = empty( $usces->options['inquiry_id'] ) ? '' : $usces->options['ssl_url'] . '/?page_id=' . $usces->options['inquiry_id'] . '&uscesid=' . $usces->get_uscesid('mobile');
			if( ! defined('USCES_INQUIRY_URL') )
				define('USCES_INQUIRY_URL', $inquiry_url);
			if( ! defined('USCES_CART_NONSESSION_URL') )
				define('USCES_CART_NONSESSION_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_CART_NUMBER);
			if( ! defined('USCES_PAYPAL_NOTIFY_URL') )
				define('USCES_PAYPAL_NOTIFY_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_CART_NUMBER . '&acting=paypal_ipn&uscesid=' . $usces->get_uscesid(false));
			define('USCES_MEMBER_HISTORY_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_MEMBER_NUMBER . '&page=history&uscesid=' . $usces->get_uscesid('mobile'));
		}
	} else {
		if( ! defined('USCES_CUSTOMER_URL') )
			define('USCES_CUSTOMER_URL', get_page_link(USCES_CART_NUMBER) . '&customerinfo=1');
		if( ! defined('USCES_CART_URL') )
			define('USCES_CART_URL', get_page_link(USCES_CART_NUMBER));
		if( ! defined('USCES_SSL_CART_URL') )
			define('USCES_SSL_CART_URL', $usces->options['ssl_url'] . '/?page_id=' . USCES_CART_NUMBER . '&uscesid=' . $usces->get_uscesid('mobile'));
		if( ! defined('USCES_LOSTMEMBERPASSWORD_URL') )
			define('USCES_LOSTMEMBERPASSWORD_URL', get_page_link(USCES_MEMBER_NUMBER) . '&page=lostmemberpassword');
		if( ! defined('USCES_NEWMEMBER_URL') )
			define('USCES_NEWMEMBER_URL', get_page_link(USCES_MEMBER_NUMBER) . '&page=newmember');
		if( ! defined('USCES_LOGIN_URL') )
			define('USCES_LOGIN_URL', get_page_link(USCES_MEMBER_NUMBER) . '&page=login');
		if( ! defined('USCES_LOGOUT_URL') )
			define('USCES_LOGOUT_URL', get_page_link(USCES_MEMBER_NUMBER) . '&page=logout');
		if( ! defined('USCES_MEMBER_URL') )
			define('USCES_MEMBER_URL', get_page_link(USCES_MEMBER_NUMBER));
		$inquiry_url = ( !isset( $usces->options['inquiry_id'] ) || !( (int)$usces->options['inquiry_id'] )) ? home_url() : get_page_link($usces->options['inquiry_id']);
		if( ! defined('USCES_INQUIRY_URL') )
			define('USCES_INQUIRY_URL', $inquiry_url);
		if( ! defined('USCES_MEMBER_HISTORY_URL') )
			define('USCES_MEMBER_HISTORY_URL', get_page_link(USCES_MEMBER_NUMBER) . '&page=history');
	}

}

function wcmb_nav_menu($nav_menu, $args){
	global $wcmb;
	if( DOCOMO === $wcmb['device_div'] || SOFTBANK === $wcmb['device_div'] || KDDI === $wcmb['device_div'] ) {
		$new_nav_menu = str_replace( array('<li', '</li>'), array('<span', '</span> / '), $nav_menu );
		$new_nav_menu = str_replace( " / \n</div>", '</div>', $new_nav_menu );
		return $new_nav_menu;
	}else{
		return $nav_menu;
	}
}

function wcmb_smartphone_output(){
	global $wcmb;

	if ( defined('WP_USE_THEMES') && WP_USE_THEMES ){
		$template = false;
		if     ( is_404()            && $template = get_404_template()            ) :
		elseif ( is_search()         && $template = get_search_template()         ) :
		elseif ( is_tax()            && $template = get_taxonomy_template()       ) :
		elseif ( is_front_page()     && $template = get_front_page_template()     ) :
		elseif ( is_home()           && $template = get_home_template()           ) :
		elseif ( is_attachment()     && $template = get_attachment_template()     ) :
			remove_filter('the_content', 'prepend_attachment');
		elseif ( is_single()         && $template = get_single_template()         ) :
		elseif ( is_page()           && $template = get_page_template()           ) :
		elseif ( is_category()       && $template = get_category_template()       ) :
		elseif ( is_tag()            && $template = get_tag_template()            ) :
		elseif ( is_author()         && $template = get_author_template()         ) :
		elseif ( is_date()           && $template = get_date_template()           ) :
		elseif ( is_archive()        && $template = get_archive_template()        ) :
		elseif ( is_comments_popup() && $template = get_comments_popup_template() ) :
		elseif ( is_paged()          && $template = get_paged_template()          ) :
		else :
			$template = get_index_template();
		endif;
		$template = str_replace(get_template(), wcmb_mobile_template(), $template );
		include( $template );
	}
	exit;
}

function wcmb_smartphone_template(){
	return wcmb_mobile_template();
}

function wcmb_smartphone_stylesheet($uri){
	return wcmb_mobile_template();
}

function wcmb_garak_template(){
	return wcmb_mobile_template();
}

function wcmb_garak_stylesheet($uri){
	return wcmb_mobile_template();
}

function wcmb_smartphone_wp_head(){
	if( defined('WCEX_ITEM_LIST_LAYOUT') ) {
		remove_filter('usces_filter_search_query', 'ill_filter_search_query',10);
		remove_filter('usces_filter_search_result', 'ill_filter_search_result', 10, 2);
		remove_action('init', 'wcex_item_list_layout_init', 12);
	}
}

function wcmb_add_admin_pages(){
	add_submenu_page(USCES_PLUGIN_BASENAME, __('Mobile Setting','mobile'), __('Mobile Setting','mobile'), 'level_6', 'wcex_mobile_setting', 'admin_mobile_setting_page');
}

function wcmb_sessid_flag(){
	return 'mobile';
}

function wcmb_home_url(){
	global $usces;
	$args = func_get_args();
	$link = rtrim($args[0], "/");
//	$s = ($usces->delim == '?') ? '/' : '';
//	$link .= ($link == get_option('home')) ? '/?' : $s.$usces->delim;
//	$link .= 'uscesid=' . $usces->get_uscesid();
//	return $link;
	return $link . '/?uscesid=' . $usces->get_uscesid('mobile');
}

function wcmb_post_link(){
	global $usces;
	$permalink_structure = get_option('permalink_structure');
	$uscesid = '/?uscesid=' . $usces->get_uscesid();
	$args = func_get_args();
	$link = str_replace($uscesid, '', $args[0]);
	if( $permalink_structure ){
		if( '/' != substr($link,-1) ){
			$link .= '/';
		}
		$link .= '?uscesid=' . $usces->get_uscesid('mobile');
	}else{
		$link .= '&uscesid=' . $usces->get_uscesid('mobile');
	}

	return $link;
}

//function wcmb_ssl_page_link(){
//	global $usces;
//	$args = func_get_args();
//	$link = rtrim($args[0], "/");
//	$s = ($usces->delim == '?') ? '/' : '';
//	$link .= ($link == get_option('siteurl')) ? '/?' : $s.$usces->delim;
//	$link .= 'uscesid=' . $usces->get_uscesid();
//	return $link;
//}
//
//function wcmb_ssl_site_url(){
//	global $usces;
//	$args = func_get_args();
//	$link = $args[0];
//	$show = $args[1];
//	if( 'url' == $show || 'home' == $show ){
//		return USCES_SSL_URL . '?uscesid=' . $usces->get_uscesid();
//	}else{
//		return $link;
//	}
//}

function wcmb_main(){
	global $usces, $wcmb;

	remove_action('wp_head', array($usces, 'shop_head'));
	remove_action('wp_head', 'wp_enqueue_scripts');
	remove_action('wp_head', 'feed_links');
	remove_action('wp_head', 'feed_links_extra');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'locale_stylesheet');
	remove_action('wp_head', 'wp_print_scripts');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wp_print_head_scripts');
	remove_action('wp_head', 'wp_print_footer_scripts');
	remove_action('wp_footer', 'wp_admin_bar_render', 1000);
}

function wcmb_mobile_template(){
	global $wcmb, $wcmb_options;

	if( SMARTPHONE === $wcmb['device_div'] ){
		return $wcmb_options['smart_template'];
	}else{
		return $wcmb_options['garak_template'];
	}
}

function wcmb_mobile_stylesheet(){
	global $wcmb_options;
	return $wcmb_options['garak_template'];
}

function admin_mobile_setting_page() {
	global $usces, $wcmb_options;


	if( isset($_POST['update_wcmb_options']) ){

		$_POST = $usces->stripslashes_deep_post($_POST);
		$usces->action_status = 'success';
		$usces->action_message = 'オプションを更新しました';

		$garak_template = trim($_POST['garak_template']);
		$garak_telop = trim($_POST['garak_telop']);
		$garak_logo = $_POST['garak_logo'];
		$garak_logo_uri = trim($_POST['garak_logo_uri']);
		$garak_ssl = isset($_POST['garak_ssl']) ? 1 : 0;
		$garak_description = isset($_POST['garak_description']) ? 1 : 0;
		$garak_referer_check = isset($_POST['garak_referer_check']) ? 1 : 0;
		$garak_rejection = isset($_POST['garak_rejection']) ? 1 : 0;
		$smart_template = trim($_POST['smart_template']);
		$smart_ssl = isset($_POST['smart_ssl']) ? 1 : 0;
		$smart_remote_address = isset($_POST['smart_remote_address']) ? 1 : 0;
		$smart_pc_theme = isset($_POST['smart_pc_theme']) ? 1 : 0;
		$smart_theme_switch = isset($_POST['smart_theme_switch']) ? 1 : 0;

		$wcmb_options['garak_template'] = !empty($garak_template) ? $garak_template : 'mobile_garak_default';
		$wcmb_options['garak_telop'] = $garak_telop;
		$wcmb_options['garak_logo'] = $garak_logo;
		$wcmb_options['garak_logo_uri'] = !empty($garak_logo_uri) ? $garak_logo_uri : WP_CONTENT_URL . '/themes/' . $wcmb_options['garak_template'] . '/images/image_top.jpg';
		$wcmb_options['garak_ssl'] = $garak_ssl;
		$wcmb_options['garak_description'] = $garak_description;
		$wcmb_options['garak_referer_check'] = $garak_referer_check;
		$wcmb_options['garak_rejection'] = $garak_rejection;
		$wcmb_options['smart_template'] = !empty($smart_template) ? $smart_template : 'mobile_smart_default';
		$wcmb_options['smart_ssl'] = $smart_ssl;
		$wcmb_options['smart_remote_address'] = $smart_remote_address;
		$wcmb_options['smart_pc_theme'] = $smart_pc_theme;
		$wcmb_options['smart_theme_switch'] = $smart_theme_switch;

		update_option('wcmb', $wcmb_options);
	}

	if(empty($usces->action_message) || $usces->action_message == '') {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}

	require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_mobile_setting_page.php');
}

function wcmb_after_cart_instant() {
	mb_convert_variables('UTF-8', "SJIS", $_POST);
}

function wcmb_filter_wpcf7_display_message($message, $status){
	global $wcmb;
	$cf_post = array();
	foreach( $_POST as $key => $value ){
		if( false === strpos( $key, '_wpcf7' ) ){
			$cf_post[$key] = $value;
		}
	}
	$wcmb['cf_post'] = $cf_post;
	if( isset( $_POST['_wpcf7'] ) && 'mail_sent_ok' == $status)
		wcmb_cf7_inquiry();

	return $message;
}

function wcmb_cf7_inquiry(){
	$cf = $_POST['_wpcf7'];
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_inquiry_'.$cf);
	add_filter('the_content', 'wcmb_filter_cf7_inquiry_the_content');
}

function wcmb_filter_cf7_inquiry_the_title( $title ){
	return 'Title OK';
}

function wcmb_filter_cf7_inquiry_the_content( $content ){
	global $wcmb, $contact_form;
	$id = $_POST['_wpcf7'];
	$wpcf7_contact_form = wpcf7_contact_form( $id );
	$regex = '/\[\s*([a-zA-Z_][0-9a-zA-Z:._-]*)\s*\]/';
	$body = preg_replace_callback( $regex, 'wcmb_mail_callback', $wpcf7_contact_form->mail['body'] );

	$html = '<br /><div class="wcmb_cf7_message">' . esc_html($wpcf7_contact_form->messages['mail_sent_ok']) . '</div><br />';
	$html .= '<hr />';
	$html .= '<br /><div class="wcmb_cf7_content"><p>' . nl2br(esc_html($body)). '</p></div><br />';
	unset($wcmb['cf_post']);
	return $html;
}

function wcmb_mail_callback( $matches ){
	global $wcmb;

	$out = '';
	foreach( $wcmb['cf_post'] as $key => $value ){
		if( $key == $matches[1] ){
			if( is_array($value) ){
				$out = implode(',', $value);
			}else{
				$out = $value;
			}
		}
	}
	if( '' == $out )
		return $matches[1];
	else
		return $out;
}

function wcmb_customerinfologin(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->error_message = $usces->zaiko_check();
	$usces->error_message = apply_filters( 'usces_filter_cart_check', $usces->error_message );
	if($usces->error_message == ''){
		if( wcmb_is_member_logged_in() ){
			$usces->error_message = has_custom_customer_field_essential();
			$usces->page = ($usces->error_message == '') ? 'delivery' : 'customer';
		}else{
			$usces->page = 'customerinfologin';
		}
	}else{
		$usces->page = 'cart';
	}
	if ( !$usces->cart->is_order_condition() ) {
		$order_conditions = $usces->get_condition();
		$usces->cart->set_order_condition($order_conditions);
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_customerinfologin2(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->error_message = $usces->lostpass_mailaddcheck();
	if($usces->error_message == ''){
		if($usces->member_login() == 'member') {
			$usces->error_message = has_custom_customer_field_essential();
			$usces->page = ($usces->error_message == '') ? 'delivery' : 'customer';
		} else {
			$usces->page = 'customerinfologin';
		}
	} else {
		$usces->page = 'customerinfologin';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_customercountry(){
	global $usces;
	$action_filter = 'action_cartFilter';
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->error_message = $usces->zaiko_check();
	if($usces->error_message == ''){
		$target_market = ( isset($usces->options['system']['target_market']) && !empty($usces->options['system']['target_market']) ) ? $usces->options['system']['target_market'] : usces_get_local_target_market();
		if(count($target_market) > 1) {
			$usces->page = 'customercountry';
		} else {
			if( defined('WCEX_DLSELLER') ) {
				if( dlseller_have_shipped() ) {
					$usces->page = 'customer';
				} else {
					$usces->page = 'newmemberform';
					$action_filter = 'action_memberFilter';
				}
			} elseif( defined('WCEX_AUTO_DELIVERY') ) {
				if( wcad_have_regular_order() ) {
					$usces->page = 'newmemberform';
					$action_filter = 'action_memberFilter';
				} else {
					$usces->page = 'customer';
				}
			} else {
				$usces->page = 'customer';
			}
		}
	} else {
		$usces->page = 'cart';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, $action_filter));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_customerinfo2(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->page = 'customer';
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_customer');
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_deliverycountry(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->error_message = wcmb_delivery1_check();
	if( $usces->error_message == '' ){
		if( $_POST['delivery']['delivery_flag'] == '1' ) {
			$target_market = ( isset($usces->options['system']['target_market']) && !empty($usces->options['system']['target_market']) ) ? $usces->options['system']['target_market'] : usces_get_local_target_market();
			if(count($target_market) > 1) {
				$usces->page = 'deliverycountry';
			} else {
				$usces->page = 'delivery2';
			}
		} else {
			$usces->page = 'delivery3';
		}
	}else{
		$usces->page = 'delivery';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_deliveryinfo2(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->page = 'delivery2';
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_delivery2');
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_deliveryinfo3(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->error_message = wcmb_delivery2_check();
	if( $usces->error_message == '' ){
		$usces->page = 'delivery3';
	}else{
		$usces->page = 'delivery2';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_deliveryinfo4(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->error_message = wcmb_delivery3_check();
	if( $usces->error_message == '' ){
		$payments = $usces->getPayments($_POST['offer']['payment_name']);
		if('acting_zeus_card' == $payments['settlement'] or 'acting_zeus_conv' == $payments['settlement'] or 'acting_remise_card' == $payments['settlement']) {
			$page_name = 'delivery4';
			if('acting_remise_card' == $payments['settlement']) {
				$cart = $usces->cart->get_cart();
				$paymod_id = 'remise';
				$charging_type = $usces->getItemChargingType($cart[0]['post_id']);
				if( 'on' != $usces->options['acting_settings'][$paymod_id]['card_activate'] 
					|| 'on' != $usces->options['acting_settings'][$paymod_id]['howpay'] 
					|| 'on' != $usces->options['acting_settings'][$paymod_id]['activate'] 
					|| 'continue' == $charging_type ) 
					$page_name = 'confirm';

			} elseif( 'acting_zeus_card' == $payments['settlement'] ) {
				$paymod_id = 'zeus';
				$pcid = NULL;
				if( wcmb_is_member_logged_in() ) {
					$member = $usces->get_member();
					$pcid = $usces->get_member_meta_value( 'zeus_pcid', $member['ID'] );
				}
				if( '2' == $usces->options['acting_settings'][$paymod_id]['security'] && 'on' == $usces->options['acting_settings'][$paymod_id]['quickcharge'] && $pcid != NULL ) 
					$page_name = 'confirm';
			}
			$usces->page = $page_name;
		} else {
			$usces->page = 'confirm';
		}
	}else{
		$usces->page = 'delivery3';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_confirm2(){
	global $usces, $wpdb;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}

	$usces->cart->entry();
	$usces->error_message = $usces->zaiko_check();
	if( $usces->error_message != '' ) {
		$usces->page = 'cart';

	} else {
		$usces->set_reserve_pre_order_id();
		if( isset($_POST['confirm']) ) {
			$usces->error_message = wcmb_delivery4_check();
		}
		if( $usces->error_message == '' ) {
			if( usces_is_member_system() && usces_is_member_system_point() && wcmb_is_member_logged_in() ) {
				unset( $_SESSION['usces_entry']['order']['usedpoint'] );
				$member_table = $wpdb->prefix."usces_member";
				$query = $wpdb->prepare("SELECT mem_point FROM $member_table WHERE ID = %d", $_SESSION['usces_member']['ID']);
				$mem_point = $wpdb->get_var( $query );
				$_SESSION['usces_member']['point'] = $mem_point;
			}
			$usces->page = 'confirm';
		} else {
			$usces->page = 'delivery4';
		}
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_backcustomercountry(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$target_market = ( isset($usces->options['system']['target_market']) && !empty($usces->options['system']['target_market']) ) ? $usces->options['system']['target_market'] : usces_get_local_target_market();
	if(count($target_market) > 1) {
		$usces->page = 'customercountry';
	} else {
		$usces->page = 'backCart';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_backdeliverycountry(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$target_market = ( isset($usces->options['system']['target_market']) && !empty($usces->options['system']['target_market']) ) ? $usces->options['system']['target_market'] : usces_get_local_target_market();
	if(count($target_market) > 1) {
		$usces->page = 'deliverycountry';
	} else {
		$usces->page = 'delivery';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_backdelivery1(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->cart->entry();
	$usces->page = 'delivery';
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_delivery');
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_backdelivery2(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	if( defined('WCEX_DLSELLER') ) {
		if( dlseller_have_shipped() ) {
			$usces->page = 'delivery2';
		} else {
			$usces->page = 'backCart';
		}
	} elseif( defined('WCEX_AUTO_DELIVERY') ) {
		if( wcad_have_regular_order() ) {
			$usces->page = 'backCart';
		} else {
			$usces->page = 'delivery2';
		}
	} else {
		$usces->page = 'delivery2';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_delivery2');
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_backdelivery3(){
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}
	$usces->page = 'delivery3';
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_delivery3');
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_regmemberdl() {
	global $usces;
	if( false === $usces->cart->num_row() ){
		header('location: ' . home_url());
		exit;
	}

	if( $usces->regist_member() == 'newcompletion' ) {
		$usces->member_just_login(stripslashes(trim($_POST['member']['mailaddress1'])), stripslashes(trim($_POST['member']['password1'])));
		$usces->cart->entry();
		$usces->page = 'delivery';
	}else{
		$usces->cart->entry();
		$usces->page = 'customer';
	}
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_'.$usces->page);
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_go2top(){
	header('location: ' . home_url());
	exit;
}

function wcmb_delivery1_check() {
	global $usces;
	$mes = '';
	$usces_entries = $usces->cart->get_entry();

	if ( !isset($_POST['offer']['delivery_method']) || (empty($_POST['offer']['delivery_method']) && $_POST['offer']['delivery_method'] != 0) )
		$mes .= __('chose one from delivery method.', 'usces') . "<br />";

	if ( $_POST['delivery']['delivery_flag'] == '0' ) {
		if(isset($_POST['offer']['delivery_method'])) {
			$d_method_index = $usces->get_delivery_method_index((int)$_POST['offer']['delivery_method']);
			$country = $usces_entries['delivery']['country'];
			$local_country = usces_get_base_country();
			if($country == $local_country) {
				if($usces->options['delivery_method'][$d_method_index]['intl'] == '1') {
					$mes .= __('配送方法が誤っています。国際便は指定できません。', 'usces') . "<br />";
				}
			} else {
				if($usces->options['delivery_method'][$d_method_index]['intl'] == '0') {
					$mes .= __('配送方法が誤っています。国際便を指定してください。', 'usces') . "<br />";
				}
			}
		}
	}

	$mes = apply_filters('wcmb_filter_delivery1_check', $mes);

	return $mes;
}

function wcmb_delivery2_check() {
	global $usces;
	$mes = '';
	$usces_entries = $usces->cart->get_entry();

	if ( trim($_POST["delivery"]["name1"]) == "" )
		$mes .= __('Name is not correct', 'usces');
	if ( trim($_POST["delivery"]["zipcode"]) == "" )
		$mes .= __('postal code is not correct', 'usces') . "<br />";
	if ( $_POST["delivery"]["pref"] == __('-- Select --', 'usces') )
		$mes .= __('enter the prefecture', 'usces') . "<br />";
	if ( trim($_POST["delivery"]["address1"]) == "" )
		$mes .= __('enter the city name', 'usces') . "<br />";
	if ( trim($_POST["delivery"]["address2"]) == "" )
		$mes .= __('enter house numbers', 'usces') . "<br />";
	if ( trim($_POST["delivery"]["tel"]) == "" )
		$mes .= __('enter phone numbers', 'usces') . "<br />";

	if(isset($usces_entries['order']['delivery_method'])) {
		$d_method_index = $usces->get_delivery_method_index((int)$usces_entries['order']['delivery_method']);
		$country = $_POST["delivery"]["country"];
		$local_country = usces_get_base_country();
		if($country == $local_country) {
			if($usces->options['delivery_method'][$d_method_index]['intl'] == '1') {
				$mes .= __('配送方法が誤っています。国際便は指定できません。', 'usces') . "<br />";
			}
		} else {
			if($usces->options['delivery_method'][$d_method_index]['intl'] == '0') {
				$mes .= __('配送方法が誤っています。国際便を指定してください。', 'usces') . "<br />";
			}
		}
	}

	$mes = apply_filters('wcmb_filter_delivery2_check', $mes);

	return $mes;
}

function wcmb_delivery3_check() {
	global $usces;
	$mes = '';
	$usces_entries = $usces->cart->get_entry();

	if( !isset($_POST['offer']['payment_name']) )
		$mes .= __('chose one from payment options.', 'usces') . "<br />";
	if( isset($usces_entries['order']['delivery_method']) and isset($_POST['offer']['payment_name']) ) {
		$d_method_index = $usces->get_delivery_method_index((int)$usces_entries['order']['delivery_method']);
		if( $usces->options['delivery_method'][$d_method_index]['nocod'] == '1' ) {
			$payments = $usces->getPayments($_POST['offer']['payment_name']);
			if('COD' == $payments['settlement'])
				$mes .= __('COD is not available.', 'usces') . "<br />";
		}
	}
//20120510ysk start 0000444
	$meta = usces_has_custom_field_meta('order');
	foreach($meta as $key => $entry) {
		$essential = $entry['essential'];
		if($essential == 1) {
			$name = $entry['name'];
			$means = $entry['means'];
			if($means == 2) {//Text
				if(trim($_POST['custom_order'][$key]) == "")
					$mes .= __($name.'を入力してください。', 'usces')."<br />";
			} else {
				if(!isset($_POST['custom_order'][$key]) or $_POST['custom_order'][$key] == "#NONE#")
					$mes .= __($name.'を選択してください。', 'usces')."<br />";
			}
		}
	}
//20120510ysk end

	if( defined('WCEX_DLSELLER') ) {
		if ( !isset($_POST['offer']['terms']) && dlseller_have_dlseller_content() )
			$mes .= __('Not agree', 'dlseller') . "<br />";
	}

	$mes = apply_filters('wcmb_filter_delivery3_check', $mes);

	return $mes;
}

function wcmb_delivery4_check() {
	global $usces;
	$mes = '';
	$usces_entries = $usces->cart->get_entry();

	$mes = apply_filters('wcmb_filter_delivery4_check', $mes);

	return $mes;
}

function wcmb_previous(){
	global $usces;
	if( isset($_SESSION['usces_previous_url']) ){
		if( strpos($_SESSION['usces_previous_url'], 'uscesid=') ){
			$previous_url = $_SESSION['usces_previous_url'];
		}else{
			 if( strpos($_SESSION['usces_previous_url'], '?') ){
				$previous_url = $_SESSION['usces_previous_url'] . '&uscesid=' . $usces->get_uscesid();
			 }else{
				 if( '/' == substr($_SESSION['usces_previous_url'], -1) ){
					$previous_url = $_SESSION['usces_previous_url'] . '?uscesid=' . $usces->get_uscesid();
				 }else{
					$previous_url = $_SESSION['usces_previous_url'] . '/?uscesid=' . $usces->get_uscesid();
				 }
			 }
		}
	}else{
		$previous_url = home_url();
	}
	header('location: ' . $previous_url);
	exit;
}

function wcmb_garak_template_redirect() {
	global $post, $usces, $usces_entries, $usces_carts, $usces_members, $member_regmode, $wcmb, $wcmb_options, $usces_item;

	if( apply_filters( 'wcmb_filter_garak_template_redirect', false ) ) return;

	if( !$wcmb['target'] ){
		include(get_stylesheet_directory() . '/wc_templates/wc_no_support.php');
		exit;
	}else if( is_single() && 'item' == $post->post_mime_type ) {
		if( defined('WCEX_DLSELLER') ) {
			$division = dlseller_get_division( $post->ID );
			if( 'data' == $division ){
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single_data.php') ){
					if( !post_password_required($post) ){
						include(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single_data.php');
						exit;
					}
				}
			}elseif( 'service' == $division ){
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single_service.php') ){
					if( !post_password_required($post) ){
						include(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single_service.php');
						exit;
					}
				}
			}else{
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single.php') ){
					if( !post_password_required($post) ){
						include(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single.php');
						exit;
					}
				}
			}
		} else {
			if( file_exists(get_stylesheet_directory() . '/wc_templates/wc_item_single.php') ){
				if( !post_password_required($post) ){
					include(get_stylesheet_directory() . '/wc_templates/wc_item_single.php');
					exit;
				}
			}
		}
	}elseif( isset($_REQUEST['page']) && ('search_item' == $_REQUEST['page'] || 'usces_search' == $_REQUEST['page']) && $usces->is_cart_page($_SERVER['REQUEST_URI']) ){
		if( file_exists(get_stylesheet_directory() . '/wc_templates/wc_search_page.php') ){
			$wcmb_delim = '&';
			include(get_stylesheet_directory() . '/wc_templates/wc_search_page.php');
			exit;
		}

	}else if( $usces->is_cart_page($_SERVER['REQUEST_URI']) ){
		switch( $usces->page ){
			case 'customerinfologin':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_customer_login_page.php') ){
					usces_get_entries();
					usces_get_member_regmode();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_customer_login_page.php');
					exit;
				}
				break;
			case 'customercountry':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_customer_country_page.php') ){
					usces_get_entries();
					usces_get_member_regmode();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_customer_country_page.php');
					exit;
				}
				break;
			case 'customer':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_customer_page.php') ){
					usces_get_entries();
					usces_get_member_regmode();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_customer_page.php');
					exit;
				}
				break;
			case 'deliverycountry':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_delivery_country_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_delivery_country_page.php');
					exit;
				}
				break;
			case 'delivery':
				if( defined('WCEX_DLSELLER') ) {
					if( dlseller_have_shipped() ) {
						$wc_templates = '/wc_templates';
						$wc_delivery = 'wc_delivery1';
					} else {
						$wc_templates = '/wc_templates_dlseller';
						$wc_delivery = 'wc_delivery3';
					}
				} else {
					$wc_templates = '/wc_templates';
					$wc_delivery = 'wc_delivery1';
				}
				if( file_exists(get_stylesheet_directory() . $wc_templates . '/cart/'.$wc_delivery.'_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . $wc_templates . '/cart/'.$wc_delivery.'_page.php');
					exit;
				}
				break;
			case 'delivery2':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_delivery2_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_delivery2_page.php');
					exit;
				}
				break;
			case 'delivery3':
				$wc_templates = ( defined('WCEX_DLSELLER') ) ? '/wc_templates_dlseller' : '/wc_templates';
				if( file_exists(get_stylesheet_directory() . $wc_templates . '/cart/wc_delivery3_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . $wc_templates . '/cart/wc_delivery3_page.php');
					exit;
				}
				break;
			case 'delivery4':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_delivery4_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_delivery4_page.php');
					exit;
				}
				break;
			case 'confirm':
				$wc_templates = ( defined('WCEX_DLSELLER') ) ? '/wc_templates_dlseller' : '/wc_templates';
				if( file_exists(get_stylesheet_directory() . $wc_templates . '/cart/wc_confirm_page.php') ){
					usces_get_entries();
					usces_get_carts();
					usces_get_members();
					include(get_stylesheet_directory() . $wc_templates . '/cart/wc_confirm_page.php');
					exit;
				}
				break;
			case 'ordercompletion':
				$wc_templates = ( defined('WCEX_DLSELLER') ) ? '/wc_templates_dlseller' : '/wc_templates';
				if( file_exists(get_stylesheet_directory() . $wc_templates . '/cart/wc_completion_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . $wc_templates . '/cart/wc_completion_page.php');
					exit;
				}
				break;
			case 'error':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_cart_error_page.php') ){
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_cart_error_page.php');
					exit;
				}
			case 'newmemberform':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_new_member_page.php') ){
					$member_regmode = 'newmemberform';
					include(get_stylesheet_directory() . '/wc_templates/member/wc_new_member_page.php');
					exit;
				}
			case 'cart':
			default:
				global $usces_gp;
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_cart_page.php') ){
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_cart_page.php');
					exit;
				}
		}
	}else if($usces->is_inquiry_page($_SERVER['REQUEST_URI']) ){

	}else if( $usces->is_member_page($_SERVER['REQUEST_URI']) ){
		if($usces->options['membersystem_state'] != 'activate') return true;

		if( wcmb_is_member_logged_in() ) {
			if( isset($usces->page) && 'delivery' == $usces->page ) {
				$wc_templates = ( defined('WCEX_DLSELLER') ) ? '/wc_templates_dlseller' : '/wc_templates';
				if( file_exists(get_stylesheet_directory() . $wc_templates . '/cart/wc_delivery1_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . $wc_templates . '/cart/wc_delivery1_page.php');
					exit;
				}
			}
			if( isset($_REQUEST['page']) && 'history' == $_REQUEST['page'] ) {
				if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_member_history_page.php') ){
					$order_id = $_REQUEST['order_id'];
					include(get_stylesheet_directory() . '/wc_templates/member/wc_member_history_page.php');
					exit;
				}
			}
			$member_regmode = 'editmemberform';
			if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_member_page.php') ){
				include(get_stylesheet_directory() . '/wc_templates/member/wc_member_page.php');
				exit;
			}

		} else {

			switch( $usces->page ){
				case 'login':
					if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_login_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates/member/wc_login_page.php');
						exit;
					}
				case 'newmemberform':
					if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_new_member_page.php') ){
						$member_regmode = 'newmemberform';
						include(get_stylesheet_directory() . '/wc_templates/member/wc_new_member_page.php');
						exit;
					}
				case 'lostmemberpassword':
					if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_lostpassword_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates/member/wc_lostpassword_page.php');
						exit;
					}
				case 'changepassword':
					if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_changepassword_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates/member/wc_changepassword_page.php');
						exit;
					}
				case 'newcompletion':
				case 'editcompletion':
				case 'lostcompletion':
				case 'changepasscompletion':
					if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_member_completion_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates/member/wc_member_completion_page.php');
						exit;
					}
				default:
					if( file_exists(get_stylesheet_directory() . '/wc_templates/member/wc_login_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates/member/wc_login_page.php');
						exit;
					}
			}
		}
	}
	return true;
}

function wcmb_smartphone_template_redirect() {
	global $usces, $post, $usces_entries, $usces_carts, $usces_members, $usces_item, $usces_gp, $member_regmode;

	if( apply_filters( 'wcmb_filter_smartphone_template_redirect', false ) ) return;

	if( is_single() && 'item' == $post->post_mime_type ) {
		$division = dlseller_get_division( $post->ID );
		$usces_item = $usces->get_item( $post->ID );
		if( 'data' == $division ){
			if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single_data.php') ){
				if( !post_password_required($post) ){
					include(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single_data.php');
					exit;
				}
			}
		}elseif( 'service' == $division ){
			if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single_service.php') ){
				if( !post_password_required($post) ){
					include(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single_service.php');
					exit;
				}
			}
		}else{
			if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single.php') ){
				if( !post_password_required($post) ){
					include(get_stylesheet_directory() . '/wc_templates_dlseller/wc_item_single.php');
					exit;
				}
			}
		}
		return true;
	}elseif( isset($_REQUEST['page']) && ('search_item' == $_REQUEST['page'] || 'usces_search' == $_REQUEST['page']) && $usces->is_cart_page($_SERVER['REQUEST_URI']) ){
		if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/wc_search_page.php') ){
			include(get_stylesheet_directory() . '/wc_templates_dlseller/wc_search_page.php');
			exit;
		}

	}else if( $usces->is_cart_page($_SERVER['REQUEST_URI']) ){
		switch( $usces->page ){
			case 'customer':
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_customer_page.php') ){
					usces_get_entries();
					usces_get_member_regmode();
					include(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_customer_page.php');
					exit;
				}
			case 'delivery':
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_delivery_page.php') ){
					usces_get_entries();
					usces_get_carts(); 
					include(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_delivery_page.php');
					exit;
				}
			case 'confirm':
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_confirm_page.php') ){
					usces_get_entries();
					usces_get_carts();
					usces_get_members();
					include(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_confirm_page.php');
					exit;
				}
			case 'ordercompletion':
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_completion_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_completion_page.php');
					exit;
				}
			case 'error':
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_cart_error_page.php') ){
					include(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_cart_error_page.php');
					exit;
				}
			case 'cart':
			default:
				if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_cart_page.php') ){
					include(get_stylesheet_directory() . '/wc_templates_dlseller/cart/wc_cart_page.php');
					exit;
				}
		}
		return true;
	}else if($usces->is_inquiry_page($_SERVER['REQUEST_URI']) ){

	}else if( $usces->is_member_page($_SERVER['REQUEST_URI']) ){
		if($usces->options['membersystem_state'] != 'activate') return;

		if( wcmb_is_member_logged_in() ) {
			$member_regmode = 'editmemberform';
			if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_member_page.php') ){
				include(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_member_page.php');
				exit;
			}

		} else {

			switch( $usces->page ){
				case 'login':
					if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_login_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_login_page.php');
						exit;
					}
				case 'newmemberform':
					if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_new_member_page.php') ){
						$member_regmode = 'newmemberform';
						include(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_new_member_page.php');
						exit;
					}
				case 'lostmemberpassword':
					if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_lostpassword_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_lostpassword_page.php');
						exit;
					}
				case 'changepassword':
					if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_changepassword_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_changepassword_page.php');
						exit;
					}
				case 'newcompletion':
				case 'editcompletion':
				case 'lostcompletion':
				case 'changepasscompletion':
					if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_member_completion_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_member_completion_page.php');
						exit;
					}
				default:
					if( file_exists(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_login_page.php') ){
						include(get_stylesheet_directory() . '/wc_templates_dlseller/member/wc_login_page.php');
						exit;
					}
			}
		}
		return true;
	}
}

function wcmb_filter_confirm_inform() {
	global $usces, $wcmb, $usces_entries, $wcmb_options;
	$args = func_get_args();
	$html = $args[0];
	$payments = $args[1];
	$acting_flag = $args[2];
	$rand = $args[3];
	$purchase_disabled = $args[4];

	if( SMARTPHONE === $wcmb['device_div'] or PC === $wcmb['device_div'] ) return $html;

	$usces_entries = $usces->cart->get_entry();
	$cart = $usces->cart->get_cart();

	if( 'acting' != substr($payments['settlement'], 0, 6) || 0 == $usces_entries['order']['total_full_price'] ){
	}else{
		switch( $acting_flag ) {
			//クレジット決済(ゼウス)
			case 'acting_zeus_card':
				$acting_opts = $usces->options['acting_settings']['zeus'];
				$html = '<form id="purchase_form" action="' . USCES_CART_URL . '" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">';
				$member = $usces->get_member();
				$pcid = $usces->get_member_meta_value('zeus_pcid', $member['ID']);
				$securecode = isset($_REQUEST['securecode']) ? $_REQUEST['securecode'] : '';
				if( '2' == $acting_opts['security'] && 'on' == $acting_opts['quickcharge'] && $pcid == '8888888888888888' && wcmb_is_member_logged_in() ) {
					$cnum1 = '8888888888888888';
					$expyy = '2010';
					$expmm = '10';
					$username = 'QUICKCHARGE';
				} else {
					$cnum1 = isset($_REQUEST['cnum1']) ? $_REQUEST['cnum1'] : '';
					$expyy = isset($_REQUEST['expyy']) ? $_REQUEST['expyy'] : '';
					$expmm = isset($_REQUEST['expmm']) ? $_REQUEST['expmm'] : '';
					$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
				}
				$html .= '<input type="hidden" name="cardnumber" value="' . esc_attr($cnum1) . '">';
				if( '1' == $acting_opts['security'] ) {
					$html .= '<input type="hidden" name="securecode" value="' . esc_attr($securecode) . '">';
				}
				$html .= '<input type="hidden" name="expyy" value="' . esc_attr($expyy) . '">
					<input type="hidden" name="expmm" value="' . esc_attr($expmm) . '">';
				$html .= '<input type="hidden" name="telno" value="' . esc_attr(str_replace('-', '', $usces_entries['customer']['tel'])) . '">
					<input type="hidden" name="email" value="' . esc_attr($usces_entries['customer']['mailaddress1']) . '">
					<input type="hidden" name="sendid" value="' . $member['ID'] . '">
					<input type="hidden" name="username" value="' . esc_attr($username) . '">
					<input type="hidden" name="money" value="' . usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false) . '">
					<input type="hidden" name="sendpoint" value="' . $rand . '">
					<input type="hidden" name="printord" value="yes">';
				if( isset($_REQUEST['cbrand']) ) {
					$div_name = 'div_' . $_REQUEST['cbrand'];
					$howpay = ( '01' == $_REQUEST[$div_name] ) ? '1' : '0';
					if( '0' == $howpay ) {
						$html .= '<input type="hidden" name="howpay" value="' . $howpay . '">';
						$html .= '<input type="hidden" name="cbrand" value="' . $_REQUEST['cbrand'] . '">';
						$html .= '<input type="hidden" name="div" value="' . $_REQUEST[$div_name] . '">';
						$html .= '<input type="hidden" name="div_1" value="' . $_REQUEST['div_1'] . '">';
						$html .= '<input type="hidden" name="div_2" value="' . $_REQUEST['div_2'] . '">';
						$html .= '<input type="hidden" name="div_3" value="' . $_REQUEST['div_3'] . '">';
					}
				}
				$html .= '
					<input type="hidden" name="cnum1" value="' . esc_html($cnum1) . '">
					<div class="send"><input name="backDelivery" type="submit" id="back_button" class="back_to_delivery_button" value="'.__('Back', 'usces').'"' . apply_filters('usces_filter_confirm_prebutton', NULL) . ' />
					<input name="purchase" type="submit" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"' . apply_filters('usces_filter_confirm_nextbutton', NULL) . $purchase_disabled . ' /></div>';
				break;

			//コンビニ決済(ゼウス)
			case 'acting_zeus_conv':
				$member = $usces->get_member();
				$acting_opts = $usces->options['acting_settings']['zeus'];
				$html = '<form id="purchase_form" action="' . USCES_CART_URL . '" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">';
				$html .= '
					<input type="hidden" name="act" value="secure_order">
					<input type="hidden" name="money" value="' . usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false) . '">
					<input type="hidden" name="username" value="' . esc_attr(trim($usces_entries['customer']['name3']) . trim($usces_entries['customer']['name4'])) . '">
					<input type="hidden" name="telno" value="' . esc_attr(str_replace('-', '', $usces_entries['customer']['tel'])) . '">
					<input type="hidden" name="email" value="' . esc_attr($usces_entries['customer']['mailaddress1']) . '">
					<input type="hidden" name="pay_cvs" value="' . $_REQUEST['pay_cvs'] . '">
					<input type="hidden" name="sendid" value="' . $member['ID'] . '">
					<input type="hidden" name="sendpoint" value="' . $rand . '">';
				if( '' != $acting_opts['testid_conv'] ){
					$html .= '<input type="hidden" name="testid" value="' . $acting_opts['testid_conv'] . '">';
					$html .= '<input type="hidden" name="test_type" value="' . $acting_opts['test_type_conv'] . '">';
				}
				$html .= '
					<div class="send"><input name="backDelivery" type="submit" id="back_button" class="back_to_delivery_button" value="'.__('Back', 'usces').'"' . apply_filters('usces_filter_confirm_prebutton', NULL) . ' />
					<input name="purchase" type="submit" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"' . apply_filters('usces_filter_confirm_nextbutton', NULL) . $purchase_disabled . ' /></div>';
				break;

			//入金お任せサービス(ゼウス)
			case 'acting_zeus_bank':
				$member = $usces->get_member();
				$acting_opts = $usces->options['acting_settings']['zeus'];
				$html = '<form id="purchase_form" action="' . $acting_opts['bank_url'] . '" method="post" onKeyDown="if (event.keyCode == 13) {return false;}" accept-charset="Shift_JIS">';
				$html .= '
					<input type="hidden" name="clientip" value="' . esc_attr($acting_opts['clientip_bank']) . '">
					<input type="hidden" name="act" value="order">
					<input type="hidden" name="money" value="' . usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false) . '">';
				if( '' != $acting_opts['testid_bank'] ){	
					$html .= '<input type="hidden" name="username" value="' . esc_attr(trim($usces_entries['customer']['name3']) . trim($usces_entries['customer']['name4']) . '_' . $acting_opts['testid_bank']) . '">';
					$html .= '<input type="hidden" name="telno" value="99999999999">';
				}else{
					$html .= '<input type="hidden" name="username" value="' . esc_attr(trim($usces_entries['customer']['name3']) . trim($usces_entries['customer']['name4'])) . '">';
					$html .= '<input type="hidden" name="telno" value="' . esc_attr(str_replace('-', '', $usces_entries['customer']['tel'])) . '">';
				}
				$html .= '<input type="hidden" name="email" value="' . esc_attr($usces_entries['customer']['mailaddress1']) . '">
					<input type="hidden" name="sendid" value="' . $member['ID'] . '">
					<input type="hidden" name="sendpoint" value="' . $rand . '">
					<input type="hidden" name="siteurl" value="' . get_option('home') . '">
					<input type="hidden" name="sitestr" value="「' . esc_attr(get_option('blogname')) . '」トップページへ">
					';
				$html .= '<input type="hidden" name="dummy" value="&#65533;" />';
				$onclick = ' onClick="document.charset=\'Shift_JIS\';"';
				$html .= '<div class="send"><input name="purchase" type="submit" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"' . apply_filters('usces_filter_confirm_nextbutton', $onclick) . $purchase_disabled . ' /></div>';
				break;

			//クレジット決済(ルミーズ)
			case 'acting_remise_card':
				$charging_type = $usces->getItemChargingType($cart[0]['post_id']);
				$frequency = $usces->getItemFrequency($cart[0]['post_id']);
				$chargingday = $usces->getItemChargingDay($cart[0]['post_id']);
				$acting_opts = $usces->options['acting_settings']['remise'];
				$usces->save_order_acting_data($rand);
				$member = $usces->get_member();
				$send_url = ('public' == $acting_opts['card_pc_ope']) ? $acting_opts['send_url_mbl'] : $acting_opts['send_url_mbl_test'];
				$html = '<form id="purchase_form" name="purchase_form" action="' . $send_url . '" method="post" onKeyDown="if (event.keyCode == 13) {return false;}" accept-charset="Shift_JIS">
					<input type="hidden" name="SHOPCO" value="' . esc_attr($acting_opts['SHOPCO']) . '" />
					<input type="hidden" name="HOSTID" value="' . esc_attr($acting_opts['HOSTID']) . '" />
					<input type="hidden" name="REMARKS3" value="' . $acting_opts['REMARKS3'] . '" />
					<input type="hidden" name="S_TORIHIKI_NO" value="' . $rand . '" />
					<input type="hidden" name="JOB" value="' . apply_filters('usces_filter_remise_card_job', $acting_opts['card_jb']) . '" />
					<input type="hidden" name="MAIL" value="' . esc_attr($usces_entries['customer']['mailaddress1']) . '" />
					<input type="hidden" name="ITEM" value="' . apply_filters('usces_filter_remise_card_item', '0000120') . '" />
					<input type="hidden" name="TOTAL" value="' . usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false) . '" />
					<input type="hidden" name="AMOUNT" value="' . usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false) . '" />
					<input type="hidden" name="TMPURL" value="' . USCES_CART_URL . '&acting=remise_card&acting_return=1&dd='.$wcmb['device_div'].'&dn='.$wcmb['device_name'].'&br='.$wcmb['browser'].'" />
					<input type="hidden" name="EXITURL" value="' . USCES_CART_URL . '&confirm=1&dd='.$wcmb['device_div'].'&dn='.$wcmb['device_name'].'&br='.$wcmb['browser'].'" />
					';

				if( 'on' == $acting_opts['payquick'] && wcmb_is_member_logged_in() ){
					$pcid = $usces->get_member_meta_value('remise_pcid', $member['ID']);
					$html .= '<input type="hidden" name="PAYQUICK" value="1">';
					if( $pcid != NULL )
						$html .= '<input type="hidden" name="PAYQUICKID" value="' . $pcid . '">';
				}
				if( 'on' == $acting_opts['howpay'] && isset($_REQUEST['div']) && '0' !== $_REQUEST['div'] && 'continue' != $charging_type ){
					$html .= '<input type="hidden" name="div" value="' . $_REQUEST['div'] . '">';
					switch( $_REQUEST['div'] ){
						case '1':
							$html .= '<input type="hidden" name="METHOD" value="61">';
							$html .= '<input type="hidden" name="PTIMES" value="2">';
							break;
						case '2':
							$html .= '<input type="hidden" name="METHOD" value="80">';
							break;
					}
				}else{
					$html .= '<input type="hidden" name="div" value="0">';
					$html .= '<input type="hidden" name="METHOD" value="10">';
				}
				if( 'continue' == $charging_type ){
					$nextdate = current_time('mysql');
					$html .= '<input type="hidden" name="AUTOCHARGE" value="1">';
					$html .= '<input type="hidden" name="AC_S_KAIIN_NO" value="' . $member['ID'] . '">';
					$html .= '<input type="hidden" name="AC_NAME" value="' . esc_attr($usces_entries['customer']['name1'].$usces_entries['customer']['name2']) . '">';
					$html .= '<input type="hidden" name="AC_KANA" value="' . esc_attr($usces_entries['customer']['name3'].$usces_entries['customer']['name4']) . '">';
					$html .= '<input type="hidden" name="AC_TEL" value="' . esc_attr(str_replace('-', '', mb_convert_kana($usces_entries['customer']['tel'], 'a', 'UTF-8'))) . '">';
					$html .= '<input type="hidden" name="AC_AMOUNT" value="' . $usces_entries['order']['total_full_price'] . '">';
					$html .= '<input type="hidden" name="AC_TOTAL" value="' . $usces_entries['order']['total_full_price'] . '">';
					$html .= '<input type="hidden" name="AC_NEXT_DATE" value="' . date('Ymd', dlseller_first_charging($cart[0]['post_id'], 'time')) . '">';
					$html .= '<input type="hidden" name="AC_INTERVAL" value="' . $frequency . 'M">';
				}
				$html .= '<input type="hidden" name="dummy" value="&#65533;" />';
				$html .= '<div class="send"><input name="purchase" type="submit" id="purchase_button" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"' . apply_filters('usces_filter_confirm_nextbutton', ' onClick="document.charset=\'Shift_JIS\';"') . $purchase_disabled . ' /></div>';
				break;

			//コンビニ決済(ルミーズ)
			case 'acting_remise_conv':
				if( function_exists('mb_strlen') ){
					$biko = ( 22 < mb_strlen($usces_entries['order']['note'])) ? (mb_substr($usces_entries['order']['note'], 0, 22).'...') : $usces_entries['order']['note'];
				}else{
					$biko = ( 44 < mb_strlen($usces_entries['order']['note'])) ? (substr($usces_entries['order']['note'], 0, 44).'...') : $usces_entries['order']['note'];
				}
				$datestr = get_date_from_gmt(gmdate('Y-m-d H:i:s', time()));
				$acting_opts = $usces->options['acting_settings']['remise'];
				$usces->save_order_acting_data($rand);
				$send_url = ('public' == $acting_opts['conv_pc_ope']) ? $acting_opts['send_url_cvs_mbl'] : $acting_opts['send_url_cvs_mbl_test'];
				$html = '<form id="purchase_form" name="purchase_form" action="' . $send_url . '" method="post" onKeyDown="if (event.keyCode == 13) {return false;}" accept-charset="Shift_JIS">
					<input type="hidden" name="SHOPCO" value="' . esc_attr($acting_opts['SHOPCO']) . '" />
					<input type="hidden" name="HOSTID" value="' . esc_attr($acting_opts['HOSTID']) . '" />
					<input type="hidden" name="REMARKS3" value="' . $acting_opts['REMARKS3'] . '" />
					<input type="hidden" name="S_TORIHIKI_NO" value="' . $rand . '" />
					<input type="hidden" name="NAME1" value="' . esc_attr($usces_entries['customer']['name1']) . '" />
					<input type="hidden" name="NAME2" value="' . esc_attr($usces_entries['customer']['name2']) . '" />
					<input type="hidden" name="KANA1" value="' . esc_attr($usces_entries['customer']['name3']) . '" />
					<input type="hidden" name="KANA2" value="' . esc_attr($usces_entries['customer']['name4']) . '" />
					<input type="hidden" name="YUBIN1" value="' . esc_attr(substr(str_replace('-', '', $usces_entries['customer']['zipcode']), 0, 3)) . '" />
					<input type="hidden" name="YUBIN2" value="' . esc_attr(substr(str_replace('-', '', $usces_entries['customer']['zipcode']), 3, 4)) . '" />
					<input type="hidden" name="ADD1" value="' . esc_attr($usces_entries['customer']['pref'] . $usces_entries['customer']['address1']) . '" />
					<input type="hidden" name="ADD2" value="' . esc_attr($usces_entries['customer']['address2']) . '" />
					<input type="hidden" name="ADD3" value="' . esc_attr($usces_entries['customer']['address3']) . '" />
					<input type="hidden" name="TEL" value="' . esc_attr(str_replace('-', '', mb_convert_kana($usces_entries['customer']['tel'], 'a', 'UTF-8'))) . '" />
					<input type="hidden" name="MAIL" value="' . esc_attr($usces_entries['customer']['mailaddress1']) . '" />
					<input type="hidden" name="TOTAL" value="' . $usces_entries['order']['total_full_price'] . '" />
					<input type="hidden" name="TAX" value="" />
					<input type="hidden" name="S_PAYDATE" value="' . date('Ymd', mktime(0,0,0,substr($datestr, 5, 2),substr($datestr, 8, 2)+$acting_opts['S_PAYDATE'],substr($datestr, 0, 4))) . '" />
					<input type="hidden" name="SEIYAKUDATE" value="' . date('Ymd', mktime(0,0,0,substr($datestr, 5, 2),substr($datestr, 8, 2),substr($datestr, 0, 4))) . '" />
					<input type="hidden" name="BIKO" value="' . esc_html($biko) . '" />
					';
				$mname_01 = '商品総額';
				$html .= '<input type="hidden" name="MNAME_01" value="' . $mname_01 . '" />
					<input type="hidden" name="MSUM_01" value="' . usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false) . '" />
					<input type="hidden" name="MNAME_02" value="" />
					<input type="hidden" name="MSUM_02" value="0" />
					<input type="hidden" name="MNAME_03" value="" />
					<input type="hidden" name="MSUM_03" value="0" />
					<input type="hidden" name="MNAME_04" value="" />
					<input type="hidden" name="MSUM_04" value="0" />
					<input type="hidden" name="MNAME_05" value="" />
					<input type="hidden" name="MSUM_05" value="0" />
					<input type="hidden" name="MNAME_06" value="" />
					<input type="hidden" name="MSUM_06" value="0" />
					<input type="hidden" name="MNAME_07" value="" />
					<input type="hidden" name="MSUM_07" value="0" />
					';
				$html .= '<input type="hidden" name="TMPURL" value="' . USCES_CART_URL . '&acting=remise_conv&acting_return=1&device_div='.$wcmb['device_div'].'&device_name='.$wcmb['device_name'].'&browser='.$wcmb['browser'].'" />
					<input type="hidden" name="OPT" value="1" />
					<input type="hidden" name="EXITURL" value="' . USCES_CART_URL . '&confirm=1&device_div='.$wcmb['device_div'].'&device_name='.$wcmb['device_name'].'&browser='.$wcmb['browser'].'" />
					';
				$html .= '<input type="hidden" name="dummy" value="&#65533;" />';
				$html .= '<div class="send"><input name="purchase" type="submit" id="purchase_button" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"' . apply_filters('usces_filter_confirm_nextbutton', ' onClick="document.charset=\'Shift_JIS\';"') . $purchase_disabled . ' /></div>';
				break;

			//クレジット決済(J-Payment)
			case 'acting_jpayment_card':
				if( DOCOMO === $wcmb['device_div'] || SOFTBANK === $wcmb['device_div'] || KDDI === $wcmb['device_div'] ) {
					$send_url = "https://credit.j-payment.co.jp/igateway/payform.aspx";
				} else {
					$send_url = $acting_opts['send_url'];
				}
				$acting_opts = $usces->options['acting_settings']['jpayment'];
				$usces->save_order_acting_data($rand);
				$am = $usces_entries['order']['total_items_price'];
				if( !empty($usces_entries['order']['cod_fee']) ) $am += $usces_entries['order']['cod_fee'];
				if( usces_is_member_system() && usces_is_member_system_point() && !empty($usces_entries['order']['usedpoint']) ) $am -= $usces_entries['order']['usedpoint'];
				if( !empty($usces_entries['order']['discount']) ) $am -= $usces_entries['order']['discount'];
//20120823ysk start 0000547
				$itemName = $usces->getItemName($cart[0]['post_id']);
				if(1 < count($cart)) $itemName .= ','.__('Others', 'usces');
				if(50 < mb_strlen($itemName)) $itemName = mb_substr($itemName, 0, 50).'...';
				$quantity = 0;
				foreach($cart as $cart_row) {
					$quantity += $cart_row['quantity'];
				}
				$desc = $itemName.' '.__('Quantity','usces').':'.$quantity;
//20120823ysk end
				$html = '<form name="purchase_form" action="'.$send_url.'" method="get" onKeyDown="if(event.keyCode == 13) {return false;}" >
					<input type="hidden" name="aid" value="'.$acting_opts['aid'].'" />
					<input type="hidden" name="cod" value="'.$rand.'" />
					<input type="hidden" name="jb" value="'.$acting_opts['card_jb'].'" />
					<input type="hidden" name="am" value="'.usces_crform($am, false, false, 'return', false).'" />
					<input type="hidden" name="tx" value="'.usces_crform($usces_entries['order']['tax'], false, false, 'return', false).'" />
					<input type="hidden" name="sf" value="'.usces_crform($usces_entries['order']['shipping_charge'], false, false, 'return', false).'" />
					<input type="hidden" name="pt" value="1" />
					<input type="hidden" name="inm" value="'.$desc.'" />
					<input type="hidden" name="pn" value="'.esc_attr(str_replace('-', '', mb_convert_kana($usces_entries['customer']['tel'], 'a', 'UTF-8'))).'" />
					<input type="hidden" name="em" value="'.esc_attr($usces_entries['customer']['mailaddress1']).'" />
					<input type="hidden" name="cmd" value="0" />
					<input type="hidden" name="acting" value="jpayment_card" />
					<input type="hidden" name="acting_return" value="1" />
					<input type="hidden" name="uscesid" value="'.$usces->get_uscesid().'" />
					<input type="hidden" name="page_id" value="'.USCES_CART_NUMBER.'" />
					';
				$html .= '<div class="send"><input name="purchase_jpayment" type="submit" class="checkout_button" value="'.__('Checkout', 'usces').'"' . $purchase_disabled . ' /></div>';
				break;

			//コンビニ・ペーパーレス決済(J-Payment)
			case 'acting_jpayment_conv':
				if( DOCOMO === $wcmb['device_div'] || SOFTBANK === $wcmb['device_div'] || KDDI === $wcmb['device_div'] ) {
					$send_url = "https://credit.j-payment.co.jp/igateway/cvs2.aspx";
				} else {
					$send_url = $acting_opts['send_url'];
				}
				$acting_opts = $usces->options['acting_settings']['jpayment'];
				$usces->save_order_acting_data($rand);
				$am = $usces_entries['order']['total_items_price'];
				if( !empty($usces_entries['order']['cod_fee']) ) $am += $usces_entries['order']['cod_fee'];
				if( usces_is_member_system() && usces_is_member_system_point() && !empty($usces_entries['order']['usedpoint']) ) $am -= $usces_entries['order']['usedpoint'];
				if( !empty($usces_entries['order']['discount']) ) $am -= $usces_entries['order']['discount'];
//20120823ysk start 0000547
				$itemName = $usces->getItemName($cart[0]['post_id']);
				if(1 < count($cart)) $itemName .= ','.__('Others', 'usces');
				if(50 < mb_strlen($itemName)) $itemName = mb_substr($itemName, 0, 50).'...';
				$quantity = 0;
				foreach($cart as $cart_row) {
					$quantity += $cart_row['quantity'];
				}
				$desc = $itemName.' '.__('Quantity','usces').':'.$quantity;
//20120823ysk end
				$html = '<form name="purchase_form" action="'.$send_url.'" method="get" onKeyDown="if(event.keyCode == 13) {return false;}" >
					<input type="hidden" name="aid" value="'.$acting_opts['aid'].'" />
					<input type="hidden" name="cod" value="'.$rand.'" />
					<input type="hidden" name="jb" value="CAPTURE" />
					<input type="hidden" name="am" value="'.usces_crform($am, false, false, 'return', false).'" />
					<input type="hidden" name="tx" value="'.usces_crform($usces_entries['order']['tax'], false, false, 'return', false).'" />
					<input type="hidden" name="sf" value="'.usces_crform($usces_entries['order']['shipping_charge'], false, false, 'return', false).'" />
					<input type="hidden" name="pt" value="2" />
					<input type="hidden" name="inm" value="'.$desc.'" />
					<input type="hidden" name="pn" value="'.esc_attr(str_replace('-', '', mb_convert_kana($usces_entries['customer']['tel'], 'a', 'UTF-8'))).'" />
					<input type="hidden" name="em" value="'.esc_attr($usces_entries['customer']['mailaddress1']).'" />
					<input type="hidden" name="cmd" value="0" />
					<input type="hidden" name="acting" value="jpayment_conv" />
					<input type="hidden" name="acting_return" value="1" />
					<input type="hidden" name="uscesid" value="'.$usces->get_uscesid().'" />
					<input type="hidden" name="page_id" value="'.USCES_CART_NUMBER.'" />
					';
				$html .= '<div class="send"><input name="purchase_jpayment" type="submit" class="checkout_button" value="'.__('Checkout', 'usces').'"' . $purchase_disabled . ' /></div>';
				break;

			//ソフトバンク・ペイメント
			case 'acting_sbps_card':
			case 'acting_sbps_conv':
			case 'acting_sbps_payeasy':
			case 'acting_sbps_wallet':
			case 'acting_sbps_mobile':
				$charging_type = $usces->getItemChargingType($cart[0]['post_id']);
				$frequency = $usces->getItemFrequency($cart[0]['post_id']);
				$chargingday = $usces->getItemChargingDay($cart[0]['post_id']);
				$acting_opts = $usces->options['acting_settings']['sbps'];
				$usces->save_order_acting_data($rand);
				$member = $usces->get_member();
				$cust_code = ( empty($member['ID']) ) ? str_replace('-', '', mb_convert_kana($usces_entries['customer']['tel'], 'a', 'UTF-8')) : $member['ID'];
				if( 'public' == $acting_opts['ope'] ) {
					$send_url = $acting_opts['send_url'];
				} elseif( 'test' == $acting_opts['ope'] ) {
					$send_url = $acting_opts['send_url_test'];
				} else {
					$send_url = $acting_opts['send_url_check'];
				}
				$sbps_cust_no = '';
				$sbps_payment_no = '';
				switch( $acting_flag ) {
				case 'acting_sbps_card':
					//if( 'on' == $acting_opts['cust'] ) {
					//	$sbps_cust_no = $usces->get_member_meta_value( 'sbps_cust_no', $member['ID'] );
					//	$sbps_payment_no = $usces->get_member_meta_value( 'sbps_payment_no', $member['ID'] );
					//}
					$pay_method = ( 'on' == $acting_opts['3d_secure'] ) ? "credit3d" : "credit";
					$acting = "sbps_card";
					$free_csv = "";
					break;
				case 'acting_sbps_conv':
					$pay_method = "webcvs";
					$acting = "sbps_conv";
					$free_csv = usces_set_free_csv( $usces_entries['customer'] );
					break;
				case 'acting_sbps_payeasy':
					$pay_method = "payeasy";
					$acting = "sbps_payeasy";
					$free_csv = usces_set_free_csv( $usces_entries['customer'] );
					break;
				case 'acting_sbps_wallet':
					$pay_method = "";
					if( 'on' == $acting_opts['wallet_yahoowallet'] ) $pay_method .= ",yahoowallet";
					if( 'on' == $acting_opts['wallet_rakuten'] ) $pay_method .= ",rakuten";
					if( 'on' == $acting_opts['wallet_paypal'] ) $pay_method .= ",paypal";
					if( 'on' == $acting_opts['wallet_netmile'] ) $pay_method .= ",netmile";
					if( 'on' == $acting_opts['wallet_alipay'] ) $pay_method .= ",alipay";
					$pay_method = ltrim( $pay_method, "," );
					$acting = "sbps_wallet";
					$free_csv = "";
					break;
				case 'acting_sbps_mobile':
					$pay_method = "";
					if( 'on' == $acting_opts['mobile_docomo'] ) $pay_method .= ",docomo";
					if( 'on' == $acting_opts['mobile_softbank'] ) $pay_method .= ",softbank";
					if( 'on' == $acting_opts['mobile_auone'] ) $pay_method .= ",auone";
					if( 'on' == $acting_opts['mobile_mysoftbank'] ) $pay_method .= ",mysoftbank";
					$pay_method = ltrim( $pay_method, "," );
					$acting = "sbps_mobile";
					$free_csv = "";
					break;
				}
				$item_id = $cart[0]['post_id'];
				$item_name = $usces->getItemName($cart[0]['post_id']);
				if(1 < count($cart)) $item_name .= ','.__('Others', 'usces');
				if(40 < mb_strlen($item_name)) $item_name = mb_substr($item_name, 0, 40).'...';
				$amount = usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false);
				$pay_type = "0";
				$auto_charge_type = "";
				$service_type = "0";
				$div_settle = "";
				$last_charge_month = "";
				$camp_type = "";
				$terminal_type = "1";//mobile
				$delim = ( false !== strpos(USCES_CART_URL, '?')) ? '&' : '?';
				$success_url = USCES_CART_URL.$delim."acting=".$acting."&acting_return=1";
				$cancel_url = USCES_CART_URL.$delim."acting=".$acting."&acting_return=1&cancel=1";
				$error_url = USCES_CART_URL.$delim."acting=".$acting."&acting_return=0";
				$pagecon_url = USCES_CART_URL;
				$free1 = $acting_flag;
				$request_date = date('YmdHis', current_time('timestamp'));
				$limit_second = "600";
				$sps_hashcode = $pay_method.$acting_opts['merchant_id'].$acting_opts['service_id'].$cust_code.$sbps_cust_no.$sbps_payment_no.$rand.$item_id.$item_name.$amount.$pay_type.$auto_charge_type.$service_type.$div_settle.$last_charge_month.$camp_type.$terminal_type.$success_url.$cancel_url.$error_url.$pagecon_url.$free1.$free_csv.$request_date.$limit_second.$acting_opts['hash_key'];
				$sps_hashcode = sha1( $sps_hashcode );
				$html = '<form id="purchase_form" name="purchase_form" action="'.$send_url.'" method="post" onKeyDown="if (event.keyCode == 13) {return false;}" accept-charset="Shift_JIS">
					<input type="hidden" name="pay_method" value="'.$pay_method.'" />
					<input type="hidden" name="merchant_id" value="'.$acting_opts['merchant_id'].'" />
					<input type="hidden" name="service_id" value="'.$acting_opts['service_id'].'" />
					<input type="hidden" name="cust_code" value="'.$cust_code.'" />
					<input type="hidden" name="sps_cust_no" value="'.$sbps_cust_no.'" />
					<input type="hidden" name="sps_payment_no" value="'.$sbps_payment_no.'" />
					<input type="hidden" name="order_id" value="'.$rand.'" />
					<input type="hidden" name="item_id" value="'.$item_id.'" />
					<input type="hidden" name="pay_item_id" value="" />
					<input type="hidden" name="item_name" value="'.$item_name.'" />
					<input type="hidden" name="tax" value="" />
					<input type="hidden" name="amount" value="'.$amount.'" />
					<input type="hidden" name="pay_type" value="'.$pay_type.'" />
					<input type="hidden" name="auto_charge_type" value="'.$auto_charge_type.'" />
					<input type="hidden" name="service_type" value="'.$service_type.'" />
					<input type="hidden" name="div_settle" value="'.$div_settle.'" />
					<input type="hidden" name="last_charge_month" value="'.$last_charge_month.'" />
					<input type="hidden" name="camp_type" value="'.$camp_type.'" />
					<input type="hidden" name="terminal_type" value="'.$terminal_type.'" />
					<input type="hidden" name="success_url" value="'.$success_url.'" />
					<input type="hidden" name="cancel_url" value="'.$cancel_url.'" />
					<input type="hidden" name="error_url" value="'.$error_url.'" />
					<input type="hidden" name="pagecon_url" value="'.$pagecon_url.'" />
					<input type="hidden" name="free1" value="'.$free1.'" />
					<input type="hidden" name="free2" value="" />
					<input type="hidden" name="free3" value="" />
					<input type="hidden" name="free_csv" value="'.$free_csv.'" />
					<input type="hidden" name="request_date" value="'.$request_date.'" />
					<input type="hidden" name="limit_second" value="'.$limit_second.'" />
					<input type="hidden" name="sps_hashcode" value="'.$sps_hashcode.'" />
					';
				$html .= '<input type="hidden" name="dummy" value="&#65533;" />';
				$html .= '<div class="send"><input name="purchase" type="submit" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"' . apply_filters('usces_filter_confirm_nextbutton', ' onClick="document.charset=\'Shift_JIS\';"') . $purchase_disabled . ' /></div>';
				break;

			//テレコムクレジット
			case 'acting_telecom_card':
				$acting_opts = $usces->options['acting_settings']['telecom'];
				$member = $usces->get_member();
				$memid = empty($member['ID']) ? 99999999 : $member['ID'];
				$send_url = $acting_opts['send_url'];
				$tel = str_replace('-', '', $usces_entries['customer']['tel']);
				$redirect_url = USCES_CART_URL.$usces->delim.'acting=telecom_card&acting_return=1&result=1';
				$redirect_back_url = USCES_CART_URL.$usces->delim.'confirm=1';
				$html = '<form id="purchase_form" action="'.$send_url.'" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
					<input type="hidden" name="clientip" value="'.$acting_opts['clientip'].'">
					<input type="hidden" name="money" value="'.apply_filters( 'usces_filter_acting_amount', usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false), $acting_flag ).'">
					<input type="hidden" name="sendid" value="'.$memid.'">
					<input type="hidden" name="usrtel" value="'.$tel.'">
					<input type="hidden" name="usrmail" value="'.esc_attr($usces_entries['customer']['mailaddress1']).'">
					<input type="hidden" name="redirect_url" value="'.$redirect_url.'">
					<input type="hidden" name="redirect_back_url" value="'.$redirect_back_url.'">
					<input type="hidden" name="i" value="on">
					<input type="hidden" name="option" value="'.$rand.'">
					';
				$html .= '<div class="send"><input name="purchase" type="submit" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"'.apply_filters('usces_filter_confirm_nextbutton', NULL).$purchase_disabled.' /></div>';
				break;
//20130225ysk start
			case 'acting_mizuho_card'://カード決済(みずほファクター)
				$acting_opts = $usces->options['acting_settings']['mizuho'];
				$send_url = ( 'public' == $acting_opts['ope'] ) ? $acting_opts['send_url_mbl'] : $acting_opts['send_url_mbl_test'];
				$p_ver = '0200';
				$stdate = date( 'Ymd' );
				$stran = sprintf( '%06d', mt_rand(1, 999999) );
				$bkcode = 'bg01';
				$amount = apply_filters( 'usces_filter_acting_amount', usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false), $acting_flag );
				$schksum = $p_ver.$stdate.$stran.$bkcode.$acting_opts['shopid'].$acting_opts['cshopid'].$amount.$acting_opts['hash_pass'];
				$schksum = htmlspecialchars( md5( $schksum ) );
				$html = '<form id="purchase_form" action="'.$send_url.'" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
					<input type="hidden" name="p_ver" value="'.$p_ver.'">
					<input type="hidden" name="stdate" value="'.$stdate.'">
					<input type="hidden" name="stran" value="'.$stran.'">
					<input type="hidden" name="bkcode" value="'.$bkcode.'">
					<input type="hidden" name="shopid" value="'.$acting_opts['shopid'].'">
					<input type="hidden" name="cshopid" value="'.$acting_opts['cshopid'].'">
					<input type="hidden" name="amount" value="'.$amount.'">
					<input type="hidden" name="schksum" value="'.$schksum.'">
					';
				$html .= '<input type="hidden" name="dummy" value="&#65533;" />';
				$html .= '<div class="send"><input name="purchase" type="submit" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"'.apply_filters('usces_filter_confirm_nextbutton', ' onClick="document.charset=\'Shift_JIS\';"').$purchase_disabled.' /></div>';
				break;
			case 'acting_mizuho_conv1'://コンビニ・ウェルネット決済(みずほファクター)
			case 'acting_mizuho_conv2'://コンビニ・セブンイレブン決済(みずほファクター)
				$acting_opts = $usces->options['acting_settings']['mizuho'];
				$send_url = ( 'public' == $acting_opts['ope'] ) ? $acting_opts['send_url_mbl'] : $acting_opts['send_url_mbl_test'];
				$p_ver = '0200';
				$stdate = date( 'Ymd' );
				$stran = sprintf( '%06d', mt_rand(1, 999999) );
				$bkcode = 'cv0'.substr( $acting_flag, -1 );
				$amount = apply_filters( 'usces_filter_acting_amount', usces_crform($usces_entries['order']['total_full_price'], false, false, 'return', false), $acting_flag );
				$custmKanji = mb_strimwidth( $usces_entries['customer']['name1'].$usces_entries['customer']['name2'], 0, 40 );
				$mailaddr = esc_attr( $usces_entries['customer']['mailaddress1'] );
				$tel = str_replace( '-', '', $usces_entries['customer']['tel'] );
				$schksum = $p_ver.$stdate.$stran.$bkcode.$acting_opts['shopid'].$acting_opts['cshopid'].$amount.mb_convert_encoding($custmKanji, 'SJIS', 'UTF-8').$mailaddr.$tel.$acting_opts['hash_pass'];
				$schksum = htmlspecialchars( md5( $schksum ) );
				$html = '<form id="purchase_form" action="'.$send_url.'" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
					<input type="hidden" name="p_ver" value="'.$p_ver.'">
					<input type="hidden" name="stdate" value="'.$stdate.'">
					<input type="hidden" name="stran" value="'.$stran.'">
					<input type="hidden" name="bkcode" value="'.$bkcode.'">
					<input type="hidden" name="shopid" value="'.$acting_opts['shopid'].'">
					<input type="hidden" name="cshopid" value="'.$acting_opts['cshopid'].'">
					<input type="hidden" name="amount" value="'.$amount.'">
					<input type="hidden" name="custmKanji" value="'.$custmKanji.'">
					<input type="hidden" name="mailaddr" value="'.$mailaddr.'">
					<input type="hidden" name="tel" value="'.$tel.'">
					<input type="hidden" name="schksum" value="'.$schksum.'">
					';
				$html .= '<input type="hidden" name="dummy" value="&#65533;" />';
				$html .= '<div class="send"><input name="purchase" type="submit" id="purchase_button" class="checkout_button" value="'.__('Checkout', 'usces').'"'.apply_filters('usces_filter_confirm_nextbutton', ' onClick="document.charset=\'Shift_JIS\';"').$purchase_disabled.' /></div>';
				break;
//20130225ysk end

			default:
		}
	}

	return $html;
}

function wcmb_fiter_the_payment_method() {
	global $wcmb;
	$args = func_get_args();
	$payments = $args[0];
	$mb_payments = array();

	foreach( $payments as $payment ){
		switch( $payment['settlement'] ) {
		case 'acting'://決済モジュール(イプシロン、PayPalスタンダード)
		case 'acting_jpayment_bank'://J-Paymentバンクチェック
		case 'acting_digitalcheck_card'://デジタルチェック(カード決済)
		case 'acting_digitalcheck_conv'://デジタルチェック(コンビニ決済)
			break;
		case 'acting_paypal_ec'://PayPalエクスプレスチェックアウト
			if( SMARTPHONE === $wcmb['device_div'] or PC === $wcmb['device_div'] ) {
				$mb_payments[] = $payment;
			}
			break;
		default:
			$mb_payments[] = $payment;
		}
	}
	ksort($mb_payments);
	return $mb_payments;
}

function wcmb_filter_mail_line() {
	$args = func_get_args();
	$line = $args[0];
	$type = $args[1];
	$mailaddress = $args[2];

	$mobile = false;
	if( !empty($mailaddress) ) {
		list($user, $domain) = explode("@", $mailaddress);
		if( 'docomo.ne.jp' == $domain || 
			'softbank.ne.jp' == $domain || 
			'ezweb.ne.jp' == $domain ) {
			$mobile = true;
		}
	}

	if( $mobile ) {
		switch( $type ) {
		case 1:
			$line = "====================";
			break;
		case 2:
			$line = "--------------------";
			break;
		case 3:
			$line = "====================";
			break;
		}
	}

	return $line;
}

function wcmb_action_essential_mark(){
	global $usces_essential_mark;
	$usces_essential_mark = str_replace('<em>', '<span class="em">', $usces_essential_mark);
	$usces_essential_mark = str_replace('</em>', '</span>', $usces_essential_mark);
}

function wcmb_filter_tax_guid(){
	$args = func_get_args();
	$str = $args[0];
	$str = str_replace('<em class="tax">', '<span class="em">', $str);
	$str = str_replace('</em>', '</span>', $str);
	return $str;
}

function wcmb_pre_reg_orderdata(){
	global $usces;
	$usces_entries = $usces->cart->get_entry();
	$delivery_time = urldecode($usces_entries['order']['delivery_time']);
	$_SESSION['usces_entry']['order']['delivery_time'] = $delivery_time;
}

function wcmb_filter_delim() {
	return "&";
}

function wcmb_filter_incart_check() {
	global $usces;
	$args = func_get_args();
	$mes = array();
	$post_id = $args[1];
	$sku = $args[2];

	$quant = isset($_POST['quant'][$post_id][$sku]) ? (int)$_POST['quant'][$post_id][$sku] : 1;
	$stock = $usces->getItemZaikoNum($post_id, $sku);
	$zaiko_id = (int)$usces->getItemZaikoStatusId($post_id, $sku);
	$itemRestriction = get_post_meta($post_id, '_itemRestriction', true);

	if( 1 > $quant ) {
		$mes[$post_id][$sku] = __('enter the correct amount', 'usces') . "<br />";
	} else if( $quant > (int)$itemRestriction && '' != $itemRestriction && '0' != $itemRestriction ) {
		$mes[$post_id][$sku] = sprintf(__("This article is limited by %d at a time.", 'usces'), $itemRestriction) . "<br />";
	} else if( $quant > (int)$stock && '' != $stock ) {
		$mes[$post_id][$sku] = __('Sorry, stock is insufficient.', 'usces') . ' ' . __('Current stock', 'usces') . $stock . "<br />";
	} else if( 1 < $zaiko_id ) {
		$mes[$post_id][$sku] = __('Sorry, this item is sold out.', 'usces') . "<br />";
	}

	$ioptkeys = $usces->get_itemOptionKey( $post_id, true );
	if( $ioptkeys ) {
		foreach( $ioptkeys as $key => $value ) {
			$decval = urldecode($value);
			$optValues = $usces->get_itemOptions( $decval, $post_id );
			if( 0 == $optValues['means'] ) { //case of select
				if( $optValues['essential'] && 
					(( isset($_POST['itemOption'][$post_id][$sku][$value]) && '#NONE#' == $_POST['itemOption'][$post_id][$sku][$value]) && 
					 ( isset($_POST['itemOption'][$post_id][$sku][$decval]) && '#NONE#' == $_POST['itemOption'][$post_id][$sku][$decval] )) ) {
					$mes[$post_id][$sku] .= sprintf(__("Chose the %s", 'usces'), $decval) . "<br />";
				}
			} elseif( 1 == $optValues['means'] ) { //case of multiselect
				if( $optValues['essential'] ) {
					$mselect = 0;
					foreach( (array)$_POST['itemOption'][$post_id][$sku][$value] as $mvalue ) {
						if( !empty($mvalue) and '#NONE#' != $mvalue ) $mselect++;
					}
					foreach( (array)$_POST['itemOption'][$post_id][$sku][$decval] as $mvalue ) {
						if( !empty($mvalue) and '#NONE#' != $mvalue ) $mselect++;
					}
					if( $mselect == 0 ) {
						$mes[$post_id][$sku] .= sprintf(__("Chose the %s", 'usces'), $decval) . "<br />";
					}
				}
			} else { //case of text
				if( $optValues['essential'] && 
					(( isset($_POST['itemOption'][$post_id][$sku][$value]) && '' == trim($_POST['itemOption'][$post_id][$sku][$value]) ) && 
					 ( isset($_POST['itemOption'][$post_id][$sku][$decval]) && '' == trim($_POST['itemOption'][$post_id][$sku][$decval]) )) ) {
					$mes[$post_id][$sku] .= sprintf(__("Input the %s", 'usces'), $decval) . "<br />";
				}
			}
		}
	}

	return $mes;
}

function wcmb_filter_newmember_button( $button ) {
	if( defined('WCEX_DLSELLER') ) {
		if( !dlseller_have_shipped() ) {
			$button = '<input name="regmemberdl" type="submit" value="' . __('transmit a message', 'usces') . '" />';
		}
	} elseif( defined('WCEX_AUTO_DELIVERY') ) {
		if( wcad_have_regular_order() ) {
			$button = '<input name="regmemberdl" type="submit" value="' . __('transmit a message', 'usces') . '" />';
		}
	}
	return $button;
}
?>
