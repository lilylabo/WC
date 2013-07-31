<?php
function wcad_have_regular_order( $cart = NULL ) {
	global $usces;
	$regular = false;

	if( NULL == $cart ) {
		if( !is_object($usces->cart) ) {
			return false;
		}
		$cart = $usces->cart->get_cart();
	}

	foreach( (array)$cart as $cart_row ) {
		$charging_type = $usces->getItemChargingType( $cart_row['post_id'], $cart_row );
		if( 'regular' == $charging_type ) {
			$regular = true;
			break;
		}
	}
	return $regular;
}

//配送方法
function wcad_get_delivery_method( $post_id, $order_delivery_method ) {
	global $usces;
	$delivery_method = $order_delivery_method;

	$item_delivery_method = $usces->getItemDeliveryMethod( $post_id );
	if( !empty($item_delivery_method) ) {
		$delivery_method = array_shift($item_delivery_method);
	}
	return $delivery_method;
}

//配送希望日(到着予定日)
function wcad_get_delivery_date( $date, $unit, $interval ) {
	$mkdate = $date;

	list( $year, $month, $day ) = explode( '-', $date );
	if( 'day' == $unit ) {
		$mkdate = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day + $interval, (int)$year) );
	} elseif( 'month' == $unit ) {
		$mkdate = date( 'Y-m-d', mktime(0, 0, 0, (int)$month + $interval, (int)$day, (int)$year) );
	}

	return $mkdate;
}

//発送予定日
function wcad_get_shipment_date( $date, $delivery_method, $delivery_country, $delivery_pref ) {
	global $usces;
	$mkdate = $date;

	//配達日数
	$delivery = 0;
	$delivery_method_index = $usces->get_delivery_method_index((int)$delivery_method);
	$delivery_days = $usces->options['delivery_days'];
	$days = ( isset($usces->options['delivery_method'][$delivery_method_index]['days']) ) ? (int)$usces->options['delivery_method'][$delivery_method_index]['days'] : -1;
	if( 0 <= $days ) {
		foreach( (array)$delivery_days as $delivery_days_value ) {
			if( (int)$delivery_days_value['id'] == $days ) {
				$delivery = (int)$delivery_days_value[$delivery_country][$delivery_pref];
			}
		}
	}
	if( 0 < $delivery ) {
		list( $year, $month, $day ) = explode( '-', $mkdate );
		$mkdate = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day - $delivery, (int)$year) );
	}

	//営業日
	list( $year, $month, $day ) = explode( '-', $mkdate );
	$business = ( isset($usces->options['business_days'][(int)$year][(int)$month][(int)$day]) ) ? $usces->options['business_days'][(int)$year][(int)$month][(int)$day] : 1;
	while( $business != 1 ) {
		$mkdate = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day - 1, (int)$year) );
		list( $year, $month, $day ) = explode( '-', $mkdate );
		$business = $usces->options['business_days'][(int)$year][(int)$month][(int)$day];
	}

	return $mkdate;
}

//自動受注日
function wcad_get_auto_order_date( $date, $post_id ) {
	global $usces;
	$mkdate = $date;

	//発送日目安
	$shipping = 0;
	$itemShipping = $usces->getItemShipping( $post_id );
	//if( $itemShipping == 0 or $itemShipping == 9 ) $itemShipping = 1;
	$shipping_indication = apply_filters( 'usces_filter_shipping_indication', $usces->options['usces_shipping_indication'] );
	if( isset($shipping_indication[$itemShipping]) ) $shipping = $shipping_indication[$itemShipping];
	if( 0 < $shipping ) {
		list( $year, $month, $day ) = explode( '-', $mkdate );
		$mkdate = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day - $shipping, (int)$year) );
	}

	//営業日
	list( $year, $month, $day ) = explode( '-', $mkdate );
	$business = ( isset($usces->options['business_days'][(int)$year][(int)$month][(int)$day]) ) ? $usces->options['business_days'][(int)$year][(int)$month][(int)$day] : 1;
	while( $business != 1 ) {
		$mkdate = date( 'Y-m-d', mktime(0, 0, 0, (int)$month, (int)$day - 1, (int)$year) );
		list( $year, $month, $day ) = explode( '-', $mkdate );
		$business = $usces->options['business_days'][(int)$year][(int)$month][(int)$day];
	}

	return $mkdate;
}

