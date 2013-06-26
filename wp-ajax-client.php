<?php
/**
 * WordPress AJAX Process Execution.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Executing AJAX process.
 *
 * @since 2.1.0
 */
define('DOING_AJAX', true);

if ( ! isset( $_REQUEST['action'] ) )
	die('-1');

require_once('../../../wp-load.php');

require_once('../../../wp-admin/includes/admin.php');
@header('Content-Type: text/html; charset=' . get_option('blog_charset'));
send_nosniff_header();

do_action('init');

/**
 * Sends back current comment total and new page links if they need to be updated.
 *
 * Contrary to normal success AJAX response ("1"), die with time() on success.
 *
 * @since 2.7
 *
 * @param int $comment_id
 * @return die
 */

switch ( $action = $_POST['action'] ) :
default :
	do_action( 'wp_ajax_' . $_POST['action'] );
	die('0');
	break;
endswitch;
?>
