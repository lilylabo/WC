<?php
// Functions

function wcmb_is_control_page(){
	global $usces;
	if( $usces->is_member_page($_SERVER['REQUEST_URI']) 
		|| $usces->is_cart_page($_SERVER['REQUEST_URI']) 
		|| $usces->is_inquiry_page($_SERVER['REQUEST_URI'])
	 ){
		return true;
	}else{
		return false;
	}
}

function wcmb_get_device_div(){
	global $wcmb;
	$id = isset($wcmb['device_div']) ? $wcmb['device_div'] : '';
	if( '' != $id ) return $id;

	$strUA = $_SERVER['HTTP_USER_AGENT'];
	if( strpos( $strUA , "remise" ) !== false && isset($_POST['CARIER_TYPE']) ){
		$id = isset( $_GET['dd'] ) ? (int)$_GET['dd'] : '';
		$wcmb['check_garak'] = true;
	}elseif( strpos( $_SERVER['HTTP_USER_AGENT'] , "DoCoMo" ) !== false ){
		$id = DOCOMO;
		$wcmb['check_garak'] = wcmb_check_garak($id);
	}elseif( (strpos( $_SERVER['HTTP_USER_AGENT'] , "Vodafone" ) !== false) || (strpos( $_SERVER['HTTP_USER_AGENT'] , "J-PHONE" ) !== false) || (strpos( $_SERVER['HTTP_USER_AGENT'] , "SoftBank" ) !== false) ){
		$id = SOFTBANK;
		$wcmb['check_garak'] = wcmb_check_garak($id);
	}elseif( (strpos( $_SERVER['HTTP_USER_AGENT'] , "UP." ) !== false) || (strpos( $_SERVER['HTTP_USER_AGENT'] , "KDDI" ) !== false) ){
		$id = KDDI;
		$wcmb['check_garak'] = wcmb_check_garak($id);
	}elseif( preg_match("/Android.*Mobile|iPhone|BlackBerry/", $_SERVER['HTTP_USER_AGENT']) ){
		$id = SMARTPHONE;
		$wcmb['check_garak'] = $id;
	}else {
		$id = PC;
		$wcmb['check_garak'] = $id;
	}
	return $id;
}

function wcmb_get_browser(){
	global $wcmb;
	$browser = isset($wcmb['browser']) ? $wcmb['browser'] : '';
	if( '' != $browser ) return $browser;

	$strUA = $_SERVER['HTTP_USER_AGENT'];
	switch ( $wcmb['device_div'] ){
	case DOCOMO:
		if( preg_match("/\(c100;/", $strUA) ) {
			$browser = 1;
		} elseif( preg_match("/\(c500;/", $strUA) ) {
			$browser = 3;
		}
		break;
	case SOFTBANK:
		if( preg_match('/(SoftBank|Vodafone)\/1.0\//', $strUA) ) {
			$browser = 2;
		} elseif( preg_match('/(SoftBank|Vodafone)\/2.0\//', $strUA) ) {
			$browser = 3;
		}
		break;
	case KDDI:
		$max_pdu = isset($_SERVER['HTTP_X_UP_DEVCAP_MAX_PDU']) ? $_SERVER['HTTP_X_UP_DEVCAP_MAX_PDU'] : 500000;
		if( 150000 >= $max_pdu ) {
			$browser = 1;
		} elseif( 300000 >= $max_pdu ) {
			$browser = 2;
		} elseif( 500000 >= $max_pdu ) {
			$browser = 3;
		}
		break;
	case SMARTPHONE:
		break;
	case PC:
		break;
	}

	if( strpos( $strUA , "remise" ) !== false && isset($_POST['CARIER_TYPE']) ) {
		$browser = 3;//isset( $_GET['br'] ) ? (int)$_GET['br'] : '';
	}

	return $browser;
}

