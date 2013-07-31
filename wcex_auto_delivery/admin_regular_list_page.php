<?php
require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__))."/regularList.class.php" );
global $wpdb;

$tableName = $wpdb->prefix."usces_regular";
$arr_column = array(
	__('order number', 'usces') => 'order_id', 
	__('Regular ID', 'autodelivery') => 'ID', 
	__('membership number', 'usces') => 'mem_id', 
	__('name', 'usces') => 'name', 
	__('payment method', 'usces') => 'payment_name', 
	__('Order date', 'autodelivery') => 'order_date', 
	__('Scheduled order date', 'autodelivery') => 'schedule_date', 
	__('Scheduled shipment date', 'autodelivery') => 'schedule_shipment_date', 
	__('Scheduled delivery date', 'autodelivery') => 'schedule_delivery_date', 
	__('Condition', 'autodelivery') => 'regular_condition');
$DT = new dataList($tableName, $arr_column);
$res = $DT->MakeTable();
$arr_search = $DT->GetSearchs();
$arr_header = $DT->GetListheaders();
$dataTableNavigation = $DT->GetDataTableNavigation();
$rows = $DT->rows;
$status = $DT->get_action_status();
$message = $DT->get_action_message();
$curent_url = urlencode(USCES_ADMIN_URL.'?'.$_SERVER['QUERY_STRING']);
$payment_name = array();
$payments = usces_get_system_option( 'usces_payment_method', 'sort' );
foreach( (array)$payments as $id => $array ) {
	$payment_name[$id] = $array['name'];
}
$applyform = usces_get_apply_addressform( $usces->options['system']['addressform'] );
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

	$("#searchselect").change(function () {
		operation.change_search_field();
	});

	operation = {
		change_search_field : function() {
			var label = '';
			var html = '';
			var column = $("#searchselect").val();

			if( column == 'ID' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][ID]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['ID']) ? $arr_search['word']['ID'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'order_id' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][order_id]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['order_id']) ? $arr_search['word']['order_id'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'mem_id' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][mem_id]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['mem_id']) ? $arr_search['word']['mem_id'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'name' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][name]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['name']) ? $arr_search['word']['name'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'payment_name' ) {
				label = '';
				html = '<select name="search[word][payment_name]" class="searchselect">';
		<?php foreach( (array)$payment_name as $key => $value ) :
				$selected = ( isset($arr_search['word']['payment_name']) && $value == $arr_search['word']['payment_name'] ) ? ' selected="selected"' : '';
		?>
				html += '<option value="<?php echo esc_attr($value); ?>"<?php echo $selected; ?>><?php echo esc_html($value); ?></option>';
		<?php endforeach; ?>
				html += '</select>';
			} else if( column == 'order_date' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][order_date]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['order_date']) ? $arr_search['word']['order_date'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'schedule_date' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][schedule_date]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['schedule_date']) ? $arr_search['word']['schedule_date'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'schedule_shipment_date' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][schedule_shipment_date]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['schedule_shipment_date']) ? $arr_search['word']['schedule_shipment_date'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'schedule_delivery_date' ) {
				label = '<?php _e('key words', 'usces'); ?>';
				html = '<input name="search[word][schedule_delivery_date]" type="text" value="<?php echo esc_attr(isset($arr_search['word']['schedule_delivery_date']) ? $arr_search['word']['schedule_delivery_date'] : ''); ?>" class="searchword" maxlength="50" />';
			} else if( column == 'regular_condition' ) {
				label = '';
				html  = '<select name="search[word][regular_condition]" class="searchselect">';
				html += '<option value="continuation"<?php if( isset($arr_search['word']['regular_condition']) && 'continuation' == $arr_search['word']['regular_condition'] ) echo ' selected="selected"'; ?>><?php _e('continuation', 'autodelivery'); ?></option>';
				html += '<option value="stop"<?php if( isset($arr_search['word']['regular_condition']) && 'stop' == $arr_search['word']['regular_condition'] ) echo ' selected="selected"'; ?>><?php _e('stop', 'autodelivery'); ?></option>';
				html += '<option value="termination"<?php if( isset($arr_search['word']['regular_condition']) && 'termination' == $arr_search['word']['regular_condition'] ) echo ' selected="selected"'; ?>><?php _e('termination', 'autodelivery'); ?></option>';
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
});

