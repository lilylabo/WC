<?php
// Template Tags
function wcmb_change_tag($str, $tag1, $attr1, $tag2, $attr2, $out=''){
	if( 3 <= wcmb_get_browser() ){
		$html = '<'.$tag1.' '.$attr1.'>'.esc_html($str).'</'.$tag1.'>';
	}else{
		$html = '<'.$tag2.' '.$attr2.'>'.esc_html($str).'</'.$tag2.'>';
	}
	
	if( $out == 'return' ){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_change_style($style1, $style2, $out=''){
	if( 3 <= wcmb_get_browser() ){
		$html = ' class="'.$style1.'"';
	}else{
		$html = ' style="'.$style2.'"';
	}
	
	if( $out == 'return' ){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_assistance_item($post_id, $title ){
	if (usces_get_assistance_id_list($post_id)) :
		$assistanceposts = new wp_query( array('post__in'=>usces_get_assistance_ids($post_id)) );
		if($assistanceposts->have_posts()) :
		add_filter( 'excerpt_length', 'welcart_assistance_excerpt_length' );
		add_filter( 'excerpt_mblength', 'welcart_assistance_excerpt_mblength' );
?>
	<div class="assistance_item">
		<h3><?php echo $title; ?></h3>
<?php
		while ($assistanceposts->have_posts()) :
			$assistanceposts->the_post();
			usces_remove_filter();
			usces_the_item();
			$post = get_post(get_the_ID());
			ob_start();
?>
			<div class="listbox">
				<div class="title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php usces_the_itemName(); ?></a></div>
				<?php if (usces_is_skus()) : ?>
				<div class="price"><?php _e('$', 'usces'); ?><?php usces_the_firstPrice(); ?></div>
				<?php endif; ?>
				<?php if (!usces_have_zaiko_anyone()) : ?>
				<div class="status">売り切れ</div>
				<?php endif; ?>
			</div>
		<?php 
			$list = ob_get_contents();
			ob_end_clean();
			echo apply_filters('wcmb_filter_assistance_item_list', $list, $post);
		endwhile; ?>
	</div><!-- end of assistance_item -->
<?php 
		wp_reset_query();
		usces_reset_filter();
		remove_filter( 'excerpt_length', 'welcart_assistance_excerpt_length' );
		remove_filter( 'excerpt_mblength', 'welcart_assistance_excerpt_mblength' );
		endif;
	endif;
}

function wcmb_the_itemGpExp( $out = '' ) {
	global $post, $usces;
	$post_id = $post->ID;
	$sku = $usces->itemsku['code'];
	$GpN1 = $usces->getItemGpNum1($post_id);
	$GpN2 = $usces->getItemGpNum2($post_id);
	$GpN3 = $usces->getItemGpNum3($post_id);
	$GpD1 = $usces->getItemGpDis1($post_id);
	$GpD2 = $usces->getItemGpDis2($post_id);
	$GpD3 = $usces->getItemGpDis3($post_id);
	$unit = $usces->getItemSkuUnit($post_id, $sku);
	$price = $usces->getItemPrice($post_id, $sku);
	$unit = !empty($unit) ? $unit : '個';

	if( ($usces->itemsku['gp'] == 0) || empty($GpN1) || empty($GpD1) ){
		return;
	}
	$html = "<div class='gp'><div class='gp_title'>".apply_filters( 'usces_filter_itemGpExp_title', __('Business package discount','usces'))."</div>\n";
	if(!empty($GpN1) && !empty($GpD1)) {
		if(empty($GpN2) || empty($GpD2)) {
			$html .= '<div class="gp_row">'.$GpN1.esc_html__($unit).'以上で単価<span class="price">'.usces_crform(round($price * (100 - $GpD1) / 100), true, false, 'return').'</span></div>';
		} else {
			$html .= '<div class="gp_row">'.$GpN1.'～'.($GpN2-1).esc_html__($unit).'で単価<span class="price">'.usces_crform(round($price * (100 - $GpD1) / 100), true, false, 'return').'</span></div>';
			if(empty($GpN3) || empty($GpD3)) {
				$html .= '<div class="gp_row">'.$GpN2.esc_html__($unit).'以上で単価<span class="price">'.usces_crform(round($price * (100 - $GpD2) / 100), true, false, 'return').'</span></div>';
			} else {
				$html .= '<div class="gp_row">'.$GpN2.'～'.($GpN3-1).esc_html__($unit).'で単価<span class="price">'.usces_crform(round($price * (100 - $GpD2) / 100), true, false, 'return').'</span></div>';
				$html .= '<div class="gp_row">'.$GpN3.esc_html__($unit).'以上で単価<span class="price">'.usces_crform(round($price * (100 - $GpD3) / 100), true, false, 'return').'</span></div>';
			}
		}
	}
	$html .= "</div>";

	if( $out == 'return' ){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_doctype(){
	global $wcmb;
	
	switch($wcmb['device_div']){
		case DOCOMO:
			$doc = '<?xml version="1.0" encoding="Shift_JIS"?>'."\n".'<!DOCTYPE html PUBLIC "-//i-mode group (ja)//DTD XHTML i-XHTML(Locale/Ver.=ja/1.0) 1.0//EN" "i-xhtml_4ja_10.dtd">';
			break;
		case SOFTBANK:
			$doc = '<?xml version="1.0" encoding="Shift_JIS" ?>'."\n".'<!DOCTYPE html PUBLIC "-//JPHONE//DTD XHTML Basic 1.0 Plus//EN" "xhtml-basic10-plus.dtd">';
			break;
		case KDDI:
			$doc = '<!DOCTYPE html PUBLIC "-//OPENWAVE//DTD XHTML 1.0//EN" "http://www.openwave.com/DTD/xhtml-basic.dtd">';
			break;
		default:
			$doc = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			break;
	}
	echo $doc;
}

function wcmb_head(){
	do_action('wcmb_head');
}

function wcmb_charset(){
	echo 'Shift-JIS';
}

function wcmb_logo(){
	global $wcmb_options;
	if( is_home() || is_front_page() ){
		if( 'allimage' == $wcmb_options['garak_logo'] || 'topimage' == $wcmb_options['garak_logo'] ){
			echo '<img src="'.$wcmb_options['garak_logo_uri'].'" alt="'.esc_attr(get_bloginfo('name')).'" />';
		}else{
			bloginfo('name');
		}
	}else{
		if( 'allimage' == $wcmb_options['garak_logo'] ){
			echo '<img src="'.$wcmb_options['garak_logo_uri'].'" alt="'.esc_attr(get_bloginfo('name')).'" />';
		}else{
			bloginfo('name');
		}
	}
}

function wcmb_description(){
	global $wcmb_options;
	if( $wcmb_options['garak_description'] )
		echo '<div class="discprition">'.esc_attr(get_bloginfo('description')).'</div>';
}

function wcmb_head_telop(){
	global $wcmb_options;
	
	if( !empty($wcmb_options['garak_telop']) ) {
		$telop = wcmb_change_tag($wcmb_options['garak_telop'], 'div', 'class="telop"', 'div', 'style="background-color:#555; color:#FFF; display:-wap-marquee;"', 'return');
		$telop = apply_filters('wcmb_filter_telop', $telop, $wcmb_options['garak_telop']);
		echo $telop;
	}
}

function wcmb_getAddDay($year, $month, $day, $num) {
	$dateAry = getdate(mktime(0, 0, 0, $month, $day + $num, $year));
	return array($dateAry['year'], $dateAry['mon'], $dateAry['mday']);
}

function wcmb_delivery_field_input() {
	global $usces;

	$usces_entries = $usces->cart->get_entry();
	$selected_delivery_method = $usces_entries['order']['delivery_method'];
	$selected_delivery_date = $usces_entries['order']['delivery_date'];
	$selected_delivery_time = urldecode($usces_entries['order']['delivery_time']);

	$delivery_date_options = '';
	$delivery_time_options = '';
	$delivery_time_limit_message = '';

	$delivery_after_days = (!empty($usces->options['delivery_after_days'])) ? (int)$usces->options['delivery_after_days'] : 15;
	$delivery_method_index = $usces->get_delivery_method_index($selected_delivery_method);
	$delivery_method = $usces->options['delivery_method'][$delivery_method_index];

	//カートに入っている商品の発送日目安
	$shipping = 0;
	$cart = $usces->cart->get_cart();
	for($i = 0; $i < count($cart); $i++) {
		$cart_row = $cart[$i];
		$post_id = $cart_row['post_id'];
		$itemShipping = $usces->getItemShipping($post_id);
		if($itemShipping == 0 or $itemShipping == 9) {
			$shipping = 0;
			break;
		}
		if($shipping < $itemShipping) $shipping = $itemShipping;
	}
//20120312ysk start 0000436
	$days = (int)$delivery_method['days'];
	if(0 <= $days) {
//20120312ysk end
	switch($shipping) {
	case 0://指定なし
	case 9://商品入荷後
		$delivery_date_options = '<option value="'.__('There is not a choice.', 'usces').'">'.__('There is not a choice.', 'usces')."</option>\n";
		break;
	default:
		$sendout = usces_get_send_out_date();
		//配達日数に設定されている県毎の日数
		$delivery_days_value = 0;
		$delivery_days = $usces->options['delivery_days'];
		//$days = (int)$delivery_method['days'];
		//if(0 <= $days) {
			for($i = 0; $i < count((array)$delivery_days); $i++) {
				if((int)$delivery_days[$i]['id'] == $days) {
					$delivery_days_value = (int)$delivery_days[$i]['value'][$usces_entries['delivery']['pref']];
					break;
				}
			}
		//}
		$year = $sendout['sendout_date']['year'];
		$month = $sendout['sendout_date']['month'];
		$day = $sendout['sendout_date']['day'];
		//配達日数加算
		if(0 < $delivery_days_value) {
			list($year, $month, $day) = wcmb_getAddDay($year, $month, $day, $delivery_days_value);
		}
		//最短配送時間帯メッセージ
		$shortest_delivery_time = (int)$usces->options['shortest_delivery_time'];
		$date_str = sprintf("%04d-%02d-%02d", $year, $month, $day);
		switch($shortest_delivery_time) {
		case 0://指定しない
			//$delivery_time_limit_message = __('最短 '.$date_str.' からご指定いただけます。', 'usces');
			break;
		case 1://午前着可
			$delivery_time_limit_message = __('最短 '.$date_str.' の午前中からご指定いただけます。', 'usces');
			break;
		case 2://午前着不可
			$delivery_time_limit_message = __('最短 '.$date_str.' の午後からご指定いただけます。', 'usces');
			break;
		default:
			$delivery_time_limit_message = "";
		}
		$delivery_date_options .= '<option value="'.__('No preference', 'usces').'">'.__('No preference', 'usces')."</option>\n";
		for($i = 0; $i < $delivery_after_days; $i++) {
			$date_str = sprintf("%04d-%02d-%02d", $year, $month, $day);
			$selected = ($date_str == $selected_delivery_date) ? ' selected' : '';
			$delivery_date_options .= '<option value="'.$date_str.'"'.$selected.'>'.$date_str."</option>\n";
			list($year, $month, $day) = getNextDay($year, $month, $day);
		}
	}
//20120312ysk start 0000436
	}
	if( $delivery_date_options == '' ) {
		$delivery_date_options = '<option value="'.__('There is not a choice.', 'usces').'">'.__('There is not a choice.', 'usces')."</option>\n";
	}
//20120312ysk end

	$lines = explode("\n", $delivery_method['time']);
	foreach((array)$lines as $line) {
		if(trim($line) != '') {
			$selected = (trim($line) === trim($selected_delivery_time)) ? ' selected' : '';
			$delivery_time_options .= '<option value="'.urlencode(trim($line)).'"'.$selected.'>'.trim($line)."</option>\n";
		}
	}
	if($delivery_time_options == '') {
		$delivery_time_options = '<option value="'.__('There is not a choice.', 'usces').'">'.__('There is not a choice.', 'usces')."</option>\n";
	}
?>
		<tr><th scope="row"><?php _e('Delivery date', 'usces'); ?></th></tr>
		<tr><td>
			<select name='offer[delivery_date]' id='delivery_date_select' class='delivery_date'><?php echo $delivery_date_options; ?></select>
			</td>
		</tr>
		<tr><th scope="row"><?php _e('Delivery Time', 'usces'); ?></th></tr>
		<tr><td>
			<div id='delivery_time_limit_message'><?php echo $delivery_time_limit_message; ?></div>
			<select name='offer[delivery_time]' id='delivery_time_select' class='delivery_time'><?php echo $delivery_time_options; ?></select>
			</td>
		</tr>
<?php
}

function wcmb_get_cart_button( $out = '' ) {
	global $usces;
	$html = '';

	if( usces_is_cart() ) {
		if( usces_is_membersystem_state() ) {
			if( wcmb_is_member_logged_in() ) {
				$html .= '<input name="customerinfologin" type="submit" class="to_customerinfo_button" value="'.__(' Next ','usces').'" /><br />';
			} else {
				$html .= '<input name="customerinfologin" type="submit" class="to_customerinfo_button" value="'.__('ログインして次へ','usces').'" /><br />';
				$html .= '<input name="customercountry" type="submit" class="to_customerinfo_button" value="'.__('会員でない方はこちら','usces').'" /><br />';
			}
		} else {
			$html .= '<input name="customercountry" type="submit" class="to_customerinfo_button" value="'.__(' Next ','usces').'" /><br />';
		}
	}
	$html .= '<input name="previous" type="submit" id="previouscart" class="continue_shopping_button" value="'.__('continue shopping','usces').'" />';

	if($out == 'return'){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_get_customer_button( $out = '' ) {
	global $usces, $member_regmode;
	$html = '';

	$button = '<input name="deliveryinfo" type="submit" class="to_deliveryinfo_button" value="'.__(' Next ', 'usces').'" /><br />';
	$html .= apply_filters('usces_filter_customer_button', $button);

	if(usces_is_membersystem_state() && $member_regmode != 'editmemberfromcart' && usces_is_login() == false ){
		$html .= '<input name="reganddeliveryinfo" type="submit" class="to_reganddeliveryinfo_button" value="'.__('To the next while enrolling', 'usces').'"'.apply_filters('usces_filter_customerinfo_prebutton', NULL).' /><br />';
	}elseif(usces_is_membersystem_state() && $member_regmode == 'editmemberfromcart' ){
		$html .= '<input name="reganddeliveryinfo" type="submit" class="to_reganddeliveryinfo_button" value="'.__('Revise member information, and to next', 'usces').'"'.apply_filters('usces_filter_customerinfo_nextbutton', NULL).' /><br />';
	}
	$html .= '<input name="backcustomercountry" type="submit" class="back_cart_button" value="'.__('Back', 'usces').'" />';

	if($out == 'return'){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_member_button() {
	$html = '
		<input name="editmember" type="submit" value="'. __('update it', 'usces').'" /><br />
		<input name="deletemember" type="submit" value="'.__('delete it', 'usces').'" /><br />
		<input name="go2top" type="submit" value="'.__('Back to the top page.', 'usces').'" />';
	echo $html;
}

function wcmb_delivery_secure_form( $out = '' ) {
	global $usces, $usces_entries, $usces_carts;
	$html = '';

	$entry = $usces->cart->get_entry();
	$payment = $usces->getPayments( $entry['order']['payment_name'] );

	switch( $payment['settlement'] ){
		case 'acting_zeus_card':
			$paymod_id = 'zeus';
			
			if( 'on' != $usces->options['acting_settings'][$paymod_id]['card_activate'] 
				|| 'on' != $usces->options['acting_settings'][$paymod_id]['activate'] )
				continue;
			
			$cnum1 = isset( $_POST['cnum1'] ) ? esc_html($_POST['cnum1']) : '';
			$cnum2 = isset( $_POST['cnum2'] ) ? esc_html($_POST['cnum2']) : '';
			$cnum3 = isset( $_POST['cnum3'] ) ? esc_html($_POST['cnum3']) : '';
			$cnum4 = isset( $_POST['cnum4'] ) ? esc_html($_POST['cnum4']) : '';
			$securecode = isset( $_POST['securecode'] ) ? esc_html($_POST['securecode']) : '';
			$expyy = isset( $_POST['expyy'] ) ? esc_html($_POST['expyy']) : '';
			$expmm = isset( $_POST['expmm'] ) ? esc_html($_POST['expmm']) : '';
			$username = isset( $_POST['username'] ) ? esc_html($_POST['username']) : '';
			$howpay = isset( $_POST['howpay'] ) ? esc_html($_POST['howpay']) : '1';
			$cbrand = isset( $_POST['cbrand'] ) ? esc_html($_POST['cbrand']) : '';
			$div = isset( $_POST['div'] ) ? esc_html($_POST['div']) : '';
			
			$html .= '<input type="hidden" name="acting" value="zeus">'."\n";
			$html .= '<table class="customer_form" id="' . $paymod_id . '">'."\n";
			
			$pcid = NULL;
			if( wcmb_is_member_logged_in() ){
				$member = $usces->get_member();
				$pcid = $usces->get_member_meta_value('zeus_pcid', $member['ID']);
			}
			if( '2' == $usces->options['acting_settings'][$paymod_id]['security'] && 'on' == $usces->options['acting_settings'][$paymod_id]['quickcharge'] && $pcid != NULL ){
				$html .= '<input name="cnum1" type="hidden" value="8888888888888888" />
				<input name="expyy" type="hidden" value="2010" />
				<input name="expmm" type="hidden" value="01" />
				<input name="username" type="hidden" value="QUICKCHARGE" />';
				
			}else{
				$html .= '
					<tr><th scope="row">'.__('カード番号', 'usces').'<input name="acting" type="hidden" value="zeus" /></th></tr>
					<tr><td colspan="2"><input name="cnum1" type="text" size="16" value="' . esc_attr($cnum1) . '" '.wcmb_set_istyle( WCMB_ISTYLE_NUM, 'return').'/>(半角数字のみ)</td></tr>';
				if( '1' == $usces->options['acting_settings'][$paymod_id]['security'] ){
					$html .= '
					<tr><th scope="row">'.__('セキュリティコード', 'usces').'</th></tr>
					<tr><td colspan="2"><input name="securecode" type="text" size="6" value="' . esc_attr($securecode) . '" '.wcmb_set_istyle( WCMB_ISTYLE_NUM, 'return').'/>(半角数字のみ)</td></tr>';
				}
				$html .= '
					<tr><th scope="row">'.__('カード有効期限', 'usces').'</th></tr>
					<tr><td colspan="2">
					<select name="expyy">
						<option value=""' . (empty($expyy) ? ' selected="selected"' : '') . '>------</option>
					';
				for($i=0; $i<10; $i++){
					$year = date('Y') - 1 + $i;
					$html .= '<option value="' . $year . '"' . (($year == $expyy) ? ' selected="selected"' : '') . '>' . $year . '</option>';
				}
				$html .= '
					</select>年 
					<select name="expmm">
						<option value=""' . (empty($expmm) ? ' selected="selected"' : '') . '>----</option>
						<option value="01"' . (('01' == $expmm) ? ' selected="selected"' : '') . '> 1</option>
						<option value="02"' . (('02' == $expmm) ? ' selected="selected"' : '') . '> 2</option>
						<option value="03"' . (('03' == $expmm) ? ' selected="selected"' : '') . '> 3</option>
						<option value="04"' . (('04' == $expmm) ? ' selected="selected"' : '') . '> 4</option>
						<option value="05"' . (('05' == $expmm) ? ' selected="selected"' : '') . '> 5</option>
						<option value="06"' . (('06' == $expmm) ? ' selected="selected"' : '') . '> 6</option>
						<option value="07"' . (('07' == $expmm) ? ' selected="selected"' : '') . '> 7</option>
						<option value="08"' . (('08' == $expmm) ? ' selected="selected"' : '') . '> 8</option>
						<option value="09"' . (('09' == $expmm) ? ' selected="selected"' : '') . '> 9</option>
						<option value="10"' . (('10' == $expmm) ? ' selected="selected"' : '') . '>10</option>
						<option value="11"' . (('11' == $expmm) ? ' selected="selected"' : '') . '>11</option>
						<option value="12"' . (('12' == $expmm) ? ' selected="selected"' : '') . '>12</option>
					</select>月</td>
					</tr>
					<tr><th scope="row">'.__('カード名義', 'usces').'</th></tr>
					<tr><td colspan="2"><input name="username" type="text" size="30" value="' . esc_attr($username) . '" '.wcmb_set_istyle( WCMB_ISTYLE_ALP, 'return').'/>(半角英字)</td></tr>';
			}
			
			if( 'on' == $usces->options['acting_settings'][$paymod_id]['howpay'] ){
				$html .= '
					<tr id="cbrand_zeus"><th scope="row">'.__('支払方法', 'usces').'</th></tr>
					<tr><td colspan="2">
						<div>
							<input type="radio" name="cbrand" value="1"'.(('1' == $cbrand) ? ' checked="checked"' : '').' />JCB・VISA・MASTER　
							<select name="div_1" id="brand1">
								<option value="01"'.(('01' == $cbrand) ? ' selected="selected"' : '').'>一括払い</option>
								<option value="99"'.(('99' == $cbrand) ? ' selected="selected"' : '').'>リボ払い</option>
								<option value="03"'.(('03' == $cbrand) ? ' selected="selected"' : '').'>3回</option>
								<option value="05"'.(('05' == $cbrand) ? ' selected="selected"' : '').'>5回</option>
								<option value="06"'.(('06' == $cbrand) ? ' selected="selected"' : '').'>6回</option>
								<option value="10"'.(('10' == $cbrand) ? ' selected="selected"' : '').'>10回</option>
								<option value="12"'.(('12' == $cbrand) ? ' selected="selected"' : '').'>12回</option>
								<option value="15"'.(('15' == $cbrand) ? ' selected="selected"' : '').'>15回</option>
								<option value="18"'.(('18' == $cbrand) ? ' selected="selected"' : '').'>18回</option>
								<option value="20"'.(('20' == $cbrand) ? ' selected="selected"' : '').'>20回</option>
								<option value="24"'.(('24' == $cbrand) ? ' selected="selected"' : '').'>24回</option>
							</select>
						</div>
						<div>
							<input type="radio" name="cbrand" value="2"'.(('2' == $cbrand) ? ' checked="checked"' : '').' />DINERS　
							<select name="div_2" id="brand2">
								<option value="01"'.(('01' == $cbrand) ? ' selected="selected"' : '').'>一括払い</option>
								<option value="99"'.(('99' == $cbrand) ? ' selected="selected"' : '').'>リボ払い</option>
							</select>
						</div>
						<div>
							<input type="radio" name="cbrand" value="3"'.(('3' == $cbrand) ? ' checked="checked"' : '').' />AMEX　
							<select name="div_3" id="brand3">
								<option value="01"'.(('01' == $cbrand) ? ' selected="selected"' : '').'>一括払いのみ</option>
							</select>
						</div>
						</td>
					</tr>';
			}
			$html .= '
				</table>';
			break;
			
		case 'acting_zeus_conv':
			$paymod_id = 'zeus';
			
			if( 'on' != $usces->options['acting_settings'][$paymod_id]['conv_activate'] 
				|| 'on' != $usces->options['acting_settings'][$paymod_id]['activate'] )
				continue;
				
				
			$pay_cvs = isset( $_POST['pay_cvs'] ) ? esc_html($_POST['pay_cvs']) : 'D001';
			
			$html .= '
				<table class="customer_form" id="'.$paymod_id.'_conv">
					<tr><th scope="row">'.__('お支払いに利用するコンビニ', 'usces').'</th></tr>
					<tr><td>
						<select name="pay_cvs" id="pay_cvs_zeus">
							<option value="D001"'.(('D001' == $pay_cvs) ? ' selected="selected"' : '').'>セブンイレブン</option>
							<option value="D002"'.(('D002' == $pay_cvs) ? ' selected="selected"' : '').'>ローソン</option>
							<option value="D030"'.(('D030' == $pay_cvs) ? ' selected="selected"' : '').'>ファミリーマート</option>
							<option value="D040"'.(('D040' == $pay_cvs) ? ' selected="selected"' : '').'>サークルKサンクス</option>
							<option value="D015"'.(('D015' == $pay_cvs) ? ' selected="selected"' : '').'>セイコーマート</option>
						</select>
						</td>
					</tr>
				</table>';
			break;
			
		case 'acting_remise_card':
			$paymod_id = 'remise';
			$charging_type = $usces->getItemChargingType($usces_carts[0]['post_id']);

			if( 'on' != $usces->options['acting_settings'][$paymod_id]['card_activate'] 
				|| 'on' != $usces->options['acting_settings'][$paymod_id]['howpay'] 
				|| 'on' != $usces->options['acting_settings'][$paymod_id]['activate'] 
				|| 'continue' == $charging_type )
				continue;
				
			$div = isset( $_POST['div'] ) ? esc_html($_POST['div']) : '0';
			
			$html .= '
				<table class="customer_form" id="'.$paymod_id.'">
					<tr><th scope="row">'.__('支払方法', 'usces').'</th></tr>
					<tr><td>
						<select name="div" id="div_remise">
							<option value="0"'.(('0' == $div) ? ' selected="selected"' : '').'>　一括払い</option>
							<option value="1"'.(('1' == $div) ? ' selected="selected"' : '').'>　2回払い</option>
							<option value="2"'.(('2' == $div) ? ' selected="selected"' : '').'>　リボ払い</option>
						</select>
						</td>
					</tr>
				</table>';
			break;
	}

	if($out == 'return'){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_the_itemQuant( $out = '' ) {
	global $usces, $post;
	$post_id = $post->ID;
	$unit = $usces->getItemSkuUnit($post_id, $usces->itemsku['code']);
	$itemRestriction = $usces->getItemRestriction($post_id);
	$itemRestriction = ( '' == $itemRestriction ) ? ITEM_RESTRICTION : $itemRestriction;
	$value = isset( $_SESSION['usces_singleitem']['quant'][$post_id][$usces->itemsku['code']] ) ? $_SESSION['usces_singleitem']['quant'][$post_id][$usces->itemsku['code']] : 1;
	$quant = '<select name="quant['.$post_id.']['.esc_attr($usces->itemsku['code']).']">';
	for( $i=1; $i<=$itemRestriction; $i++){
		$quant .= '<option value="'.$i.'"'.($i == $value ? ' selected="selected"' : '').'>'.$i.esc_html($unit).'</option>';
	}
	$quant .= '</select>';
	$html = apply_filters('wcmb_filter_the_itemQuant', $quant, $post);
	
	if( $out == 'return' ){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_the_itemOption( $name, $label = '#default#', $out = '' ) {
	global $post, $usces;
	$post_id = $post->ID;
	$session_value = isset( $_SESSION['usces_singleitem']['itemOption'][$post_id][$usces->itemsku['code']][$name] ) ? $_SESSION['usces_singleitem']['itemOption'][$post_id][$usces->itemsku['code']][$name] : NULL;
	
	if($label == '#default#')
		$label = $name;

	$opts = usces_get_opts($post_id, 'name');
	if(!$opts)
		return false;
	
	$opt = $opts[$name];
	$means = (int)$opt['means'];
	$essential = (int)$opt['essential'];

	//$sku = esc_attr($usces->itemsku['code']);
	$sku = esc_attr(urlencode($usces->itemsku['code']));
	$optcode = esc_attr(urlencode($name));
	$name = esc_attr($name);
	$label = esc_attr($label);
	$html = "\n<label for='itemOption[{$post_id}][{$sku}][{$optcode}]' class='iopt_label'>{$label}</label>\n";
	switch($means) {
	case 0://Single-select
		$selects = explode("\n", $opt['value']);
		$html .= "\n<select name='itemOption[{$post_id}][{$sku}][{$optcode}]' id='itemOption[{$post_id}][{$sku}][{$optcode}]' class='iopt_select' onKeyDown=\"if (event.keyCode == 13) {return false;}\">\n";
		if($essential == 1){
			if( '#NONE#' == $session_value || NULL == $session_value ) 
				$selected = ' selected="selected"';
			else
				$selected = '';
			$html .= "\t<option value='#NONE#'{$selected}>".__('Choose','usces')."</option>\n";
		}
		$i=0;
		foreach($selects as $v) {
			if( ($i == 0 && $essential == 0 && NULL == $session_value) || esc_attr($v) == $session_value ) 
				$selected = ' selected="selected"';
			else
				$selected = '';
			$html .= "\t<option value='".esc_attr($v)."'{$selected}>".esc_html($v)."</option>\n";
			$i++;
		}
		$html .= "</select>\n";
		break;
	case 1://Multi-select
		$selects = explode("\n", $opt['value']);
		foreach($selects as $v) {
			if(is_array($session_value)) {
				$checked = (array_key_exists($v, $data)) ? ' checked="checked"' : '';
			} else {
				$checked = ($session_value == $v) ? ' checked="checked"' : '';
			}
			$html .= "\n<input name='itemOption[{$post_id}][{$sku}][{$optcode}][".esc_attr($v)."]' type='checkbox' id='itemOption[{$post_id}][{$sku}][{$optcode}][".esc_attr($v)."]' value='".esc_attr($v)."'".$checked."><label for='itemOption[{$post_id}][{$sku}][{$optcode}][".esc_attr($v)."]' class='iopt_label'>".esc_html($v)."</label>";
		}
		break;
	case 2://Text
		$html .= "\n<input name='itemOption[{$post_id}][{$sku}][{$optcode}]' type='text' id='itemOption[{$post_id}][{$sku}][{$optcode}]' class='iopt_text' onKeyDown=\"if (event.keyCode == 13) {return false;}\" value=\"".esc_attr($session_value)."\" />\n";
		break;
	case 5://Text-area
		$html .= "\n<textarea name='itemOption[{$post_id}][{$sku}][{$optcode}]' id='itemOption[{$post_id}][{$sku}][{$optcode}]' class='iopt_textarea'>".esc_attr($session_value)."</textarea>\n";
		break;
	}
	
	$html = apply_filters('wcmb_filter_the_itemOption', $html, $opts, $name, $label, $post_id, $sku);
	
	if( $out == 'return' ){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_get_cart_rows( $out = '' ) {
	global $usces, $usces_gp, $wcmb;
	$cart = $usces->cart->get_cart();
	$usces_gp = 0;
	$res = '';
	$mupButton = "upButton";
	$mdelButton = "delButton";
	if( KDDI === $wcmb['device_div'] ) {
		$mupButton = "mupButton";
		$mdelButton = "mdelButton";
	}

	for($i=0; $i<count($cart); $i++) { 
		$cart_row = $cart[$i];
		$post_id = $cart_row['post_id'];
		$sku = $cart_row['sku'];
		$sku_code = esc_attr(urldecode($cart_row['sku']));
		if( KDDI === $wcmb['device_div'] ) $sku = wcmb_slugtolower_au( $sku_code );
		$quantity = $cart_row['quantity'];
		$options = $cart_row['options'];
		$advance = $usces->cart->wc_serialize($cart_row['advance']);
		$itemCode = $usces->getItemCode($post_id);
		$itemName = $usces->getItemName($post_id);
		$cartItemName = $usces->getCartItemName($post_id, $sku_code);
		$itemRestriction = $usces->getItemRestriction($post_id);
		$itemRestriction = ( '' == $itemRestriction ) ? ITEM_RESTRICTION : $itemRestriction;
		$skuPrice = $cart_row['price'];
		$skuZaikonum = $usces->getItemZaikonum($post_id, $sku_code);
		$unit = $usces->getItemSkuUnit($post_id, $sku);
		$stockid = $usces->getItemZaikoStatusId($post_id, $sku_code);
		$stock = $usces->getItemZaiko($post_id, $sku_code);
		$red = (in_array($stock, array(__('sellout','usces'), __('Out Of Stock','usces'), __('Out of print','usces')))) ? 'class="signal_red"' : '';
		$pictid = $usces->get_mainpictid($itemCode);
		if ( empty($options) ) {
			$optstr = '';
			$options = array();
		}
		$gp = ( usces_is_gptekiyo($post_id, $sku_code, $quantity) ) ? '<div class="item_gp">GP</div>' : '';

		$res .= '<table>';
		
		$res .= '<tr>';
		$res .= '<td colspan="2">'.wcmb_change_tag($cartItemName, 'div', 'class="itemname"', 'div', 'style="background-color:#EEEEEE;"', 'return').'</td>';
		$res .= '</tr>';
		
		$res .= '<tr>';
		$res .= '<td colspan="2">'.$gp.usces_crform(($skuPrice), true, false, 'return').'×';
		$res .= '<select name="quant['.$i.']['.$post_id.']['.$sku.']">';
		for( $r=1; $r<=$itemRestriction; $r++){
			$res .= '<option value="'.$r.'"'.($r == $quantity ? ' selected="selected"' : '').'>'.$r.esc_html($unit).'</option>';
		}
		$res .= '</select><input name="'.$mupButton.'['.$i.']['.$post_id.']['.$sku.']" class="upButton" type="submit" value="'.__('Quantity renewal','usces').'" />';
		$res .= '</td>';
		$res .= '</tr>';
		
		$res .= '<tr>';
		$res .= '<td rowspan="2" width="64">'.'<a href="'.get_permalink($post_id).'">'.wp_get_attachment_image( $pictid, array(60, 60), true ).'</a>'.'</td>';
		$res .= '<td class="item_price">金額　'.usces_crform(($skuPrice * $quantity), true, false, 'return').'</td>';
		$res .= '</tr>';
		
		$res .= '<tr>';
		$res .= '<td class="item_stock">在庫状況　'.esc_html( $stock ).'</td>';
		$res .= '</tr>';
		
		$res .= '<tr>';
		$res .= '<td colspan="2" class="item_option">';
		if( is_array($options) && count($options) > 0 ){
			$optstr = '';
			foreach($options as $key => $value){
				if( !empty($key) ) {
					$key = urldecode($key);
					if(is_array($value)) {
						$c = '';
						$optstr .= esc_html($key).' : '; 
						foreach($value as $v) {
							$optstr .= $c.nl2br(esc_html(urldecode($v)));
							$c = ', ';
						}
						$optstr .= "<br />\n"; 
					} else {
						$optstr .= esc_html($key).' : '.nl2br(esc_html(urldecode($value)))."<br />\n"; 
					}
				}
			}
			$res .= apply_filters( 'wcmb_filter_option_cart', $optstr, $options);
		}
		$res .= apply_filters( 'wcmb_filter_option_info_cart', '', $cart_row );
		$res .= '</td>';
		$res .= '</tr>';

		$res .= '<tr>';
		$res .= '<td colspan="2">'.'　<input name="'.$mdelButton.'['.$i.']['.$post_id.']['.$sku.']" class="delButton" type="submit" value="この商品を削除する" />'.'</td>';
		$res .= '</tr>';
		
		$res .= '</table>';
		$res .= '<input name="itemRestriction['.$i.']" type="hidden" value="'.$itemRestriction.'" />';
		$res .= '<input name="stockid['.$i.']" type="hidden" value="'.$stockid.'" />';
		$res .= '<input name="itempostid['.$i.']" type="hidden" value="'.$post_id.'" />';
		$res .= '<input name="itemsku['.$i.']" type="hidden" value="'.$sku.'" />';
		$res .= '<input name="zaikonum['.$i.']['.$post_id.']['.$sku.']" type="hidden" value="'.esc_attr($skuZaikonum).'" />';
		$res .= '<input name="skuPrice['.$i.']['.$post_id.']['.$sku.']" type="hidden" value="'.esc_attr($skuPrice).'" />';
		$res .= '<input name="advance['.$i.']['.$post_id.']['.$sku.']" type="hidden" value="'.esc_attr($advance).'" />';
		foreach( $options as $key => $value ) {
			if( KDDI === $wcmb['device_div'] ) {
				$key = urlencode( $key );
				$key = wcmb_slugtolower_au( $key );
			}
			if( is_array($value) ) {
				foreach($value as $v) {
					$res .= '<input name="itemOption['.$i.']['.$post_id.']['.$sku.']['.$key.']['.$v.']" type="hidden" value="'.$v.'" />';
				}
			} else {
				$res .= '<input name="itemOption['.$i.']['.$post_id.']['.$sku.']['.$key.']" type="hidden" value="'.$value.'" />';
			}
		}
		$res .= '<hr />';
	}
	
	if($out == 'return'){
		return $res;
	}else{
		echo $res;
	}
}

function wcmb_get_confirm_rows( $out = '' ) {
	global $usces, $usces_members, $usces_entries;
	$memid = ( empty($usces_members['ID']) ) ? 999999999 : $usces_members['ID'];
	$usces->set_cart_fees( $usces_members, $usces_entries );
	$usces_entries = $usces->cart->get_entry();
	
	$cart = $usces->cart->get_cart();
	$res = '';
	for($i=0; $i<count($cart); $i++) { 
		$cart_row = $cart[$i];
		$post_id = $cart_row['post_id'];
		$sku = esc_attr($cart_row['sku']);
		$sku_code = esc_attr(urldecode($cart_row['sku']));
		$quantity = $cart_row['quantity'];
		$options = $cart_row['options'];
		$itemCode = $usces->getItemCode($post_id);
		$itemName = $usces->getItemName($post_id);
		$cartItemName = $usces->getCartItemName($post_id, $sku_code);
		$skuPrice = $cart_row['price'];
		$unit = $usces->getItemSkuUnit($post_id, $sku);
		$pictid = $usces->get_mainpictid($itemCode);
		if(empty($options)) {
			$optstr = '';
			$options =  array();
		}
		$gp = ( usces_is_gptekiyo($post_id, $sku_code, $quantity) ) ? '<span class="item_gp">GP</span>' : '';

		$res .= '
			<tr>
				<td rowspan="4" class="confirm_num">'.($i + 1).'</td>
				<td class="confirm_item" colspan="2"><div class="confirm_itemname">'.$cartItemName."</div>\n";
		if( is_array($options) && count($options) > 0 ){
			$optstr = '';
			foreach($options as $key => $value){
				if( !empty($key) ) {
					$key = urldecode($key);
					if(is_array($value)) {
						$c = '';
						$optstr .= esc_html($key).' : ';
						foreach($value as $v) {
							$optstr .= $c.nl2br(esc_html(urldecode($v)));
							$c = ', ';
						}
						$optstr .= "<br />\n";
					} else {
						$optstr .= esc_html($key).' : '.nl2br(esc_html(urldecode($value)))."<br />\n";
					}
				}
			}
			if($optstr != '') $optstr = '<div class="confirm_option">'.$optstr.'</div>';
			$res .= apply_filters( 'wcmb_filter_option_confirm', $optstr, $options);
		}
		$res .= apply_filters( 'wcmb_filter_option_info_confirm', '', $cart_row );
		$res .= '</td>
			</tr>
			<tr><td class="aright">'.__('Unit price','usces').'</td><td class="confirm_unit">'.$gp.usces_crform($skuPrice, true, false, 'return').'</td></tr>
			<tr><td class="aright">'.__('Quantity', 'usces').'</td><td class="confirm_quantity">'.$cart_row['quantity'].esc_html($unit).'</td></tr>
			<tr><td class="aright">'.__('Amount', 'usces').'</td><td class="confirm_amount">'.usces_crform(($skuPrice * $cart_row['quantity']), true, false, 'return').'</td></tr>';
	}
	
	if($out == 'return'){
		return $res;
	}else{
		echo $res;
	}
}

function wcmb_addressform( $type, $data, $out = 'return' ){
	global $usces, $usces_settings;
	$options = get_option('usces');
	$applyform = usces_get_apply_addressform($options['system']['addressform']);
	$formtag = '';
	switch( $type ){
	case 'confirm':
	case 'member':
		$values = $data;
		break;
	case 'customer':
	case 'delivery':
		$values = $data[$type];
		break;
	}
	$values['country'] = !empty($values['country']) ? $values['country'] : usces_get_local_addressform();

	if( 'confirm' == $type ) {
		switch($applyform) {
		case 'JP':
			$formtag .= wcmb_custom_field_info($data, 'customer', 'name_pre', 'return');
			$formtag .= '<tr><th>'.__('Full name', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['name1']).' '.esc_html($values['customer']['name2']).'</td></tr>';
			$furigana_customer = '<tr><th>'.__('furigana', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['name3']).' '.esc_html($values['customer']['name4']).'</td></tr>';
			$formtag .= apply_filters( 'usces_filter_furigana_confirm_customer', $furigana_customer, $type, $values );
			$formtag .= wcmb_custom_field_info($data, 'customer', 'name_after', 'return');
			$formtag .= '
			<tr><th>'.__('Zip/Postal Code', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['zipcode']).'</td></tr>';
			if(1 < count($options['system']['target_market'])) {
				$formtag .= '
			<tr><th>'.__('Country', 'usces').'</th></tr><tr><td>'.esc_html($usces_settings['country'][$values['customer']['country']]).'</td></tr>';
			}
			$formtag .= '
			<tr><th>'.__('Province', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['pref']).'</td></tr>
			<tr><th>'.__('city', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['address1']).'</td></tr>
			<tr><th>'.__('numbers', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['address2']).'</td></tr>
			<tr><th>'.__('building name', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['address3']).'</td></tr>
			<tr><th>'.__('Phone number', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['tel']).'</td></tr>
			<tr><th>'.__('FAX number', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['fax']).'</td></tr>';
			$formtag .= wcmb_custom_field_info($data, 'customer', 'fax_after', 'return');
			$formtag .= '
			<tr><td class="aright"><a href="'.USCES_CART_URL.'&customerinfo2=1">お客様情報を変更する</a></td></tr>';
			
			if( !defined('WCEX_DLSELLER') ) {
			$shipping_address_info  = '<tr class="ttl"><td><h3>'.__('Shipping address information', 'usces').'</h3></td></tr>';
			$shipping_address_info .= wcmb_custom_field_info($data, 'delivery', 'name_pre', 'return');
			$shipping_address_info .= '<tr><th>'.__('Full name', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['name1']).' '.esc_html($values['delivery']['name2']).'</td></tr>';
			$furigana_delivery = '<tr><th>'.__('furigana', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['name3']).' '.esc_html($values['delivery']['name4']).'</td></tr>';
			$shipping_address_info .= apply_filters( 'wcmb_filter_furigana_confirm_delivery', $furigana_delivery, $type, $values );
			$shipping_address_info .= wcmb_custom_field_info($values, 'delivery', 'name_after', 'return');
			$shipping_address_info .= '
			<tr><th>'.__('Zip/Postal Code', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['zipcode']).'</td></tr>';
			if(1 < count($options['system']['target_market'])) {
				$shipping_address_info .= '
			<tr><th>'.__('Country', 'usces').'</th></tr><tr><td>'.esc_html($usces_settings['country'][$values['delivery']['country']]).'</td></tr>';
			}
			$shipping_address_info .= '
			<tr><th>'.__('Province', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['pref']).'</td></tr>
			<tr><th>'.__('city', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['address1']).'</td></tr>
			<tr><th>'.__('numbers', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['address2']).'</td></tr>
			<tr><th>'.__('building name', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['address3']).'</td></tr>
			<tr><th>'.__('Phone number', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['tel']).'</td></tr>
			<tr><th>'.__('FAX number', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['fax']).'</td></tr>';
			$shipping_address_info .= wcmb_custom_field_info($data, 'delivery', 'fax_after', 'return');
			$formtag .= apply_filters('wcmb_filter_shipping_address_info', $shipping_address_info);
			$formtag .= '
			<tr><td class="aright"><a href="'.USCES_CART_URL.'&deliveryinfo2=1">配送先情報を変更する</a></td></tr>';
			}
			break;
			
		case 'US':
			$formtag .= wcmb_custom_field_info($data, 'customer', 'name_pre', 'return');
			$formtag .= '<tr><th>'.__('Full name', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['name2']).' '.esc_html($values['customer']['name1']).'</td></tr>';
			$formtag .= wcmb_custom_field_info($data, 'customer', 'name_after', 'return');
			$formtag .= '
			<tr><th>'.__('Address Line1', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['address2']).'</td></tr>
			<tr><th>'.__('Address Line2', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['address3']).'</td></tr>
			<tr><th>'.__('city', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['address1']).'</td></tr>
			<tr><th>'.__('State', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['pref']).'</td></tr>
			<tr><th>'.__('Country', 'usces').'</th></tr><tr><td>'.esc_html($usces_settings['country'][$values['customer']['country']]).'</td></tr>
			<tr><th>'.__('Zip', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['zipcode']).'</td></tr>
			<tr><th>'.__('Phone number', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['tel']).'</td></tr>
			<tr><th>'.__('FAX number', 'usces').'</th></tr><tr><td>'.esc_html($values['customer']['fax']).'</td></tr>';
			$formtag .= wcmb_custom_field_info($data, 'customer', 'fax_after', 'return');
			
			$shipping_address_info = '<tr class="ttl"><td><h3>'.__('Shipping address information', 'usces').'</h3></td></tr>';
			$shipping_address_info .= wcmb_custom_field_info($data, 'delivery', 'name_pre', 'return');
			$shipping_address_info .= '<tr><th>'.__('Full name', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['name2']).' '.esc_html($values['delivery']['name1']).'</td></tr>';
			$shipping_address_info .= wcmb_custom_field_info($data, 'delivery', 'name_after', 'return');
			$shipping_address_info .= '
			<tr><th>'.__('Address Line1', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['address2']).'</td></tr>
			<tr><th>'.__('Address Line2', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['address3']).'</td></tr>
			<tr><th>'.__('city', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['address1']).'</td></tr>
			<tr><th>'.__('State', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['pref']).'</td></tr>
			<tr><th>'.__('Country', 'usces').'</th></tr><tr><td>'.esc_html($usces_settings['country'][$values['delivery']['country']]).'</td></tr>
			<tr><th>'.__('Zip', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['zipcode']).'</td></tr>
			<tr><th>'.__('Phone number', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['tel']).'</td></tr>
			<tr><th>'.__('FAX number', 'usces').'</th></tr><tr><td>'.esc_html($values['delivery']['fax']).'</td></tr>';
			$shipping_address_info .= wcmb_custom_field_info($data, 'delivery', 'fax_after', 'return');
			$formtag .= apply_filters('wcmb_filter_shipping_address_info', $shipping_address_info);
			break;
		}
		$res = apply_filters('wcmb_filter_apply_addressform_confirm', $formtag, $type, $data);
	
	}else{
		switch($applyform) {
		case 'JP':
			$formtag .= wcmb_custom_field_input($data, $type, 'name_pre', 'return');
			$formtag .= '
			<tr class="inp1"><th scope="row" colspan="2">'.usces_get_essential_mark('name1', $data).__('Full name', 'usces').'</th></tr>
			<tr>
				<td>'.__('Familly name', 'usces').'<input name="'.$type.'[name1]" id="name1" type="text" value="'.esc_attr($values['name1']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_KN1, 'return').' /></td>
				<td>'.__('Given name', 'usces').'<input name="'.$type.'[name2]" id="name2" type="text" value="'.esc_attr($values['name2']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_KN1, 'return').' /></td>
			</tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('name3', $data).__('furigana', 'usces').'</th></tr>
			<tr>
				<td>'.__('Familly name', 'usces').'<input name="'.$type.'[name3]" id="name3" type="text" value="'.esc_attr($values['name3']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_KN2, 'return').' /></td>
				<td>'.__('Given name', 'usces').'<input name="'.$type.'[name4]" id="name4" type="text" value="'.esc_attr($values['name4']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_KN2, 'return').' /></td>
			</tr>';
			$formtag .= wcmb_custom_field_input($data, $type, 'name_after', 'return');
			$formtag .= '
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('zipcode', $data).__('Zip/Postal Code', 'usces').'<br /><span>例)'.apply_filters( 'wcmb_filter_after_zipcode', '1112222', $applyform ).'</span></th></tr>
			<tr><td colspan="2"><input name="'.$type.'[zipcode]" id="zipcode" type="text" value="'.esc_attr($values['zipcode']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_NUM, 'return').' /></td></tr>';
			if(1 < count($options['system']['target_market'])) {
				$formtag .= '
			<tr><th scope="row" colspan="2">'.__('Country', 'usces').'</th></tr>
			<tr><td colspan="2"><input type="text" name="'.$type.'[country]" value="'.$usces_settings['country'][$values['country']].'" readonly /></td></tr>';
			}
			$formtag .= '
			<input name="'.$type.'[country]" type="hidden" value="'.esc_attr($values['country']).'" >
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('states', $data).__('Province', 'usces').'</th></tr>
			<tr><td colspan="2">'.usces_pref_select( $type, $values ).'</td></tr>
			<tr class="inp2"><th scope="row" colspan="2">'.usces_get_essential_mark('address1', $data).__('city', 'usces').'<br /><span>例)'.apply_filters( 'wcmb_filter_after_address1', __('Kitakami Yokohama', 'usces'), $applyform ).'</span></th></tr>
			<tr><td colspan="2"><input name="'.$type.'[address1]" id="address1" type="text" value="'.esc_attr($values['address1']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_KN1, 'return').' /></td></tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('address2', $data).__('numbers', 'usces').'<br /><span>例)'.apply_filters( 'wcmb_filter_after_address2', '3-4-55', $applyform ).'</span></th></tr>
			<tr><td colspan="2"><input name="'.$type.'[address2]" id="address2" type="text" value="'.esc_attr($values['address2']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_KN1, 'return').' /></td></tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('address3', $data).__('building name', 'usces').'<br /><span>例)'.apply_filters( 'wcmb_filter_after_address3', __('tuhanbuild 4F', 'usces'), $applyform ).'</span></th></tr>
			<tr><td colspan="2"><input name="'.$type.'[address3]" id="address3" type="text" value="'.esc_attr($values['address3']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_KN1, 'return').' /></td></tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('tel', $data).__('Phone number', 'usces').'<br /><span>例)'.apply_filters( 'wcmb_filter_after_tel', '1000101000', $applyform ).'</span></th></tr>
			<tr><td colspan="2"><input name="'.$type.'[tel]" id="tel" type="text" value="'.esc_attr($values['tel']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_NUM, 'return').' /></td></tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('fax', $data).__('FAX number', 'usces').'<br /><span>例)'.apply_filters( 'wcmb_filter_after_fax', '1000101000', $applyform ).'</span></th></tr>
			<tr><td colspan="2"><input name="'.$type.'[fax]" id="fax" type="text" value="'.esc_attr($values['fax']).'" onKeyDown="if (event.keyCode == 13) {return false;}" '.wcmb_set_istyle( WCMB_ISTYLE_NUM, 'return').' /></td></tr>';
			$formtag .= wcmb_custom_field_input($data, $type, 'fax_after', 'return');
			break;
			
		case 'US':
			$formtag .= wcmb_custom_field_input($data, $type, 'name_pre', 'return');
			$formtag .= '
			<tr class="inp1"><th scope="row" colspan="2">'.usces_get_essential_mark('name1', $data).__('Full name', 'usces').'</th></tr>
			<tr>
				<td>'.__('Given name', 'usces').'<input name="'.$type.'[name2]" id="name2" type="text" value="'.esc_attr($values['name2']).'" /></td>
				<td>'.__('Familly name', 'usces').'<input name="'.$type.'[name1]" id="name1" type="text" value="'.esc_attr($values['name1']).'" /></td>
			</tr>';
			$formtag .= wcmb_custom_field_input($data, $type, 'name_after', 'return');
			$formtag .= '
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('address2', $data).__('Address Line1', 'usces').'</th></tr>
			<tr><td colspan="2">'.__('Street address', 'usces').'<br /><input name="'.$type.'[address2]" id="address2" type="text" value="'.esc_attr($values['address2']).'" /></td></tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('address3', $data).__('Address Line2', 'usces').'</th></tr>
			<tr><td colspan="2">'.__('Apartment, building, etc.', 'usces').'<br /><input name="'.$type.'[address3]" id="address3" type="text" value="'.esc_attr($values['address3']).'" /></td></tr>
			<tr class="inp2"><th scope="row" colspan="2">'.usces_get_essential_mark('address1', $data).__('city', 'usces').'</th></tr>
			<tr><td colspan="2"><input name="'.$type.'[address1]" id="address1" type="text" value="'.esc_attr($values['address1']).'" /></td></tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('states', $data).__('State', 'usces').'</th></tr>
			<tr><td colspan="2">'.usces_pref_select( $type, $values ).'</td></tr>';
			if(1 < count($options['system']['target_market'])) {
				$formtag .= '
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('country', $data).__('Country', 'usces').'</th></tr>
			<tr><td colspan="2"><input type="text" name="'.$type.'[country]" value="'.$usces_settings['country'][$values['country']].'" readonly /></td></tr>';
			}
			$formtag .= '<input name="'.$type.'[country]" type="hidden" value="'.esc_attr($values['country']).'" >
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('zipcode', $data).__('Zip', 'usces').'</th></tr>
			<tr><td colspan="2"><input name="'.$type.'[zipcode]" id="zipcode" type="text" value="'.esc_attr($values['zipcode']).'" /></td></tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('tel', $data).__('Phone number', 'usces').'</th></tr>
			<tr><td colspan="2"><input name="'.$type.'[tel]" id="tel" type="text" value="'.esc_attr($values['tel']).'" /></td></tr>
			<tr><th scope="row" colspan="2">'.usces_get_essential_mark('fax', $data).__('FAX number', 'usces').'</th></tr>
			<tr><td colspan="2"><input name="'.$type.'[fax]" id="fax" type="text" value="'.esc_attr($values['fax']).'" /></td></tr>';
			$formtag .= wcmb_custom_field_input($data, $type, 'fax_after', 'return');
			break;
		}
		$res = apply_filters('wcmb_filter_apply_addressform', $formtag, $type, $data);
	}

	if($out == 'return') {
		return $res;
	} else {
		echo $res;
	}
}

function wcmb_custom_field_input( $data, $custom_field, $position, $out = '' ) {
	$html = '';
	switch($custom_field) {
	case 'order':
		$label = 'custom_order';
		$field = 'usces_custom_order_field';
		break;
	case 'customer':
		$label = 'custom_customer';
		$field = 'usces_custom_customer_field';
		break;
	case 'delivery':
		$label = 'custom_delivery';
		$field = 'usces_custom_delivery_field';
		break;
	case 'member':
		$label = 'custom_member';
		$field = 'usces_custom_member_field';
		break;
	default:
		return;
	}

	$meta = usces_has_custom_field_meta($custom_field);

	if(!empty($meta) and is_array($meta)) {
		foreach($meta as $key => $entry) {
			if($custom_field == 'order' or $entry['position'] == $position) {
				$name = $entry['name'];
				$means = $entry['means'];
				$essential = $entry['essential'];
				$value = '';
				if(is_array($entry['value'])) {
					foreach($entry['value'] as $k => $v) {
						$value .= $v."\n";
					}
				}
				$value = trim($value);

				$e = ($essential == 1) ? '<span class="em">'.__('*', 'usces').'</span>' : '';
				$html .= '
					<tr><th scope="row" colspan="2">'.$e.esc_html($name).apply_filters('usces_filter_custom_field_input_label', NULL, $key, $entry).'</th></tr>
					<tr><td colspan="2">';
				switch($means) {
					case 0://シングルセレクト
					case 1://マルチセレクト
						$selects = explode("\n", $value);
						$multiple = ($means == 0) ? '' : ' multiple';
						$multiple_array = ($means == 0) ? '' : '[]';
						$html .= '
							<select name="'.$label.'['.esc_attr($key).']'.$multiple_array.'" class="iopt_select"'.$multiple.'>';
						if($essential == 1) 
							$html .= '
								<option value="#NONE#">'.__('Choose','usces').'</option>';
						foreach($selects as $v) {
							$selected = ( isset($data[$label][$key]) && $data[$label][$key] == $v) ? ' selected' : '';
							$html .= '
								<option value="'.esc_attr($v).'"'.$selected.'>'.esc_html($v).'</option>';
						}
						$html .= '
							</select>';
						break;
					case 2://テキスト
						$html .= '
							<input type="text" name="'.$label.'['.esc_attr($key).']" size="30" value="'.esc_attr($data[$label][$key]).'" />';
						break;
					case 3://ラジオボタン
						$selects = explode("\n", $value);
						foreach($selects as $v) {
							$checked = ( isset($data[$label][$key]) && $data[$label][$key] == $v) ? ' checked' : '';
							$html .= '
							<input type="radio" name="'.$label.'['.esc_attr($key).']" value="'.esc_attr($v).'"'.$checked.'><label for="'.$label.'['.esc_attr($key).']['.esc_attr($v).']" class="iopt_label">'.esc_html($v).'</label>';
						}
						break;
					case 4://チェックボックス
						$selects = explode("\n", $value);
						foreach($selects as $v) {
							if( isset($data[$label][$key]) && is_array($data[$label][$key])) {
								$checked = (array_key_exists($v, $data[$label][$key])) ? ' checked' : '';
							} else {
								$checked = ( isset($data[$label][$key]) && $data[$label][$key] == $v) ? ' checked' : '';
							}
							$html .= '
							<input type="checkbox" name="'.$label.'['.esc_attr($key).']['.esc_attr($v).']" value="'.esc_attr($v).'"'.$checked.'><label for="'.$label.'['.esc_attr($key).']['.esc_attr($v).']" class="iopt_label">'.esc_html($v).'</label>';
						}
						break;
				}
				$html .= apply_filters('wcmb_filter_custom_field_input_value', NULL, $key, $entry).'</td>';
				$html .= '
					</tr>';
			}
		}
	}
	
	$html = apply_filters('wcmb_filter_custom_field_input', $html, $data, $custom_field, $position);

	if($out == 'return') {
		return $html;
	} else {
		echo $html;
	}
}

function wcmb_custom_field_info( $data, $custom_field, $position, $out = '' ) {

	$html = '';
	switch($custom_field) {
	case 'order':
		$label = 'custom_order';
		$field = 'usces_custom_order_field';
		break;
	case 'customer':
		$label = 'custom_customer';
		$field = 'usces_custom_customer_field';
		break;
	case 'delivery':
		$label = 'custom_delivery';
		$field = 'usces_custom_delivery_field';
		break;
	case 'member':
		$label = 'custom_member';
		$field = 'usces_custom_member_field';
		break;
	default:
		return;
	}

	$meta = usces_has_custom_field_meta($custom_field);

	if(!empty($meta) and is_array($meta)) {
		foreach($meta as $key => $entry) {
			if($custom_field == 'order' or $entry['position'] == $position) {
				$name = $entry['name'];
				$means = $entry['means'];

				$html .= '
					<tr><th>'.esc_html($name).'</th></tr><tr><td>';
				if(!empty($data[$label][$key])) {
					switch($means) {
					case 0://シングルセレクト
					case 2://テキスト
					case 3://ラジオボタン
						$html .= esc_html($data[$label][$key]);
						break;
					case 1://マルチセレクト
					case 4://チェックボックス
						if(is_array($data[$label][$key])) {
							$c = '';
							foreach($data[$label][$key] as $v) {
								$html .= $c.esc_html($v);
								$c = ', ';
							}
						} else {
							$html .= esc_html($data[$label][$key]);
						}
						break;
					}
				}
				$html .= '</td></tr>';
			}
		}
	}

	$html = apply_filters('wcmb_filter_custom_field_info', $html, $data, $custom_field, $position);

	if($out == 'return') {
		return $html;
	} else {
		echo $html;
	}
}

function wcmb_member_history_list(){
	global $usces;
	
	$usces_members = $usces->get_member();
	$usces_member_history = $usces->get_member_history($usces_members['ID']);

	$html = '<table class="history_list">';
	if ( !count($usces_member_history) ) {
		$html .= '<tr><td>'.__('There is no purchase history for this moment.', 'usces').'</td></tr>';

	} else {
		$html .= '
			<tr>
			<th class="historyrow">注文No</th>
			<th class="historyrow">'.__('Purchase date', 'usces').'</th>
			<th class="historyrow">'.__('Purchase price', 'usces').'</th>
			</tr>';
		foreach ( $usces_member_history as $umhs ) {
			$cart = $umhs['cart'];
			$date = mysql2date('y/m/d', $umhs['order_date']);
			$html .= '
			<tr>
			<td class="rightnum"><a href="'.USCES_MEMBER_HISTORY_URL.'&order_id='.$umhs['ID'].'">'.usces_get_deco_order_id($umhs['ID']).'</a></td>
			<td class="date">'.$date.'</td>
			<td class="rightnum">'.usces_crform(($usces->get_total_price($cart)-$umhs['usedpoint']+$umhs['discount']+$umhs['shipping_charge']+$umhs['cod_fee']+$umhs['tax']), true, false, 'return').'</td>
			</tr>';
		}
	}
	$html .= '</table>';

	echo $html;
}

function wcmb_member_history( $order_id ){
	global $wpdb, $usces;
	
	$usces_members = $usces->get_member();

	$order_table = $wpdb->prefix."usces_order";
	$query = $wpdb->prepare("SELECT ID, order_cart, order_condition, order_date, order_usedpoint, order_getpoint, 
							order_discount, order_shipping_charge, order_cod_fee, order_tax, order_status 
							FROM $order_table WHERE mem_id = %d AND ID = %d ", $usces_members['ID'], $order_id);
	$row = $wpdb->get_row( $query, ARRAY_A );

	$html = '<table class="history">';
	if ( !count($row) ) {
		$html .= '<tr>
		<td>'.__('There is no purchase history for this moment.', 'usces').'</td>
		</tr>
		</table>';

	} else {
		$umhs = array(
			'ID' => $row['ID'],
			'cart' => unserialize($row['order_cart']),
			'condition' => unserialize($row['order_condition']),
			'getpoint' => $row['order_getpoint'],
			'usedpoint' => $row['order_usedpoint'],
			'discount' => $row['order_discount'],
			'shipping_charge' => $row['order_shipping_charge'],
			'cod_fee' => $row['order_cod_fee'],
			'tax' => $row['order_tax'],
			'order_status' => $row['order_status'],
			'date' => mysql2date(__('Y/m/d'), $row['order_date']),
			'order_date' => $row['order_date']
			);

		$cart = $umhs['cart'];
		$html .= '
			<tr><th class="historyrow">'.__('Order number', 'usces').'</th><td class="rightnum">'.usces_get_deco_order_id( $umhs['ID'] ).'</td></tr>
			<tr><th class="historyrow">'.__('Purchase date', 'usces').'</th><td class="date">'.$umhs['date'].'</td></tr>
			<tr><th class="historyrow">'.__('Purchase price', 'usces').'</th><td class="rightnum">'.usces_crform(($usces->get_total_price($cart)-$umhs['usedpoint']+$umhs['discount']+$umhs['shipping_charge']+$umhs['cod_fee']+$umhs['tax']), true, false, 'return').'</td></tr>';
		if( usces_is_membersystem_point() ){
			$html .= '
			<tr><th class="historyrow">'.__('Used points', 'usces').'</th><td class="rightnum">'.number_format($umhs['usedpoint']).'</td></tr>';
		}
		$html .= '
			<tr><th class="historyrow">'.__('Special Price', 'usces').'</th><td class="rightnum">'.usces_crform($umhs['discount'], true, false, 'return').'</td></tr>
			<tr><th class="historyrow">'.__('Shipping', 'usces').'</th><td class="rightnum">'.usces_crform($umhs['shipping_charge'], true, false, 'return').'</td></tr>
			<tr><th class="historyrow">'.__('C.O.D', 'usces').'</th><td class="rightnum">'.usces_crform($umhs['cod_fee'], true, false, 'return').'</td></tr>
			<tr><th class="historyrow">'.__('consumption tax', 'usces').'</th><td class="rightnum">'.usces_crform($umhs['tax'], true, false, 'return').'</td></tr>';
		if( usces_is_membersystem_point() ){
			$html .= '
			<tr><th class="historyrow">'.__('Acquired points', 'usces').'</th><td class="rightnum">'.number_format($umhs['getpoint']).'</td></tr>';
		}
		$html .= '
			</table>';
		
		for($i=0; $i<count($cart); $i++) { 
			$cart_row = $cart[$i];
			$post_id = $cart_row['post_id'];
			$sku = urldecode($cart_row['sku']);
			$quantity = $cart_row['quantity'];
			$options = $cart_row['options'];
			$itemCode = $usces->getItemCode($post_id);
			$itemName = $usces->getItemName($post_id);
			$cartItemName = $usces->getCartItemName($post_id, $sku);
			$skuPrice = $cart_row['price'];
			$unit = $usces->getItemSkuUnit($post_id, $sku);
			$pictid = $usces->get_mainpictid($itemCode);
			if(empty($options)) {
				$optstr = '';
				$options =  array();
			}
			$gp = ( usces_is_gptekiyo($post_id, $sku, $quantity) ) ? '<span class="item_gp">GP</span>' : '';

			$html .= '<hr />';
			$html .= '<table id="retail_table_'.$umhs['ID'].'" class="retail">';
			$html .= '<tr>';
			$html .= '<td colspan="2">'.wcmb_change_tag($cartItemName, 'div', 'class="itemname"', 'div', 'style="background-color:#EEEEEE;"', 'return').'</td>';
			$html .= '</tr>';
			
			$html .= '<tr>';
			$html .= '<td rowspan="3" width="64">'.'<a href="'.get_permalink($post_id).'">'.wp_get_attachment_image( $pictid, array(60, 60), true ).'</a>'.'</td>';
			$html .= '<td class="item_skuprice">単価　'.$gp.usces_crform(($skuPrice), true, false, 'return').'</td>';
			$html .= '</tr>';
			
			$html .= '<tr>';
			$html .= '<td class="item_quant">数量　'.number_format($cart_row['quantity']).esc_html($unit).'</td>';
			$html .= '</tr>';
			
			$html .= '<tr>';
			$html .= '<td class="item_price">金額　'.usces_crform(($skuPrice * $quantity), true, false, 'return').'</td>';
			$html .= '</tr>';
			
			$html .= '<tr>';
			$html .= '<td colspan="2" class="item_option">';
			if( is_array($options) && count($options) > 0 ){
				$optstr = '';
				foreach($options as $key => $value){
					if( !empty($key) ) {
						$key = urldecode($key);
						if(is_array($value)) {
							$c = '';
							$optstr .= esc_html($key).' : '; 
							foreach($value as $v) {
								$optstr .= $c.nl2br(esc_html(urldecode($v)));
								$c = ', ';
							}
							$optstr .= "<br />\n"; 
						} else {
							$optstr .= esc_html($key).' : '.nl2br(esc_html(urldecode($value)))."<br />\n"; 
						}
					}
				}
				$html .= apply_filters( 'wcmb_filter_option_history', $optstr, $options);
			}
			$html .= apply_filters('wcmb_filter_history_item_name', NULL, $umhs, $cart_row, $i);
			$html .= '</td>';
			$html .= '</tr>';
			$html .= '</table>';
		}
	}

	echo $html;
}

function wcmb_get_item_custom( $post_id, $type = 'list', $out = '' ){
	global $usces, $allowedposttags;
	$cfields = $usces->get_post_custom($post_id);
	switch( $type ){
		case 'list':
			$list = '';
			$html = '<ul class="item_custom_field">'."\n";
			foreach( $cfields as $key => $value ){
				if( 'wccs_' == substr($key, 0, 5) ){
					$list .= '<li>' . esc_html(substr($key, 5)) . ' : ' . nl2br(wp_kses($value[0], $allowedposttags)) . '</li>'."\n";
				}
			}
			if(empty($list)){
				$html = '';
			}else{
				$html .= $list . '</ul>'."\n";
			}
			break;

		case 'table':
			$list = '';
			$html = '<table class="item_custom_field">'."\n";
			foreach($cfields as $key => $value){
				if( 'wccs_' == substr($key, 0, 5) ){
					$list .= '<tr><th>' . esc_html(substr($key, 5)) . '</th></tr><tr><td>' . nl2br(wp_kses($value[0], $allowedposttags)) . '</td></tr>'."\n";
				}
			}
			if(empty($list)){
				$html = '';
			}else{
				$html .= $list . '</table>'."\n";
			}
			break;

		case 'notag':
			$list = '';
			foreach($cfields as $key => $value){
				if( 'wccs_' == substr($key, 0, 5) ){
					$list .= esc_html(substr($key, 5)) . ' : ' . nl2br(wp_kses($value[0], $allowedposttags)) . "\r\n";
				}
			}
			if(empty($list)){
				$html = '';
			}else{
				$html = $list;
			}
			break;
	}
	$html = apply_filters( 'usces_filter_item_custom', $html, $post_id);
	
	if( 'return' == $out){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_list_category( $out = '' ){
	global $usces;

	$html = '';
	$itemgenre = get_category_by_slug('itemgenre');
	$cats = get_term_children($itemgenre->cat_ID, 'category');

	$permalink_structure = get_option('permalink_structure');
	foreach((array)$cats as $parent_id) {
		if(!empty($parent_id)) {
			$parent = get_category($parent_id);
			if($itemgenre->cat_ID == $parent->parent) {
				$link = ( $permalink_structure ) ? str_replace("&uscesid=", "/?uscesid=", get_category_link($parent_id)) : get_category_link($parent_id);
				$html .= '<tr><td'.wcmb_change_style('parent', 'background:#CCC;', 'return').'><a href="'.$link.'">'.get_cat_name($parent_id).'</a></td></tr>';
				$catIDs = get_term_children($parent_id, 'category');
				if(count($catIDs) > 0) {
					sort($catIDs);
					$chtml = '';
					for($i = 0; $i<count($catIDs); $i++) {
						$child = get_category($catIDs[$i]);
						if($parent_id == $child->parent) {
							$link = ( $permalink_structure ) ? str_replace("&uscesid=", "/?uscesid=", get_category_link($catIDs[$i])) : get_category_link($catIDs[$i]);
							$chtml .= '<a href="'.$link.'">'.get_cat_name($catIDs[$i]).'</a>/';
						}
					}
					$chtml = rtrim($chtml, '/');
					$html .= '<tr><td class="child">'.$chtml.'</td></tr>';
				}
			}
		}
	}

	if( 'return' == $out ){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_set_istyle( $istyle, $out = '' ){
	global $wcmb;
	$html = '';

	if( 3 <= wcmb_get_browser() ) {
		switch($wcmb['device_div']) {
		case DOCOMO:
			switch($istyle) {
			case WCMB_ISTYLE_KN1:
				$html = ' style="-wap-input-format:&quot;*&lt;ja:h&gt;&quot;;-wap-input-format:*M;"';
				break;
			case WCMB_ISTYLE_KN2:
				$html = ' style="-wap-input-format:&quot;*&lt;ja:hk&gt;&quot;;-wap-input-format:*M;"';
				break;
			case WCMB_ISTYLE_ALP:
				$html = ' style="-wap-input-format:&quot;*&lt;ja:en&gt;&quot;;-wap-input-format:*m;"';
				break;
			case WCMB_ISTYLE_NUM:
				$html = ' style="-wap-input-format:&quot;*&lt;ja:n&gt;&quot;;-wap-input-format:*N;"';
				break;
			}
			break;
		case KDDI:
		case SOFTBANK:
			switch($istyle) {
			case WCMB_ISTYLE_KN1:
				$html = ' istyle="'.$istyle.'" mode="hiragana" format="*M"';
				break;
			case WCMB_ISTYLE_KN2:
				$html = ' istyle="'.$istyle.'" mode="katakana" format="*M"';
				break;
			case WCMB_ISTYLE_ALP:
				$html = ' istyle="'.$istyle.'" mode="alphabet" format="*m"';
				break;
			case WCMB_ISTYLE_NUM:
				$html = ' istyle="'.$istyle.'" mode="numeric" format="*x"';
				break;
			}
			break;
		default:
			break;
		}
	} else {
		switch($wcmb['device_div']) {
		case DOCOMO:
			$html = ' istyle="'.$istyle.'"';
			break;
		case KDDI:
			switch($istyle) {
			case WCMB_ISTYLE_KN1:
				$html = ' format="*M"';
				break;
			case WCMB_ISTYLE_KN2:
				$html = ' format="*M"';
				break;
			case WCMB_ISTYLE_ALP:
				$html = ' format="*m"';
				break;
			case WCMB_ISTYLE_NUM:
				$html = ' format="*N"';
				break;
			}
			break;
		case SOFTBANK:
			switch($istyle) {
			case WCMB_ISTYLE_KN1:
				$html = ' mode="hiragana"';
				break;
			case WCMB_ISTYLE_KN2:
				$html = ' mode="katakana"';
				break;
			case WCMB_ISTYLE_ALP:
				$html = ' mode="alphabet"';
				break;
			case WCMB_ISTYLE_NUM:
				$html = ' mode="numeric"';
				break;
			}
			break;
		default:
			break;
		}
	}

	if($out == 'return'){
		return $html;
	}else{
		echo $html;
	}
}

function wcmb_the_inquiry_form() {
	global $usces;
	$_POST = $usces->stripslashes_deep_post($_POST);
	$error_message = '';
	if( isset($_POST['inq_name']) && '' != trim($_POST['inq_name']) ) {
		$inq_name = trim($_POST['inq_name']);
	}else{
		$inq_name = '';
		if($usces->page == 'deficiency')
			$error_message .= __('Please input your name.', 'usces') . "<br />";
	}
	if( isset($_POST['inq_mailaddress']) && is_email(trim($_POST['inq_mailaddress'])) ) {
		$inq_mailaddress = trim($_POST['inq_mailaddress']);
	}elseif( isset($_POST['inq_mailaddress']) && !is_email(trim($_POST['inq_mailaddress'])) ) {
		$inq_mailaddress = trim($_POST['inq_mailaddress']);
		if($usces->page == 'deficiency')
			$error_message .= __('E-mail address is not correct', 'usces') . "<br />";
	}else{
		$inq_mailaddress = '';
		if($usces->page == 'deficiency')
			$error_message .= __('Please input your e-mail address.', 'usces') . "<br />";
	}
	if( isset($_POST['inq_contents']) && '' != trim($_POST['inq_contents']) ) {
		$inq_contents = trim($_POST['inq_contents']);
	}else{
		$inq_contents = '';
		if($usces->page == 'deficiency')
			$error_message .= __('Please input contents.', 'usces');
	}
	

	if($usces->page == 'inquiry_comp') :
?>
	<div class="inquiry_comp"><?php _e('sending completed','usces') ?></div>
	<div class="compbox"><?php _e('I send a reply email to a visitor. I ask in a few minutes to be able to have you refer in there being the fear that e-mail address is different again when the email from this shop does not arrive.','usces') ?></div>
<?php
	elseif($usces->page == 'inquiry_error') :
?>
	<div class="inquiry_comp"><?php _e('Failure in sending','usces') ?></div>
<?php 
	else :
?>
<?php if( !empty($error_message) ): ?>
<div class="error_message"><?php echo $error_message; ?></div>
<?php endif; ?>
<form name="inquiry_form" action="<?php //echo USCES_CART_URL; ?>" method="post">
<table border="0" cellpadding="0" cellspacing="0" class="inquiry_table">
<tr><th scope="row"><?php _e('Full name','usces') ?></th></tr>
<tr><td><input name="inq_name" type="text" class="inquiry_name" value="<?php echo esc_attr($inq_name); ?>" /></td></tr>
<tr><th scope="row"><?php _e('e-mail adress','usces') ?></th></tr>
<tr><td><input name="inq_mailaddress" type="text" class="inquiry_mailaddress" value="<?php echo esc_attr($inq_mailaddress); ?>" <?php wcmb_set_istyle( WCMB_ISTYLE_ALP ); ?>/></td></tr>
<tr><th scope="row"><?php _e('contents','usces') ?></th></tr>
<tr><td><textarea name="inq_contents" class="inquiry_contents"><?php echo esc_attr($inq_contents); ?></textarea></td></tr>
</table>
<div class="send"><input name="inquiry_button" type="submit" value="<?php _e('Admit to send it with this information.','usces') ?>" /></div>
</form>
<?php
	endif;
}

function wcmb_filter_history_item_name_dlseller() {
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
		}elseif( empty($period['lastdates']) || $period['lastdates'] >= date('Y/m/d') ){
			$html .= '<div class="redownload_link"><a class="redownload_button" href="' . USCES_CART_URL . '&dlseller_transition=download&rid=' . $index . '&oid=' . $data['ID'] . apply_filters('dlseller_filter_download_para', '', $rows['post_id'], $rows['sku']) . '">' . __('Download the latest version', 'dlseller') . (!empty($dlitem['dlseller_version']) ? '（v' . $dlitem['dlseller_version'] . '）' : '') . '</a></div>';
		}else{
			$html .= '<div class="limitover">' . __('Expired', 'dlseller') . '</div>';
		}
	}
	return $html;
}

function wcmb_action_cart_clear( $html ) {
	$html .= '<br /><div class="cartclear"><a href="'.USCES_CART_URL.'&cartclear=1">全ての商品を削除する</a></div>';
	echo $html;
}

function wcmb_cart_clear() {
	global $usces;
	$_SESSION['usces_cart'] = array();
	$usces->page = 'cart';
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_upButton() {
	global $usces;
	$usces->page = 'cart';
	wcmb_upCart();
	$usces->error_message = $usces->zaiko_check();
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_upCart() {
	global $usces;

	if(!isset($_POST['quant'])) return false;

	foreach($_POST['quant'] as $index => $vs) {
		$ids = array_keys($vs);
		$post_id = $ids[0];

		$skus = array_keys($vs[$post_id]);
		$sku = $skus[0];

		$serial = wcmb_up_serialize( $index, $post_id, $sku );

		if ( $_POST['quant'][$index][$post_id][$sku] != '') {
			$_SESSION['usces_cart'][$serial]['quant'] = (int)$_POST['quant'][$index][$post_id][$sku];
			$_SESSION['usces_cart'][$serial]['advance'] = isset($_POST['advance'][$index][$post_id][$sku]) ? $usces->cart->wc_unserialize($_POST['advance'][$index][$post_id][$sku]) : array();
			$price = $usces->cart->get_realprice($post_id, wcmb_slugtoupper_au( $sku ), $_SESSION['usces_cart'][$serial]['quant']);
			$_SESSION['usces_cart'][$serial]['price'] = $price;
		}
	}

	unset( $_SESSION['usces_entry']['order']['usedpoint'] );
	do_action('usces_action_after_upCart');
}

function wcmb_delButton() {
	global $usces;
	$usces->page = 'cart';
	wcmb_del_row();
	add_action('the_post', array($usces, 'action_cartFilter'));
	add_filter('yoast-ga-push-after-pageview', 'usces_trackPageview_cart');
	add_action('template_redirect', array($usces, 'template_redirect'));
}

function wcmb_del_row() {
	global $usces;

	$indexs = array_keys($_POST['mdelButton']);
	$index = $indexs[0];
	$ids = array_keys($_POST['mdelButton'][$index]);
	$post_id = $ids[0];
	$skus = array_keys($_POST['mdelButton'][$index][$post_id]);
	$sku = $skus[0];

	$serial = wcmb_up_serialize( $index, $post_id, $sku );
	do_action('usces_cart_del_row', $index);

	if(isset($_SESSION['usces_cart'][$serial]))
		unset($_SESSION['usces_cart'][$serial]);

	unset( $_SESSION['usces_entry']['order']['usedpoint'] );
	do_action('usces_action_after_cart_del_row', $index);
}

function wcmb_slugtolower_au( $str ) {
	//global $wcmb;
	//if( KDDI === $wcmb['device_div'] ) {
		preg_match_all( '/(%[a-zA-Z0-9][a-zA-Z0-9])+/', $str, $matches );
		$newstr = '';
		foreach( $matches[0] as $slug ) {
			$newslug = strtolower( $slug );
			$newstr = str_replace( $slug, $newslug, $str );
		}
		if( $newstr ) $str = $newstr;
	//}
	return $str;
}

function wcmb_slugtoupper_au( $str ) {
	global $wcmb;
	if( KDDI === $wcmb['device_div'] ) {
		preg_match_all( '/(%[a-zA-Z0-9][a-zA-Z0-9])+/', $str, $matches );
		$newstr = '';
		foreach( $matches[0] as $slug ) {
			$newslug = strtoupper( $slug );
			$newstr = str_replace( $slug, $newslug, $str );
		}
		if( $newstr ) $str = $newstr;
	}
	return $str;
}

function wcmb_up_serialize( $index, $id, $sku ) {
	global $usces;
	$sku = wcmb_slugtoupper_au( $sku );
	if( isset($_POST['itemOption'][$index]) ) {
		$_POST = $usces->stripslashes_deep_post($_POST);
		foreach( $_POST['itemOption'][$index][$id][$sku] as $key => $value ) {
			$key = wcmb_slugtoupper_au( $key );
			if( is_array($value) ) {
				foreach( $value as $k => $v ) {
					$pots[$key][$v] = $v;
				}
			} else {
				$pots[$key] = $value;
			}
		}
		$sels[$id][$sku] = $pots;
	} else {
		$sels[$id][$sku] = 0;
	}
	return serialize($sels);
}

function wcmb_action_confirm_page_point_inform_zeus() {
	global $usces, $usces_entries;
	$html = '';

	$payments = usces_get_payments_by_name( $usces_entries['order']['payment_name'] );
	$acting_flag = ( 'acting' == $payments['settlement'] ) ? $payments['module'] : $payments['settlement'];
	$_POST = $usces->stripslashes_deep_post($_POST);

	switch( $acting_flag ) {
	case 'acting_zeus_card':
		$member = $usces->get_member();
		$pcid = $usces->get_member_meta_value('zeus_pcid', $member['ID']);
		if( '2' == $usces->options['acting_settings']['zeus']['security'] && 'on' == $usces->options['acting_settings']['zeus'] && $pcid == '8888888888888888' && wcmb_is_member_logged_in() ) {
			$html .= '<input name="cnum1" type="hidden" value="8888888888888888" />
				<input name="expyy" type="hidden" value="2010" />
				<input name="expmm" type="hidden" value="01" />
				<input name="username" type="hidden" value="QUICKCHARGE" />';
		} else {
			if( isset($_POST['cnum1']) ) 
				$html .= '<input type="hidden" name="cnum1" value="'.$_POST['cnum1'].'">';
			if( isset($_POST['securecode']) ) 
				$html .= '<input type="hidden" name="securecode" value="'.$_POST['securecode'].'">';
			if( isset($_POST['expyy']) ) 
				$html .= '<input type="hidden" name="expyy" value="'.$_POST['expyy'].'">';
			if( isset($_POST['expmm']) ) 
				$html .= '<input type="hidden" name="expmm" value="'.$_POST['expmm'].'">';
			if( isset($_POST['username']) ) 
				$html .= '<input type="hidden" name="username" value="'.$_POST['username'].'">';
			if( isset($_POST['howpay']) ) 
				$html .= '<input type="hidden" name="howpay" value="'.$_POST['howpay'].'">';
			if( isset($_POST['cbrand']) ) 
				$html .= '<input type="hidden" name="cbrand" value="'.$_POST['cbrand'].'">';
			if( isset($_POST['div_1']) ) 
				$html .= '<input type="hidden" name="div_1" value="'.$_POST['div_1'].'">';
			if( isset($_POST['div_2']) ) 
				$html .= '<input type="hidden" name="div_2" value="'.$_POST['div_2'].'">';
			if( isset($_POST['div_3']) ) 
				$html .= '<input type="hidden" name="div_3" value="'.$_POST['div_3'].'">';
		}
		break;

	case 'acting_zeus_conv':
		if( isset($_POST['pay_cvs']) ) 
			$html = '<input type="hidden" name="pay_cvs" value="'.$_POST['pay_cvs'].'">';
		break;
	}
	echo $html;
}
?>
