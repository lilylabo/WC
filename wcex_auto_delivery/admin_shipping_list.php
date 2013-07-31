<?php
require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__))."/shippingList.class.php" );
global $wpdb;

$tableName = $wpdb->prefix."usces_order";
$arr_column = array(
	__('ID', 'usces') => 'ID', 
	__('order number', 'usces') => 'deco_id', 
	__('Regular ID', 'autodelivery') => 'reg_id', 
	__('Order date', 'autodelivery') => 'date', 
	__('Shipping date', 'usces') => 'delidue_date', 
	__('Delivery date', 'usces') => 'delivery_date', 
	__('membership number', 'usces') => 'mem_id', 
	__('name', 'usces') => 'name', 
	__('shipping option', 'usces') => 'delivery_method', 
	__('payment method', 'usces') => 'payment_name', 
	__('settlement', 'autodelivery') => 'settltment_status' 
);
$DT = new dataList( $tableName, $arr_column );
$res = $DT->MakeTable();
$arr_search = $DT->GetSearchs();
$arr_header = $DT->GetListheaders();
$dataTableNavigation = $DT->GetDataTableNavigation();
$rows = $DT->rows;
$status = $DT->get_action_status();
$message = $DT->get_action_message();
$payment_name = array();
$payments = usces_get_system_option( 'usces_payment_method', 'sort' );
foreach( (array)$payments as $id => $array ) {
	$payment_name[$id] = $array['name'];
}
$csod_meta = usces_has_custom_field_meta('order');
$cscs_meta = usces_has_custom_field_meta('customer');
$csde_meta = usces_has_custom_field_meta('delivery');
$wcad_opt_shipping = get_option( 'wcad_opt_shipping' );
$chk_shp = ( !empty($wcad_opt_shipping['chk_shp']) ) ? $wcad_opt_shipping['chk_shp'] : array();
$applyform = usces_get_apply_addressform( $usces->options['system']['addressform'] );
$curent_url = urlencode( USCES_ADMIN_URL.'?'.$_SERVER['QUERY_STRING'] );
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

	$("input[name='allcheck']").click(function () {
		if( $(this).attr("checked") ){
			$("input[name*='listcheck']").attr({checked: true});
		}else{
			$("input[name*='listcheck']").attr({checked: false});
		}
	});

	$("#searchselect").change(function() {
		operation.change_search_field();
	});

	operation = {
		change_search_field : function() {
			var label = '';
			var html = '';
			var column = $("#searchselect").val();
			if( column == 'deco_id' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][deco_id]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['deco_id']) ? $arr_search['word']['deco_id'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'reg_id' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][reg_id]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['reg_id']) ? $arr_search['word']['reg_id'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'date' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][date]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['date']) ? $arr_search['word']['date'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'delidue_date' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][delidue_date]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['delidue_date']) ? $arr_search['word']['delidue_date'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'delivery_date' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][delivery_date]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['delivery_date']) ? $arr_search['word']['delivery_date'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'mem_id' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][mem_id]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['mem_id']) ? $arr_search['word']['mem_id'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'name' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][name]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['name']) ? $arr_search['word']['name'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'delivery_method' ) {
				label = '';
				html = '<select name="search[word][delivery_method]" class="searchselect">';
		<?php foreach( (array)$usces->options['delivery_method'] as $dkey => $dvalue ) {
				if( isset($arr_search['word']['delivery_method']) && $dvalue['id'] == $arr_search['word']['delivery_method'] ) {
					$dselected = ' selected="selected"';
				} else {
					$dselected = '';
				}
		?>
				html += '<option value="<?php echo esc_attr($dvalue['id']); ?>"<?php echo $dselected ?>><?php echo esc_html($dvalue['name']); ?></option>';
		<?php } ?>
				html += '</select>';
			} else if( column == 'payment_name' ) {
				label = '';
				html = '<select name="search[word][payment_name]" class="searchselect">';
		<?php foreach( (array)$payment_name as $pnkey => $pnvalue ) {
				if( isset($arr_search['word']['payment_name']) && $pnvalue == $arr_search['word']['payment_name'] ) {
					$pnselected = ' selected="selected"';
				} else {
					$pnselected = '';
				}
		?>
				html += '<option value="<?php echo esc_attr($pnvalue); ?>"<?php echo $pnselected ?>><?php echo esc_html($pnvalue); ?></option>';
		<?php } ?>
				html += '</select>';
			}

			$("#searchlabel").html( label );
			$("#searchfield").html( html );
		}
	};
});

