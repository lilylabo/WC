<?php
require_once( USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__))."/continueMemberList.class.php" );
global $wpdb;

$tableName = $wpdb->prefix . "usces_order";
$arr_column = array(
			__('Order ID', 'dlseller') => 'ID', 
			__('Member ID', 'dlseller') => 'mem_id', 
			__('Name', 'dlseller') => 'name', 
			__('Limit of Card(Month/Year)', 'dlseller') => 'limitofcard', 
			__('Amount', 'dlseller') => 'price', 
			__('Settlement Supplier', 'dlseller') => 'order_payment_name', 
			__('Application Date', 'dlseller') => 'order_date', 
			__('Renewal Date', 'dlseller') => 'nextcontract', 
			__('Next Withdrawal Date', 'dlseller') => 'nextchargeday', 
			__('Condition', 'dlseller') => 'condition', 
			__('Status', 'dlseller') => 'status');

$DT = new dataList($tableName, $arr_column);
$res = $DT->MakeTable();
$arr_search = $DT->GetSearchs();
$arr_status = get_option('usces_management_status');
$arr_header = $DT->GetListheaders();
$dataTableNavigation = $DT->GetDataTableNavigation();
$rows = $DT->rows;
$status = $DT->get_action_status();
$message = $DT->get_action_message();
//$pref = get_option('usces_pref');
$pref = $usces->options['province'];
foreach ( (array)$usces->options['payment_method'] as $id => $array ) {
	$payment_name[$id] = $usces->options['payment_method'][$id]['name'];
}
$ums = get_option('usces_management_status');
foreach((array)$ums as $key => $value){
	if($key == 'noreceipt' || $key == 'receipted' || $key == 'pending'){
		$receipt_status[$key] = $value;
	}else{
		$order_status[$key] = $value;
	}
}
$order_status['new'] = __('new order', 'usces');
$curent_url = USCES_ADMIN_URL . '?' . $_SERVER['QUERY_STRING'];
?>
<script type="text/javascript">
jQuery(function($){
<?php if($status == 'success'){ ?>
			$("#anibox").animate({ backgroundColor: "#ECFFFF" }, 2000);
<?php }else if($status == 'caution'){ ?>
			$("#anibox").animate({ backgroundColor: "#FFF5CE" }, 2000);
<?php }else if($status == 'error'){ ?>
			$("#anibox").animate({ backgroundColor: "#FFE6E6" }, 2000);
<?php } ?>

//	$("#aAdditionalURLs").click(function () {
//		$("#AdditionalURLs").toggle();
//	});
	$("#mailSendDialog").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 650,
		width: 700,
		resizable: true,
		modal: true,
		buttons: {
			'<?php _e('close', 'usces'); ?>': function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#sendmailmessage").html( "" );
			$('#sendmailaddress').val('');
		}
	});
	$("#mailSendAlert").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 200,
		width: 200,
		resizable: false,
		modal: false
	});
	$("#sendmail").click(function() {
		uscesMail.sendMail();
	});
	uscesMail = {
		sendMail : function() {
			if($("#sendmailaddress").val() == "") return;
		
			var address = encodeURIComponent($("#sendmailaddress").val());
			var message = encodeURIComponent($("#sendmailmessage").val());
			var name = encodeURIComponent($("#sendmailname").val());
			var subject = encodeURIComponent($("#sendmailsubject").val());
			var order_id = $("#order_id").val();
			var member_id = $("#member_id").val();
			
			var s = uscesMail.settings;
			s.data = "action=dlseller_send_mail_ajax&mailaddress=" + address + "&message=" + message + "&name=" + name + "&subject=" + subject + "&oid=" + order_id + "&mid=" + member_id;
			s.success = function(data, dataType){
				if(data == 'success') {
					
					$('#mailSendAlert').dialog('option', 'buttons', {
															'OK': function() {
																	$(this).dialog('close');
																	$('#mailSendDialog').dialog('close');
																	location.href = $('#usces_referer').val();
																}
															});
					$('#mailSendAlert fieldset').dialog('option', 'title', 'SUCCESS');
					$('#mailSendAlert fieldset').html('<p><?php _e('E-mail has been sent.', 'usces'); ?></p>');
					$('#mailSendAlert').dialog('open');
					
					
					
				}else if(data == 'error'){
					$('#mailSendAlert').dialog('option', 'buttons', {
															'OK': function() {
																	$(this).dialog('close');
																}
															});
					$('#mailSendAlert fieldset').dialog('option', 'title', 'ERROR');
					$('#mailSendAlert fieldset').html('<p><?php _e('Failure in sending e-mails.', 'usces'); ?></p>');
					$('#mailSendAlert').dialog('open');
				}
			};
			s.error = function(data, dataType){
				$('#mailSendAlert').dialog('option', 'buttons', {
														'OK': function() {
																$(this).dialog('close');
															}
														});
				$('#mailSendAlert fieldset').dialog('option', 'title', 'ERROR');
				$('#mailSendAlert fieldset').html('<p><?php _e('Failure in sending e-mails.', 'usces'); ?></p>');
				$('#mailSendAlert').dialog('open');
			};
			$.ajax( s );
			return false;
		},
		getMailData : function( member_id, order_id ) {
			$("#order_id").val(order_id);
			$("#member_id").val(member_id);
			var p = uscesMail.settings;
			p.url = uscesL10n.requestFile;
			p.data = "action=dlseller_make_mail_ajax&order_id=" + order_id + "&member_id=" + member_id;
			p.success = function(data, dataType){
				if( 0 == data ) {
					alert('<?php _e('Data Error', 'dlseller'); ?>');
				}else{
				//alert(data);
					strs = data.split('#usces#');
					//alert(strs[1]);return;
					$("#sendmailaddress").val( strs[0] );
					$("#sendmailname").val( strs[1] );
					$("#sendmailsubject").val( strs[2] );
					$("#sendmailmessage").val( strs[3] );
					$('#mailSendDialog').dialog('option', 'title', '<?php _e('Update Request Email', 'dlseller'); ?>');
					$('#mailSendDialog').dialog('open');
				}
			};
			p.error = function(data, dataType){
				alert('<?php _e('Send Error', 'dlseller'); ?>');
			};
			$.ajax( p );
			return false;
		},
		settings: {
			url: uscesL10n.requestFile,
			type: 'POST',
			cache: false,
			success: function(data, dataType){
			}, 
			error: function(msg){
				//$("#ajax-response").html(msg);
			}
		}
	};
});