function wcad_get_auto_order() {
	global $usces, $wpdb;
	$orders = array();
	$today = date( 'Y-m-d', current_time('timestamp') );

	$regular_table_name = $wpdb->prefix."usces_regular";
	$regular_detail_table_name = $wpdb->prefix."usces_regular_detail";
	$query = $wpdb->prepare( "SELECT reg_id FROM {$regular_detail_table_name} WHERE regdet_schedule_date = %s AND regdet_condition = 'continuation'", $today );
	$rows = $wpdb->get_results( $query, ARRAY_A );
	foreach( (array)$rows as $row ) {
		$orders[] = $row['reg_id'];
	}
	$orders = array_unique($orders);
usces_log("wcad_get_auto_order:orders=".print_r($orders,true),"wcad.log");
	return $orders;
}

function wcad_make_order( $orders ) {
	global $wpdb, $usces, $wcad_options;

	$order_date = date( 'Y-m-d', current_time('timestamp') );

	$regular_table_name = $wpdb->prefix."usces_regular";
	$regular_detail_table_name = $wpdb->prefix."usces_regular_detail";
	$order_table_name = $wpdb->prefix."usces_order";
	$cr = $usces->get_currency_code();

	foreach( (array)$orders as $reg_id ) {
		//Get regular data
		$query = $wpdb->prepare( "SELECT * FROM {$regular_table_name} WHERE reg_id = %d", $reg_id );
		$regular_order = $wpdb->get_row( $query, ARRAY_A );

		//Get regular detail data
		$query = $wpdb->prepare( "SELECT * FROM {$regular_detail_table_name} WHERE reg_id = %d AND regdet_schedule_date = %s AND regdet_condition = 'continuation' ORDER BY regdet_schedule_delidue_date", $reg_id, $order_date );
		$regular_detail = $wpdb->get_results( $query, ARRAY_A );
		if( 1 < count($regular_detail) ) {
			$delivery_date = $regular_detail[0]['regdet_schedule_delivery_date'];
			$regdet_id = array();
			foreach( (array)$regular_detail as $row ) {
				$regdet_id[] = $row['regdet_id'];
				if( $delivery_date < $row['regdet_schedule_delivery_date'] ) $delivery_date = $row['regdet_schedule_delivery_date'];
			}
			$query = $wpdb->prepare( "SELECT regdet_id FROM {$regular_detail_table_name} WHERE reg_id = %d AND regdet_schedule_delivery_date = %s AND regdet_condition = 'continuation'", $reg_id, $delivery_date );
			$rows = $wpdb->get_results( $query, ARRAY_A );
			foreach( (array)$rows as $row ) {
				$regdet_id[] = $row['regdet_id'];
			}
			$regdet_ids = array_unique( $regdet_id );
			$regdet_id_in = "";
			foreach( (array)$regdet_ids as $id ) {
				$regdet_id_in .= $id.",";
			}
			$regdet_id_in = rtrim( $regdet_id_in, "," );
			$query = $wpdb->prepare( "SELECT * FROM {$regular_detail_table_name} WHERE reg_id = %d AND regdet_id IN(".$regdet_id_in.") ORDER BY regdet_schedule_delidue_date", $reg_id );
			$regular_detail = $wpdb->get_results( $query, ARRAY_A );
			$delidue_date = $regular_detail[0]['regdet_schedule_delidue_date'];
			$delivery_method = -1;
			$delivery_time = '';
		} else {
			$delivery_date = $regular_detail[0]['regdet_schedule_delivery_date'];
			$delidue_date = $regular_detail[0]['regdet_schedule_delidue_date'];
			$delivery_method = $regular_detail[0]['regdet_delivery_method'];
			$delivery_time = $regular_detail[0]['regdet_delivery_time'];
		}

		//Make cart
		$cart = array();
		$_SESSION['usces_cart'] = array();
		$i = 0;
		foreach( (array)$regular_detail as $detail ) {
			$post_id = $detail['regdet_post_id'];
			$sku = urldecode( $detail['regdet_sku'] );
			$cart[$i]['serial'] = $detail['regdet_serial'];
			$cart[$i]['post_id'] = $post_id;
			$cart[$i]['sku'] = $detail['regdet_sku'];
			$cart[$i]['options'] = unserialize($detail['regdet_options']);
			$cart[$i]['price'] = $detail['regdet_price'];
			$cart[$i]['quantity'] = $detail['regdet_quantity'];
			$advance = $usces->cart->wc_unserialize( $detail['regdet_advance'] );
			$regular = $advance[$post_id][$sku]['regular'];
			$frequency = isset( $regular['frequency'] ) ? (int)$regular['frequency'] : 0;
			$regular['remain'] = ( 0 == $frequency ) ? -1 : (int)$detail['regdet_remain'] - 1;
			$regular['times'] = (int)$detail['regdet_times'] + 1;
			$advance[$post_id][$sku]['regular'] = $regular;
			$cart[$i]['advance'] = $usces->cart->wc_serialize( $advance );
			$i++;

			$serial = $detail['regdet_serial'];
			$_SESSION['usces_cart'][$serial] = $cart[$i];
		}

		if( $delivery_method < 0 ) {
			$available_delivery_method = $usces->get_available_delivery_method();
			$delivery_method = $available_delivery_method[0];
			foreach( (array)$regular_detail as $detail ) {
				if( $delivery_method == $detail['regdet_delivery_method'] ) {
					$delivery_time = $detail['regdet_delivery_time'];
					break;
				}
			}
		}

		$item_total_price = $usces->get_total_price( $cart );
		$usedpoint = 0;

		$display_mode = $usces->options['display_mode'];
		if( 'Promotionsale' == $display_mode ) {
			if( isset($wcad_options['campaign']) and $wcad_options['campaign'] == 'off' ) $display_mode = 'Usualsale';
		}
		$getpoint = $usces->get_order_point( $regular_order['reg_mem_id'], $display_mode, $cart );
		$discount = $usces->get_order_discount( $display_mode, $cart );

		$delivery = (array)unserialize($regular_order['reg_delivery']);
		$entry = array();
		$entry['order']['delivery_method'] = $delivery_method;
		$entry['delivery']['country'] = $delivery['country'];
		if( empty($usces->options['postage_privilege']) || $item_total_price < $usces->options['postage_privilege'] ) {
			$shipping_charge = $usces->getShippingCharge( $delivery['pref'], $cart, $entry );
		} else {
			$shipping_charge = 0;
		}
		$total_price = $item_total_price - $usedpoint + $discount + $shipping_charge;
		$cod_fee = wcad_get_cod_fee( $regular_order['reg_payment_name'], $total_price );
		$total_price += $cod_fee;
		$total_price = apply_filters( 'wcad_filter_set_cart_fees_total_price', $total_price, $item_total_price, $usedpoint, $discount, $shipping_charge, $cod_fee );
		$tax = $usces->getTax( $total_price );

		$query = $wpdb->prepare(
			"INSERT INTO $order_table_name (
				`mem_id`, `order_email`, `order_name1`, `order_name2`, `order_name3`, `order_name4`, 
				`order_zip`, `order_pref`, `order_address1`, `order_address2`, `order_address3`, 
				`order_tel`, `order_fax`, `order_delivery`, `order_cart`, `order_note`, `order_delivery_method`, `order_delivery_date`, `order_delivery_time`, 
				`order_payment_name`, `order_condition`, `order_item_total_price`, `order_getpoint`, `order_usedpoint`, `order_discount`, 
				`order_shipping_charge`, `order_cod_fee`, `order_tax`, `order_date`, `order_delidue_date`, `order_status`) 
			VALUES (%d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %s, %f, %d, %d, %f, %f, %f, %f, %s, %s, %s)", 
				$regular_order['reg_mem_id'], 
				$regular_order['reg_email'], 
				$regular_order['reg_name1'], 
				$regular_order['reg_name2'], 
				$regular_order['reg_name3'], 
				$regular_order['reg_name4'], 
				$regular_order['reg_zip'], 
				$regular_order['reg_pref'], 
				$regular_order['reg_address1'], 
				$regular_order['reg_address2'], 
				$regular_order['reg_address3'], 
				$regular_order['reg_tel'], 
				$regular_order['reg_fax'], 
				$regular_order['reg_delivery'], 
				serialize( $cart ), 
				$regular_order['reg_note'], 
				$delivery_method, 
				$delivery_date, 
				$delivery_time, 
				$regular_order['reg_payment_name'], 
				$regular_order['reg_condition'], 
				$item_total_price, 
				$getpoint, 
				$usedpoint, 
				$discount, 
				$shipping_charge, 
				$cod_fee, 
				$tax, 
				$order_date, 
				$delidue_date, 
				$regular_order['reg_status'] 
		);
		$res = $wpdb->query( $query );
		usces_log( 'wcad_make_order : '.$wpdb->last_error, 'database_error.log' );

		if( $res === false) {
			$new_id = false;
		} else {
			$new_id = $wpdb->insert_id;
		}

		if( !$new_id ) {
			usces_log( 'reg_error_data : '.print_r($regular, true), 'acting_transaction.log' );
			return false;

		} else {
			$order_id = $regular_order['reg_order_id'];

			$usces->set_order_meta_value( 'customer_country', $regular_order['reg_country'], $new_id );

			$order_meta_table_name = $wpdb->prefix."usces_order_meta";
			$query = $wpdb->prepare( "SELECT meta_key, meta_value FROM $order_meta_table_name WHERE order_id = %d AND meta_key LIKE %s", $order_id, 'csod_%' );
			$rows = $wpdb->get_results( $query, ARRAY_A );
			foreach( $rows as $row ) {
				$usces->set_order_meta_value( $row['meta_key'], $row['meta_value'], $new_id );
			}
			$query = $wpdb->prepare( "SELECT meta_key, meta_value FROM $order_meta_table_name WHERE order_id = %d AND meta_key LIKE %s", $order_id, 'cscs_%' );
			$rows = $wpdb->get_results( $query, ARRAY_A );
			foreach( $rows as $row ) {
				$usces->set_order_meta_value( $row['meta_key'], $row['meta_value'], $new_id );
			}
			$query = $wpdb->prepare( "SELECT meta_key, meta_value FROM $order_meta_table_name WHERE order_id = %d AND meta_key LIKE %s", $order_id, 'csde_%' );
			$rows = $wpdb->get_results( $query, ARRAY_A );
			foreach( $rows as $row ) {
				$usces->set_order_meta_value( $row['meta_key'], $row['meta_value'], $new_id );
			}

			foreach( $cart as $cart_row ) {
				$sku = urldecode( $cart_row['sku'] );
				$zaikonum = $usces->getItemZaikoNum( $cart_row['post_id'], $sku );
				if( $zaikonum == '' ) continue;
				$zaikonum = $zaikonum - $cart_row['quantity'];
				$usces->updateItemZaikoNum( $cart_row['post_id'], $sku, $zaikonum );
				if( $zaikonum <= 0 ) $usces->updateItemZaiko( $cart_row['post_id'], $sku, 2 );
			}

			$payments = $usces->getPayments( $regular_order['reg_payment_name'] );
			$charging_type = 'regular';
			$args = array( 'cart'=>$cart, 'entry'=>$entry, 'order_id'=>$new_id, 'member_id'=>$regular_order['reg_mem_id'], 'payments'=>$payments, 'charging_type'=>$charging_type );
			usces_action_reg_orderdata( $args );

			$usces->set_order_meta_value( 'regular_id', $reg_id, $new_id );

			//Regular detail data update
			foreach( (array)$regular_detail as $detail ) {
				$unit = $detail['regdet_unit'];
				$interval = (int)$detail['regdet_interval'];
				if( 0 == (int)$detail['regdet_frequency'] ) {
					$remain = 0;
					$schedule_delivery_date = wcad_get_delivery_date( $delivery_date, $unit, $interval );//配送希望日
					$schedule_delidue_date = wcad_get_shipment_date( $schedule_delivery_date, $detail['regdet_delivery_method'], $delivery['country'], $delivery['pref'] );//発送予定日
					$schedule_date = wcad_get_auto_order_date( $schedule_delidue_date, $detail['regdet_post_id'] );//自動受注日
				} else {
					$remain = (int)$detail['regdet_remain'] - 1;
					if( 0 < $remain ) {
						$schedule_delivery_date = wcad_get_delivery_date( $delivery_date, $unit, $interval );//配送希望日
						$schedule_delidue_date = wcad_get_shipment_date( $schedule_delivery_date, $detail['regdet_delivery_method'], $delivery['country'], $delivery['pref'] );//発送予定日
						$schedule_date = wcad_get_auto_order_date( $schedule_delidue_date, $detail['regdet_post_id'] );//自動受注日
					} else {
						$schedule_delivery_date = "";
						$schedule_delidue_date = "";
						$schedule_date = "";
					}
				}
				$times = (int)$detail['regdet_times'] + 1;
				$condition = ( empty($schedule_date) ) ? "termination" : "continuation";

				$query = $wpdb->prepare( "UPDATE $regular_detail_table_name SET 
					regdet_remain = %d, regdet_times = %d, 
					regdet_schedule_date = %s, regdet_schedule_delidue_date = %s, regdet_schedule_delivery_date = %s, 
					regdet_condition = %s 
				WHERE regdet_id = %d ", 
					$remain, 
					$times, 
					$schedule_date, 
					$schedule_delidue_date, 
					$schedule_delivery_date, 
					$condition, 
					$detail['regdet_id'] 
				);
				$res = $wpdb->query( $query );
			}

			$acting_flag = ( 'acting' == $payments['settlement'] ) ? $payments['module'] : $payments['settlement'];
			switch( $acting_flag ) {
				case 'acting_zeus_card':
					$member = $usces->get_member_info( $regular_order['reg_mem_id'] );
					if( $member ) {
						$acting_opts = $usces->options['acting_settings']['zeus'];
						$interface = parse_url($acting_opts['card_url']);
						//$pcid = $usces->get_member_meta_value( 'zeus_pcid', $member['ID'] );
						$vars  = 'send=mall';
						$vars .= '&clientip='.$acting_opts['clientip'];
						//$vars .= '&cardnumber='.$pcid;
						$vars .= '&cardnumber=8888888888888888';
						$vars .= '&expyy=10';
						$vars .= '&expmm=01';
						$vars .= '&telno='.str_replace('-', '', $member['mem_tel']);
						$vars .= '&email='.$member['mem_email'];
						$vars .= '&sendid='.$member['ID'];
						$vars .= '&username=WCEXAUTODELIVERY';
						$vars .= '&money='.($total_price+$tax);
						$vars .= '&printord=yes';
						$vars .= '&pubsec=yes';

						$header  = "POST ".$interface['path']." HTTP/1.1\r\n";
						$header .= "Host: ".$_SERVER['HTTP_HOST']."\r\n";
						$header .= "User-Agent: PHP Script\r\n";
						$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
						$header .= "Content-Length: ".strlen($vars)."\r\n";
						$header .= "Connection: close\r\n\r\n";
						$header .= $vars;
						$fp = fsockopen( 'ssl://'.$interface['host'], 443, $errno, $errstr, 30 );

						if( $fp ) {
							fwrite( $fp, $header );
							while( !feof($fp) ) {
								$scr = fgets( $fp, 1024 );
								$page .= $scr;
							}
							fclose($fp);

							if( false !== strpos( $page, 'Success_order') ) {
								usces_log('zeus card : [WCEX_Auto_Delivery] Auto order data (acting_processing)', 'acting_transaction.log');
								$ordd = usces_get_order_number( $page );
								if( !empty($ordd) ) $usces->set_order_meta_value( 'order_number', $ordd, $new_id );

							} else {
								usces_log('zeus card : [WCEX_Auto_Delivery] Certification Error', 'acting_transaction.log');
								usces_log('zeus card : [WCEX_Auto_Delivery] '.$page, 'acting_transaction.log');
								$settltment = array( "settltment_status" => "不履行", "settltment_errmsg" => "[定期購入]決済が完了しませんでした。" );
								$usces->set_order_meta_value( $acting_flag, serialize($settltment), $new_id );
							}
						} else {
							usces_log('zeus card : [WCEX_Auto_Delivery] Socket Error', 'acting_transaction.log');
							usces_log('zeus card : [WCEX_Auto_Delivery] '.$page, 'acting_transaction.log');
							$settltment = array( "settltment_status" => "不履行", "settltment_errmsg" => "[定期購入]決済が完了しませんでした。" );
							$usces->set_order_meta_value( $acting_flag, serialize($settltment), $new_id );
						}
					} else {
						usces_log('zeus card : [WCEX_Auto_Delivery] Member Error : '.$regular_order['reg_mem_id'], 'acting_transaction.log');
						$settltment = array( "settltment_status" => "不履行", "settltment_errmsg" => "[定期購入]決済が完了しませんでした。" );
						$usces->set_order_meta_value( $acting_flag, serialize($settltment), $new_id );
					}
					break;
			}

			do_action( 'wcad_action_reg_auto_orderdata', $args );
		}
	}
}