function wcmb_get_device_name($id){
	global $wcmb;
	$name = isset($wcmb['device_name']) ? $wcmb['device_name'] : '';
	if( '' != $name ) return $name;

	$strUA = $_SERVER['HTTP_USER_AGENT'];
	switch ( $id ){
	case DOCOMO:
		if (preg_match("/^DoCoMo\/2/", $strUA)) {
			$parts = explode(' ', $strUA);
			$parts2 = explode('(', $parts[1]);
			$name = $parts2[0];
		} else if (preg_match("/^DoCoMo\/1/", $strUA)) {
			$parts = explode('/', $strUA);
			$name = $parts[2];
		}
		break;
	case SOFTBANK:
		$name = (isset($_SERVER[ "HTTP_X_JPHONE_MSNAME" ])) ? $_SERVER[ "HTTP_X_JPHONE_MSNAME" ] : 'noidea';//false;
		break;
	case KDDI:
		$substart = strpos($strUA, '-') + 1;
		$subend = strpos($strUA, ' ');
		$name = substr($strUA, $substart, $subend - $substart);
		break;
	case SMARTPHONE:
		if (preg_match("/iPhone/", $strUA)){
			$name = 'iPhone';
		}elseif(preg_match('/Android.*; ([^;]*); (.*) Build.*Mobile/', $strUA, $matches)){
			$name = $matches[2];
		}elseif(preg_match('/^(BlackBerry.*)\/.*Profile/', $strUA, $matches)){
			$name = $matches[1];
		}
		break;
	case PC:
		if (preg_match("/iPad/", $strUA)) {
			$name = 'iPad';
		}elseif(preg_match('/Android.*; ([^;]*); (.*) Build/', $strUA, $matches)){
			$name = $matches[2];
		} else {
			$name = 'PC';
		}
		break;
	}

	if( strpos( $strUA , "remise" ) !== false && isset($_POST['CARIER_TYPE']) ) {
		$name = isset( $_GET['dn'] ) ? $_GET['dn'] : '';
	}

	return $name;
}

function wcmb_output(){
	global $wcmb;
	if( PC === $wcmb['device_div'] ) return;

	$template = wcmb_check_refere();

	ob_end_clean();
	ob_start('wcmb_ob_callback');

	$browser = wcmb_get_browser();

	if( 2 === $browser ){
		header("Content-Type: text/html; charset=Shift_JIS");
	}elseif( 1 === $browser ){
		header('Content-Type: application/xhtml+xml; charset=Shift_JIS');
	}else{
		header("Content-Type: text/html; charset=Shift_JIS");
	}

	if( $template ){
		include( get_stylesheet_directory() . '/' . $template );
		exit;
	}
}

function wcmb_ob_callback($buffer){
	$buffer = wcmb_cf7_action($buffer);
	$buffer = wcmb_conclusion($buffer);
	$buffer = wcmb_ssl_replace($buffer);
	$buffer = mb_convert_encoding($buffer, 'SJIS', get_option('blog_charset'));
	if (function_exists('mb_http_output')) {
		mb_http_output('pass');
	}
	//usces_log('buffer : '.$buffer, 'acting_transaction.log');
	return $buffer;
	exit;
}