jQuery(document).ready(function($){
	$("table#mainDataTable tr:even").addClass("rowSelection_even");
	$("table#mainDataTable tr").hover(function() {
		$(this).addClass("rowSelection_hilight");
	},
	function() {
		$(this).removeClass("rowSelection_hilight");
	});
});

function jump(){
	location.href = "http://usctest.welcarthosting.net/future/wordpress/wp-admin/admin.php?page=usces_continue";
}
</script>

<div class="wrap">
<div class="usces_admin">
<form action="<?php echo USCES_ADMIN_URL.'?page=usces_continue'; ?>" method="post" name="tablesearch">

<h2>Welcart Management <?php _e('Continue Members','dlseller'); ?></h2>
<p class="version_info">Version <?php echo USCES_VERSION; ?></p>
<div id="aniboxStatus" class="<?php echo $status; ?>">
	<div id="anibox" class="clearfix">
		<img src="<?php echo USCES_PLUGIN_URL; ?>/images/list_message_<?php echo $status; ?>.gif" />
		<div class="mes" id="info_massage"><?php echo $message; ?></div>
	</div>
</div>

<div id="datatable">
<div id="tablenavi"><?php echo $dataTableNavigation ?></div>

<!--<div id="tablesearch">
<div id="searchBox">
		<table id="search_table">
		<tr>
		<td><?php _e('search fields', 'usces'); ?></td>
		<td><select name="search[column]" class="searchselect" id="searchselect">
		    <option value="none"> </option>
<?php foreach ((array)$arr_column as $key => $value):
		if($value == $arr_search['column']){
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}
		if($value == 'total_price') continue;
?>
		    <option value="<?php echo $value ?>"<?php echo $selected ?>><?php echo $key ?></option>
<?php endforeach; ?>
    	</select></td>
		<td id="searchlabel"></td>
		<td id="searchfield"></td>
		<td><input name="searchIn" type="submit" class="searchbutton" value="<?php _e('Search', 'usces'); ?>" />
		<input name="searchOut" type="submit" class="searchbutton" value="<?php _e('Cancellation', 'usces'); ?>" />
		<input name="searchSwitchStatus" id="searchSwitchStatus" type="hidden" value="<?php echo $DT->searchSwitchStatus; ?>" />
		</td>
		</tr>
		</table>
		<table id="period_table">
		<tr>
		<td><?php _e('Period', 'usces'); ?></td>
		<td><select name="search[period]" class="searchselect">
<?php foreach ((array)$DT->arr_period as $key => $value):
		if($key == $arr_search['period']){
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}
?>
		    <option value="<?php echo $key ?>"<?php echo $selected ?>><?php echo $value ?></option>
<?php endforeach; ?>
		</select></td>
		</tr>
		</table>
		<table id="change_table">
		<tr>
		<td><?php _e('Oparation in bulk', 'usces'); ?></td>
		<td><select name="allchange[column]" class="searchselect" id="changeselect">
		    <option value="none"> </option>
		    <option value="order_reciept"><?php _e('Edit the receiving money status', 'usces'); ?></option>
		    <option value="order_status"><?php _e('Edit of status process', 'usces'); ?></option>
		    <option value="delete"><?php _e('Delete in bulk', 'usces'); ?></option>
    	</select></td>
		<td id="changelabel"></td>
		<td id="changefield"></td>
		<td><input name="collective" type="submit" class="searchbutton" id="collective_change" value="<?php _e('start', 'usces'); ?>" />
		</td>
		</tr>
		</table>
		<input name="action" id="oederlistaction" type="hidden" />
</div>
</div>
-->
<table id="mainDataTable" cellspacing="1">
	<tr>
		<!--<th scope="col"><input name="allcheck" type="checkbox" value="" /></th>-->
