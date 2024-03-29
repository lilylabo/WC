<?php
/*
Template Name: Inquiry
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
*/
get_header();
?>

<div id="content" class="two-column">
<div class="ttl">
<h1 class="pagetitle"><?php _e('Visit/Contact Us','usces') ?></h1>
</div>
<div class="pagebox">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="inqbox">
<?php the_content(); ?>
<?php usces_the_inquiry_form(); ?>
<?php edit_post_link(__('Edit this'), '<p>', '</p>'); ?>
</div>
<?php endwhile; endif; ?>
</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_sidebar('home'); ?>

<?php get_footer(); ?>