function toggleVisibility( id ) {
	var e = document.getElementById(id);
	if( e.style.display == 'block' ) {
		e.style.display = 'none';
		document.getElementById("searchSwitchStatus").value = 'OFF';
	} else {
		e.style.display = 'block';
		document.getElementById("searchSwitchStatus").value = 'ON';
		document.getElementById("searchVisiLink").style.display = 'none';
	}
};

jQuery(document).ready(function($) {
	$("table#mainDataTable tr:even").addClass("rowSelection_even");
	$(".nodate").removeClass("rowSelection_even");
	$(".nodate").css( "background-color","#C0C0C0" );
	$(".excess").removeClass("rowSelection_even");
	$(".excess").css( "background-color","#FFCCCC" );
	$("table#mainDataTable tr").hover(
		function() {
			$(this).addClass("rowSelection_hilight");
		},
		function() {
			$(this).removeClass("rowSelection_hilight");
		}
	);
	if( $("#searchSwitchStatus").val() == 'OFF' ) {
		$("#searchBox").css("display", "none");
		$("#searchVisiLink").html('<?php _e('Show the Operation field', 'usces'); ?>');
	} else {
		$("#searchBox").css("display", "block");
		$("#searchVisiLink").css("display", "none");
	}

	operation.change_search_field();

	$("#dlShippingListDialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 620,
		width: 700,
		resizable: true,
		modal: true,
		buttons: {
			'<?php _e('close', 'usces'); ?>': function() {
				$(this).dialog('close');
			}
		},
		close: function() {
		}
	});

	$('#dl_shp').click(function() {
		var listcheck = "";
		$("input[name*=\'listcheck\']").each(function(i) {
			if( $(this).attr("checked") ) {
				listcheck += "&listcheck["+i+"]="+$(this).val();
			}
		});
		var args = "&search[column]="+$(':input[name="search[column]"]').val()
			+"&search[word]["+$("#searchselect").val()+"]="+$(':input[name="search[word]['+$("#searchselect").val()+']"]').val()
			+"&search[period]="+$(':input[name="search[period]"]').val()
			+"&searchSwitchStatus="+$(':input[name="searchSwitchStatus"]').val()
			+"&ftype=csv";
		$(".check_shipping").each(function(i) {
			if( $(this).attr('checked') ) {
				args += '&check['+$(this).val()+']=on';
			}
		});
		location.href = "<?php echo USCES_ADMIN_URL; ?>?page=usces_shippinglist&shipping_action=dlshippinglist&noheader=true"+args+listcheck;
	});
	$('#dl_shippinglist').click(function() {
		if( $("input[name*=\'listcheck\']:checked").length == 0 ) {
			alert("<?php _e('Choose the data.', 'usces'); ?>");
			$("#oederlistaction").val("");
			return false;
		}
		$('#dlShippingListDialog').dialog('open');
	});
});
</script>

<div class="wrap">
<div class="usces_admin">
<form action="<?php echo USCES_ADMIN_URL.'?page=usces_shippinglist'; ?>" method="post" name="tablesearch">

<h2>Welcart Management <?php _e('Shipping Schedule List','autodelivery'); ?></h2>
<p class="version_info">Version <?php echo WCEX_AUTO_DELIVERY_VERSION; ?></p>
<div id="aniboxStatus" class="<?php echo $status; ?>">
	<div id="anibox" class="clearfix">
		<img src="<?php echo USCES_PLUGIN_URL; ?>/images/list_message_<?php echo $status; ?>.gif" />
		<div class="mes" id="info_massage"><?php echo $message; ?></div>
	</div>
</div>

<div id="datatable">
<div id="tablenavi"><?php echo $dataTableNavigation; ?></div>

<div id="tablesearch">
<div id="searchBox">
	<table id="search_table">
	<tr>
	<td><?php _e('search fields', 'usces'); ?></td>
	<td><select name="search[column]" class="searchselect" id="searchselect">
	    <option value="none"> </option>