function wcad_update_regulardata() {
	global $usces, $wpdb;
	$_POST = $usces->stripslashes_deep_post( $_POST );
	$res = 0;

	$regular_table_name = $wpdb->prefix."usces_regular";
	$query = $wpdb->prepare( "UPDATE $regular_table_name SET 
		reg_email = %s, reg_name1 = %s, reg_name2 = %s, reg_name3 = %s, reg_name4 = %s, 
		reg_country = %s, reg_zip = %s, reg_pref = %s, 
		reg_address1 = %s, reg_address2 = %s, reg_address3 = %s, 
		reg_tel = %s, reg_fax = %s, reg_delivery = %s, reg_note = %s 
	WHERE reg_id = %d ", 
		$_POST['regular']['email'], 
		$_POST['regular']['name1'], 
		$_POST['regular']['name2'], 
		$_POST['regular']['name3'], 
		$_POST['regular']['name4'], 
		$_POST['regular']['country'], 
		$_POST['regular']['zipcode'], 
		$_POST['regular']['pref'], 
		$_POST['regular']['address1'], 
		$_POST['regular']['address2'], 
		$_POST['regular']['address3'], 
		$_POST['regular']['tel'], 
		$_POST['regular']['fax'], 
		serialize( $_POST['delivery'] ), 
		$_POST['regular']['note'], 
		$_POST['reg_id'] 
	);
	if( 0 <= $wpdb->query( $query ) ) {
		$regular_detail_table_name = $wpdb->prefix."usces_regular_detail";
		$detail_count = (int)$_POST['detail_count'];
		for( $i = 0; $i < $detail_count; $i++ ) {
			if( "continuation" == $_POST['regular_before_condition_'.$i] ) {
				$quantity = $_POST['regdet_quantity_'.$i];
				if( empty($quantity) or !is_numeric($quantity) or 1 > (int)$quantity ) {
					$res = -1;
					break;
				}

				$condition = $_POST['regular_condition_'.$i];
				if( "continuation" == $condition ) {
					$schedule_date = $_POST['schedule_date_'.$i];
					$schedule_delidue_date = $_POST['schedule_delidue_date_'.$i];
					$schedule_delivery_date = $_POST['schedule_delivery_date_'.$i];
				} else {
					$schedule_date = "";
					$schedule_delidue_date = "";
					$schedule_delivery_date = "";
				}

				$query = $wpdb->prepare( "UPDATE $regular_detail_table_name SET 
					regdet_quantity = %d, regdet_schedule_date = %s, regdet_schedule_delidue_date = %s, regdet_schedule_delivery_date = %s, regdet_condition = %s 
				WHERE regdet_id = %d ", 
					$quantity, 
					$schedule_date, 
					$schedule_delidue_date, 
					$schedule_delivery_date, 
					$condition, 
					$_POST['regdet_id_'.$i] 
				);
				$res += $wpdb->query( $query );
				if( $res < 0 ) break;
			}
		}
	}
	return $res;
}

