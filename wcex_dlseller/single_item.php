<?php
usces_the_item();
$usces_item = dlseller_get_item();
usces_have_skus();
$charging_type = $this->getItemChargingType($post->ID);
$division = dlseller_get_division( $post->ID );

$html = '<!-- single_item.php -->'."\n";

$html .= '<div id="itempage">'."\n";

$html .= '<div class="itemimg">'."\n";
$html .= '<a href="' . usces_the_itemImageURL(0, 'return') . '"';
$html = apply_filters('usces_itemimg_anchor_rel', $html);
$html .= '>';
$itemImage = usces_the_itemImage(0, 300, 300, $post, 'return');
$html .= apply_filters('usces_filter_the_itemImage', $itemImage, $post);
$html .= '</a>'."\n";
$html .= '</div>'."\n";
	
$html .= '<h3>' . usces_the_itemName( 'return' ) . '&nbsp;（' . usces_the_itemCode( 'return' ) . '）</h3>'."\n";
/* download contents ********************************************************************/
if( 'data' == $division ):

	$html .= '<div class="exp">'."\n";
	$html .= '<div class="field">'."\n";
	if( isset($this->itemsku['value']['cprice']) && $this->itemsku['value']['cprice'] > 0 ){
		$usces_listprice = __('List price', 'usces') . usces_guid_tax('return');
		$html .='<div class="field_name">' . apply_filters('usces_filter_listprice_label', $usces_listprice, __('List price', 'usces'), usces_guid_tax('return')) . '</div>'."\n";
		$html .='<div class="field_cprice">' . usces_the_itemCpriceCr('return');
		if( 'continue' == $charging_type ){
			$html .= '(' . dlseller_frequency_name($post->ID, 'amount', 'return') . ')';
		}
		$html .= '</div>'."\n";
	}
	$usces_sellingprice = __('selling price', 'usces') . usces_guid_tax('return');
	$html .= '<div class="field_name">' . apply_filters('usces_filter_sellingprice_label', $usces_sellingprice, __('selling price', 'usces'), usces_guid_tax('return')) . '</div>'."\n";
	$html .= '<div class="field_price">' . usces_the_itemPriceCr('return');
	if( 'continue' == $charging_type ){
		$html .= '(' . dlseller_frequency_name($post->ID, 'amount', 'return') . ')';
	}
	$html .= '</div>'."\n";
	$html .= '</div>'."\n";
	if( 'continue' == $charging_type ){
		// Charging Type Continue
		$html .= '<div class="field">'."\n";
		$html .= '<table class="dlseller">'."\n";
		$html .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . dlseller_first_charging( $post->ID ) . '</td></tr>'."\n";
		if( 0 < (int)$usces_item['dlseller_interval'] ){
			$html .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month（Automatic Renewal）', 'dlseller') . '</td></tr>'."\n";
		}
		$html .= '</table>'."\n";
		$html .= '</div>'."\n";
	}
	$item_custom = usces_get_item_custom( $post->ID, 'table', 'return' );
	if($item_custom){
		$html .= '<div class="field">'."\n";
		$html .= $item_custom;
		$html .= '</div>'."\n";
	}
		$html .= '<div class="field"><table class="dlseller">'."\n";
		$html .= '<tr><th>' . __('dlValidity(days)', 'dlseller') . '</th><td>' . esc_html(usces_dlseller_validity($post)) . '</td></tr>'."\n";
		$html .= '<tr><th>' . __('File Name', 'dlseller') . '</th><td>' . esc_html(usces_dlseller_filename($post)) . '</td></tr>'."\n";
		$html .= '<tr><th>' . __('Release Date', 'dlseller') . '</th><td>' . esc_html(usces_get_itemMeta('_dlseller_date', $post->ID, 'return')) . '</td></tr>'."\n";
		$html .= '<tr><th>' . __('Version', 'dlseller') . '</th><td>' . esc_html(usces_get_itemMeta('_dlseller_version', $post->ID, 'return')) . '</td></tr>'."\n";
		$html .= '<tr><th>' . __('Author', 'dlseller') . '</th><td>' . esc_html(usces_get_itemMeta('_dlseller_author', $post->ID, 'return')) . '</td></tr>'."\n";
		$html = apply_filters('dlseller_filter_item_field', $html, $post);
		$html .= '</table>'."\n";
		$html .= '</div>'."\n";
		
	$html .= $content."\n";
	
	$html .= '</div><!-- end of exp -->'."\n";
	$html .= usces_the_itemGpExp('return')."\n";
	
	$html .= '<form action="' . USCES_CART_URL . '" method="post">'."\n";
	$html .= '<div class="skuform" align="right">'."\n";
	if (usces_is_options()) {
		$html .= "<table class='item_option'><caption>" . apply_filters('usces_filter_single_item_options_caption', __('Please appoint an option.', 'usces'), $post) . "</caption>\n";
		while (usces_have_options()) {
			$opttr = "<tr><th>" . esc_html($this->itemopt['key']) . '</th><td>' . usces_the_itemOption(usces_getItemOptName(),'','return') . "</td></tr>";
			$html .= apply_filters('usces_filter_singleitem_option', $opttr, $this->itemopt['key'], usces_getItemOptName()) . "\n";
		}
		$html .= "</table>\n";
	}

		$html .= '<div style="margin-top:10px">' . usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0, 'return') . '</div>'."\n";

	$html .= '</div><!-- end of skuform -->'."\n";
	$html = apply_filters('usces_filter_single_item_inform', $html);
	$html .= "\n".'</form>'."\n";
	$html .= '<div class="clear"></div>'."\n";