function wcmb_ssl_replace($buffer){
	global $usces;
	if( $usces->use_ssl && ($usces->is_cart_or_member_page($_SERVER['REQUEST_URI']) || $usces->is_inquiry_page($_SERVER['REQUEST_URI'])) ){
		$pattern = array(
			'|(<[^<]*)href=\"'.get_option('siteurl').'([^>]*)\.css([^>]*>)|', 
			'|(<[^<]*)src=\"'.get_option('siteurl').'([^>]*>)|'
		);
		$replacement = array(
			'${1}href="'.USCES_SSL_URL_ADMIN.'${2}.css${3}', 
			'${1}src="'.USCES_SSL_URL_ADMIN.'${2}'
		);
		$buffer = preg_replace($pattern, $replacement, $buffer);
	}
	
				
	if( !is_admin() ){
		$pattern = '/href=([\"|\']'.str_replace('/', '\/', get_option('home')).'[^\"\']*[\"|\'])/';
		preg_match_all($pattern, $buffer, $matches);
		$targ = array_unique($matches[1]);
		foreach($targ as $value){
			if( 
				strpos($value, '/wp-admin/') || 
				strpos($value, '/wp-content/') || 
				strpos($value, '/wp-includes/') || 
				strpos($value, '/xmlrpc.php') || 
				strpos($value, '/wp-login.php') || 
				strpos($value, 'uscesid=')
			) continue;
			
			if( '"' == substr($value, 0, 1) ){
				$rep = trim($value,'"');
				if( strpos($rep, '?') )
					$rep = '"'.$rep.'&uscesid=' . $usces->get_uscesid('mobile') . '"';
				else
					$rep = '"'.$rep.'?uscesid=' . $usces->get_uscesid('mobile') . '"';
					
			}else{
				$rep = trim($value,'\'');
				if( strpos($rep, '?') )
					$rep = '\''.$rep.'&uscesid=' . $usces->get_uscesid('mobile') . '\'';
				else
					$rep = '\''.$rep.'?uscesid=' . $usces->get_uscesid('mobile') . '\'';
					
			}
			
			$buffer = str_replace($value, $rep, $buffer);
		}
	}
	
	return $buffer;
}

function wcmb_set_default_theme(){
	global $usces;
	$garakpath = WP_CONTENT_DIR.'/themes/mobile_garak_default';
	$smartpath = WP_CONTENT_DIR.'/themes/mobile_smart_default';
	$garakresourcepath = WP_PLUGIN_DIR.'/wcex_mobile/themes/mobile_garak_default';
	$smartresourcepath = WP_PLUGIN_DIR.'/wcex_mobile/themes/mobile_smart_default';
	if( !file_exists($garakresourcepath) || !file_exists($smartresourcepath) )
		return false;

	if( !file_exists($garakpath) ) {
		mkdir($garakpath);
		$usces->dir_copy($garakresourcepath, $garakpath);
	}
	if( !file_exists($smartpath) ) {
		mkdir($smartpath);
		$usces->dir_copy($smartresourcepath, $smartpath);
	}
}

function wcmb_kddi_url($buffer){
	
	return $buffer;
}

function wcmb_final_size($buffer){
	$res1 = preg_match_all('/<img[^>]*src=["|\']([^>^ ]*)["|\'][^>]*>/', $buffer, $matches);
	if( !$res1 )
		return filesize($orgfilepath);

	$total = 0;
	for( $i=0; $i<count($matches[0]); $i++ ){
		$src = $matches[1][$i];
		$res2 = preg_match('/('.str_replace('/', '\/', get_option('siteurl')).'|'.str_replace('/', '\/', USCES_SSL_URL_ADMIN).')([^"\']*)/', $src, $srcmatch);
		if( !$res2 )
			continue;
			
		$orgfilepath = str_replace($srcmatch[1], ABSPATH, $srcmatch[0]);
		if( file_exists($orgfilepath) ){
			$size = filesize($orgfilepath);
		}
		//usces_log('size : '.$size.' : '.$path, 'acting_transaction.log');
		$total += $size;
	}
	$total += strlen($buffer);
	return $total;
}

function wcmb_cf7_action($buffer){
	preg_match('/<div class="wpcf7"([^>]*)><form action="([^"#]*)([^"]*)"/', $buffer, $matches );
	if( isset($matches[2]) ){
		$rep = rtrim($matches[2],'%2F') . '&mbtime=' . time();
		$buffer = preg_replace('/<div class="wpcf7"([^>]*)><form action="([^"#]*)([^"]*)"/', '<div class="wpcf7"${1}><form action="'.$rep.'"', $buffer);
		if( 1 === wcmb_get_browser() ){
			preg_match('/<div style="display: none;">(.*_wpcf7_unit_tag[^<]*)<\/div>/s', $buffer, $matches2 );
			$buffer = preg_replace('/<div style="display: none;">(.*_wpcf7_unit_tag[^<]*)<\/div>/s', '${1}', $buffer );
		}
	}
	return $buffer;
}

