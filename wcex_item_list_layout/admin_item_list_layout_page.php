<?php
$status = $usces->action_status;
$message = $usces->action_message;
$usces->action_status = 'none';
$usces->action_message = '';

$ill_options = get_option('item_list_layout');
$ill_sort = get_option('item_list_layout_sort');

$opt_str = '';
foreach($ill_options as $k => $v){
	$opt_str .= "'".$k."'" . ',';
}
$opt_str = rtrim($opt_str, ',');
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
	
	wcex_ill_opts = new Array(<?php echo $opt_str; ?>);

		var $tabs = $('#illtabs').tabs({
			cookie: {
				// store cookie for a day, without, it would be a session cookie
				expires: 1
			},
			show: function(event, ui) {
				wcexILL.style(wcex_ill_opts[ui.index]);
			}

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
<h2>WCEX <?php _e('Item List Layout','ill'); ?></h2>
<div class="varsion_num">v <?php echo WCEX_ITEM_LIST_LAYOUT_VERSION; ?></div>
<div id="aniboxStatus" class="<?php echo $status; ?>">
	<div id="anibox" class="clearfix">
		<img src="<?php echo USCES_PLUGIN_URL; ?>/images/list_message_<?php echo $status; ?>.gif" />
		<div class="mes" id="info_massage"><?php echo $message; ?></div>
	</div>
</div>

<div id="poststuff" class="metabox-holder">
<div class="postbox">
<h3 class="hndle"><span><?php _e('Setting','usces'); ?></span></h3>
<div class="inside">
<form action="" method="post" name="option_form" id="option_form">
<input name="add_layout" type="submit" class="button" value="<?php _e('Added Layout','ill'); ?>" />

<div id="illtabs">

	<ul>
<?php
	foreach($ill_options as $id => $value):
		$layout_name = ill_get_layout_name( $value['category'] );
?>
		<li><a href="#illtabs_<?php echo $id; ?>"><?php echo $layout_name; ?>_<?php echo $id; ?></a></li>
<?php
	endforeach;
?>
	</ul>

<?php
	foreach($ill_options as $id => $value):
		$layout_name = ill_get_layout_name( $value['category'] );
?>
	<div id="illtabs_<?php echo $id; ?>">
		<table class="ill_category_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_category_<?php echo $id; ?>');"><?php _e('Category', 'ill'); ?></a></th>
					<td><select name="category_<?php echo $id; ?>"> 
							<option value="other"<?php if( 'other' == $value['category'] ) echo ' selected="selected"'; ?>><?php _e('Other Categories', 'ill'); ?></option> 
							<!--<option value="<?php echo USCES_ITEM_CAT_PARENT_ID; ?>"<?php if( USCES_ITEM_CAT_PARENT_ID == $value['category'] ) echo ' selected="selected"'; ?>><?php _e('All Items', 'ill'); ?></option>--> 
							<?php 
							$categories=  get_categories('hide_empty=0&child_of=' . USCES_ITEM_CAT_PARENT_ID); 
							foreach ($categories as $cat) {
							?>
							<option value="<?php echo $cat->term_id; ?>"<?php if( $value['category'] == $cat->term_id ) echo ' selected="selected"'; ?>><?php echo esc_html($cat->cat_name); ?>(<?php echo $cat->category_count; ?>)</option>
							<?php
							}
							?>
							<option value="search"<?php if( 'search' == $value['category'] ) echo ' selected="selected"'; ?>><?php _e('Result of Multiple Item Search', 'ill'); ?></option> 
						</select>
					</td>
				<td colspan="4"><div id="ex_category_<?php echo $id; ?>" class="explanation"><?php _e('Set the Categories to Use. If not Specified, Choose &quot;Other Categories&quot;', 'ill'); ?></div></td>
			</tr>
		</table>
		<div class="ill_table_block clearfix">
		<div class="ill_tables">
		<table class="ill_table">
			<tr height="50">
				<th class="ill_style"></th>
				<td><input name="style_<?php echo $id; ?>" type="radio" id="style1_<?php echo $id; ?>" value="showcase"<?php if( $value['style'] == 'showcase' ) echo ' checked="checked"' ?> onclick="wcexILL.style(<?php echo $id; ?>);" /></td><td><label for="style1_<?php echo $id; ?>">グリッド型</label></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_width_<?php echo $id; ?>');"><?php _e('Total Column Width', 'ill'); ?></a></th>
				<td><input name="width_<?php echo $id; ?>" type="text" id="width_<?php echo $id; ?>" value="<?php echo $value['width']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_width_<?php echo $id; ?>" class="explanation"><?php _e('Choose total layout width. Initially set at 533.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_colum_<?php echo $id; ?>');"><?php _e('Number of Thumbnails', 'ill'); ?></a></th>
				<td><input name="colum_<?php echo $id; ?>" type="text" id="colum_<?php echo $id; ?>" value="<?php echo $value['colum']; ?>" size="6" /></td>
				<td colspan="4"><div id="ex_colum_<?php echo $id; ?>" class="explanation"><?php _e('Choose the number of thumbnails for showcase display. Initially set at 3.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_limargin_<?php echo $id; ?>');"><?php _e('Margin Width of Frames', 'ill'); ?></a></th>
				<td><input name="limargin_<?php echo $id; ?>" type="text" id="limargin_<?php echo $id; ?>" value="<?php echo $value['limargin']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_limargin_<?php echo $id; ?>" class="explanation"><?php _e('Choose the margin between frames. Initially set at 10.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_lipadding_<?php echo $id; ?>');"><?php _e('Inside Margin of Frames', 'ill'); ?></a></th>
				<td><input name="lipadding_<?php echo $id; ?>" type="text" id="lipadding_<?php echo $id; ?>" value="<?php echo $value['lipadding']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_lipadding_<?php echo $id; ?>" class="explanation"><?php _e('Choose inside margin (padding) of frames. Initially set at 5.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_liborder_<?php echo $id; ?>');"><?php _e('Border Width', 'ill'); ?></a></th>
				<td><input name="liborder_<?php echo $id; ?>" type="text" id="liborder_<?php echo $id; ?>" value="<?php echo $value['liborder']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_liborder_<?php echo $id; ?>" class="explanation"><?php _e('Choose the width of border. When the value of <br /> is 2 and above,  you need to fix item_list_layout.css. Border will disappear when set at <br />0. Initially set at 1.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_liheight_<?php echo $id; ?>');"><?php _e('Height of Frames', 'ill'); ?></a></th>
				<td><input name="liheight_<?php echo $id; ?>" type="text" id="liheight_<?php echo $id; ?>" value="<?php echo $value['liheight']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_liheight_<?php echo $id; ?>" class="explanation"><?php _e('Choose the height of frames. Initially set at 240.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_txtheight_<?php echo $id; ?>');"><?php _e('Height of Text Area', 'ill'); ?></a></th>
				<td><input name="txtheight_<?php echo $id; ?>" type="text" id="txtheight_<?php echo $id; ?>" value="<?php echo $value['txtheight']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_txtheight_<?php echo $id; ?>" class="explanation"><?php _e('Choose the height of text area. Initially set at 50.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_posts_per_page_<?php echo $id; ?>');"><?php _e('Number of Items per Page', 'ill'); ?></a></th>
				<td><input name="posts_per_page_<?php echo $id; ?>" type="text" id="posts_per_page_<?php echo $id; ?>" value="<?php echo $value['posts_per_page']; ?>" size="6" /></td>
				<td><div id="ex_posts_per_page_<?php echo $id; ?>" class="explanation"><?php _e('Choose the number of items per page. Initially set at 12.', 'ill'); ?></div></td>
			</tr>
		</table>
		</div>
		<div class="ill_tables">
		<table class="ill_table">
			<tr height="50">
				<th class="ill_style"></th>
				<td><input name="style_<?php echo $id; ?>" id="style2_<?php echo $id; ?>" type="radio" value="list"<?php if( $value['style'] == 'list' ) echo ' checked="checked"' ?> onclick="wcexILL.style(<?php echo $id; ?>);" /></td><td><label for="style2_<?php echo $id; ?>">リスト型</label></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_width_l_<?php echo $id; ?>');"><?php _e('Total Column Width', 'ill'); ?></a></th>
				<td><input name="width_l_<?php echo $id; ?>" type="text" id="width_l_<?php echo $id; ?>" value="<?php echo $value['width_l']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_width_l_<?php echo $id; ?>" class="explanation"><?php _e('Choose total layout width. Initially set at 533.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_limargin_l_<?php echo $id; ?>');"><?php _e('Space between Lines', 'ill'); ?></a></th>
				<td><input name="limargin_l_<?php echo $id; ?>" type="text" id="limargin_l_<?php echo $id; ?>" value="<?php echo $value['limargin_l']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_limargin_l_<?php echo $id; ?>" class="explanation"><?php _e('Choose the space between lines. Initially set at 10.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_lipadding_l_<?php echo $id; ?>');"><?php _e('Inside Margin of Lines', 'ill'); ?></a></th>
				<td><input name="lipadding_l_<?php echo $id; ?>" type="text" id="lipadding_l_<?php echo $id; ?>" value="<?php echo $value['lipadding_l']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_lipadding_l_<?php echo $id; ?>" class="explanation"><?php _e('Choose the inside margin of lines (padding). Initially set at 5.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_imgwidth_l_<?php echo $id; ?>');"><?php _e('Photo Width', 'ill'); ?></a></th>
				<td><input name="imgwidth_l_<?php echo $id; ?>" type="text" id="imgwidth_l_<?php echo $id; ?>" value="<?php echo $value['imgwidth_l']; ?>" size="6" />px</td>
				<td colspan="4"><div id="ex_imgwidth_l_<?php echo $id; ?>" class="explanation"><?php _e('Choose the thumbnail width. The height will change proportionally. Initially set at 150.', 'ill'); ?></div></td>
			</tr>
		</table>
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_posts_per_page_l_<?php echo $id; ?>');"><?php _e('Number of Items per Page', 'ill'); ?></a></th>
				<td><input name="posts_per_page_l_<?php echo $id; ?>" type="text" id="posts_per_page_l_<?php echo $id; ?>" value="<?php echo $value['posts_per_page_l']; ?>" size="6" /></td>
				<td><div id="ex_posts_per_page_l_<?php echo $id; ?>" class="explanation"><?php _e('Choose the number of items per page. Initially set at 12.', 'ill'); ?></div></td>
			</tr>
		</table>
		</div>
		</div>
		<div class="ill_table_block">
		<table class="ill_table">
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_illheader_<?php echo $id; ?>');"><?php _e('Header', 'ill'); ?></a></th>
				<td><textarea name="illheader_<?php echo $id; ?>" id="illheader_<?php echo $id; ?>" cols="80" rows="5"><?php echo isset($value['illheader']) ? $value['illheader'] : ''; ?></textarea></td>
				<td colspan="4"><div id="ex_illheader_<?php echo $id; ?>" class="explanation"><?php _e('You can choose displayed header of the layout with html.', 'ill'); ?></div></td>
			</tr>
			<tr height="50">
				<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_illfooter_<?php echo $id; ?>');"><?php _e('Footer', 'ill'); ?></a></th>
				<td><textarea name="illfooter_<?php echo $id; ?>" id="illfooter_<?php echo $id; ?>" cols="80" rows="5"><?php echo isset($value['illfooter']) ? $value['illfooter'] : ''; ?></textarea></td>
				<td colspan="4"><div id="ex_illfooter_<?php echo $id; ?>" class="explanation"><?php _e('You can choose displayed footer of the layout with html.', 'ill'); ?></div></td>
			</tr>
		</table>
		</div>
	<input name="update_layout[<?php echo $id; ?>]" type="submit" class="button" value="<?php echo sprintf(__('Update %s', 'ill'), $layout_name . '_' . $id); ?>" />
<?php
		if( $id > 0 )
			echo '<input name="del_layout[' . $id . ']" type="submit" class="button" style="color:#F00;" value="' . sprintf(__('Delete %s', 'ill'), $layout_name . '_' . $id) . '" onclick="return wcexILL.del(' . $id . ');" />';
?>
	</div>
<?php
	endforeach;
?>


</div>
	<input name="ill_action" type="hidden" value="edit_layout" />
</form>
</div><!--postbox-->
</div><!--poststuff-->


<div id="poststuff" class="metabox-holder">
<div class="postbox">
<h3 class="hndle"><span><?php _e('Sort Category','ill'); ?></span></h3>
<div class="inside">
<form action="" method="post" name="option_form" id="option_form">
	<table class="form_table">
		<tr height="70">
			<th><?php _e('Display Sort Category', 'ill'); ?></th>
			<td><input name="sort[new]" type="checkbox" id="sort_new" value="1"<?php if( $ill_sort['new'] == '1' ) echo ' checked="checked"' ?> /></td><td width="80"><label for="sort_new"><?php _e('New to Old', 'ill'); ?></label></td>
			<td><input name="sort[cheap]" type="checkbox" id="sort_cheap" value="1"<?php if( $ill_sort['cheap'] == '1' ) echo ' checked="checked"' ?> /></td><td width="80"><label for="sort_cheap"><?php _e('Price: Low to High', 'ill'); ?></label></td>
			<td><input name="sort[high]" type="checkbox" id="sort_high" value="1"<?php if( $ill_sort['high'] == '1' ) echo ' checked="checked"' ?> /></td><td width="80"><label for="sort_high"><?php _e('Price: High to Low', 'ill'); ?></label></td>
			<td><input name="sort[popular]" type="checkbox" id="sort_popular" value="1"<?php if( $ill_sort['popular'] == '1' ) echo ' checked="checked"' ?> /></td><td width="80"><label for="sort_popular"><?php _e('Best Selling to Least', 'ill'); ?></label></td>
			<td><input name="sort[name]" type="checkbox" id="sort_name" value="1"<?php if( $ill_sort['name'] == '1' ) echo ' checked="checked"' ?> /></td><td width="80"><label for="sort_name"><?php _e('By Item Name', 'ill'); ?></label></td>
			<td><input name="sort[code]" type="checkbox" id="sort_code" value="1"<?php if( $ill_sort['code'] == '1' ) echo ' checked="checked"' ?> /></td><td width="80"><label for="sort_code"><?php _e('By Item Codee', 'ill'); ?></label></td>
		</tr>
	</table>
	<input name="submit" type="submit" class="button" value="<?php _e('Update Sort Category','ill'); ?>" />
	<input name="ill_action" type="hidden" value="update_sort" />
</form>
<p><?php _e('Choose categories to display in sorted menu. Menu will not show unless chosen.','ill'); ?></p>
</div>
</div><!--postbox-->
</div><!--poststuff-->

<div id="poststuff" class="metabox-holder">
<div class="postbox">
<h3 class="hndle"><span><?php _e('Update Custom Field','ill'); ?></span></h3>
<div class="inside">
<form action="" method="post" name="option_form" id="option_form">
	<table class="form_table">
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_price');"><?php _e('Item Price Information', 'ill'); ?></a></th>
			<td><input name="ill_field_price" type="submit" class="button" value="<?php _e('Update Price Field','ill'); ?>" /></td>
			<td colspan="4"><div id="ex_price" class="explanation"><?php _e('Prepare custom fields when sorted by price. When changing item price, you can change custom field (usces price) at the time, or update all by this update button.', 'ill'); ?></div></td>
		</tr>
		<tr height="50">
			<th><a style="cursor:pointer;" onclick="toggleVisibility('ex_popular');"><?php _e('Popularity Information', 'ill'); ?></a></th>
			<td><input name="ill_field_popular" type="submit" class="button" value="<?php _e('Update Popularity Field','ill'); ?>" /></td>
			<td colspan="4"><div id="ex_popular" class="explanation"><?php _e('Prepare custom fields when sorted by popularity. Please update by pressing renew button, as the sales figures are not reflected on time. <br /> custom field (usces_popular) will be added.', 'ill'); ?></div></td>
		</tr>
	</table>
	<input name="ill_action" type="hidden" value="update_customfield" />
</form>
</div>
</div><!--postbox-->
</div><!--poststuff-->



<p><?php _e("When there's category.php in the theme folder, please edit according to category.php. If there is none, please copy the attached category.php to the theme folder.",'ill'); ?></p>
<p><?php _e('When custom fields are updated, custom fields titled usces_price, usces_popular will be made. If it already exists, it will be updated.', 'ill'); ?></p>
<p><?php _e('Updating custom fields will take time if there are many items.', 'ill'); ?></p>
</div><!--usces_admin-->
</div><!--wrap-->