function wcad_delete_regulardata() {
	global $wpdb;
	$res = 0;

	if( !isset($_REQUEST['regular_id']) || $_REQUEST['regular_id'] == '' ) return 0;

	$reg_id = $_REQUEST['regular_id'];
	$regular_table_name = $wpdb->prefix."usces_regular";
	$regular_detail_table_name = $wpdb->prefix."usces_regular_detail";
	$meta_table = $wpdb->prefix.'usces_order_meta';

	$query = $wpdb->prepare( "DELETE FROM $regular_table_name WHERE reg_id = %d", $reg_id );
	$res = $wpdb->query( $query );
	if( $res ) {
		$query = $wpdb->prepare( "DELETE FROM $regular_detail_table_name WHERE reg_id = %d", $reg_id );
		$wpdb->query( $query );

		//$query = $wpdb->prepare( "DELETE FROM $meta_table WHERE meta_key = 'regular_id' AND meta_value = %d", $reg_id );
		$query = $wpdb->prepare( "UPDATE $meta_table SET meta_value = '-' WHERE meta_key = 'regular_id' AND meta_value = %d", $reg_id );
		$wpdb->query( $query );
	}

	return $res;
}

function wcad_isdate( $date ) {
	if( empty($date) ) return false;
	try {
		new DateTime( $date );
		return true;
	} catch( Exception $e ) {
		return false;
	}
}

