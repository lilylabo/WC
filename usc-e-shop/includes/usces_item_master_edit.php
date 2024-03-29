<?php
/**
 * Edit post administration panel.
 *
 * Manage Post actions: post, edit, delete, etc.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
//require_once('admin.php');
/* 30 **************************************/
global $wp_version;
/***************************************/

//$parent_file = 'admin.php?page=usces_itemedit';
//$submenu_file = 'admin.php?page=usces_itemedit';
wp_reset_vars(array('action', 'safe_mode', 'withcomments', 'posts', 'content', 'edited_post_title', 'comment_error', 'profile', 'trackback_url', 'excerpt', 'showcomments', 'commentstart', 'commentend', 'commentorder'));

/**
 * Redirect to previous page.
 *
 * @param int $post_ID Optional. Post ID.
 */
function redirect_post($post_ID = '') {
	global $action;

	$referredby = '';
	if ( !empty($_POST['referredby']) ) {
		$referredby = preg_replace('|https?://[^/]+|i', '', $_POST['referredby']);
		$referredby = remove_query_arg('_wp_original_http_referer', $referredby);
	}
	$referer = preg_replace('|https?://[^/]+|i', '', wp_get_referer());

	if ( !empty($_POST['mode']) && 'bookmarklet' == $_POST['mode'] ) {
		$location = $_POST['referredby'];
	} elseif ( !empty($_POST['mode']) && 'sidebar' == $_POST['mode'] ) {
		if ( isset($_POST['saveasdraft']) )
			$location = 'sidebar.php?a=c';
		elseif ( isset($_POST['publish']) )
			$location = 'sidebar.php?a=b';
	} elseif ( ( isset($_POST['save']) || isset($_POST['publish']) ) && ( empty($referredby) || $referredby == $referer || 'redo' != $referredby ) ) {
		if ( isset($_POST['_wp_original_http_referer']) && strpos( $_POST['_wp_original_http_referer'], '/wp-admin/post.php') === false && strpos( $_POST['_wp_original_http_referer'], '/wp-admin/post-new.php') === false ) {
			$location = add_query_arg( array(
				'_wp_original_http_referer' => urlencode( stripslashes( $_POST['_wp_original_http_referer'] ) ),
				'message' => 1
			), usces_link_replace( get_edit_post_link( $post_ID, 'url' ) ) );
		} else {
			if ( isset( $_POST['publish'] ) ) {
				if ( 'pending' == get_post_status( $post_ID ) )
					$location = add_query_arg( 'message', 8, usces_link_replace( get_edit_post_link( $post_ID, 'url' ) ) );
				else
					$location = add_query_arg( 'message', 6, usces_link_replace( get_edit_post_link( $post_ID, 'url' ) ) );
			} else {
				$location = add_query_arg( 'message', 7, usces_link_replace( get_edit_post_link( $post_ID, 'url' ) ) );
			}
			//$dd=usces_link_replace( get_edit_post_link( $post_ID, 'url' ) );
//	var_dump($referer);echo "<br>\n";
//	var_dump($dd);echo "<br>\n";
//	var_dump($location);echo "\n";
//	exit;
		}
	} elseif (isset($_POST['addmeta']) && $_POST['addmeta']) {
		$location = add_query_arg( 'message', 2, wp_get_referer() );
		$location = explode('#', $location);
		$location = $location[0] . '#postcustom';
	} elseif (isset($_POST['deletemeta']) && $_POST['deletemeta']) {
		$location = add_query_arg( 'message', 3, wp_get_referer() );
		$location = explode('#', $location);
		$location = $location[0] . '#postcustom';
	} elseif (!empty($referredby) && $referredby != $referer) {
		$location = $_POST['referredby'];
		$location = remove_query_arg('_wp_original_http_referer', $location);
		if ( false !== strpos($location, 'edit.php') || false !== strpos($location, 'edit-post-drafts.php') )
			$location = add_query_arg('posted', $post_ID, $location);
		elseif ( false !== strpos($location, 'wp-admin') )
			$location = "post-new.php?posted=$post_ID";
	} elseif ( isset($_POST['publish']) ) {
		$location = "post-new.php?posted=$post_ID";
	} elseif ($action == 'editattachment') {
		$location = 'attachments.php';
	} elseif ( 'post-quickpress-save-cont' == $_POST['action'] ) {
		$location = "post.php?action=edit&post=$post_ID&message=7";
	} else {
		$location = add_query_arg( 'message', 4, usces_link_replace( get_edit_post_link( $post_ID, 'url' ) ) );
	}
	//var_dump($location);echo "<br>\n";
	wp_redirect( $location );
}