<?php foreach ( (array)$arr_header as $value ) : ?>
		<th scope="col"><?php echo $value ?></th>
<?php endforeach; ?>
		<!--<th scope="col">&nbsp;</th>-->
	</tr>
<?php foreach ( (array)$rows as $array ) : ?>
	<tr>
	<!--<td><input name="listcheck[]" type="checkbox" value="<?php echo $array['ID']; ?>" /></td>-->
	<?php foreach ( (array)$array as $key => $value ) : ?>
		<?php if( $value == '' || $value == ' ' ) $value = '&nbsp;'; ?>
		<?php if( $key == 'ID' ): ?>
		<td class="center"><!--<a href="<?php echo USCES_ADMIN_URL.'?page=usces_continue&continue_action=edit&ID=' . $value.'&usces_referer='.urlencode($curent_url); ?>">--><?php echo $value; ?><!--</a>--></td>
		<?php elseif( $key == 'name' ): ?>
		<td class="left"><?php esc_html_e($value); ?></td>
		<?php elseif( $key == 'limitofcard' ): ?>
		<td class="center"><?php echo $value; ?><?php dlseller_upcard_url($array['mem_id'], $array['ID'], $array['limitofcard']); ?></td>
		<?php elseif( $key == 'mem_id' ): ?>
		<td class="center"><?php echo $value; ?></td>
		<?php elseif( $key == 'price' ): ?>
		<td class="price"><?php usces_crform($value, true, false); ?></td>
		<?php elseif( $key == 'order_payment_name' ): ?>
		<td class="left"><?php esc_html_e($value); ?></td>
		<?php elseif( $key == 'order_date' ): ?>
		<td class="center"><?php echo date(__('Y/m/d'), strtotime($value)); ?></td>
		<?php elseif( $key == 'interval' ): ?>
		<td class="center"><?php echo dlseller_next_contracting( $array['ID'] ); ?></td>
		<?php elseif( $key == 'chargingday' ): ?>
		<td class="center"><?php echo dlseller_next_charging( $array['ID'] ); ?></td>
		<?php elseif( $key == 'condition' ): ?>
		<td class="center"><?php echo $value; ?></td>
		<?php elseif( $key == 'status' && $value != 'continuation' ): ?>
		<td class="red center"><?php _e('cancellation', 'dlseller'); ?></td>
		<?php elseif( $key == 'status' && $value == 'continuation' ): ?>
		<td class="green center"><?php _e('continuation', 'dlseller'); ?></td>
		<?php endif; ?>
<?php endforeach; ?>
	<!--<td><a href="<?php echo USCES_ADMIN_URL.'?page=usces_continue&continue_action=delete&ID=' . $array['ID']; ?>" onclick="return deleteconfirm('<?php echo $array['ID']; ?>');"><span style="color:#FF0000; font-size:9px;"><?php _e('Delete', 'usces'); ?></span></a></td>-->
	</tr>
<?php endforeach; ?>
</table>

</div>
<!--<div class="chui">
<h3>受注詳細画面（作成中）について</h3>
<p>各行の受注番号をクリックすると受注詳細画面が表示されます。受注詳細画面では注文商品の追加、修正、削除など受注に関する全ての情報を編集することができま、問い合わせや電話での変更依頼に対応します。</p>
<p>「見積り」ステイタスを利用することで見積りをメール送信できます。見積書印刷でFAX対応も可能です。注文をいただいた場合は「受注」ステイタスに変更することで、見積りの内容がそのまま受注データとなります。</p>
<p>その他のステイタスには銀行振り込みの場合の「入金」ステイタス、発送完了した場合の「完了」、注文の「キャンセル」などがあり、各業務の終了後にステイタスを変更することを習慣付ければ、複数の担当者での業務もスムーズに行うことができます。</p>
</div>
-->
<input name="member_id" type="hidden" id="member_id" value="" />
<input name="order_id" type="hidden" id="order_id" value="" />
<input name="usces_referer" type="hidden" id="usces_referer" value="<?php echo $curent_url; ?>" />
</form>
</div><!--usces_admin-->
</div><!--wrap-->
<div id="mailSendDialog" title="">
	<div id="order-response"></div>
	<fieldset>
		<p><?php _e("Check the mail and click 'send'", 'usces'); ?></p>
		<label><?php _e('e-mail adress', 'usces'); ?></label><input type="text" name="sendmailaddress" id="sendmailaddress" class="text" /><br />
		<label><?php _e('Client name', 'usces'); ?></label><input type="text" name="sendmailname" id="sendmailname" class="text" /><br />
		<label><?php _e('subject', 'usces'); ?></label><input type="text" name="sendmailsubject" id="sendmailsubject" class="text" /><input name="sendmail" id="sendmail" type="button" value="<?php _e('send', 'usces'); ?>" /><br />
		<textarea name="sendmailmessage" id="sendmailmessage"></textarea>
		<input name="mailChecked" id="mailChecked" type="hidden" />
	</fieldset>
</div>
<div id="mailSendAlert" title="">
	<div id="order-response"></div>
	<fieldset>
	</fieldset>
</div>

<script type="text/javascript">
//	rowSelection("mainDataTable");
</script>
