<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content">
<?php if (have_posts()) : have_posts(); the_post(); ?>
<h1 class="pagetitle"><?php the_title(); ?></h1>
<div class="catbox">
	<div class="post" id="<?php echo $post->post_name; ?>">
		<div class="entry">
		
<?php $uscpaged = isset($_REQUEST['uscpaged']) ? $_REQUEST['uscpaged'] : 1; ?>
<?php
	$usccategory = '';
	if(isset($_REQUEST['category'])) {
		foreach((array)$_REQUEST['category'] as $catkey => $catval) 
			$usccategory .= '&category['.$catval.']='.$catval;
	}
?>
<div id="searchbox">

<?php //******* part of result ************** ?>
<?php
usces_remove_filter();
if (isset($_REQUEST['usces_search'])) :
	$catresult = usces_search_categories(); 
	$search_query = array('category__and' => $catresult, 'posts_per_page' => 10, 'paged' => $uscpaged);
	$search_query = apply_filters('usces_filter_search_query', $search_query);
	$my_query = new WP_Query( $search_query );
?>

	<div class="title"><?php _e('Search results', 'usces'); ?>&nbsp;&nbsp;<?php echo number_format($my_query->found_posts); ?><?php _e('cases', 'usces'); ?></div>

<?php if ($my_query->have_posts()) : ?>	
	<div class="navigation clearfix">
	<?php if( $uscpaged > 1 ) : ?>
		<a style="float:left; cursor:pointer;" href="<?php echo USCES_CART_URL . $wcmb_delim; ?>page=search_item&usces_search=&uscpaged=<?php echo ($uscpaged - 1); ?><?php echo $usccategory; ?>"><?php _e('&laquo; Previous article', 'usces'); ?></a>
	<?php endif; ?>
	<?php if( $uscpaged < $my_query->max_num_pages ) : ?>
		<a style="float:right; cursor:pointer;" href="<?php echo USCES_CART_URL . $wcmb_delim; ?>page=search_item&usces_search=&uscpaged=<?php echo ($uscpaged + 1); ?><?php echo $usccategory; ?>"><?php _e('Next article &raquo;', 'usces'); ?></a>
	<?php endif; ?>
	</div>


	<?php if( $search_result = apply_filters('usces_filter_search_result', NULL, $my_query)) : ?>
	<?php echo $search_result; ?>
	<?php else : ?>
	
	<div class="searchitems">
	<?php while ($my_query->have_posts()) : $my_query->the_post(); 	usces_the_item(); ?>
		<div class="itemlist clearfix">
			<div class="loopimg">
				<a href="<?php the_permalink(); ?>"><?php usces_the_itemImage(0, 100, 100, $post); ?></a>
			</div>
			<div class="loopexp">
				<div class="itemtitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
				<div class="field"><?php the_content(); ?></div>
			</div>
		</div>
	<?php endwhile; ?>
	</div><!-- searchitems -->

	<?php endif; ?>


	<div class="navigation clearfix">
	<?php if( $uscpaged > 1 ) : ?>
		<a style="float:left; cursor:pointer;" href="<?php echo USCES_CART_URL . $wcmb_delim; ?>page=search_item&usces_search=&uscpaged=<?php echo ($uscpaged - 1); ?><?php echo $usccategory; ?>"><?php _e('&laquo; Previous article', 'usces'); ?></a>
	<?php endif; ?>
	<?php if( $uscpaged < $my_query->max_num_pages ) : ?>
		<a style="float:right; cursor:pointer;" href="<?php echo USCES_CART_URL . $wcmb_delim; ?>page=search_item&usces_search=&uscpaged=<?php echo ($uscpaged + 1); ?><?php echo $usccategory; ?>"><?php _e('Next article &raquo;', 'usces'); ?></a>
	<?php endif; ?>
	</div>

<?php else : ?>
	<div class="searchitems"><p><?php _e('The article was not found.', 'usces'); ?></p></div>
<?php endif; ?>

<?php endif; wp_reset_query(); ?>
<?php //******* End Result ************** ?>

<?php //******* Start Form ************** ?>
	<form name="searchindetail" action="<?php echo USCES_CART_URL . $wcmb_delim; ?>page=search_item" method="post">
	<div class="field">
		<label class="outlabel"><?php _e('Categories: AND Search', 'usces'); ?></label><?php echo usces_categories_checkbox('return'); ?>
	</div>
	<input name="usces_search_button" class="usces_search_button" type="submit" value="<?php _e('Search', 'usces'); ?>" />
	<input name="uscpaged" id="usces_paged" type="hidden" value="1" />
	<input name="usces_search" type="hidden" />
	<?php do_action('usces_action_search_item_inform'); ?>
	</form>
<?php //******* End Form ************** ?>

</div><!-- searchbox -->

		</div><!-- end of entry -->
	</div><!-- end of post -->
	<?php endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_footer(); ?>