function wcad_action_admin_ajax() {
	switch( $_POST['mode'] ) {
	case 'wcad_get_scheduled_date':
		$schedule_delidue_date = wcad_get_shipment_date( $_POST['delivery_date'], $_POST['delivery_method'], $_POST['country'], $_POST['pref'] );//発送予定日
		$schedule_date = wcad_get_auto_order_date( $schedule_delidue_date, $_POST['post_id'] );//自動受注日
		$res = "ok#usces#".$schedule_date."#usces#".$schedule_delidue_date;
		die($res);
		break;
	}
}

add_filter( 'usces_filter_backCustomer_page', 'wacd_filter_backCustomer_page' );
function wacd_filter_backCustomer_page( $page ) {
	if( usces_is_login() ) {
		$page = 'cart';
	}
	return $page;
}

add_action( 'init', 'wcad_action_init2', 8 );
function wcad_action_init2() {
	usces_register_action( '10wcad_transition', 'request', 'wcad_transition', NULL, 'wcex_wcad_main' );
}

function wcex_wcad_main() {
	global $usces, $wp_query, $usces_item, $post;

	$action = $_REQUEST['wcad_transition'];

	switch( $action ) {

	case 'newmember':
		$usces->page = 'newmemberform';
		add_filter( 'usces_filter_newmember_form_action', 'usces_wcad_newmember_form_action' );
		add_filter( 'usces_filter_newmember_button', 'usces_wcad_newmember_button' );
		add_filter( 'usces_filter_newmember_inform', 'usces_wcad_newmember_inform' );
		add_action( 'usces_action_newmember_page_inform', 'usces_wcad_newmember_wc_inform' );
		add_action( 'the_post', array($usces, 'action_memberFilter') );
		add_filter( 'yoast-ga-push-after-pageview', 'usces_trackPageview_editmemberform' );
		add_action( 'template_redirect', array($usces, 'template_redirect') );
		break;

	case 'regmember':
		$res = $usces->regist_member();
		if( 'newcompletion' == $res ) {
			$email = trim($_POST['member']['mailaddress1']);
			$pass = trim($_POST['member']['password1']);
			$lires = $usces->member_just_login( $email, $pass );
			if( $lires == 'login' ) {
				wp_redirect( get_option('home') );
				exit;
			}
//			$usces->page = 'delivery';
			add_filter( 'yoast-ga-push-after-pageview', 'usces_trackPageview_cart' );
			add_action( 'the_post', array($usces, 'action_cartFilter') );
			wp_redirect( USCES_CART_URL.$usces->delim . 'customerinfo=1' );
			exit;

		} else {
			$usces->page = 'newmemberform';
			add_filter( 'usces_filter_newmember_form_action', 'usces_wcad_newmember_form_action' );
			add_filter( 'usces_filter_newmember_button', 'usces_wcad_newmember_button' );
			add_filter( 'usces_filter_newmember_inform', 'usces_wcad_newmember_inform' );
			add_action( 'usces_action_newmember_page_inform', 'usces_wcad_newmember_wc_inform' );
			add_action( 'the_post', array($usces, 'action_memberFilter') );
			add_filter( 'yoast-ga-push-after-pageview', 'usces_trackPageview_editmemberform' );
		}
		break;
	}
}

