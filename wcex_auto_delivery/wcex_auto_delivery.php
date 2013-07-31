<?php
/*
Plugin Name: WCEX Auto Delivery
Plugin URI: http://www.welcart.com/
Description: このプラグインはWelcart専用の定期購入販売拡張プラグインです。Welcart本体と一緒にご利用下さい。
Version: 1.0
Author: Collne Inc.
Author URI: http://www.welcart.com/
*/

if( !defined('USCES_EX_PLUGIN') )
	define( 'USCES_EX_PLUGIN', 1 );

define( 'WCEX_AUTO_DELIVERY', true );
define( 'WCEX_AUTO_DELIVERY_VERSION', "1.0.0.1305141" );
define( 'WCEX_AUTO_DELIVERY_DB_REGULAR', '1.0' );
define( 'WCEX_AUTO_DELIVERY_DB_REGULAR_DETAIL', '1.0' );

if( defined('USCES_VERSION') and version_compare(USCES_VERSION, '1.2-bata', '>=') ):

	load_plugin_textdomain( 'autodelivery', USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/languages', plugin_basename(dirname(__FILE__)).'/languages' );

	require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/functions/define_function.php' );
	require( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/functions/function.php' );
	require( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/functions/template_func.php' );
	require( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/functions/utility.php' );

	$wcad_options = get_option( 'wcad_options' );
	if( !isset($wcad_options['acting_payment']) ) $wcad_options['acting_payment'] = 'off';
	if( !isset($wcad_options['date_calculation']) ) $wcad_options['date_calculation'] = 'off';
	if( !isset($wcad_options['campaign']) ) $wcad_options['campaign'] = 'on';
	if( !isset($wcad_options['scheduled_time']['hour']) ) $wcad_options['scheduled_time']['hour'] = 2;
	if( !isset($wcad_options['scheduled_time']['min']) ) $wcad_options['scheduled_time']['min'] = 0;
	update_option( 'wcad_options', $wcad_options );

	if( !get_option("usces_db_regular") or !get_option("usces_db_regular_detail") )
		wcad_create_table();

	add_action( 'plugins_loaded', 'wcad_setup' );
	register_deactivation_hook( __FILE__, 'wcad_event_clear' );

endif;

function wcad_setup() {
	global $usces, $wcad_options;

	add_action( 'init', 'wcad_init', 9 );
	add_action( 'usces_action_shop_admin_menue', 'wcad_add_shop_admin_menu' );
	add_action( 'usces_action_management_admin_menue', 'wcad_add_management_admin_menu' );
	add_action( 'usces_main', 'wcad_define_functions', 1 );
	add_action( 'wcad_event', 'wcad_do_event' );
	add_action( 'wp', 'wcad_schedule' );

	//register_activation_hook( __FILE__, 'wcad_create_regular_table' );

	//if( !isset($wcad_options['start_charge']) ) $wcad_options['start_charge'] = 'off';
	//if( !isset($wcad_options['auto_stop']) ) $wcad_options['auto_stop'] = 'off';
	//if( !isset($wcad_options['auto_order']) ) $wcad_options['auto_order'] = 'on';

	update_option( 'wcad_options', $wcad_options );
}

function wcad_init() {

	add_filter( 'usces_template_path_customer', 'wcad_path_customer', 20 );
	add_filter( 'usces_item_master_second_section', 'wcad_item_master_second_section', 10, 2 );
	add_filter( 'usces_filter_get_item', 'wcad_filter_get_item', 10, 2 );
	add_filter( 'usces_filter_send_order_mail_meisai', 'wcad_filter_order_mail_meisai', 10, 4 );
	add_filter( 'usces_fiter_the_payment_method', 'wcad_filter_the_payment_method' );
	add_filter( 'usces_filter_payment_detail', 'wcad_filter_payment_detail', 10, 2 );
	add_filter( 'usces_filter_delete_order_check', 'wcad_filter_delete_order_check', 10, 2 );
	add_filter( 'usces_filter_option_info_cart', 'wcad_filter_option_info_cart', 10, 2 );
	add_filter( 'usces_filter_option_info_confirm', 'wcad_filter_option_info_cart', 10, 2 );
	add_filter( 'usces_filter_order_edit_form_row', 'wcad_filter_order_edit_form_row', 10, 3 );
	add_filter( 'usces_filter_member_edit_form_row', 'wcad_filter_member_edit_form_row', 10, 3 );
	add_filter( 'usces_filter_option_info_history', 'wcad_filter_option_info_history', 10, 4 );
	add_filter( 'usces_filter_all_option_pdf', 'wcad_filter_all_option_pdf', 10, 5 );
	if( defined('WCEX_MOBILE') ) {
		add_filter( 'wcmb_filter_option_info_cart', 'wcad_filter_option_info', 9, 2 );
		add_filter( 'wcmb_filter_option_info_confirm', 'wcad_filter_option_info', 9, 2 );
		add_filter( 'wcmb_filter_history_item_name', 'wcad_filter_history_item_name', 9, 4 );
		add_filter( 'wcmb_filter_garak_template_redirect', 'wcad_filter_template_redirect' );
	}
	add_filter( 'usces_filter_order_list_sql_select', 'wcad_filter_order_list_sql_select' );
	add_filter( 'usces_filter_order_list_sql_jointable', 'wcad_filter_order_list_sql_jointable' );
	add_filter( 'usces_filter_order_list_column', 'wcad_filter_order_list_column' );
	add_filter( 'usces_filter_order_list_detail_trclass', 'wcad_filter_order_list_detail_trclass', 10, 2 );
	add_filter( 'usces_filter_order_list_detail', 'wcad_filter_order_list_detail', 10, 3 );
	add_filter( 'usces_filter_sku_meta_form_advance_title', 'wcad_filter_sku_meta_form_advance_title', 10 );
	add_filter( 'usces_filter_sku_meta_form_advance_field', 'wcad_filter_sku_meta_form_advance_field', 10, 2 );
	add_filter( 'usces_filter_sku_meta_row_advance', 'wcad_filter_sku_meta_row_advance', 10, 2 );
	add_filter( 'usces_filter_add_item_sku_meta_value', 'wcad_filter_add_item_sku_meta_value', 10 );
	add_filter( 'usces_filter_up_item_sku_meta_value', 'wcad_filter_up_item_sku_meta_value', 10 );
	add_filter( 'usces_filter_item_save_sku_metadata', 'wcad_filter_item_save_sku_metadata', 10, 2 );
	add_filter( 'usces_filter_inCart_price', 'wcad_filter_inCart_price', 10, 2 );
	add_filter( 'usces_filter_upCart_price', 'wcad_filter_upCart_price', 10, 3 );
	add_filter( 'usces_filter_settle_info_field_keys', 'wcad_filter_settle_info_field_keys' );
	add_filter( 'usces_filter_template_redirect', 'wcad_filter_template_redirect' );

	add_action( 'save_post', 'wcad_item_save_metadata' );
	add_action( 'init', 'wcad_add_stylesheet' );
	add_action( 'wp_head', 'wcad_shop_head' );
	add_action( 'usces_action_after_inCart', 'wcad_action_after_inCart' );
	add_action( 'usces_action_reg_orderdata', 'wcad_action_reg_orderdata', 11 );
	add_action( 'usces_action_del_orderdata', 'wcad_action_del_orderdata', 10 );
	add_action( 'usces_action_update_orderdata', 'wcad_action_update_orderdata', 10, 2 );
	add_action( 'usces_action_item_dupricate', 'wcad_action_item_dupricate', 10, 2 );
	add_action( 'usces_pre_reg_orderdata', 'wcad_pre_reg_orderdata' );
	add_action( 'usces_action_admin_ajax', 'wcad_action_admin_ajax' );
	add_action( 'usces_action_order_list_document_ready_js', 'wcad_action_order_list_document_ready_js' );
	add_action( 'usces_action_memberinfo_page_header', 'wcad_action_memberinfo_page_header' );

	add_action( 'usces_action_edit_memberdata', 'wcad_action_edit_memberdata', 10, 2 );
	add_action( 'usces_action_post_update_memberdata', 'wcad_action_post_update_memberdata', 10, 2 );

	add_action( 'wcad_action_reg_auto_orderdata', 'wcad_auto_order_mail' );
	add_action( 'admin_notices', 'wcad_admin_notices' );

	if( is_admin() && ( isset($_REQUEST['page']) && ( 'usces_shippinglist' == $_REQUEST['page'] || 'usces_regularlist' == $_REQUEST['page'] ) ) ) {
		wp_enqueue_script('jquery-ui-dialog');
	}
}

function wcad_add_shop_admin_menu() {
	add_submenu_page( USCES_PLUGIN_BASENAME, __('Auto Delivery Setting','autodelivery'), __('Auto Delivery Setting','autodelivery'), 'level_6', 'wcex_auto_delivery', 'wcad_admin_page' );
}

function wcad_add_management_admin_menu() {
	add_submenu_page( 'usces_orderlist', __('Shipping Schedule List','autodelivery'), __('Shipping Schedule List','autodelivery'), 'level_6', 'usces_shippinglist', 'wcad_shipping_list_page' );
	add_submenu_page( 'usces_orderlist', __('Regular Purchase List','autodelivery'), __('Regular Purchase List','autodelivery'), 'level_6', 'usces_regularlist', 'wcad_regular_list_page' );
}

function wcad_schedule( $add = 1 ) {
	if( wp_next_scheduled('wcad_event') )
		return;

	global $wcad_options;
	$gmt_offset = get_option( 'gmt_offset' );
	$now = current_time( 'timestamp', 0 );
	$year = (int)date('Y',$now);
	$month = (int)date('n',$now);
	$day = (int)date('j',$now) + (int)$add;
	$timestamp = mktime( (int)$wcad_options['scheduled_time']['hour'], (int)$wcad_options['scheduled_time']['min'], 0, $month, $day, $year ) - ( $gmt_offset * 3600 );
	wp_schedule_event( $timestamp, 'daily', 'wcad_event' );
}

function wcad_do_event() {
	if( !wcad_event_mark() )
		return;

	$auto_orders = wcad_get_auto_order();

	if( 0 < count($auto_orders) )
		wcad_make_order( $auto_orders );
}

function wcad_event_clear() {
	wp_clear_scheduled_hook( 'wcad_event' );
}

function wcad_event_mark() {
	global $wpdb;

list($micro, $unixtime) = explode(" ", microtime());
$sec = $micro + date("s", $unixtime);
usces_log("wcad_event_mark:".date('Y-m-d H:i:s', current_time('timestamp')).$sec,"wcad.log");
	$today = date( 'Y-m-d', current_time('timestamp') );
	$table_name = $wpdb->prefix."usces_access";
	$query = $wpdb->prepare( "SELECT acc_date FROM {$table_name} WHERE acc_key = %s LIMIT 1", 'wcad_event' );
	$acc_date = $wpdb->get_var( $query );
	if( $acc_date == $today )
		return false;

	sleep(rand(1,10));

	if( $acc_date ) {
		$query = $wpdb->prepare( "UPDATE {$table_name} SET acc_date = %s WHERE acc_key = %s LIMIT 1", $today, 'wcad_event' );
	} else {
		$query = $wpdb->prepare( "INSERT INTO {$table_name} (acc_date, acc_key) VALUES (%s, %s)", $today, 'wcad_event' );
	}
usces_log("wcad_event_mark:query=".$query,"wcad.log");
	$res = $wpdb->query( $query );
	if( !$res ) {
usces_log("wcad_event_mark:res=".$res,"wcad.log");
usces_log("wcad_event_mark:*** Stopped the automatic orders. ***","wcad.log");
		return false;
	}
	return true;
}

function wcad_path_customer( $path ) {
	$path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/customer_info.php';
	return $path;
}

/* setting page */
function wcad_admin_page() {
	global $usces, $wpdb, $wcad_options;

	if( isset($_POST['auto_delivery_transition']) && 'auto_delivery_option_update' == $_POST['auto_delivery_transition'] ) {
		$wcad_options['acting_payment'] = $_POST['acting_payment'];
		$wcad_options['date_calculation'] = $_POST['date_calculation'];
		$wcad_options['campaign'] = $_POST['campaign'];
		$wcad_options['scheduled_time'] = $_POST['scheduled_time'];
		update_option( 'wcad_options', $wcad_options );

		if( ($_POST['scheduled_time']['hour'] != $_POST['scheduled_time_before']['hour']) or 
			($_POST['scheduled_time']['min'] != $_POST['scheduled_time_before']['min']) ) {
			$add = 1;
			$today = date( 'Y-m-d', current_time('timestamp') );
			$table_name = $wpdb->prefix."usces_access";
			$query = $wpdb->prepare( "SELECT acc_date FROM {$table_name} WHERE acc_key = %s LIMIT 1", 'wcad_event' );
			$acc_date = $wpdb->get_var( $query );
			if( $acc_date and $acc_date != $today ) {
				$now = date( 'Hi', current_time('timestamp') );
				if( $now < $_POST['scheduled_time']['hour'].$_POST['scheduled_time']['min'] ) {
					$add = 0;
				}
			}
			wcad_event_clear();
			wcad_schedule( $add );
		}

		$usces->action_status = 'success';
		$usces->action_message = __('options are updated', 'usces');
	}

	if( empty($usces->action_message) || $usces->action_message == '' ) {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}

	require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_auto_delivery_page.php' );
}

/* shipping schedule list page */
function wcad_shipping_list_page() {
	global $usces;

	if( empty($usces->action_message) || $usces->action_message == '' ) {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}

	$action = ( isset($_REQUEST['shipping_action']) ) ? $_REQUEST['shipping_action'] : '';
	switch( $action ) {
	case 'dlshippinglist':
		wcad_download_shipping_list();
		break;
	default:
		require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_shipping_list.php' );
	}
}

/* regular purchase list page */
function wcad_regular_list_page() {
	global $usces;

	if( empty($usces->action_message) || $usces->action_message == '' ) {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}

	$action = isset( $_REQUEST['regular_action'] ) ? $_REQUEST['regular_action'] : '';
	switch( $action ) {
	case 'editpost':
		$res = wcad_update_regulardata();
		if( 1 <= $res ) {
			$usces->set_action_status('success', __('Regular purchase data has been updated.','autodelivery'));
		} elseif( 0 == $res ) {
			$usces->set_action_status('none', '');
		} else {
			$usces->set_action_status('error', 'ERROR : '.__('failure in update','usces'));
		}
		require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_regular_edit_form.php' );
		break;
	case 'edit':
		require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_regular_edit_form.php' );
		break;
	case 'delete':
		$res = wcad_delete_regulardata();
		if( 1 === $res ) {
			$usces->set_action_status('success', __('Regular purchase data has been deleted.','autodelivery'));
		} elseif( 0 === $res ) {
			$usces->set_action_status('none', '');
		} else {
			$usces->set_action_status('error', 'ERROR : '.__('failure in delete','usces'));
		}
	default:
		require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_regular_list_page.php' );
	}
}

/* item page */
function wcad_item_master_second_section() {
	global $usces;
	$args = func_get_args();
	$html = $args[0];
	$post_id = $args[1];

	$division = $usces->getItemDivision( $post_id );
	$item_charging_type = get_post_meta( $post_id, '_item_charging_type', true );
	$regular_unit = get_post_meta( $post_id, '_wcad_regular_unit', true );
	$regular_interval = get_post_meta( $post_id, '_wcad_regular_interval', true );
	$regular_frequency = get_post_meta( $post_id, '_wcad_regular_frequency', true );

	$addtag = '
	<script type="text/javascript">
	jQuery(function($) {
		$("#item_charging_type").change(function() {
			if( "2" == $(this).val() ) {
				$("tr.regular").css("display","");
			} else {
				$("tr.regular").css("display","none");
			}
		});
		$("#item_charging_type").val('.(int)$item_charging_type.');
		$("#item_charging_type").triggerHandler("change");
		$("input[name=\'regular_unit\']").change(function() {
			if( "day" == $(this).val() ) {
				$("#regular_unit_name").html("'.__('Daily','autodelivery').'");
			} else if( "month" == $(this).val() ) {
				$("#regular_unit_name").html("'.__('Monthly','autodelivery').'");
			}
		});';
	if( !empty($regular_unit) ) {
		$addtag .= '
		$("#regular_unit_'.$regular_unit.'").attr("checked", true);
		$("input[name=\'regular_unit\']").triggerHandler("change");';
		if( "day" == $regular_unit ) {
			$addtag .= '
			$("#regular_unit_name").html("'.__('Daily','autodelivery').'");';
		} else if( "month" == $regular_unit ) {
			$addtag .= '
			$("#regular_unit_name").html("'.__('Monthly','autodelivery').'");';
		}
	}
	$addtag .= '
		});
	</script>
	<th>'.__('Charging type','usces').'</th>
	<td>
		<select id="item_charging_type" name="item_charging_type">
			<option value="0">'.__('Normal Charging','autodelivery').'</option>
			<option value="2">'.__('Regular Purchase','autodelivery').'</option>
		</select>
	</td>
	</tr>
	</tr>
	<tr class="regular">
	<th>'.__('Unit','autodelivery').'</th>
	<td><label for="regular_unit_day" style="width: 120px;"><input name="regular_unit" id="regular_unit_day" type="radio" value="day" />&nbsp;'.__('Daily','autodelivery').'</label>&nbsp;&nbsp;<label for="regular_unit_month" style="width: 120px;"><input name="regular_unit" id="regular_unit_month" type="radio" value="month" />&nbsp;'.__('Monthly','autodelivery').'</label></td>
	</tr>
	<tr class="regular">
	<th>'.__('Interval','autodelivery').'</th>
	<td><input type="text" name="regular_interval" id="regular_interval" class="short_text num" value="'.esc_attr($regular_interval).'" /><span id="regular_unit_name"></span></td>
	</tr>
	<tr class="regular">
	<th>'.__('Frequency','autodelivery').'</th>
	<td><input type="text" name="regular_frequency" id="regular_frequency" class="short_text num" value="'.esc_attr($regular_frequency).'" /></td>
	</tr>
	';

	echo $addtag.$html;
}

function wcad_filter_get_item() {
	global $usces;
	$args = func_get_args();
	$usces_item = ( isset($args[0]) ) ? $args[0] : array();
	if( isset($args[1]) ) {
		$post_id = $args[1];
		$item_division = get_post_meta( $post_id, '_item_division', true );
		$item_charging_type = get_post_meta( $post_id, '_item_charging_type', true );
		$regular_unit = get_post_meta( $post_id, '_wcad_regular_unit', true );
		$regular_interval = get_post_meta( $post_id, '_wcad_regular_interval', true );
		$regular_frequency = get_post_meta( $post_id, '_wcad_regular_frequency', true );
		$usces_item['item_division'] = ( !empty($item_division) ) ? $item_division : 'shipped';
		$usces_item['item_charging_type'] = ( !empty($item_charging_type) ) ? $item_charging_type : 0;
		$usces_item['regular_unit'] = ( !empty($regular_unit) ) ? $regular_unit : '';
		$usces_item['regular_interval'] = ( !empty($regular_interval) ) ? (int)$regular_interval : '';
		$usces_item['regular_frequency'] = ( !empty($regular_frequency) ) ? (int)$regular_frequency : '';
	}
	return $usces_item;
}

function wcad_filter_order_mail_meisai() {
	global $usces;
	$args = func_get_args();
	$meisai = $args[0];
	$data = $args[1];
	$entry = $args[3];
	$cart = unserialize($data['order_cart']);

	if( !wcad_have_regular_order($cart) ) return $meisai;

	$order_id = $data['ID'];
	$payment = $usces->getPayments( $entry['order']['payment_name'] );

	$meisai = "\r\n".__('Items','usces')." : \r\n";
	foreach( $cart as $cart_row ) {
		$post_id = $cart_row['post_id'];
		$sku = $cart_row['sku'];
		$sku_code = urldecode($cart_row['sku']);
		$quantity = $cart_row['quantity'];
		$options = $cart_row['options'];
		$cartItemName = $usces->getCartItemName($post_id, $sku_code);
		$skuPrice = $cart_row['price'];
		$item_custom = usces_get_item_custom( $post_id, 'notag', 'return' );
		$charging_type = $usces->getItemChargingType( $post_id, $cart_row );
		$usces_item = $usces->get_item( $post_id );
		if( empty($options) ) {
			$optstr = '';
			$options = array();
		}
		$meisai .= usces_mail_line( 2, $entry['customer']['mailaddress1'] );//--------------------
		$meisai .= $cartItemName."\r\n\r\n";
		if( is_array($options) && count($options) > 0 ) {
			foreach( $options as $key => $value ) {
				if( !empty($key) ) {
					$key = urldecode($key);
					if( is_array($value) ) {
						$c = '';
						$meisai .= $key.' : ';
						foreach( $value as $v ) {
							$meisai .= $c.urldecode($v);
							$c = ', ';
						}
						$meisai .= "\r\n";
					} else {
						$meisai .= $key.' : '.urldecode($value)."\r\n";
					}
				}
			}
		}
		$meisai .= __('Unit price','usces')." ".usces_crform( $skuPrice, true, false, 'return' ).__(' * ','usces').$cart_row['quantity']."\r\n";
		if( $item_custom )
			$meisai .= $item_custom;

		if( !empty($cart_row['advance']) ) {
			$advance = $usces->cart->wc_unserialize( $cart_row['advance'] );
			$regular = $advance[$post_id][$sku_code]['regular'];
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
			$frequency = isset( $regular['frequency'] ) ? (int)$regular['frequency'] : 0;
			$remain = isset( $regular['remain'] ) ? (int)$regular['remain'] : -1;
			$times = isset( $regular['times'] ) ? (int)$regular['times'] : -1;
			$meisai .= wcad_set_regular_str( $unit, $interval, $frequency, false, $remain, $times );
		}
	}

	$total_full_price = $data['order_item_total_price'] - $data['order_usedpoint'] + $data['order_discount'] + $data['order_shipping_charge'] + $data['order_cod_fee'] + $data['order_tax'];
	$meisai .= usces_mail_line( 3, $entry['customer']['mailaddress1'] );//====================
	$meisai .= __('total items','usces')."    : ".usces_crform( $data['order_item_total_price'], true, false, 'return' )."\r\n";

	if( $data['order_usedpoint'] != 0 )
		$meisai .= __('use of points','usces')." : ".number_format($data['order_usedpoint']).__('Points','usces')."\r\n";
	if( $data['order_discount'] != 0 )
		$meisai .= __('Special Price','usces')."    : ".usces_crform( $data['order_discount'], true, false, 'return' )."\r\n";
	$meisai .= __('Shipping','usces')."     : ".usces_crform( $data['order_shipping_charge'], true, false, 'return' )."\r\n";
	if( $payment['settlement'] == 'COD' )
		$meisai .= apply_filters('usces_filter_cod_label', __('COD fee', 'usces'))."  : ".usces_crform( $data['order_cod_fee'], true, false, 'return' )."\r\n";
	if( !empty($usces->options['tax_rate']) )
		$meisai .= __('consumption tax','usces')."    : ".usces_crform( $data['order_tax'], true, false, 'return' )."\r\n";
	$meisai .= usces_mail_line( 2, $data['order_email'] );//--------------------
	$meisai .= __('Payment amount','usces')."  : ".usces_crform( $total_full_price, true, false, 'return' )."\r\n";
	$meisai .= usces_mail_line( 2, $data['order_email'] );//--------------------
	$meisai .= "(".__('Currency', 'usces').' : '.__(usces_crcode( 'return' ), 'usces').")\r\n\r\n";

	return $meisai;
}

function wcad_filter_the_payment_method() {
	$args = func_get_args();
	$payments = $args[0];
	$regular_payments = array();

	if( wcad_have_regular_order() ) {
		foreach( (array)$payments as $payment ) {
			if( 'COD' == $payment['settlement'] ) {
				$regular_payments[] = $payment;
			} elseif( 'acting_zeus_card' == $payment['settlement'] and usces_is_login() ) {
				$regular_payments[] = $payment;
			}
		}
		ksort( $regular_payments );
	} else {
		$regular_payments = $payments;
	}
	return $regular_payments;
}

function wcad_filter_payment_detail() {
	$args = func_get_args();
	$str = $args[0];
	$usces_entries = $args[1];

	if( wcad_have_regular_order() ) {
		$str = '　['.__('Regular Purchase','autodelivery').']';
	}
	return $str;
}

function wcad_filter_delete_order_check() {
	$args = func_get_args();
	$order_id = $args[1];
	$res = true;

	return $res;
}

function wcad_filter_option_info_cart() {
	global $usces;
	$args = func_get_args();
	$cart_row = $args[1];
	$str = '';

	if( !empty($cart_row['advance']) ) {
		$advance = $usces->cart->wc_unserialize($cart_row['advance']);
		$post_id = $cart_row['post_id'];
		$sku = $cart_row['sku'];
		$regular = isset( $advance[$post_id][$sku]['regular'] ) ? $advance[$post_id][$sku]['regular'] : array();
		if( !empty($regular) ) {
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
			$frequency = isset( $regular['frequency'] ) ? (int)$regular['frequency'] : 0;
			if( !empty($unit) and 1 <= $interval )
				$str = wcad_set_regular_str( $unit, $interval, $frequency );
		}
	}
	return $str;
}

function wcad_filter_history_item_name() {
	global $usces;
	$args = func_get_args();
	$cart_row = $args[2];
	$str = '';

	if( !empty($cart_row['advance']) ) {
		$advance = $usces->cart->wc_unserialize($cart_row['advance']);
		$post_id = $cart_row['post_id'];
		$sku = $cart_row['sku'];
		$regular = isset( $advance[$post_id][$sku]['regular'] ) ? $advance[$post_id][$sku]['regular'] : array();
		if( !empty($regular) ) {
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
			$frequency = isset( $regular['frequency'] ) ? (int)$regular['frequency'] : 0;
			if( !empty($unit) and 1 <= $interval )
				$str = wcad_set_regular_str( $unit, $interval, $frequency );
		}
	}
	return $str;
}

function wcad_filter_order_edit_form_row() {
	global $usces;
	$args = func_get_args();
	$optstr = $args[0];
	$cart = $args[1];
	$materials = $args[2];

	if( !empty($materials['advance']) ) {
		$advance = $usces->cart->wc_unserialize(unserialize($materials['advance']));
		$post_id = $materials['post_id'];
		$sku = $materials['sku'];
		$regular = isset( $advance[$post_id][$sku]['regular'] ) ? $advance[$post_id][$sku]['regular'] : array();
		if( !empty($regular) ) {
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
			$frequency = isset( $regular['frequency'] ) ? (int)$regular['frequency'] : 0;
			$times = isset( $regular['times'] ) ? (int)$regular['times'] : -1;
			$remain = isset( $regular['remain'] ) ? (int)$regular['remain'] : -1;
			if( !empty($unit) and 1 <= $interval )
				$optstr .= wcad_set_regular_str( $unit, $interval, $frequency, true, $remain, $times );
		}
	}
	return $optstr;
}

function wcad_filter_member_edit_form_row() {
	global $usces;
	$args = func_get_args();
	$optstr = $args[0];
	$cart = $args[1];
	$materials = $args[2];

	if( !empty($materials['advance']) ) {
		$advance = $usces->cart->wc_unserialize(unserialize($materials['advance']));
		$post_id = $materials['post_id'];
		$sku = $materials['sku'];
		$regular = isset( $advance[$post_id][$sku]['regular'] ) ? $advance[$post_id][$sku]['regular'] : array();
		if( !empty($regular) ) {
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? $regular['interval'] : 0;
			$frequency = isset( $regular['frequency'] ) ? $regular['frequency'] : 0;
			if( !empty($unit) and 1 <= $interval )
				$optstr .= wcad_set_regular_str( $unit, $interval, $frequency );
		}
	}
	return $optstr;
}

function wcad_filter_option_info_history() {
	global $usces;
	$args = func_get_args();
	$optstr = $args[0];
	$umhs = $args[1];
	$cart_row = $args[2];
	$i = $args[3];

	if( !empty($cart_row['advance']) ) {
		$advance = $usces->cart->wc_unserialize($cart_row['advance']);
		$post_id = $cart_row['post_id'];
		$sku = urldecode($cart_row['sku']);
		$regular = isset( $advance[$post_id][$sku]['regular'] ) ? $advance[$post_id][$sku]['regular'] : array();
		if( !empty($regular) ) {
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? $regular['interval'] : 0;
			$frequency = isset( $regular['frequency'] ) ? $regular['frequency'] : 0;
			if( !empty($unit) and 1 <= $interval )
				$optstr .= wcad_set_regular_str( $unit, $interval, $frequency );
		}
	}
	return $optstr;
}

function wcad_filter_all_option_pdf() {
	global $usces;
	$args = func_get_args();
	$optstr = $args[0];
	$post_id = $args[2];
	$sku = $args[3];

	if( !empty($args[4]) ) {
		$advance = $usces->cart->wc_unserialize($args[4]);
		$regular = isset( $advance[$post_id][$sku]['regular'] ) ? $advance[$post_id][$sku]['regular'] : array();
		if( !empty($regular) ) {
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? $regular['interval'] : 0;
			$frequency = isset( $regular['frequency'] ) ? $regular['frequency'] : 0;
			if( !empty($unit) and 1 <= $interval )
				$optstr .= wcad_set_regular_str( $unit, $interval, $frequency, false );
		}
	}
	return $optstr;
}

function wcad_filter_option_info() {
	global $usces;
	$args = func_get_args();
	$info = $args[0];
	$cart_row = $args[1];

	$post_id = $cart_row['post_id'];
	$charging_type = $usces->getItemChargingType( $post_id, $cart_row );
	if( 'regular' == $charging_type ) {
		$sku = urlencode($cart_row['sku']);
		$advance = $usces->cart->wc_unserialize($cart_row['advance']);
		$regular = isset( $advance[$post_id][$sku]['regular'] ) ? $advance[$post_id][$sku]['regular'] : array();
		$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
		$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
		$frequency = isset( $regular['frequency'] ) ? (int)$regular['frequency'] : 0;
		$info = wcad_set_regular_str( $unit, $interval, $frequency );
	}
	return $info;
}

function wcad_item_save_metadata() {
	global $usces;

	$post_id = isset($_POST['post_ID']) ? $_POST['post_ID'] : -1;
	if( $post_id < 0 ) return $post_id;

	if( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) {
		return $post_id;
	} else {
		if( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
	}

	if( isset($_POST['item_division']) ) {
		$item_division = $_POST['item_division'];
		update_post_meta( $post_id, '_item_division', $item_division );
	}
	if( isset($_POST['item_charging_type']) ) {
		$item_charging_type = (int)$_POST['item_charging_type'];
		update_post_meta( $post_id, '_item_charging_type', $item_charging_type );
	}
	if( isset($_POST['regular_unit']) ) {
		$regular_unit = trim($_POST['regular_unit']);
		update_post_meta( $post_id, '_wcad_regular_unit', $regular_unit );
	}
	if( isset($_POST['regular_interval']) ) {
		$regular_interval = trim($_POST['regular_interval']);
		update_post_meta( $post_id, '_wcad_regular_interval', $regular_interval );
	}
	if( isset($_POST['regular_frequency']) ) {
		$regular_frequency = trim($_POST['regular_frequency']);
		update_post_meta( $post_id, '_wcad_regular_frequency', $regular_frequency );
	}
}

function wcad_add_stylesheet() {
	if( is_admin() ) {
		$admin_autodeliveryStyleUrl = WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/admin_auto_delivery.css';
		$admin_autodeliveryStyleFile = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_auto_delivery.css';
		if( file_exists($admin_autodeliveryStyleFile) ) {
			wp_register_style( 'adminAutodeliveryStyleSheets', $admin_autodeliveryStyleUrl );
			wp_enqueue_style( 'adminAutodeliveryStyleSheets' );
		}
	} else {
		$autodeliveryStyleUrl = WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/auto_delivery.css';
		$autodeliveryStyleFile = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/auto_delivery.css';
		if( file_exists($autodeliveryStyleFile) ) {
			wp_register_style( 'autodeliveryStyleSheets', $autodeliveryStyleUrl );
			wp_enqueue_style( 'autodeliveryStyleSheets' );
		}
	}
}

function wcad_shop_head() {
	if( file_exists(get_stylesheet_directory().'/auto_delivery.css') ) {
?>
		<link href="<?php echo get_stylesheet_directory_uri(); ?>/auto_delivery.css" rel="stylesheet" type="text/css" />
<?php
	}
}

function wcad_action_after_inCart( $serial ) {
	global $usces;

	$array = unserialize($serial);
	$ids = array_keys($array);
	$skus = array_keys($array[$ids[0]]);
	$post_id = $ids[0];
	$sku = $skus[0];

	if( !isset($_POST['advance'][$post_id][$sku]['regular']) ) {
		if( isset($_SESSION['usces_cart'][$serial]['advance']) ) {
			$advance = $usces->cart->wc_unserialize( $_SESSION['usces_cart'][$serial]['advance'] );
			$advance[$post_id][$sku]['regular'] = array();
			$_SESSION['usces_cart'][$serial]['advance'] = $usces->cart->wc_serialize( $advance );
		}
	}
}

function wcad_action_reg_orderdata( $args ) {
	global $usces, $wpdb;

	if( wcad_have_regular_order($args['cart']) ) {

		$order_table_name = $wpdb->prefix."usces_order";
		$regular_table_name = $wpdb->prefix."usces_regular";
		$regular_detail_table_name = $wpdb->prefix."usces_regular_detail";

		$data = $usces->get_order_data( $args['order_id'], 'direct' );
		$country = $usces->get_order_meta_value( 'customer_country', $args['order_id'] );

		$query = $wpdb->prepare(
			"INSERT INTO $regular_table_name (
				`reg_order_id`, `reg_mem_id`, `reg_email`, `reg_name1`, `reg_name2`, `reg_name3`, `reg_name4`, 
				`reg_country`, `reg_zip`, `reg_pref`, `reg_address1`, `reg_address2`, `reg_address3`, `reg_tel`, `reg_fax`, 
				`reg_delivery`, `reg_note`, `reg_payment_name`, `reg_condition`, `reg_cod_fee`, `reg_date`, `reg_modified`) 
			VALUES (%d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %f, %s, %s)", 
				$args['order_id'], 
				$data['mem_id'], 
				$data['order_email'], 
				$data['order_name1'], 
				$data['order_name2'], 
				$data['order_name3'], 
				$data['order_name4'], 
				$country, 
				$data['order_zip'], 
				$data['order_pref'], 
				$data['order_address1'], 
				$data['order_address2'], 
				$data['order_address3'], 
				$data['order_tel'], 
				$data['order_fax'], 
				$data['order_delivery'], 
				$data['order_note'], 
				$data['order_payment_name'], 
				$data['order_condition'], 
				$data['order_cod_fee'], 
				$data['order_date'], 
				get_date_from_gmt(gmdate('Y-m-d H:i:s', time())) 
		);

		$res = $wpdb->query( $query );
		usces_log( 'wcad_reg_regulardata : '.$wpdb->last_error, 'database_error.log' );

		if( $res === false ) {
			$regular_id = false;
		} else {
			$regular_id = $wpdb->insert_id;
		}

		if( $regular_id ) {

			$delivery = (array)unserialize($data['order_delivery']);

			foreach( (array)$args['cart'] as $cart_row ) {
				$post_id = $cart_row['post_id'];
				$charging_type = $usces->getItemChargingType( $post_id, $cart_row );
				if( 'regular' == $charging_type ) {
					$advance = $usces->cart->wc_unserialize( $cart_row['advance'] );
					$sku = urldecode( $cart_row['sku'] );
					$regular = $advance[$post_id][$sku]['regular'];
					$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
					$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
					if( empty($unit) or 1 > $interval ) //通常課金扱い
						continue;

					$frequency = isset( $regular['frequency'] ) ? (int)$regular['frequency'] : 0;
					$remain = ( 0 == $frequency ) ? 0 : $frequency - 1;
					$times = 1;
					$condition = "continuation";
					$delivery_method = wcad_get_delivery_method( $post_id, $data['order_delivery_method'] );//配送方法
					$delivery_time = ( $delivery_method == $data['order_delivery_method'] ) ? $data['order_delivery_time'] : '';
					$schedule_delivery_date = wcad_get_delivery_date( $data['order_delivery_date'], $unit, $interval );//配送希望日
					$schedule_delidue_date = wcad_get_shipment_date( $schedule_delivery_date, $delivery_method, $delivery['country'], $delivery['pref'] );//発送予定日
					$schedule_date = wcad_get_auto_order_date( $schedule_delidue_date, $post_id );//自動受注日

					$query = $wpdb->prepare(
						"INSERT INTO {$regular_detail_table_name} (
							`reg_id`, `regdet_serial`, `regdet_post_id`, `regdet_sku`, `regdet_options`, `regdet_price`, `regdet_quantity`, `regdet_advance`, 
							`regdet_unit`, `regdet_interval`, `regdet_frequency`, `regdet_remain`, `regdet_times`, 
							`regdet_schedule_date`, `regdet_schedule_delidue_date`, `regdet_schedule_delivery_date`, 
							`regdet_delivery_method`, `regdet_delivery_time`, `regdet_condition`) 
						VALUES (%d, %s, %d, %s, %s, %f, %d, %s, %s, %d, %d, %d, %d, %s, %s, %s, %d, %s, %s)", 
							$regular_id, 
							$cart_row['serial'], 
							$cart_row['post_id'], 
							$cart_row['sku'], 
							serialize($cart_row['options']), 
							$cart_row['price'], 
							$cart_row['quantity'], 
							$cart_row['advance'], 
							$unit, 
							$interval, 
							$frequency, 
							$remain, 
							$times, 
							$schedule_date, 
							$schedule_delidue_date, 
							$schedule_delivery_date, 
							$delivery_method, 
							$delivery_time, 
							$condition 
					);
					$res = $wpdb->query( $query );
				}
			}
		}
	}
}

function wcad_action_del_orderdata( $obj ) {
	global $usces;
	if( !$obj ) return;

}

function wcad_action_update_orderdata( $obj ) {
	global $usces;
	if( !$obj ) return;
	$order_data = $usces->get_order_data( $obj->ID );

}

function wcad_action_item_dupricate( $post_id, $newpost_id ) {
	if( $item_division = get_post_meta($post_id, '_item_division', true) ) {
		update_post_meta( $newpost_id, '_item_division', $item_division );
	}
	if( $item_charging_type = get_post_meta($post_id, '_item_charging_type', true) ) {
		update_post_meta( $newpost_id, '_item_charging_type', $item_charging_type );
	}
	if( $regular_unit = get_post_meta($post_id, '_wcad_regular_unit', true) ) {
		update_post_meta( $newpost_id, '_wcad_regular_unit', $regular_unit );
	}
	if( $regular_interval = get_post_meta($post_id, '_wcad_regular_interval', true) ) {
		update_post_meta( $newpost_id, '_wcad_regular_interval', $regular_interval );
	}
	if( $regular_frequency = get_post_meta($post_id, '_wcad_regular_frequency', true) ) {
		update_post_meta( $newpost_id, '_wcad_regular_frequency', $regular_frequency );
	}
}

function wcad_del_order_meta( $key, $order_id ) {
	global $wpdb;
	$ordermeta_table = $wpdb->prefix."usces_order_meta";
	$query = $wpdb->prepare( "DELETE FROM $ordermeta_table WHERE meta_key = %s AND order_id = %d", $key, $order_id );
	$res = $wpdb->query( $query );
}

function wcad_start_charging() {
	$wcad_options = get_option('wcad_options');
	$start_charge = ( 'on' == $wcad_options['start_charge'] ) ? true : false;
	return $start_charge;
}

function wcad_auto_stop() {
	$wcad_options = get_option('wcad_options');
	$auto_stop = ( 'on' == $wcad_options['auto_stop'] ) ? true : false;
	return $auto_stop;
}

function wcad_auto_order() {
	$wcad_options = get_option('wcad_options');
	$auto_order = ( 'on' == $wcad_options['auto_order'] ) ? true : false;
	return $auto_order;
}

function wcad_create_table() {
	global $wpdb;

	if( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if( !empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";

	$regular_table = $wpdb->prefix."usces_regular";
	$regular_detail_table = $wpdb->prefix."usces_regular_detail";

	require_once( ABSPATH.'wp-admin/includes/upgrade.php' );

	if( $wpdb->get_var( "show tables like '$regular_table'" ) != $regular_table ) {

		$sql = "CREATE TABLE `".$regular_table."` (
			`reg_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`reg_order_id` bigint(20) unsigned NOT NULL,
			`reg_mem_id` bigint(20) unsigned DEFAULT NULL,
			`reg_email` varchar(100) NOT NULL,
			`reg_name1` varchar(100) NOT NULL,
			`reg_name2` varchar(100) DEFAULT NULL,
			`reg_name3` varchar(100) DEFAULT NULL,
			`reg_name4` varchar(100) DEFAULT NULL,
			`reg_country` varchar(50) DEFAULT NULL,
			`reg_zip` varchar(50) DEFAULT NULL,
			`reg_pref` varchar(100) NOT NULL,
			`reg_address1` varchar(100) NOT NULL,
			`reg_address2` varchar(100) DEFAULT NULL,
			`reg_address3` varchar(100) DEFAULT NULL,
			`reg_tel` varchar(100) NOT NULL,
			`reg_fax` varchar(100) DEFAULT NULL,
			`reg_delivery` longtext,
			`reg_note` text,
			`reg_payment_name` varchar(100) NOT NULL,
			`reg_condition` text,
			`reg_cod_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
			`reg_date` date NOT NULL DEFAULT '0000-00-00',
			`reg_modified` varchar(20) DEFAULT NULL,
			`reg_status` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`reg_id`),
			KEY `reg_date` (`reg_date`)
		) ENGINE = MyISAM AUTO_INCREMENT=0 $charset_collate;";

		dbDelta( $sql );
		add_option( "usces_db_regular", WCEX_AUTO_DELIVERY_DB_REGULAR );
	}

	if( $wpdb->get_var( "show tables like '$regular_detail_table'" ) != $regular_detail_table ) {

		$sql = "CREATE TABLE `".$regular_detail_table."` (
			`regdet_id` bigint(20) NOT NULL auto_increment,
			`reg_id` bigint(20) NOT NULL default '0',
			`regdet_serial` text NOT NULL,
			`regdet_post_id` bigint(20) NOT NULL,
			`regdet_sku` varchar(255) NOT NULL,
			`regdet_options` longtext,
			`regdet_price` decimal(10,2) NOT NULL,
			`regdet_quantity` int(10) NOT NULL,
			`regdet_advance` longtext,
			`regdet_unit` varchar(10) NOT NULL,
			`regdet_interval` int(10) NOT NULL,
			`regdet_frequency` int(10) NOT NULL,
			`regdet_remain` int(10) DEFAULT '0',
			`regdet_times` int(10) DEFAULT '0',
			`regdet_schedule_date` varchar(20) DEFAULT NULL,
			`regdet_schedule_delidue_date` varchar(20) DEFAULT NULL,
			`regdet_schedule_delivery_date` varchar(20) DEFAULT NULL,
			`regdet_delivery_method` int(10) DEFAULT '-1',
			`regdet_delivery_time` varchar(100) DEFAULT NULL,
			`regdet_discount` decimal(10,2) DEFAULT '0.00',
			`regdet_condition` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`regdet_id`),
			KEY `reg_id` (`reg_id`),
			KEY `regdet_schedule_date` (`regdet_schedule_date`)
		) ENGINE = MYISAM $charset_collate;";

		dbDelta( $sql );
		add_option( "usces_db_regular_detail", WCEX_AUTO_DELIVERY_DB_REGULAR_DETAIL );
	}
}

function wcad_pre_reg_orderdata() {
	global $usces, $wcad_options;
	$cart = $usces->cart->get_cart();

	if( isset($wcad_options['date_calculation']) and $wcad_options['date_calculation'] == 'off' ) {
		if( !wcad_have_regular_order($cart) ) return;
	}

	$usces_entries = $usces->cart->get_entry();

	$selected_delivery_method = $usces_entries['order']['delivery_method'];
	$selected_delivery_date = $usces_entries['order']['delivery_date'];

	//配達日数に設定されている県毎の日数
	$delivery = 0;
	$delivery_method_index = $usces->get_delivery_method_index((int)$selected_delivery_method);
	$days = ( isset($usces->options['delivery_method'][$delivery_method_index]['days']) ) ? (int)$usces->options['delivery_method'][$delivery_method_index]['days'] : -1;
	if( 0 <= $days ) {
		$delivery_days = $usces->options['delivery_days'];
		foreach( (array)$delivery_days as $delivery_days_value ) {
			if( (int)$delivery_days_value['id'] == $days ) {
				$delivery = (int)$delivery_days_value[$usces_entries['delivery']['country']][$usces_entries['delivery']['pref']];
			}
		}
	}

	//カートに入っている商品の発送日目安
	$shipping = 0;
	$shipping_indication = apply_filters( 'usces_filter_shipping_indication', $usces->options['usces_shipping_indication'] );
	foreach( $cart as $cart_row ) {
		$itemShipping = $usces->getItemShipping( $cart_row['post_id'] );
		if( $itemShipping == 0 or $itemShipping == 9 ) $itemShipping = 0;
		if( $shipping < $itemShipping ) $shipping = $itemShipping;
	}
	$indication_incart = isset( $shipping_indication[$shipping] ) ? $shipping_indication[$shipping] : false;

	//配送希望日(到着予定日)
	if( wcad_isdate($selected_delivery_date) ) {
		$delivery_date = $selected_delivery_date;

	} else {

		list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = split( '([^0-9])', current_time('mysql') );//today

		$bus_day_arr = ( isset($usces->options['business_days']) ) ? $usces->options['business_days'] : false;
		if( !is_array($bus_day_arr) ) {
			$today_bus_flag = 1;
		} else {
			$today_bus_flag = isset( $bus_day_arr[(int)$today_year][(int)$today_month][(int)$today_day] ) ? (int)$bus_day_arr[(int)$today_year][(int)$today_month][(int)$today_day] : 1;
		}

		$limit_hour = ( !empty($usces->options['delivery_time_limit']['hour']) ) ? $usces->options['delivery_time_limit']['hour'] : false;
		$limit_min = ( !empty($usces->options['delivery_time_limit']['min']) ) ? $usces->options['delivery_time_limit']['min'] : false;

		if( false === $hour || false === $minute ) {
			$time_limit_addition = false;
		} elseif( ($hour.':'.$minute.':'.$second) > ($limit_hour.':'.$limit_min.':00') ) {
			$time_limit_addition = 1;
		} else {
			$time_limit_addition = 0;
		}

		$sendout_num = 0;
		if( $today_bus_flag ) {
			if( $time_limit_addition ) {
				$sendout_num += 1;
			}
		}
		if( false !== $indication_incart ) {
			$sendout_num += $indication_incart;
		}

		//営業日
		for( $i = 0; $i <= $sendout_num; $i++ ) {
			list( $yyyy, $mm, $dd ) = explode( '-', date( 'Y-m-d', mktime(0, 0, 0, (int)$today_month, (int)$today_day + $i, (int)$today_year) ) );
			if( isset($bus_day_arr[(int)$yyyy][(int)$mm][(int)$dd]) && !$bus_day_arr[(int)$yyyy][(int)$mm][(int)$dd] ) {
				$sendout_num++;
			}
			if( 100 < $sendout_num ) break;
		}

		$delivery_date = date( 'Y-m-d', mktime( 0, 0, 0, (int)$today_month, (int)$today_day + $sendout_num, (int)$today_year ) );

		//配達日数
		if( 0 < $delivery ) {
			list( $year, $month, $day ) = explode( '-', $delivery_date );
			$delivery_date = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day + $delivery, (int)$year) );
		}

		$_SESSION['usces_entry']['order']['delivery_date'] = $delivery_date;
	}

	//発送予定日
	if( isset($_SESSION['usces_entry']['order']['delidue_date']) and wcad_isdate($_SESSION['usces_entry']['order']['delidue_date']) ) {
	} else {

		$delidue_date = $delivery_date;

		//配達日数
		if( 0 < $delivery ) {
			list( $year, $month, $day ) = explode( '-', $delidue_date );
			$delidue_date = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day - $delivery, (int)$year) );
		}

		//営業日
		list( $year, $month, $day ) = explode( '-', $delidue_date );
		$business = ( isset($usces->options['business_days'][(int)$year][(int)$month][(int)$day]) ) ? $usces->options['business_days'][(int)$year][(int)$month][(int)$day] : 1;
		while( $business != 1 ) {
			$delidue_date = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day - 1, (int)$year) );
			list( $year, $month, $day ) = explode( '-', $delidue_date );
			$business = $usces->options['business_days'][(int)$year][(int)$month][(int)$day];
		}

		$_SESSION['usces_entry']['order']['delidue_date'] = $delidue_date;
	}
}

