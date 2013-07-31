<?php
add_action( 'usces_action_single_item_outform', 'wcad_action_single_item_outform' );
function wcad_action_single_item_outform( $nouse ) {
	echo wcad_item_single_regular('return');
}

add_filter( 'usces_filter_single_item_outform', 'wcad_filter_single_item_outform' );
function wcad_filter_single_item_outform( $html ) {
	return $html.wcad_item_single_regular('return');
}

function wcad_item_single_regular( $out = '' ) {
	global $post, $usces;
	ob_start();

	if( 'regular' == $usces->getItemChargingType( $post->ID ) ) : 
		$regular_unit = get_post_meta( $post->ID, '_wcad_regular_unit', true );
		if( 'day' == $regular_unit ) {
			$regular_unit_name = __('Daily','autodelivery');
		} elseif( 'month' == $regular_unit ) {
			$regular_unit_name = __('Monthly','autodelivery');
		} else {
			$regular_unit_name = '';
		}
		$regular_interval = get_post_meta( $post->ID, '_wcad_regular_interval', true );
		$regular_frequency = get_post_meta( $post->ID, '_wcad_regular_frequency', true );

		if( usces_sku_num() === 1 ) : 
			if( usces_have_zaiko() ) : 
?>
<div id="wc_regular">
	<p class="wcr_tlt"><?php _e('Regular Purchase', 'autodelivery') ?></p>
	<div class="inside">
		<table>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_interval', __('Interval', 'autodelivery') ); ?></th><td><?php echo $regular_interval; ?><?php echo $regular_unit_name; ?></td></tr>
		<?php if( 1 < (int)$regular_frequency ) : ?>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_frequency', __('Frequency', 'autodelivery') ); ?></th><td><?php echo $regular_frequency; ?><?php _e('times', 'autodelivery'); ?></td></tr>
		<?php else: ?>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_frequency_free', '&nbsp;' ); ?></th><td><?php echo apply_filters( 'wcad_filter_item_single_value_frequency_free', '&nbsp;' ); ?></td></tr>
		<?php endif; ?>
		</table>

		<form action="<?php echo USCES_CART_URL; ?>" method="post">
		<?php usces_the_itemGpExp(); ?>
		<div class="skuform" align="right">
		<?php if( usces_is_options() ) : ?>
			<table class='item_option'>
				<caption><?php _e('Please appoint an option.', 'usces'); ?></caption>
			<?php while( usces_have_options() ) : ?>
				<tr><th><?php usces_the_itemOptName(); ?></th><td><?php wcad_the_itemOption(usces_getItemOptName(),''); ?></td></tr>
			<?php endwhile; ?>
			</table>
		<?php endif; ?>
		<?php if( wcad_get_skurprice() > 0 ) : ?>
			<div class="field">
				<div class="field_name"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?></div>
				<div class="field_price"><?php wcad_the_itemPriceCr(); ?></div>
			</div>
		<?php endif; ?>
			<div style="margin-top:10px"><?php _e('Quantity', 'usces'); ?><?php wcad_the_itemQuant(); ?><?php usces_the_itemSkuUnit(); ?><?php wcad_the_itemSkuButton(__('Apply for a regular purchase', 'autodelivery'), 0); ?></div>
			<div class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></div>
		</div>
		<?php echo apply_filters('wcad_item_single_sku_after_field', NULL); ?>
		<?php do_action('wcad_action_single_item_inform'); ?>
		</form>
	</div>
</div>
<?php
			endif;

		elseif( usces_sku_num() > 1 ) : 

			if( usces_have_zaiko_anyone( $post->ID ) ) : 
				usces_the_item();
?>
<div id="wc_regular">
	<p class="wcr_tlt"><?php _e('Regular Purchase', 'autodelivery') ?></p>
	<div class="inside">
		<table>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_interval', __('Interval', 'autodelivery') ); ?></th><td><?php echo $regular_interval; ?><?php echo $regular_unit_name; ?></td></tr>
		<?php if( 1 < (int)$regular_frequency ) : ?>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_frequency', __('Frequency', 'autodelivery') ); ?></th><td><?php echo $regular_frequency; ?><?php _e('times', 'autodelivery'); ?></td></tr>
		<?php else: ?>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_frequency_free', '&nbsp;' ); ?></th><td><?php echo apply_filters( 'wcad_filter_item_single_value_frequency_free', '&nbsp;' ); ?></td></tr>
		<?php endif; ?>
		</table>

		<form action="<?php echo USCES_CART_URL; ?>" method="post">
		<div class="skuform">
			<table class="skumulti">
				<thead>
				<tr>
					<th rowspan="2" class="thborder"><?php _e('order number', 'usces'); ?></th>
					<th colspan="2"><?php _e('Title', 'usces'); ?></th>
			<?php if( usces_the_itemCprice('return') > 0 ) : ?>
					<th colspan="2">(<?php _e('List price', 'usces'); ?>)<?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?></th>
			<?php else : ?>
					<th colspan="2"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?></th>
			<?php endif; ?>
				</tr>
				<tr>
					<th class="thborder"><?php _e('stock status', 'usces'); ?></th>
					<th class="thborder"><?php _e('Quantity', 'usces'); ?></th>
					<th class="thborder"><?php _e('unit', 'usces'); ?></th>
					<th class="thborder">&nbsp;</th>
				</tr>
				</thead>
				<tbody>
		<?php while( usces_have_skus() ) : ?>
				<tr>
					<td rowspan="2"><?php usces_the_itemSku(); ?></td>
					<td colspan="2" class="skudisp subborder"><?php usces_the_itemSkuDisp(); ?>
			<?php if( usces_is_options() ) : ?>
						<table class='item_option'>
						<caption><?php _e('Please appoint an option.', 'usces'); ?></caption>
				<?php while( usces_have_options() ) : ?>
							<tr>
								<th><?php usces_the_itemOptName(); ?></th>
								<td><?php wcad_the_itemOption(usces_getItemOptName(),''); ?></td>
							</tr>
				<?php endwhile; ?>
						</table>
			<?php endif; ?>
					</td>
					<td colspan="2" class="subborder price">
			<?php if( usces_the_itemCprice('return') > 0 ) : ?>
					<span class="cprice">(<?php usces_the_itemCpriceCr(); ?>)</span>
			<?php endif; ?>
					<span class="price"><?php wcad_the_itemPriceCr(); ?></span>
					<br /><?php usces_the_itemGpExp(); ?>
					</td>
				</tr>
				<tr>
					<td class="zaiko"><?php usces_the_itemZaikoStatus(); ?></td>
					<td class="quant"><?php wcad_the_itemQuant(); ?></td>
					<td class="unit"><?php usces_the_itemSkuUnit(); ?></td>
			<?php if( !usces_have_zaiko() ) : ?>
					<td class="button"><?php echo apply_filters('usces_filters_single_sku_zaiko_message', esc_html(usces_get_itemZaiko('name'))); ?></td>
			<?php else : ?>
					<td class="button"><?php wcad_the_itemSkuButton(__('Apply for a regular purchase', 'autodelivery'), 0); ?></td>
			<?php endif; ?>
				</tr>
				<tr>
					<td colspan="5" class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></td>
				</tr>
		<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		<?php echo apply_filters( 'wcad_single_item_multi_sku_after_field', NULL ); ?>
		<?php do_action( 'wcad_action_single_item_inform' ); ?>
		</form>
	</div>
</div>
<?php
			endif;
		endif;
	endif;

	$html = ob_get_contents();
	ob_end_clean();

	$html = apply_filters( 'wcad_filter_item_single_regular', $html );

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

/* for garak */
add_action( 'usces_action_single_item_outform_garak', 'wcad_action_single_item_outform_garak' );
function wcad_action_single_item_outform_garak( $nouse ) {
	echo wcad_item_single_regular_garak('return');
}

add_filter( 'usces_filter_single_item_outform_garak', 'wcad_filter_single_item_outform_garak' );
function wcad_filter_single_item_outform_garak( $html ) {
	return $html.wcad_item_single_regular_garak('return');
}

function wcad_item_single_regular_garak( $out = '' ) {
	global $post, $usces;
	ob_start();

	if( 'regular' == $usces->getItemChargingType( $post->ID ) ) : 
		$regular_unit = get_post_meta( $post->ID, '_wcad_regular_unit', true );
		if( 'day' == $regular_unit ) {
			$regular_unit_name = __('Daily','autodelivery');
		} elseif( 'month' == $regular_unit ) {
			$regular_unit_name = __('Monthly','autodelivery');
		} else {
			$regular_unit_name = '';
		}
		$regular_interval = get_post_meta( $post->ID, '_wcad_regular_interval', true );
		$regular_frequency = get_post_meta( $post->ID, '_wcad_regular_frequency', true );

		if( usces_sku_num() === 1 ) : 
			if( usces_have_zaiko() ) : 
?>
<div id="wc_regular">
	<p class="wcr_tlt"><?php _e('Regular Purchase', 'autodelivery') ?></p>
	<div class="inside">
		<table>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_interval', __('Interval', 'autodelivery') ); ?></th><td><?php echo $regular_interval; ?><?php echo $regular_unit_name; ?></td></tr>
		<?php if( 1 < (int)$regular_frequency ) : ?>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_frequency', __('Frequency', 'autodelivery') ); ?></th><td><?php echo $regular_frequency; ?><?php _e('times', 'autodelivery'); ?></td></tr>
		<?php else: ?>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_frequency_free', '&nbsp;' ); ?></th><td><?php echo apply_filters( 'wcad_filter_item_single_value_frequency_free', '&nbsp;' ); ?></td></tr>
		<?php endif; ?>
		</table>

		<form action="<?php echo USCES_CART_URL; ?>" method="post">
		<?php wcmb_the_itemGpExp(); ?>
		<?php if( usces_is_options() ) : ?>
		<table class="option">
		<?php while( usces_have_options() ) : ?>
			<tr><th><?php usces_the_itemOptName(); ?></th></tr>
			<tr><td><?php wcmb_the_itemOption(usces_getItemOptName(),''); ?></td></tr>
		<?php endwhile; ?>
		</table>
		<?php endif; ?>
		<?php if( wcad_get_skurprice() > 0 ) : ?>
		<div class="price"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php wcad_the_itemPriceCr(); ?></div>
		<?php endif; ?>
		<div class="cartbutton">
		<?php _e('Quantity', 'usces'); ?><?php wcmb_the_itemQuant(); ?>
		<?php wcad_the_itemSkuButton(__('Apply for a regular purchase', 'autodelivery'), 0); ?>
		</div>
		<div class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></div>
		<?php echo apply_filters('wcad_item_single_sku_after_field_garak', NULL); ?>
		<?php do_action('wcad_action_single_item_inform_garak'); ?>
		</form>
	</div>
</div>
<?php
			endif;

		elseif( usces_sku_num() > 1 ) : 

			if( usces_have_zaiko_anyone( $post->ID ) ) : 
				usces_the_item();
?>
<div id="wc_regular">
	<p class="wcr_tlt"><?php _e('Regular Purchase', 'autodelivery') ?></p>
	<div class="inside">
		<table>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_interval', __('Interval', 'autodelivery') ); ?></th><td><?php echo $regular_interval; ?><?php echo $regular_unit_name; ?></td></tr>
		<?php if( 1 < (int)$regular_frequency ) : ?>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_frequency', __('Frequency', 'autodelivery') ); ?></th><td><?php echo $regular_frequency; ?><?php _e('times', 'autodelivery'); ?></td></tr>
		<?php else: ?>
			<tr><th><?php echo apply_filters( 'wcad_filter_item_single_label_frequency_free', '&nbsp;' ); ?></th><td><?php echo apply_filters( 'wcad_filter_item_single_value_frequency_free', '&nbsp;' ); ?></td></tr>
		<?php endif; ?>
		</table>

		<form action="<?php echo USCES_CART_URL; ?>" method="post">
		<?php while( usces_have_skus() ) : ?>
		<div class="sku_name">
			<div class="name"><?php usces_the_itemSkuDisp(); ?></div>
			<div class="code">(<?php usces_the_itemSku(); ?>)</div>
		</div>
		<?php if( usces_the_itemCprice('return') > 0 ) : ?>
		<div class="cprice"><?php _e('List price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php usces_the_itemCpriceCr(); ?></div>
		<?php endif; ?>
		<div class="price"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?>：<?php wcad_the_itemPriceCr(); ?></div>
		<?php wcmb_the_itemGpExp(); ?>
		<?php if( usces_is_options() ) : ?>
		<table class="option">
		<?php while (usces_have_options()) : ?>
			<tr><th><?php usces_the_itemOptName(); ?></th></tr>
			<tr><td><?php wcmb_the_itemOption(usces_getItemOptName(),''); ?></td></tr>
		<?php endwhile; ?>
		</table>
		<?php endif; ?>
		<?php if( !usces_have_zaiko() ) : ?>
		<div class="soldout"><?php echo apply_filters('usces_filters_single_sku_zaiko_message', esc_html(usces_get_itemZaiko('name'))); ?></div>
		<?php else : ?>
		<div class="cartbutton">
		<?php _e('Quantity', 'usces'); ?><?php wcmb_the_itemQuant(); ?>
		<?php wcad_the_itemSkuButton(__('Apply for a regular purchase', 'autodelivery'), 0); ?>
		</div>
		<?php endif; ?>
		<div class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></div>
		<?php endwhile; ?>
		<?php echo apply_filters( 'wcad_single_item_multi_sku_after_field_garak', NULL ); ?>
		<?php do_action( 'wcad_action_single_item_inform_garak' ); ?>
		</form>
	</div>
</div>
<?php
			endif;
		endif;
	endif;

	$html = ob_get_contents();
	ob_end_clean();

	$html = apply_filters( 'wcad_filter_item_single_regular_garak', $html );

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

/* for smartphone */
add_action( 'usces_action_single_item_outform_smart', 'wcad_action_single_item_outform_smart' );
function wcad_action_single_item_outform_smart( $nouse ) {
	echo wcad_item_single_regular_smart('return');
}

add_filter( 'usces_filter_single_item_outform_smart', 'wcad_filter_single_item_outform_smart' );
function wcad_filter_single_item_outform_smart( $html ) {
	return $html.wcad_item_single_regular_smart('return');
}

function wcad_item_single_regular_smart( $out = '' ) {
	global $post, $usces;
	ob_start();

	if( 'regular' == $usces->getItemChargingType( $post->ID ) ) : 
		$regular_unit = get_post_meta( $post->ID, '_wcad_regular_unit', true );
		if( 'day' == $regular_unit ) {
			$regular_unit_name = __('Daily','autodelivery');
		} elseif( 'month' == $regular_unit ) {
			$regular_unit_name = __('Monthly','autodelivery');
		} else {
			$regular_unit_name = '';
		}
		$regular_interval = get_post_meta( $post->ID, '_wcad_regular_interval', true );
		$regular_frequency = get_post_meta( $post->ID, '_wcad_regular_frequency', true );

		if( usces_sku_num() === 1 ) : 
			if( usces_have_zaiko() ) : 
?>
<div id="wc_regular">
	<p class="wcr_tlt"><?php _e('Regular Purchase', 'autodelivery') ?></p>
	<div class="inside">

<p class="regular_status"><span><?php echo apply_filters( 'wcad_filter_item_single_label_interval', __('Interval', 'autodelivery') ); ?></span><em><?php echo $regular_interval; ?><?php echo $regular_unit_name; ?></em></p>
<?php if( 1 < (int)$regular_frequency ) : ?>
<p class="regular_status"><span><?php echo apply_filters( 'wcad_filter_item_single_label_frequency', __('Frequency', 'autodelivery') ); ?></span><em><?php echo $regular_frequency; ?><?php _e('times', 'autodelivery'); ?></em></p>
<?php else: ?>
<p class="regular_status"><span><?php echo apply_filters( 'wcad_filter_item_single_label_frequency_free', '&nbsp;' ); ?></span><em><?php echo apply_filters( 'wcad_filter_item_single_value_frequency_free', '&nbsp;' ); ?></em></p>
<?php endif; ?>

<form action="<?php echo USCES_CART_URL; ?>" method="post">
<div class="skuform">
<?php usces_the_itemGpExp(); ?>
<?php if (usces_is_options()) : ?>
<p class="opt_ex"><?php _e('Please appoint an option.', 'usces'); ?></p>
<dl class="item_option">
<?php while (usces_have_options()) : ?>
<dt><?php usces_the_itemOptName(); ?></dt>
<dd><?php wcad_the_itemOption(usces_getItemOptName(),''); ?></dd>
<?php endwhile; ?>
</dl>
<?php endif; ?>
<p class="into_cart"><span><?php _e('Quantity', 'usces'); ?></span><?php wcad_the_itemQuant(); ?><span><?php usces_the_itemSkuUnit(); ?></span><?php wcad_the_itemSkuButton(__('Apply for a regular purchase', 'autodelivery'), 0); ?></p>
<div class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></div>
</div>
<?php echo apply_filters('wcad_item_single_sku_after_field_smart', NULL); ?>
<?php do_action('wcad_action_single_item_inform_smart'); ?>
</form>

	</div>
</div>
<?php
			endif;

		elseif( usces_sku_num() > 1 ) : 

			if( usces_have_zaiko_anyone( $post->ID ) ) : 
				usces_the_item();
?>
<div id="wc_regular">
	<p class="wcr_tlt"><?php _e('Regular Purchase', 'autodelivery') ?></p>
	<div class="inside">

<p class="regular_status"><span><?php echo apply_filters( 'wcad_filter_item_single_label_interval', __('Interval', 'autodelivery') ); ?></span><em><?php echo $regular_interval; ?><?php echo $regular_unit_name; ?></em></p>
<?php if( 1 < (int)$regular_frequency ) : ?>
<p class="regular_status"><span><?php echo apply_filters( 'wcad_filter_item_single_label_frequency', __('Frequency', 'autodelivery') ); ?></span><em><?php echo $regular_frequency; ?><?php _e('times', 'autodelivery'); ?></em></p>
<?php else: ?>
<p class="regular_status"><span><?php echo apply_filters( 'wcad_filter_item_single_label_frequency_free', __('Frequency', 'autodelivery') ); ?></span><em><?php echo apply_filters( 'wcad_filter_item_single_value_frequency_free', __('Free cycle', 'autodelivery') ); ?></em></p>
<?php endif; ?>

<form action="<?php echo USCES_CART_URL; ?>" method="post">
<div id="accordion" class="skuform">
<div class="skumulti accordion" id="slider">
<?php while( usces_have_skus() ) : ?>
<h3 id="skutitle-<?php esc_attr_e($sku_index); ?>"><?php usces_the_itemSkuDisp(); ?> (<?php usces_the_itemSku(); ?>)</h3>
<div id="skuform-<?php esc_attr_e($sku_index); ?>">
<p class="zaiko_status"><span><?php _e('stock status', 'usces'); ?></span><em><?php usces_the_itemZaiko(); ?></em></p>
<p class="field">
<?php if( usces_the_itemCprice('return') > 0 ) : ?>
<span class="field_name"><?php _e('List price', 'usces'); ?><?php usces_guid_tax(); ?></span>
<span class="field_cprice"><?php usces_the_itemCpriceCr(); ?></span>
<?php endif; ?>
<span class="field_name"><?php _e('selling price', 'usces'); ?><?php usces_guid_tax(); ?></span>
<strong class="field_price"><?php wcad_the_itemPriceCr(); ?></strong>
</p>
<?php usces_the_itemGpExp(); ?>
<?php if (usces_is_options()) : ?>
<p class="opt_ex"><?php _e('Please appoint an option.', 'usces'); ?></p>
<dl class="item_option">
<?php while (usces_have_options()) : ?>
<dt class="optname"><?php usces_the_itemOptName(); ?></dt>
<dd class="optlist"><?php wcad_the_itemOption(usces_getItemOptName(),''); ?></dd>
<?php endwhile; ?>
</dl>
<?php endif; ?>
<?php if( !usces_have_zaiko() ) : ?>
<p class="zaiko_status_cart"><?php echo apply_filters('usces_filters_single_sku_zaiko_message', __('Sold Out', 'usces')); ?></p>
<?php else : ?>
<p class="into_cart"><span></span><?php wcad_the_itemQuant(); ?><span><?php usces_the_itemSkuUnit(); ?></span><?php wcad_the_itemSkuButton(__('Apply for a regular purchase', 'autodelivery'), 0); ?></p>
<p class="error_message"><?php usces_singleitem_error_message($post->ID, usces_the_itemSku('return')); ?></p>
<?php endif; ?>
</div>
<?php endwhile; ?>
</div>
</div>
<?php echo apply_filters('single_item_multi_sku_after_field', NULL); ?>
<?php do_action( 'wcad_action_single_item_inform_smart' ); ?>
</form>

	</div>
</div>
<?php
			endif;
		endif;
	endif;

	$html = ob_get_contents();
	ob_end_clean();

	$html = apply_filters( 'wcad_filter_item_single_regular_smart', $html );

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

function wcad_get_skurprice( $post_id = '', $sku = '' ) {
	global $usces;

	$rprice = 0;
	if( !empty($post_id) and !empty($sku) ) {
		$skus = $usces->get_skus( $post_id, 'code' );
		$advance = ( isset($skus[$sku]['advance']) ) ? $skus[$sku]['advance'] : '';
	} else {
		$advance = ( isset($usces->itemsku['advance']) ) ? $usces->itemsku['advance'] : '';
	}
	if( !empty($advance) ) {
		$advance = maybe_unserialize( $advance );
		if( isset($advance['rprice']) ) $rprice = (int)$advance['rprice'];
	}
	return $rprice;
}

function wcad_the_itemPriceCr( $out = '' ) {
	global $usces;

	$rprice = wcad_get_skurprice();
	$price = ( 0 < $rprice ) ? $rprice : $usces->itemsku['price'];
	$html = esc_html($usces->get_currency( $price, true, false ));
	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

function wcad_the_itemOption( $name, $label = '#default#', $out = '' ) {
	global $post, $usces;
	$post_id = $post->ID;

	if( $label == '#default#' ) 
		$label = $name;

	$opts = usces_get_opts( $post_id, 'name' );
	if( !$opts ) 
		return false;

	$opt = $opts[$name];
	$means = (int)$opt['means'];
	$essential = (int)$opt['essential'];

	$html = '';
	$sku = esc_attr(urlencode($usces->itemsku['code']));
	$optcode = esc_attr(urlencode($name));
	$name = esc_attr($name);
	$label = esc_attr($label);
	$session_value = isset( $_SESSION['usces_singleitem']['itemOption'][$post_id][$sku][$optcode] ) ? $_SESSION['usces_singleitem']['itemOption'][$post_id][$sku][$optcode] : NULL;
	$html .= "\n<label for='itemOption_regular[{$post_id}][{$sku}][{$optcode}]' class='iopt_label'>{$label}</label>\n";
	switch( $means ) {
	case 0://Single-select
	case 1://Multi-select
		$selects = explode("\n", $opt['value']);
		$multiple = ($means === 0) ? '' : ' multiple';
		$multiple_array = ($means == 0) ? '' : '[]';
		$html .= "\n<select name='itemOption[{$post_id}][{$sku}][{$optcode}]{$multiple_array}' id='itemOption_regular[{$post_id}][{$sku}][{$optcode}]' class='iopt_select'{$multiple} onKeyDown=\"if (event.keyCode == 13) {return false;}\">\n";
		if( $essential == 1 ) {
			$selected = ( '#NONE#' == $session_value || NULL == $session_value ) ? ' selected="selected"' : '';
			$html .= "\t<option value='#NONE#'{$selected}>".__('Choose','usces')."</option>\n";
		}
		$i = 0;
		foreach( $selects as $v ) {
			$selected = ( ($i == 0 && $essential == 0 && NULL == $session_value) || esc_attr($v) == $session_value ) ? ' selected="selected"' : '';
			$html .= "\t<option value='".esc_attr($v)."'{$selected}>".esc_html($v)."</option>\n";
			$i++;
		}
		$html .= "</select>\n";
		break;
	case 2://Text
		$html .= "\n<input name='itemOption[{$post_id}][{$sku}][{$optcode}]' type='text' id='itemOption_regular[{$post_id}][{$sku}][{$optcode}]' class='iopt_text' onKeyDown=\"if (event.keyCode == 13) {return false;}\" value=\"".esc_attr($session_value)."\" />\n";
		break;
	case 5://Text-area
		$html .= "\n<textarea name='itemOption[{$post_id}][{$sku}][{$optcode}]' id='itemOption_regular[{$post_id}][{$sku}][{$optcode}]' class='iopt_textarea'>".esc_attr($session_value)."</textarea>\n";
		break;
	}

	$html = apply_filters( 'wcad_filter_the_itemOption', $html, $opts, $name, $label, $post_id, $usces->itemsku['code'] );

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

function wcad_the_itemQuant( $out = '' ) {
	global $usces, $post;
	$post_id = $post->ID;
	$sku = esc_attr(urlencode($usces->itemsku['code']));
	$value = isset( $_SESSION['usces_singleitem']['quant'][$post_id][$sku] ) ? $_SESSION['usces_singleitem']['quant'][$post_id][$sku] : 1;
	$quant = "<input name=\"quant[{$post_id}][".$sku."]\" type=\"text\" id=\"quant_regular[{$post_id}][".$sku."]\" class=\"skuquantity\" value=\"".$value."\" onKeyDown=\"if (event.keyCode == 13) {return false;}\" />";
	$html = apply_filters( 'wcad_filter_the_itemQuant', $quant, $post );

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

function wcad_the_itemSkuButton( $value, $type = 0, $out = '', $js = true ) {
	global $usces, $post;
	$item = $usces->item;
	if( empty($item) ) {
		$item->ID = 0;
		$item->post_mime_type = '';
	}
	$post_id = $post->ID;
	$zaikonum = $usces->itemsku['stocknum'];
	$zaiko_status = $usces->itemsku['stock'];
	$gptekiyo = $usces->itemsku['gp'];
	$rprice = wcad_get_skurprice();
	$skuPrice = ( 0 < $rprice ) ? $rprice : $usces->getItemPrice($post_id, $usces->itemsku['code']);
	$value = esc_attr(apply_filters( 'wcad_filter_incart_button_label', $value ));
	$sku = esc_attr(urlencode($usces->itemsku['code']));
	$type = ( $type == 1 ) ? 'button' : 'submit';

	$html  = "<input name=\"zaikonum[{$post_id}][{$sku}]\" type=\"hidden\" id=\"zaikonum_regular[{$post_id}][{$sku}]\" value=\"{$zaikonum}\" />\n";
	$html .= "<input name=\"zaiko[{$post_id}][{$sku}]\" type=\"hidden\" id=\"zaiko_regular[{$post_id}][{$sku}]\" value=\"{$zaiko_status}\" />\n";
	$html .= "<input name=\"gptekiyo[{$post_id}][{$sku}]\" type=\"hidden\" id=\"gptekiyo_regular[{$post_id}][{$sku}]\" value=\"{$gptekiyo}\" />\n";
	$html .= "<input name=\"skuPrice[{$post_id}][{$sku}]\" type=\"hidden\" id=\"skuPrice_regular[{$post_id}][{$sku}]\" value=\"{$skuPrice}\" />\n";
	if( $usces->use_js ) {
		$html .= "<input name=\"inCart[{$post_id}][{$sku}]\" type=\"{$type}\" id=\"inCart_regular[{$post_id}][{$sku}]\" class=\"skubutton\" value=\"{$value}\" onclick=\"return auto_delivery.intoCart_regular('{$post_id}','{$sku}')\" />";
	} else {
		$html .= "<a name=\"cart_button\"></a><input name=\"inCart[{$post_id}][{$sku}]\" type=\"{$type}\" id=\"inCart_regular[{$post_id}][{$sku}]\" class=\"skubutton\" value=\"{$value}\" />";
	}
	$html .= "<input name=\"usces_referer\" type=\"hidden\" value=\"".$_SERVER['REQUEST_URI']."\" />\n";

	$unit = get_post_meta( $post_id, '_wcad_regular_unit', true );
	$interval = get_post_meta( $post_id, '_wcad_regular_interval', true );
	$frequency = get_post_meta( $post_id, '_wcad_regular_frequency', true );
	$html_advance = '
	<input name="advance['.$post_id.']['.$sku.'][regular][unit]" id="advance_regular['.$post_id.']['.$sku.'][regular][unit]" type="hidden" value="'.$unit.'" />
	<input name="advance['.$post_id.']['.$sku.'][regular][interval]" id="advance_regular['.$post_id.']['.$sku.'][regular][interval]" type="hidden" value="'.$interval.'" />
	<input name="advance['.$post_id.']['.$sku.'][regular][frequency]" id="advance_regular['.$post_id.']['.$sku.'][regular][frequency]" type="hidden" value="'.$frequency.'" />
	';
	$html .= apply_filters( 'wcad_filter_the_itemSkuButton_advance', $html_advance, $post_id, $sku, $unit, $interval, $frequency );

	if( $usces->use_js && (is_page(USCES_CART_NUMBER) || $usces->is_cart_page($_SERVER['REQUEST_URI']) || (is_singular() && 'item' == $item->post_mime_type)) ) {
		$html_js = '
		<script type=\'text/javascript\'>
		(function($) {
			auto_delivery = {
				intoCart_regular : function( post_id, sku ) {
					var zaikonum = document.getElementById("zaikonum_regular["+post_id+"]["+sku+"]").value;
					var zaiko = document.getElementById("zaiko_regular["+post_id+"]["+sku+"]").value;
					if( (zaiko != \'0\' && zaiko != \'1\') ||  parseInt(zaikonum) == 0 ) {
						alert("'.__('temporaly out of stock now', 'usces').'");
						return false;
					}
					var mes = \'\';
					if( document.getElementById("quant_regular["+post_id+"]["+sku+"]") ) {
						var quant = document.getElementById("quant_regular["+post_id+"]["+sku+"]").value;
						if( quant == \'0\' || quant == \'\' || !(uscesCart.isNum(quant)) ) {
							mes += "'.__('enter the correct amount', 'usces').'\n";
						}
						var checknum = \'\';
						var checkmode = \'\';
						if( parseInt(uscesL10n.itemRestriction) <= parseInt(zaikonum) && uscesL10n.itemRestriction != \'\' && uscesL10n.itemRestriction != \'0\' && zaikonum != \'\' ) {
							checknum = uscesL10n.itemRestriction;
							checkmode = \'rest\';
						} else if( parseInt(uscesL10n.itemRestriction) > parseInt(zaikonum) && uscesL10n.itemRestriction != \'\' && uscesL10n.itemRestriction != \'0\' && zaikonum != \'\' ) {
							checknum = zaikonum;
							checkmode = \'zaiko\';
						} else if( (uscesL10n.itemRestriction == \'\' || uscesL10n.itemRestriction == \'0\') && zaikonum != \'\' ) {
							checknum = zaikonum;
							checkmode = \'zaiko\';
						} else if( uscesL10n.itemRestriction != \'\' && uscesL10n.itemRestriction != \'0\' && zaikonum == \'\' ) {
							checknum = uscesL10n.itemRestriction;
							checkmode = \'rest\';
						}
						if( parseInt(quant) > parseInt(checknum) && checknum != \'\' ) {
							if( checkmode == \'rest\' ) {
								mes += '.__("'This article is limited by '+checknum+' at a time.'", 'usces').'+"\n";
							} else {
								mes += '.__("'Stock is remainder '+checknum+'.'", 'usces').'+"\n";
							}
						}
					}
					for( i = 0; i<uscesL10n.key_opts.length; i++ ) {
						var skuob = document.getElementById("itemOption_regular["+post_id+"]["+sku+"]["+uscesL10n.key_opts[i]+"]");
						if( uscesL10n.opt_esse[i] == \'1\' ) {
							if( uscesL10n.opt_means[i] < 2 && skuob.value == \'#NONE#\' ) {
								mes += uscesL10n.mes_opts[i]+"\n";
							} else if ( uscesL10n.opt_means[i] >= 2 && skuob.value == \'\' ) {
								mes += uscesL10n.mes_opts[i]+"\n";
							}
						}
					}
		';
		$html_js .= apply_filters( 'wcad_filter_js_check', '', $post_id, $sku, $unit, $interval, $frequency );
		$html_js .= '
					if( mes != \'\' ) {
						alert( mes );
						return false;
					}
				}
			};
		})(jQuery);
		</script>
		';
		$html .= apply_filters( 'wcad_filter_the_itemSkuButton_js', $html_js, $post_id, $sku, $unit, $interval, $frequency );
	}

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

function wcad_set_regular_str( $unit, $interval, $frequency, $br = true, $remain = -1, $times = -1 ) {
	$brstr = ( $br ) ? "<br />" : "";
	$str = '';
	if( 'day' == $unit ) {
		$unitstr = __('Daily', 'autodelivery');
	} elseif( 'month' == $unit ) {
		$unitstr = __('Monthly', 'autodelivery');
	} else {
		return $str;
	}
	$title = '['.__('Regular Purchase', 'autodelivery').']';
	if( $br ) $title = '<span class="regular_info_title">'.$title.'</span>';
	$str .= $title.$brstr."\r\n";
	$str .= apply_filters( 'wcad_filter_item_single_label_interval', __('Interval', 'autodelivery') ).' : '.$interval.$unitstr.$brstr."\r\n";
	if( 1 < $frequency ) {
		$str .= apply_filters( 'wcad_filter_item_single_label_frequency', __('Frequency', 'autodelivery') ).' : '.$frequency.__('times', 'autodelivery');
		if( 0 < $times ) $str .= '('.$times.__('th', 'autodelivery').')';
		$str .= $brstr."\r\n";
	} else {
		$str .= apply_filters( 'wcad_filter_item_single_label_frequency_free', '' ).apply_filters( 'wcad_filter_item_single_value_frequency_free', '' );
		if( 0 < $times ) $str .= $times.__('th', 'autodelivery').$brstr."\r\n";
	}
	if( 0 <= $remain ) $str .= apply_filters( 'wcad_filter_item_single_label_remaining', __('Remaining', 'autodelivery') ).' : '.$remain.__('times', 'autodelivery').$brstr."\r\n";
	if( $br ) $str = '<div class="regular_info">'.$str.'</div>';
	$str = apply_filters( 'wcad_filter_set_regular_str', $str, $unit, $interval, $frequency, $br, $remain, $times );
	return $str;
}

add_filter( 'usces_filter_states_form_js', 'wcad_filter_states_form_js' );
function wcad_filter_states_form_js( $js ) {
	global $usces;

	$js = '';
	if( $usces->use_js 
			&& (( (is_page(USCES_MEMBER_NUMBER) || $usces->is_member_page($_SERVER['REQUEST_URI'])) && ((true === $usces->is_member_logged_in() && '' == $usces->page) || 'member' == $usces->page || 'editmemberform' == $usces->page || 'newmemberform' == $usces->page) )
			|| ( (is_page(USCES_CART_NUMBER) || $usces->is_cart_page($_SERVER['REQUEST_URI'])) && ('customer' == $usces->page || 'delivery' == $usces->page) ) 
			)) {

		$js .= '
		<script type="text/javascript">
		(function($) {
		uscesForm = {
			settings: {
				url: uscesL10n.ajaxurl,
				type: "POST",
				cache: false,
				success: function(data, dataType){
					//$("tbody#item-opt-list").html( data );
				}, 
				error: function(msg){
					//$("#ajax-response").html(msg);
				}
			},

			changeStates : function( country, type ) {

				var s = this.settings;
				s.url = "'.USCES_SSL_URL.'/";
				s.data = "usces_ajax_action=change_states&country=" + country;
				s.success = function(data, dataType) {
					if( "error" == data ) {
						alert("error");
					} else {
						$("select#" + type + "_pref").html( data );
						if( customercountry == country && "customer" == type ) {
							$("#" + type + "_pref").attr({selectedIndex:customerstate});
						} else if( deliverycountry == country && "delivery" == type ) {
							$("#" + type + "_pref").attr({selectedIndex:deliverystate});
						} else if( customercountry == country && "member" == type ) {
							$("#" + type + "_pref").attr({selectedIndex:customerstate});
						}
					}
				};
				s.error = function(msg) {
					alert("error");
				};
				$.ajax( s );
				return false;
			}
		};';

		if( 'customer' == $usces->page ) {

			if( ! wcad_have_regular_order() ) {
				$js .= '
				var customerstate = $("#customer_pref").get(0).selectedIndex;
				var customercountry = $("#customer_country").val();
				var deliverystate = "";
				var deliverycountry = "";
				var memberstate = "";
				var membercountry = "";
				$("#customer_country").change(function() {
					var country = $("#customer_country option:selected").val();
					uscesForm.changeStates( country, "customer" );
				});';
			}

		} elseif( 'delivery' == $usces->page ) {

			$js .= '
			var customerstate = "";
			var customercountry = "";
			var deliverystate = $("#delivery_pref").get(0).selectedIndex;
			var deliverycountry = $("#delivery_country").val();
			var memberstate = "";
			var membercountry = "";
			$("#delivery_country").change(function() {
				var country = $("#delivery_country option:selected").val();
				uscesForm.changeStates( country, "delivery" );
			});';

		} elseif( (true === $usces->is_member_logged_in() && '' == $usces->page) || (true === $usces->is_member_logged_in() && 'member' == $usces->page) || 'editmemberform' == $usces->page || 'newmemberform' == $usces->page ) {
			if( $usces->is_member_logged_in() and ( isset($_REQUEST['page']) and 'autodelivery_history' == $_REQUEST['page'] ) ) {
			} else {
			$js .= '
			var customerstate = "";
			var customercountry = "";
			var deliverystate = "";
			var deliverycountry = "";
			var memberstate = $("#member_pref").get(0).selectedIndex;
			var membercountry = $("#member_country").val();
			$("#member_country").change(function() {
				var country = $("#member_country option:selected").val();
				uscesForm.changeStates( country, "member" );
			});';
			}
		}
		$js .= '
		})(jQuery);
		</script>';
	}
	return $js;
}

add_action( 'usces_action_admin_mailform', 'wcad_action_admin_mailform' );
add_filter( 'wcad_filter_order_confirm_mail_meisai', 'wcad_filter_order_mail_meisai', 10, 4 );
function wcad_auto_order_mail( $new_data ) {
	global $usces, $wpdb;
	$order_id = $new_data['order_id'];
	$tableName = $wpdb->prefix."usces_order";
	$query = $wpdb->prepare("SELECT * FROM $tableName WHERE ID = %d", $order_id);
	$data = $wpdb->get_row( $query, ARRAY_A );
	$deli = unserialize($data['order_delivery']);
	$cart = unserialize($data['order_cart']);
	$country = $usces->get_order_meta_value('country', $order_id);
	$customer = array(
		'mailaddress' => $data['order_email'],
		'name1' => $data['order_name1'],
		'name2' => $data['order_name2'],
		'name3' => $data['order_name3'],
		'name4' => $data['order_name4'],
		'zipcode' => $data['order_zip'],
		'country' => $usces_settings['country'][$country],
		'pref' => $data['order_pref'],
		'address1' => $data['order_address1'],
		'address2' => $data['order_address2'],
		'address3' => $data['order_address3'],
		'tel' => $data['order_tel'],
		'fax' => $data['order_fax']
	);
	$condition = unserialize($data['order_condition']);

	$total_full_price = $data['order_item_total_price'] - $data['order_usedpoint'] + $data['order_discount'] + $data['order_shipping_charge'] + $data['order_cod_fee'] + $data['order_tax'];

	$mail_data = $usces->options['mail_data'];
	$payment = $usces->getPayments( $data['order_payment_name'] );
	$entry = array( 'order' => array('payment_name' => $payment) );
	$res = false;

	$msg_body = "\r\n\r\n\r\n".__('【定期購入内容】','usces')."\r\n";
	$msg_body .= usces_mail_line( 1, $data['order_email'] );//********************
	$msg_body .= apply_filters('wcad_filter_order_confirm_mail_first', NULL, $data);
	$msg_body .= uesces_get_mail_addressform( 'admin_mail_customer', $customer, $order_id );
	$msg_body .= __('自動受注番号','usces')."\t: ".usces_get_deco_order_id( $order_id )."\r\n";

	$meisai = __('定期商品','usces')."\t\t: \r\n";
	foreach ( (array)$cart as $cart_row ) {
		$post_id = $cart_row['post_id'];
		$sku = urldecode($cart_row['sku']);
		$quantity = $cart_row['quantity'];
		$options = $cart_row['options'];
		$itemCode = $usces->getItemCode($post_id);
		$itemName = $usces->getItemName($post_id);
		$cartItemName = $usces->getCartItemName($post_id, $sku);
		$skuPrice = $cart_row['price'];
		if( empty($options) ) {
			$optstr = '';
			$options =  array();
		}

		$meisai .= usces_mail_line( 2, $data['order_email'] );//--------------------
		$meisai .= "$cartItemName \n";
		if( is_array($options) && count($options) > 0 ) {
			$optstr = '';
			foreach( $options as $key => $value ) {
				if( !empty($key) ) {
					$key = urldecode($key);
					if( is_array($value) ) {
						$c = '';
						$optstr .= $key."\t\t: ";
						foreach( $value as $v ) {
							$optstr .= $c.urldecode($v);
							$c = ', ';
						}
						$optstr .= "\r\n";
					} else {
						$optstr .= $key."\t\t: ".urldecode($value)."\r\n";
					}
				}
			}
			$meisai .= apply_filters( 'wcad_filter_option_adminmail', $optstr, $options, $cart_row );
		}
		$meisai .= __('Unit price','usces')."\t".usces_crform( $skuPrice, true, false, 'return' ).__(' * ','usces').$cart_row['quantity']."\r\n";
	}

	$meisai .= usces_mail_line( 3, $data['order_email'] );//====================
	$meisai .= __('total items','usces')."\t\t: ".usces_crform( $data['order_item_total_price'], true, false, 'return' )."\r\n";

	if( $data['order_usedpoint'] != 0 )
		$meisai .= __('use of points','usces')."\t\t: ".number_format($data['order_usedpoint']).__('Points','usces')."\r\n";
	if( $data['order_discount'] != 0 )
		$meisai .= apply_filters('usces_confirm_discount_label', __('Campaign disnount', 'usces'))."\t: ".usces_crform( $data['order_discount'], true, false, 'return' )."\r\n";
	$meisai .= __('Shipping','usces')."\t\t\t: ".usces_crform( $data['order_shipping_charge'], true, false, 'return' )."\r\n";
	if( $payment['settlement'] == 'COD' )
		$meisai .= apply_filters('usces_filter_cod_label', __('COD fee', 'usces'))."\t\t: ".usces_crform( $data['order_cod_fee'], true, false, 'return' )."\r\n";
	if( !empty($usces->options['tax_rate']) )
		$meisai .= __('consumption tax','usces')."\t\t: ".usces_crform( $data['order_tax'], true, false, 'return' )."\r\n";
	$meisai .= usces_mail_line( 2, $data['order_email'] );//--------------------
	$meisai .= __('Payment amount','usces')."\t: ".usces_crform( $total_full_price, true, false, 'return' )."\r\n";
	$meisai .= usces_mail_line( 2, $data['order_email'] );//--------------------
	$meisai .= "(".__('Currency', 'usces').': '.__(usces_crcode( 'return' ), 'usces').")\r\n\r\n";

	$msg_body .= apply_filters('wcad_filter_order_confirm_mail_meisai', $meisai, $data, $cart, $entry);


	$msg_shipping = __('** A shipping address **','usces')."\r\n";
	$msg_shipping .= usces_mail_line( 1, $data['order_email'] );//********************

	$msg_shipping .= uesces_get_mail_addressform( 'admin_mail', $deli, $order_id );

	if( $data['order_delidue_date'] == NULL || $data['order_delidue_date'] == '#none#' ) {
		$msg_shipping .= "\r\n";
	} else {
		$msg_shipping .= __('Shipping date', 'usces')."\t\t: ".$data['order_delidue_date']."\r\n";
		$msg_shipping .= __("* A shipment due date is a day to ship an article, and it's not the arrival day.", 'usces')."\r\n";
		$msg_shipping .= "\r\n";
	}
	$deli_meth = (int)$data['order_delivery_method'];
	if( 0 <= $deli_meth ) {
		$deli_index = $usces->get_delivery_method_index($deli_meth);
		$msg_shipping .= __('Delivery Method','usces')."\t\t: ".$usces->options['delivery_method'][$deli_index]['name']."\r\n";
	}
	$msg_shipping .= __('Delivery date','usces')."\t\t: ".$data['order_delivery_date']."\r\n";
	$msg_shipping .= __('Delivery Time','usces')."\t: ".$data['order_delivery_time']."\r\n";
	$msg_shipping .= "\r\n";
	$msg_body .= apply_filters('wcad_filter_order_confirm_mail_shipping', $msg_shipping, $data);


	$msg_payment = __('** Payment method **','usces')."\r\n";
	$msg_payment .= usces_mail_line( 1, $data['order_email'] );//********************
	$msg_payment .= $payment['name']. "\r\n\r\n";
	if( $payment['settlement'] == 'transferAdvance' || $payment['settlement'] == 'transferDeferred' ) {
		$transferee = __('Transfer','usces')."\t\t: \r\n";
		$transferee .= $usces->options['transferee']."\r\n";
		$msg_payment .= apply_filters('wcad_filter_mail_transferee', $transferee, $payment);
		$msg_payment .= "\r\n".usces_mail_line( 2, $data['order_email'] )."\r\n";//--------------------
	} elseif( $payment['settlement'] == 'acting_jpayment_conv' ) {
		$args = maybe_unserialize($usces->get_order_meta_value('settlement_args', $order_id));
		$msg_payment .= __('決済番号', 'usces')."\t\t: ".$args['gid']."\r\n";
		$msg_payment .= __('決済金額', 'usces')."\t\t: ".number_format($args['ta']).__('dollars','usces')."\r\n";
		$msg_payment .= __('お支払先', 'usces')."\t\t: ".usces_get_conv_name($args['cv'])."\r\n";
		$msg_payment .= __('コンビニ受付番号','usces')."\t\t: ".$args['no']."\r\n";
		if( $args['cv'] != '030' ) {//ファミリーマート以外
			$msg_payment .= __('コンビニ受付番号情報URL', 'usces')."\t\t: ".$args['cu']."\r\n";
		}
		$msg_payment .= "\r\n".usces_mail_line( 2, $data['order_email'] )."\r\n";//--------------------
	} elseif( $payment['settlement'] == 'acting_jpayment_bank' ) {
		$args = maybe_unserialize($usces->get_order_meta_value('settlement_args', $order_id));
		$msg_payment .= __('決済番号', 'usces')."\t\t: ".$args['gid']."\r\n";
		$msg_payment .= __('決済金額', 'usces')."\t\t: ".number_format($args['ta']).__('dollars','usces')."\r\n";
		$bank = explode('.', $args['bank']);
		$msg_payment .= __('銀行コード','usces')."\t\t: ".$bank[0]."\r\n";
		$msg_payment .= __('銀行名','usces')."\t\t: ".$bank[1]."\r\n";
		$msg_payment .= __('支店コード','usces')."\t\t: ".$bank[2]."\r\n";
		$msg_payment .= __('支店名','usces')."\t\t: ".$bank[3]."\r\n";
		$msg_payment .= __('口座種別','usces')."\t\t: ".$bank[4]."\r\n";
		$msg_payment .= __('口座番号','usces')."\t\t: ".$bank[5]."\r\n";
		$msg_payment .= __('口座名義','usces')."\t\t: ".$bank[6]."\r\n";
		$msg_payment .= __('支払期限','usces')."\t\t: ".substr($args['exp'], 0, 4).'年'.substr($args['exp'], 4, 2).'月'.substr($args['exp'], 6, 2)."日\r\n";
		$msg_payment .= "\r\n".usces_mail_line( 2, $data['order_email'] )."\r\n";//--------------------
	}

	$msg_body .= apply_filters('wcad_filter_order_confirm_mail_payment', $msg_payment, $order_id, $payment, $cart, $data);
	$msg_body .= usces_mail_custom_field_info( 'order', '', $order_id, $data['order_email'] );

	$msg_body .= "\r\n";
	$msg_body .= __('** Others / a demand **','usces')."\r\n";
	$msg_body .= usces_mail_line( 1, $data['order_email'] );//********************
	$msg_body .= $data['order_note']."\r\n\r\n";

	$msg_body .= apply_filters('wcad_filter_order_confirm_mail_body', NULL, $data);

	//usces_log('wcad_auto_order_mail : '.print_r($msg_body,true), 'wcad_test.log');

	$subject = apply_filters( 'wcad_filter_send_order_mail_subject_thankyou', $mail_data['title']['thankyou'], $data );
	$message = do_shortcode( $mail_data['header']['auto_delivery']).$msg_body.do_shortcode($mail_data['footer']['auto_delivery'] );
	$confirm_para = array(
		'to_name' => sprintf(__('Mr/Mrs %s', 'usces'), (usces_localized_name( $customer["name1"], $customer["name2"], 'return' ))),
		'to_address' => $customer['mailaddress'], 
		'from_name' => get_option('blogname'),
		'from_address' => $usces->options['sender_mail'],
		'return_path' => $usces->options['sender_mail'],
		'subject' => $subject,
		'message' => $message
	);
	$confirm_para = apply_filters( 'wcad_send_ordermail_para_to_customer', $confirm_para, $data );

	usces_send_mail( $confirm_para );

	$options = get_option('usces');
	$mail_datas = $options['mail_data'];

	$admin_mailtitle = "【自動受注報告】";
	$admin_mailheader = "自動受注が登録されました。\n\n\n\n";
	$admin_mailfooter = $mail_datas['footer']['auto_delivery'];
	$subject = apply_filters( 'wcad_filter_send_order_mail_subject_order', $admin_mailtitle, $data );
	$message = apply_filters( 'wcad_filter_send_order_mail_message_head', $admin_mailheader, $data ).$msg_body.apply_filters( 'wcad_filter_send_order_mail_message_foot', $admin_mailfooter, $data )."\n";

	$order_para = array(
		'to_name' => __('An order email','usces'),
		'to_address' => $usces->options['order_mail'], 
		'from_name' => sprintf(__('Mr/Mrs %s', 'usces'), (usces_localized_name( $customer["name1"], $customer["name2"], 'return' ))),
		'from_address' => $customer['mailaddress'],
		'return_path' => $usces->options['error_mail'],
		'subject' => $subject,
		'message' => $message
	);

	$order_para = apply_filters( 'wcad_send_ordermail_para_to_manager', $order_para, $data );
	$res = usces_send_mail( $order_para );

	return $res;
}

function wcad_action_admin_mailform() {
	global $usces;
	$usces->options = get_option('usces');
	$mail_datas = $usces->options['mail_data'];
	if( empty($mail_datas['title']['auto_delivery']) ) {
		$mail_datas['title']['auto_delivery'] = "【定期購入による自動注文のご案内】";
	}
	if( empty($mail_datas['header']['auto_delivery']) ) {
		$mail_datas['header']['auto_delivery'] = "このメールは、定期購入の商品発送予定をお知らせするものです。\n";
		$mail_datas['header']['auto_delivery'] .= "\n";
		$mail_datas['header']['auto_delivery'] .= "お届け回数は、各商品の明細ごとに記載されています。\n\n\n";
	}
	if( empty($mail_datas['footer']['auto_delivery']) ) {
		$mail_datas['footer']['auto_delivery'] = $mail_datas['footer']['othermail'];
	}
?>
		<div class="postbox">
		<h3 class="hndle"><span>定期購入による自動注文のご案内メール（自動送信）</span><a style="cursor:pointer;" onclick="toggleVisibility('ex_auto_delivery');"> (<?php _e('explanation', 'usces'); ?>) </a></h3>
		<div class="inside">
		<table class="form_table">
			<tr>
				<th width="150"><?php _e('Title', 'usces'); ?></th>
				<td><input name="title[auto_delivery]" id="title[auto_delivery]" type="text" class="mail_title" value="<?php esc_attr_e($mail_datas['title']['auto_delivery']); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php _e('header', 'usces'); ?></th>
				<td><textarea name="header[auto_delivery]" id="header[auto_delivery]" class="mail_header"><?php esc_attr_e($mail_datas['header']['auto_delivery']); ?></textarea></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th><?php _e('footer', 'usces'); ?></th>
				<td><textarea name="footer[auto_delivery]" id="footer[auto_delivery]" class="mail_footer"><?php esc_attr_e($mail_datas['footer']['auto_delivery']); ?></textarea></td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<hr size="1" color="#CCCCCC" />
		<div id="ex_auto_delivery" class="explanation">定期購入による自動注文が登録された際に、自動送信される発送予定のご案内メール。</div>
		</div>
		</div><!--postbox-->
<?php
}

function wcad_autodelivery_history( $out = '' ) {
	global $usces, $usces_settings;

	$applyform = usces_get_apply_addressform( $usces->options['system']['addressform'] );
	$usces_members = $usces->get_member();
	$history = wcad_get_member_history( $usces_members['ID'] );
	ob_start();

	if( !count($history) ):
?>
<table>
	<tr>
		<td><?php _e('There is no purchase history for this moment.', 'usces'); ?></td>
	</tr>
</table>
<?php
	else:
		foreach( (array)$history as $regular_order ):
			$delivery = (array)unserialize( $regular_order['reg_delivery'] );
			switch( $applyform ) {
			case 'JP':
				$name = $delivery['name1'].$delivery['name2'];
				break;
			case 'US':
			default:
				$name = $delivery['name2'].$delivery['name1'];
			}
?>
<div class="inside">
<table>
	<tr>
		<th class="historyrow"><?php _e('Regular ID', 'autodelivery'); ?></th>
		<th class="historyrow"><?php _e('order number', 'usces'); ?></th>
		<th class="historyrow"><?php _e('Order date', 'autodelivery'); ?></th>
		<th class="historyrow"><?php _e('payment method', 'usces'); ?></th>
		<th class="historyrow"><?php _e('shipping address', 'usces'); ?></th>
	</tr>
	<tr>
		<td><?php echo $regular_order['reg_id']; ?></td>
		<td><?php echo usces_get_deco_order_id($regular_order['reg_order_id']); ?></td>
		<td class="date"><?php echo $regular_order['reg_date']; ?></td>
		<td><?php echo $regular_order['reg_payment_name']; ?></td>
		<td><?php echo sprintf(__('Mr/Mrs %s', 'usces'), $name).'<br />'.$delivery['pref'].$delivery['address1'].$delivery['address2']; ?></td>
	</tr>
</table>
<table>
<?php
			$regular_detail = (array)unserialize( $regular_order['reg_detail'] );
			$idx = 0;
			foreach( (array)$regular_detail as $detail ):
				$post_id = urldecode($detail['regdet_post_id']);
				$sku_code = urldecode($detail['regdet_sku']);
				$options = unserialize($detail['regdet_options']);
				$itemCode = $usces->getItemCode($post_id);
				$itemName = $usces->getItemName($post_id);
				$cartItemName = $usces->getCartItemName($post_id, $sku_code);
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
				$cart_thumbnail = '<a href="'.get_permalink($post_id).'">'.wp_get_attachment_image( $pictid, array(60, 60), true ).'</a>';

				$price = $detail['regdet_price'] * $detail['regdet_quantity'];

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
	<tbody>
	<tr>
		<td rowspan="4"><?php echo ($idx+1); ?></td>
		<td rowspan="4">
			<div class="item_img"><?php echo $cart_thumbnail; ?></div>
			<div class="item_content">
			<p class="itemname"><?php esc_html_e($cartItemName); ?><br /><?php echo $optstr; ?></p>
			</div>
		</td>
		<th><?php _e('Unit price','usces'); ?></th><td><?php usces_crform( $detail['regdet_price'], true, false ); ?></td>
		<th><?php _e('Interval', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_interval ); ?><?php esc_attr_e( $regular_unit ); ?></td>
	</tr>
	<tr>
		<th><?php _e('Quantity','usces'); ?></th><td><?php esc_attr_e( $detail['regdet_quantity'] ); ?></td>
		<th><?php _e('Frequency', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_frequency ); ?></td>
	</tr>
	<tr>
		<th><?php _e('Amount','usces'); ?></th><td><?php usces_crform( $price, true, false ); ?></td>
		<th><?php _e('Remaining', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_remain ); ?></td>
	</tr>
	<tr>
		<th><?php _e('The next scheduled delivery date', 'autodelivery'); ?></th><td><?php esc_attr_e( $detail['regdet_schedule_delivery_date'] ); ?></td>
		<th><?php _e('Condition', 'autodelivery'); ?></th><td><div<?php echo ' class="'.$detail['regdet_condition'].'"'; ?>><?php _e($detail['regdet_condition'], 'autodelivery'); ?></div></td>
	</tr>
	</tbody>
<?php
				$idx++;
			endforeach;
?>
</table>
</div>
<?php
		endforeach;
	endif;

	$html = ob_get_contents();
	ob_end_clean();

	$html = apply_filters( 'wcad_filter_autodelivery_history', $html );

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

function wcad_autodelivery_history_garak( $out = '' ) {
	global $usces, $usces_settings;

	$applyform = usces_get_apply_addressform( $usces->options['system']['addressform'] );
	$usces_members = $usces->get_member();
	$history = wcad_get_member_history( $usces_members['ID'] );
	ob_start();

	if( !count($history) ):
?>
<table>
	<tr>
		<td><?php _e('There is no purchase history for this moment.', 'usces'); ?></td>
	</tr>
</table>
<?php
	else:
		foreach( (array)$history as $regular_order ):
			$delivery = (array)unserialize( $regular_order['reg_delivery'] );
			switch( $applyform ) {
			case 'JP':
				$name = $delivery['name1'].$delivery['name2'];
				break;
			case 'US':
			default:
				$name = $delivery['name2'].$delivery['name1'];
			}
?>
<div class="inside">
<table>
	<tr><th class="historyrow"><?php _e('Regular ID', 'autodelivery'); ?></th><td><?php echo $regular_order['reg_id']; ?></td></tr>
	<tr><th class="historyrow"><?php _e('order number', 'usces'); ?></th><td><?php echo usces_get_deco_order_id($regular_order['reg_order_id']); ?></td></tr>
	<tr><th class="historyrow"><?php _e('Order date', 'autodelivery'); ?></th><td class="date"><?php echo $regular_order['reg_date']; ?></td></tr>
	<tr><th class="historyrow"><?php _e('payment method', 'usces'); ?></th><td><?php echo $regular_order['reg_payment_name']; ?></td></tr>
	<tr><th class="historyrow"><?php _e('shipping address', 'usces'); ?></th><td><?php echo sprintf(__('Mr/Mrs %s', 'usces'), $name).'<br />'.$delivery['pref'].$delivery['address1'].$delivery['address2']; ?></td></tr>
</table>
<?php
			$regular_detail = (array)unserialize( $regular_order['reg_detail'] );
			$idx = 0;
			foreach( (array)$regular_detail as $detail ):
				$post_id = urldecode($detail['regdet_post_id']);
				$sku_code = urldecode($detail['regdet_sku']);
				$options = unserialize($detail['regdet_options']);
				$itemCode = $usces->getItemCode($post_id);
				$itemName = $usces->getItemName($post_id);
				$cartItemName = $usces->getCartItemName($post_id, $sku_code);
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
				$cart_thumbnail = '<a href="'.get_permalink($post_id).'">'.wp_get_attachment_image( $pictid, array(60, 60), true ).'</a>';

				$price = $detail['regdet_price'] * $detail['regdet_quantity'];

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
<table>
	<tr><th scope="row" class="num"><?php echo ($idx+1); ?></th>
		<td>
			<div class="item_img"><?php echo $cart_thumbnail; ?></div>
			<div class="item_content">
			<p class="itemname"><?php esc_html_e($cartItemName); ?><br /><?php echo $optstr; ?></p>
			</div>
		</td>
	</tr>
</table>
<table>
	<tbody>
	<tr><th><?php _e('Unit price','usces'); ?></th><td><?php usces_crform( $detail['regdet_price'], true, false ); ?></td></tr>
	<tr><th><?php _e('Quantity','usces'); ?></th><td><?php esc_attr_e( $detail['regdet_quantity'] ); ?></td></tr>
	<tr><th><?php _e('Amount','usces'); ?></th><td><?php usces_crform( $price, true, false ); ?></td></tr>
	<tr><th><?php _e('The next scheduled delivery date', 'autodelivery'); ?></th><td><?php esc_attr_e( $detail['regdet_schedule_delivery_date'] ); ?></td></tr>
	<tr><th><?php _e('Interval', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_interval ); ?><?php esc_attr_e( $regular_unit ); ?></td></tr>
	<tr><th><?php _e('Frequency', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_frequency ); ?></td></tr>
	<tr><th><?php _e('Remaining', 'autodelivery'); ?></th><td><?php esc_attr_e( $regular_remain ); ?></td></tr>
	<tr><th><?php _e('Condition', 'autodelivery'); ?></th><td><div<?php echo ' class="'.$detail['regdet_condition'].'"'; ?>><?php _e($detail['regdet_condition'], 'autodelivery'); ?></div></td></tr>
	</tbody>
</table>
<?php
				$idx++;
			endforeach;
?>
</div>
<?php
		endforeach;
	endif;

	$html = ob_get_contents();
	ob_end_clean();

	$html = apply_filters( 'wcad_filter_autodelivery_history_garak', $html );

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

function wcad_get_admin_addressform( $type, $values, $out = '' ) {
	global $usces, $usces_settings;
	$options = get_option( 'usces' );
	$applyform = usces_get_apply_addressform( $options['system']['addressform'] );
	$html = '';

	switch( $applyform ) {
	case 'JP':
		$html .= '
		<tr>
			<th class="label">'.__('name', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[name1]" type="text" class="text short" value="'.esc_attr( $values['name1'] ).'" /><input name="'.$type.'[name2]" type="text" class="text short" value="'.esc_attr( $values['name2'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('furigana', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[name3]" type="text" class="text short" value="'.esc_attr( $values['name3'] ).'" /><input name="'.$type.'[name4]" type="text" class="text short" value="'.esc_attr( $values['name4'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Zip/Postal Code', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[zipcode]" type="text" class="text short" value="'.esc_attr( $values['zipcode'] ).'" />'.apply_filters( 'wcad_filter_admin_addressform_zipcode', NULL, $type ).'</td>
		</tr>
		<tr>
			<th class="label">'.__('Country', 'usces').'</th>
			<td class="col1">'.uesces_get_target_market_form( $type, $values['country'] ).'</td>
		</tr>
		<tr>
			<th class="label">'.__('Province', 'usces').'</th>
			<td class="col1">'.usces_pref_select( $type, $values ).'</td>
		</tr>
		<tr>
			<th class="label">'.__('city', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address1]" type="text" class="text long" value="'.esc_attr( $values['address1'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('numbers', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address2]" type="text" class="text long" value="'.esc_attr( $values['address2'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('building name', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address3]" type="text" class="text long" value="'.esc_attr( $values['address3'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Phone number', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[tel]" type="text" class="text long" value="'.esc_attr( $values['tel'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('FAX number', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[fax]" type="text" class="text long" value="'.esc_attr( $values['fax'] ).'" /></td>
		</tr>';
		break;

	case 'CN':
		$html .= '
		<tr>
			<th class="label">'.__('name', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[name1]" type="text" class="text short" value="'.esc_attr( $values['name1'] ).'" /><input name="'.$type.'[name2]" type="text" class="text short" value="'.esc_attr( $values['name2'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Country', 'usces').'</th>
			<td class="col1">'.uesces_get_target_market_form( $type, $values['country'] ).'</td>
		</tr>
		<tr>
			<th class="label">'.__('State', 'usces').'</th>
			<td class="col1">'.usces_pref_select( $type, $values ).'</td>
		</tr>
		<tr>
			<th class="label">'.__('city', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address1]" type="text" class="text long" value="'.esc_attr( $values['address1'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Address Line1', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address2]" type="text" class="text long" value="'.esc_attr( $values['address2'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Address Line2', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address3]" type="text" class="text long" value="'.esc_attr( $values['address3'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Zip', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[zipcode]" type="text" class="text short" value="'.esc_attr( $values['zipcode'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Phone number', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[tel]" type="text" class="text long" value="'.esc_attr( $values['tel'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('FAX number', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[fax]" type="text" class="text long" value="'.esc_attr( $values['fax'] ).'" /></td>
		</tr>';
		break;

	case 'US':
	default:
		$html .= '
		<tr>
			<th class="label">'.__('name', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[name2]" type="text" class="text short" value="'.esc_attr( $values['name2'] ).'" /><input name="'.$type.'[name1]" type="text" class="text short" value="'.esc_attr( $values['name1'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Address Line1', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address2]" type="text" class="text long" value="'.esc_attr( $values['address2'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Address Line2', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address3]" type="text" class="text long" value="'.esc_attr( $values['address3'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('city', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[address1]" type="text" class="text long" value="'.esc_attr( $values['address1'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('State', 'usces').'</th>
			<td class="col1">'.usces_pref_select( $type, $values ).'</td>
		</tr>
		<tr>
			<th class="label">'.__('Country', 'usces').'</th>
			<td class="col1">'.uesces_get_target_market_form( $type, $values['country'] ).'</td>
		</tr>
		<tr>
			<th class="label">'.__('Zip', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[zipcode]" type="text" class="text short" value="'.esc_attr( $values['zipcode'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('Phone number', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[tel]" type="text" class="text long" value="'.esc_attr( $values['tel'] ).'" /></td>
		</tr>
		<tr>
			<th class="label">'.__('FAX number', 'usces').'</th>
			<td class="col1"><input name="'.$type.'[fax]" type="text" class="text long" value="'.esc_attr( $values['fax'] ).'" /></td>
		</tr>';
		break;
	}

	if( $out == 'return' ) {
		return $html;
	} else {
		echo $html;
	}
}

?>