function usces_wcad_newmember_form_action( $url ) {
	$url = USCES_CART_URL;
	return $url;
}

function usces_wcad_newmember_urlquery( $query ) {
	return '&wcad_transition=newmember';
}

function usces_wcad_newmember_inform( $html ) {
	$html .= '<input name="wcad_transition" type="hidden" value="regmember" />';
	return $html;
}

function usces_wcad_newmember_wc_inform() {
	$html = '<input name="wcad_transition" type="hidden" value="regmember" />';
	echo $html;
}

function usces_wcad_newmember_button( $button ) {
	$button = '<input name="regmemberwcad" type="submit" value="' . __('transmit a message', 'usces') . '" />';
	return $button;
}

//add_filter( 'usces_filter_get_total_price', 'wcad_filter_get_total_price', 10, 2 );
function wcad_filter_get_total_price() {
	global $usces;
	$args = func_get_args();
	$cart = $args[1];

	if( empty($cart) )
		$cart = $usces->cart->get_cart();

	$total_price = 0;
	if( !empty($cart) ) {
		foreach( (array)$cart as $cart_row ) {
			$quantity = $cart_row['quantity'];
			$post_id = $cart_row['post_id'];
			$charging_type = $usces->getItemChargingType( $post_id, $cart_row );
			if( 'regular' == $charging_type ) {
				$advance = $usces->cart->wc_unserialize( $cart_row['advance'] );
				$sku = urldecode( $cart_row['sku'] );
				$regular = $advance[$post_id][$sku]['regular'];
				$unit = isset( $regular['unit'] ) ? $regular['unit'] : '';
				$interval = isset( $regular['interval'] ) ? (int)$regular['interval'] : 0;
				if( empty($unit) or 1 > $interval ) {
					$rprice = 0;
				} else {
					$rprice = wcad_get_skurprice( $post_id, $sku );
				}
			} else {
				$rprice = 0;
			}
			$skuPrice = ( 0 < $rprice ) ? $rprice : $cart_row['price'];
			$total_price += ($skuPrice * $quantity);
		}
	}
	return $total_price;
}