<?php foreach( (array)$arr_column as $key => $value ):
		if( $key == 'ID' ) continue;
		$selected = ( $value == $arr_search['column'] ) ? ' selected="selected"' : '';
?>
	    <option value="<?php echo esc_attr($value); ?>"<?php echo $selected; ?>><?php echo esc_html($key); ?></option>
<?php endforeach; ?>
	</select></td>
	<td id="searchlabel"></td>
	<td id="searchfield"></td>
	<td><input name="searchIn" type="submit" class="searchbutton" value="<?php _e('Search', 'usces'); ?>" />
	<input name="searchOut" type="submit" class="searchbutton" value="<?php _e('Cancellation', 'usces'); ?>" />
	<input name="searchSwitchStatus" id="searchSwitchStatus" type="hidden" value="<?php echo esc_attr($DT->searchSwitchStatus); ?>" />
	</td>
	</tr>
	</table>
	<input name="action" id="shippinglistaction" type="hidden" />
	<table id="dl_list_table">
	<tr>
	<td><input type="button" id="dl_shippinglist" class="searchbutton" value="<?php _e('Download Shipping List', 'autodelivery'); ?>" /></td>
	</tr>
	</table>
</div>
</div>

<table id="mainDataTable" cellspacing="1">
	<tr>
<?php
	$list_header = '<th scope="col"><input name="allcheck" type="checkbox" value="" /></th>';
	foreach( (array)$arr_header as $value ) {
		if( $value == 'ID' ) continue;
		$list_header .= '<th scope="col">'.$value.'</th>';
	}
	echo $list_header;
?>
	</tr>
<?php
	$today = date( 'Y-m-d', current_time('timestamp') );
	foreach( (array)$rows as $array ):
		$trclass = '';
		if( empty($array['delidue_date']) ) {
			$trclass = ' class="nodate"';
		} elseif( $today >= $array['delidue_date'] ) {
			$trclass = ' class="excess"';
		}
?>
	<tr<?php echo $trclass; ?>>
<?php
		$list_detail = '<td align="center"><input name="listcheck[]" type="checkbox" value="'.$array['ID'].'" /></td>';
		foreach( (array)$array as $key => $value ) {
			if( $value == '' || $value == ' ' || $value == '#none#' ) $value = '&nbsp;';
			if( $key === 'deco_id' ) {
				$list_detail .= '<td><a href="'.USCES_ADMIN_URL.'?page=usces_orderlist&order_action=edit&order_id='.$array['ID'].'&usces_referer='.$curent_url.'">'.esc_html($value).'</a></td>';
			} elseif( $key === 'reg_id' ) {
				if( $value == '&nbsp;' ) {
					$list_detail .= '<td>'.esc_html($value).'</td>';
				} else {
					$list_detail .= '<td><a href="'.USCES_ADMIN_URL.'?page=usces_regularlist&regular_action=edit&regular_id='.$array['reg_id'].'&usces_referer='.$curent_url.'">'.esc_html($value).'</a></td>';
				}
			} elseif( $key === 'date' ) {
				$list_detail .= '<td class="center">'.esc_html($value).'</td>';
			} elseif( $key === 'delidue_date' ) {
				if( !wcad_isdate($array['delidue_date']) ) $value = '&nbsp;';
				$list_detail .= '<td class="center">'.esc_html($value).'</td>';
			} elseif( $key === 'delivery_date' ) {
				if( !wcad_isdate($array['delivery_date']) ) $value = '&nbsp;';
				$list_detail .= '<td class="center">'.esc_html($value).'</td>';
			} elseif( $key === 'mem_id' ) {
				if( $value == '0' ) $value = '&nbsp;';
				$list_detail .= '<td>'.esc_html($value).'</td>';
			} elseif( $key === 'name' ) {
				switch( $applyform ) {
				case 'JP': 
					$list_detail .= '<td>'.esc_html($value).'</td>';
					break;
				case 'US':
				default:
					$names = explode(' ', $value);
					$list_detail .= '<td>'.esc_html($names[1].' '.$names[0]).'</td>';
				}
			} elseif( $key === 'delivery_method' ) {
				if( -1 != $value ) {
					$delivery_method_index = $usces->get_delivery_method_index($value);
					$value = $usces->options['delivery_method'][$delivery_method_index]['name'];
				} else {
					$value = '&nbsp;';
				}
				$list_detail .= '<td class="green">'.esc_html($value).'</td>';
			} elseif( $key === 'payment_name' ) {
				$list_detail .= '<td>'.esc_html($value).'</td>';
			} elseif( $key === 'settltment_status' ) {
				$list_detail .= '<td class="red">'.esc_html($value).'</td>';
			}
		}
		echo $list_detail;
