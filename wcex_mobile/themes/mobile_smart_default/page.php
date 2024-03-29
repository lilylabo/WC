<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
get_header();
?>

<div id="content" class="two-column">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="ttl"><h1 class="pagetitle"><?php the_title(); ?></h1></div>
<div class="pagebox">
<div class="post" id="<?php echo $post->post_name; ?>">
<div class="entry">
<?php the_content(); ?>
<?php wp_link_pages(array('before' => '<p><strong>' . __('Pages:', 'uscestheme') . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
</div>
<?php comments_template( '', true ); ?>
</div>
</div><!-- end of catbox -->
<?php endwhile; endif; ?>
<?php edit_post_link(__('Edit this entry.', 'uscestheme'), '<p>', '</p>'); ?>
</div><!-- end of content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>
