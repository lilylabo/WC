<?php
/*
Plugin Name: WCEX DL Seller
Plugin URI: http://www.welcart.com/
Description: このプラグインはWelcart専用のダウンロード販売用拡張プラグインです。Welcart本体と一緒にご利用下さい。
Version: 2.1
Author: Collne Inc.
Author URI: http://www.welcart.com/
*/

if ( !defined('USCES_EX_PLUGIN') )
	define('USCES_EX_PLUGIN', 1);
	
define('WCEX_DLSELLER', true);
define('WCEX_DLSELLER_VERSION', "2.1.0.1302251");

if ( defined('USCES_VERSION') ):
	load_plugin_textdomain('dlseller', USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/languages', plugin_basename(dirname(__FILE__)).'/languages');
	if(version_compare(USCES_VERSION, '1.1-bata', '>=')) {
		require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/define_function11.php');
	} else {
		require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/define_function.php');
	}
	
	add_action('init', 'wcex_dlseller_init', 9);
	add_action('usces_action_shop_admin_menue', 'dlseller_add_shop_admin_menue');
	add_action('usces_action_management_admin_menue', 'dlseller_add_management_admin_menue');
	add_action('wp_ajax_dlseller_make_mail_ajax', 'dlseller_make_mail_ajax' );
	add_action('wp_ajax_dlseller_send_mail_ajax', 'dlseller_send_mail_ajax' );
	add_action('usces_main', 'dlseller_define_functions', 1);

	$dlseller_options = get_option('dlseller');
	if( !isset($dlseller_options['dlseller_restricting']) || empty($dlseller_options['dlseller_restricting']) )
		$dlseller_options['dlseller_restricting'] = 'on';
	update_option('dlseller', $dlseller_options);
endif;

function dlseller_add_shop_admin_menue(){
	add_submenu_page(USCES_PLUGIN_BASENAME, __('DLSeller Setting','dlseller'), __('DLSeller Setting','dlseller'), 'level_6', 'wcex_dlseller', 'dlseller_shop_admin_page');
}

function dlseller_add_management_admin_menue(){
	add_submenu_page('usces_orderlist', __('Continue Members','dlseller'), __('Continue Members','dlseller'), 'level_6', 'usces_continue', 'continue_member_list_page');
}

function wcex_dlseller_init(){
	global $usces;
	usces_register_action('09dlseller_card_update', 'request', 'dlseller_card_update', NULL, 'dlseller_card_update');
	usces_register_action('10dlseller_transition', 'request', 'dlseller_transition', NULL, 'wcex_dlseller_main');
	add_filter('usces_template_path_single_item', 'usces_dlseller_path_single_item');
	add_filter('usces_template_path_customer', 'usces_dlseller_path_customer');
	add_filter('usces_template_path_delivery', 'usces_dlseller_path_delivery');
	add_filter('usces_template_path_ordercompletion', 'usces_dlseller_path_ordercompletion');
	add_filter('usces_filter_inCart_quant', 'usces_filter_dlseller_incart_quant');
	add_filter('usces_filter_single_item_inform', 'dlseller_filter_single_item_inform');
	add_filter('usces_filter_get_item', 'dlseller_get_item', 10, 2);
	add_filter('usces_filter_member_check', 'dlseller_member_check', 11);
	add_filter('usces_filter_customer_check', 'dlseller_customer_check', 11);
	add_filter('usces_filter_delivery_check', 'dlseller_delivery_check', 11);
	add_filter('usces_filter_order_confirm_mail_first', 'dlseller_order_mail_first', 10, 2);
	add_filter('usces_filter_order_confirm_mail_shipping', 'dlseller_order_mail_shipping', 10, 2);
	add_filter('usces_filter_send_order_mail_first', 'dlseller_order_mail_first', 10, 2);
	add_filter('usces_filter_send_order_mail_shipping', 'dlseller_order_mail_shipping', 10, 2);
	add_filter('usces_filter_order_confirm_mail_meisai', 'dlseller_filter_order_mail_meisai', 10, 2);
	add_filter('usces_filter_send_order_mail_meisai', 'dlseller_filter_order_mail_meisai', 10, 2);
	add_filter('usces_filter_js_intoCart', 'dlseller_filter_js_intoCart', 10, 2);
	add_filter('usces_item_master_second_section', 'dlseller_item_master_second_section', 10, 2);
	add_filter('usces_filter_admin_modified_label', 'dlseller_filter_admin_modified_label', 10);
	add_filter('usces_filter_confirm_prebutton_value', 'dlseller_filter_confirm_prebutton_value', 10);
	add_filter('usces_filter_states_form_js', 'dlseller_filter_states_form_js', 10);
	add_filter('usces_filter_history_item_name', 'dlseller_filter_history_item_name', 10, 4);
	add_filter('usces_filter_member_history_header', 'dlseller_filter_member_history_header', 10, 2 );
	add_filter('usces_filter_memberinfo_page_header', 'dlseller_filter_memberinfo_page_header', 10, 2 );
	add_action('usces_action_memberinfo_page_header', 'dlseller_action_memberinfo_page_header', 10, 2 );
	add_filter('usces_filter_confirm_shipping_info', 'dlseller_filter_confirm_shipping_info', 10 );
	add_filter('usces_filter_shipping_address_info', 'dlseller_filter_confirm_shipping_info', 10 );
	add_filter('usces_filter_payment_detail', 'dlseller_filter_payment_detail', 10, 2 );
	add_filter('usces_filter_remise_card_job', 'dlseller_filter_remise_card_job', 10 );
	add_filter('usces_filter_remise_card_item', 'dlseller_filter_remise_card_item', 10 );
	add_filter('usces_fiter_the_payment_method', 'dlseller_fiter_the_payment_method');//20111107ysk 0000287

	add_action('save_post', 'dlseller_item_save_metadata');
	add_action('wp_print_styles', 'add_dlseller_stylesheet');
	add_action('wp_head', 'dlseller_shop_head');
	add_action('usces_action_member_logout', 'dlseller_action_member_logout');
	add_action('usces_action_reg_orderdata', 'dlseller_action_reg_orderdata', 10);
	add_action('usces_action_del_orderdata', 'dlseller_action_del_orderdata', 10);
	add_action('usces_action_update_orderdata', 'dlseller_action_update_orderdata', 10);
	add_filter('usces_filter_template_redirect', 'dlseller_filter_template_redirect', 2);
	add_action('usces_action_single_item_inform', 'dlseller_action_single_item_inform');
	add_action('usces_action_essential_mark', 'dlseller_action_essential_mark', 10, 2);
	add_action('usces_action_item_dupricate', 'dlseller_action_item_dupricate', 10, 2);

	if( is_admin() && ( isset($_REQUEST['page']) && 'usces_continue' == $_REQUEST['page'] ) ){
		wp_enqueue_script('jquery-ui-dialog');
	}
}

function dlseller_action_item_dupricate($post_id, $newpost_id){
	
	if( $item_division = get_post_meta($post_id, '_item_division', true) ){
		update_post_meta($newpost_id, '_item_division', $item_division);
	}
	if( $item_charging_type = get_post_meta($post_id, '_item_charging_type', true) ){
		update_post_meta($newpost_id, '_item_charging_type', $item_charging_type);
	}
	if( $item_frequency = get_post_meta($post_id, '_item_frequency', true) ){
		update_post_meta($newpost_id, '_item_frequency', $item_frequency);
	}
	if( $item_chargingday = get_post_meta($post_id, '_item_chargingday', true) ){
		update_post_meta($newpost_id, '_item_chargingday', $item_chargingday);
	}
	if( $dlseller_interval = get_post_meta($post_id, '_dlseller_interval', true) ){
		update_post_meta($newpost_id, '_dlseller_interval', $dlseller_interval);
	}
	if( $dlseller_validity = get_post_meta($post_id, '_dlseller_validity', true) ){
		update_post_meta($newpost_id, '_dlseller_validity', $dlseller_validity);
	}
	if( $dlseller_file = get_post_meta($post_id, '_dlseller_file', true) ){
		update_post_meta($newpost_id, '_dlseller_file', $dlseller_file);
	}
	if( $dlseller_date = get_post_meta($post_id, '_dlseller_date', true) ){
		update_post_meta($newpost_id, '_dlseller_date', $dlseller_date);
	}
	if( $dlseller_version = get_post_meta($post_id, '_dlseller_version', true) ){
		update_post_meta($newpost_id, '_dlseller_version', $dlseller_version);
	}
	if( $dlseller_author = get_post_meta($post_id, '_dlseller_author', true) ){
		update_post_meta($newpost_id, '_dlseller_author', $dlseller_author);
	}
	if( $dlseller_purchases = get_post_meta($post_id, '_dlseller_purchases', true) ){
		update_post_meta($newpost_id, '_dlseller_purchases', $dlseller_purchases);
	}
	if( $dlseller_downloads = get_post_meta($post_id, '_dlseller_downloads', true) ){
		update_post_meta($newpost_id, '_dlseller_downloads', $dlseller_downloads);
	}
}

function dlseller_action_essential_mark( $data=NULL, $field=NULL ) {
	global $usces_essential_mark;
	$type = ( is_array($data) && isset($data['type'])) ? $data['type'] : '';
	$dlseller_options = get_option('dlseller');
	if ( (isset($dlseller_options['dlseller_member_reinforcement']) && 'on' == $dlseller_options['dlseller_member_reinforcement']) || 'customer' == $type || 'delivery' == $type )
		return;
	
	$usces_essential_mark = array(
		'name1' => '<em>' . __('*', 'usces') . '</em>',
		'name2' => '',
		'name3' => '',
		'name4' => '',
		'zipcode' => '',
		'country' => '',
		'states' => '',
		'address1' => '',
		'address2' => '',
		'address3' => '',
		'tel' => '',
		'fax' => ''
		);
}

function dlseller_filter_remise_card_item( $item ) {
	if( dlseller_have_continue_charge() )
		$item = '0000990';
	return $item;
}

function dlseller_filter_remise_card_job( $job ) {
	if( dlseller_have_continue_charge() )
		$job = 'AUTH';
	return $job;
}

function dlseller_filter_payment_detail() {
	$args = func_get_args();
	$str = $args[0];
	$usces_entries = $args[1];
	
	if( dlseller_have_continue_charge() ) {
		$str = '　'.__('Recurring Subscription', 'dlseller');
	}
	return $str;
}

function dlseller_filter_confirm_shipping_info( $html ) {
	if( ! dlseller_have_shipped() ) {
		$html = '';
	}
	return $html;
}
//ダイアログに変更
function dlseller_filter_memberinfo_page_header($html) {
	global $usces;
	$html .= '
<script type="text/javascript">
	function pdfWindow( type, id ) {
		var wx = 800;
		var wy = 900;
		var x = (screen.width- wx) / 2;
		var y = (screen.height - wy) / 2;
		x = 0;
		y = 0;
		printWin = window.open("' . USCES_SSL_URL_ADMIN.'/wp-admin/admin.php?page=usces_orderlist&order_action=pdfout&order_id="+id+"&type="+type+"&uscesid='.$usces->get_uscesid().'","sub","left="+x+",top="+y+",width="+wx+",height="+wy+",scrollbars=yes");
	}
</script>'."\n";
	return $html;
}
function dlseller_action_memberinfo_page_header($html) {
	global $usces;
	$html .= '
<script type="text/javascript">
	function pdfWindow( type, id ) {
		var wx = 800;
		var wy = 900;
		var x = (screen.width- wx) / 2;
		var y = (screen.height - wy) / 2;
		x = 0;
		y = 0;
		printWin = window.open("' . USCES_SSL_URL_ADMIN.'/wp-admin/admin.php?page=usces_orderlist&order_action=pdfout&order_id="+id+"&type="+type+"&uscesid='.$usces->get_uscesid().'","sub","left="+x+",top="+y+",width="+wx+",height="+wy+",scrollbars=yes");
	}
</script>'."\n";
	echo $html;
}

function dlseller_filter_member_history_header() {
	$args = func_get_args();
	$data = $args[1];
		
	$html = '<tr><td class="retail" colspan="9">'."\n";
	$html .= '　' . '<a href="javascript:void(0)" class="bill_pdf_button" onclick="pdfWindow( \'bill\', \'' . $data['ID'] . '\' )">' . __('Invoice', 'usces') . ' PDF</a>';
	if( !preg_match('/noreceipt/', $data['order_status']) ){
		$html .= '　' . '<a href="javascript:void(0)" class="receipt_pdf_button" onclick="pdfWindow( \'receipt\', \'' . $data['ID'] . '\' )">' . __('Receipt', 'usces') . ' PDF</a>' . "\n";
	}else{
		$html .= '<span class="noreceipt">' . __('unpaid', 'usces') . '</span>';
	}
	$html .= '</td></tr>' . "\n";
	return $html;
}

function dlseller_filter_history_item_name(){
	global $usces;
	$args = func_get_args();
	$data = $args[1];
	$rows = $args[2];
	$index = $args[3];
	$dlitem = $usces->get_item( $rows['post_id'] );
	$division = dlseller_get_division( $rows['post_id'] );
	$member = $usces->get_member();
	$mid = (int)$member['ID'];
	$period = dlseller_get_validityperiod($mid, $rows['post_id']);
	$html = '';
	if( 'data' == $division ) {
		if( preg_match('/noreceipt/', $data['order_status']) ){
			$html .= '';
		}elseif(  empty($period['lastdates']) || $period['lastdates'] >= date('Y/m/d') ){
			$html .= '<div class="redownload_link"><a class="redownload_button" href="' . USCES_CART_URL . $usces->delim . 'dlseller_transition=download&rid=' . $index . '&oid=' . $data['ID'] . apply_filters('dlseller_filter_download_para', '', $rows['post_id'], $rows['sku']) . '">' . __('Download the latest version', 'dlseller') . (!empty($dlitem['dlseller_version']) ? '（v' . $dlitem['dlseller_version'] . '）' : '') . '</a></div>';
		}else{
			$html .= '<div class="limitover">' . __('Expired', 'dlseller') . '</div>';
		}
	}
	return $html;
}

function dlseller_filter_states_form_js( $js ) {
	global $usces;
	if( !dlseller_have_shipped() && ( (is_page(USCES_CART_NUMBER) || $usces->is_cart_page($_SERVER['REQUEST_URI'])) && ('customer' == $usces->page || 'delivery' == $usces->page) ) ) {
		$js = '';
	}
	return $js;
}

function dlseller_filter_template_redirect() {
	global $usces, $post, $usces_entries, $usces_carts, $usces_members, $usces_item, $usces_gp, $member_regmode;
	if( is_single() && 'item' == $post->post_mime_type ) {
		$division = dlseller_get_division( $post->ID );
		$usces_item = $usces->get_item( $post->ID );
		if( 'data' == $division ){
			if( file_exists(get_stylesheet_directory() . '/wc_templates/wc_item_single_data.php') ){
				include(get_stylesheet_directory() . '/wc_templates/wc_item_single_data.php');
				exit;
			}
		}elseif( 'service' == $division ){
			if( file_exists(get_stylesheet_directory() . '/wc_templates/wc_item_single_service.php') ){
				include(get_stylesheet_directory() . '/wc_templates/wc_item_single_service.php');
				exit;
			}
		}else{
			if( file_exists(get_stylesheet_directory() . '/wc_templates/wc_item_single.php') ){
				include(get_stylesheet_directory() . '/wc_templates/wc_item_single.php');
				exit;
			}
		}
		return true;
	}elseif( isset($_REQUEST['page']) && ('search_item' == $_REQUEST['page'] || 'usces_search' == $_REQUEST['page']) && $usces->is_cart_page($_SERVER['REQUEST_URI']) ){
		if( file_exists(get_stylesheet_directory() . '/wc_templates/wc_search_page.php') ){
			include(get_stylesheet_directory() . '/wc_templates/wc_search_page.php');
			exit;
		}
		
	}else if( $usces->is_cart_page($_SERVER['REQUEST_URI']) ){
		switch( $usces->page ){
			case 'customer':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_customer_page.php') ){
					usces_get_entries();
					usces_get_member_regmode();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_customer_page.php');
					exit;
				}
			case 'delivery':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_delivery_page.php') ){
					usces_get_entries();
					usces_get_carts(); 
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_delivery_page.php');
					exit;
				}
			case 'confirm':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_confirm_page.php') ){
					usces_get_entries();
					usces_get_carts();
					usces_get_members();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_confirm_page.php');
					exit;
				}
			case 'ordercompletion':
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_completion_page.php') ){
					usces_get_entries();
					usces_get_carts();
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_completion_page.php');
					exit;
				}
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
				if( file_exists(get_stylesheet_directory() . '/wc_templates/cart/wc_cart_page.php') ){
					include(get_stylesheet_directory() . '/wc_templates/cart/wc_cart_page.php');
					exit;
				}
		}
		return true;
	}else if($usces->is_inquiry_page($_SERVER['REQUEST_URI']) ){

	}else if( $usces->is_member_page($_SERVER['REQUEST_URI']) ){
	if($usces->options['membersystem_state'] != 'activate') return;
		
		if( $usces->is_member_logged_in() ) {
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
		return true;
	}
}