function wcmb_conclusion($buffer){
	global $usces, $wcmb, $wcmb_options;
	
	if( DOCOMO !== $wcmb['device_div'] && SOFTBANK !== $wcmb['device_div'] && KDDI !== $wcmb['device_div'] )
		return $buffer;

	$browser = wcmb_get_browser();
	

	$res1 = preg_match_all('/<img[^>]*src=["|\']([^>^ ]*)["|\'][^>]*>/', $buffer, $matches);
	if( !$res1 )
		return $buffer;

	$total = 0;
	for( $i=0; $i<count($matches[0]); $i++ ){
		$src = $matches[1][$i];
		$res2 = preg_match('/('.str_replace('/', '\/', get_option('siteurl')).'|'.str_replace('/', '\/', USCES_SSL_URL_ADMIN).')([^"\']*)/', $src, $srcmatch);
		if( !$res2 )
			continue;
			
		$orgfilepath = str_replace($srcmatch[1], ABSPATH, $srcmatch[0]);
		$orgfileurl = $srcmatch[0];
		if( file_exists($orgfilepath) ){
			$size = filesize($orgfilepath);
		}
		$total += $size;
	}
	$total += strlen($buffer);
	
	if( 1 === $browser || (2 === $browser && 300000 < $total) ){
		
		for( $i=0; $i<count($matches[0]); $i++ ){
			$img = $matches[0][$i];
			$src = $matches[1][$i];
			$srcmatch = array();
			$res2 = preg_match('/('.str_replace('/', '\/', get_option('siteurl')).'|'.str_replace('/', '\/', USCES_SSL_URL_ADMIN).')([^"\']*)/', $src, $srcmatch);
			if( !$res2 )
				continue;
			$res3 = preg_match('/class=["|\'][^"\']*size-optimized[^"\']*["|\']/', $img);
			if( $res3 )
				continue;
			
			preg_match('/width=["|\']([^"\']*)["|\']/', $img, $widths);
			preg_match('/height=["|\']([^"\']*)["|\']/', $img, $heights);
			if( !isset($widths[1]) || !isset($heights[1]))
				continue;
				
			$path_parts = pathinfo($src);
			$file_exte = strtolower($path_parts['extension']);
			$file_name = $path_parts['filename'];
			$basename = $file_name . '-' . $widths[1] . 'x' . $heights[1] . '.gif';
			$orgfilepath = str_replace($srcmatch[1], ABSPATH, $srcmatch[0]);
			$orgfileurl = $srcmatch[0];
			$optfilepath = ABSPATH . WCMB_IMGPATH . $basename;
			$optfileurl = $srcmatch[1] . WCMB_IMGPATH . $basename;
			
			if( !file_exists($optfilepath) ){
				if( !file_exists($orgfilepath) )
					continue;
				
				wcmb_make_image_cache($orgfilepath, $optfilepath, $widths[1], $browser, 'item');
			}
				
			if( !file_exists($optfilepath) ){

			}else{
				list($optwidth, $optheight) = getimagesize($optfilepath);
				$pattern = array(
					'/class=["|\']([^"\']*)(size-[^"\'\s]+ )([^"\']*)["|\']/', 
					'/class=["|\']([^"\']*)["|\']/', 
					'/src=(["|\'])' . str_replace('/', '\/', $orgfileurl) . '(["|\'])/', 
					'/width=["|\']([^"\']*)["|\']/', 
					'/height=["|\']([^"\']*)["|\']/'
				);
				$replacement = array(
					'class="${1}${3}"', 
					'class="${1} size-optimized"', 
					'src=${1}' . $optfileurl . '${2}', 
					'width="' . $optwidth . '"', 
					'height="' . $optheight . '"'
				);
				$optimg = preg_replace($pattern, $replacement, $img);
				$buffer = str_replace($img, '<br />'.$optimg.'<br />', $buffer);
			}
		}
		
	}
	return $buffer;
}

