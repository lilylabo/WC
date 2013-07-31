<?php
$status = $usces->action_status;
$message = $usces->action_message;
$usces->action_status = 'none';
$usces->action_message = '';

global $wpdb, $usces_settings;

$regular_action = 'editpost';

$regular_id = $_REQUEST['regular_id'];

$regular_table_name = $wpdb->prefix."usces_regular";
$regular_detail_table_name = $wpdb->prefix."usces_regular_detail";
$order_table_name = $wpdb->prefix."usces_order";
$order_meta_table_name = $wpdb->prefix."usces_order_meta";

//Regular Data
$query = $wpdb->prepare( "SELECT 
	r.reg_id, r.reg_order_id, r.reg_mem_id, r.reg_email, 
	r.reg_name1 AS name1, r.reg_name2 AS name2, r.reg_name3 AS name3, r.reg_name4 AS name4, 
	r.reg_country AS country, r.reg_zip AS zipcode, r.reg_pref AS pref, 
	r.reg_address1 AS address1, r.reg_address2 AS address2, r.reg_address3 AS address3, 
	r.reg_tel AS tel, r.reg_fax AS fax, r.reg_delivery, r.reg_note, r.reg_payment_name, r.reg_date, 
	meta.meta_value AS order_id 
	FROM {$regular_table_name} AS r 
	LEFT JOIN {$order_meta_table_name} AS meta ON r.reg_order_id = meta.order_id AND meta.meta_key = 'dec_order_id' 
	WHERE reg_id = %d", 
	$regular_id
);
$regular_order = $wpdb->get_row( $query, ARRAY_A );
$delivery = (array)unserialize( $regular_order['reg_delivery'] );
//Regular Detail Data
$query = $wpdb->prepare( "SELECT * FROM {$regular_detail_table_name} WHERE reg_id = %d ORDER BY regdet_id", $regular_id );
$regular_detail = $wpdb->get_results( $query, ARRAY_A );
//Regular History Data
$query = $wpdb->prepare("
	SELECT ID, meta.meta_value AS deco_id, DATE_FORMAT(order_date, %s) AS date, DATE_FORMAT(order_delidue_date, %s) AS delidue_date, DATE_FORMAT(order_delivery_date, %s) AS delivery_date 
		FROM {$order_table_name} 
		LEFT JOIN {$order_meta_table_name} AS meta ON ID = meta.order_id AND meta.meta_key = 'dec_order_id' 
		LEFT JOIN {$regular_table_name} ON ID = reg_order_id 
		WHERE reg_id = %d 
	UNION ALL 
	SELECT ID, meta1.meta_value AS deco_id, DATE_FORMAT(order_date, %s) AS date, DATE_FORMAT(order_delidue_date, %s) AS delidue_date, DATE_FORMAT(order_delivery_date, %s) AS delivery_date 
		FROM {$order_table_name} 
		LEFT JOIN {$order_meta_table_name} AS meta1 ON ID = meta1.order_id AND meta1.meta_key = 'dec_order_id' 
		LEFT JOIN {$order_meta_table_name} AS meta2 ON ID = meta2.order_id AND meta2.meta_key = 'regular_id' 
		WHERE meta2.meta_value = %d 
	ORDER BY ID DESC, date DESC",
	'%Y-%m-%d', '%Y-%m-%d', '%Y-%m-%d', $regular_id, '%Y-%m-%d', '%Y-%m-%d', '%Y-%m-%d', $regular_id
);
$regular_history = $wpdb->get_results( $query, ARRAY_A );
$applyform = usces_get_apply_addressform( $usces->options['system']['addressform'] );
$curent_url = urlencode(USCES_ADMIN_URL.'?'.$_SERVER['QUERY_STRING']);
$arr_condition = apply_filters( 'wcad_filter_regular_condition', array( "continuation", "stop" ) );
$shipping_indication = apply_filters( 'usces_filter_shipping_indication', $usces->options['usces_shipping_indication'] );
$bus_day_arr = ( isset($usces->options['business_days']) ) ? $usces->options['business_days'] : false;
?>
<script type="text/javascript">
jQuery(function($) {
<?php if( $status == 'success' ) { ?>
	$("#anibox").animate({ backgroundColor: "#ECFFFF" }, 2000);
<?php } else if( $status == 'caution' ) { ?>
	$("#anibox").animate({ backgroundColor: "#FFF5CE" }, 2000);
<?php } else if( $status == 'error' ) { ?>
	$("#anibox").animate({ backgroundColor: "#FFE6E6" }, 2000);
<?php } ?>

	regularFunc = {
		getScheduledDate: function( delivery_date, i ) {
			var post_id = $('#regdet_post_id_'+i).val();
			var delivery_method = $('#regdet_delivery_method_'+i).val();
			var s = regularFunc.settings;
			s.data = "action=usces_admin_ajax&mode=wcad_get_scheduled_date&post_id="+post_id+"&delivery_date="+delivery_date+"&delivery_method="+delivery_method+"&country="+$('#regular_country').val()+"&pref="+$('#regular_pref').val();
			s.success = function( data, dataType ) {
				var values = data.split( '#usces#' );
				if( 'ok' == values[0] ) {
					$('#schedule_date_'+i).html( values[1] );
					$('#schedule_delidue_date_'+i).html( values[2] );
					$("input[name='schedule_date_"+i+"']").val( values[1] );
					$("input[name='schedule_delidue_date_"+i+"']").val( values[2] );
				}
			};
			s.error = function( data, dataType ) {
				alert( 'ERROR' );
			};
			$.ajax( s );
			return false;
		},
		settings: {
			url: uscesL10n.requestFile,
			type: 'POST',
			cache: false,
			success: function( data, dataType ) {
			},
			error: function( msg ) {
				//$("#ajax-response").html( msg );
			}
		}
	};
});
</script>
<div class="wrap">
<div class="usces_admin">
<form action="<?php echo USCES_ADMIN_URL.'?page=usces_regularlist&regular_action='.$regular_action; ?>" method="post" name="editpost">