/* order list customized */
function wcad_filter_order_list_sql_select() {
	$management_status = array(
		'duringorder' => __('temporaly out of stock', 'usces'),
		'cancel' => __('Cancel', 'usces'),
		'completion' => __('It has sent it out.', 'usces'),
		'estimate' => __('An estimate', 'usces'),
		'adminorder' => __('Management of Note', 'usces'),
		'continuation' => __('Continuation', 'usces'),
		'termination' => __('Termination', 'usces')
	);
	$status_sql = '';
	foreach( $management_status as $status_key => $status_name ) {
		$status_sql .= " WHEN LOCATE('".$status_key."', order_status) > 0 THEN '".$status_name."'";
	}

	$select = array(
		"ID",
		"meta1.meta_value AS deco_id",
		"IFNULL(reg_id, meta2.meta_value) AS reg_id",
		"DATE_FORMAT(order_date, '%Y-%m-%d %H:%i') AS date",
		"mem_id",
		"CONCAT(order_name1, ' ', order_name2) AS name",
		"order_pref AS pref",
		"order_delivery_method AS delivery_method",
		"(order_item_total_price - order_usedpoint + order_discount + order_shipping_charge + order_cod_fee + order_tax) AS total_price",
		"order_payment_name AS payment_name",
		"CASE WHEN LOCATE('noreceipt', order_status) > 0 THEN '".__('unpaid', 'usces')."' 
			 WHEN LOCATE('receipted', order_status) > 0 THEN '".__('payment confirmed', 'usces')."' 
			 WHEN LOCATE('pending', order_status) > 0 THEN '".__('Pending', 'usces')."' 
			 ELSE '&nbsp;' 
		END AS receipt_status",
		"CASE {$status_sql} 
			 ELSE '".__('new order', 'usces')."' 
		END AS order_status",
		"order_modified",
		"IFNULL(reg_id, '') AS reg_parent_id"
	);
	return $select;
}