function wcmb_filter_content_image($content){
	global $usces, $wcmb, $wcmb_options;
	
	if( DOCOMO !== $wcmb['device_div'] && SOFTBANK !== $wcmb['device_div'] && KDDI !== $wcmb['device_div'] )
		return $content;

	$browser = wcmb_get_browser();

	$res1 = preg_match_all('/<img[^>]*src=["|\']([^>^ ]*)["|\'][^>]*>/', $content, $matches);
	if( !$res1 )
		return $content;
		
	for( $i=0; $i<count($matches[0]); $i++ ){
		$img = $matches[0][$i];
		$src = $matches[1][$i];
		$res2 = preg_match('/('.str_replace('/', '\/', get_option('siteurl')).'|'.str_replace('/', '\/', USCES_SSL_URL_ADMIN).')([^"\']*)/', $src, $srcmatch);
		if( !$res2 )
			continue;
		
		$path_parts = pathinfo($src);
		$file_exte = strtolower($path_parts['extension']);
		$file_name = $path_parts['filename'];
		$newname = $file_name . '.gif';

		$orgfilepath = str_replace($srcmatch[1], ABSPATH, $srcmatch[0]);
		$orgfileurl = $srcmatch[0];
		$optfilepath = ABSPATH . WCMB_IMGPATH . $newname;
		$optfileurl = $srcmatch[1] . WCMB_IMGPATH . $newname;
		
		if( !file_exists($optfilepath) ){
			if( !file_exists($orgfilepath) )
				continue;
			
			wcmb_make_image_cache($orgfilepath, $optfilepath, 50, $browser);
		}
			
		if( !file_exists($optfilepath) ){
			$optwidth =  10;
			$optheight =  10;
			$optfileurl = $orgfileurl;
		}else{
			list($optwidth, $optheight) = getimagesize($optfilepath);
		}
		switch ($browser){
			case 1:
				$pattern = array(
					'/(<a [^>]*>)' . str_replace('/', '\/', $img) . '(<\/a>)/'
				);
				$replacement = array(
					'<br />${1}' . $newname . '${2}<br />'
				);
				$content = preg_replace($pattern, $replacement, $content);
				$content = str_replace($img, '<a href="'.$optfileurl.'">'.$newname.'</a>', $content);
				break;
			case 2:
			case 3:
			default:
				$pattern = array(
					'/class=["|\']([^"\']*)(size-[^"\'\s]+ )([^"\']*)["|\']/', 
					'/class=["|\']([^"\']*)["|\']/', 
					'/src=(["|\'])' . str_replace('/', '\/', $orgfileurl) . '(["|\'])/', 
					'/width=["|\']([^"\']*)["|\']/', 
					'/height=["|\']([^"\']*)["|\']/'
				);
				$replacement = array(
					'class="${1}${3}"', 
					'class="${1} size-optimized"', 
					'src=${1}' . $optfileurl . '${2}', 
					'width="' . $optwidth . '"', 
					'height="' . $optheight . '"'
				);
				$optimg = preg_replace($pattern, $replacement, $img);
				$content = str_replace($img, '<br />'.$optimg.'<br />', $content);
		}
	}
	return $content;
}