<h2>Welcart Management <?php _e('Edit Regular Purchase Data', 'autodelivery'); ?></h2>
<p class="version_info">Version <?php echo WCEX_AUTO_DELIVERY_VERSION; ?></p>
<div id="aniboxStatus" class="<?php echo $status; ?>">
	<div id="anibox" class="clearfix">
		<img id="info_image" src="<?php echo USCES_PLUGIN_URL; ?>/images/list_message_<?php echo $status; ?>.gif" />
		<div class="mes" id="info_massage"><?php echo $message; ?></div>
	</div>
</div>

<div class="ordernavi"><input name="upButton" class="upButton" type="submit" value="<?php _e('change decision', 'usces'); ?>" /><?php _e("When you change amount, please click 'Edit' before you finish your process.", 'usces'); ?></div>

<div class="error_message"><?php echo $usces->error_message; ?></div>

<!-- regular info -->
<div id="ad_r_info" class="clearfix">
	<h3><span><?php _e('Order information', 'autodelivery'); ?></span></h3>

	<div id="ad_r_info_1" class="infobox">
	<div class="inside">
	<table class="mem_info mb10">
		<tr><th class="label"><?php _e('Regular ID', 'autodelivery'); ?></th><td class="col1"><?php esc_attr_e( $regular_order['reg_id'] ); ?></td></tr>
		<tr><th class="label"><?php _e('order number', 'usces'); ?></th><td class="col1"><?php esc_attr_e( $regular_order['order_id'] ); ?></td></tr>
		<tr><th class="label"><?php _e('Order date', 'autodelivery'); ?></th><td class="col1"><?php esc_attr_e( $regular_order['reg_date'] ); ?></td></tr>
		<tr><th class="label"><?php _e('membership number', 'usces'); ?></th><td class="col1"><?php esc_attr_e( empty($regular_order['reg_mem_id']) ? '&nbsp;' : $regular_order['reg_mem_id'] ); ?></td></tr>
		<tr><th class="label"><?php _e('payment method', 'usces'); ?></th><td class="col1"><?php esc_attr_e( $regular_order['reg_payment_name'] ); ?></td></tr>
	</table>
	</div>
	<div class="inside">
	<table class="mem_info">
		<tr><th class="label"><?php _e('Notes', 'usces'); ?></th><td class="col1"><textarea name="regular[note]"><?php esc_attr_e( isset($regular_order['reg_note']) ? $regular_order['reg_note'] : '' ); ?></textarea></td></tr>
	</table>
	</div>
	</div>

	<div id="ad_r_info_2" class="infobox">
	<div class="inside">
	<table class="mem_info">
		<tr>
			<th class="label">e-mail</th>
			<td class="col1"><input name="regular[email]" type="text" class="text long" value="<?php esc_attr_e( $regular_order['reg_email'] ); ?>" /></td>
		</tr>
		<?php wcad_get_admin_addressform( 'regular', $regular_order ); ?>
	</table>
	</div>
	</div>

	<div id="ad_r_info_3" class="infobox">
	<div class="inside">
	<table class="mem_info">
		<tr><th colspan="2" class="tlt"><?php _e('shipping address', 'usces'); ?></th></tr>
		<?php wcad_get_admin_addressform( 'delivery', $delivery ); ?>
	</table>
	</div>
	</div>
