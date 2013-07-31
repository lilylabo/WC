<?php
// shipping list download
function wcad_download_shipping_list() {
	global $wpdb, $usces, $usces_settings;

	$ext = $_REQUEST['ftype'];
	if( $ext == 'csv' ) {//CSV
		$table_h = "";
		$table_f = "";
		$tr_h = "";
		$tr_f = "";
		$th_h1 = '"';
		$th_h = ',"';
		$th_f = '"';
		$td_h1 = '"';
		$td_h = ',"';
		$td_f = '"';
		$sp = ":";
		$nb = " ";
		$lf = "\n";
	} else {
		exit();
	}
	$csod_meta = usces_has_custom_field_meta( 'order' );
	$cscs_meta = usces_has_custom_field_meta( 'customer' );
	$csde_meta = usces_has_custom_field_meta( 'delivery' );
	$applyform = usces_get_apply_addressform( $usces->options['system']['addressform'] );

	//==========================================================================
	$wcad_opt_shipping = get_option( 'wcad_opt_shipping' );
	if( !is_array($wcad_opt_shipping) ) {
		$wcad_opt_shipping = array();
	}
	$wcad_opt_shipping['ftype_shp'] = $ext;
	$chk_shp = array();
	$chk_shp['ID'] = 1;
	$chk_shp['deco_id'] = 1;
	$chk_shp['date'] = 1;
	$chk_shp['mem_id'] = ( isset($_REQUEST['check']['mem_id']) ) ? 1 : 0;
	$chk_shp['email'] = ( isset($_REQUEST['check']['email']) ) ? 1 : 0;
	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'name_pre' ) {
				$name = $entry['name'];
				$cscs_key = 'cscs_'.$key;
				$chk_shp[$cscs_key] = ( isset($_REQUEST['check'][$cscs_key]) ) ? 1 : 0;
			}
		}
	}
	$chk_shp['name'] = 1;
	if( $applyform == 'JP' ) {
		$chk_shp['kana'] = ( isset($_REQUEST['check']['kana']) ) ? 1 : 0;
	}
	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'name_after' ) {
				$name = $entry['name'];
				$cscs_key = 'cscs_'.$key;
				$chk_shp[$cscs_key] = ( isset($_REQUEST['check'][$cscs_key]) ) ? 1 : 0;
			}
		}
	}
	$chk_shp['zip'] = ( isset($_REQUEST['check']['zip']) ) ? 1 : 0;
	$chk_shp['country'] = ( isset($_REQUEST['check']['country']) ) ? 1 : 0;
	$chk_shp['pref'] = ( isset($_REQUEST['check']['pref']) ) ? 1 : 0;
	$chk_shp['address1'] = ( isset($_REQUEST['check']['address1']) ) ? 1 : 0;
	$chk_shp['address2'] = ( isset($_REQUEST['check']['address2']) ) ? 1 : 0;
	$chk_shp['address3'] = ( isset($_REQUEST['check']['address3']) ) ? 1 : 0;
	$chk_shp['tel'] = ( isset($_REQUEST['check']['tel']) ) ? 1 : 0;
	$chk_shp['fax'] = ( isset($_REQUEST['check']['fax']) ) ? 1 : 0;
	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'fax_after' ) {
				$name = $entry['name'];
				$cscs_key = 'cscs_'.$key;
				$chk_shp[$cscs_key] = ( isset($_REQUEST['check'][$cscs_key]) ) ? 1 : 0;
			}
		}
	}
	//--------------------------------------------------------------------------
	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'name_pre' ) {
				$name = $entry['name'];
				$csde_key = 'csde_'.$key;
				$chk_shp[$csde_key] = ( isset($_REQUEST['check'][$csde_key]) ) ? 1 : 0;
			}
		}
	}
	$chk_shp['delivery_name'] = ( isset($_REQUEST['check']['delivery_name']) ) ? 1 : 0;
	if( $applyform == 'JP' ) {
		$chk_shp['delivery_kana'] = ( isset($_REQUEST['check']['delivery_kana']) ) ? 1 : 0;
	}
	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'name_after' ) {
				$name = $entry['name'];
				$csde_key = 'csde_'.$key;
				$chk_shp[$csde_key] = ( isset($_REQUEST['check'][$csde_key]) ) ? 1 : 0;
			}
		}
	}
	$chk_shp['delivery_zip'] = ( isset($_REQUEST['check']['delivery_zip']) ) ? 1 : 0;
	$chk_shp['delivery_country'] = ( isset($_REQUEST['check']['delivery_country']) ) ? 1 : 0;
	$chk_shp['delivery_pref'] = ( isset($_REQUEST['check']['delivery_pref']) ) ? 1 : 0;
	$chk_shp['delivery_address1'] = ( isset($_REQUEST['check']['delivery_address1']) ) ? 1 : 0;
	$chk_shp['delivery_address2'] = ( isset($_REQUEST['check']['delivery_address2']) ) ? 1 : 0;
	$chk_shp['delivery_address3'] = ( isset($_REQUEST['check']['delivery_address3']) ) ? 1 : 0;
	$chk_shp['delivery_tel'] = ( isset($_REQUEST['check']['delivery_tel']) ) ? 1 : 0;
	$chk_shp['delivery_fax'] = ( isset($_REQUEST['check']['delivery_fax']) ) ? 1 : 0;
	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'fax_after' ) {
				$name = $entry['name'];
				$csde_key = 'csde_'.$key;
				$chk_shp[$csde_key] = ( isset($_REQUEST['check'][$csde_key]) ) ? 1 : 0;
			}
		}
	}
	//--------------------------------------------------------------------------
	$chk_shp['shipping_date'] = ( isset($_REQUEST['check']['shipping_date']) ) ? 1 : 0;
	$chk_shp['peyment_method'] = ( isset($_REQUEST['check']['peyment_method']) ) ? 1 : 0;
	$chk_shp['delivery_method'] = ( isset($_REQUEST['check']['delivery_method']) ) ? 1 : 0;
	$chk_shp['delivery_date'] = ( isset($_REQUEST['check']['delivery_date']) ) ? 1 : 0;
	$chk_shp['delivery_time'] = ( isset($_REQUEST['check']['delivery_time']) ) ? 1 : 0;
	$chk_shp['delidue_date'] = ( isset($_REQUEST['check']['delidue_date']) ) ? 1 : 0;
	$chk_shp['status'] = ( isset($_REQUEST['check']['status']) ) ? 1 : 0;
	$chk_shp['total_amount'] = 1;
	$chk_shp['usedpoint'] = ( isset($_REQUEST['check']['usedpoint']) ) ? 1 : 0;
	$chk_shp['discount'] = 1;
	$chk_shp['shipping_charge'] = 1;
	$chk_shp['cod_fee'] = 1;
	$chk_shp['tax'] = 1;
	$chk_shp['note'] = ( isset($_REQUEST['check']['note']) ) ? 1 : 0;
	if( !empty($csod_meta) ) {
		foreach( $csod_meta as $key => $entry ) {
			$name = $entry['name'];
			$csod_key = 'csod_'.$key;
			$chk_shp[$csod_key] = ( isset($_REQUEST['check'][$csod_key]) ) ? 1 : 0;
		}
	}
	//--------------------------------------------------------------------------
	$chk_shp['item_code'] = 1;
	$chk_shp['sku_code'] = 1;
	$chk_shp['item_name'] = (isset($_REQUEST['check']['item_name'])) ? 1 : 0;
	$chk_shp['sku_name'] = (isset($_REQUEST['check']['sku_name'])) ? 1 : 0;
	$chk_shp['options'] = (isset($_REQUEST['check']['options'])) ? 1 : 0;
	$chk_shp['quantity'] = 1;
	$chk_shp['price'] = 1;
	$chk_shp['unit'] = (isset($_REQUEST['check']['unit'])) ? 1 : 0;
	$wcad_opt_shipping['chk_shp'] = apply_filters( 'wcad_filter_chk_shp', $chk_shp );
	update_option( 'wcad_opt_shipping', $wcad_opt_shipping );
	//==========================================================================

	if( isset($_REQUEST['check']['status']) ) {
		$usces_management_status = get_option('usces_management_status');
		$usces_management_status['new'] = __('new order', 'usces');
	}

	//==========================================================================
	$line  = $table_h;
	$line .= $tr_h;
	$line .= $th_h1.__('ID', 'usces').$th_f;
	$line .= $th_h.__('Order number', 'usces').$th_f;
	$line .= $th_h.__('order date', 'usces').$th_f;
	if( isset($_REQUEST['check']['mem_id']) ) $line .= $th_h.__('membership number', 'usces').$th_f;
	if( isset($_REQUEST['check']['email']) ) $line .= $th_h.__('e-mail', 'usces').$th_f;
	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'name_pre' ) {
				$name = $entry['name'];
				$cscs_key = 'cscs_'.$key;
				if( isset($_REQUEST['check'][$cscs_key]) ) $line .= $th_h.usces_entity_decode($name, $ext).$th_f;
			}
		}
	}
	$line .= $th_h.__('name', 'usces').$th_f;
	if( $applyform == 'JP' ) {
		if( isset($_REQUEST['check']['kana']) ) $line .= $th_h.__('furigana', 'usces').$th_f;
	}
	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'name_after' ) {
				$name = $entry['name'];
				$cscs_key = 'cscs_'.$key;
				if( isset($_REQUEST['check'][$cscs_key]) ) $line .= $th_h.usces_entity_decode($name, $ext).$th_f;
			}
		}
	}
	switch( $applyform ) {
	case 'JP':
		if( isset($_REQUEST['check']['zip']) ) $line .= $th_h.__('Zip/Postal Code', 'usces').$th_f;
		if( isset($_REQUEST['check']['country']) ) $line .= $th_h.__('Country', 'usces').$th_f;
		if( isset($_REQUEST['check']['pref']) ) $line .= $th_h.__('Province', 'usces').$th_f;
		if( isset($_REQUEST['check']['address1']) ) $line .= $th_h.__('city', 'usces').$th_f;
		if( isset($_REQUEST['check']['address2']) ) $line .= $th_h.__('numbers', 'usces').$th_f;
		if( isset($_REQUEST['check']['address3']) ) $line .= $th_h.__('building name', 'usces').$th_f;
		if( isset($_REQUEST['check']['tel']) ) $line .= $th_h.__('Phone number', 'usces').$th_f;
		if( isset($_REQUEST['check']['fax']) ) $line .= $th_h.__('FAX number', 'usces').$th_f;
		break;
	case 'US':
	default:
		if( isset($_REQUEST['check']['address2']) ) $line .= $th_h.__('Address Line1', 'usces').$th_f;
		if( isset($_REQUEST['check']['address3']) ) $line .= $th_h.__('Address Line2', 'usces').$th_f;
		if( isset($_REQUEST['check']['address1']) ) $line .= $th_h.__('city', 'usces').$th_f;
		if( isset($_REQUEST['check']['pref']) ) $line .= $th_h.__('State', 'usces').$th_f;
		if( isset($_REQUEST['check']['country']) ) $line .= $th_h.__('Country', 'usces').$th_f;
		if( isset($_REQUEST['check']['zip']) ) $line .= $th_h.__('Zip', 'usces').$th_f;
		if( isset($_REQUEST['check']['tel']) ) $line .= $th_h.__('Phone number', 'usces').$th_f;
		if( isset($_REQUEST['check']['fax']) ) $line .= $th_h.__('FAX number', 'usces').$th_f;
		break;
	}
	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'fax_after' ) {
				$name = $entry['name'];
				$cscs_key = 'cscs_'.$key;
				if( isset($_REQUEST['check'][$cscs_key]) ) $line .= $th_h.usces_entity_decode($name, $ext).$th_f;
			}
		}
	}
	//--------------------------------------------------------------------------
	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'name_pre' ) {
				$name = $entry['name'];
				$csde_key = 'csde_'.$key;
				if( isset($_REQUEST['check'][$csde_key]) ) $line .= $th_h.usces_entity_decode($name, $ext).$th_f;
			}
		}
	}
	if( isset($_REQUEST['check']['delivery_name']) ) $line .= $th_h.__('Shipping Name', 'usces').$th_f;
	if( $applyform == 'JP' ) {
		if( isset($_REQUEST['check']['delivery_kana']) ) $line .= $th_h.__('Shipping Furigana', 'usces').$th_f;
	}
	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'name_after' ) {
				$name = $entry['name'];
				$csde_key = 'csde_'.$key;
				if( isset($_REQUEST['check'][$csde_key]) ) $line .= $th_h.usces_entity_decode($name, $ext).$th_f;
			}
		}
	}
	switch( $applyform ) {
	case 'JP':
		if( isset($_REQUEST['check']['delivery_zip']) ) $line .= $th_h.__('Shipping Zip', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_country']) ) $line .= $th_h.__('配送先国', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_pref']) ) $line .= $th_h.__('Shipping State', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_address1']) ) $line .= $th_h.__('Shipping City', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_address2']) ) $line .= $th_h.__('Shipping Address1', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_address3']) ) $line .= $th_h.__('Shipping Address2', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_tel']) ) $line .= $th_h.__('Shipping Phone', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_fax']) ) $line .= $th_h.__('Shipping FAX', 'usces').$th_f;
		break;
	case 'US':
	default:
		if( isset($_REQUEST['check']['delivery_address2']) ) $line .= $th_h.__('Shipping Address1', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_address3']) ) $line .= $th_h.__('Shipping Address2', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_address1']) ) $line .= $th_h.__('Shipping City', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_pref']) ) $line .= $th_h.__('Shipping State', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_country']) ) $line .= $th_h.__('配送先国', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_zip']) ) $line .= $th_h.__('Shipping Zip', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_tel']) ) $line .= $th_h.__('Shipping Phone', 'usces').$th_f;
		if( isset($_REQUEST['check']['delivery_fax']) ) $line .= $th_h.__('Shipping FAX', 'usces').$th_f;
		break;
	}
	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'fax_after' ) {
				$name = $entry['name'];
				$csde_key = 'csde_'.$key;
				if( isset($_REQUEST['check'][$csde_key]) ) $line .= $th_h.usces_entity_decode($name, $ext).$th_f;
			}
		}
	}
	//--------------------------------------------------------------------------
	if( isset($_REQUEST['check']['shipping_date']) ) $line .= $th_h.__('shpping date', 'usces').$th_f;
	if( isset($_REQUEST['check']['peyment_method']) ) $line .= $th_h.__('payment method', 'usces').$th_f;
	if( isset($_REQUEST['check']['delivery_method']) ) $line .= $th_h.__('shipping option', 'usces').$th_f;
	if( isset($_REQUEST['check']['delivery_date']) ) $line .= $th_h.__('Delivery date', 'usces').$th_f;
	if( isset($_REQUEST['check']['delivery_time']) ) $line .= $th_h.__('delivery time', 'usces').$th_f;
	if( isset($_REQUEST['check']['delidue_date']) ) $line .= $th_h.__('Shipping date', 'usces').$th_f;
	if( isset($_REQUEST['check']['status']) ) $line .= $th_h.__('Status', 'usces').$th_f;
	$line .= $th_h.__('Total Amount', 'usces').$th_f;
	if( isset($_REQUEST['check']['usedpoint']) ) $line .= $th_h.__('Used points', 'usces').$th_f;
	$line .= $th_h.__('Disnount', 'usces').$th_f;
	$line .= $th_h.__('Shipping', 'usces').$th_f;
	$line .= $th_h.apply_filters('usces_filter_cod_label', __('COD fee', 'usces')).$th_f;
	$line .= $th_h.__('consumption tax', 'usces').$th_f;
	if( isset($_REQUEST['check']['note']) ) $line .= $th_h.__('Notes', 'usces').$th_f;
	if( !empty($csod_meta) ) {
		foreach( $csod_meta as $key => $entry ) {
			$name = $entry['name'];
			$csod_key = 'csod_'.$key;
			if( isset($_REQUEST['check'][$csod_key]) ) $line .= $th_h.usces_entity_decode($name, $ext).$th_f;
		}
	}
	//--------------------------------------------------------------------------
	$line .= $th_h.__('item code', 'usces').$th_f;
	$line .= $th_h.__('SKU code', 'usces').$th_f;
	if(isset($_REQUEST['check']['item_name'])) $line .= $th_h.__('item name', 'usces').$th_f;
	if(isset($_REQUEST['check']['sku_name'])) $line .= $th_h.__('SKU display name ', 'usces').$th_f;
	if(isset($_REQUEST['check']['options'])) $line .= $th_h.__('options for items', 'usces').$th_f;
	$line .= $th_h.__('Quantity', 'usces').$th_f;
	$line .= $th_h.__('Unit price', 'usces').$th_f;
	if(isset($_REQUEST['check']['unit'])) $line .= $th_h.__('unit', 'usces').$th_f;
	$line .= $tr_f.$lf;
	//==========================================================================
	$tableName = $wpdb->prefix."usces_order";
	$ids = $_REQUEST['listcheck'];
	foreach( (array)$ids as $order_id ) {
		$query = $wpdb->prepare( "SELECT *, (order_item_total_price - order_usedpoint + order_discount + order_shipping_charge + order_cod_fee + order_tax) AS total_price FROM $tableName WHERE ID = %d", $order_id );
		$data = $wpdb->get_row( $query, ARRAY_A );
		$deli = unserialize($data['order_delivery']);

		$line_order  = $tr_h;
		$line_order .= $td_h1.$order_id.$td_f;
		$line_order .= $td_h.usces_get_deco_order_id( $order_id ).$td_f;
		$line_order .= $td_h.$data['order_date'].$td_f;
		if( isset($_REQUEST['check']['mem_id']) ) $line_order .= $td_h.$data['mem_id'].$td_f;
		if( isset($_REQUEST['check']['email']) ) $line_order .= $td_h.usces_entity_decode($data['order_email'], $ext).$td_f;
		if( !empty($cscs_meta) ) {
			foreach( $cscs_meta as $key => $entry ) {
				if( $entry['position'] == 'name_pre' ) {
					$name = $entry['name'];
					$cscs_key = 'cscs_'.$key;
					if( isset($_REQUEST['check'][$cscs_key]) ) {
						$value = maybe_unserialize( $usces->get_order_meta_value($cscs_key, $order_id) );
						if( empty($value) ) {
							$value = '';
						} elseif( is_array($value) ) {
							$concatval = '';
							$c = '';
							foreach( $value as $v ) {
								$concatval .= $c.$v;
								$c = ' ';
							}
							$value = $concatval;
						}
						$line_order .= $td_h.usces_entity_decode($value, $ext).$td_f;
					}
				}
			}
		}
		switch( $applyform ) {
		case 'JP': 
			$line_order .= $td_h.usces_entity_decode($data['order_name1'].' '.$data['order_name2'], $ext).$td_f;
			if( isset($_REQUEST['check']['kana']) ) $line_order .= $td_h.usces_entity_decode($data['order_name3'].' '.$data['order_name4'], $ext).$td_f;
			break;
		case 'US':
		default:
			$line_order .= $td_h.usces_entity_decode($data['order_name2'].' '.$data['order_name1'], $ext).$td_f;
			break;
		}
		if( !empty($cscs_meta) ) {
			foreach( $cscs_meta as $key => $entry ) {
				if( $entry['position'] == 'name_after' ) {
					$name = $entry['name'];
					$cscs_key = 'cscs_'.$key;
					if( isset($_REQUEST['check'][$cscs_key]) ) {
						$value = maybe_unserialize( $usces->get_order_meta_value($cscs_key, $order_id) );
						if( empty($value) ) {
							$value = '';
						} elseif( is_array($value) ) {
							$concatval = '';
							$c = '';
							foreach( $value as $v ) {
								$concatval .= $c.$v;
								$c = ' ';
							}
							$value = $concatval;
						}
						$line_order .= $td_h.usces_entity_decode($value, $ext).$td_f;
					}
				}
			}
		}
		switch( $applyform ) {
		case 'JP':
			if( isset($_REQUEST['check']['zip']) ) $line_order .= $td_h.usces_entity_decode($data['order_zip'], $ext).$td_f;
			if( isset($_REQUEST['check']['country']) ) $line_order .= $td_h.$usces_settings['country'][$usces->get_order_meta_value('customer_country', $order_id)].$td_f;
			if( isset($_REQUEST['check']['pref']) ) $line_order .= $td_h.usces_entity_decode($data['order_pref'], $ext).$td_f;
			if( isset($_REQUEST['check']['address1']) ) $line_order .= $td_h.usces_entity_decode($data['order_address1'], $ext).$td_f;
			if( isset($_REQUEST['check']['address2']) ) $line_order .= $td_h.usces_entity_decode($data['order_address2'], $ext).$td_f;
			if( isset($_REQUEST['check']['address3']) ) $line_order .= $td_h.usces_entity_decode($data['order_address3'], $ext).$td_f;
			if( isset($_REQUEST['check']['tel']) ) $line_order .= $td_h.usces_entity_decode($data['order_tel'], $ext).$td_f;
			if( isset($_REQUEST['check']['fax']) ) $line_order .= $td_h.usces_entity_decode($data['order_fax'], $ext).$td_f;
			break;
		case 'US':
		default:
			if( isset($_REQUEST['check']['address2']) ) $line_order .= $td_h.usces_entity_decode($data['order_address2'], $ext).$td_f;
			if( isset($_REQUEST['check']['address3']) ) $line_order .= $td_h.usces_entity_decode($data['order_address3'], $ext).$td_f;
			if( isset($_REQUEST['check']['address1']) ) $line_order .= $td_h.usces_entity_decode($data['order_address1'], $ext).$td_f;
			if( isset($_REQUEST['check']['pref']) ) $line_order .= $td_h.usces_entity_decode($data['order_pref'], $ext).$td_f;
			if( isset($_REQUEST['check']['country']) ) $line_order .= $td_h.$usces_settings['country'][$usces->get_order_meta_value('customer_country', $order_id)].$td_f;
			if( isset($_REQUEST['check']['zip']) ) $line_order .= $td_h.usces_entity_decode($data['order_zip'], $ext).$td_f;
			if( isset($_REQUEST['check']['tel']) ) $line_order .= $td_h.usces_entity_decode($data['order_tel'], $ext).$td_f;
			if( isset($_REQUEST['check']['fax']) ) $line_order .= $td_h.usces_entity_decode($data['order_fax'], $ext).$td_f;
			break;
		}
		if( !empty($cscs_meta) ) {
			foreach( $cscs_meta as $key => $entry ) {
				if( $entry['position'] == 'fax_after' ) {
					$name = $entry['name'];
					$cscs_key = 'cscs_'.$key;
					if( isset($_REQUEST['check'][$cscs_key]) ) {
						$value = maybe_unserialize( $usces->get_order_meta_value($cscs_key, $order_id) );
						if( empty($value) ) {
							$value = '';
						} elseif( is_array($value) ) {
							$concatval = '';
							$c = '';
							foreach( $value as $v ) {
								$concatval .= $c.$v;
								$c = ' ';
							}
							$value = $concatval;
						}
						$line_order .= $td_h.usces_entity_decode($value, $ext).$td_f;
					}
				}
			}
		}
		//----------------------------------------------------------------------
		if( !empty($csde_meta) ) {
			foreach( $csde_meta as $key => $entry ) {
				if( $entry['position'] == 'name_pre' ) {
					$name = $entry['name'];
					$csde_key = 'csde_'.$key;
					if( isset($_REQUEST['check'][$csde_key]) ) {
						$value = maybe_unserialize( $usces->get_order_meta_value($csde_key, $order_id) );
						if( empty($value) ) {
							$value = '';
						} elseif( is_array($value) ) {
							$concatval = '';
							$c = '';
							foreach( $value as $v ) {
								$concatval .= $c.$v;
								$c = ' ';
							}
							$value = $concatval;
						}
						$line_order .= $td_h.usces_entity_decode($value, $ext).$td_f;
					}
				}
			}
		}
		switch( $applyform ) {
		case 'JP':
			if( isset($_REQUEST['check']['delivery_name']) ) $line_order .= $td_h.usces_entity_decode($deli['name1'].' '.$deli['name2'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_kana']) ) $line_order .= $td_h.usces_entity_decode($deli['name3'].' '.$deli['name4'], $ext).$td_f;
			break;
		case 'US':
		default:
			if( isset($_REQUEST['check']['delivery_name']) ) $line_order .= $td_h.usces_entity_decode($deli['name2'].' '.$deli['name1'], $ext).$td_f;
			break;
		}
		if( !empty($csde_meta) ) {
			foreach( $csde_meta as $key => $entry ) {
				if( $entry['position'] == 'name_after' ) {
					$name = $entry['name']."</td>";
					$csde_key = 'csde_'.$key;
					if( isset($_REQUEST['check'][$csde_key]) ) {
						$value = maybe_unserialize( $usces->get_order_meta_value($csde_key, $order_id) );
						if( empty($value) ) {
							$value = '';
						} elseif( is_array($value) ) {
							$concatval = '';
							$c = '';
							foreach( $value as $v ) {
								$concatval .= $c.$v;
								$c = ' ';
							}
							$value = $concatval;
						}
						$line_order .= $td_h.usces_entity_decode($value, $ext).$td_f;
					}
				}
			}
		}
		switch( $applyform ) {
		case 'JP':
			if( isset($_REQUEST['check']['delivery_zip']) ) $line_order .= $td_h.usces_entity_decode($deli['zipcode'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_country']) ) $line_order .= $td_h.$usces_settings['country'][$deli['country']].$td_f;
			if( isset($_REQUEST['check']['delivery_pref']) ) $line_order .= $td_h.usces_entity_decode($deli['pref'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_address1']) ) $line_order .= $td_h.usces_entity_decode($deli['address1'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_address2']) ) $line_order .= $td_h.usces_entity_decode($deli['address2'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_address3']) ) $line_order .= $td_h.usces_entity_decode($deli['address3'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_tel']) ) $line_order .= $td_h.usces_entity_decode($deli['tel'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_fax']) ) $line_order .= $td_h.usces_entity_decode($deli['fax'], $ext).$td_f;
			break;
		case 'US':
		default:
			if( isset($_REQUEST['check']['delivery_address2']) ) $line_order .= $td_h.usces_entity_decode($deli['address2'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_address3']) ) $line_order .= $td_h.usces_entity_decode($deli['address3'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_address1']) ) $line_order .= $td_h.usces_entity_decode($deli['address1'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_pref']) ) $line_order .= $td_h.usces_entity_decode($deli['pref'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_country']) ) $line_order .= $td_h.$usces_settings['country'][$deli['country']].$td_f;
			if( isset($_REQUEST['check']['delivery_zip']) ) $line_order .= $td_h.usces_entity_decode($deli['zipcode'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_tel']) ) $line_order .= $td_h.usces_entity_decode($deli['tel'], $ext).$td_f;
			if( isset($_REQUEST['check']['delivery_fax']) ) $line_order .= $td_h.usces_entity_decode($deli['fax'], $ext).$td_f;
			break;
		}
		if( !empty($csde_meta) ) {
			foreach( $csde_meta as $key => $entry ) {
				if( $entry['position'] == 'fax_after' ) {
					$name = $entry['name'];
					$csde_key = 'csde_'.$key;
					if( isset($_REQUEST['check'][$csde_key]) ) {
						$value = maybe_unserialize( $usces->get_order_meta_value($csde_key, $order_id) );
						if( empty($value) ) {
							$value = '';
						} elseif( is_array($value) ) {
							$concatval = '';
							$c = '';
							foreach( $value as $v ) {
								$concatval .= $c.$v;
								$c = ' ';
							}
							$value = $concatval;
						}
						$line_order .= $td_h.usces_entity_decode($value, $ext).$td_f;
					}
				}
			}
		}
		//----------------------------------------------------------------------
		if( isset($_REQUEST['check']['shipping_date']) ) $line_order .= $td_h.$data['order_modified'].$td_f;
		if( isset($_REQUEST['check']['peyment_method']) ) $line_order .= $td_h.$data['order_payment_name'].$td_f;
		if( isset($_REQUEST['check']['delivery_method']) ) {
			$delivery_method = '';
			if( strtoupper($data['order_delivery_method']) == '#NONE#' ) {
				$delivery_method = __('No preference', 'usces');
			} else {
				foreach( (array)$usces->options['delivery_method'] as $dkey => $delivery ) {
					if( $delivery['id'] == $data['order_delivery_method'] ) {
						$delivery_method = $delivery['name'];
						break;
					}
				}
			}
			$line_order .= $td_h.$delivery_method.$td_f;
		}
		if( isset($_REQUEST['check']['delivery_date']) ) $line_order .= $td_h.$data['order_delivery_date'].$td_f;
		if( isset($_REQUEST['check']['delivery_time']) ) $line_order .= $td_h.$data['order_delivery_time'].$td_f;
		if( isset($_REQUEST['check']['delidue_date']) ) {
			$order_delidue_date = ( strtoupper($data['order_delidue_date']) == '#NONE#' ) ? '' : $data['order_delidue_date'];
			$line_order .= $td_h.$order_delidue_date.$td_f;
		}
		if( isset($_REQUEST['check']['status']) ) {
			$order_status = explode(',', $data['order_status']);
			$status = '';
			foreach( (array)$order_status as $os ) {
				if( isset($usces_management_status[$os]) ) 
					$status .= $usces_management_status[$os].$sp;
			}
			$line_order .= $td_h.trim($status, $sp).$td_f;
		}
		$line_order .= $td_h.usces_crform($data['total_price'], false, false, 'return', false).$td_f;
		if( isset($_REQUEST['check']['usedpoint']) ) $line_order .= $td_h.$data['order_usedpoint'].$td_f;
		$line_order .= $td_h.$data['order_discount'].$td_f;
		$line_order .= $td_h.$data['order_shipping_charge'].$td_f;
		$line_order .= $td_h.$data['order_cod_fee'].$td_f;
		$line_order .= $td_h.$data['order_tax'].$td_f;
		if( isset($_REQUEST['check']['note']) ) $line_order .= $td_h.usces_entity_decode($data['order_note'], $ext).$td_f;
		if( !empty($csod_meta) ) {
			foreach( $csod_meta as $key => $entry ) {
				$name = $entry['name'];
				$csod_key = 'csod_'.$key;
				if( isset($_REQUEST['check'][$csod_key]) ) {
					$value = maybe_unserialize( $usces->get_order_meta_value($csod_key, $order_id) );
					if( empty($value) ) {
						$value = '';
					} elseif( is_array($value) ) {
						$concatval = '';
						$c = '';
						foreach( $value as $v ) {
							$concatval .= $c.$v;
							$c = ' ';
						}
						$value = $concatval;
					}
					$line_order .= $td_h.usces_entity_decode($value, $ext).$td_f;
				}
			}
		}
		//----------------------------------------------------------------------
		$cart = unserialize($data['order_cart']);
		for( $i = 0; $i < count($cart); $i++ ) {
			$cart_row = $cart[$i];
			$post_id = $cart_row['post_id'];
			$sku = urldecode($cart_row['sku']);

			$line_cart  = $td_h.$usces->getItemCode($post_id).$td_f;
			$line_cart .= $td_h.$sku.$td_f;
			if(isset($_REQUEST['check']['item_name'])) $line_cart .= $td_h.usces_entity_decode($usces->getItemName($post_id), $ext).$td_f;
			if(isset($_REQUEST['check']['sku_name'])) $line_cart .= $td_h.usces_entity_decode($usces->getItemSkuDisp($post_id, $sku), $ext).$td_f;
			if(isset($_REQUEST['check']['options'])) {
				$options = $cart_row['options'];
				$optstr = '';
				if(is_array($options) && count($options) > 0) {
					foreach((array)$options as $key => $value) {
						if(!empty($key)) {
							if(is_array($value)) {
								foreach($value as $v) {
									$optstr .= usces_entity_decode(urldecode($key), $ext).$sp;
									foreach($value as $v) {
										$optstr .= usces_entity_decode(urldecode($v), $ext).$nb;
									}
								}
							} else {
								//$optstr .= usces_entity_decode($key, $ext).$sp.usces_entity_decode($value, $ext).$nb;
								$optstr .= usces_entity_decode(urldecode($key).$sp.urldecode($value), $ext).$nb;
							}
						}
					}
				}
				$line_cart .= $td_h.$optstr.$td_f;
			}
			$line_cart .= $td_h.$cart_row['quantity'].$td_f;
			$line_cart .= $td_h.usces_crform($cart_row['price'], false, false, 'return', false).$td_f;
			if(isset($_REQUEST['check']['unit'])) $line_cart .= $td_h.usces_entity_decode($usces->getItemSkuUnit($post_id, $sku), $ext).$td_f;
			$line .= $line_order.$line_cart.$tr_f.$lf;
		}
	}
	$line .= $table_f.$lf;
	//==========================================================================

	//if( $ext == 'xls' ) {
	//	header( "Content-Type: application/vnd.ms-excel; charset=Shift-JIS" );
	//} elseif( $ext == 'csv' ) {
		header( "Content-Type: application/octet-stream" );
	//}
	header( "Content-Disposition: attachment; filename=usces_shipping_list.".$ext );
	mb_http_output( 'pass' );
	print( mb_convert_encoding($line, "SJIS-win", "UTF-8") );
	exit();
}
?>
