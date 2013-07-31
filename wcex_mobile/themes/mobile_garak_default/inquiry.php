<?php
/*
Template Name: Inquiry
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
*/
get_header();
?>

<div id="content">
<div class="catbox">
	<h1 class="page_title"><?php _e('Visit/Contact Us','usces') ?></h1>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="inqbox">
		<?php the_content(); ?>
		<?php wcmb_the_inquiry_form(); ?>
		<?php edit_post_link(__('Edit this entry.', 'kubrick'), '<p>', '</p>'); ?>
	</div>
	<?php endwhile; endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_footer(); ?>