function wcad_get_cod_fee( $payment_name, $amount_by_cod ) {
	global $usces;

	$fee = 0;
	$payments = $usces->getPayments( $payment_name );
	if( 'COD' != $payments['settlement'] )
		return $fee;

	if( 'change' != $usces->options['cod_type'] ) {
		$fee = ( isset($usces->options['cod_fee']) ) ? $usces->options['cod_fee'] : 0;

	} else {
		$price = $amount_by_cod + $usces->getTax( $amount_by_cod );
		if( $price <= $usces->options['cod_first_amount'] ) {
			$fee = $usces->options['cod_first_fee'];

		} elseif( isset($usces->options['cod_amounts']) ) {
			$last = count( $usces->options['cod_amounts'] ) - 1;
			if( $price > $usces->options['cod_amounts'][$last] ) {
				$fee = $usces->options['cod_end_fee'];

			} else {
				foreach( $usces->options['cod_amounts'] as $key => $value ) {
					if( $price <= $value ) {
						$fee = $usces->options['cod_fees'][$key];
						break;
					}
				}
			}

		} else {
			$fee = $usces->options['cod_end_fee'];
		}
	}
	return $fee;
}

function wcad_get_member_history( $mem_id, $condition = '' ) {
	global $wpdb;
	$regular_table_name = $wpdb->prefix."usces_regular";
	$regular_detail_table_name = $wpdb->prefix."usces_regular_detail";

	$history = array();
	$query = $wpdb->prepare( "SELECT * FROM {$regular_table_name} WHERE reg_mem_id = %d ORDER BY reg_order_id DESC", $mem_id );
	$regular_order = $wpdb->get_results( $query, ARRAY_A );
	foreach( (array)$regular_order as $order_row ) {
		if( 'continuation' == $condition ) {
			if( 'continuation' != $order_row['reg_condition'] ) 
				continue;
		}

		$detail = array();
		$query = $wpdb->prepare( "SELECT * FROM {$regular_detail_table_name} WHERE reg_id = %d ORDER BY regdet_id", $order_row['reg_id'] );
		$regular_detail = $wpdb->get_results( $query, ARRAY_A );
		foreach( (array)$regular_detail as $detail_row ) {
			$detail[] = array(
				'regdet_serial'=>$detail_row['regdet_serial'],
				'regdet_post_id'=>$detail_row['regdet_post_id'],
				'regdet_sku'=>$detail_row['regdet_sku'],
				'regdet_options'=>$detail_row['regdet_options'],
				'regdet_price'=>$detail_row['regdet_price'],
				'regdet_quantity'=>$detail_row['regdet_quantity'],
				'regdet_advance'=>$detail_row['regdet_advance'],
				'regdet_unit'=>$detail_row['regdet_unit'],
				'regdet_interval'=>$detail_row['regdet_interval'],
				'regdet_frequency'=>$detail_row['regdet_frequency'],
				'regdet_remain'=>$detail_row['regdet_remain'],
				'regdet_times'=>$detail_row['regdet_times'],
				'regdet_schedule_date'=>$detail_row['regdet_schedule_date'],
				'regdet_schedule_delidue_date'=>$detail_row['regdet_schedule_delidue_date'],
				'regdet_schedule_delivery_date'=>$detail_row['regdet_schedule_delivery_date'],
				'regdet_delivery_method'=>$detail_row['regdet_delivery_method'],
				'regdet_delivery_time'=>$detail_row['regdet_delivery_time'],
				'regdet_condition'=>$detail_row['regdet_condition']
			);
		}

		$history[] = array(
			'reg_id'=>$order_row['reg_id'],
			'reg_order_id'=>$order_row['reg_order_id'],
			'reg_delivery'=>$order_row['reg_delivery'],
			'reg_note'=>$order_row['reg_note'],
			'reg_payment_name'=>$order_row['reg_payment_name'],
			'reg_condition'=>$order_row['reg_condition'],
			'reg_cod_fee'=>$order_row['reg_cod_fee'],
			'reg_date'=>$order_row['reg_date'],
			'reg_status'=>$order_row['reg_status'],
			'reg_detail'=>serialize($detail)
		);
	}

	return $history;
}

function wcad_have_member_regular_order( $mem_id ) {
	global $wpdb;
	$regular = false;

	$regular_table_name = $wpdb->prefix."usces_regular";
	$regular_detail_table_name = $wpdb->prefix."usces_regular_detail";
	$query = $wpdb->prepare( "SELECT r.reg_id FROM {$regular_detail_table_name} AS d INNER JOIN {$regular_table_name} AS r WHERE r.reg_mem_id = %d AND d.regdet_condition = 'continuation' GROUP BY r.reg_id", $mem_id );
	$regular_order = $wpdb->get_results( $query, ARRAY_A );
	if( 0 < count($regular_order) ) $regular = true;

	return $regular;
}
?>
