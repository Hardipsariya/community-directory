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
        // Debug: Log that function was called
        error_log('CD Form Submission - Function called');
        error_log('CD Form Submission - POST data: ' . print_r($_POST, true));
        error_log('CD Form Submission - FILES data: ' . print_r($_FILES, true));

        if (isset($_POST['cd_registration_nonce']) && wp_verify_nonce($_POST['cd_registration_nonce'], 'cd_registration')) {
            error_log('CD Form Submission - Nonce verified successfully');

            $head_details = array_map('sanitize_text_field', wp_unslash($_POST['cd_head_details']));
            $family_members = isset($_POST['cd_family_members']) ? array_map(function ($member) {
                return array_map('sanitize_text_field', wp_unslash($member));
            }, $_POST['cd_family_members']) : array();

            // Debug: Log form submission
            error_log('CD Form Submission - Files array: ' . print_r($_FILES, true));
            error_log('CD Form Submission - Business brochure file: ' . (isset($_FILES['cd_head_details']['name']['business_brochure']) ? $_FILES['cd_head_details']['name']['business_brochure'] : 'NOT SET'));

            // Handle profile picture upload.
            if (! empty($_FILES['cd_head_details']['name']['profile_picture'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $file = array(
                    'name'     => $_FILES['cd_head_details']['name']['profile_picture'],
                    'type'     => $_FILES['cd_head_details']['type']['profile_picture'],
                    'tmp_name' => $_FILES['cd_head_details']['tmp_name']['profile_picture'],
                    'error'    => $_FILES['cd_head_details']['error']['profile_picture'],
                    'size'     => $_FILES['cd_head_details']['size']['profile_picture'],
                );

                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($file, $upload_overrides);

                if ($movefile && ! isset($movefile['error'])) {
                    $attachment = array(
                        'guid'           => $movefile['url'],
                        'post_mime_type' => $movefile['type'],
                        'post_title'     => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );

                    $attachment_id = wp_insert_attachment($attachment, $movefile['file'], 0);

                    if (! is_wp_error($attachment_id)) {
                        require_once ABSPATH . 'wp-admin/includes/image.php';
                        $attachment_data = wp_generate_attachment_metadata($attachment_id, $movefile['file']);
                        wp_update_attachment_metadata($attachment_id, $attachment_data);
                        $head_details['profile_picture_id'] = $attachment_id;
                    }
                }
            }

            // Handle business brochure upload
            if (! empty($_FILES['cd_head_details']['name']['business_brochure'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $file = array(
                    'name'     => $_FILES['cd_head_details']['name']['business_brochure'],
                    'type'     => $_FILES['cd_head_details']['type']['business_brochure'],
                    'tmp_name' => $_FILES['cd_head_details']['tmp_name']['business_brochure'],
                    'error'    => $_FILES['cd_head_details']['error']['business_brochure'],
                    'size'     => $_FILES['cd_head_details']['size']['business_brochure'],
                );

                // Check for upload errors first
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $error_message = __('Error uploading Business Brochure: Upload failed.', CD_TEXT_DOMAIN);
                    set_transient('cd_form_success', $error_message, 30);
                    wp_redirect(home_url('/family-registration'));
                    exit;
                }

                // Validate file type (PDF only)
                $allowed_types = array('application/pdf');
                if (! in_array($file['type'], $allowed_types)) {
                    $error_message = __('Error: Only PDF files are allowed for Business Brochure.', CD_TEXT_DOMAIN);
                    set_transient('cd_form_success', $error_message, 30);
                    wp_redirect(home_url('/family-registration'));
                    exit;
                }

                // Validate file size (5MB limit)
                if ($file['size'] > 5 * 1024 * 1024) {
                    $error_message = __('Error: Business Brochure file size must be below 5 MB.', CD_TEXT_DOMAIN);
                    set_transient('cd_form_success', $error_message, 30);
                    wp_redirect(home_url('/family-registration'));
                    exit;
                }

                // Validate file is not empty
                if ($file['size'] === 0) {
                    $error_message = __('Error: Business Brochure file is empty.', CD_TEXT_DOMAIN);
                    set_transient('cd_form_success', $error_message, 30);
                    wp_redirect(home_url('/family-registration'));
                    exit;
                }

                $upload_overrides = array('test_form' => false);
                $movefile = wp_handle_upload($file, $upload_overrides);

                if ($movefile && ! isset($movefile['error'])) {
                    $attachment = array(
                        'guid'           => $movefile['url'],
                        'post_mime_type' => $movefile['type'],
                        'post_title'     => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );

                    $attachment_id = wp_insert_attachment($attachment, $movefile['file'], 0);

                    if (! is_wp_error($attachment_id)) {
                        require_once ABSPATH . 'wp-admin/includes/image.php';
                        $attachment_data = wp_generate_attachment_metadata($attachment_id, $movefile['file']);
                        wp_update_attachment_metadata($attachment_id, $attachment_data);

                        // Ensure the brochure ID is saved
                        $head_details['business_brochure_id'] = $attachment_id;

                        // Debug: Log successful upload
                        error_log('Business Brochure uploaded successfully. Attachment ID: ' . $attachment_id);
                    } else {
                        $error_message = __('Error: Failed to create attachment for Business Brochure.', CD_TEXT_DOMAIN);
                        set_transient('cd_form_success', $error_message, 30);
                        wp_redirect(home_url('/family-registration'));
                        exit;
                    }
                } else {
                    // Handle upload error
                    $error_message = __('Error uploading Business Brochure: ', CD_TEXT_DOMAIN) . (isset($movefile['error']) ? $movefile['error'] : 'Unknown error');
                    set_transient('cd_form_success', $error_message, 30);
                    wp_redirect(home_url('/family-registration'));
                    exit;
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

            $auto_publish = get_option('cd_auto_publish', '0');
            $post_status = ($auto_publish === '1') ? 'publish' : 'pending';
            $approved_status = ($auto_publish === '1') ? '1' : '0';

            $post_id = wp_insert_post(array(
                'post_type'   => 'family_head',
                'post_status' => $post_status,
                'post_title'  => $head_details['name'],
            ));

            if ($post_id) {
                update_post_meta($post_id, 'cd_head_details', $head_details);
                update_post_meta($post_id, 'cd_family_members', $family_members);
                update_post_meta($post_id, 'cd_approved', $approved_status); // 0 = pending, 1 = approved

                if ($auto_publish === '1') {
                    set_transient('cd_form_success', __('Family registration submitted successfully !', CD_TEXT_DOMAIN), 30);
                } else {
                    set_transient('cd_form_success', __('Family registration submitted successfully ! It will appear after admin approval.', CD_TEXT_DOMAIN), 30);
                }

                wp_redirect(home_url('/family-registration'));
                exit;
            }
        } else {
            error_log('CD Form Submission - Nonce verification failed or not set');
            error_log('CD Form Submission - cd_registration_nonce: ' . (isset($_POST['cd_registration_nonce']) ? $_POST['cd_registration_nonce'] : 'NOT SET'));
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