function dlseller_action_usces_main() {
	global $usces;
	if( !isset($_REQUEST['dlseller_transition']) && $usces->is_cart_page($_SERVER['REQUEST_URI'])){
		usces_dlseller_login_check();
	}
}

function dlseller_action_member_logout() {
	unset($_SESSION['usces_cart']);
}

function dlseller_shop_head() {
	if( file_exists(get_stylesheet_directory() . '/dlseller.css') ){
?>
		<link href="<?php echo get_stylesheet_directory_uri(); ?>/dlseller.css" rel="stylesheet" type="text/css" />
<?php
	}
}

function usces_dlseller_login_check() {
	global $usces;

	if( !$usces->is_member_logged_in() ){
		header('location: '.USCES_MEMBER_URL);
		exit;
	}
}

function dlseller_card_update() {
	global $usces;
	$dls_opts = get_option('dlseller');
	$action = $_REQUEST['dlseller_card_update'];
	switch($action){
		case 'login':
		
			if( $usces->is_member_logged_in() ){
				add_filter('usces_template_path_cart', 'usces_dlseller_path_upaction');
				add_filter('usces_filter_title_cart', 'usces_dlseller_title_upaction');
				$usces->page = 'cart';
				add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
				add_action('the_post', array($usces, 'action_cartFilter'));
			}else{

				$res = $usces->member_login();
				if( $res != 'login' ){
					add_filter('usces_template_path_cart', 'usces_dlseller_path_upaction');
					add_filter('usces_filter_title_cart', 'usces_dlseller_title_upaction');
					$usces->page = 'cart';
					add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
					add_action('the_post', array($usces, 'action_cartFilter'));
				}else{
					add_filter('usces_filter_login_page_header', 'usces_dlseller_path_uplogin_header');
					add_filter('usces_template_path_login', 'usces_dlseller_path_uplogin');
					$usces->page = 'login';
					add_filter('usces_filter_login_form_action', 'usces_dlseller_login_form_action');
					add_filter('usces_filter_login_button', 'usces_dlseller_login_button');
					add_filter('usces_filter_login_inform', 'usces_dlseller_login_inform_cardup');
	//				add_action('usces_action_login_page_inform', 'usces_dlseller_login_wc_inform_cardup');
					add_filter('usces_filter_memberTitle', 'usces_dlseller_title_login',1);
					add_action('the_post', array($usces, 'action_memberFilter'));
					add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_login');
				}
			}
			break;
	}
	return false;
}

