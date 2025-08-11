<?php

/**
 * Plugin Name: Community Directory
 * Description: A WordPress plugin for caste/family-based registration and display with frontend and backend functionality.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL-2.0+
 * Text Domain: community-directory
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define plugin constants.
define('CD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CD_TEXT_DOMAIN', 'community-directory');

// Include necessary files with error logging.
$required_files = array(
    'includes/class-cpt.php',
    'includes/class-ajax.php',
    'includes/class-admin.php',
    'includes/class-frontend.php',
    'includes/class-family-tree.php',
);
foreach ($required_files as $file) {
    if (file_exists(CD_PLUGIN_DIR . $file)) {
        require_once CD_PLUGIN_DIR . $file;
    } else {
        error_log('Community Directory: Missing file ' . CD_PLUGIN_DIR . $file);
    }
}

// Check if WordPress translation functions are available.
if (! function_exists('_e')) {
    error_log('Community Directory: WordPress translation function _e is not defined.');
}

// Initialize plugin components.
function cd_init()
{
    if (class_exists('CD_CPT')) {
        CD_CPT::register();
    } else {
        error_log('Community Directory: CD_CPT class not found.');
    }
    if (class_exists('CD_AJAX')) {
        CD_AJAX::register();
    } else {
        error_log('Community Directory: CD_AJAX class not found.');
    }
    if (class_exists('CD_Admin')) {
        CD_Admin::register();
    } else {
        error_log('Community Directory: CD_Admin class not found.');
    }
    if (class_exists('CD_Frontend')) {
        CD_Frontend::register();
    } else {
        error_log('Community Directory: CD_Frontend class not found.');
    }
    if (class_exists('CD_Family_Tree')) {
        CD_Family_Tree::register();
    } else {
        error_log('Community Directory: CD_Family_Tree class not found.');
    }
}
add_action('plugins_loaded', 'cd_init');

// Activation hook: Create database tables.
register_activation_hook(__FILE__, array('CD_CPT', 'activate'));

// Deactivation hook: Clean up database.
register_deactivation_hook(__FILE__, array('CD_CPT', 'deactivate'));

// Enqueue scripts and styles.
function cd_enqueue_scripts()
{
    // Enqueue Tailwind CSS via CDN for responsive styling.
    wp_enqueue_style('cd-tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19');

    // Enqueue Treant.js and Raphael for family tree.
    wp_enqueue_style('cd-treant', 'https://cdn.jsdelivr.net/npm/treant-js@1.0/Treant.css', array(), '1.0');
    wp_enqueue_script('cd-raphael', 'https://cdn.jsdelivr.net/npm/raphael@2.3.0/raphael.min.js', array(), '2.3.0', true);
    wp_enqueue_script('cd-treant', 'https://cdn.jsdelivr.net/npm/treant-js@1.0/Treant.js', array('cd-raphael'), '1.0', true);

    // Enqueue jQuery validation for form.
    wp_enqueue_script('cd-jquery-validate', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', array('jquery'), '1.19.5', true);

    // Enqueue custom scripts and styles.
    wp_enqueue_style('cd-styles', CD_PLUGIN_URL . 'assets/css/styles.css', array(), '1.0.0');
    wp_enqueue_script('cd-frontend', CD_PLUGIN_URL . 'assets/js/frontend.js', array('jquery', 'cd-jquery-validate'), '1.0.0', true);
    wp_enqueue_script('cd-family-tree', CD_PLUGIN_URL . 'assets/js/family-tree.js', array('cd-treant'), '1.0.0', true);

    // Localize script for AJAX.
    wp_localize_script('cd-frontend', 'cd_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cd_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'cd_enqueue_scripts');