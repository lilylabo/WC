<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content">
<div class="catbox">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="post" id="<?php echo $post->post_name; ?>">
		<h1<?php wcmb_change_style('page_title', 'text-align:center;'); ?>><?php the_title(); ?></h1>
		<div class="entry">
		<?php the_content(); ?>
		<?php wp_link_pages(array('before' => '<p><strong>' . __('Pages:', 'kubrick') . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		</div>
	</div>
	<?php endwhile; endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_footer(); ?>
