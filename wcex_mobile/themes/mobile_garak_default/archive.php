<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content">
<div id="category">
<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>
<h1 class="page_title"><?php printf(__('%s', 'usces'), single_cat_title('', false)); ?></h1>
<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
<h1 class="page_title"><?php printf(__('Posts Tagged &#8216;%s&#8217;', 'usces'), single_tag_title('', false) ); ?></h1>
<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
<h1 class="page_title"><?php printf(_c('Archive for %s|Daily archive page', 'usces'), get_the_time(__('Y/m/d'))); ?></h1>
<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
<h1 class="page_title"><?php printf(_c('Archive for %s|Monthly archive page', 'kubrick'), get_the_time(__('Y/m/d'))); ?></h1>
<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
<h1 class="page_title"><?php printf(_c('Archive for %s|Yearly archive page', 'kubrick'), get_the_time(__('Y/m/d'))); ?></h1>
<?php /* If this is an author archive */ } elseif (is_author()) { ?>
<h1 class="page_title"><?php _e('Author Archive', 'kubrick'); ?></h1>
<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
<h1 class="page_title"><?php _e('Blog Archives', 'kubrick'); ?></h1>
<?php } ?>

	<?php if (have_posts()) : ?>
	<div class="navigation clearfix">
		<div class="alignleft"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
		<div class="alignright"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
	</div>
	
	<?php while (have_posts()) : the_post(); ?>
	<div class="post">
		<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
		<div class="entry"><?php the_excerpt() ?></div>
		<?php if(!usces_is_item()): ?>
		<div class="entry-footer"><?php the_time('Y/n/j G:i'); ?></div>
		<?php endif; ?>
	</div>
	<?php endwhile; ?>
	
	<div class="navigation clearfix">
		<div class="alignleft"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
		<div class="alignright"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
	</div>

<?php else : ?>

	<?php if ( is_category() ) : // If this is a category archive ?>
	<p><?php echo single_cat_title('', false); ?><?php _e('in not yet registered', 'usces') ?></p>
	<?php elseif( is_date() ) : ?>
	<p><?php _e('Data for this date is not yet registered.', 'usces') ?></p>
	<?php elseif( is_author() ) : $userdata = get_userdatabylogin(get_query_var('author_name')); ?>
	<p><?php _e('Data by', 'usces') ?> <?php echo $userdata->display_name; ?> <?php _e('is not yet registered.', 'usces') ?></p>
	<?php else : ?>
	<p><?php echo __('No posts found.', 'kubrick'); ?></p>
	<?php endif; ?>
	
<?php endif; ?>

</div><!-- #category -->
</div><!-- #content -->

<?php get_footer(); ?>