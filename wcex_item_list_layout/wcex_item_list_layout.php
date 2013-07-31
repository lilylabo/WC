<?php
/*
Plugin Name: WCEX Item List Layout
Plugin URI: http://www.welcart.com/
Description: このプラグインはWelcart専用の拡張プラグインです。Welcart1系と一緒にご利用下さい。
Version: 1.3
Author: Collne Inc.
Author URI: http://www.collne.com/
*/


if ( !defined('USCES_EX_PLUGIN') )
	define('USCES_EX_PLUGIN', 1);
	
define('WCEX_ITEM_LIST_LAYOUT', true);
define('WCEX_ITEM_LIST_LAYOUT_VERSION', "1.3.0.1303151");

if ( defined('USCES_VERSION') ):

	load_plugin_textdomain('ill', WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/languages', plugin_basename(dirname(__FILE__)).'/languages');

	$ill_default_opts = array(
			'category' => 'other', 
			'style' => 'showcase', 
			'width' => 533, 
			'colum' => 3, 
			'limargin' => 10, 
			'lipadding' => 5, 
			'liborder' => 1, 
			'liheight' => 240, 
			'txtheight' => 50, 
			'posts_per_page' => 30, 
			'width_l' => 533, 
			'limargin_l' => 10, 
			'lipadding_l' => 15, 
			'imgwidth_l' => 150, 
			'illheader' => "", 
			'illfooter' => "", 
			'posts_per_page_l' => 30
		);
	

	$ill_options = get_option('item_list_layout');
	if( $ill_options == false ){
		$ill_options = array();
		$ill_options[0] = $ill_default_opts;
		update_option('item_list_layout', $ill_options);
	}
	$ill_sort = get_option('item_list_layout_sort');
	if( $ill_sort == false ){
		$ill_sort = array(
			'new' => '1', 
			'cheap' => '1', 
			'high' => '1', 
			'popular' => '1', 
			'name' => '1', 
			'code' => '1'
		);
		update_option('item_list_layout_sort', $ill_sort);
	}
	$ill_categories = get_option('item_list_layout_categories');
	if( $ill_categories == false ){
		$ill_categories = array();
		wcex_ill_categories_update();
//		$categories=  get_categories('hide_empty=0&child_of=' . USCES_ITEM_CAT_PARENT_ID);
//		$categories[]->term_id = USCES_ITEM_CAT_PARENT_ID;
//
//		foreach ($categories as $cat) {
//			$ill_categories[$cat->term_id] = 'default';
//		}
//		update_option('item_list_layout_categories', $ill_categories);
	}
	
	add_action('wp_print_scripts', 'item_list_layout_scripts');
	add_action('init', 'wcex_item_list_layout_init', 12);
	//add_action('admin_menu', 'item_list_layout_add_pages');
	add_action('usces_action_shop_admin_menue', 'item_list_layout_add_pages');
	add_action('parse_request', 'wcex_item_list_layout_query_posts',0);
	add_action('wp_head', 'wcex_item_list_layout_wp_head');
	add_action('add_category', 'wcex_ill_categories_update');
	add_action('create_category', 'wcex_ill_categories_update');
	add_action('delete_category', 'wcex_ill_categories_update');
	
endif;


function wcex_item_list_layout_wp_head(){
	if( file_exists(get_stylesheet_directory() . '/item_list_layout.css') ){
?>
<link href="<?php echo get_stylesheet_directory_uri(); ?>/item_list_layout.css" rel="stylesheet" type="text/css" />
<?php
	}
}

function item_list_layout_scripts(){
	
	//if ( is_plugin_page() && $_REQUEST['page'] == 'wcex_item_list_layout' ) {
	if ( is_admin() && (isset($_REQUEST['page']) && $_REQUEST['page'] == 'wcex_item_list_layout') ) {
	
		wp_enqueue_script('jquery-ui-tabs', array('jquery-ui-core'));
		$jquery_cookieUrl = USCES_WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/js/jquery.cookie.js';
		wp_enqueue_script('jquery-cookie', $jquery_cookieUrl, array('jquery'), '1.0' );
		$item_list_layoutUrl = USCES_WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/js/item_list_layout.js';
		wp_enqueue_script('item_list_layout', $item_list_layoutUrl, array('jquery-ui-tabs'), '1.0' );
	
	}
}

function item_list_layout_add_pages(){
	add_submenu_page(USCES_PLUGIN_BASENAME, __('Item List Layout','ill'), __('Item List Layout','ill'), 'level_5', 'wcex_item_list_layout', 'admin_item_list_layout_page');
}

function wcex_item_list_layout_init(){
	global $usces;
	
	$cssUrl = USCES_WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/css/item_list_layout.css';
	if( !is_admin() )
		wp_enqueue_style('itemListLayoutStyleSheets', $cssUrl );
		
	if ( is_admin() && (isset($_REQUEST['page']) && $_REQUEST['page'] == 'wcex_item_list_layout') ) {
		$admincssUrl = USCES_WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/css/admin.css';
		wp_enqueue_style('ItemListLayoutAdminStyleSheets', $admincssUrl );
	}
		
	if ( $usces->is_cart_page($_SERVER['REQUEST_URI']) && (isset($_REQUEST['page']) && $_REQUEST['page'] == 'search_item') ) {
		add_filter('usces_filter_search_query', 'ill_filter_search_query',10);
		add_filter('usces_filter_search_result', 'ill_filter_search_result', 10, 2);
	}
}

function ill_filter_search_query($search_query){
	$search_query = wcex_item_list_layout_search_query( $search_query );
	return $search_query;
}

function ill_filter_search_result_pre(){
	$args = func_get_args();
	$html = wcex_ill_sort_navigation( $args[1], 'return' );
	return $html;
}

function ill_filter_search_result(){
	global $usces, $post, $ill_default_opts;
	$args = func_get_args();
	$my_query = $args[1];

	$ill_options = get_option('item_list_layout');

	$ill_categories = get_option('item_list_layout_categories');
	if ( $usces->is_cart_page($_SERVER['REQUEST_URI']) && $_REQUEST['page'] == 'search_item' ) {
		//$id = $ill_categories[ 'search' ];
		$id = ( !empty($ill_categories[ 'search' ]) ) ? $ill_categories[ 'search' ] : 'default';

	}else if( isset( $ill_categories[ $my_query->query_vars['cat'] ] ) !== false ){
		$id = $ill_categories[ $my_query->query_vars['cat'] ];
	}else{
		$id = 'default';
	}
	if( 'default' === $id  || $id === NULL ){
		$opts = $ill_default_opts;
	}else{
		$opts = $ill_options[$id];
	}
	
	if( 'showcase' == $opts['style'] ){
	
		$width = $opts['width'];
		$colum = $opts['colum'];
		$limargin = $opts['limargin'];
		$lipadding = $opts['lipadding'];
		$liborder = $opts['liborder'];
		$liwidth = ($width + $limargin) / $colum - $limargin - ($lipadding * 2) - ($liborder * 2);
		$liheight = $opts['liheight'] - $lipadding * 2;
		$txtheight = $opts['txtheight'];
		$imgwidth = $liwidth;
		$imgheight = $liheight - $txtheight;
		$border = $liborder ? '' : 'border:0px';
	
		$html = '<div id="ill_wrap" class="item_list_layout" style="width:'.$width.'px;">' . "\n";
		$html .= '<ul id="ill_ul" class="item_list_layout_ul clearfix" style="width:'.($width+$limargin).'px;">' . "\n";
		
		while ($my_query->have_posts()) {
			$my_query->the_post();
			usces_the_item();
	
			$list = '<li id="ill_li" class="item_list_layout_li" style="text-align: center; overflow: hidden; display: block; float: left; padding:'.$lipadding.'px; width:'.$liwidth.'px; height:'.$liheight.'px; margin-right:'.$limargin.'px; margin-bottom:'.$limargin.'px; '.$border.'">
			<a href="' . get_permalink($post->ID) . '">' . usces_the_itemImage(0, $imgwidth, $imgheight, $post, 'return' ) . '<div class="thumtitle">' . esc_html(usces_the_itemName('return')) . '</div></a>';
			$price = '<div class="price">';
			if( function_exists('usces_crform') ){
				$price .= usces_crform( usces_the_firstPrice('return'), true, false, 'return' );
			}else{
				$price .= __('$', 'usces') . number_format(usces_the_firstPrice('return'));
			}
			$price .= $usces->getGuidTax() . '</div>';
			$list .= apply_filters('item_list_layout_filter_list_price', $price, $post, $opts);
			$list .= '</li>' . "\n";
			$html .= apply_filters('item_list_layout_filter_list', $list, $post, $opts);
		}
		$html .= '</ul>' . "\n";
		$html .= '</div>' . "\n";
		
	}else if( 'list' == $opts['style'] ){
	
		$width = $opts['width_l'];
		$limargin = $opts['limargin_l'];
		$lipadding = $opts['lipadding_l'];
		$liwidth = $width - $lipadding * 2;
		$imgwidth = $opts['imgwidth_l'];
		$imgheight = $liwidth;
	
		$html = '<div id="ill_wrap" class="item_list_layout" style="width:'.$width.'px;">' . "\n";
		$html .= '<ul id="ill_ul" class="item_list_layout_ul" style="width:'.$width.'px;">' . "\n";
		while ($my_query->have_posts()) {
			$my_query->the_post();
			usces_the_item();
	
			$excerpt = ( $post->post_excerpt == '' ) ? $post->post_content : $post->post_excerpt;
			$list = '<li id="ill_li" class="item_list_layout_li list clearfix" style="padding:'.$lipadding.'px; width:'.$liwidth.'px; margin-bottom:'.$limargin.'px;">
			<a href="' . get_permalink($post->ID) . '">' . usces_the_itemImage(0, $imgwidth, $imgheight, $post, 'return' ) . '<div class="thumtitle">' . esc_html(usces_the_itemName('return')) . '</div></a>';
			$price = '<div class="price">';
			if( function_exists('usces_crform') ){
				$price .= usces_crform( usces_the_firstPrice('return'), true, false, 'return' );
			}else{
				$price .= __('$', 'usces') . number_format(usces_the_firstPrice('return'));
			}
			$price .= $usces->getGuidTax() . '</div><div class="exp">' . $excerpt . '</div>';
			$list .= apply_filters('item_list_layout_filter_list_price', $price, $post, $opts);
			$list .= '</li>' . "\n";
			$html .= apply_filters('item_list_layout_filter_list', $list, $post, $opts);
		}
		$html .= '</ul>' . "\n";
		$html .= '</div>' . "\n";
		
	}
	return $html;
}

function wcex_item_list_layout(){
	global $usces, $posts, $post, $wp_query, $ill_default_opts;
	$ill_options = get_option('item_list_layout');
	$ill_categories = get_option('item_list_layout_categories');
	$id = ( isset( $ill_categories[ $wp_query->query_vars['cat'] ] ) !== false ) ? $ill_categories[ $wp_query->query_vars['cat'] ] : 'default';
	if( 'default' === $id  || $id === NULL ){
		$opts = $ill_default_opts;
	}else{
		$opts = $ill_options[$id];
	}

	if( 'showcase' == $opts['style'] ){
	
		$width = $opts['width'];
		$colum = $opts['colum'];
		$limargin = $opts['limargin'];
		$lipadding = $opts['lipadding'];
		$liborder = $opts['liborder'];
		$liwidth = ($width + $limargin) / $colum - $limargin - ($lipadding * 2) - ($liborder * 2);
		$liheight = $opts['liheight'] - $lipadding * 2;
		$txtheight = $opts['txtheight'];
		$imgwidth = $liwidth;
		$imgheight = $liheight - $txtheight;
		$border = $liborder ? '' : 'border:0px';
	
		$html = '<div id="ill_wrap" class="item_list_layout" style="width:'.$width.'px;">' . "\n";
		$html .= '<ul id="ill_ul" class="item_list_layout_ul clearfix" style="width:'.($width+$limargin).'px;">' . "\n";
		while (have_posts()) {
			the_post();
			usces_the_item();
	
			$list = '<li id="ill_li" class="item_list_layout_li" style="text-align: center; overflow: hidden; display: block; float: left; padding:'.$lipadding.'px; width:'.$liwidth.'px; height:'.$liheight.'px; margin-right:'.$limargin.'px; margin-bottom:'.$limargin.'px; '.$border.'">
			<a href="' . get_permalink($post->ID) . '">' . usces_the_itemImage(0, $imgwidth, $imgheight, $post, 'return' ) . '<div class="thumtitle">' . esc_html(usces_the_itemName('return')) . '</div></a>';
			$price = '<div class="price">';
			if( function_exists('usces_crform') ){
				$price .= usces_crform( usces_the_firstPrice('return'), true, false, 'return' );
			}else{
				$price .= __('$', 'usces') . number_format(usces_the_firstPrice('return'));
			}
			$price .= $usces->getGuidTax() . '</div>';
			$list .= apply_filters('item_list_layout_filter_list_price', $price, $post, $opts);
			$list .= '</li>' . "\n";
			$html .= apply_filters('item_list_layout_filter_list', $list, $post, $opts);
		}
		$html .= '</ul>' . "\n";
		$html .= '</div>' . "\n";
		
	}else if( 'list' == $opts['style'] ){
	
		$width = $opts['width_l'];
		$limargin = $opts['limargin_l'];
		$lipadding = $opts['lipadding_l'];
		$liwidth = $width - $lipadding * 2;
		$imgwidth = $opts['imgwidth_l'];
		$imgheight = $liwidth;
	
		$html = '<div id="ill_wrap" class="item_list_layout" style="width:'.$width.'px;">' . "\n";
		$html .= '<ul id="ill_ul" class="item_list_layout_ul" style="width:'.$width.'px;">' . "\n";
		
		while (have_posts()) {
			the_post();
			usces_the_item();
	
			$list = '<li id="ill_li" class="item_list_layout_li list clearfix" style="padding:'.$lipadding.'px; width:'.$liwidth.'px; margin-bottom:'.$limargin.'px;">
			<a href="' . get_permalink($post->ID) . '">' . usces_the_itemImage(0, $imgwidth, $imgheight, $post, 'return' ) . '<div class="thumtitle">' . esc_html(usces_the_itemName('return')) . '</div></a>';
			$price = '<div class="price">';
			if( function_exists('usces_crform') ){
				$price .= usces_crform( usces_the_firstPrice('return'), true, false, 'return' );
			}else{
				$price .= __('$', 'usces') . number_format(usces_the_firstPrice('return'));
			}
			$price .= $usces->getGuidTax() . '</div><div class="exp">' . do_shortcode( get_the_content('[...]') ) . '</div>';
			$list .= apply_filters('item_list_layout_filter_list_price', $price, $post, $opts);
			$list .= '</li>' . "\n";
			$html .= apply_filters('item_list_layout_filter_list', $list, $post, $opts);
		}
		$html .= '</ul>' . "\n";
		$html .= '</div>' . "\n";
		
	}
	echo $html;
}

function wcex_ill_sort_navigation( $my_query = NULL, $out = '' ){

	$ill_sort = get_option('item_list_layout_sort');

	$html = '<ul class="sort_navigation clearfix">' . "\n";
	if( $ill_sort['new'] == '1' )
		$html .= '<li><a href="' . wcex_item_list_layout_cat_link($my_query) . 'ill_sort=new&ill_order=DESC">' . __('New to Old', 'ill') . '</a></li>' . "\n";
	if( $ill_sort['cheap'] == '1' )
		$html .= '<li><a href="' . wcex_item_list_layout_cat_link($my_query) . 'ill_sort=cheap&ill_order=ASC">' . __('Price: Low to High', 'ill') . '</a></li>' . "\n";
	if( $ill_sort['high'] == '1' )
		$html .= '<li><a href="' . wcex_item_list_layout_cat_link($my_query) . 'ill_sort=high&ill_order=DESC">' . __('Price: High to Low', 'ill') . '</a></li>' . "\n";
	if( $ill_sort['popular'] == '1' )
		$html .= '<li><a href="' . wcex_item_list_layout_cat_link($my_query) . 'ill_sort=popular&ill_order=DESC">' . __('Best Selling to Least', 'ill') . '</a></li>' . "\n";
	if( $ill_sort['name'] == '1' )
		$html .= '<li><a href="' . wcex_item_list_layout_cat_link($my_query) . 'ill_sort=name&ill_order=ASC">' . __('By Item Name', 'ill') . '</a></li>' . "\n";
	if( $ill_sort['code'] == '1' )
		$html .= '<li><a href="' . wcex_item_list_layout_cat_link($my_query) . 'ill_sort=code&ill_order=ASC">' . __('By Item Codee', 'ill') . '</a></li>' . "\n";
	$html .= '</ul>' . "\n";
	
	if( 'return' == $out ){
		return $html;
	}else{
		echo $html;
	}
}

function wcex_ill_header( $out = '' ){
	global $wp_query, $ill_default_opts;
	$ill_options = get_option('item_list_layout');
	$ill_categories = get_option('item_list_layout_categories');
	$id = ( isset( $ill_categories[ $wp_query->query_vars['cat'] ] ) !== false ) ? $ill_categories[ $wp_query->query_vars['cat'] ] : 'default';
	if( 'default' === $id  || $id === NULL ){
		$opts = $ill_default_opts;
	}else{
		$opts = $ill_options[$id];
	}
	
	if( 'return' == $out ){
		return $opts['illheader'];
	}else{
		echo $opts['illheader'];
	}
}

function wcex_ill_footer( $out = '' ){
	global $wp_query, $ill_default_opts;
	$ill_options = get_option('item_list_layout');
	$ill_categories = get_option('item_list_layout_categories');
	$id = ( isset( $ill_categories[ $wp_query->query_vars['cat'] ] ) !== false ) ? $ill_categories[ $wp_query->query_vars['cat'] ] : 'default';
	if( 'default' === $id  || $id === NULL ){
		$opts = $ill_default_opts;
	}else{
		$opts = $ill_options[$id];
	}
	
	if( 'return' == $out ){
		return $opts['illfooter'];
	}else{
		echo $opts['illfooter'];
	}
}

function wcex_item_list_layout_query_posts( $wp_query ){
	//global $wp_query;
	global $ill_default_opts;
	
	if(is_admin())
		return;
	
	if( isset($wp_query->query_vars['topitemreco']) )
		return;
	
	$ill_query = array();
	
	if( isset($_GET['ill_sort']) ){
		switch( $_GET['ill_sort'] ){
			case 'new':
				$ill_query['orderby'] = 'date';
				break;
			case 'cheap':
			case 'high':
				$ill_query['meta_key'] = 'usces_price';
				$ill_query['orderby'] = 'meta_value';
				break;
			case 'popular':
				$ill_query['meta_key'] = 'usces_popular';
				$ill_query['orderby'] = 'meta_value';
				break;
			case 'name':
				$ill_query['meta_key'] = '_itemName';
				$ill_query['orderby'] = 'meta_value';
				break;
			case 'code':
				$ill_query['meta_key'] = '_itemCode';
				$ill_query['orderby'] = 'meta_value';
				break;
			default:
				$ill_query['orderby'] = 'ID';
		}
	}else{
		$ill_query['orderby'] = 'ID';
	}
	if( isset($_GET['ill_order']) ){
		$ill_query['order'] = $_GET['ill_order'];
	}else{
		$ill_query['order'] = 'DESC';
		$ill_query = apply_filters('lli_filter_default_order', $ill_query);
	}
	
	if( !isset($wp_query->query_vars['cat']) || empty($wp_query->query_vars['cat']) ){
		//$category_name = substr($wp_query->query_vars['category_name'], strrpos($wp_query->query_vars['category_name'], '/'));
		$query_vars_category_name = isset($wp_query->query_vars['category_name']) ? $wp_query->query_vars['category_name'] : '';
		$category_name = substr($query_vars_category_name, strrpos($query_vars_category_name, '/'));
		$cat_ob = get_category_by_slug($category_name);
		//$cat_id = (int)$cat_ob->term_id;
		$cat_id = (!empty($cat_ob)) ? (int)$cat_ob->term_id : 0;
	}else{
		$cat_id = (int)$wp_query->query_vars['cat'];
	}
	$paged = empty($wp_query->query_vars['paged']) ? 1 : $wp_query->query_vars['paged'];
	
	$ill_options = get_option('item_list_layout');
	$ill_categories = get_option('item_list_layout_categories');
	$id = ( isset( $ill_categories[ $cat_id ] ) !== false ) ? $ill_categories[ $cat_id ] : 'default';
	if( 'default' === $id  || $id === NULL ){
		$opts = $ill_default_opts;
	}else{
		$opts = $ill_options[$id];
	}
	if( 'showcase' == $opts['style'] ){
		$ill_query['posts_per_page'] = $opts['posts_per_page'] ? $opts['posts_per_page'] : 12;
	}else if( 'list' == $opts['style'] ){
		$ill_query['posts_per_page'] = $opts['posts_per_page_l'] ? $opts['posts_per_page_l'] : 12;
	}
				
	$ill_query['cat'] = $cat_id;
	$ill_query['paged'] = $paged;
	$ill_query['cache_results'] = false;
	$ill_query['post_status'] = 'publish';


	if( usces_is_cat_of_item($cat_id) ){
		$wp_query->query_vars = apply_filters('lli_filter_query_vars', array_merge( $wp_query->query_vars, $ill_query ));
	} 
}

function wcex_item_list_layout_search_query( $search_query ){
	global $wp_query, $usces, $ill_default_opts;

	$ill_query = array();
	
	if( isset($_GET['ill_sort']) ){
		switch( $_GET['ill_sort'] ){
			case 'new':
				$ill_query['orderby'] = 'date';
				break;
			case 'cheap':
			case 'high':
				$ill_query['meta_key'] = 'usces_price';
				$ill_query['orderby'] = 'meta_value';
				break;
			case 'popular':
				$ill_query['meta_key'] = 'usces_popular';
				$ill_query['orderby'] = 'meta_value';
				break;
			case 'name':
				$ill_query['meta_key'] = '_itemName';
				$ill_query['orderby'] = 'meta_value';
				break;
			case 'code':
				$ill_query['meta_key'] = '_itemCode';
				$ill_query['orderby'] = 'meta_value';
				break;
			default:
				$ill_query['orderby'] = 'ID';
		}
	}else{
		$ill_query['orderby'] = 'ID';
	}
	if( isset($_GET['ill_order']) ){
		$ill_query['order'] = $_GET['ill_order'];
	}else{
		$ill_query['order'] = 'DESC';
		$ill_query = apply_filters('lli_filter_search_default_order', $ill_query);
	}
	
	$ill_options = get_option('item_list_layout');
	$ill_categories = get_option('item_list_layout_categories');

	if ( $usces->is_cart_page($_SERVER['REQUEST_URI']) && $_REQUEST['page'] == 'search_item' ) {
		//$id = $ill_categories[ 'search' ];
		$id = ( !empty($ill_categories[ 'search' ]) ) ? $ill_categories[ 'search' ] : 'default';
	}else if( isset( $ill_categories[ $wp_query->query_vars['cat'] ] ) !== false ){
		$id = $ill_categories[ $wp_query->query_vars['cat'] ];
	}else{
		$id = 'default';
	}
	if( 'default' === $id  || $id === NULL ){
		$opts = $ill_default_opts;
	}else{
		$opts = $ill_options[$id];
	}
	$ill_query['posts_per_page'] = $opts['posts_per_page'] ? $opts['posts_per_page'] : 12;
	$ill_query['paged'] = $wp_query->query_vars['paged'];
	if( isset($_GET['category__and']) ){
		$ill_query['category__and'] = explode(',', $_GET['category__and']);
	}
	$ill_query['cache_results'] = false;
	$ill_query['post_status'] = 'publish';

	$search_query = apply_filters('lli_filter_search_query_vars', array_merge( $search_query, $ill_query ));

	return $search_query;
}

function wcex_item_list_layout_cat_link( $my_query = NULL ){
	global $usces;
	
	if($my_query != NULL && isset($_REQUEST['usces_search'])){
		$wp_query = $my_query;
	}else{
		global $wp_query;
	}
	
	if ( $usces->is_cart_page($_SERVER['REQUEST_URI']) && $_REQUEST['page'] == 'search_item' ) {
	
		$link = USCES_CART_URL . $usces->delim . 'page=search_item&usces_search&category__and=' . implode(',', $wp_query->query_vars['category__and']) . '&';
	
	}else{
	
		$cat = (int)$wp_query->query_vars['cat'];
		$link = get_category_link( $cat );
		if( strpos($link, '?') !== false ){
			$link .= '&';
		}else{
			$link .= '?';
		}
	}
	
	return $link;
}

function ill_get_layout_name( $cat_id ){
	switch ($cat_id){
		case 'other':
			$layout_name = __('Other Categories', 'ill');
			break;
		case 'search':
			$layout_name = __('Result of Multiple Item Search', 'ill');
			break;
		default:
			$term = get_category( $cat_id, ARRAY_A );
			$layout_name = $term['name'];
	}
	return $layout_name;
}

function admin_item_list_layout_page() {
	global $usces, $wpdb, $wp_locale, $current_user;
	global $wp_query, $ill_default_opts;
	
	$action = isset($_POST['ill_action']) ? $_POST['ill_action'] : '';
	
	if( '' != $action)
		
	switch ( $action ){
	
		case 'edit_layout':
	
			$ill_options = get_option('item_list_layout');
			
			if( isset($_POST['update_layout']) ){
			
				$keys = array_keys( $_POST['update_layout'] );
				$id = $keys[0];
					$ill_options[$id]['category'] = $_POST["category_{$id}"];
					$ill_options[$id]['style'] = $_POST["style_{$id}"];
					$ill_options[$id]['width'] = (int)$_POST["width_{$id}"];
					$ill_options[$id]['colum'] = (int)$_POST["colum_{$id}"];
					$ill_options[$id]['limargin'] = (int)$_POST["limargin_{$id}"];
					$ill_options[$id]['lipadding'] = (int)$_POST["lipadding_{$id}"];
					$ill_options[$id]['liborder'] = (int)$_POST["liborder_{$id}"];
					$ill_options[$id]['liheight'] = (int)$_POST["liheight_{$id}"];
					$ill_options[$id]['txtheight'] = (int)$_POST["txtheight_{$id}"];
					$ill_options[$id]['posts_per_page'] = (int)$_POST["posts_per_page_{$id}"];
					$ill_options[$id]['width_l'] = (int)$_POST["width_l_{$id}"];
					$ill_options[$id]['limargin_l'] = (int)$_POST["limargin_l_{$id}"];
					$ill_options[$id]['lipadding_l'] = (int)$_POST["lipadding_l_{$id}"];
					$ill_options[$id]['imgwidth_l'] = (int)$_POST["imgwidth_l_{$id}"];
					$ill_options[$id]['illheader'] = stripslashes($_POST["illheader_{$id}"]);
					$ill_options[$id]['illfooter'] = stripslashes($_POST["illfooter_{$id}"]);
					$ill_options[$id]['posts_per_page_l'] = (int)$_POST["posts_per_page_l_{$id}"];
				$layout_name = ill_get_layout_name( $ill_options[$id]['category'] );
				$usces->action_status = 'success';
				$usces->action_message = sprintf(__('%s Updated', 'ill'), $layout_name . '_' . $id);
				
			}else if( isset($_POST['add_layout']) ){
			
				$id = max(array_keys($ill_options)) + 1;
				$ill_options[$id] = $ill_default_opts;
				$usces->action_status = 'success';
				$layout_name = __('Other Categories', 'ill');
				$usces->action_message = sprintf(__('%s Added', 'ill'), $layout_name . '_' . $id);
				
			}else if( isset($_POST['del_layout']) ){
			
				$keys = array_keys( $_POST['del_layout'] );
				$id = $keys[0];
				$layout_name = ill_get_layout_name( $ill_options[$id]['category'] );
				unset($ill_options[$id]);
				$usces->action_status = 'success';
				$usces->action_message = sprintf(__('%s Deleted', 'ill'), $layout_name . '_' . $id);
			
			}
	
			update_option('item_list_layout', $ill_options);
			
			wcex_ill_categories_update();
//			$ill_categories = array();
//			$categories =  get_categories('hide_empty=0&child_of=' . USCES_ITEM_CAT_PARENT_ID); 
//			$categories[]->term_id = USCES_ITEM_CAT_PARENT_ID;
//			foreach ($categories as $cat) {
//				$ill_categories[$cat->term_id] = 'default';
//			}
//
//			$other = false;
//			$no_others = array();
//			foreach( $ill_options as $opt_num => $opt_val ){
//				if( 'other' != $opt_val['category'] ){
//					$ill_categories[$opt_val['category']] = $opt_num;
//					$no_others[] = $opt_val['category'];
//				}else{
//					$other = $opt_num;
//				}
//			}
//			if( $other !== false ){
//				foreach( $ill_categories as $cat => $opt_num ){
//					if( !in_array($cat, $no_others) ){
//						$ill_categories[$cat] = $other;
//					}
//				}
//			}
//			update_option('item_list_layout_categories', $ill_categories);

			break;
		
		case 'update_sort':
		
			if( isset($_POST['sort']) ){
				$ill_sort = get_option('item_list_layout_sort');
				$ill_sort['new'] = isset( $_POST['sort']['new'] ) ? '1' : '0';
				$ill_sort['cheap'] = isset( $_POST['sort']['cheap'] ) ? '1' : '0';
				$ill_sort['high'] = isset( $_POST['sort']['high'] ) ? '1' : '0';
				$ill_sort['popular'] = isset( $_POST['sort']['popular'] ) ? '1' : '0';
				$ill_sort['name'] = isset( $_POST['sort']['name'] ) ? '1' : '0';
				$ill_sort['code'] = isset( $_POST['sort']['code'] ) ? '1' : '0';
				update_option('item_list_layout_sort', $ill_sort);
			}
			break;
		
		case 'update_customfield':
		
			if( isset($_POST['ill_field_price']) ){
				$res = ill_update_customfield_price();
				$fieldname = __('Item Price Information', 'ill');
			}else if( isset($_POST['ill_field_popular']) ){
				$res = ill_update_customfield_popular();
				$fieldname = __('Bestselling Item Information', 'ill');
			}
			
			if( $res === true ){
				$usces->action_status = 'success';
				$usces->action_message = sprintf(__('Update of %s Completed', 'ill'), $fieldname);
			}else if( $res === false ){
				$usces->action_status = 'error';
				$usces->action_message = sprintf(__('Update of %s Not Completed', 'ill'), $fieldname);
			}else{
				$usces->action_status = '';
				$usces->action_message = '';
			}
			break;
	}
	
	if(empty($usces->action_message) || $usces->action_message == '') {
		$usces->action_status = 'none';
		$usces->action_message = '';
	}

	require_once(USCES_WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/admin_item_list_layout_page.php');
}

function wcex_ill_categories_update(){
	$ill_options = get_option('item_list_layout');
	$ill_categories = array();
	$categories =  get_categories('hide_empty=0&child_of=' . USCES_ITEM_CAT_PARENT_ID); 
	$categories[]->term_id = USCES_ITEM_CAT_PARENT_ID;
	foreach ($categories as $cat) {
		$ill_categories[$cat->term_id] = 'default';
	}

	$other = false;
	$no_others = array();
	foreach( $ill_options as $opt_num => $opt_val ){
		if( 'other' != $opt_val['category'] ){
			$ill_categories[$opt_val['category']] = $opt_num;
			$no_others[] = $opt_val['category'];
		}else{
			$other = $opt_num;
		}
	}
	if( $other !== false ){
		foreach( $ill_categories as $cat => $opt_num ){
			if( !in_array($cat, $no_others) ){
				$ill_categories[$cat] = $other;
			}
		}
	}
	update_option('item_list_layout_categories', $ill_categories);
}

function ill_update_customfield_price(){
	global $usces;
	$ret = true;
	
	$items = $usces->getItemIds('front');
	foreach ( (array) $items as $post_id ){
		$skus = $usces->get_skus( $post_id );
		if( !empty($skus[0]['price']) ) {
			$price = str_pad($skus[0]['price'], 11, "0", STR_PAD_LEFT);
		}else{
			$price = '';
		}
		update_post_meta( $post_id, 'usces_price', $price );
	}
	
	return $ret;
}

function ill_update_customfield_popular( $days = 30 ){
	global $wpdb, $usces;
	$ret = true;
	$order_table = $wpdb->prefix . "usces_order";
	$enddate = substr(get_date_from_gmt(gmdate('Y-m-d H:i:s', time())), 0, 10);
	//$yearstr = substr($datestr, 0, 4);
	//$monthstr = substr($datestr, 5, 2);
	//$daystr = substr($datestr, 8, 2);
	$yearstr = substr($enddate, 0, 4);
	$monthstr = substr($enddate, 5, 2);
	$daystr = substr($enddate, 8, 2);
	$startdate = date('Y-m-d', mktime(0, 0, 0, (int)$monthstr, ($daystr - $days), (int)$yearstr));

	$query = $wpdb->prepare("SELECT order_cart FROM $order_table 
								WHERE order_date >= %s AND order_date <= %s", $startdate, $enddate);
	$carts = $wpdb->get_col( $query );
	if( !$carts )
		$ret = false;

	$data = array();
	foreach ( (array) $carts as $cartstr ){
		$cart = unserialize($cartstr);
		foreach ( (array) $cart as $row ){
			$post_id = $row['post_id'];
			if( isset($data[$post_id]) ){
				$data[$post_id] += $row['quantity'];
			}else{
				$data[$post_id] = $row['quantity'];
			}
			//$res = update_post_meta( $post_id, 'usces_price', $price );
		}
	}
	$items = $usces->getItemIds('front');
	$items = array_flip($items);
	foreach ( $items as $post_id => $value ){
			$items[$post_id] = '*';
	}
	foreach ( $data as $key => $value ){
		if( isset($items[$key]) ){
			$items[$key] = $value;
		}
	}
	foreach ( $items as $post_id => $value ){
		$value = str_pad($value, 11, "0", STR_PAD_LEFT);
		update_post_meta( $post_id, 'usces_popular', $value );
	}
	return $ret;
}
?>
