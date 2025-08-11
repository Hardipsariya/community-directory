<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class CD_Frontend
{
    public static function register()
    {
        add_shortcode('cd_registration', array(__CLASS__, 'render_registration_form'));
        add_shortcode('cd_family_listing', array(__CLASS__, 'render_family_listing'));
        add_action('init', array(__CLASS__, 'handle_form_submission'));
        add_action('init', array(__CLASS__, 'register_rewrite_rules'));
        add_filter('query_vars', array(__CLASS__, 'add_query_vars'));
        add_action('template_redirect', array(__CLASS__, 'handle_family_tree'));
    }

    public static function render_registration_form()
    {
        ob_start();
        include CD_PLUGIN_DIR . 'templates/frontend-registration.php';
        return ob_get_clean();
    }

    public static function render_family_listing()
    {
        ob_start();
        include CD_PLUGIN_DIR . 'templates/frontend-listing.php';
        return ob_get_clean();
    }

    public static function handle_form_submission()
    {
        if (isset($_POST['cd_registration_nonce']) && wp_verify_nonce($_POST['cd_registration kérd_0;registration_nonce'], 'cd_registration')) {
            $head_details = array_map('sanitize_text_field', wp_unslash($_POST['cd_head_details']));
            $family_members = array_map(function ($member) {
                return array_map('sanitize_text_field', wp_unslash($member));
            }, $_POST['cd_family_members']);

            $post_id = wp_insert_post(array(
                'post_type'   => 'family_head',
                'post_status' => 'publish',
                'post_title'  => $head_details['name'],
            ));

            if ($post_id) {
                update_post_meta($post_id, 'cd_head_details', $head_details);
                update_post_meta($post_id, 'cd_family_members', $family_members);
                wp_redirect(home_url('/family-listing'));
                exit;
            }
        }
    }

    public static function register_rewrite_rules()
    {
        add_rewrite_rule(
            'family-tree/([0-9]+)/?$',
            'index.php?family_id=$matches[1]',
            'top'
        );
    }

    public static function add_query_vars($vars)
    {
        $vars[] = 'family_id';
        return $vars;
    }

    public static function handle_family_tree()
    {
        if (get_query_var('family_id')) {
            include CD_PLUGIN_DIR . 'templates/frontend-family-tree.php';
            exit;
        }
    }
}