function wcad_filter_order_list_sql_jointable() {
	global $wpdb;
	$args = func_get_args();
	//$jointable = $args[0];
	$meta_table = $wpdb->prefix.'usces_order_meta';
	$regular_table = $wpdb->prefix.'usces_regular';
	$jointable = array(
		"LEFT JOIN {$meta_table} AS meta1 ON ID = meta1.order_id AND meta1.meta_key = 'dec_order_id'",
		"LEFT JOIN {$meta_table} AS meta2 ON ID = meta2.order_id AND meta2.meta_key = 'regular_id'",
		"LEFT JOIN {$regular_table} ON ID = reg_order_id"
	);
	return $jointable;
}

function wcad_filter_order_list_column() {
	$arr_column = array(
		__('ID', 'usces') => 'ID',
		__('Order number', 'usces') => 'deco_id',
		__('Regular ID', 'autodelivery') => 'reg_id',
		__('date', 'usces') => 'date',
		__('membership number', 'usces') => 'mem_id',
		__('name', 'usces') => 'name',
		__('Region', 'usces') => 'pref',
		__('shipping option', 'usces') => 'delivery_method',
		__('Amount', 'usces').'('.__(usces_crcode( 'return' ), 'usces').')' => 'total_price',
		__('payment method', 'usces') => 'payment_name',
		__('transfer statement', 'usces') => 'receipt_status',
		__('Processing', 'usces') => 'order_status',
		__('shpping date', 'usces') => 'order_modified'
	);
	return $arr_column;
}

