<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */

get_header();
?>

<div id="content">
<h1 class="page_title"><?php esc_html_e( single_cat_title('', false) ); ?></h1>

<div id="category">
<?php if( usces_is_cat_of_item($wp_query->query_vars['cat']) ) : //商品カテゴリーの場合(Welcart0.6以降) ?>

	<?php if (have_posts()) : //商品が有ったら ?>
	
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div class="navigation">
		<?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?>｜<?php next_posts_link(__('Next article &raquo;', 'usces')); ?>
	</div>
	<?php endif; ?>
	
	<?php usces_remove_filter(); ?>
	<table class="itemlist">
	<?php while (have_posts()) : the_post(); usces_the_item(); ?>
	<tr>
		<td class="thumimg"><?php usces_the_itemImage($number = 0, $width = 70, $height = 70 ); ?></td>
		<td>
		<h2><a href="<?php the_permalink() ?>"><?php usces_the_itemName(); ?></a></h2>
		<?php if (usces_is_skus()) : ?>
		<div class="price"><?php usces_crform( usces_the_firstPrice('return'), true, false ); ?></div>
		<?php endif; ?>
		<div class="entry"><?php the_excerpt() ?></div>
		</td>
	</tr>
	<?php endwhile; ?>
	</table>
	
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div class="navigation">
		<?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?>｜<?php next_posts_link(__('Next article &raquo;', 'usces')); ?>
	</div>
	<?php endif; ?>
	
	<?php else : //商品が無かったら ?>
	
		<p><?php echo single_cat_title('', false); ?><?php _e('in not yet registered', 'usces') ?></p>
		
	<?php endif; ?>
	

<?php else : //商品以外のカテゴリーの場合 ?>


	<?php if (have_posts()) : //記事が有ったら ?>
	
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div class="navigation">
		<?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?>｜<?php next_posts_link(__('Next article &raquo;', 'usces')); ?>
	</div>
	<?php endif; ?>
	
	<?php while (have_posts()) : the_post(); ?>
	<div class="post">
		<h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
		<div class="entry"><?php the_excerpt() ?></div>
		<div class="entry-footer"><?php the_time('Y/n/j G:i'); ?></div>
	</div>
	<?php endwhile; ?>
	
	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div class="navigation">
		<?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?>｜<?php next_posts_link(__('Next article &raquo;', 'usces')); ?>
	</div>
	<?php endif; ?>

	<?php else : //記事が無かったら ?>
	
		<p><?php echo single_cat_title('', false); ?><?php _e('in not yet registered', 'usces') ?></p>
		
	<?php endif; ?>
	
	
	
<?php endif; ?>

</div><!-- #category -->
</div><!-- #content -->

<?php get_footer(); ?>