function wcmb_make_image_cache($orgfilepath, $optfilepath, $max, $browser, $flag = 'item'){
	if( 1 === $browser){
		$quality = 80;
	}else{
		$quality = 100;
	}
	
	list($pre_width, $pre_height, $type, $attr) = getimagesize($orgfilepath);
	if( 'item' == $flag ){
		$width = $max;
		$height = (int)($pre_height * $max / $pre_width);
	}else{
		if( $pre_width >= $pre_height ){
			$width = $max;
			$height = (int)($pre_height * $max / $pre_width);
		}else{
			$height = $max;
			$width = (int)($pre_width * $max / $pre_height);
		}
	}
	
	$path_parts = pathinfo($orgfilepath);
	$file_exte = strtolower($path_parts['extension']);
	$file_name = $path_parts['filename'];
	$basename = $file_name . '.gif';

	if( "jpg" == $file_exte || "jpeg" == $file_exte ){
		$image = ImageCreateFromJPEG($orgfilepath);
	}else if( $file_exte == 'png' ){
		$image = ImageCreateFromPNG($orgfilepath);
	}else{
		$image = ImageCreateFromGIF($orgfilepath);
	}
	if( $image ){
		$new_image = ImageCreateTrueColor($width, $height);
		ImageCopyResampled($new_image,$image,0,0,0,0,$width,$height,$pre_width,$pre_height);
		ImageGIF($new_image, $optfilepath, $quality);
		ImageDestroy($image);
		ImageDestroy($new_image);
	}
}

function is_support_ip( $str, $ip ){
	$mask = "";
	$ip_str = "";
	$ary_temp = explode( "/", $str );
	if( is_array( $ary_temp ) ){
		$mask = isset( $ary_temp[1] ) ? $ary_temp[1] : "";
		$ip_str = $ary_temp[0];
	}
	if( strlen( $mask ) <= 0 || strlen( $ip_str ) <= 0 ){
		return( false );
	}
	$ary_target = explode( ".", $ip_str );
	$ary_access = explode( ".", $ip );
	
	if( !is_array( $ary_target ) || !is_array( $ary_access ) ){
		return( false );
	}
	if( count( $ary_target ) < 4 || count( $ary_access ) < 4 ){
		return( false );
	}
	
	$bin_target = "";
	$bin_access = "";
	$bin_mask = str_pad( str_repeat( "1", $mask ), 32, "0", STR_PAD_RIGHT );
	for( $i = 0; $i < 4; $i++ ){
		$bin_target .= str_pad( decbin( $ary_target[$i] ), 8, "0", STR_PAD_LEFT );
		$bin_access .= str_pad( decbin( $ary_access[$i] ), 8, "0", STR_PAD_LEFT );
	}
	
	if( bindec( $bin_target & $bin_mask ) == bindec( $bin_access & $bin_mask ) ){
		return( true );
	}else{
		return( false );
	}
}

function wcmb_check_garak($device_div){
	global $wcmb;
	$ip = apply_filters( 'wcmb_filter_remote_address', $_SERVER['REMOTE_ADDR'] );
	$flg = false;
	foreach( $wcmb['band'][$device_div] as $band ){
		if( is_support_ip( $band, $ip ) ){
			$flg = true;
			break;
		}
	}
	unset($wcmb['band']);
	return $flg;
}