function wcex_dlseller_main() {
	global $usces, $wp_query, $usces_item, $post;
	$dls_opts = get_option('dlseller');
	$action = $_REQUEST['dlseller_transition'];

	switch($action){

		case 'single_item':
		
			$ids = array_keys($_POST['inCart']);
			$post_id = $ids[0];
			$division = dlseller_get_division( $post_id );
			$charging_type = $usces->getItemChargingType( $post_id );
			if( 'continue' == $charging_type ){
				if( $usces->cart->num_row() !== false ) {
					$usces->cart->crear_cart();
				}
			}else{
				if( $usces->cart->num_row() !== false && dlseller_have_continue_charge() ) {
					$usces->cart->crear_cart();
				}
			}
			return true;
			break;
			
		case 'login':
			$res = $usces->member_login();
			if( $res != 'login' ){
				if( $usces->cart->num_row() !== false ){
					$member = $usces->get_member();
					$mid = (int)$member['ID'];
					$cart = $usces->cart->get_cart();
					$dlseller_cart = $cart;
					$cart_row = $cart[0];
					$post_id = $cart_row['post_id'];
					$sku = $cart_row['sku'];
					$usces_item = $usces->get_item( $post_id );
					$usces_item['sku'] = $sku;
					$period = dlseller_get_validityperiod($mid, $post_id);
					if( $usces->is_purchased_item($mid, $post_id) === true && ( empty($period['lastdate']) || $period['lastdate'] >= mysql2date(__('Y/m/d'),current_time('mysql', 0)) ) ){
						$usces->cart->crear_cart();
						add_filter('usces_template_path_cart', 'usces_dlseller_path_redownload');
						add_filter('usces_filter_title_cart', 'usces_dlseller_title_redownload',1);
					}
					$usces->page = 'cart';
					add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
					add_action('the_post', array($usces, 'action_cartFilter'));
				}else{
					$usces->page = 'member';
					add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_member');
					add_action('the_post', array($usces, 'action_memberFilter'));
				}
			}else{
				$usces->page = 'login';
				add_filter('usces_filter_login_form_action', 'usces_dlseller_login_form_action');
				add_filter('usces_filter_login_button', 'usces_dlseller_login_button');
				add_filter('usces_filter_login_inform', 'usces_dlseller_login_inform');
				add_action('usces_action_login_page_inform', 'usces_dlseller_login_wc_inform');
				add_filter('usces_filter_newmember_urlquery', 'usces_dlseller_newmember_urlquery');
				add_filter('usces_filter_memberTitle', 'usces_dlseller_title_login',1);
				add_action('the_post', array($usces, 'action_memberFilter'));
				add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_login');
			}
			break;
		case 'newmember':
			$usces->page = 'newmemberform';
			add_filter('usces_filter_newmember_form_action', 'usces_dlseller_newmember_form_action');
			add_filter('usces_filter_newmember_button', 'usces_dlseller_newmember_button');
			add_filter('usces_filter_newmember_inform', 'usces_dlseller_newmember_inform');
			add_action('usces_action_newmember_page_inform', 'usces_dlseller_newmember_wc_inform');
			add_action('the_post', array($usces, 'action_memberFilter'));
			add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_editmemberform');
			add_action('template_redirect', array($usces, 'template_redirect'));
			break;
		case 'regmember':
			$res = $usces->regist_member();
			if( 'newcompletion' == $res ){
				$email = stripslashes(trim($_POST['member']['mailaddress1']));
				$pass = stripslashes(trim($_POST['member']['password1']));
				$lires = $usces->member_just_login($email, $pass);
				if( $lires == 'login' ){
					wp_redirect(get_option('home'));
					exit;
				}
				wp_redirect(USCES_CART_URL.$usces->delim . 'customerinfo=1');
				exit;
//				$usces->page = 'delivery';
//				add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
//				add_action('the_post', array($usces, 'action_cartFilter'));
			}else{
				$usces->page = 'newmemberform';
				add_filter('usces_filter_newmember_form_action', 'usces_dlseller_newmember_form_action');
				add_filter('usces_filter_newmember_button', 'usces_dlseller_newmember_button');
				add_filter('usces_filter_newmember_inform', 'usces_dlseller_newmember_inform');
				add_action('usces_action_newmember_page_inform', 'usces_dlseller_newmember_wc_inform');
				add_action('the_post', array($usces, 'action_memberFilter'));
				add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_editmemberform');
				add_action('template_redirect', array($usces, 'template_redirect'));
			}
			break;
		case 'cart':
			return true;
			break;
		case 'confirm':
			return true;
			break;
		case 'download':
			dlseller_download();
			break;
		case 'results':
			dlseller_results_csv();
			break;
		case 'member_reference':
			if( $usces->is_member_logged_in() ){
				$usces->page = 'cart';
				$member = $usces->get_member();
				$mid = (int)$member['ID'];
				$post_id = (int)$_GET['post_id'];
				$sku = urldecode($_GET['sku']);
				$usces_item = $usces->get_item( $post_id );
				$usces_item['sku'] = $sku;
				$period = dlseller_get_validityperiod($mid, $post_id);
				if( $usces->is_purchased_item($mid, $post_id) === true && ( empty($period['lastdates']) || $period['lastdates'] >= date('Y/m/d') ) ){
					add_filter('usces_template_path_cart', 'usces_dlseller_path_redownload');
					if( 'service' == $usces_item['item_division'] ){
						add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_ordercompletion');
						add_filter('usces_filter_title_cart', 'usces_dlseller_title_information',1);
					}else{
						add_filter('yoast-ga-push-after-pageview', 'dlseller_trackPageview_redownload');
						add_filter('usces_filter_title_cart', 'usces_dlseller_title_redownload',1);
					}
				}
				add_action('the_post', array($usces, 'action_cartFilter'));
			}else{
				$usces->page = 'login';
				add_filter('usces_filter_login_form_action', 'usces_dlseller_login_form_action');
				add_filter('usces_filter_login_button', 'usces_dlseller_login_button');
				add_filter('usces_filter_login_inform', 'usces_dlseller_login_inform');
				add_action('usces_action_login_page_inform', 'usces_dlseller_login_wc_inform');
				add_filter('usces_filter_newmember_urlquery', 'usces_dlseller_newmember_urlquery');
				add_filter('usces_filter_memberTitle', 'usces_dlseller_title_login',1);
				add_action('the_post', array($usces, 'action_memberFilter'));
			}
			break;
	}
}

function usces_dlseller_path_uplogin_header(){
	$mes = '<h2>' . __('Credit card update processing', 'dlseller') . '</h2>
	<p>' . __('Login is necessary to update Credit card. Please work to update it according to the guidance of the next page if You can log in.', 'dlseller') . '</p>';
	return $mes;
}
function usces_dlseller_login_form_action( $url ){
	$url = USCES_CART_URL;
	return $url;
}

function usces_dlseller_newmember_form_action( $url ){
	$url = USCES_CART_URL;
	return $url;
}

function usces_dlseller_path_single_item( $path ){
	global $post;
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/single_item.php';
	return $path;
}

function usces_dlseller_path_customer( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/customer_info.php';
	return $path;
}

function usces_dlseller_path_delivery( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/delivery_info.php';
	return $path;
}

function usces_dlseller_path_confirm( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/confirm.php';
	return $path;
}

function usces_dlseller_path_ordercompletion( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/completion.php';
	return $path;
}

function usces_dlseller_path_redownload( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/redownload.php';
	return $path;
}

function usces_dlseller_path_member_form( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/member_form.php';
	return $path;
}

function usces_dlseller_path_member( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/member.php';
	return $path;
}

function usces_dlseller_path_uplogin( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/uplogin.php';
	return $path;
}

function usces_dlseller_path_upaction( $path ){
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/upaction.php';
	return $path;
}

function usces_dlseller_newmember_urlquery( $query ){
	return '&dlseller_transition=newmember';
}

function usces_dlseller_title_cart( $title ){
	return __('Checkout', 'dlseller');
}

function usces_dlseller_title_redownload( $title ){
	return __('Redownload', 'dlseller');
}
function usces_dlseller_title_information( $title ){
	return __('Information', 'dlseller');
}
function usces_dlseller_title_ordercompletion( $title ){
	return __('Download', 'dlseller');
}
function usces_dlseller_title_login( $title ){
	return __('Log-in for members', 'usces');
}

function usces_dlseller_title_upaction( $title ){
	return __('Credit card update processing', 'usces');
}

function dlseller_order_mail_first(){
	$args = func_get_args();
	$data = $args[1];
	$str = __('membership number', 'usces') . " : " . $data['mem_id'] . "\r\n";
	return $str;
}

function dlseller_order_mail_shipping($msg_shipping){
	if( dlseller_have_shipped() )
		return $msg_shipping;
	else
		return "";
}

function usces_filter_dlseller_incart_quant($quant){
	if( dlseller_have_continue_charge() )
		return 1;
	else
		return $quant;
}


function usces_filter_cart_upbutton($button){
	$addtag .= '';
	return $addtag;
}

function dlseller_filter_single_item_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="single_item" />';
	return $html;
}

function dlseller_action_single_item_inform(){
	echo '<input name="dlseller_transition" type="hidden" value="single_item" />';
}

function usces_dlseller_login_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="login" />';
	return $html;
}

function usces_dlseller_login_inform_cardup($html){
	$html .= '<input name="dlseller_card_update" type="hidden" value="login" />';
	$html .= '<input name="dlseller_order_id" type="hidden" value="' . (int)$_REQUEST['dlseller_order_id'] . '" />';
	$html .= '<input name="dlseller_up_mode" type="hidden" value="2" />';
	return $html;
}

function usces_dlseller_newmember_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="regmember" />';
	return $html;
}

function usces_dlseller_newmember_wc_inform(){
	$html = '<input name="dlseller_transition" type="hidden" value="regmember" />';
	echo $html;
}

function usces_dlseller_cart_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="cart" />';
	return $html;
}

function dlseller_customer_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="customer" />';
	return $html;
}

function usces_dlseller_confirm_inform($html){
	$html .= '<input name="dlseller_transition" type="hidden" value="confirm" />';
	return $html;
}

function usces_dlseller_login_wc_inform_cardup(){
	echo '<input name="dlseller_card_update" type="hidden" value="login" />';
}

function usces_dlseller_login_button($button){
	$button = '<input type="submit" name="dlSeller" id="member_login" value="' . __('Log-in', 'usces') . '" tabindex="100" />';
	return $button;
}

function usces_dlseller_newmember_button($button){
	$button = '<input name="regmemberdl" type="submit" value="' . __('transmit a message', 'usces') . '" />';
	return $button;
}

function dlseller_download() {
	global $usces;
	$member = $usces->get_member();
	$mid = (int)$member['ID'];
	if( !isset($_GET['pid']) ){
		$rid = (int)$_GET['rid'];
		$oid = (int)$_GET['oid'];
		$order = $usces->get_order_data($oid);
		if( $order === false )
			exit;
			
		$cart = $order['cart'];
		$post_id = $cart[$rid]['post_id'];
		$sku = $cart[$rid]['sku'];
	}else{
		$post_id = (int)$_GET['pid'];
		$sku = isset($_GET['sku']) ? $_GET['sku'] : NULL;
	}

	if( $usces->is_purchased_item($mid, $post_id, urldecode($sku)) === false ){
		echo __('An error occurred. Please refer to a manager.', 'dlseller') . '(error:nomember or nopurchase)<br /><br /><br />';
		echo '<a href="' . get_option('home') . '">' . __('Back to the top page.', 'usces') . '</a>';
		exit;
	}

//	$files = get_post_meta($post_id, '_dlseller_file', true);
	$filename = dlseller_get_filename( $post_id, $sku );
	$downloads = get_post_meta($post_id, '_dlseller_downloads', true);
	$dl = (int)$downloads;
	$dlseller_options = get_option('dlseller');
	$delseller_path = $dlseller_options['content_path'] . $filename;
	if( !file_exists($delseller_path) || !is_file($delseller_path) ){
		echo __('An error occurred. Please refer to a manager.', 'dlseller') . '(error:nofile)<br /><br /><br />';
		echo '<a href="' . get_option('home') . '">' . __('Back to the top page.', 'usces') . '</a>';
		exit;
	}

	$content_length = filesize($delseller_path);
	
	header("Pragma: ");
	header( "Content-Disposition: attachment; filename=" . $filename);
	header( "Content-Type: application/octet-stream" );
	header( "Content-Length: " . $content_length ) ;
	
	set_time_limit(0);
	ob_end_flush();
	flush();
	$fp = fopen($delseller_path, "r");
	while(!feof($fp)){
		sleep(1);
		print fread($fp, round($dlseller_options['dlseller_rate'] * 1024));
		ob_flush();
		flush();
	}
	fclose($fp);
	
	$dl++;
	usces_dlseller_update_dlcount($post_id, 0, 1 );
	exit;
}

