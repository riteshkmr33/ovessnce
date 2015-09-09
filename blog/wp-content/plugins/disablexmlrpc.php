<?php
/**
* Plugin Name: Disable XMLRPC
* Version: 0.1
* Plugin URI: http://wpengineer.com/?p=2484
* Description: Disable XMLRPC server for WP >= v3.5
* Author: Lutz Schröer
* Author URI: http://elektroelch.net/
*/

function remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'remove_x_pingback');
add_filter('xmlrpc_enabled', '__return_false');

?>