<?php
/*
Plugin Name: DS-Kalation
Description: Super geil
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function dsk_install() {
    // TODO
}

function dsk_deactivation() {
    // TODO
}

register_activation_hook(__FILE__, 'dsk_install');
register_deactivation_hook(__FILE__, 'dsk_deactivation');
function prevent_google_maps_resize() {
    echo '<meta name="viewport" content="initial-scale=1.1, user-scalable=no" />';
}
add_action( 'wp_head', 'prevent_google_maps_resize' );

add_action('admin_menu', 'dsk_add_menu');
function dsk_add_menu() {
    add_menu_page(
        'DS Kalation',
        'Ds Kalation',
        'read',
        'ds-kalation/admin-page.php',
        '',
        'https://www.die-staemme.de/favicon.ico'
    );
}
?>