/* service one charge ***************************************************************/
elseif( 'service' == $division ):

	$html .= '<div class="exp">'."\n";
	
	$html .= '<div class="field">'."\n";
	if( $this->itemsku['value']['cprice'] > 0 ){
		$usces_listprice = __('List price', 'usces') . usces_guid_tax('return');
		$html .='<div class="field_name">' . apply_filters('usces_filter_listprice_label', $usces_listprice, __('List price', 'usces'), usces_guid_tax('return')) . '</div>'."\n";
		$html .='<div class="field_cprice">' . usces_the_itemCpriceCr('return');
		if( 'continue' == $charging_type ){
			$html .= '(' . dlseller_frequency_name($post->ID, 'amount', 'return') . ')';
		}
		$html .= '</div>'."\n";
	}
	$usces_sellingprice = __('selling price', 'usces') . usces_guid_tax('return');
	$html .= '<div class="field_name">' . apply_filters('usces_filter_sellingprice_label', $usces_sellingprice, __('selling price', 'usces'), usces_guid_tax('return')) . '</div>'."\n";
	$html .= '<div class="field_price">' . usces_the_itemPriceCr('return');
	if( 'continue' == $charging_type ){
		$html .= '(' . dlseller_frequency_name($post->ID, 'amount', 'return') . ')';
	}
	$html .= '</div>'."\n";
	$html .= '</div>'."\n";
	if( 'continue' == $charging_type ){
		// Charging Type Continue
		$html .= '<div class="field">'."\n";
		$html .= '<table class="dlseller">'."\n";
		$html .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . dlseller_first_charging( $post->ID ) . '</td></tr>'."\n";
		if( 0 < (int)$usces_item['dlseller_interval'] ){
			$html .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month（Automatic Renewal）', 'dlseller') . '</td></tr>'."\n";
		}
		$html .= '</table>'."\n";
		$html .= '</div>'."\n";
	}
	$item_custom = usces_get_item_custom( $post->ID, 'table', 'return' );
	if($item_custom){
		$html .= '<div class="field">'."\n";
		$html .= $item_custom;
		$html .= '</div>'."\n";
	}
		
	$html .= $content."\n";
	
	$html .= '</div><!-- end of exp -->'."\n";
	$html .= usces_the_itemGpExp('return')."\n";

	$html .= '<form action="' . USCES_CART_URL . '" method="post">'."\n";
	$html .= '<div class="skuform" align="right">'."\n";
	if (usces_is_options()) {
		$html .= "<table class='item_option'><caption>" . apply_filters('usces_filter_single_item_options_caption', __('Please appoint an option.', 'usces'), $post) . "</caption>\n";
		while (usces_have_options()) {
			$opttr = "<tr><th>" . esc_html($this->itemopt['key']) . '</th><td>' . usces_the_itemOption(usces_getItemOptName(),'','return') . "</td></tr>";
			$html .= apply_filters('usces_filter_singleitem_option', $opttr, $this->itemopt['key'], usces_getItemOptName()) . "\n";
		}
		$html .= "</table>\n";
	}

		$html .= '<div style="margin-top:10px">' . usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0, 'return') . '</div>'."\n";

	$html .= '</div><!-- end of skuform -->'."\n";
	$html = apply_filters('usces_filter_single_item_inform', $html);
	$html .= "\n".'</form>'."\n";
	$html .= '<div class="clear"></div>'."\n";