function usces_get_message($post_ID) {

	if ( ( isset($_POST['save']) || isset($_POST['publish']) ) ) {
		if ( isset($_POST['_wp_original_http_referer']) && strpos( $_POST['_wp_original_http_referer'], '/wp-admin/post.php') === false && strpos( $_POST['_wp_original_http_referer'], '/wp-admin/post-new.php') === false ) {
			$this->action_message = sprintf(__('Post updated. <a href="%s">View post</a>'), get_permalink($post_ID));
		} else {
			if ( isset( $_POST['publish'] ) ) {
				if ( 'pending' == get_post_status( $post_ID ) )
					$this->action_message = sprintf(__('Post submitted. <a href="%s">Preview post</a>'), add_query_arg( 'preview', 'true', get_permalink($post_ID) ) );
				else
					$this->action_message = sprintf(__('Post published. <a href="%s">View post</a>'), get_permalink($post_ID));
			} else {
				$this->action_message = __('Post saved.');
			}
		}
	} elseif (isset($_POST['addmeta']) && $_POST['addmeta']) {
		$this->action_message = __('Custom field updated.');
	} elseif (isset($_POST['deletemeta']) && $_POST['deletemeta']) {
		$this->action_message = __('Custom field deleted.');
	} elseif ( 'post-quickpress-save-cont' == $_POST['action'] ) {
		$this->action_message = __('Post saved.');
	} else {
		$this->action_message = __('Post updated.');
	}
	
	$this->action_status = 'none';
}

if ( isset( $_POST['deletepost'] ) )
	$action = 'delete';
//elseif ( isset($_POST['wp-preview']) && 'dopreview' == $_POST['wp-preview'] )
//	$action = 'preview';