function wcad_filter_order_list_header() {
	$args = func_get_args();
	$list_header = $args[0];
	$header = $args[1];

	$html = '<th scope="col"><input name="allcheck" type="checkbox" value="" /></th>';
	foreach( (array)$header as $value ) {
		$html .= '<th scope="col">'.$value.'</th>';
	}
	$html .= '<th scope="col">&nbsp;</th>';
	return $html;
}

function wcad_filter_order_list_detail_trclass() {
	$args = func_get_args();
	$detail = $args[1];

	$trclass = ( !empty($detail['reg_parent_id']) ) ? ' class="regular_parent_order"' : '';
	return $trclass;
}

function wcad_filter_order_list_detail() {
	global $usces, $wpdb;
	$args = func_get_args();
	$html = $args[0];
	$detail = $args[1];
	$curent_url = $args[2];
	$options = get_option('usces');
	$applyform = usces_get_apply_addressform($options['system']['addressform']);

	$list_detail = '<td align="center"><input name="listcheck[]" type="checkbox" value="'.$detail['ID'].'" /></td>';
	foreach( (array)$detail as $key => $value ) {
		if( $value == '' || $value == ' ' ) $value = '&nbsp;';
		if( $key == 'ID' || $key == 'deco_id' ) {
			$list_detail .= '<td><a href="'.USCES_ADMIN_URL.'?page=usces_orderlist&order_action=edit&order_id='.$detail['ID'].'&usces_referer='.$curent_url.'">'.esc_html($value).'</a></td>';
		} elseif( $key == 'reg_id' ) {
			if( $value == '&nbsp;' or $value == '-' ) {
				$list_detail .= '<td>'.esc_html($value).'</td>';
			} else {
				$list_detail .= '<td><a href="'.USCES_ADMIN_URL.'?page=usces_regularlist&regular_action=edit&regular_id='.$value.'&usces_referer='.$curent_url.'">'.esc_html($value).'</a></td>';
			}
		} elseif( $key == 'mem_id' ) {
			if( $value == '0' ) $value = '&nbsp;';
			$list_detail .= '<td>'.esc_html($value).'</td>';
		} elseif( $key == 'name' ) {
			switch( $applyform ) {
			case 'JP': 
				$list_detail .= '<td>'.esc_html($value).'</td>';
				break;
			case 'US':
			default:
				$names = explode(' ', $value);
				$list_detail .= '<td>'.esc_html($names[1].' '.$names[0]).'</td>';
			}
		} elseif( $key == 'delivery_method' ) {
			if( -1 != $value ) {
				$delivery_method_index = $usces->get_delivery_method_index($value);
				$value = $options['delivery_method'][$delivery_method_index]['name'];
			} else {
				$value = '&nbsp;';
			}
			$list_detail .= '<td class="green">'.esc_html($value).'</td>';
		} elseif( $key == 'total_price' ) {
			$list_detail .= '<td class="price">'.usces_crform( $value, true, false, 'return' ).'</td>';
		} elseif( $key == 'payment_name' ) {
			if( $value == '#none#' ) {
				$list_detail .= '<td>&nbsp;</td>';
			} else {
				$list_detail .= '<td>'.esc_html($value).'</td>';
			}
		} elseif( $key == 'receipt_status' ) {
			if( $value == __('unpaid', 'usces') ) {
				$list_detail .= '<td class="red">'.esc_html($value).'</td>';
			} elseif( $value == 'Pending' ) {
				$list_detail .= '<td class="red">'.esc_html($value).'</td>';
			} elseif( $value == __('payment confirmed', 'usces') ) {
				$list_detail .= '<td class="green">'.esc_html($value).'</td>';
			} else {
				$list_detail .= '<td>'.esc_html($value).'</td>';
			}
		} elseif( $key == 'order_status' ) {
			if( $value == __('It has sent it out.', 'usces') ) {
				$list_detail .= '<td class="green">'.esc_html($value).'</td>';
			} else {
				$list_detail .= '<td>'.esc_html($value).'</td>';
			}
		} elseif( $key == 'date' || $key == 'pref' || $key == 'order_modified' ) {
			$list_detail .= '<td>'.esc_html($value).'</td>';
		}
	}
	$list_detail .= '<td><a href="'.USCES_ADMIN_URL.'?page=usces_orderlist&order_action=delete&order_id='.$detail['ID'].'" onclick="return deleteconfirm(\''.$detail['ID'].'\');"><span style="color:#FF0000; font-size:9px;">'.__('Delete', 'usces').'</span></a></td>';
	return $list_detail;
}