<input type="hidden" name="reg_id" value="<?php esc_attr_e( $regular_order['reg_id'] ); ?>" />
</div>
<!-- regular info -->

<!-- regular cart -->
<div id="ad_r_item" class="clearfix">

<h3><?php _e('Regular purchase information', 'autodelivery'); ?></h3>

<table>
	<thead>
	<tr>
		<th scope="row" class="num"><?php echo __('No.','usces'); ?></th>
		<th class="itembox"><?php _e('Items', 'usces'); ?></th>
		<th><?php _e('Scheduled date', 'autodelivery'); ?></th>
		<th><?php _e('Status', 'autodelivery'); ?></th>
	</tr>
	</thead>
	<tbody id="orderitemlist">
<?php
	$idx = 0;
	foreach( (array)$regular_detail as $detail ):
		$post_id = urldecode($detail['regdet_post_id']);
		$sku_code = urldecode($detail['regdet_sku']);
		$options = unserialize($detail['regdet_options']);
		$itemCode = $usces->getItemCode($post_id);
		$itemName = $usces->getItemName($post_id);
		$cartItemName = $usces->getCartItemName($post_id, $sku_code);
		$stock = $usces->getItemZaiko($post_id, $sku_code);
		$red = ( in_array($stock, array(__('sellout', 'usces'), __('Out Of Stock', 'usces'), __('Out of print', 'usces'))) ) ? ' class="signal_red"' : '';
		$pictid = (int)$usces->get_mainpictid($itemCode);
		if( empty($options) ) {
			$optstr = '';
			$options = array();
		}
		if( is_array($options) && count($options) > 0 ) {
			$optstr = '';
			foreach( $options as $key => $value ) {
				if( !empty($key) ) {
					$key = urldecode($key);
					if( is_array($value) ) {
						$c = '';
						$optstr .= esc_html($key).' : ';
						foreach( $value as $v ) {
							$optstr .= $c.nl2br(esc_html(urldecode($v)));
							$c = ', ';
						}
						$optstr .= "<br />\n";
					} else {
						$optstr .= esc_html($key).' : '.nl2br(esc_html(urldecode($value)))."<br />\n";
					}
				}
			}
		}
