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

        // Display success message if form was submitted
        if (get_transient('cd_form_success')) {
            $message = get_transient('cd_form_success');
            delete_transient('cd_form_success');
            echo '<div class="bg-green-100 mb-4 px-4 py-3 border border-green-400 rounded text-green-700" role="alert">';
            echo esc_html($message);
            echo '</div>';
        }

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
        if (isset($_POST['cd_registration_nonce']) && wp_verify_nonce($_POST['cd_registration_nonce'], 'cd_registration')) {
            $head_details = array_map('sanitize_text_field', wp_unslash($_POST['cd_head_details']));
            $family_members = isset($_POST['cd_family_members']) ? array_map(function ($member) {
                return array_map('sanitize_text_field', wp_unslash($member));
            }, $_POST['cd_family_members']) : array();



            // Handle profile picture upload.
            if (! empty($_FILES['cd_head_details']['name']['profile_picture'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $attachment_id = media_handle_upload('cd_head_details[profile_picture]', 0);
                if (! is_wp_error($attachment_id)) {
                    $head_details['profile_picture_id'] = $attachment_id;
                }
            }

            // Handle family member photo uploads.
            foreach ($_FILES['cd_family_members']['name'] as $index => $data) {
                if (! empty($data['photo'])) {
                    $file = array(
                        'name'     => $_FILES['cd_family_members']['name'][$index]['photo'],
                        'type'     => $_FILES['cd_family_members']['type'][$index]['photo'],
                        'tmp_name' => $_FILES['cd_family_members']['tmp_name'][$index]['photo'],
                        'error'    => $_FILES['cd_family_members']['error'][$index]['photo'],
                        'size'     => $_FILES['cd_family_members']['size'][$index]['photo'],
                    );
                    $attachment_id = media_handle_upload('cd_family_members[' . $index . '][photo]', 0);
                    if (! is_wp_error($attachment_id)) {
                        $family_members[$index]['photo_id'] = $attachment_id;
                    }
                }
            }

            $post_id = wp_insert_post(array(
                'post_type'   => 'family_head',
                'post_status' => 'pending',
                'post_title'  => $head_details['name'],
            ));

            if ($post_id) {
                update_post_meta($post_id, 'cd_head_details', $head_details);
                update_post_meta($post_id, 'cd_family_members', $family_members);
                update_post_meta($post_id, 'cd_approved', '0'); // 0 = pending, 1 = approved
                set_transient('cd_form_success', __('Family registration submitted successfully! It will appear after admin approval.', CD_TEXT_DOMAIN), 30);
                wp_redirect(home_url('/family-registration'));
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