function wcad_filter_sku_meta_form_advance_title() {
	//$args = func_get_args();
	$title = '<th>'.__('regular purchase price','autodelivery').'('.__(usces_crcode('return'),'usces').')</th><th>&nbsp;</th>';
	return $title;
}

function wcad_filter_sku_meta_form_advance_field() {
	//$args = func_get_args();
	$field = '<td class="item-sku-cprice"><input type="text" id="newskuadvance" name="newskuadvance" class="newskuprice metaboxfield" /></td><td class="item-sku-zaikonum">&nbsp;</td>';
	return $field;
}

function wcad_filter_sku_meta_row_advance() {
	$args = func_get_args();
	$sku = $args[1];
	$advance = ( isset($sku['advance']) ) ? $sku['advance'] : '';
	if( empty($advance) ) {
		$rprice = '';
	} else {
		$advance = unserialize( $advance );
		$rprice = ( isset($advance['rprice']) ) ? (int)$advance['rprice'] : '';
	}
	$id = (int)$sku['meta_id'];

	$field  = "<td class='item-sku-cprice'><input name='itemsku[".$id."][skuadvance]' id='itemsku[".$id."][skuadvance]' class='skuprice metaboxfield' type='text' value='".$rprice."' /></td>";
	$field .= "<td>&nbsp;</td>";
	return $field;
}