function dlseller_item_master_second_section() {
	global $usces;
	$args = func_get_args();
	$html = $args[0];
	$post_ID = $args[1];

	$division = dlseller_get_division( $post_ID );

	switch( $division ){
		case 'shipped':
			$shipped_select = ' checked="checked"';
			$data_select = '';
			$serv_select = '';
			break;
		case 'data':
			$shipped_select = '';
			$data_select = ' checked="checked"';
			$serv_select = '';
			break;
		case 'service':
			$shipped_select = '';
			$data_select = '';
			$serv_select = ' checked="checked"';
			break;
		default:
			$shipped_select = '';
			$data_select = '';
			$serv_select = '';
	}
	$item_charging_type = get_post_meta($post_ID, '_item_charging_type', true);
	$item_frequency = get_post_meta($post_ID, '_item_frequency', true);
	$item_chargingday = get_post_meta($post_ID, '_item_chargingday', true);
	$dlseller_interval = get_post_meta($post_ID, '_dlseller_interval', true);
	$dlseller_validity = get_post_meta($post_ID, '_dlseller_validity', true);
	$dlseller_file = get_post_meta($post_ID, '_dlseller_file', true);
	$dlseller_date = get_post_meta($post_ID, '_dlseller_date', true);
	$dlseller_version = get_post_meta($post_ID, '_dlseller_version', true);
	$dlseller_author = get_post_meta($post_ID, '_dlseller_author', true);
	$dlseller_purchases = get_post_meta($post_ID, '_dlseller_purchases', true);
	$dlseller_downloads = get_post_meta($post_ID, '_dlseller_downloads', true);
	$dls_mon = usces_dlseller_get_dlcount($post_ID, 'month');
	$dls_tol = usces_dlseller_get_dlcount($post_ID, 'total');



	$addtag = '
	<script type="text/javascript">
	jQuery(function($) {
		$("#division_shipped").change(function() {
			$("tr.shipped").css("display","");
			$("tr.dl_service").css("display","none");
			$("tr.dl_content").css("display","none");
			$("tr.dl_data").css("display","none");
		});
		$("#division_data").change(function() {
			$("tr.shipped").css("display","none");
			$("tr.dl_service").css("display","none");
			$("tr.dl_content").css("display","");
			$("tr.dl_data").css("display","");
		});
		$("#division_service").change(function() {
			$("tr.shipped").css("display","none");
			$("tr.dl_service").css("display","");
			$("tr.dl_content").css("display","");
			$("tr.dl_data").css("display","none");
		});
		var dld = $("input[name=\'item_division\']:checked").val();
		if( "shipped" == dld ){
			$("tr.shipped").css("display","");
			$("tr.dl_service").css("display","none");
			$("tr.dl_content").css("display","none");
			$("tr.dl_data").css("display","none");
		}else if( "data" == dld ){
			$("tr.shipped").css("display","none");
			$("tr.dl_service").css("display","none");
			$("tr.dl_content").css("display","");
			$("tr.dl_data").css("display","");
		}else if( "service" == dld ){
			$("tr.shipped").css("display","none");
			$("tr.dl_service").css("display","");
			$("tr.dl_content").css("display","");
			$("tr.dl_data").css("display","none");
		}
		var dct = $("#item_charging_type").val();
		if( "0" == dct ){
			$("tr.dl_frequency").css("display","none");
			$("tr.dl_chargingday").css("display","none");
			$("tr.dl_interval").css("display","none");
		}else{
			$("tr.dl_frequency").css("display","");
			$("tr.dl_chargingday").css("display","");
			$("tr.dl_interval").css("display","");
		}
		$("#item_charging_type").change(function() {
			dct = $("#item_charging_type").val();
			if( "0" == dct ){
				$("tr.dl_frequency").css("display","none");
				$("tr.dl_chargingday").css("display","none");
				$("tr.dl_interval").css("display","none");
			}else{
				$("tr.dl_frequency").css("display","");
				$("tr.dl_chargingday").css("display","");
				$("tr.dl_interval").css("display","");
			}
		});
	});
	</script>
	<tr>
	<th>' .  __('Division', 'dlseller') . '</th>
	<td><label for="division_shipped" style="width: 120px;"><input name="item_division" id="division_shipped" type="radio" value="shipped"' . $shipped_select . ' />&nbsp;' . __('Shipped', 'dlseller') . '</label>&nbsp;&nbsp;<label for="division_data" style="width: 120px;"><input name="item_division" id="division_data" type="radio" value="data"' . $data_select . ' />&nbsp;' . __('Data file', 'dlseller') . '</label>&nbsp;&nbsp;<label for="division_service" style="width: 120px;"><input name="item_division" id="division_service" type="radio" value="service"' . $serv_select . ' />&nbsp;' . __('Service', 'dlseller') . '</label></td>
	</tr>
	<th>' .  __('Charging type','usces') . '</th>
	<td>
		<select id="item_charging_type" name="item_charging_type">
			<option value="0"' . ( 0 === (int)$item_charging_type ? ' selected="selected"' : '' ) . '>' . __('Normal Charging','dlseller') . '</option>
			<option value="1"' . ( 1 === (int)$item_charging_type ? ' selected="selected"' : '' ) . '>' . __('Recurring Subscription','dlseller') . '</option>
		</select>
	</td>
	</tr>
	<tr class="dl_frequency">
	<th>' .  __('Charging Interval', 'dlseller') . '</th>
	<td>
		<select id="item_frequency" name="item_frequency">
			<option value="1"' . ( 1 === (int)$item_frequency ? ' selected="selected"' : '' ) . '>' . __('毎月','dlseller') . '</option>
			<option value="6"' . ( 6 === (int)$item_frequency ? ' selected="selected"' : '' ) . '>' . __('半年毎','dlseller') . '</option>
			<option value="12"' . ( 12 === (int)$item_frequency ? ' selected="selected"' : '' ) . '>' . __('毎年','dlseller') . '</option>
		</select>
	</td>
	</tr>
	<tr class="dl_chargingday">
	<th>' .  __('Charging Date', 'dlseller') . '</th>
	<td>
		<select id="item_chargingday" name="item_chargingday">'."\n";
		for($i=1; $i<29; $i++){
			$addtag .= '		<option value="' . $i . '"' . ( $i === (int)$item_chargingday ? ' selected="selected"' : '' ) . '>' . str_pad($i, 2, " ", STR_PAD_LEFT) . '</option>' . "\n";
		}
	$addtag .= '	</select>
	</td>
	<tr class="dl_interval">
	<th>' .  __('Contract Period(Months)', 'dlseller') . '</th>
	<td><input type="text" name="dlseller_interval" id="dlseller_interval" class="itemCode" value="' . esc_attr($dlseller_interval) . '" /></td>
	</tr>
	</tr>
	<tr class="dl_data">
	<th>' .  __('Validity(days)', 'dlseller') . '</th>
	<td><input type="text" name="dlseller_validity" id="dlseller_validity" class="itemCode" value="' . esc_attr($dlseller_validity) . '" /></td>
	</tr>
	<tr class="dl_data">
	<th>' .  __('File Name', 'dlseller') . '</th>
	<td><input type="text" name="dlseller_file" id="dlseller_file" class="itemCode" value="' . esc_attr($dlseller_file) . '" /></td>
	</tr>
	<tr class="dl_data">
	<th>' .  __('Release Date', 'dlseller') . '</th>
	<td><input type="text" name="dlseller_date" id="dlseller_date" class="itemCode" value="' . esc_attr($dlseller_date) . '" /></td>
	</tr>
	<tr class="dl_data">
	<th>' .  __('Version', 'dlseller') . '</th>
	<td><input type="text" name="dlseller_version" id="dlseller_version" class="itemCode" value="' . esc_attr($dlseller_version) . '" /></td>
	</tr>
	<tr class="dl_data">
	<th>' .  __('Author', 'dlseller') . '</th>
	<td><input type="text" name="dlseller_author" id="dlseller_author" class="itemCode" value="' . esc_attr($dlseller_author) . '" /></td>
	</tr>
	<tr class="dl_data">
	<th>' .  __('Purchases', 'dlseller') . '</th>
	<td>' . $dls_mon['par'] . '(' . $dls_tol['par'] . ')</td>
	</tr>
	<tr class="dl_data">
	<th>' .  __('Downloads', 'dlseller') . '</th>
	<td>' . $dls_mon['dl'] . '(' . $dls_tol['dl'] . ')</td>
	</tr>
	';

	echo $addtag . $html;
}

function dlseller_item_save_metadata() {
	global $usces;
	
	$post_ID = isset($_POST['post_ID']) ? $_POST['post_ID'] : -1;
	if( $post_ID < 0 ) return $post_ID;

	if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
	  return $post_ID;
	} else {
	if ( !current_user_can( 'edit_post', $post_ID ))
	  return $post_ID;
	}

	if(isset($_POST['item_division'])){
		$item_division = $_POST['item_division'];
		update_post_meta($post_ID, '_item_division', $item_division);
	}
	if(isset($_POST['item_charging_type'])){
		$item_charging_type = (int)$_POST['item_charging_type'];
		update_post_meta($post_ID, '_item_charging_type', $item_charging_type);
	}
	if(isset($_POST['item_frequency'])){
		$item_frequency = (int)$_POST['item_frequency'];
		update_post_meta($post_ID, '_item_frequency', $item_frequency);
	}
	if(isset($_POST['item_chargingday'])){
		$item_chargingday = (int)$_POST['item_chargingday'];
		update_post_meta($post_ID, '_item_chargingday', $item_chargingday);
	}
	if(isset($_POST['dlseller_interval'])){
		$dlseller_interval = trim($_POST['dlseller_interval']);
		update_post_meta($post_ID, '_dlseller_interval', $dlseller_interval);
	}
	if(isset($_POST['dlseller_validity'])){
		$dlseller_validity = trim($_POST['dlseller_validity']);
		update_post_meta($post_ID, '_dlseller_validity', $dlseller_validity);
	}
	if(isset($_POST['dlseller_file'])){
		$dlseller_file = trim($_POST['dlseller_file']);
		update_post_meta($post_ID, '_dlseller_file', $dlseller_file);
	}
	if(isset($_POST['dlseller_date'])){
		$dlseller_date = trim($_POST['dlseller_date']);
		update_post_meta($post_ID, '_dlseller_date', $dlseller_date);
	}
	if(isset($_POST['dlseller_version'])){
		$dlseller_version = trim($_POST['dlseller_version']);
		update_post_meta($post_ID, '_dlseller_version', $dlseller_version);
	}
	if(isset($_POST['dlseller_author'])){
		$dlseller_author = trim($_POST['dlseller_author']);
		update_post_meta($post_ID, '_dlseller_author', $dlseller_author);
	}
	if(isset($_POST['dlseller_purchases'])){
		$dlseller_purchases = trim($_POST['dlseller_purchases']);
		update_post_meta($post_ID, '_dlseller_purchases', $dlseller_purchases);
	}
	if(isset($_POST['dlseller_downloads'])){
		$dlseller_downloads = trim($_POST['dlseller_downloads']);
		update_post_meta($post_ID, '_dlseller_downloads', $dlseller_downloads);
	}
}

function dlseller_shop_admin_page() {
	global $usces, $wpdb, $wp_locale, $current_user;
	global $wp_query;
	
	if( isset($_POST['dlseller_transition']) && 'dlseller_option_update' == $_POST['dlseller_transition']){
		$_POST = $usces->stripslashes_deep_post($_POST);
		$dlseller_options = array();
		$dlseller_options['content_path'] = trim($_POST['dlseller_content_path']);
		$dlseller_options['dlseller_terms'] = trim($_POST['dlseller_terms']);
		$dlseller_options['dlseller_terms2'] = trim($_POST['dlseller_terms2']);
		$dlseller_options['dlseller_rate'] = (int)trim($_POST['dlseller_rate']);
		$dlseller_options['dlseller_member_reinforcement'] = isset($_POST['dlseller_member_reinforcement']) ? $_POST['dlseller_member_reinforcement'] : 'off';
		$dlseller_options['dlseller_restricting'] = isset($_POST['dlseller_restricting']) ? $_POST['dlseller_restricting'] : 'on';
		update_option('dlseller', $dlseller_options);
		$usces->action_status = 'success';
		$usces->action_message = __('options are updated', 'usces');
	}
	
	if(empty($usces->action_message) || $usces->action_message == '') {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}

	require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_dlseller_page.php');
}

/* continue member list page */
function continue_member_list_page() {
	global $usces;
	if(empty($usces->
	action_message) || $usces->action_message == '') {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}
	$member_action = isset($_REQUEST['continue_action']) ? $_REQUEST['continue_action'] : '';
	switch ($member_action) {
		case 'editpost':
			$res = usces_update_continue_memberdata();
			if ( 1 === $res ) {
				$usces->set_action_status('success', __('Continuation charging information is updated','dlseller'));
			} elseif ( 0 === $res ) {
				$usces->set_action_status('none', '');
			} else {
				$usces->set_action_status('error', 'ERROR : '.__('failure in update','usces'));
			}
			require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_continue_member_edit_form.php');	
			break;
		case 'edit':
			require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_continue_member_edit_form.php');	
			break;
		default:
			require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_continue_member_list_page.php');	
	}

}