function deleteconfirm( regular_id ) {
	if( confirm(<?php _e("'Are you sure of deleting a regular purchase id ' + regular_id + ' ?'", 'autodelivery'); ?>)) {
		return true;
	} else {
		return false;
	}
}
</script>

<div class="wrap">
<div class="usces_admin">

<h2>Welcart Management <?php _e('Regular Purchase List','autodelivery'); ?></h2>
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
<form action="<?php echo USCES_ADMIN_URL.'?page=usces_regularlist'; ?>" method="post" name="tablesearch">
	<table id="search_table">
	<tr>
	<td><?php _e('search fields', 'usces'); ?></td>
	<td><select name="search[column]" class="searchselect" id="searchselect">
		<option value="none"> </option>
<?php foreach( (array)$arr_column as $key => $value ) :
		if( $value == 'total_price' || $value == 'condition' ) continue;
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
	<input name="action" id="regularlistaction" type="hidden" />
</form>
</div>
</div>

<table id="mainDataTable" cellspacing="1">
	<tr>
<?php
	$list_header = '';
	foreach( (array)$arr_header as $value ) {
		$list_header .= '<th scope="col">'.$value.'</th>';
	}
	$list_header .= '<th scope="col">&nbsp;</th>';
	echo $list_header;
?>
	</tr>
<?php foreach( (array)$rows as $array ): ?>
	<tr>
<?php
		$list_detail = '';
		foreach( (array)$array as $key => $value ) {
			if( $value == '' || $value == ' ' ) $value = '&nbsp;';
			if( $key === 'ID' ) {
				if( $value == '&nbsp;' ) {
					$list_detail .= '<td>'.esc_html($value).'</td>';
				} else {
					$list_detail .= '<td><a href="'.USCES_ADMIN_URL.'?page=usces_regularlist&regular_action=edit&regular_id='.$array['ID'].'&usces_referer='.$curent_url.'">'.$value.'</a></td>';
				}
			} elseif( $key === 'order_id' ) {
				if( $value == '&nbsp;' ) {
					$list_detail .= '<td>'.esc_html($value).'</td>';
				} else {
					$list_detail .= '<td><a href="'.USCES_ADMIN_URL.'?page=usces_orderlist&order_action=edit&order_id='.$array['order_id'].'&usces_referer='.$curent_url.'">'.$value.'</a></td>';
				}
			} elseif( $key === 'mem_id' ) {
				if( $value == '0' ) $value = '&nbsp;';
				$list_detail .= '<td>'.esc_html($value).'</td>';
			} elseif( $key === 'name' ) {
				switch( $applyform ) {
				case 'JP': 
					break;
				case 'US':
				default:
					$names = explode(' ', $value);
					$value = $names[1].' '.$names[0];
				}
				$list_detail .= '<td>'.esc_html($value).'</td>';
			} elseif( $key === 'payment_name' ) {
				$list_detail .= '<td>'.esc_html($value).'</td>';
			} elseif( $key === 'order_date' || $key == 'schedule_date' || $key == 'schedule_shipment_date' || $key == 'schedule_delivery_date' ) {
				$list_detail .= '<td class="center">'.$value.'</td>';
			} elseif( $key === 'regular_condition' ) {
				switch( $value ) {
				case 'continuation': 
					$list_detail .= '<td class="green">'.__('continuation', 'autodelivery').'</td>'; break;
				case 'stop':
					$list_detail .= '<td>'.__('stop', 'autodelivery').'</td>'; break;
				case 'termination':
					$list_detail .= '<td>'.__('termination', 'autodelivery').'</td>'; break;
				}
			}
		}
		$list_detail .= '<td><a href="'.USCES_ADMIN_URL.'?page=usces_regularlist&regular_action=delete&regular_id='.$array['ID'].'" onclick="return deleteconfirm(\''.$array['ID'].'\');"><span style="color:#FF0000; font-size:9px;">'.__('Delete', 'usces').'</span></a></td>';
		echo $list_detail;
?>
	</tr>
<?php endforeach; ?>
</table>

</div>
<input name="usces_referer" type="hidden" id="usces_referer" value="<?php echo $curent_url; ?>" />

<!--</form>-->
</div><!--usces_admin-->
</div><!--wrap-->
