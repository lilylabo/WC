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
<h2 class="pagetitle"><?php esc_html_e( single_cat_title('', false) ); ?></h2>

<div class="catbox">

<?php if( usces_is_cat_of_item($wp_query->query_vars['cat']) ) : //商品カテゴリーの場合(Welcart0.6以降) ?>

	<div class="ill_header_block"><?php wcex_ill_header(); //ヘッダー（html） ?></div>

	<?php if (have_posts()) : //商品が有ったら ?>
	
	<?php wcex_ill_sort_navigation(); //ソート用ナビゲーション ?>
	
	<div class="navigation clearfix">
		<div style="float:right;"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
		<div style="float:left;"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
	</div>
	
	<?php wcex_item_list_layout(); //ループコンテンツ ?>
	
	<div class="navigation clearfix">
		<div style="float:right;"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
		<div style="float:left;"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
	</div>
	
	<?php else : //商品が無かったら ?>
	
		<p><?php echo single_cat_title('', false); ?><?php _e('in not yet registered', 'usces') ?></p>
		
	<?php endif; ?>
	
	<div class="ill_footer_block"><?php wcex_ill_footer(); //フッター（html） ?></div>
	

<?php else : //商品以外のカテゴリーの場合 ?>


	<?php if (have_posts()) : //記事が有ったら ?>
	
	<div class="navigation clearfix">
		<div style="float:right;"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
		<div style="float:left;"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
	</div>
	
	<?php while (have_posts()) : the_post(); ?>
	<div <?php post_class(); ?>>
		<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permanent Link to %s', 'kubrick'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h3>
		<div class="entry">
			<p><small><?php the_date('Y/n/j'); ?></small></p>
			<?php the_content() ?>
		</div>
	</div>
	<?php endwhile; ?>
	
	<div class="navigation clearfix">
		<div style="float:right;"><?php next_posts_link(__('Next article &raquo;', 'usces')); ?></div>
		<div style="float:left;"><?php previous_posts_link(__('&laquo; Previous article', 'usces')); ?></div>
	</div>

	<?php else : //記事が無かったら ?>
	
		<p><?php echo single_cat_title('', false); ?><?php _e('in not yet registered', 'usces') ?></p>
		
	<?php endif; ?>
	
	
	
<?php endif; ?>

</div><!-- end of catbox -->
</div><!-- end of content -->

<?php get_footer(); ?>