?>
	</tr>
<?php endforeach; ?>
</table>

</div>

<div id="dlShippingListDialog" title="<?php _e('Download Shipping List', 'autodelivery'); ?>">
	<p><?php _e('Select the item you want, please press the download.', 'usces'); ?></p>
	<fieldset>
		<input type="button" id="dl_shp" value="<?php _e('Download', 'usces'); ?>" />
	</fieldset>
	<fieldset><legend><?php _e('Customer Information', 'usces'); ?></legend>
		<label for="chk_shp[ID]"><input type="checkbox" class="check_shipping" id="chk_shp[ID]" value="ID" checked disabled /><?php _e('ID', 'usces'); ?></label>
		<label for="chk_shp[deco_id]"><input type="checkbox" class="check_shipping" id="chk_shp[deco_id]" value="deco_id" checked disabled /><?php _e('Order number', 'usces'); ?></label>
		<label for="chk_shp[date]"><input type="checkbox" class="check_shipping" id="chk_shp[date]" value="date" checked disabled /><?php _e('order date', 'usces'); ?></label>
		<label for="chk_shp[mem_id]"><input type="checkbox" class="check_shipping" id="chk_shp[mem_id]" value="mem_id"<?php if($chk_shp['mem_id'] == 1) echo ' checked'; ?> /><?php _e('membership number', 'usces'); ?></label>
		<label for="chk_shp[email]"><input type="checkbox" class="check_shipping" id="chk_shp[email]" value="email"<?php if($chk_shp['email'] == 1) echo ' checked'; ?> /><?php _e('e-mail', 'usces'); ?></label>
<?php 
	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'name_pre' ) {
				$cscs_key = 'cscs_'.$key;
				$checked = ($chk_shp[$cscs_key] == 1) ? ' checked' : '';
				$name = esc_attr($entry['name']);
				echo '<label for="chk_shp['.$cscs_key.']"><input type="checkbox" class="check_shipping" id="chk_shp['.$cscs_key.']" value="'.$cscs_key.'"'.$checked.' />'.$name.'</label>'."\n";//20111116ysk 0000302
			}
		}
	}
?>
		<label for="chk_shp[name]"><input type="checkbox" class="check_shipping" id="chk_shp[name]" value="name" checked disabled /><?php _e('name', 'usces'); ?></label>
