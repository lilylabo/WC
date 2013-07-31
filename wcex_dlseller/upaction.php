<?php
if ( !$this->is_member_logged_in() ) die();


$up_mode = (int)$_REQUEST['dlseller_up_mode'];//1:date,2:all
$order_id = (int)$_REQUEST['dlseller_order_id'];
$order = $this->get_order_data($order_id);
$payments = usces_get_payments_by_name($order['payment_name']);
$acting_flag = ( 'acting' == $payments['settlement'] ) ? $payments['module'] : $payments['settlement'];
$member = $this->get_member();

$member_id = $member['ID'];
$now = date('Ym', current_time('timestamp', 0));
$infos = $this->get_member_info( $member_id );
$limits = explode('/', $infos['limitofcard']);
$regd = date('Ym', strtotime(substr(current_time('mysql', 0), 0, 2) . $limits[1] . '-' . $limits[0] . '-01'));

$html = '<div id="memberpages">
<div class="whitebox">
<div class="header_explanation">';

if( $member_id != $order['mem_id'] ){
	$html .= 'オーダー情報が異なります。<br />管理者にお問い合わせください。';
}else if( $regd > $now && 1 === $up_mode ){
	$html .= 'カード情報の更新処理は完了いたしております。<br />ありがとうございました。今後ともよろしくお願いいたします。';
}else if( 2 === $up_mode ){

	$rand = '0000000' . sprintf('%010d', mt_rand(1, 9999999999));
	$this->save_order_acting_data($rand);
	
	$html .= '「更新処理を行なう」をクリックしますと決済会社のページが表示されますので、新しいカードの情報をご記入ください。<br />
	尚、この処理はクレジットカード有効期限などのカード情報を更新するもので、現在ご利用いただいていますサービスのご契約更新ではありません。<br />
	現在のご契約をご確認いただくには会員ページをご覧ください。';
	
	switch( $acting_flag ){
	
		case 'acting_remise_card':
	
			$acting_opts = $this->options['acting_settings']['remise'];
			$ac_memberid = $this->get_member_meta_value('remise_memid', $member['ID']);
			$limitofcard = explode('/', $this->get_member_meta_value('limitofcard', $member['ID']));
			$expire = substr(current_time('mysql', 0), 0, 2) . $limitofcard[1] . $limitofcard[0];
			$now = date('Ym', current_time('timestamp', 0));
			$job = ( $expire >= $now ) ? 'CHECK' : 'AUTH';
			
			$send_url = ('public' == $acting_opts['card_pc_ope']) ? $acting_opts['send_url_pc'] : $acting_opts['send_url_pc_test'];
			$html .= '<form name="purchase_form" action="' . $send_url . '" method="post" onKeyDown="if (event.keyCode == 13) {return false;}" accept-charset="Shift_JIS">';
			$html .= '<input type="hidden" name="SHOPCO" value="' . esc_attr($acting_opts['SHOPCO']) . '" />';
			$html .= '<input type="hidden" name="HOSTID" value="' . esc_attr($acting_opts['HOSTID']) . '" />';
			$html .= '<input type="hidden" name="REMARKS3" value="' . $acting_opts['REMARKS3'] . '" />';
			$html .= '<input type="hidden" name="S_TORIHIKI_NO" value="' . $rand . '" />';
			$html .= '<input type="hidden" name="JOB" value="' . $job . '" />';
			$html .= '<input type="hidden" name="MAIL" value="' . esc_attr($member['mailaddress1']) . '" />';
			$html .= '<input type="hidden" name="ITEM" value="0000990" />';
			$html .= '<input type="hidden" name="RETURL" value="' . USCES_CART_URL . $this->delim . 'acting=remise_card&acting_return=1&dlseller_update=1" />';
			$html .= '<input type="hidden" name="NG_RETURL" value="' . USCES_CART_URL . $this->delim . 'acting=remise_card&acting_return=0&dlseller_update=0" />';
			$html .= '<input type="hidden" name="EXITURL" value="' . USCES_CART_URL . $this->delim . 'dlseller_card_update=login&dlseller_order_id=' . $order_id . '" />';
			$html .= '<input type="hidden" name="TOTAL" value="' . usces_crform($order['end_price'], false, false, 'return', false) . '" />';
			$html .= '<input type="hidden" name="AMOUNT" value="' . usces_crform($order['end_price'], false, false, 'return', false) . '" />';
			$html .= '<input type="hidden" name="div" value="0">';
			$html .= '<input type="hidden" name="METHOD" value="10">';
			$html .= '<input type="hidden" name="OPT" value="dlseller_card_update">';
			
			$html .= '<input type="hidden" name="AUTOCHARGE" value="1">';
			$html .= '<input type="hidden" name="AC_MEMBERID" value="' . $ac_memberid . '" />';
			$html .= '<input type="hidden" name="AC_S_KAIIN_NO" value="' . $member['ID'] . '">';
			$html .= '<input type="hidden" name="AC_NAME" value="' . esc_attr($member['name1'] . $member['name2']) . '">';
			$html .= '<input type="hidden" name="AC_KANA" value="' . esc_attr($member['name3'] . $member['name4']) . '">';
			$html .= '<input type="hidden" name="AC_TEL" value="' . esc_attr(str_replace('-', '', mb_convert_kana($member['tel'], 'a', 'UTF-8'))) . '">';
			$html .= '<input type="hidden" name="AC_AMOUNT" value="' . usces_crform($order['end_price'], false, false, 'return', false) . '">';
			$html .= '<input type="hidden" name="AC_TOTAL" value="' . usces_crform($order['end_price'], false, false, 'return', false) . '">';
			//$html .= '<input type="hidden" name="AC_NEXT_DATE" value="' . date('Ymd', mktime(0,0,0,substr($nextdate, 5, 2)+1,1,substr($nextdate, 0, 4))) . '">';
			//$html .= '<input type="hidden" name="AC_INTERVAL" value="1M">';
		
			$html .= '<input type="hidden" name="dummy" value="&#65533;" />';
			$html .= '<div class="send"><input name="purchase" type="submit" class="checkout_button" value="'.__('更新処理を行なう', 'usces').'" onClick="document.charset=\'Shift_JIS\';" /></div>';
			$html = apply_filters('usces_filter_confirm_inform', $html);
			$html .= '</form>';
			break;
	}
}else{
	$html .= '不正な要求です。';
}
$html .= '</div></div></div>';
?>