function dlseller_get_item() {
	global $usces;
	$args = func_get_args();
	$usces_item = isset($args[0]) ? $args[0] : array();
	if(isset($args[1])) {
		$post_id = $args[1];
		$item_division = get_post_meta($post_id, '_item_division', true);
		$item_charging_type = get_post_meta($post_id, '_item_charging_type', true);
		$item_frequency = get_post_meta($post_id, '_item_frequency', true);
		$dlseller_interval = get_post_meta($post_id, '_dlseller_interval', true);
		$dlseller_validity = get_post_meta($post_id, '_dlseller_validity', true);
		$dlseller_file = get_post_meta($post_id, '_dlseller_file', true);
		$dlseller_date = get_post_meta($post_id, '_dlseller_date', true);
		$dlseller_version = get_post_meta($post_id, '_dlseller_version', true);
		$dlseller_author = get_post_meta($post_id, '_dlseller_author', true);
		$dlseller_purchases = get_post_meta($post_id, '_dlseller_purchases', true);
		$dlseller_downloads = get_post_meta($post_id, '_dlseller_downloads', true);
//20120614ysk start 0000505
		$usces_item['item_division'] = !empty($item_division) ? $item_division : 'shipped';
		$usces_item['item_charging_type'] = !empty($item_charging_type) ? $item_charging_type : 0;
		$usces_item['item_frequency'] = !empty($item_frequency) ? $item_frequency : 1;
		$usces_item['item_chargingday'] = $usces->getItemChargingDay( $post_id );
		$usces_item['dlseller_interval'] = !empty($dlseller_interval) ? $dlseller_interval : '';
		$usces_item['dlseller_validity'] = !empty($dlseller_validity) ? $dlseller_validity : '';
		$usces_item['dlseller_file'] = !empty($dlseller_file) ? $dlseller_file : '';
		$usces_item['dlseller_date'] = !empty($dlseller_date) ? $dlseller_date : '';
		$usces_item['dlseller_version'] = !empty($dlseller_version) ? $dlseller_version : '';
		$usces_item['dlseller_author'] = !empty($dlseller_author) ? $dlseller_author : '';
//20120614ysk end
		$usces_item['dlseller_purchases'] = (int)$dlseller_purchases;
		$usces_item['dlseller_downloads'] = (int)$dlseller_downloads;
	}
	return $usces_item;
}

function dlseller_get_validityperiod($mid, $post_id) {
	global $usces;
	$history = $usces->get_member_history($mid);
	foreach ( $history as $row ) {
		if(strpos($row['order_status'], 'cancel') !== false || strpos($row['order_status'], 'estimate') !== false){
			continue;
		}else{
			$carts = $row['cart'];
			foreach($carts as $cart){
				if( $post_id == $cart['post_id'] ){
					$firstdate = $row['order_date'];
					break 2;
				}
			}
		}
	}
	if( !$firstdate ){
		$res = array('firstdate'=>NULL, 'lastdate'=>NULL);
	}else{
		$item = $usces->get_item( $post_id );
		if( empty($item['dlseller_validity']) ){
			$res = array( 'firstdate'=>(mysql2date(__('Y/m/d'), $firstdate)), 'lastdate'=>__('No limit', 'dlseller'),  'firstdates'=>date('Y/m/d', strtotime($firstdate)), 'lastdates'=>'' );
		}else{
			$t = getdate(strtotime($firstdate));
			$hour = empty($t['hour']) ? 0 : $t['hour'];
			$min = empty($t['minutes']) ? 0 : $t['minutes'];
			$sec = empty($t['seconds']) ? 0 : $t['seconds'];
			$month = empty($t['mon']) ? 0 : $t['mon'];
			$day = empty($t['mday']) ? 0 : $t['mday'];
			$year = empty($t['year']) ? 0 : $t['year'];
			$lastdate = date('Y-m-d H:i:s',  mktime($hour, $min, $sec, $month, $day+$item['dlseller_validity'], $year) );
			$res = array( 'firstdate'=>(mysql2date(__('Y/m/d'), $firstdate)), 'lastdate'=>(mysql2date(__('Y/m/d'), $lastdate)), 'firstdates'=>date('Y/m/d', strtotime($firstdate)), 'lastdates'=>(date('Y/m/d', strtotime($lastdate))) );
		}
	}
	return $res;
}

function dlseller_member_check() {
	$mes = '';
	foreach ( $_POST['member'] as $key => $vlue ) {
		$_SESSION['usces_member'][$key] = trim($vlue);
	}
	if ( $_POST['member_regmode'] == 'editmemberform' ) {
		if ( (trim($_POST['member']['password1']) != '' || trim($_POST['member']['password2']) != '') && trim($_POST['member']['password1']) != trim($_POST['member']['password2']) )
			$mes .= __('Password is not correct.', 'usces') . "<br />";
		if ( !is_email( $_POST['member']['mailaddress1'] ) )
			$mes .= __('e-mail address is not correct', 'usces') . "<br />";
			
	} else {
		if ( trim($_POST['member']['password1']) == '' || trim($_POST['member']['password2']) == '' || trim($_POST['member']['password1']) != trim($_POST['member']['password2']) )
			$mes .= __('Password is not correct.', 'usces') . "<br />";
			if ( !is_email($_POST['member']['mailaddress1']) || trim($_POST['member']['mailaddress1']) == '' || trim($_POST['member']['mailaddress2']) == '' || trim($_POST['member']['mailaddress1']) != trim($_POST['member']['mailaddress2']) )
			$mes .= __('e-mail address is not correct', 'usces') . "<br />";
		
	}
	if ( trim($_POST["member"]["name1"]) == "" )
		$mes .= __('Name is not correct', 'usces') . "<br />";
		
	$dlseller_opts = get_option('dlseller');
	if( 'on' == $dlseller_opts['dlseller_member_reinforcement'] ){
//		if ( trim($_POST["member"]["name3"]) == "" && USCES_JP )
//			$mes .= __('Invalid CANNAT pretend.', 'usces') . "<br />";
		if ( trim($_POST["member"]["zipcode"]) == "" )
			$mes .= __('postal code is not correct', 'usces') . "<br />";
		if ( $_POST["member"]["pref"] == __('-- Select --', 'usces') )
			$mes .= __('enter the prefecture', 'usces') . "<br />";
		if ( trim($_POST["member"]["address1"]) == "" )
			$mes .= __('enter the city name', 'usces') . "<br />";
		if ( trim($_POST["member"]["address2"]) == "" )
			$mes .= __('enter house numbers', 'usces') . "<br />";
		if ( trim($_POST["member"]["tel"]) == "" )
			$mes .= __('enter phone numbers', 'usces') . "<br />";
		if( trim($_POST['member']["tel"]) != "" && preg_match("/[^\d-]/", trim($_POST["member"]["tel"])) )
			$mes .= __('Please input a phone number with a half size number.', 'usces') . "<br />";
	}

	$mes = usces_filter_member_check_custom_member( $mes );
	$mes = apply_filters('dlseller_filter_member_check', $mes);
	
	return $mes;
}

function dlseller_customer_check($mes) {
	global $usces;
	$mes = '';

	if( dlseller_have_shipped() ){

		if ( !is_email($_POST['customer']['mailaddress1']) || trim($_POST['customer']['mailaddress1']) == '' || trim($_POST['customer']['mailaddress2']) == '' || trim($_POST['customer']['mailaddress1']) != trim($_POST['customer']['mailaddress2']) )
			$mes .= __('e-mail address is not correct', 'usces') . "<br />";
		if ( trim($_POST["customer"]["name1"]) == "" )
			$mes .= __('Name is not correct', 'usces') . "<br />";//20111116ysk 0000299
//		if ( trim($_POST["customer"]["name3"]) == "" && USCES_JP )
//			$mes .= __('Invalid CANNAT pretend.', 'usces') . "<br />";
		if ( trim($_POST["customer"]["zipcode"]) == "" )
			$mes .= __('postal code is not correct', 'usces') . "<br />";
		if ( $_POST["customer"]["pref"] == __('-- Select --', 'usces') )
			$mes .= __('enter the prefecture', 'usces') . "<br />";
		if ( trim($_POST["customer"]["address1"]) == "" )
			$mes .= __('enter the city name', 'usces') . "<br />";
		if ( trim($_POST["customer"]["address2"]) == "" )
			$mes .= __('enter house numbers', 'usces') . "<br />";
		if ( trim($_POST["customer"]["tel"]) == "" )
			$mes .= __('enter phone numbers', 'usces') . "<br />";
		if( trim($_POST['customer']["tel"]) != "" && preg_match("/[^\d-]/", trim($_POST["customer"]["tel"])) )
			$mes .= __('Please input a phone number with a half size number.', 'usces') . "<br />";
	}

	$mes = usces_filter_customer_check_custom_customer( $mes );

	return $mes;
}

function dlseller_delivery_check($mes) {
	global $usces;
	$mes = '';
	$ses = '';
	$entries = $usces->cart->get_entry();

	if( dlseller_have_shipped() ){

		if ( $_POST['delivery']['delivery_flag'] == '1' ) {
			if ( trim($_POST["delivery"]["name1"]) == "" )
				$mes .= __('Name is not correct', 'usces');
//			if ( trim($_POST["delivery"]["name3"]) == "" && USCES_JP )
//				$mes .= __('Invalid CANNAT pretend.', 'usces') . "<br />";
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
		}else{
			if( empty($entries['customer']['name1']) )
				$ses .= __('Name is not correct', 'usces');
			if( empty($entries['customer']['zipcode']) )
				$ses .= __('postal code is not correct', 'usces') . "<br />";
			if( $entries['customer']['pref'] == __('-- Select --', 'usces') )
				$ses .= __('enter the prefecture', 'usces') . "<br />";
			if( empty($entries['customer']['address1']) )
				$ses .= __('enter the city name', 'usces') . "<br />";
			if( empty($entries['customer']['address2']) )
				$ses .= __('enter house numbers', 'usces') . "<br />";
			if( empty($entries['customer']['tel']) )
				$ses .= __('enter phone numbers', 'usces') . "<br />";
				
			if( !empty($ses) ){
				$_SESSION['usces_entry']['delivery']['delivery_flag'] = 1;
				$mes .= $ses;
			}
			
		}
		if ( !isset($_POST['offer']['delivery_method']) || (empty($_POST['offer']['delivery_method']) && $_POST['offer']['delivery_method'] != 0) )
			$mes .= __('chose one from delivery method.', 'usces') . "<br />";		
	}

	if ( !isset($_POST['offer']['payment_name']) )
		$mes .= __('chose one from payment options.', 'usces') . "<br />";
		
//20101119ysk start
	if(isset($_POST['offer']['delivery_method']) and isset($_POST['offer']['payment_name'])) {
		$d_method_index = $usces->get_delivery_method_index((int)$_POST['offer']['delivery_method']);
		if($usces->options['delivery_method'][$d_method_index]['nocod'] == '1') {
			$payments = $usces->getPayments($_POST['offer']['payment_name']);
			if('COD' == $payments['settlement'])
				$mes .= __('COD is not available.', 'usces') . "<br />";
		}
	}
//20101119ysk end

	$mes = usces_filter_delivery_check_custom_order( $mes );

	if ( !isset($_POST['offer']['terms']) && dlseller_have_dlseller_content() )
		$mes .= __('Not agree', 'dlseller') . "<br />";
	
	return $mes;
}

