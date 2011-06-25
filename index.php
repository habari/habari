<?php
/**
 * Habari Index
 *
 * In this file, we just set the root dir and include system/system.php
 *
 * @package Habari
 */

/**
 * Define the constant HABARI_PATH.
 * The path to the root of this Habari installation.
 */
if ( !defined( 'HABARI_PATH' ) ) {
	define( 'HABARI_PATH', dirname( __FILE__ ) );
}

/**
 * Require system/index.php, where the magic happens
 */
require( HABARI_PATH . '/system/index.php' );

?>