/* shipped item ***************************************************************/
else :

	if(usces_sku_num() === 1) { //1SKU
		
		$html .= '<div class="exp">'."\n";
		$html .= '<div class="field">'."\n";
		if( usces_the_itemCprice('return') > 0 ){
			$usces_listprice = __('List price', 'usces') . usces_guid_tax('return');
			$html .= '<div class="field_name">' . apply_filters('usces_filter_listprice_label', $usces_listprice, __('List price', 'usces'), usces_guid_tax('return')) . '</div>'."\n";
			$html .= '<div class="field_cprice">' . usces_the_itemCpriceCr('return');
			if( 'continue' == $charging_type ){
				$html .= '(' . dlseller_frequency_name($post->ID, 'amount', 'return') . ')';
			}
			$html .= '</div>'."\n";
		}
		$usces_sellingprice = __('selling price', 'usces') . usces_guid_tax('return');
		$html .= '<div class="field_name">' . apply_filters('usces_filter_sellingprice_label', $usces_sellingprice, __('selling price', 'usces'), usces_guid_tax('return')) . '</div>'."\n";
		$html .= '<div class="field_price">' . usces_the_itemPriceCr('return');
		if( 'continue' == $charging_type ){
			$html .= '(' . dlseller_frequency_name($post->ID, 'amount', 'return') . ')';
		}
		$html .= '</div>'."\n";
		$html .= '</div>'."\n";
		$singlestock = '<div class="field">' . __('stock status', 'usces') . ' : ' . esc_html(usces_the_itemZaiko('return')) . '</div>'."\n";
		$html .= apply_filters('single_item_stock_field', $singlestock);
		if( 'continue' == $charging_type ){
			// Charging Type Continue
			$html .= '<div class="field">'."\n";
			$html .= '<table class="dlseller">'."\n";
			$html .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . dlseller_first_charging( $post->ID ) . '</td></tr>'."\n";
			if( 0 < (int)$usces_item['dlseller_interval'] ){
				$html .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month（Automatic Renewal）', 'dlseller') . '</td></tr>'."\n";
			}
			$html .= '</table>'."\n";
			$html .= '</div>'."\n";
		}

		$item_custom = usces_get_item_custom( $post->ID, 'list', 'return' );
		if($item_custom){
			$html .= '<div class="field">'."\n";
			$html .= $item_custom;
			$html .= '</div>'."\n";
		}
			
		$html .= $content."\n";
		$html .= '</div><!-- end of exp -->'."\n";
		$html .= usces_the_itemGpExp('return');

		$html .= '<form action="' . USCES_CART_URL . '" method="post">'."\n";
		$html .= '<div class="skuform" align="right">'."\n";
		if (usces_is_options()) {
			$html .= "<table class='item_option'><caption>" . apply_filters('usces_filter_single_item_options_caption', __('Please appoint an option.', 'usces'), $post) . "</caption>\n";
			while (usces_have_options()) {
				$opttr = "<tr><th>" . esc_html(usces_getItemOptName()) . '</th><td>' . usces_the_itemOption(usces_getItemOptName(),'','return') . "</td></tr>";
				$html .= apply_filters('usces_filter_singleitem_option', $opttr, usces_getItemOptName(), NULL) . "\n";
			}
			$html .= "</table>\n";
		}
		if( !usces_have_zaiko() ){
			$html .= '<div class="zaiko_status">' . apply_filters('usces_filters_single_sku_zaiko_message', __('Sold Out', 'usces')) . '</div>'."\n";
		}else{
			$html .= '<div style="margin-top:10px">'.__('Quantity', 'usces').usces_the_itemQuant('return') . esc_html(usces_the_itemSkuUnit('return')) . usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0, 'return') . '</div>'."\n";
			$html .= '<div class="error_message">' . usces_singleitem_error_message($post->ID, usces_the_itemSku('return'), 'return') . '</div>'."\n";
		}
	
		$html .= '</div><!-- end of skuform -->'."\n";
		$html = apply_filters('usces_filter_single_item_inform', $html);
		$html .= "\n".'</form>'."\n";
		$html .= apply_filters('single_item_single_sku_after_field', NULL);
		
	} elseif(usces_sku_num() > 1) { //some SKU
		$html .= '<div class="exp">'."\n";
		$html .= $content."\n";
		if( 'continue' == $charging_type ){
			// Charging Type Continue
			$html .= '<div class="field">'."\n";
			$html .= '<table class="dlseller">'."\n";
			$html .= '<tr><th>' . __('First Withdrawal Date', 'dlseller') . '</th><td>' . dlseller_first_charging( $post->ID ) . '</td></tr>'."\n";
			if( 0 < (int)$usces_item['dlseller_interval'] ){
				$html .= '<tr><th>' . __('Contract Period', 'dlseller') . '</th><td>' . $usces_item['dlseller_interval'] . __('Month（Automatic Renewal）', 'dlseller') . '</td></tr>'."\n";
			}
			$html .= '</table>'."\n";
			$html .= '</div>'."\n";
		}
		$item_custom = usces_get_item_custom( $post->ID, 'list', 'return' );
		if($item_custom){
			$html .= '<div class="field">'."\n";
			$html .= $item_custom;
			$html .= '</div>'."\n";
		}
		$html .= '</div>'."\n";
		
		$html .= '<form action="' . USCES_CART_URL . '" method="post">'."\n";
		$html .= '<div class="skuform">'."\n";
		$html .= '<table class="skumulti">'."\n";
		$html .= '<thead>'."\n";
		$html .= '<tr>'."\n";
		$html .= '<th rowspan="2" class="thborder">'.__('order number', 'usces').'</th>'."\n";
		$html .= '<th colspan="2">'.__('Title', 'usces').'</th>'."\n";
		if( usces_the_itemCprice('return') > 0 ){
			$usces_bothprice = '('.__('List price', 'usces').')'.__('selling price', 'usces') . usces_guid_tax('return');
			$html .= '<th colspan="2">'.apply_filters('usces_filter_bothprice_label', $usces_bothprice, __('List price', 'usces'), __('selling price', 'usces'), usces_guid_tax('return')) . '</th>'."\n";
		}else{
			$usces_sellingprice = __('selling price', 'usces') . usces_guid_tax('return');
			$html .= '<th colspan="2">'.apply_filters('usces_filter_sellingprice_label', $usces_sellingprice, __('selling price', 'usces'), usces_guid_tax('return')) . '</th>'."\n";
		}
		$html .= '</tr>'."\n";
		$html .= '<tr>'."\n";
		$html .= '<th class="thborder">'.__('stock status', 'usces').'</th>'."\n";
		$html .= '<th class="thborder">'.__('Quantity', 'usces').'</th>'."\n";
		$html .= '<th class="thborder">'.__('unit', 'usces').'</th>'."\n";
		$html .= '<th class="thborder">&nbsp;</th>'."\n";
		$html .= '</tr>'."\n";
		$html .= '</thead>'."\n";
		$html .= '<tbody>'."\n";
		do {
			$html .= '<tr>'."\n";
			$html .= '<td rowspan="2">' . esc_html(usces_the_itemSku('return')) . '</td>'."\n";
			$html .= '<td colspan="2" class="skudisp subborder">' . apply_filters('usces_filter_singleitem_skudisp', esc_html(usces_the_itemSkuDisp('return')))."\n";
			if (usces_is_options()) {
				$html .= "<table class='item_option'><caption>" . apply_filters('usces_filter_single_item_options_caption', __('Please appoint an option.', 'usces'), $post) . "</caption>\n";
				while (usces_have_options()) {
					$opttr = "<tr><th>" . esc_html(usces_getItemOptName()) . '</th><td>' . usces_the_itemOption(usces_getItemOptName(),'','return') . "</td></tr>";
					$html .= apply_filters('usces_filter_singleitem_option', $opttr, usces_getItemOptName(), NULL) . "\n";
				}
				$html .= "</table>\n";
	//			while (usces_have_options()) {
	//				$html .= '<br />' . usces_the_itemOption(usces_getItemOptName(),'', 'return');
	//			}
			}
			$html .= '</td>'."\n";
			$html .= '<td colspan="2" class="subborder price">'."\n";
			if( usces_the_itemCprice('return') > 0 ){
				$html .= '<span class="cprice">(' . usces_the_itemCpriceCr('return') . ')'."\n";
			$html .= '</span>'."\n";
			}			
			$html .= '<span class="price">' . usces_the_itemPriceCr('return');
			if( 'continue' == $charging_type ){
				$html .= '(' . dlseller_frequency_name($post->ID, 'amount', 'return') . ')';
			}
			$html .= '</span><br />'."\n";
			$html .= usces_the_itemGpExp('return') . '</td>'."\n";
			$html .= '</tr>'."\n";
			$html .= '<tr>'."\n";
			$html .= '<td class="zaiko">' . usces_the_itemZaiko('return') . '</td>'."\n";
			$html .= '<td class="quant">' . usces_the_itemQuant('return') . '</td>'."\n";
			$html .= '<td class="unit">' . usces_the_itemSkuUnit('return') . '</td>'."\n";
			if( !usces_have_zaiko() ){
				$html .= '<td class="button">' . apply_filters('usces_filters_single_sku_zaiko_message', __('Sold Out', 'usces')) . '</td>'."\n";
			}else{
				$html .= '<td class="button">' . usces_the_itemSkuButton(__('Add to Shopping Cart', 'usces'), 0, 'return') . '</td>'."\n";
			}
			$html .= '</tr>'."\n";
			$html .= '<tr><td colspan="5" class="error_message">' . usces_singleitem_error_message($post->ID, usces_the_itemSku('return'), 'return') . '</td></tr>'."\n";
	
		} while (usces_have_skus());
		$html .= '</tbody>'."\n";
		$html .= '</table>'."\n";
		$html .= '</div><!-- end of skuform -->'."\n";
		$html = apply_filters('usces_filter_single_item_inform', $html);
		$html .= "\n".'</form>'."\n";
		$html .= apply_filters('single_item_multi_sku_after_field', NULL);
	}