function wcad_filter_add_item_sku_meta_value() {
	$args = func_get_args();
	$value = $args[0];
	$newskurprice = ( isset($_POST['newskuadvance']) ) ? array( 'rprice' => trim( $_POST['newskuadvance'] ) ) : array();
	$value['advance'] = serialize( $newskurprice );
	return $value;
}

function wcad_filter_up_item_sku_meta_value() {
	$args = func_get_args();
	$value = $args[0];
	$skurprice = ( isset($_POST['skuadvance']) ) ? array( 'rprice' => trim( $_POST['skuadvance'] ) ) : array();
	$value['advance'] = serialize( $skurprice );
	return $value;
}

function wcad_filter_item_save_sku_metadata() {
	$args = func_get_args();
	$value = $args[0];
	$meta_id = $args[1];
	$skurprice = ( isset($_POST['itemsku'][$meta_id]['skuadvance']) ) ? array( 'rprice' => trim( $_POST['itemsku'][$meta_id]['skuadvance'] ) ) : array();
	$value['advance'] = serialize( $skurprice );
	return $value;
}

function wcad_filter_inCart_price() {
	global $usces;
	$args = func_get_args();
	$price = $args[0];
	$serial = $args[1];

	if( isset($_POST['advance']) ) {
		$ids = array_keys($_POST['inCart']);
		$post_id = $ids[0];
		$skus = array_keys($_POST['inCart'][$post_id]);
		$sku = $skus[0];
		$regular = isset( $_POST['advance'][$post_id][$sku]['regular'] ) ? $_POST['advance'][$post_id][$sku]['regular'] : array();
		if( !empty($regular) ) {
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
			if( !empty($unit) and 1 <= $interval ) {
				$rprice = wcad_get_skurprice( $post_id, $sku );
				if( 0 < $rprice ) $price = $rprice;
			}
		}
	}
	return $price;
}

