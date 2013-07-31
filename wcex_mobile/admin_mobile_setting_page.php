<?php
$status = $usces->action_status;
$message = $usces->action_message;
$usces->action_status = 'none';
$usces->action_message = '';
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
<?php if(version_compare( USCES_VERSION, '1.3.3.1307071', '>=' )){ ?>
	if( $("#smart_pc_theme").attr("checked") ){
		$("#smart_theme_switch").attr("disabled", true);
	}
	$("#smart_pc_theme").change(function () {
		if( $("#smart_pc_theme").attr("checked") ){
			$("#smart_theme_switch").attr("disabled", true);
		}else{
			$("#smart_theme_switch").attr("disabled", false);
		}
	});
<?php } ?>
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
<h2>WCEX <?php _e('Mobile Setting','mobile'); ?></h2>
<div id="aniboxStatus" class="<?php echo $status; ?>">
	<div id="anibox" class="clearfix">
		<img src="<?php echo USCES_PLUGIN_URL; ?>/images/list_message_<?php echo $status; ?>.gif" />
		<div class="mes" id="info_massage"><?php echo $message; ?></div>
	</div>
</div>

<form action="" method="post" name="option_form" id="option_form">
	<input name="update_wcmb_options" type="submit" class="button" value="設定を更新" />

<div id="poststuff" class="metabox-holder">
<div class="postbox">
<h3 class="hndle"><span><?php _e('ガラケー表示設定','usces'); ?></span></h3>
<div class="inside">
	<table class="form_table">
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_garak_template');"><?php _e('テンプレート', 'usces'); ?></a></th>
			<td><input name="garak_template" type="text" class="long" value="<?php echo $wcmb_options['garak_template']; ?>" /></td>
			<td colspan="4"><div id="ex_garak_template" class="explanation"><?php _e('使用するガラケーテーマのディレクトリ名。初期値は mobile_garak_default 。', 'usces'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_garak_telop');"><?php _e('ヘッダー・テロップ', 'usces'); ?></a></th>
			<td><input name="garak_telop" type="text" class="long_str" value="<?php echo $wcmb_options['garak_telop']; ?>" /></td>
			<td colspan="4"><div id="ex_garak_telop" class="explanation"><?php _e('ヘッダーの最上部に表示するテロップ。表示しない場合は空白。', 'usces'); ?></div></td>
		</tr>
		<tr height="20">
			<th rowspan="3"><a style="cursor:pointer;" onclick="toggleVisibility('ex_garak_logo');"><?php _e('ヘッダー・ロゴ', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="garak_logo" id="garak_logo_allimage" type="radio" value="allimage"<?php echo ('allimage' == $wcmb_options['garak_logo'] ? ' checked="checked"' : ''); ?> /><label for="garak_logo_allimage">常に画像を表示</label></td>
			<td rowspan="3" colspan="3"><div id="ex_garak_logo" class="explanation"><?php _e('ヘッダーロゴの表示方法を選択します。', 'usces'); ?></div></td>
		</tr>
		<tr height="20">
			<td nowrap="nowrap"><input name="garak_logo" type="radio" id="garak_logo_topimage" value="topimage" <?php echo ('topimage' == $wcmb_options['garak_logo'] ? ' checked="checked"' : ''); ?> /><label for="garak_logo_topimage">トップは画像、その他はサイト名を表示</label></td>
		</tr>
		<tr height="20">
			<td nowrap="nowrap"><input name="garak_logo" id="garak_logo_allname" type="radio" value="allname"<?php echo ('allname' == $wcmb_options['garak_logo'] ? ' checked="checked"' : ''); ?> /><label for="garak_logo_allname">常にサイト名を表示</label></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_garak_logo_url');"><?php _e('ロゴ画像のURL', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="garak_logo_uri" id="garak_logo_image_url"  type="text" class="long_str" value="<?php echo $wcmb_options['garak_logo_uri']; ?>" /></td>
			<td colspan="4"><div id="ex_garak_logo_url" class="explanation"><?php _e('初期値はガラケー用テーマ内の/images/image_top.jpg ', 'usces'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_garak_ssl');"><?php _e('SSL を無効にする', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="garak_ssl" id="garak_ssl" type="checkbox" value="1"<?php echo (1 == $wcmb_options['garak_ssl'] ? ' checked="checked"' : ''); ?> /></td>
			<td colspan="4"><div id="ex_garak_ssl" class="explanation"><?php _e('ガラケーは共有SSL が使えません。必要に応じて無効にしてください。ただし無効にした場合は通常のアクセスとなります。', 'usces'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_garak_description');"><?php _e('キャッチフレーズを表示する', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="garak_description" id="garak_description" type="checkbox" value="1"<?php echo ((isset($wcmb_options['garak_description']) && 1 == $wcmb_options['garak_description']) ? ' checked="checked"' : ''); ?> /></td>
			<td colspan="4"><div id="ex_garak_description" class="explanation"><?php _e('一般設定のキャッチフレーズを表示します。表示しない場合はチェックを外します。', 'usces'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_garak_referer_check');"><?php _e('リファラーをチェックする', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="garak_referer_check" id="garak_referer_check" type="checkbox" value="1"<?php echo ((isset($wcmb_options['garak_referer_check']) && 1 == $wcmb_options['garak_referer_check']) ? ' checked="checked"' : ''); ?> /></td>
			<td colspan="4"><div id="ex_garak_referer_check" class="explanation"><?php _e('リファラーによるチェックを行い、リファラーを持たない機種については非対応とします。チェックしない場合はチェックを外します。', 'usces'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_garak_rejection');"><?php _e('ガラケーのアクセスを拒否する', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="garak_rejection" id="garak_rejection" type="checkbox" value="1"<?php echo ((isset($wcmb_options['garak_rejection']) && 1 == $wcmb_options['garak_rejection']) ? ' checked="checked"' : ''); ?> /></td>
			<td colspan="4"><div id="ex_garak_rejection" class="explanation"><?php _e('ガラケーを非対応にし、PC及びスマホのみをアクセス可能とします。', 'usces'); ?></div></td>
		</tr>
	</table>
</div>
</div><!--postbox-->
</div><!--poststuff-->

<div id="poststuff" class="metabox-holder">
<div class="postbox">
<h3 class="hndle"><span><?php _e('スマートフォン表示設定','usces'); ?></span></h3>
<div class="inside">
	<table class="form_table">
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_smart_template');"><?php _e('テンプレート', 'usces'); ?></a></th>
			<td><input name="smart_template" type="text" class="long" value="<?php echo $wcmb_options['smart_template']; ?>" /></td>
			<td colspan="4"><div id="ex_smart_template" class="explanation"><?php _e('使用するスマートフォン用テーマのディレクトリ名。初期値は mobile_smart_default 。', 'usces'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_smart_ssl');"><?php _e('SSL を無効にする', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="smart_ssl" id="smart_ssl" type="checkbox" value="1"<?php echo (1 == $wcmb_options['smart_ssl'] ? ' checked="checked"' : ''); ?> /></td>
			<td colspan="4"><div id="ex_smart_ssl" class="explanation"><?php _e('必要に応じて無効にしてください。サーバーがSSLに対応していなければ有効にはなりません。', 'usces'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_smart_remote_address');"><?php _e('リモートアドレスチェックを行う', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="smart_remote_address" id="smart_remote_address" type="checkbox" value="1"<?php echo (1 == $wcmb_options['smart_remote_address'] ? ' checked="checked"' : ''); ?> /></td>
			<td colspan="4"><div id="ex_smart_remote_address" class="explanation"><?php _e('3G通信を行うauのスマホに対応するには、チェックを外す必要がありますが、セキュリティーが低下します。', 'usces'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_smart_pc_theme');"><?php _e('PC用テンプレートのみを使用する', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="smart_pc_theme" id="smart_pc_theme" type="checkbox" value="1"<?php echo (1 == $wcmb_options['smart_pc_theme'] ? ' checked="checked"' : ''); ?> /></td>
			<td colspan="4"><div id="ex_smart_pc_theme" class="explanation"><?php _e('スマホ専用テーマを使用せず、PC用のテーマを表示させます。', 'usces'); ?></div></td>
		</tr>
		<?php if(version_compare( USCES_VERSION, '1.3.3.1307071', '>=' )): ?>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_smart_theme_switch');"><?php _e('PC・スマホテンプレート切り替えボタンを表示する', 'usces'); ?></a></th>
			<td nowrap="nowrap"><input name="smart_theme_switch" id="smart_theme_switch" type="checkbox" value="1"<?php echo (1 == $wcmb_options['smart_theme_switch'] ? ' checked="checked"' : ''); ?> /></td>
			<td colspan="4"><div id="ex_smart_theme_switch" class="explanation"><?php _e('ページ最下部に、スマホ専用テーマとPC用のテーマの切り替えスイッチを表示させます。', 'usces'); ?></div></td>
		</tr>
		<?php endif; ?>
	</table>
</div>
</div><!--postbox-->
</div><!--poststuff-->

	<input name="update_wcmb_options" type="submit" class="button" value="設定を更新" />
	<input name="wcmb_action" type="hidden" value="action1" />
</form>
<p></p>
<p>PC、ガラケー、スマートフォンそれぞれ独自のカスタムメニューが登録できます。＞「外観」->「メニュー」</p>
<p>ガラケー及びスマートフォン用のテーマは有効化しません。常にPC用のテーマを有効化しておいてください。</p>
</div><!--usces_admin-->
</div><!--wrap-->