?>
	<tr<?php echo ' class="'.$detail['regdet_condition'].'"'; ?>>
		<td><?php echo ($idx+1); ?></td>
		<td class="clearfix">
			<div class="item_img"><?php echo wp_get_attachment_image( $pictid, array(150, 150), true ); ?></div>
			<div class="item_content">
			<p class="itemname"><?php esc_html_e( $cartItemName ); ?><br /><?php echo $optstr; ?></p>
			<table>
				<tr><th><?php _e('Unit price','usces'); ?></th><td><?php usces_crform( $detail['regdet_price'], true, false ); ?></td></tr>
				<tr><th><?php _e('Quantity','usces'); ?></th><td><input name="regdet_quantity_<?php echo $idx; ?>" id="regdet_quantity_<?php echo $idx; ?>" class="text price" type="text" value="<?php esc_attr_e( $detail['regdet_quantity'] ); ?>" /></td></tr>
				<?php $price = $detail['regdet_price'] * $detail['regdet_quantity']; ?>
				<tr><th><?php _e('Amount','usces'); ?>(<?php usces_crcode(); ?>)</th><td><?php usces_crform( $price, true, false ); ?></td></tr>
				<tr><th><?php _e('Current stock', 'usces'); ?></th><td<?php echo $red; ?>><?php esc_html_e( $stock ); ?></td></tr>
			</table>
			</div>
		</td>
		<td>
			<table>
				<tr><th><?php _e('Scheduled order date', 'autodelivery'); ?></th>
					<td><div id="schedule_date_<?php echo $idx; ?>"><?php esc_attr_e( $detail['regdet_schedule_date'] ); ?></div><input type="hidden" name="schedule_date_<?php echo $idx; ?>" value="<?php esc_attr_e( $detail['regdet_schedule_date'] ); ?>" /></td>
				</tr>
				<tr><th><?php _e('Scheduled shipment date', 'autodelivery'); ?></th>
					<td><div id="schedule_delidue_date_<?php echo $idx; ?>"><?php esc_attr_e( $detail['regdet_schedule_delidue_date'] ); ?></div><input type="hidden" name="schedule_delidue_date_<?php echo $idx; ?>" value="<?php esc_attr_e( $detail['regdet_schedule_delidue_date'] ); ?>" /></td>
				</tr>
				<tr><th><?php _e('Scheduled delivery date', 'autodelivery'); ?></th>
					<td>
				<?php
					if( isset($detail['regdet_schedule_delivery_date']) and $detail['regdet_schedule_delivery_date'] != "" ):
				?>
					<select name="schedule_delivery_date_<?php echo $idx; ?>" id="schedule_delivery_date_<?php echo $idx; ?>" onchange="regularFunc.getScheduledDate( this.value, '<?php echo $idx; ?>' );">
					<?php
						list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = split( '([^0-9])', current_time('mysql') );

						//配達日数に設定されている県毎の日数
						$days = 0;
						$delivery_method_index = $usces->get_delivery_method_index((int)$detail['regdet_delivery_method']);
						$delivery_method_days = ( isset($usces->options['delivery_method'][$delivery_method_index]['days']) ) ? (int)$usces->options['delivery_method'][$delivery_method_index]['days'] : -1;
						if( 0 <= $delivery_method_days ) {
							$delivery_days = $usces->options['delivery_days'];
							foreach( (array)$delivery_days as $delivery_days_value ) {
								if( (int)$delivery_days_value['id'] == $delivery_method_days ) {
									$days = (int)$delivery_days_value[$delivery['country']][$delivery['pref']];
								}
							}
						}

						//発送日目安
						$shipping = 0;
						$itemShipping = $usces->getItemShipping( $detail['regdet_post_id'] );
						if( $itemShipping == 0 or $itemShipping == 9 ) $itemShipping = 0;
						$indication_incart = isset( $shipping_indication[$itemShipping] ) ? $shipping_indication[$itemShipping] : false;

						$sendout_num = 1;

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
						if( 0 < $days ) {
							list( $year, $month, $day ) = explode( '-', $delivery_date );
							$delivery_date = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day + $days, (int)$year) );
						}

						$date = explode( "-", $delivery_date );
						$delivery_date_limit = apply_filters( 'wcad_filter_delivery_date_limit_values', 50 );
						for( $d = 0; $d < $delivery_date_limit; $d++ ) {
							$value = date( 'Y-m-d', mktime(0,0,0,(int)$date[1],(int)$date[2]+$d,(int)$date[0]) );
							$selected = ( isset($detail['regdet_schedule_delivery_date']) and $detail['regdet_schedule_delivery_date'] == $value ) ? ' selected="selected"' : '';
							echo "\t<option value='{$value}'{$selected}>{$value}</option>\n";
						}
					?>
					</select>
				<?php
					endif;
				?>
					</td>
				</tr>
			</table>
		</td>
		<td>
			<table>
				<?php
					if( 'day' == $detail['regdet_unit'] ) {
						$regular_unit = __('Daily', 'autodelivery');
					} elseif( 'month' == $detail['regdet_unit'] ) {
						$regular_unit = __('Monthly' ,'autodelivery');
					} else {
						$regular_unit = '';
					}
					$regular_interval = $detail['regdet_interval'];
					if( isset($detail['regdet_frequency']) and 0 < (int)$detail['regdet_frequency'] ) {
						$regular_frequency = $detail['regdet_frequency'].__('times', 'autodelivery');
						$regular_remain = $detail['regdet_remain'].__('times', 'autodelivery');
					} else {
						$regular_frequency = __('Free cycle', 'autodelivery');
						$regular_remain = "&nbsp;";
					}
				?>
				<tr><th><?php _e('Interval', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_interval ); ?><?php esc_attr_e( $regular_unit ); ?></td></tr>
				<tr><th><?php _e('Frequency', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_frequency ); ?></td></tr>
				<tr><th><?php _e('Remaining', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_remain ); ?></td></tr>
				<tr><th><?php _e('Condition', 'autodelivery'); ?></th><td>
				<?php
					if( 'continuation' == $detail['regdet_condition'] ):
				?>
					<select name="regular_condition_<?php echo $idx; ?>" id="regular_condition_<?php echo $idx; ?>">
					<?php foreach( $arr_condition as $condition ): ?>
						<option value="<?php echo $condition; ?>"<?php if( $condition == $detail['regdet_condition'] ) echo ' selected="selected"'; ?>><?php _e($condition, 'autodelivery'); ?></option>
					<?php endforeach; ?>
					</select>
				<?php
					else:
						_e($detail['regdet_condition'], 'autodelivery');
					endif;
				?>
				</td>
				</tr>
			</table>
			<input type="hidden" name="regdet_id_<?php echo $idx; ?>" value="<?php esc_attr_e( $detail['regdet_id'] ); ?>" />
			<input type="hidden" name="regular_before_condition_<?php echo $idx; ?>" value="<?php esc_attr_e( $detail['regdet_condition'] ); ?>" />
			<input type="hidden" id="regdet_post_id_<?php echo $idx; ?>" value="<?php esc_attr_e( $detail['regdet_post_id'] ); ?>" />
			<input type="hidden" id="regdet_delivery_method_<?php echo $idx; ?>" value="<?php esc_attr_e( $detail['regdet_delivery_method'] ); ?>" />
		</td>
	</tr>