function wcad_filter_upCart_price() {
	global $usces;
	$args = func_get_args();
	$price = $args[0];
	$serial = $args[1];
	$index = $args[2];

	if( isset($_POST['advance'][$index]) ) {
		$array = unserialize($serial);
		$ids = array_keys($array);
		$skus = array_keys($array[$ids[0]]);
		$post_id = $ids[0];
		$sku = $skus[0];
		$advance = $usces->cart->wc_unserialize($usces->cart->wc_unserialize($_POST['advance'][$index][$post_id][$sku]));
		$regular = isset( $advance[$post_id][$sku]['regular'] ) ? $advance[$post_id][$sku]['regular'] : array();
		if( !empty($regular) ) {
			$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
			$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
			if( !empty($unit) and 1 <= $interval ) {
				$rprice = wcad_get_skurprice( $post_id, $sku );
				if( 0 < $rprice ) $price = $rprice;
			}
		}
	}
	return $price;
}

function wcad_filter_settle_info_field_keys() {
	$args = func_get_args();
	$keys = $args[0];
	array_push( $keys, "settltment_status", "settltment_errmsg" );
	return $keys;
}

function wcad_admin_notices() {
	global $usces, $wcad_options;

	if( 'off' == $wcad_options['acting_payment'] )
		return;

	$msg = '';
	$option_value = usces_get_system_option( 'usces_payment_method', 'sort' );
	$p_flag = false;
	foreach( $option_value as $payments ) {
		if( 'acting_zeus_card' == $payments['settlement'] ) {
			$p_flag = true;
			break;
		}
	}
	$opts = $usces->options['acting_settings'];
	$batch = isset($opts['zeus']['batch']) ? $opts['zeus']['batch'] : 'off';

	if( !$p_flag || 'off' == $batch ) {
		$msg = '<div class="error">';

		if( !$p_flag ) {
			$msg .= '<p>支払方法に「カード決済（ゼウス）」が登録されていません。</p>';
		}

		if( 'off' == $batch ) {
			$msg .= '<p>クレジット決済にて、ゼウスのバッチ処理を「利用する」に設定してください。</p>';
		}

		$msg .= '</div>';
	}

	echo $msg;
}

