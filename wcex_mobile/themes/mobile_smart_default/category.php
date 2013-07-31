<?php
/**
 * <meta content="charset=UTF-8">
 * Sumple cotegory.php
 */
// このテンプレートにはサイドバーが設置されていません。
// お使いのテーマにあわせたカスタマイズを行ってください。

get_header();
?>

<div id="content">
<div class="ttl"><h1 class="pagetitle"><?php esc_html_e( single_cat_title('', false) ); ?></h1></div>

<div class="catbox">

<?php if( usces_is_cat_of_item($wp_query->query_vars['cat']) ) : //商品カテゴリーの場合(Welcart0.6以降) ?>

<?php if (have_posts()) : //商品が有ったら ?>

<?php // wcex_ill_sort_navigation(); //ソート用ナビゲーション ?>

<div class="navigation clearfix">
<div class="next"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
<div class="prev"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
</div>

<?php // wcex_item_list_layout(); //ループコンテンツ ?>

<?php while (have_posts()) : the_post(); ?>
<div class="archive">

<div <?php post_class(); ?>>
<a href="<?php the_permalink() ?>" class="clearfix"><span class="img_block"><span class="thumb"><?php usces_the_itemImage($number = 0, $width = 72, $height = 72 ); ?></span></span>
<strong><?php usces_the_itemName(); ?>&nbsp;(<?php usces_the_itemCode(); ?>)</strong>
<span class="price"><?php usces_crform( usces_the_firstPrice('return'), true, false ); ?><?php usces_guid_tax(); ?></span></a>
</div>

</div><!-- end of archive -->
<?php endwhile; ?>

<div class="navigation clearfix">
<div class="next"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
<div class="prev"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
</div>

<?php else : //商品が無かったら ?>

<p><?php echo single_cat_title('', false); ?><?php _e('in not yet registered', 'usces') ?></p>

<?php endif; ?>


<?php else : //商品以外のカテゴリーの場合 ?>


<?php if (have_posts()) : //記事が有ったら ?>

<div class="navigation clearfix">
<div class="next"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
<div class="prev"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
</div>

<?php while (have_posts()) : the_post(); ?>
<div class="archive">
<div <?php post_class(); ?>>
<h2 id="post-<?php the_ID(); ?>" class="list_title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
<div class="entry">
<p><small><?php the_date('Y/n/j'); ?></small></p>
<?php the_excerpt() ?>
</div>
</div>
</div>
<?php endwhile; ?>

<div class="navigation clearfix">
<div class="next"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
<div class="prev"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
</div>

<?php else : //記事が無かったら ?>

<p><?php echo single_cat_title('', false); ?><?php _e('in not yet registered', 'usces') ?></p>

<?php endif; ?>

<?php endif; ?>

</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>