function add_dlseller_stylesheet() {
	$dlsellerStyleUrl = WP_PLUGIN_URL . '/wcex_dlseller/dlseller.css';
	$dlsellerStyleFile = WP_PLUGIN_DIR . '/wcex_dlseller/dlseller.css';
	if ( file_exists($dlsellerStyleFile) ) {
		wp_register_style('dlsellerStyleSheets', $dlsellerStyleUrl);
		wp_enqueue_style( 'dlsellerStyleSheets');
	}
}

function usces_dlseller_validity($post) {
	$validity = usces_get_itemMeta('_dlseller_validity', $post->ID, 'return');
	if ( empty($validity) )
		$res = __('No limit', 'dlseller');
	else
		$res = $validity;

	return $res;
}

function usces_dlseller_get_dlcount($post_id, $piriod = 'total', $mon = 0){
	global $usces;
	$today = substr(get_date_from_gmt(gmdate('Y-m-d H:i:s', time())), 0, 10);
	switch ($piriod){
		case 'today':
			$startday = $today;
			$endday = $today;
			break;
		case 'month':
			if($mon){
				$startday = $mon . '01';
				$endday = date('Y-m-d', mktime(0,0,0,(substr($mon, 5, 2)+1), 0, substr($mon, 0, 4)));
			}else{
				$startday = substr($today, 0, 8) . '01';
				$endday = date('Y-m-d', mktime(0,0,0,(substr($today, 5, 2)+1), 0, substr($today, 0, 4)));
			}
			break;
		case 'total':
			$startday = '2000-01-01';
			$endday = $today;
			break;
	}
	$data = $usces->get_access_piriod('dlseller', 'count', $startday, $endday);
	if( $data === false ){
		$res = NULL;
	}else{
		$res = array('par' => 0, 'dl' => 0);
		$par = 0;
		$dl = 0;
		foreach( $data as $values ){
			$vals = unserialize($values['acc_value']);
			foreach( (array)$vals as $key => $dls ){
				if($key == $post_id){
					$par += $dls['par'];
					$dl += $dls['dl'];
				}
			}
		}
		$res = array('par' => $par, 'dl' => $dl);
	}
	return $res;
}

function usces_dlseller_update_dlcount($post_id, $par=0, $dl=0 ){
	global $usces;
	$today = substr(get_date_from_gmt(gmdate('Y-m-d H:i:s', time())), 0, 10);
	
	$values = $usces->get_access('dlseller', 'count', $today);
	if( $values === NULL || !isset($values[$post_id]) ){
		$values[$post_id]['par'] = $par;
		$values[$post_id]['dl'] = $dl;
	}else{
		$values[$post_id]['par'] += $par;
		$values[$post_id]['dl'] += $dl;
	}
	
	$array = array();
	$array['acc_key'] = 'dlseller';
	$array['acc_type'] = 'count';
	$array['acc_value'] = $values;
	$array['acc_date'] = $today;
	
	$usces->update_access($array);
}

function usces_dlseller_filename($post, $sku = '') {
	if( is_object($post) ){
		$post_id = $post->ID;
	}else{
		$post_id = $post;
	}
	$path = dlseller_get_filename( $post_id );
	if ( empty($path) )
		$res = '';
	else
		$res = basename($path);

	return $res;
}

function dlseller_results_csv(){

	$content = 'AAA';
	$filename = 'dlseller_result.csv';

	header( "Content-Disposition: attachment; filename=" . $filename);
	header( "Content-Type: application/octet-stream" );
	//header( "Content-Length: " . $content_length ) ;
	print $content;

	exit;
}

function dlseller_action_reg_orderdata( $args ){
	global $usces;
	if( 'continue' != $args['charging_type'] ) return;
	
	$key = 'continuepay_' . $args['order_id'];
	//$order_date = substr($args['order_date'], 0, 10);
	$order_data = $usces->get_order_data($args['order_id']);
	$order_date = $order_data['date'];
	$carts = $order_data['cart'];
	$usces_item = $usces->get_item( $carts[0]['post_id'] );
	
	$value = array(
			'price'		=> $usces->get_total_price( $args['cart'] ),
			'acting'	=> $args['entry']['order']['payment_name'],
			'startdate'	=> $order_date,
			'interval'	=> $usces_item['dlseller_interval'],
			'chargingday' => $usces_item['item_chargingday'],
			'frequency'	=> $usces_item['item_frequency'],
			'condition'	=> '',
			'status'	=> 'continuation',
			'chargedday'	=> NULL,
			'contractedday' => NULL
			);
	
	$usces->set_member_meta_value($key, serialize($value));
}

function dlseller_action_del_orderdata( $obj ){
	global $usces;
	if( !$obj ) return;
	
	$member_id = $obj->mem_id;
	$key = 'continuepay_' . $obj->ID;
	$usces->del_member_meta($key, $member_id);
}

function dlseller_action_update_orderdata( $obj ){
	global $usces;
	if( !$obj ) return;
	$order_data = $usces->get_order_data($obj->ID);
	$carts = $order_data['cart'];
	if( !dlseller_have_continue_charge( $carts ) ) return;
	
	$member_id = $obj->mem_id;
	$key = 'continuepay_' . $obj->ID;
	$continues = $usces->get_member_meta_value($key, $member_id);
	if( empty($continues) ) 
		$continues = serialize(array());
		
	$usces_item = $usces->get_item( $carts[0]['post_id'] );
	
	$value = unserialize($continues);
	$condition = isset( $value['condition'] ) ? $value['condition'] : '';
	$chargedday = isset( $value['chargedday'] ) ? $value['chargedday'] : NULL;
	$contractedday = isset( $value['contractedday'] ) ? $value['contractedday'] : NULL;
	$status = ( false !== strpos($obj->order_status, 'cancel') ) ? 'cancellation' : 'continuation';

	$value = array(
			'price'		=> $order_data['end_price'],
			'acting'	=> $obj->order_payment_name,
			'startdate'	=> $obj->order_date,
			'interval'	=> $usces_item['dlseller_interval'],
			'chargingday' => $usces_item['item_chargingday'],
			'frequency'	=> $usces_item['item_frequency'],
			'condition'	=> $condition,
			'status'	=> $status, 
			'chargedday'	=> $chargedday,
			'contractedday' => $contractedday
			);
		
	$usces->set_member_meta_value($key, serialize($value), $member_id);
	
	$limit = $usces->get_member_meta_value('limitofcard', $member_id);
	$lmitparts = explode('/', $limit);
	if( 4 == strlen($lmitparts[0]) ){
		$limit = $lmitparts[1] . '/' . $lmitparts[0];
		$usces->set_member_meta_value('limitofcard', $limit, $member_id);
	}

}

function dlseller_trackPageview_redownload($push){
	$push[] = "'_trackPageview','/wc_redownload'";
	return $push;
}

function usces_ssl_attachment_uli_incontent($content)
{
	global $usces;
	if( $usces->is_cart_or_member_page($_SERVER['REQUEST_URI']) || $usces->is_inquiry_page($_SERVER['REQUEST_URI']) ){
		$content = str_replace(('src="'.get_option('siteurl')), ('src="'.USCES_SSL_URL_ADMIN), $content);
	}
	return $content;
}

function dlseller_filter_js_intoCart(){
	global $usces;
	$args = func_get_args();
	$str = $args[0];
	$post_id = $args[1];
	$js = '';

	$division = dlseller_get_division( $post_id );
	$charging_type = $usces->getItemChargingType( $post_id );
	
	if( false !== $usces->cart->num_row() && 'continue' == $charging_type ){
		if( dlseller_have_continue_charge() ) {
			$js = "if(confirm('" . __('You can add only one Monthly Subscription item in your shopping cart.', 'dlseller') . "')){\n";
			$js .= "return true;\n";
			$js .= "}else{\n";
			$js .= "return false;\n";
			$js .= "}\n";
		}else{
			$js = "if(confirm('" . __('You have Monthly Subscription item in your shopping cart. If you wantto add this item,you have to clear the item in your cart.Is it ok to clear your cart?', 'dlseller') . "')){\n";
			$js .= "return true;\n";
			$js .= "}else{\n";
			$js .= "return false;\n";
			$js .= "}\n";
		}
	}else if( false !== $usces->cart->num_row() ){
		if( dlseller_have_continue_charge() ) {
			$js = "if(confirm('" . __('This is Monthly Subscription item. If you want to add this item, youhave to clear theitem in your cart. Is it ok to clear your cart?', 'dlseller') . "')){\n";
			$js .= "return true;\n";
			$js .= "}else{\n";
			$js .= "return false;\n";
			$js .= "}\n";
		}
	}
	
	return $js;
}

function dlseller_filter_admin_modified_label(){
	return __('Modified', 'dlseller');
}

function dlseller_filter_confirm_prebutton_value(){
	return __('Back', 'usces');
}
//loop
function dlseller_filter_order_mail_meisai(){
	global $usces;
	$args = func_get_args();
	$meisai = $args[0];
	$data = $args[1];
	$order_id = $data['ID'];
	$cart = unserialize($data['order_cart']);
	$post_id = $cart[0]['post_id'];
	$charging_type = $usces->getItemChargingType( $post_id );

	if( 'continue' != $charging_type ) return $meisai;
	
	foreach ( $cart as $cart_row ) {
		$post_id = $cart_row['post_id'];
		$sku = $cart_row['sku'];
		$sku_code = urldecode($cart_row['sku']);
		$quantity = $cart_row['quantity'];
		$options = $cart_row['options'];
		$cartItemName = $usces->getCartItemName($post_id, $sku_code);
		$skuPrice = $cart_row['price'];
		$item_custom = usces_get_item_custom( $post_id, 'notag', 'return' );
		$charging_type = $usces->getItemChargingType( $post_id );
		$usces_item = $usces->get_item( $post_id );
		$continue_data = unserialize($usces->get_member_meta_value('continuepay_' . $order_id, $data['mem_id']));

		if ( empty($options) ) {
			$optstr =  '';
			$options =  array();
		}
		
		$meisai = "------------------------------------------------------------------\r\n";
		$meisai .= $cartItemName . "\r\n\r\n";
		if( is_array($options) && count($options) > 0 ){
			foreach($options as $key => $value){
				if( !empty($key) ) {
					$key = urldecode($key);
					if(is_array($value)) {
						$c = '';
						$meisai .= $key . ' : ';
						foreach($value as $v) {
							$meisai .= $c.urldecode($v);
							$c = ', ';
						}
						$meisai .= "\r\n"; 
					} else {
						$meisai .= $key . ' : ' . urldecode($value) . "\r\n"; 
					}
				}
			}
		}
		$meisai .= __('Unit price','usces') . " ".usces_crform( $skuPrice, true, false, 'return' ) . __(' * ','usces') . $cart_row['quantity'] . "\r\n";
		$meisai .= __('Amount', 'usces') . '（' . dlseller_frequency_name( $post_id, 'amount', 'return' ) . '） : ' . usces_crform( $skuPrice, true, false, 'return' ) . "\r\n\r\n";
		$meisai .= __('Application Date', 'dlseller') . ' : ' . $continue_data['startdate'] . "\r\n";
	
		$meisai .= __('Next Withdrawal Date', 'dlseller') . ' : ' . dlseller_next_charging( $order_id ) . "\r\n";
		if( 0 < (int)$usces_item['dlseller_interval'] )
			$meisai .= __('Renewal Date', 'dlseller') . ' : ' . dlseller_next_contracting( $order_id ) . "\r\n\r\n";
		

		if( $item_custom )
			$meisai .= $item_custom;
	
	}

	return $meisai;
}

