<?php
/**
 * <meta content="charset=UTF-8">
 * @package Welcart
 * @subpackage Welcart Default Theme
 */
?>

<div id="comment_block">

<?php if ( post_password_required() ) : ?>
<p><?php _e('Enter your password to view comments.'); ?></p>
<?php return; endif; ?>

<h2 id="comments">&oplus; <?php comments_number(__('No Comments'), __('1 Comment'), __('% Comments')); ?>
<?php if ( comments_open() ) : ?>
	<a href="#postcomment" title="<?php _e("Leave a comment"); ?>">&raquo;</a>
<?php endif; ?>
</h2>

<?php if ( $comments ) : ?>
<ol id="commentlist">

<?php foreach ($comments as $comment) : ?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
	<?php echo get_avatar( $comment, 32 ); ?>
		<div class="com_txt"><p><cite><?php comment_type(_x('Comment','noun'), __('Trackback'), __('Pingback')); ?> <?php _e('by'); ?> <?php comment_author_link() ?> &#8212; <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite> <?php edit_comment_link(__("Edit This"), ' |'); ?></p>
<?php comment_text() ?></div>
	</li>

<?php endforeach; ?>

</ol>

<?php else : // If there are no comments yet ?>
	<p><?php _e('No comments yet.'); ?></p>
<?php endif; ?>

<p class="feed"><?php post_comments_feed_link(__('<abbr title="Really Simple Syndication">RSS</abbr> feed for comments on this post.')); ?>
<?php if ( pings_open() ) : ?>
 | <a href="<?php trackback_url() ?>" rel="trackback"><?php _e('TrackBack <abbr title="Universal Resource Locator">URL</abbr>'); ?></a>
<?php endif; ?>
</p>

<?php if ( comments_open() ) : ?>
<h2 id="postcomment">&oplus; <?php _e('Leave a comment'); ?></h2>
<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.'), get_option('siteurl')."/wp-login.php?redirect_to=".urlencode(get_permalink()));?></p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

<?php if ( $user_ID ) : ?>

<p><?php printf(__('Logged in as %s.'), '<a href="'.get_option('siteurl').'/wp-admin/profile.php">'.$user_identity.'</a>'); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account') ?>"><?php _e('Log out &raquo;'); ?></a></p>

<?php else : ?>

<p><label for="author"><?php _e('Name'); ?> <?php if ($req) _e('(required)'); ?></label><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" /></p>
<p><label for="email"><?php _e('Mail (will not be published)');?> <?php if ($req) _e('(required)'); ?></label><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" /></p>
<p><label for="url"><?php _e('Website'); ?></label><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" /></p>

<?php endif; ?>

<!--<p><small><strong>XHTML:</strong> <?php printf(__('You can use these tags: %s'), allowed_tags()); ?></small></p>-->

<p><label for="url"><?php _e('Comment'); ?> <?php if ($req) _e('(required)'); ?></label><textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"></textarea></p>

<div class="send"><input name="submit" type="submit" id="submit" tabindex="5" value="<?php echo esc_attr(__('Submit Comment')); ?>" />
<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
</div>
<?php do_action('comment_form', $post->ID); ?>

</form>

<?php endif; // If registration required and not logged in ?>

<?php else : // Comments are closed ?>
<p><?php _e('Sorry, the comment form is closed at this time.'); ?></p>
<?php endif; ?>
</div>
