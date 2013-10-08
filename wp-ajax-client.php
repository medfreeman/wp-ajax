<?php
/**
 * WordPress AJAX Process Execution.
 *
 */

define('DOING_AJAX', true);

if ( ! isset( $_REQUEST['action'] ) || ! isset( $_POST['action'] ) || $_POST['action'] != 'wp-ajax-submit-url' )
	die('-1');

require_once('../../../wp-load.php');

require_once('../../../wp-admin/includes/admin.php');

@header('Content-Type: text/html; charset=' . get_option('blog_charset'));
send_nosniff_header();

do_action( 'wp_ajax_wp-ajax-submit-url' );
die('0');
break;
?>