<?php
	$idx++;
	endforeach;
?>
</tbody>
</table>
</div>
<!-- regular cart -->

<!-- regular history -->
<div id="ad_r_history" class="clearfix">

<h3><?php _e('Automatic order history', 'autodelivery'); ?></h3>

<table>
	<tr>
		<th class="historyrow"><?php _e('order number', 'usces'); ?></th>
		<th class="historyrow">&nbsp;</th>
		<th class="historyrow"><?php _e('item name', 'usces'); ?></th>
		<th class="historyrow"><?php _e('Order date', 'autodelivery'); ?></th>
		<th class="historyrow"><?php _e('Shipment date', 'autodelivery'); ?></th>
		<th class="historyrow"><?php _e('Delivery date', 'autodelivery'); ?></th>
		<th class="historyrow"><?php _e('times', 'autodelivery'); ?></th>
		<th class="historyrow"><?php _e('Remaining', 'autodelivery'); ?></th>
	</tr>
<?php
	foreach( (array)$regular_history as $history ):
		$query = $wpdb->prepare( "SELECT order_cart FROM {$order_table_name} WHERE ID = %d ", $history['ID'] );
		$cart = $wpdb->get_var( $query );
		$cart = (array)unserialize($cart);
		foreach( $cart as $cart_row ):
			$charging_type = $usces->getItemChargingType( $cart_row['post_id'], $cart_row );
			if( 'regular' == $charging_type ):
				$post_id = $cart_row['post_id'];
				$sku_code = urldecode( $cart_row['sku'] );
				$itemCode = $usces->getItemCode($post_id);
				$itemName = $usces->getItemName($post_id);
				$pctid = (int)$usces->get_mainpictid($itemCode);
				$advance = $usces->cart->wc_unserialize( $cart_row['advance'] );
				$regular = $advance[$post_id][$sku_code]['regular'];
				$times = ( isset($regular['times']) and 1 < (int)$regular['times'] ) ? $regular['times'].__('th', 'autodelivery') : __('first time', 'autodelivery');
				if( isset($regular['frequency']) and 0 < (int)$regular['frequency'] ) {
					$remain = ( isset($regular['remain']) and 0 <= (int)$regular['remain'] ) ? $regular['remain'] : $regular['frequency'] - 1;
				} else {
					$remain = "&nbsp;";
				}
?>
	<tr>
		<td><a href="<?php echo USCES_ADMIN_URL; ?>?page=usces_orderlist&order_action=edit&order_id=<?php echo $history['ID']; ?>&usces_referer=<?php echo $curent_url; ?>"><?php echo $history['deco_id']; ?></a></td>
		<td><?php echo wp_get_attachment_image( $pctid, array(50, 50), true ); ?></td>
		<td><?php esc_attr_e( $itemName ); ?></td>
		<td><?php esc_attr_e( $history['date'] ); ?></td>
		<td><?php esc_attr_e( $history['delidue_date'] ); ?></td>
		<td><?php esc_attr_e( $history['delivery_date'] ); ?></td>
		<td><?php esc_attr_e( $times ); ?></td>
		<td><?php esc_attr_e( $remain ); ?></td>
	</tr>
<?php
			endif;
		endforeach;
	endforeach;
?>
</table>
</div>
<!-- regular history -->

<input type="hidden" name="regular_action" value="<?php echo $regular_action; ?>" />
<input type="hidden" name="regular_id" value="<?php echo $regular_id; ?>" />
<input type="hidden" name="detail_count" value="<?php echo $idx; ?>" />
<input type="hidden" name="usces_referer" id="usces_referer" value="<?php echo ( isset($_REQUEST['usces_referer']) ? $_REQUEST['usces_referer'] : '' ); ?>" />

</form>

</div><!--usces_admin-->
</div><!--wrap-->