<?php 
	switch( $applyform ) {
	case 'JP':
?>
		<label for="chk_shp[kana]"><input type="checkbox" class="check_shipping" id="chk_shp[kana]" value="kana"<?php if($chk_shp['kana'] == 1) echo ' checked'; ?> /><?php _e('furigana', 'usces'); ?></label>
<?php 
		break;
	}

	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'name_after' ) {
				$cscs_key = 'cscs_'.$key;
				$checked = ($chk_shp[$cscs_key] == 1) ? ' checked' : '';
				$name = esc_attr($entry['name']);
				echo '<label for="chk_shp['.$cscs_key.']"><input type="checkbox" class="check_shipping" id="chk_shp['.$cscs_key.']" value="'.$cscs_key.'"'.$checked.' />'.$name.'</label>'."\n";//20111116ysk 0000302
			}
		}
	}

	switch( $applyform ) {
	case 'JP':
?>
		<label for="chk_shp[zip]"><input type="checkbox" class="check_shipping" id="chk_shp[zip]" value="zip"<?php if($chk_shp['zip'] == 1) echo ' checked'; ?> /><?php _e('Zip/Postal Code', 'usces'); ?></label>
		<label for="chk_shp[country]"><input type="checkbox" class="check_shipping" id="chk_shp[country]" value="country"<?php if($chk_shp['country'] == 1) echo ' checked'; ?> /><?php _e('Country', 'usces'); ?></label>
		<label for="chk_shp[pref]"><input type="checkbox" class="check_shipping" id="chk_shp[pref]" value="pref"<?php if($chk_shp['pref'] == 1) echo ' checked'; ?> /><?php _e('Province', 'usces'); ?></label>
		<label for="chk_shp[address1]"><input type="checkbox" class="check_shipping" id="chk_shp[address1]" value="address1"<?php if($chk_shp['address1'] == 1) echo ' checked'; ?> /><?php _e('city', 'usces'); ?></label>
		<label for="chk_shp[address2]"><input type="checkbox" class="check_shipping" id="chk_shp[address2]" value="address2"<?php if($chk_shp['address2'] == 1) echo ' checked'; ?> /><?php _e('numbers', 'usces'); ?></label>
		<label for="chk_shp[address3]"><input type="checkbox" class="check_shipping" id="chk_shp[address3]" value="address3"<?php if($chk_shp['address3'] == 1) echo ' checked'; ?> /><?php _e('building name', 'usces'); ?></label>
		<label for="chk_shp[tel]"><input type="checkbox" class="check_shipping" id="chk_shp[tel]" value="tel"<?php if($chk_shp['tel'] == 1) echo ' checked'; ?> /><?php _e('Phone number', 'usces'); ?></label>
		<label for="chk_shp[fax]"><input type="checkbox" class="check_shipping" id="chk_shp[fax]" value="fax"<?php if($chk_shp['fax'] == 1) echo ' checked'; ?> /><?php _e('FAX number', 'usces'); ?></label>
<?php 
		break;
	case 'US':
	default:
?>
		<label for="chk_shp[address2]"><input type="checkbox" class="check_shipping" id="chk_shp[address2]" value="address2"<?php if($chk_shp['address2'] == 1) echo ' checked'; ?> /><?php _e('Address Line1', 'usces'); ?></label>
		<label for="chk_shp[address3]"><input type="checkbox" class="check_shipping" id="chk_shp[address3]" value="address3"<?php if($chk_shp['address3'] == 1) echo ' checked'; ?> /><?php _e('Address Line2', 'usces'); ?></label>
		<label for="chk_shp[address1]"><input type="checkbox" class="check_shipping" id="chk_shp[address1]" value="address1"<?php if($chk_shp['address1'] == 1) echo ' checked'; ?> /><?php _e('city', 'usces'); ?></label>
		<label for="chk_shp[pref]"><input type="checkbox" class="check_shipping" id="chk_shp[pref]" value="pref"<?php if($chk_shp['pref'] == 1) echo ' checked'; ?> /><?php _e('State', 'usces'); ?></label>
		<label for="chk_shp[country]"><input type="checkbox" class="check_shipping" id="chk_shp[country]" value="country"<?php if($chk_shp['country'] == 1) echo ' checked'; ?> /><?php _e('Country', 'usces'); ?></label>
		<label for="chk_shp[zip]"><input type="checkbox" class="check_shipping" id="chk_shp[zip]" value="zip"<?php if($chk_shp['zip'] == 1) echo ' checked'; ?> /><?php _e('Zip', 'usces'); ?></label>
		<label for="chk_shp[tel]"><input type="checkbox" class="check_shipping" id="chk_shp[tel]" value="tel"<?php if($chk_shp['tel'] == 1) echo ' checked'; ?> /><?php _e('Phone number', 'usces'); ?></label>
		<label for="chk_shp[fax]"><input type="checkbox" class="check_shipping" id="chk_shp[fax]" value="fax"<?php if($chk_shp['fax'] == 1) echo ' checked'; ?> /><?php _e('FAX number', 'usces'); ?></label>
<?php 
		break;
	}

	if( !empty($cscs_meta) ) {
		foreach( $cscs_meta as $key => $entry ) {
			if( $entry['position'] == 'fax_after' ) {
				$cscs_key = 'cscs_'.$key;
				$checked = ($chk_shp[$cscs_key] == 1) ? ' checked' : '';
				$name = esc_attr($entry['name']);
				echo '<label for="chk_shp['.$cscs_key.']"><input type="checkbox" class="check_shipping" id="chk_shp['.$cscs_key.']" value="'.$cscs_key.'"'.$checked.' />'.$name.'</label>'."\n";//20111116ysk 0000302
			}
		}
	}