function dlseller_get_charging_type( $post_id ){
	global $usces;
	$charging_type = $usces->getItemChargingType( $post_id );
	return $charging_type;
}

function dlseller_get_division( $post_id ){
	if( usces_is_item($post_id) ){
		$item_division = get_post_meta($post_id, '_item_division', true);
		$division = empty($item_division) ? 'shipped' : $item_division;
	}else{
		$division = NULL;
	}
	return $division;
}

function dlseller_have_shipped(){
	global $usces;
	$carts = $usces->cart->get_cart();
	
	$division = '';
	if(!empty($carts)) {
		foreach( $carts as $index => $cart ){
			extract($cart);
			if( 'shipped' == dlseller_get_division( $post_id ) ){
				$division = 'shipped';
				break;
			}
		}
	}
	if( 'shipped' == $division )
		return true;
	else
		return false;
}

function dlseller_have_continue_charge( $carts = NULL ){
	global $usces;
	if( NULL == $carts )
		$carts = $usces->cart->get_cart();
	$charging_type = 'once';
	foreach( $carts as $cart ){
		if( 'continue' == $usces->getItemChargingType( $cart['post_id'] ) ){
			$charging_type = 'continue';
			break;
		}
	}
	if( 'continue' == $charging_type )
		return true;
	else
		return false;
}

function dlseller_have_dlseller_content(){
	global $usces;
	$carts = $usces->cart->get_cart();
	
	$division = '';
	foreach( $carts as $index => $cart ){
		extract($cart);
		$content = dlseller_get_division( $post_id );
		if( 'data' == $content || 'service' == $content ){
			$division = 'dlseller';
			break;
		}
	}
	if( 'dlseller' == $division )
		return true;
	else
		return false;
}

function dlseller_terms(){
	$dlseller_options = get_option('dlseller');
	if( dlseller_have_continue_charge() ){
		$dlseller_terms = nl2br(esc_html($dlseller_options['dlseller_terms2']));
	}else{
		$dlseller_terms = nl2br(esc_html($dlseller_options['dlseller_terms']));
	}
	echo $dlseller_terms;
}

function dlseller_completion_info($usces_carts , $out=''){
	global $usces, $usces_entries;
	$member = $usces->get_member();
	$oid = isset($usces_entries['order']['ID']) ? $usces_entries['order']['ID'] : '';
	$html = '<ul class="dllist">';
	
	for($i=0; $i<count($usces_carts); $i++) { 
		$cart_row = $usces_carts[$i];
		$post_id = $cart_row['post_id'];
		$sku = $cart_row['sku'];
		$sku_code = esc_attr(urldecode($cart_row['sku']));
		$item_post = get_post( $post_id );
		$usces_item = $usces->get_item( $post_id );
		$periods = dlseller_get_validityperiod($member['ID'], $post_id);
		$cartItemName = $usces->getCartItemName($post_id, $sku_code );
		$purchased = $usces->is_purchased_item($member['ID'], $post_id);
		$charging_type = $usces->getItemChargingType( $post_id );
		$item_custom = usces_get_item_custom( $post_id, 'table', 'return' );
		$options = $cart_row['options'];
		if ( empty($options) ) {
			$optstr =  '';
			$options =  array();
		}
		
		$list = '<li>';
		$list .= '<div class="thumb">'."\n";
		$itemImage = usces_the_itemImage(0, 200, 250, $item_post, 'return');
		$list .= apply_filters('dlseller_filter_the_itemImage', $itemImage, $item_post);
		$list .= '</div>'."\n";

		if( 'service' == $usces_item['item_division'] ){
			
			if( 'continue' == $charging_type ){
		
				$nextdate = get_date_from_gmt(gmdate('Y-m-d H:i:s', time()));
				$chargingday = $usces->getItemChargingDay( $post_id );

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>' . esc_html($cartItemName) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '<tr><th>' . __('Application Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), mktime(0,0,0,(int)substr($nextdate, 5, 2),(int)substr($nextdate, 8, 4),(int)substr($nextdate, 0, 4))) . '</td></tr>'."\n";
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . dlseller_first_charging( $post_id ) . '</td></tr>'."\n";
				if( 0 < (int)$usces_item['dlseller_interval'] ){
					$list .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month（Automatic Renewal）', 'dlseller') . '</td></tr>'."\n";
				}
				$list .= '</table>'."\n";
				$list .= '</div>'."\n";
			
			}else{

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Code', 'dlseller') . '</th><td>' . esc_html($usces_item['itemCode']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>' . esc_html($cartItemName) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				$purchase_mes = '';
				if( $purchased !== true ){
					$purchase_mes = '<p>' . __('I contact you by an email if I can confirm the receipt of money.', 'dlseller') . '</p>'."\n";
				}
				$list .= apply_filters('dlseller_filter_service_purchase_message', $purchase_mes, $purchased);
				$list .= '</div>'."\n";
				$list .= '<div class="clear"></div>'."\n";
			}

		}elseif( 'data' == $usces_item['item_division'] ){

			usces_dlseller_update_dlcount($post_id, 1, 0 );
			$files = dlseller_get_filename( $post_id, $sku );
			$filename = basename($files);
			
			if( 'continue' == $charging_type ){
		
				$nextdate = get_date_from_gmt(gmdate('Y-m-d H:i:s', time()));
				$chargingday = $usces->getItemChargingDay( $post_id );
			
				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Code', 'dlseller') . '</th><td>' . esc_html($usces_item['itemCode']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>' . esc_html($cartItemName) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '<tr><th>' . __('Version', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_version']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Release Date', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_date']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Author', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_author']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('dlValidity(days)', 'dlseller') . '</th><td>'."\n";
				if( empty($periods['lastdate']) ){
					$list .= __('No limit', 'dlseller');
				}else{
					$list .= esc_html($periods['firstdate']) . '～' . esc_html($periods['lastdate']);
				}
				$list .= '</td></tr>'."\n";
				$list .= '<tr><th>' . __('File Name', 'dlseller') . '</th><td>' . $filename . '</td></tr>'."\n";
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . dlseller_first_charging( $post_id ) . '</td></tr>'."\n";
				if( 0 < (int)$usces_item['dlseller_interval'] ){
					$list .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month（Automatic Renewal）', 'dlseller') . '</td></tr>'."\n";
				}
				$list .= '</table>'."\n";
				$list .= '<a class="redownload_button" href="' . USCES_CART_URL . $usces->delim . 'dlseller_transition=download&rid=' . $i . '&oid=' . $oid . apply_filters('dlseller_filter_download_para', '', $post_id, $sku) . '">' . __('Download', 'dlseller') . '</a>'."\n";
				$list .= '<p>' . __('You can download it again during your subscription period.', 'dlseller') . '</p>'."\n";
				$list .= '</div>'."\n";
				$list .= '<div class="clear"></div>'."\n";

			}else{
			
				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Code', 'dlseller') . '</th><td>' . esc_html($usces_item['itemCode']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>' . esc_html($cartItemName) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '<tr><th>' . __('Version', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_version']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Release Date', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_date']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Author', 'dlseller') . '</th><td>' . esc_html($usces_item['dlseller_author']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('dlValidity(days)', 'dlseller') . '</th><td>'."\n";
				if( empty($periods['lastdate']) ){
					$list .= __('No limit', 'dlseller');
				}else{
					$list .= esc_html($periods['firstdate']) . '～' . esc_html($periods['lastdate']);
				}
				$list .= '</td></tr>'."\n";
				$list .= '<tr><th>' . __('File Name', 'dlseller') . '</th><td>' . $filename . '</td></tr>'."\n";
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				if( $purchased !== true ){
					$purchase_mes = '<p>' . __('After the receipt of money, you can download it from your member page.', 'dlseller') . '</p>'."\n";
					$purchase_mes .= '<p>' . __('I contact you by an email if I can confirm the receipt of money.', 'dlseller') . '</p>'."\n";
				}else{
					$purchase_mes = '<a class="redownload_button" href="' . USCES_CART_URL . $usces->delim . 'dlseller_transition=download&rid=' . $i . '&oid=' . $oid . apply_filters('dlseller_filter_download_para', '', $post_id, $sku) . '">' . __('Download', 'dlseller') . '</a>'."\n";
					$purchase_mes .= '<p>' . __('You can download it again during your subscription period.', 'dlseller') . '</p>'."\n";
				}
				$list .= apply_filters('dlseller_filter_data_purchase_message', $purchase_mes, $purchased);
				$list .= '</div>'."\n";
				$list .= '<div class="clear"></div>'."\n";
			}
		
		}else{//'shipped' == $usces_item['item_division']
			
			if( 'continue' == $charging_type ){
		
				$nextdate = get_date_from_gmt(gmdate('Y-m-d H:i:s', time()));
				$chargingday = $usces->getItemChargingDay( $post_id );
				
				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>' . esc_html($cartItemName) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '<tr><th>' . __('Application Date', 'dlseller') . '</th><td>' . date(__('Y/m/d'), mktime(0,0,0,(int)substr($nextdate, 5, 2),(int)substr($nextdate, 8, 4),(int)substr($nextdate, 0, 4))) . '</td></tr>'."\n";
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . dlseller_first_charging( $post_id ) . '</td></tr>'."\n";
				if( 0 < (int)$usces_item['dlseller_interval'] ){
					$list .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month（Automatic Renewal）', 'dlseller') . '</td></tr>'."\n";
				}
				$list .= '</table>'."\n";
				$list .= '</div>'."\n";
			
			}else{

				$list .= '<div class="item_info_list">'."\n";
				$list .= '<table class="dlseller">'."\n";
				$list .= '<tr><th>' . __('Item Code', 'dlseller') . '</th><td>' . esc_html($usces_item['itemCode']) . '</td></tr>'."\n";
				$list .= '<tr><th>' . __('Item Name', 'dlseller') . '</th><td>' . esc_html($cartItemName) . '</td></tr>'."\n";
				if( is_array($options) && count($options) > 0 ){
					foreach($options as $key => $value){
						if( !empty($key) ) {
							$key = urldecode($key);
							if(is_array($value)) {
								$c = '';
								$list .= '<tr><th>' . esc_html($key) . '</th><td>';
								foreach($value as $v) {
									$list .= $c.esc_html(urldecode($v));
									$c = ', ';
								}
								$list .= '</td></tr>'."\n";
							} else {
								$list .= '<tr><th>' . esc_html($key) . '</th><td>' . nl2br(esc_html(urldecode($value))) . '</td></tr>'."\n";
							}
						}
					}
				}
				$list .= '</table>'."\n";
				if($item_custom){
					$list .= $item_custom;
				}
				if( $purchased !== true ){
					$purchase_mes = '<p>' . __('I am in the dispatch of the product if I can confirm the receipt of money.', 'dlseller') . '</p>'."\n";
				}else{
					$purchase_mes = '';
				}
				$list .= apply_filters('dlseller_filter_shipped_purchase_message', $purchase_mes, $purchased);
				$list .= '</div>'."\n";
				$list .= '<div class="clear"></div>'."\n";
			}

		}
		
		
		$list .= '</li>'."\n";
		$html .= apply_filters('dlseller_filter_completion_list', $list, $cart_row);
	}
	if( isset($_GET['dlseller_update']) ){
		$html .= '<li>';
		$html .= '<div class="update_info">'."\n";
		$html .= '<p>' . __('Card information update processing was completed. Thank you.', 'dlseller') . '</p>'."\n";
		$html .= '</div>'."\n";
		$html .= '</li>';
	}
	$html .= '</ul>'."\n";
	$html = apply_filters('dlseller_filter_completion_html', $html, $usces_carts);

	if( 'return' == $out )
		return $html;
	else
		echo $html;
}

