<?php
/*
 * Plugin Name: Featured Image Sharpen Up
 * Version: 1.0
 * Plugin URI: https://mabujo.com/
 * Description: This plugin helps increase the page loading speed of your site by replacing featured post images with a small inline image, and lazy loading the full image.
 * Author: mabujo
 * Author URI: https://mabujo.com
 * Requires at least: 4.0
 * Tested up to: 4.4
 *
 * Text Domain: featured-image-sharpen-up
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author mabujo
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-featured-image-sharpen-up.php' );

/**
 * Returns the main instance of Featured_Image_Sharpen_Up to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Featured_Image_Sharpen_Up
 */
function Featured_Image_Sharpen_Up () {
	$instance = Featured_Image_Sharpen_Up::instance( __FILE__, '1.0.0' );

	return $instance;
}

Featured_Image_Sharpen_Up();