function wcmb_cart_row_of_smartphone( $row, $cart, $materials ){
	extract($materials);
	
	$row = '';
	if ( empty($options) ) {
		$optstr =  '';
		$options =  array();
	}
	$row .= '<tr>
		<td rowspan="2">' . ($i + 1) . '</td>
		<td rowspan="2">';
		$cart_thumbnail = '<a href="' . get_permalink($post_id) . '">' . wp_get_attachment_image( $pictid, array(60, 60), true ) . '</a>';
		$row .= apply_filters('usces_filter_cart_thumbnail', $cart_thumbnail, $post_id, $pictid, $i,$cart_row);
		$row .= '</td><td colspan="6" class="aleft">' . esc_html($cartItemName) . '<br />';
		
	if( is_array($options) && count($options) > 0 ){
		$optstr = '';
		foreach($options as $key => $value){
			if( !empty($key) ) {
				$key = urldecode($key);
				if(is_array($value)) {
					$c = '';
					$optstr .= esc_html($key) . ' : '; 
					foreach($value as $v) {
						$optstr .= $c.nl2br(esc_html(urldecode($v)));
						$c = ', ';
					}
					$optstr .= "<br />\n"; 
				} else {
					$optstr .= esc_html($key) . ' : ' . nl2br(esc_html(urldecode($value))) . "<br />\n"; 
				}
			}
		}
		$row .= apply_filters( 'usces_filter_option_cart', $optstr, $options);
	}
	$row .= apply_filters( 'usces_filter_option_info_cart', '', $cart_row );
	$row .= '</td>';
		$row .= '</tr>';
		$row .= '<tr>
		<td colspan="2" class="aright">';
	if( usces_is_gptekiyo($post_id, $sku_code, $quantity) ) {
		$usces_gp = 1;
		$Business_pack_mark = '<img src="' . get_template_directory_uri() . '/images/gp.gif" alt="' . __('Business package discount','usces') . '" /><br />';
		$row .= apply_filters('usces_filter_itemGpExp_cart_mark', $Business_pack_mark);
	}
	$row .= usces_crform($skuPrice, true, false, 'return') . '
		</td>
		<td><input name="quant[' . $i . '][' . $post_id . '][' . $sku . ']" class="quantity" type="text" value="' . esc_attr($cart_row['quantity']) . '" /></td>
		<td class="aright">' . usces_crform(($skuPrice * $cart_row['quantity']), true, false, 'return') . '</td>
		<td ' . $red . '>' . $stock . '</td>
		<td>';
	foreach($options as $key => $value){
		if(is_array($value)) {
			foreach($value as $v) {
				$row .= '<input name="itemOption[' . $i . '][' . $post_id . '][' . $sku . '][' . $key . '][' . $v . ']" type="hidden" value="' . $v . '" />';
			}
		} else {
			$row .= '<input name="itemOption[' . $i . '][' . $post_id . '][' . $sku . '][' . $key . ']" type="hidden" value="' . $value . '" />';
		}
	}
	$row .= '<input name="itemRestriction[' . $i . ']" type="hidden" value="' . $itemRestriction . '" />
		<input name="stockid[' . $i . ']" type="hidden" value="' . $stockid . '" />
		<input name="itempostid[' . $i . ']" type="hidden" value="' . $post_id . '" />
		<input name="itemsku[' . $i . ']" type="hidden" value="' . $sku . '" />
		<input name="zaikonum[' . $i . '][' . $post_id . '][' . $sku . ']" type="hidden" value="' . esc_attr($skuZaikonum) . '" />
		<input name="skuPrice[' . $i . '][' . $post_id . '][' . $sku . ']" type="hidden" value="' . esc_attr($skuPrice) . '" />
		<input name="advance[' . $i . '][' . $post_id . '][' . $sku . ']" type="hidden" value="' . esc_attr($advance) . '" />
		<input name="delButton[' . $i . '][' . $post_id . '][' . $sku . ']" class="delButton" type="submit" value="' . __('Delete','usces') . '" />
		</td>
	</tr>';
	
	return $row;
}

function wcmb_confirm_row_of_smartphone( $row, $cart, $materials ){
	extract($materials);
	
	$row = '';
	if (empty($options)) {
		$optstr =  '';
		$options =  array();
	}

	$row .= '<tr>
		<td rowspan="2">' . ($i + 1) . '</td>
		<td rowspan="2">';
	$cart_thumbnail = wp_get_attachment_image( $pictid, array(60, 60), true );
	$row .= apply_filters('usces_filter_cart_thumbnail', $cart_thumbnail, $post_id, $pictid, $i, $cart_row);
	$row .= '</td><td colspan="4" class="aleft">' . $cartItemName . '<br />';
	if( is_array($options) && count($options) > 0 ){
		$optstr = '';
		foreach($options as $key => $value){
			if( !empty($key) ) {
				$key = urldecode($key);
				if(is_array($value)) {
					$c = '';
					$optstr .= esc_html($key) . ' : '; 
					foreach($value as $v) {
						$optstr .= $c.nl2br(esc_html(urldecode($v)));
						$c = ', ';
					}
					$optstr .= "<br />\n"; 
				} else {
					$optstr .= esc_html($key) . ' : ' . nl2br(esc_html(urldecode($value))) . "<br />\n"; 
				}
			}
		}
		$row .= apply_filters( 'usces_filter_option_confirm', $optstr, $options);
	}
	$row .= apply_filters( 'usces_filter_option_info_confirm', '', $cart_row );
	$row .= '</td>';
	$row .= '<td></td>';
		$row .= '</tr>';
		$row .= '<tr>
		<td colspan="2" class="aright">' . usces_crform($skuPrice, true, false, 'return') . '</td>
		<td>' . $cart_row['quantity'] . '</td>
		<td class="aright">' . usces_crform(($skuPrice * $cart_row['quantity']), true, false, 'return') . '</td>
		<td>' . apply_filters('usces_additional_confirm',  NULL, array($i, $post_id, $sku_code)) . '</td>';
	$row .'</tr>';
	
	return $row;
}