function wcad_action_order_list_document_ready_js() {
	$js = '
	$(".regular_parent_order").removeClass("rowSelection_even");
	$(".regular_parent_order").css( "background-color","#e6fe9e" );
	';
	echo $js;
}

function wcad_action_memberinfo_page_header() {
	global $usces;

	$html = '';
	$usces_members = $usces->get_member();
	if( wcad_have_member_regular_order( $usces_members['ID'] ) ) {
		$html = '
		<div class="gotoedit">
		<a href="'.USCES_MEMBER_URL.$usces->delim.'page=autodelivery_history">'. __('Regular purchase information', 'autodelivery').'はこちら&gt;&gt;</a>
		</div>';
	}
	echo $html;
}

function wcad_filter_template_redirect() {
	global $usces;

	if( $usces->is_member_page($_SERVER['REQUEST_URI']) ) {
		if( $usces->options['membersystem_state'] != 'activate' ) return;

		if( $usces->is_member_logged_in() and ( isset($_REQUEST['page']) and 'autodelivery_history' == $_REQUEST['page'] ) ) {
			if( file_exists(get_stylesheet_directory().'/wc_templates/member/wc_autodelivery_history_page.php') ) {
				$usces->page = 'autodelivery_history';
				include(get_stylesheet_directory().'/wc_templates/member/wc_autodelivery_history_page.php');
				exit;
			}
		}
	}
	return;
}

function wcad_action_edit_memberdata() {
	global $usces, $wpdb;
	$args = func_get_args();
	$member = $args[0];
	$member_id = $args[1];

	$regular_table_name = $wpdb->prefix."usces_regular";
	$query = $wpdb->prepare( "SELECT reg_id FROM {$regular_table_name} WHERE reg_mem_id = %d", $member_id );
	$rows = $wpdb->get_results( $query, ARRAY_A );
	foreach( (array)$rows as $row ) {
		$query = $wpdb->prepare(
			"UPDATE {$regular_table_name} SET 
				reg_email = %s, reg_name1 = %s, reg_name2 = %s, reg_name3 = %s, reg_name4 = %s, 
				reg_country = %s, reg_zip = %s, reg_pref = %s, reg_address1 = %s, reg_address2 = %s, reg_address3 = %s, 
				reg_tel = %s, reg_fax = %s 
			WHERE reg_id = %d", 
				trim($_POST['member']['mailaddress1']), 
				trim($_POST['member']['name1']), 
				trim($_POST['member']['name2']), 
				trim($_POST['member']['name3']), 
				trim($_POST['member']['name4']), 
				trim($_POST['member']['country']), 
				trim($_POST['member']['zipcode']), 
				trim($_POST['member']['pref']), 
				trim($_POST['member']['address1']), 
				trim($_POST['member']['address2']), 
				trim($_POST['member']['address3']), 
				trim($_POST['member']['tel']), 
				trim($_POST['member']['fax']), 
			$row['reg_id']
		);
		$res = $wpdb->query( $query );
	}
}

function wcad_action_post_update_memberdata() {
	global $usces, $wpdb;
	$args = func_get_args();
	$member_id = $args[0];
	$res = $args[1];

	if( $res ) {
		$regular_table_name = $wpdb->prefix."usces_regular";
		$query = $wpdb->prepare( "SELECT reg_id FROM {$regular_table_name} WHERE reg_mem_id = %d", $member_id );
		$rows = $wpdb->get_results( $query, ARRAY_A );
		foreach( (array)$rows as $row ) {
			$query = $wpdb->prepare(
				"UPDATE {$regular_table_name} SET 
					reg_email = %s, reg_name1 = %s, reg_name2 = %s, reg_name3 = %s, reg_name4 = %s, 
					reg_country = %s, reg_zip = %s, reg_pref = %s, reg_address1 = %s, reg_address2 = %s, reg_address3 = %s, 
					reg_tel = %s, reg_fax = %s 
				WHERE reg_id = %d", 
					trim($_POST['member']['email']), 
					trim($_POST['member']['name1']), 
					trim($_POST['member']['name2']), 
					trim($_POST['member']['name3']), 
					trim($_POST['member']['name4']), 
					trim($_POST['member']['country']), 
					trim($_POST['member']['zipcode']), 
					trim($_POST['member']['pref']), 
					trim($_POST['member']['address1']), 
					trim($_POST['member']['address2']), 
					trim($_POST['member']['address3']), 
					trim($_POST['member']['tel']), 
					trim($_POST['member']['fax']), 
				$row['reg_id']
			);
			$res = $wpdb->query( $query );
		}
	}
}

?>