?>
	</fieldset>
	<fieldset><legend><?php _e('Shipping address information', 'usces'); ?></legend>
<?php 
	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'name_pre' ) {
				$csde_key = 'csde_'.$key;
				$checked = ($chk_shp[$csde_key] == 1) ? ' checked' : '';
				$name = esc_attr($entry['name']);
				echo '<label for="chk_shp['.$csde_key.']"><input type="checkbox" class="check_shipping" id="chk_shp['.$csde_key.']" value="'.$csde_key.'"'.$checked.' />'.$name.'</label>'."\n";//20111116ysk 0000302
			}
		}
	}
?>
		<label for="chk_shp[delivery_name]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_name]" value="delivery_name"<?php if($chk_shp['delivery_name'] == 1) echo ' checked'; ?> /><?php _e('name', 'usces'); ?></label>
<?php 
	switch( $applyform ) {
	case 'JP':
?>
		<label for="chk_shp[delivery_kana]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_kana]" value="delivery_kana"<?php if($chk_shp['delivery_kana'] == 1) echo ' checked'; ?> /><?php _e('furigana', 'usces'); ?></label>
<?php 
		break;
	}

	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'name_after' ) {
				$csde_key = 'csde_'.$key;
				$checked = ($chk_shp[$csde_key] == 1) ? ' checked' : '';
				$name = esc_attr($entry['name']);
				echo '<label for="chk_shp['.$csde_key.']"><input type="checkbox" class="check_shipping" id="chk_shp['.$csde_key.']" value="'.$csde_key.'"'.$checked.' />'.$name.'</label>'."\n";//20111116ysk 0000302
			}
		}
	}

	switch( $applyform ) {
	case 'JP':
?>
		<label for="chk_shp[delivery_zip]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_zip]" value="delivery_zip"<?php if($chk_shp['delivery_zip'] == 1) echo ' checked'; ?> /><?php _e('Zip/Postal Code', 'usces'); ?></label>
		<label for="chk_shp[delivery_country]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_country]" value="delivery_country"<?php if($chk_shp['delivery_country'] == 1) echo ' checked'; ?> /><?php _e('Country', 'usces'); ?></label>
		<label for="chk_shp[delivery_pref]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_pref]" value="delivery_pref"<?php if($chk_shp['delivery_pref'] == 1) echo ' checked'; ?> /><?php _e('Province', 'usces'); ?></label>
		<label for="chk_shp[delivery_address1]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_address1]" value="delivery_address1"<?php if($chk_shp['delivery_address1'] == 1) echo ' checked'; ?> /><?php _e('city', 'usces'); ?></label>
		<label for="chk_shp[delivery_address2]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_address2]" value="delivery_address2"<?php if($chk_shp['delivery_address2'] == 1) echo ' checked'; ?> /><?php _e('numbers', 'usces'); ?></label>
		<label for="chk_shp[delivery_address3]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_address3]" value="delivery_address3"<?php if($chk_shp['delivery_address3'] == 1) echo ' checked'; ?> /><?php _e('building name', 'usces'); ?></label>
		<label for="chk_shp[delivery_tel]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_tel]" value="delivery_tel"<?php if($chk_shp['delivery_tel'] == 1) echo ' checked'; ?> /><?php _e('Phone number', 'usces'); ?></label>
		<label for="chk_shp[delivery_fax]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_fax]" value="delivery_fax"<?php if($chk_shp['delivery_fax'] == 1) echo ' checked'; ?> /><?php _e('FAX number', 'usces'); ?></label>
<?php 
		break;
	case 'US':
	default:
?>
		<label for="chk_shp[delivery_address2]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_address2]" value="delivery_address2"<?php if($chk_shp['delivery_address2'] == 1) echo ' checked'; ?> /><?php _e('Address Line1', 'usces'); ?></label>
		<label for="chk_shp[delivery_address3]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_address3]" value="delivery_address3"<?php if($chk_shp['delivery_address3'] == 1) echo ' checked'; ?> /><?php _e('Address Line2', 'usces'); ?></label>
		<label for="chk_shp[delivery_address1]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_address1]" value="delivery_address1"<?php if($chk_shp['delivery_address1'] == 1) echo ' checked'; ?> /><?php _e('city', 'usces'); ?></label>
		<label for="chk_shp[delivery_pref]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_pref]" value="delivery_pref"<?php if($chk_shp['delivery_pref'] == 1) echo ' checked'; ?> /><?php _e('State', 'usces'); ?></label>
		<label for="chk_shp[delivery_country]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_country]" value="delivery_country"<?php if($chk_shp['delivery_country'] == 1) echo ' checked'; ?> /><?php _e('Country', 'usces'); ?></label>
		<label for="chk_shp[delivery_zip]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_zip]" value="delivery_zip"<?php if($chk_shp['delivery_zip'] == 1) echo ' checked'; ?> /><?php _e('Zip', 'usces'); ?></label>
		<label for="chk_shp[delivery_tel]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_tel]" value="delivery_tel"<?php if($chk_shp['delivery_tel'] == 1) echo ' checked'; ?> /><?php _e('Phone number', 'usces'); ?></label>
		<label for="chk_shp[delivery_fax]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_fax]" value="delivery_fax"<?php if($chk_shp['delivery_fax'] == 1) echo ' checked'; ?> /><?php _e('FAX number', 'usces'); ?></label>
<?php 
		break;
	}

	if( !empty($csde_meta) ) {
		foreach( $csde_meta as $key => $entry ) {
			if( $entry['position'] == 'fax_after' ) {
				$csde_key = 'csde_'.$key;
				$checked = ($chk_shp[$csde_key] == 1) ? ' checked' : '';
				$name = esc_attr($entry['name']);
				echo '<label for="chk_shp['.$csde_key.']"><input type="checkbox" class="check_shipping" id="chk_shp['.$csde_key.']" value="'.$csde_key.'"'.$checked.' />'.$name.'</label>'."\n";//20111116ysk 0000302
			}
		}
	}