function dlseller_upcard_url( $member_id, $order_id, $cardlimit){
	global $usces;
	if( !$member_id || !$order_id || !$cardlimit ) return;

	$html = '';
	$limits = explode('/', $cardlimit);
	$limit = substr(current_time('mysql', 0), 0, 2) . $limits[1] . $limits[0];
	$now = date('Ym', current_time('timestamp', 0));
	
	if( $limit <= $now ){
		$html = '<a href="javascript:void(0)" onClick="uscesMail.getMailData(\'' . $member_id . '\', \'' . $order_id . '\')">' . __('Update Request Email', 'dlseller') . '</a>';
	}
	
	echo '<br />' . $html;
}

function dlseller_make_mail_ajax(){
	global $usces;
	
	$order_id = $_POST['order_id'];
	$member_id = $_POST['member_id'];
	$now = date('Ym', current_time('timestamp', 0));
	$infos = $usces->get_member_info( $member_id );
	$mail_data = $usces->options['mail_data'];
	$delim = ( false === strpos(usces_url('cartnonsession', 'return'), '?') ) ? '?' : '&';
	
	$limits = explode('/', $infos['limitofcard']);
	$regd = date('Ym', strtotime(substr(current_time('mysql', 0), 0, 2) . $limits[1] . '-' . $limits[0] . '-01'));
	if( $regd == $now ){
		$flag = 'NOW';
	}else if( $regd < $now ){
		$flag = 'PASSED';
	}else{
		die();
	}

	$limit = date(__('F, Y'), strtotime(substr(current_time('mysql', 0), 0, 2) . $limits[1] . '-' . $limits[0] . '-01'));
	$mail = $infos['mem_email'];
	$name = usces_localized_name( $infos['mem_name1'], $infos['mem_name2'], 'return' );
	$subject = __('クレジットカード情報更新のお願い', 'dlseller');
	$message = "メンバーID：" . $infos['ID'] . "\n";
	$message .= "お名前：" . $name . " 様\n\n\n";
	$message .= "平素は当サービスをご利用いただき誠にありがとうございます。\n\n";
	$message .= "このメールは、ご契約中のサービスの継続ご利用におきましての重要な\n";
	$message .= "連絡でございますので必ずお目通しください。\n\n";
	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
	$message .= "現在ご登録のクレジットカードの有効期限は、" . $limit . "となっており\n";
	if( 'NOW' == $flag ){
		$message .= "このままですと、来月のお支払いが不履行となってしまいます。\n";
	}else{
		$message .= "当月のお支払いが不履行となっております。\n";
	}
	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
	$message .= "つきましては、お手元に新しいクレジットカードが届いておりましたら\n";
	$message .= "今月中に下のURL をクリックしてカード情報の更新を行なっていただきたいと\n";
	$message .= "存じます。\n";
	$message .= "大変お手数をおかけ致しますが、何卒よろしくお願いいたします。\n\n\n";
	$message .= usces_url('cartnonsession', 'return') . $delim . 'dlseller_card_update=login&dlseller_up_mode=1&dlseller_order_id=' . $order_id . "\n";
	$message .= "\n尚、カード情報更新手続きがうまく行かなかった場合は、恐れ入りますが\n";
	$message .= "下記のメールアドレス宛てにご連絡ください。\n\n";
	$message .= "今後ともよろしくお願いいたします。\n";
	$message .= "\n\n";
	$message .= $mail_data['footer']['ordermail'];
			
	$ret = $mail . '#usces#' . $name . '#usces#' . $subject . '#usces#' . $message;
	die($ret);
}

function dlseller_send_mail_ajax() {
	global $wpdb, $usces;
	$_POST = $usces->stripslashes_deep_post($_POST);
	
	$order_id = $_POST['oid'];
	$member_id = $_POST['mid'];
	
	$para = array(
			'to_name' => sprintf(__('Mr/Mrs %s', 'usces'), trim(urldecode($_POST['name']))),
			'to_address' => trim($_POST['mailaddress']), 
			'from_name' => get_option('blogname'), 
			'from_address' => $usces->options['sender_mail'], 
			'return_path' => $usces->options['error_mail'],
			'subject' => trim(urldecode($_POST['subject'])),
			'message' => trim(urldecode($_POST['message']))
			);
	
	$res = usces_send_mail( $para );
	if($res){
	
		$key = 'continuepay_' . $order_id;
		$continuepay = unserialize($usces->get_member_meta_value($key, $member_id));
		$continuepay['condition'] = __('Credit card update request', 'dlseller') . '<br />（' . date(__('F j, Y'), current_time('timestamp', 0)) . '）';
		$usces->set_member_meta_value($key, serialize($continuepay), $member_id);
	
		$bcc_para = array(
				'to_name' => 'Shop Admin',
				'to_address' => $usces->options['sender_mail'], 
				'from_name' => 'Welcart Auto BCC', 
				'from_address' => 'Welcart', 
				'return_path' => $usces->options['error_mail'],
				'subject' => trim(urldecode($_POST['subject'])) . ' to ' . sprintf(__('Mr/Mrs %s', 'usces'), trim(urldecode($_POST['name']))),
				'message' => trim(urldecode($_POST['message']))
				);
		
		usces_send_mail( $bcc_para );

		die('success');
	}else{
		die('error');
	}
}

function dlseller_frequency_name( $post_id, $type = '', $out = '' ){
	global $usces;
	$frequency = (int)$usces->getItemFrequency( $post_id );
	if( 'amount' == $type ){
		switch( $frequency ){
			case 1:
				$name = __('Monthly Fee', 'dlseller');
				break;
			case 6:
				$name = __('Semiannual Fee', 'dlseller');
				break;
			case 12:
				$name = __('Annual Fee', 'dlseller');
				break;
		}
	}else{
		switch( $frequency ){
			case 1:
				$name = __('Monthly Fee', 'dlseller');
				break;
			case 6:
				$name = __('Semiannual Fee', 'dlseller');
				break;
			case 12:
				$name = __('Annual Fee', 'dlseller');
				break;
		}
	}
	if( 'return' == $out )
		return $name;
	else
		echo $name;
}

function dlseller_first_charging( $post_id, $type = '' ){
	global $usces;
	$now = current_time('mysql');
	$thisyear = (int)substr($now, 0, 4);
	$thismonth = (int)substr($now, 5, 2);
	$today = (int)substr($now, 8, 2);
	$usces_item = $usces->get_item( $post_id );
	
	if( $today < $usces_item['item_chargingday'] ){
		$month = $thismonth;
	}else{
		$month = $thismonth + 1;
	}
	
	$time = mktime(0, 0, 0, $month, $usces_item['item_chargingday'], $thisyear);
	$date = date(__('Y/m/d'), $time);

	if( 'time' == $type ){
		return $time;
	}else{
		return $date;
	}
}

function dlseller_next_charging( $order_id, $type = '' ){
	global $usces;
	$key = 'continuepay_' . $order_id;
	$order_data = $usces->get_order_data($order_id);
	$carts = $order_data['cart'];
	$post_id = $carts[0]['post_id'];
	$usces_item = $usces->get_item( $post_id );

	$continue_data = unserialize($usces->get_member_meta_value($key, $order_data['mem_id']));
	if( empty($continue_data) ) return;
	
	if( NULL == $continue_data['chargedday'] ){
	
		$time = dlseller_first_charging( $post_id, 'time' );
		$date = date(__('Y/m/d'), $time);
	
	}else{
	
		$date = $continue_data['chargedday'];
		$year = (int)substr($date, 0, 4);
		$month = (int)substr($date, 5, 2);
		$day = (int)substr($date, 8, 2);
		$time = mktime(0, 0, 0, $month + $usces_item['item_frequency'], $usces_item['item_chargingday'], $year);
		$date = date(__('Y/m/d'), $time);
	}
	

	if( 'time' == $type ){
		return $time;
	}else{
		return $date;
	}
}

function dlseller_first_contracting( $post_id, $type = '' ){
	global $usces;
	$now = current_time('mysql');
	$thisyear = (int)substr($now, 0, 4);
	$thismonth = (int)substr($now, 5, 2);
	$today = (int)substr($now, 8, 2);
	$usces_item = $usces->get_item( $post_id );
	
	if( $today < $usces_item['item_chargingday'] ){
		$month = $thismonth;
	}else{
		$month = $thismonth + 1;
	}
	
	if( empty($usces_item['dlseller_interval']) ){
	
		$time = NULL;
		$date = NULL;
	
	}else{

		$time = mktime(0, 0, 0, $month + $usces_item['dlseller_interval'], $usces_item['item_chargingday'], $thisyear);
		$date = date(__('Y/m/d'), $time);
	}

	if( 'time' == $type ){
		return $time;
	}else{
		return $date;
	}
}

function dlseller_next_contracting( $order_id, $type = '' ){
	global $usces;
	$key = 'continuepay_' . $order_id;
	$order_data = $usces->get_order_data($order_id);
	$carts = $order_data['cart'];
	$post_id = $carts[0]['post_id'];
	$usces_item = $usces->get_item( $post_id );

	$continue_data = unserialize($usces->get_member_meta_value($key, $order_data['mem_id']));
	if( empty($continue_data) ) return;
	
	if( empty($usces_item['dlseller_interval']) ){
	
		$time = NULL;
		$date = NULL;
	
	}elseif( empty($continue_data['contractedday']) ){
	
		$time = dlseller_first_contracting( $post_id, 'time' );
		$date = date(__('Y/m/d'), $time);
	
	}else{
	
		$date = $continue_data['contractedday'];
		$year = (int)substr($date, 0, 4);
		$month = (int)substr($date, 5, 2);
		$day = (int)substr($date, 8, 2);
		$time = mktime(0, 0, 0, $month + $usces_item['dlseller_interval'], $usces_item['item_chargingday'], $year);
		$date = date(__('Y/m/d'), $time);
	}
	

	if( 'time' == $type ){
		return $time;
	}else{
		return $date;
	}
}

function dlseller_get_filename( $post_id, $sku = '' ){
	$file = get_post_meta($post_id, '_dlseller_file', true);
	return apply_filters('dlseller_filter_filename', $file, $post_id, $sku);
}
//20111107ysk start 0000287
function dlseller_fiter_the_payment_method() {
	$args = func_get_args();
	$payments = $args[0];
	//$value = i$args[1];
	$dl_payments = array();

	if( !dlseller_have_shipped() ) {
		foreach( $payments as $payment ){
			if( $payment['settlement'] != 'COD' ) 
				$dl_payments[] = $payment;
		}
		ksort($dl_payments);
	} else {
		$dl_payments = $payments;
	}
	return $dl_payments;
}
//20111107ysk end
?>
