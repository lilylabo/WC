<?php
$status = $usces->action_status;
$message = $usces->action_message;
$usces->action_status = 'none';
$usces->action_message = '';


$dlseller_options = get_option('dlseller');
$dlseller_options = maybe_unserialize($dlseller_options);
if( !isset($dlseller_options['content_path']) || $dlseller_options['content_path'] == '' ){
	$dlseller_content_path = USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/';
}else{
	$dlseller_content_path = $dlseller_options['content_path'];
}
$dlseller_terms = isset($dlseller_options['dlseller_terms']) ? $dlseller_options['dlseller_terms'] : '';
$dlseller_terms2 = isset($dlseller_options['dlseller_terms2']) ? $dlseller_options['dlseller_terms2'] : '';
if( isset($dlseller_options['dlseller_rate']) ){
	$dlseller_rate = $dlseller_options['dlseller_rate'];
}else{
	$dlseller_rate = 5000;
	$dlseller_options['dlseller_rate'] = 1000;
}
$dlseller_member_reinforcement = isset($dlseller_options['dlseller_member_reinforcement']) ? $dlseller_options['dlseller_member_reinforcement'] : 'off';
$dlseller_restricting = isset($dlseller_options['dlseller_restricting']) ? $dlseller_options['dlseller_restricting'] : 'on';
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

	$("#aAdditionalURLs").click(function () {
		$("#AdditionalURLs").toggle();
	});
});

function toggleVisibility(id) {
   var e = document.getElementById(id);
   if(e.style.display == 'block')
	  e.style.display = 'none';
   else
	  e.style.display = 'block';
}
</script>
<div class="wrap">
<div class="usces_admin">
<h2>WCEX <?php _e('DLSeller Setting','dlseller'); ?></h2>
<div class="varsion_num">v<?php echo WCEX_DLSELLER_VERSION; ?></div>
<div id="aniboxStatus" class="<?php echo $status; ?>">
	<div id="anibox" class="clearfix">
		<img src="<?php echo USCES_PLUGIN_URL; ?>/images/list_message_<?php echo $status; ?>.gif" />
		<div class="mes" id="info_massage"><?php echo $message; ?></div>
	</div>
</div>
<form action="" method="post" name="option_form" id="option_form">
<input name="dlseller_option_update" type="submit" class="button" value="<?php _e('change decision','usces'); ?>" />
<div id="poststuff" class="metabox-holder">

<div class="postbox">
<h3 class="hndle"><span><?php _e('Setting','dlseller'); ?></span></h3>
<div class="inside">
<!--
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_restricting');">購入者制限</a></th>
		<td><input name="dlseller_restricting" type="radio" id="dlseller_restricting_1" value="on"<?php if( $dlseller_restricting == 'on' ) echo ' checked="checked"' ?> /></td><td><label for="dlseller_restricting_1">メンバーのみが購入できる</label></td>
		<td><input name="dlseller_restricting" type="radio" id="dlseller_restricting_2" value="off"<?php if( $dlseller_restricting == 'off' ) echo ' checked="checked"' ?> /></td><td><label for="dlseller_restricting_2">誰でも購入できる</label></td>
		<td><div id="ex_dlseller_restricting" class="explanation"><?php _e('「メンバーのみが購入できる」を選択すると、カートへ投入の際ログインを強要されます。<br />初期値は「メンバーのみが購入できる」です。', 'usces'); ?></div></td>
	</tr>
</table>
-->
<table class="form_table">
	<tr height="40">
		<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_member_reinforcement');">会員情報チェックの強化</a></th>
		<td><input name="dlseller_member_reinforcement" type="radio" id="dlseller_member_reinforcement_1" value="on"<?php if( $dlseller_member_reinforcement == 'on' ) echo ' checked="checked"' ?> /></td><td><label for="dlseller_member_reinforcement_1">強化する</label></td>
		<td><input name="dlseller_member_reinforcement" type="radio" id="dlseller_member_reinforcement_2" value="off"<?php if( $dlseller_member_reinforcement == 'off' ) echo ' checked="checked"' ?> /></td><td><label for="dlseller_member_reinforcement_2">強化しない</label></td>
		<td><div id="ex_dlseller_member_reinforcement" class="explanation"><?php _e('強化すると、住所や電話番号が必須項目となります。<br />決済で「分割払い」や「自動継続課金」を行う場合は「強化する」を選択してください。', 'usces'); ?></div></td>
	</tr>
</table>
<table class="form_table">
	<tr height="50">
	    <th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_content_path');"><?php _e('Contents directory path', 'dlseller'); ?></a></th>
		<td><input name="dlseller_content_path" type="text" id="dlseller_content_path" value="<?php esc_html_e($dlseller_content_path); ?>" size="80" /></td>
	    <td><div id="ex_dlseller_content_path" class="explanation"><?php _e('Please appoint the full path of the directory which contents file is in.', 'dlseller'); ?></div></td>
	</tr>
</table>
<!--<table class="form_table">
	<tr height="50">
	    <th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_results');"><?php _e('実績CSV', 'dlseller'); ?></a></th>
		<td><a href="<?php echo get_option('siteurl') . '/wp-admin/admin.php?page=wcex_dlseller&dlseller_transition=results'; ?>"><?php _e('Download', 'dlseller'); ?></a></td>
	    <td><div id="ex_dlseller_results" class="explanation"><?php _e('ダウンロード及び購入実績数をCSV形式でダウンロードします。', 'dlseller'); ?></div></td>
	</tr>
</table>-->
<table class="form_table">
	<tr height="50">
	    <th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_terms');"><?php _e('Terms of Use', 'dlseller'); ?></a></th>
		<td><textarea name="dlseller_terms" cols="90" rows="10"><?php esc_html_e($dlseller_terms); ?></textarea></td>
	    <td><div id="ex_dlseller_terms" class="explanation"><?php _e('Terms of Use', 'dlseller'); ?></div></td>
	</tr>
</table>
<table class="form_table">
	<tr height="50">
	    <th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_terms2');">継続課金用利用規約</a></th>
		<td><textarea name="dlseller_terms2" cols="90" rows="10"><?php esc_html_e($dlseller_terms2); ?></textarea></td>
	    <td><div id="ex_dlseller_terms2" class="explanation">自動継続課金用の利用規約</div></td>
	</tr>
</table>
<table class="form_table">
	<tr height="50">
	    <th><a style="cursor:pointer;" onclick="toggleVisibility('ex_dlseller_rate');"><?php _e('転送レート', 'dlseller'); ?></a></th>
		<td><input name="dlseller_rate" type="text" id="dlseller_rate" value="<?php esc_html_e($dlseller_rate); ?>" size="30" /></td>
	    <td><div id="ex_dlseller_rate" class="explanation"><?php _e('初期値は1000です。エラーが出る場合は500程に下げてください。', 'dlseller'); ?></div></td>
	</tr>
</table>
</div>
</div><!--postbox-->


</div><!--poststuff-->



<input name="dlseller_option_update" type="submit" class="button" value="<?php _e('change decision','usces'); ?>" />
<input type="hidden" name="post_ID" value="<?php echo USCES_CART_NUMBER ?>" />
<input type="hidden" name="dlseller_transition" value="dlseller_option_update" />
</form>
</div><!--usces_admin-->
</div><!--wrap-->