switch($action) {
//case 'postajaxpost':
//case 'post-quickpress-publish':
//case 'post-quickpress-save':
//	check_admin_referer('add-post');
//
//	if ( 'post-quickpress-publish' == $action )
//		$_POST['publish'] = 'publish'; // tell write_post() to publish
//
//	if ( 'post-quickpress-publish' == $action || 'post-quickpress-save' == $action ) {
//		$_POST['comment_status'] = get_option('default_comment_status');
//		$_POST['ping_status'] = get_option('default_ping_status');
//	}
//
//	if ( !empty( $_POST['quickpress_post_ID'] ) ) {
//		$_POST['post_ID'] = (int) $_POST['quickpress_post_ID'];
//		$post_ID = edit_post();
//	} else {
//		$post_ID = 'postajaxpost' == $action ? item_option_edit_post() : write_post();
//	}
//
//	if ( 0 === strpos( $action, 'post-quickpress' ) ) {
//		$_POST['post_ID'] = $post_ID;
//		// output the quickpress dashboard widget
//		require_once(ABSPATH . 'wp-admin/includes/dashboard.php');
//		wp_dashboard_quick_press();
//		exit;
//	}
//
//	redirect_post($post_ID);
//	exit();
//	break;

case 'post':
case 'edit':
	$editing = true;

	if ( empty( $_GET['post'] ) ) {
		wp_redirect("post.php");
		exit();
	}

	$title = 'Welcart Shop '.__('Edit item', 'usces');

	$this->action_status = 'none';

	if ( version_compare($wp_version, '3.0-beta', '>') ){
		global $post;
		if ( $post ) {
			$post_type_object = get_post_type_object($post->post_type);
			if ( $post_type_object ) {
				$post_type = $post->post_type;
				$current_screen->post_type = $post->post_type;
				$current_screen->id = $current_screen->post_type;
			}
			$post_id = $post->ID;
			$post_ID = $post->ID;
		}
	
		$p = $post_id;
	
		if ( empty($post->ID) )
			wp_die( __('You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?') );
	
		if ( !current_user_can($post_type_object->cap->edit_post, $post_id) )
			wp_die( __('You are not allowed to edit this item.') );
	
		if ( 'trash' == $post->post_status )
			wp_die( __('You can&#8217;t edit this item because it is in the Trash. Please restore it and try again.') );
	
		if ( null == $post_type_object )
			wp_die( __('Unknown post type.') );
	
		$post_type = $post->post_type;
		//$title = sprintf(__('Edit %s'), $post_type_object->singular_label);
		if ( version_compare($wp_version, '3.4-beta', '>') ){
			include(USCES_PLUGIN_DIR."/includes/edit-form-advanced34.php");
		}else{
			include(USCES_PLUGIN_DIR."/includes/edit-form-advanced30.php");
		}
		
	}else{
	
		$post_ID = $p = (int) $_GET['post'];
		$post = get_post($post_ID);
	
		if ( empty($post->ID) ) wp_die( __("You attempted to edit a post that doesn't exist. Perhaps it was deleted?") );
	
		if ( 'post' != $post->post_type ) {
			wp_redirect( get_edit_post_link( $post->ID, 'url' ) );
			exit();
		}
		if ( !current_user_can('edit_post', $post_ID) )
			die ( __('You are not allowed to edit this post.') );
	
		$post = get_post_to_edit($post_ID);
		include(USCES_PLUGIN_DIR."/includes/edit-form-advanced.php");
	}

	break;

//case 'editattachment':
//	$post_id = (int) $_POST['post_ID'];
//
//	check_admin_referer('update-attachment_' . $post_id);
//
//	// Don't let these be changed
//	unset($_POST['guid']);
//	$_POST['post_type'] = 'attachment';
//
//	// Update the thumbnail filename
//	$newmeta = wp_get_attachment_metadata( $post_id, true );
//	$newmeta['thumb'] = $_POST['thumb'];
//
//	wp_update_attachment_metadata( $post_id, $newmeta );
//
case 'editpost':

		global $post;
		$title = 'Welcart Shop '.__('Edit item', 'usces');
//		if ( !current_user_can('edit_post', $post->ID) )
//			die ( __('You are not allowed to edit this post.') );
	
	if ( version_compare($wp_version, '3.0-beta', '>') ){
		if ( $post ) {
			$post_type_object = get_post_type_object($post->post_type);
			if ( $post_type_object ) {
				$post_type = $post->post_type;
				$current_screen->post_type = $post->post_type;
				$current_screen->id = $current_screen->post_type;
			}
			$post_id = $post->ID;
			$post_ID = $post->ID;
		}
		add_action('check_admin_referer', 'usces_update_check_admin');

		check_admin_referer('update-' . $post_type . '_' . $post_id);
		$post_id = edit_post();
		$post_ID = $post_id;

		if ( version_compare($wp_version, '3.5-beta', '>') ){
			$post = get_post($post_id, OBJECT, 'edit');
		}else{
			$post = get_post_to_edit($post_ID);
		}
	
		//redirect_post($post_id); // Send user on their way while we keep working
		if ( version_compare($wp_version, '3.4-beta', '>') ){
			include(USCES_PLUGIN_DIR."/includes/edit-form-advanced34.php");
		}else{
			include(USCES_PLUGIN_DIR."/includes/edit-form-advanced30.php");
		}
	}else{
	
		$post_ID = edit_post();
	$post = get_post_to_edit($post_ID);
	
		include(USCES_PLUGIN_DIR."/includes/edit-form-advanced.php");
	}
	
	
	if ( ( isset($_POST['save']) || isset($_POST['publish']) ) ) {
		if ( isset($_POST['_wp_original_http_referer']) && strpos( $_POST['_wp_original_http_referer'], '/wp-admin/post.php') === false && strpos( $_POST['_wp_original_http_referer'], '/wp-admin/post-new.php') === false ) {
			$this->action_message = sprintf(__('Post updated. <a href="%s">View post</a>'), get_permalink($post_ID));
		} else {
			if ( isset( $_POST['publish'] ) ) {
				if ( 'pending' == get_post_status( $post_ID ) )
					$this->action_message = sprintf(__('Post submitted. <a href="%s">Preview post</a>'), add_query_arg( 'preview', 'true', get_permalink($post_ID) ) );
				else
					$this->action_message = sprintf(__('Post published. <a href="%s">View post</a>'), get_permalink($post_ID));
			} else {
				$this->action_message = __('Post saved.');
			}
		}
	} elseif (isset($_POST['addmeta']) && $_POST['addmeta']) {
		$this->action_message = __('Custom field updated.');
	} elseif (isset($_POST['deletemeta']) && $_POST['deletemeta']) {
		$this->action_message = __('Custom field deleted.');
	} elseif ( 'post-quickpress-save-cont' == $_POST['action'] ) {
		$this->action_message = __('Post saved.');
	} else {
		$this->action_message = __('Post updated.');
	}
	$this->action_status = 'success';
//	redirect_post($post_ID); // Send user on their way while we keep working
//	exit();
	break;

case 'new':

	$title = 'Welcart Shop ' . __('Add New Item', 'usces');
	global $post;

	if ( version_compare($wp_version, '3.0-beta', '>') ){
		global $post_ID, $current_screen;
		if ( !isset($_GET['post_type']) )
			$post_type = 'post';
		elseif ( in_array( $_GET['post_type'], get_post_types( array('public' => true ) ) ) )
			$post_type = $_GET['post_type'];
		else
			wp_die( __('Invalid post type') );
		
		$action = 'post';
		$post = get_default_post_to_edit( $post_type, true );
		$post_ID = $post->ID;

		if ( version_compare($wp_version, '3.4-beta', '>') ){
			include(USCES_PLUGIN_DIR."/includes/edit-form-advanced34.php");
		}else{
			include(USCES_PLUGIN_DIR."/includes/edit-form-advanced30.php");
		}
	}else{
		if ( current_user_can('edit_pages') ) {
			$action = 'post';
			$post = get_default_post_to_edit();
			include(USCES_PLUGIN_DIR."/includes/edit-form-advanced.php");
		}
	}


	break;

case 'delete':
	$post_id = (isset($_GET['post']))  ? intval($_GET['post']) : intval($_POST['post_ID']);
	check_admin_referer('delete-post_' . $post_id);

	$post = & get_post($post_id);

	if ( !current_user_can('delete_post', $post_id) )
		wp_die( __('You are not allowed to delete this post.') );

	if ( $post->post_type == 'attachment' ) {
		if ( ! wp_delete_attachment($post_id) )
			wp_die( __('Error in deleting...') );
	} else {
		if ( !wp_delete_post($post_id) )
			wp_die( __('Error in deleting...') );
	}

	$sendback = wp_get_referer();
	if (strpos($sendback, 'admin.php') !== false) $sendback = admin_url('admin.php?page=usces_itemedit&deleted=1');
	elseif (strpos($sendback, 'attachments.php') !== false) $sendback = admin_url('attachments.php');
	//else $sendback = add_query_arg('deleted', 1, $sendback);
	else $sendback = admin_url('admin.php?page=usces_itemedit&deleted=1');
	//wp_redirect($sendback);
	exit();
	break;

//case 'preview':
//	check_admin_referer( 'autosave', 'autosavenonce' );
//
//	$url = post_preview();
//var_dump($url);
////	wp_redirect($url);
//	exit();
//	break;

default:
	//wp_redirect('admin.php?page=usces_itemedit');
	exit();
	break;
} // end switch
//include('admin-footer.php');
?>