function wcmb_is_member_logged_in( $id = false ) {
	if( defined( 'USCES_VERSION' ) and version_compare( USCES_VERSION, '1.2.2', '>=' ) ) {
		global $usces;
		$login = $usces->is_member_logged_in( $id );
	} else {
		$login = ( empty($_SESSION['usces_member']['ID']) ) ? false : true;
	}
	return $login;
}

function wcmb_action_theme_switcher(){
	global $wcmb_options;

	if( $wcmb_options['smart_pc_theme'] ){
		return;
	}
	
	if( !$wcmb_options['smart_theme_switch'] ){
		wcmb_theme_smart();
		return;
	}

	if( isset($_POST['themetopc']) ){
		$_SESSION['wcmb_smart_theme'] = 'pc';
	}elseif( isset($_POST['themetosmart']) ){
		$_SESSION['wcmb_smart_theme'] = 'smart';
		wcmb_theme_smart();
	}
			
	if( !isset($_SESSION['wcmb_smart_theme']) )
		$_SESSION['wcmb_smart_theme'] = 'smart';
		
	if( 'smart' == $_SESSION['wcmb_smart_theme'] ){
		wcmb_theme_smart();
	}
				
	add_action('wp_footer', 'wcmb_smart_theme_switch');
}

function wcmb_smart_theme_switch(){
	if( 'smart' == $_SESSION['wcmb_smart_theme'] ){
	?>
	<style type="text/css">
		.theme_switcher {
			color: red;
		}
	</style>
	<form method="post">
		<input type="submit" name="themetopc" class="theme_switcher" value="PCデザインに切り替える" />
	</form>
	<?php
	}else{
	?>
	<style type="text/css">
		.theme_switcher {
			color: red;
		}
	</style>
	<form method="post">
		<input type="submit" name="themetosmart" class="theme_switcher" value="スマホデザインに切り替える" />
	</form>
	<?php
	}
}

function wcmb_theme_smart(){
	add_filter('template', 'wcmb_smartphone_template');
	add_filter('stylesheet', 'wcmb_smartphone_stylesheet');
	add_action('wp_head', 'wcmb_smartphone_wp_head');
	add_action('init', 'wcmb_remove_filter', 90);
	add_filter('usces_filter_single_item_inform', 'wcmb_filter_single_item_inform');
	add_action('usces_action_single_item_inform', 'wcmb_action_single_item_inform');
	if( defined('WCEX_DLSELLER') ) {
		if( defined( 'USCES_VERSION' ) and version_compare( USCES_VERSION, '1.2.2', '>=' ) ) {
			remove_filter('usces_filter_template_redirect', 'dlseller_filter_template_redirect', 1);
			add_filter('usces_filter_template_redirect', 'wcmb_smartphone_template_redirect', 1);
		} else {
			remove_filter('usces_action_template_redirect', 'dlseller_filter_template_redirect', 1);
			add_filter('usces_action_template_redirect', 'wcmb_smartphone_template_redirect', 1);
		}
	}
}