?>
	</fieldset>
	<fieldset><legend><?php _e('Order Infomation', 'usces'); ?></legend>
		<label for="chk_shp[shipping_date]"><input type="checkbox" class="check_shipping" id="chk_shp[shipping_date]" value="shipping_date"<?php if($chk_shp['shipping_date'] == 1) echo ' checked'; ?> /><?php _e('shpping date', 'usces'); ?></label>
		<label for="chk_shp[peyment_method]"><input type="checkbox" class="check_shipping" id="chk_shp[peyment_method]" value="peyment_method"<?php if($chk_shp['peyment_method'] == 1) echo ' checked'; ?> /><?php _e('payment method','usces'); ?></label>
		<label for="chk_shp[delivery_method]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_method]" value="delivery_method"<?php if($chk_shp['delivery_method'] == 1) echo ' checked'; ?> /><?php _e('shipping option','usces'); ?></label>
		<label for="chk_shp[delivery_date]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_date]" value="delivery_date"<?php if($chk_shp['delivery_date'] == 1) echo ' checked'; ?> /><?php _e('Delivery date','usces'); ?></label>
		<label for="chk_shp[delivery_time]"><input type="checkbox" class="check_shipping" id="chk_shp[delivery_time]" value="delivery_time"<?php if($chk_shp['delivery_time'] == 1) echo ' checked'; ?> /><?php _e('delivery time','usces'); ?></label>
		<label for="chk_shp[delidue_date]"><input type="checkbox" class="check_shipping" id="chk_shp[delidue_date]" value="delidue_date"<?php if($chk_shp['delidue_date'] == 1) echo ' checked'; ?> /><?php _e('Shipping date', 'usces'); ?></label>
		<label for="chk_shp[status]"><input type="checkbox" class="check_shipping" id="chk_shp[status]" value="status"<?php if($chk_shp['status'] == 1) echo ' checked'; ?> /><?php _e('Status', 'usces'); ?></label>
		<label for="chk_shp[total_amount]"><input type="checkbox" class="check_shipping" id="chk_shp[total_amount]" value="total_amount" checked disabled /><?php _e('Total Amount', 'usces'); ?></label>
		<label for="chk_shp[usedpoint]"><input type="checkbox" class="check_shipping" id="chk_shp[usedpoint]" value="usedpoint"<?php if($chk_shp['usedpoint'] == 1) echo ' checked'; ?> /><?php _e('Used points', 'usces'); ?></label>
		<label for="chk_shp[discount]"><input type="checkbox" class="check_shipping" id="chk_shp[discount]" value="discount" checked disabled /><?php _e('Disnount', 'usces'); ?></label>
		<label for="chk_shp[shipping_charge]"><input type="checkbox" class="check_shipping" id="chk_shp[shipping_charge]" value="shipping_charge" checked disabled /><?php _e('Shipping', 'usces'); ?></label>
		<label for="chk_shp[cod_fee]"><input type="checkbox" class="check_shipping" id="chk_shp[cod_fee]" value="cod_fee" checked disabled /><?php echo apply_filters('usces_filter_cod_label', __('COD fee', 'usces')); ?></label>
		<label for="chk_shp[tax]"><input type="checkbox" class="check_shipping" id="chk_shp[tax]" value="tax" checked disabled /><?php _e('consumption tax', 'usces'); ?></label>
		<label for="chk_shp[note]"><input type="checkbox" class="check_shipping" id="chk_shp[note]" value="note"<?php if($chk_shp['note'] == 1) echo ' checked'; ?> /><?php _e('Notes', 'usces'); ?></label>