endif;


$imageid = usces_get_itemSubImageNums();
if( !empty($imageid) ):
	$html .= '<div class="itemsubimg">';
	foreach ( $imageid as $id ) {
		$html .= '<a href="' . usces_the_itemImageURL($id, 'return') . '"';
		$html = apply_filters('usces_itemimg_anchor_rel', $html);
		$html .= '>';
		$itemImage = usces_the_itemImage($id, 137, 200, $post, 'return');
		$html .= apply_filters('usces_filter_the_SubImage', $itemImage, $post, $id);
		$html .= '</a>'."\n";
	}
	$html .= '</div><!-- end of itemsubimg -->'."\n";
endif;

if (usces_get_assistance_id_list($post->ID)) {
	$html .= '<div class="assistance_item">'."\n";
	$assistanceposts = get_posts('include='.usces_get_assistance_id_list($post->ID));
	if ($assistanceposts) {
		$html .= '<h3>' . esc_html(usces_the_itemCode( 'return' )) . __('An article concerned', 'usces') . '</h3>'."\n";
		$html .= '<ul class="clearfix">'."\n";
		foreach ($assistanceposts as $post) {
			setup_postdata($post);
			usces_the_item();
			$html .= '<li><div class="listbox clearfix">'."\n";
			$html .= '<div class="slit"><a href="' . get_permalink($post->ID) . '" rel="bookmark" title="' . esc_attr($post->post_title) . '">' . usces_the_itemImage(0, 100, 100, $post, 'return') . '</a></div>'."\n";
			$html .= '<div class="detail">'."\n";
			$html .= '<h4>' . esc_html(usces_the_itemName('return')) . '</h4>' . $post->post_excerpt . "\n";
			$html .= '<p>'."\n";
			if (usces_is_skus()) {
				$html .= '￥' . usces_the_firstPrice('return');
			}
			$html .= '<br />'."\n";
			$html .= '&raquo; <a href="' . get_permalink($post->ID) . '" rel="bookmark" title="' . esc_attr($post->post_title) . '">'.__('see the details', 'usces').'</a></p>'."\n";
			$html .= '</div>'."\n";
			$html .= '</div></li>'."\n";
		}
		$html .= '</ul>'."\n";
	}
	
	$html .= '</div><!-- end of assistance_item -->'."\n";
}

$html .= '</div><!-- end of itemspage -->'."\n\n";
?>
