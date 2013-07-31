<?php
global $usces_entries, $usces_carts;
usces_get_entries();
usces_get_carts();

$mid = $usces_entries['customer']['ID'];
$oid = $usces_entries['order']['ID'];

$html = '';

$html .= '<h3>'.__('It has been sent succesfully.', 'usces').'</h3>'."\n";
$html .= '<div class="post">'."\n";
$html .= '<div class="download">'."\n";
$html .= '<div class="header_explanation">'."\n";
$header = '<p>'.__('Thank you for shopping.', 'usces').'<br />'.__("If you have any questions, please contact us by 'Contact'.", 'usces').'</p>';
$html .= apply_filters('usces_filter_cartcompletion_page_header', $header, $usces_entries, $usces_carts)."\n";
//$html .= apply_filters('usces_filter_cartcompletion_page_header', NULL,$usces_entries );
$html .= '</div><!-- header_explanation -->'."\n";

$cart_row = $usces_carts[0];
$post_id = $cart_row['post_id'];
$sku = $cart_row['sku'];
$item_post = get_post( $post_id );
$usces_item = $this->get_item( $post_id );
$cartItemName = $this->getCartItemName($post_id, $sku );
$periods = dlseller_get_validityperiod($mid, $post_id);

$dlseller_options = get_option('dlseller');


$html .= dlseller_completion_info($usces_carts , 'return');


require( USCES_PLUGIN_DIR . "/includes/completion_settlement.php");

$html .= apply_filters('usces_filter_cartcompletion_page_body', NULL, $usces_entries, $usces_carts)."\n";

$html .= '<form action="' . get_option('home') . '" method="post" onKeyDown="if (event.keyCode == 13) {return false;}">
<div class="send"><input name="top" type="submit" value="'.__('Back to the top page.', 'usces').'" /></div>
</form>';

$html .= '<div class="footer_explanation">';
$footer = '';
$html .= apply_filters('usces_filter_cartcompletion_page_footer', $footer);
$html .= '</div><!-- footer_explanation -->';

$html .= '</div><!-- download -->
</div><!-- post -->';
?>