<?php 
	if( !empty($csod_meta) ) {
		foreach( $csod_meta as $key => $entry ) {
			$csod_key = 'csod_'.$key;
			$checked = (isset($chk_shp[$csod_key]) && $chk_shp[$csod_key] == 1) ? ' checked' : '';
			$name = esc_attr($entry['name']);
			echo '<label for="chk_shp['.$csod_key.']"><input type="checkbox" class="check_shipping" id="chk_shp['.$csod_key.']" value="'.$csod_key.'"'.$checked.' />'.$name.'</label>'."\n";//20111116ysk 0000302
		}
	}
?>
	</fieldset>
	<fieldset><legend><?php _e('Product Information', 'usces'); ?></legend>
		<label for="chk_shp[item_code]"><input type="checkbox" class="check_shipping" id="chk_shp[item_code]" value="item_code" checked disabled /><?php _e('item code', 'usces'); ?></label>
		<label for="chk_shp[sku_code]"><input type="checkbox" class="check_shipping" id="chk_shp[sku_code]" value="sku_code" checked disabled /><?php _e('SKU code', 'usces'); ?></label>
		<label for="chk_shp[item_name]"><input type="checkbox" class="check_shipping" id="chk_shp[item_name]" value="item_name"<?php if($chk_shp['item_name'] == 1) echo ' checked'; ?> /><?php _e('item name', 'usces'); ?></label>
		<label for="chk_shp[sku_name]"><input type="checkbox" class="check_shipping" id="chk_shp[sku_name]" value="sku_name"<?php if($chk_shp['sku_name'] == 1) echo ' checked'; ?> /><?php _e('SKU display name ', 'usces'); ?></label>
		<label for="chk_shp[options]"><input type="checkbox" class="check_shipping" id="chk_shp[options]" value="options"<?php if($chk_shp['options'] == 1) echo ' checked'; ?> /><?php _e('options for items', 'usces'); ?></label>
		<label for="chk_shp[quantity]"><input type="checkbox" class="check_shipping" id="chk_shp[quantity]" value="quantity" checked disabled /><?php _e('Quantity','usces'); ?></label>
		<label for="chk_shp[price]"><input type="checkbox" class="check_shipping" id="chk_shp[price]" value="price" checked disabled /><?php _e('Unit price','usces'); ?></label>
		<label for="chk_shp[unit]"><input type="checkbox" class="check_shipping" id="chk_shp[unit]" value="unit"<?php if($chk_shp['unit'] == 1) echo ' checked'; ?> /><?php _e('unit', 'usces'); ?></label>
	</fieldset>
</div>

</form>
</div><!--usces_admin-->
</div><!--wrap-->
