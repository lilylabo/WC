<?php
$status = $usces->action_status;
$message = $usces->action_message;
$usces->action_status = 'none';
$usces->action_message = '';

$wcad_options = get_option('wcad_options');
$scheduled_time['hour'] = ( isset($wcad_options['scheduled_time']['hour']) ) ? $wcad_options['scheduled_time']['hour'] : '02';
$scheduled_time['min'] = ( isset($wcad_options['scheduled_time']['min']) ) ? $wcad_options['scheduled_time']['min'] : '00';
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

	$("#aAdditionalURLs").click(function () {
		$("#AdditionalURLs").toggle();
	});
});

function toggleVisibility( id ) {
	var e = document.getElementById(id);
	if( e.style.display == 'block' )
		e.style.display = 'none';
	else
		e.style.display = 'block';
}
</script>
<div class="wrap">
<div class="usces_admin">
<h2>WCEX <?php _e('Auto Delivery Setting','autodelivery'); ?></h2>
<div class="varsion_num">v<?php echo WCEX_AUTO_DELIVERY_VERSION; ?></div>
<div id="aniboxStatus" class="<?php echo $status; ?>">
	<div id="anibox" class="clearfix">
		<img src="<?php echo USCES_PLUGIN_URL; ?>/images/list_message_<?php echo $status; ?>.gif" />
		<div class="mes" id="info_massage"><?php echo $message; ?></div>
	</div>
</div>
<form action="" method="post" name="option_form" id="option_form">
<input name="auto_delivery_option_update" type="submit" class="button" value="<?php _e('change decision','usces'); ?>" />
<div id="poststuff" class="metabox-holder">

<div class="postbox">
<h3 class="hndle"><span><?php _e('Auto Delivery Setting','autodelivery'); ?></span></h3>
<div class="inside">
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_auto_delivery_acting_payment');"><?php _e('Use of credit card payment','autodelivery'); ?></a></th>
		<td><input name="acting_payment" type="radio" id="acting_payment_1" value="off"<?php if( $wcad_options['acting_payment'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="acting_payment_1"><?php _e('Do not use a credit card payment (cash only)','autodelivery'); ?></label></td>
		<td><input name="acting_payment" type="radio" id="acting_payment_2" value="on"<?php if( $wcad_options['acting_payment'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="acting_payment_2"><?php _e('Use credit card payment','autodelivery'); ?></label></td>
		<td><div id="ex_auto_delivery_acting_payment" class="explanation"><?php _e('If you use a credit card payment, you will be prompted to log in membership at the time of purchase is not limited to the payment method.<br />You can buy if you want to buy only products (usually purchase products other than regularly) is even if you are not logged in.<br />Credit card payment in the case of regular purchase, you need a settlement (batch processing) Zeus.','autodelivery'); ?></div></td>
	</tr>
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_auto_delivery_date_calculation');"><?php _e('Calculate delivery date','autodelivery'); ?></a></th>
		<td><input name="date_calculation" type="radio" id="date_calculation_1" value="off"<?php if( $wcad_options['date_calculation'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="date_calculation_1"><?php _e('Does not calculate','autodelivery'); ?></label></td>
		<td><input name="date_calculation" type="radio" id="date_calculation_2" value="on"<?php if( $wcad_options['date_calculation'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="date_calculation_2"><?php _e('Calculate','autodelivery'); ?></label></td>
		<td><div id="ex_auto_delivery_date_calculation" class="explanation"><?php _e('Automatically calculate the dates of delivery.<br />(When it includes a regular purchase, always calculate.)','autodelivery'); ?></div></td>
	</tr>
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_auto_delivery_campaign');"><?php _e('Campaign','autodelivery'); ?></a></th>
		<td><input name="campaign" type="radio" id="campaign_1" value="off"<?php if( $wcad_options['campaign'] == 'off' ) echo ' checked="checked"'; ?> /></td><td><label for="campaign_1"><?php _e('Does not apply','autodelivery'); ?></label></td>
		<td><input name="campaign" type="radio" id="campaign_2" value="on"<?php if( $wcad_options['campaign'] == 'on' ) echo ' checked="checked"'; ?> /></td><td><label for="campaign_2"><?php _e('Apply','autodelivery'); ?></label></td>
		<td><div id="ex_auto_delivery_campaign" class="explanation"><?php _e('You whether or not to apply the automatic order campaign.','autodelivery'); ?></div></td>
	</tr>
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_auto_delivery_scheduled_time');"><?php _e('Time of automatic orders','autodelivery'); ?></a></th>
		<td colspan="4">
			<select name="scheduled_time[hour]">
<?php
			for( $i = 0; $i < 24; $i++ ):
				$hour = sprintf( '%02d', $i );
?>
				<option value="<?php echo $hour; ?>"<?php if( $scheduled_time['hour'] == $hour ) echo ' selected'; ?>><?php echo $hour; ?></option>
<?php
			endfor;
?>
			</select>:&nbsp;<select name="scheduled_time[min]">
<?php
			$i = 0;
			while( $i < 60 ):
				$min = sprintf( '%02d', $i );
?>
				<option value="<?php echo $min; ?>"<?php if( $scheduled_time['min'] == $min ) echo ' selected'; ?>><?php echo $min; ?></option>
<?php
				$i += 10;
			endwhile;
?>
			</select>
		</td>
		<td><div id="ex_auto_delivery_scheduled_time" class="explanation"><?php _e('It is time to perform the automatic orders. Mail order is sent to the buyer.','autodelivery'); ?></div></td>
	</tr>
</table>
</div>
</div><!--postbox-->

</div><!--poststuff-->

<input name="auto_delivery_option_update" type="submit" class="button" value="<?php _e('change decision','usces'); ?>" />
<input type="hidden" name="post_ID" value="<?php echo USCES_CART_NUMBER; ?>" />
<input type="hidden" name="auto_delivery_transition" value="auto_delivery_option_update" />
<input type="hidden" name="scheduled_time_before[hour]" value="<?php echo $scheduled_time['hour']; ?>" />
<input type="hidden" name="scheduled_time_before[min]" value="<?php echo $scheduled_time['min']; ?>" />
</form>
</div><!--usces_admin-->
</div><!--wrap-->
