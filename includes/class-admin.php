<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Fallback for _e if not defined
if (! function_exists('_e')) {
    function _e($text, $domain)
    {
        echo esc_html($text);
        error_log('Community Directory: _e function not defined, using fallback in class-admin.php');
    }
}

class CD_Admin
{
    public static function register()
    {
        add_action('admin_menu', array(__CLASS__, 'add_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
        add_action('wp_ajax_cd_toggle_approval', array(__CLASS__, 'handle_toggle_approval'));
        add_action('wp_ajax_cd_toggle_auto_publish', array(__CLASS__, 'handle_toggle_auto_publish'));
        add_action('admin_init', array(__CLASS__, 'handle_admin_form_submission'));
    }

    public static function add_menu()
    {
        add_menu_page(
            __('Community Directory', CD_TEXT_DOMAIN),
            __('Community Directory', CD_TEXT_DOMAIN),
            'manage_options',
            'cd_directory',
            array(__CLASS__, 'render_family_list'),
            'dashicons-groups',
            30
        );
        $hook1 = add_submenu_page(
            'cd_directory',
            __('Family List', CD_TEXT_DOMAIN),
            __('Family List', CD_TEXT_DOMAIN),
            'manage_options',
            'cd_directory',
            array(__CLASS__, 'render_family_list')
        );
        $hook2 = add_submenu_page(
            'cd_directory',
            __('Add New Family', CD_TEXT_DOMAIN),
            __('Add New Family', CD_TEXT_DOMAIN),
            'manage_options',
            'cd_add_family',
            array(__CLASS__, 'render_add_family_form')
        );
        $hook3 = add_submenu_page(
            'cd_directory',
            __('Plugin Settings', CD_TEXT_DOMAIN),
            __('Settings', CD_TEXT_DOMAIN),
            'manage_options',
            'cd_settings',
            array(__CLASS__, 'render_settings')
        );

    }

    public static function enqueue_admin_scripts()
    {
        wp_enqueue_style('cd-admin', CD_PLUGIN_URL . 'assets/css/styles.css', array(), '1.0.0');
        wp_enqueue_script('cd-admin', CD_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('jquery-validate', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', array('jquery'), '1.19.5', true);

        // Localize script for AJAX
        wp_localize_script('cd-admin', 'cd_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('cd_nonce')
        ));
    }

    public static function render_family_list()
    {
        $search = sanitize_text_field($_GET['s'] ?? '');
        $args = array(
            'post_type'      => 'family_head',
            'posts_per_page' => -1,
            's'              => $search,
        );
        $query = new WP_Query($args);
?>
        <div class="wrap">
            <h1><?php _e('Family List', CD_TEXT_DOMAIN); ?></h1>
            <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Family added successfully!', CD_TEXT_DOMAIN); ?></p>
                </div>
            <?php endif; ?>
            <form method="get">
                <input type="hidden" name="page" value="cd_directory">
                <input type="text" name="s" value="<?php echo esc_attr($search); ?>"
                    placeholder="<?php _e('Search by name', CD_TEXT_DOMAIN); ?>" class="regular-text">
                <button type="submit" class="button"><?php _e('Search', CD_TEXT_DOMAIN); ?></button>
            </form>
            <table class="wp-list-table fixed widefat striped">
                <thead>
                    <tr>
                        <th><?php _e('Name', CD_TEXT_DOMAIN); ?></th>
                        <th><?php _e('City', CD_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Education', CD_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Occupation', CD_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Status', CD_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Actions', CD_TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php
                        $head_details = get_post_meta(get_the_ID(), 'cd_head_details', true);
                        $approved = get_post_meta(get_the_ID(), 'cd_approved', true);
                        ?>
                        <tr>
                            <td><?php echo esc_html($head_details['name'] ?? ''); ?></td>
                            <td><?php echo esc_html($head_details['city'] ?? ''); ?></td>
                            <td><?php echo esc_html($head_details['education'] ?? ''); ?></td>
                            <td><?php echo esc_html($head_details['occupation_type'] ?? ''); ?></td>
                            <td>
                                <label class="cd-toggle">
                                    <input type="checkbox" class="cd-approval-toggle"
                                        data-post-id="<?php echo get_the_ID(); ?>"
                                        <?php checked($approved, '1'); ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="cd-status-text"><?php echo $approved === '1' ? 'Approved' : 'Pending'; ?></span>
                            </td>
                            <td>
                                <a href="<?php echo admin_url('post.php?post=' . get_the_ID() . '&action=edit'); ?>"
                                    class="button"><?php _e('Edit', CD_TEXT_DOMAIN); ?></a>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=cd_directory&action=delete&post_id=' . get_the_ID()), 'cd_delete_post'); ?>"
                                    class="button"
                                    onclick="return confirm('<?php _e('Are you sure?', CD_TEXT_DOMAIN); ?>');"><?php _e('Delete', CD_TEXT_DOMAIN); ?></a>
                            </td>
                        </tr>
                    <?php endwhile;
                    wp_reset_postdata(); ?>
                </tbody>
            </table>
        </div>
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['post_id']) && wp_verify_nonce($_GET['_wpnonce'], 'cd_delete_post')) {
            wp_delete_post(absint($_GET['post_id']), true);
            wp_redirect(admin_url('admin.php?page=cd_directory'));
            exit;
        }
    }

    public static function render_add_family_form()
    {
        if (!file_exists(CD_PLUGIN_DIR . 'templates/admin-registration.php')) {
            $fallback_content = '<div class="wrap"><h1>Add New Family</h1><p>Error: Template file not found.</p></div>';
        } else {
            ob_start();
            $result = include CD_PLUGIN_DIR . 'templates/admin-registration.php';
            $content = ob_get_clean();

            if (empty($content)) {
                $fallback_content = '<div class="wrap"><h1>Add New Family</h1><p>Form loading...</p></div>';
            } else {
                $fallback_content = $content;
            }
        }

        echo $fallback_content;
    }

    public static function render_settings()
    {
        ?>
        <div class="wrap">
            <h1><?php _e('Plugin Settings', CD_TEXT_DOMAIN); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('cd_settings_group');
                $display_fields = get_option('cd_display_fields', array('name', 'city', 'education', 'occupation_type', 'mobile'));
                $display_mode = get_option('cd_display_mode', 'card');
                $education_options = get_option('cd_education_options', array('High School', 'Bachelor', 'Master', 'PhD'));
                $business_industry_options = get_option('cd_business_industry_options', array('Furniture', 'Tailor'));
                $relation_options = get_option('cd_relation_options', array('Mother', 'Father', 'Son', 'Daughter', 'Wife', 'Husband', 'Brother', 'Sister', 'Grandmother', 'Grandfather', 'Uncle', 'Aunt', 'Nephew', 'Niece'));
                ?>
                <h2><?php _e('Frontend Display Fields', CD_TEXT_DOMAIN); ?></h2>
                <p><label><input type="checkbox" name="cd_display_fields[name]" value="1"
                            <?php checked(in_array('name', $display_fields)); ?>> <?php _e('Name', CD_TEXT_DOMAIN); ?></label>
                </p>
                <p><label><input type="checkbox" name="cd_display_fields[city]" value="1"
                            <?php checked(in_array('city', $display_fields)); ?>> <?php _e('City', CD_TEXT_DOMAIN); ?></label>
                </p>
                <p><label><input type="checkbox" name="cd_display_fields[education]" value="1"
                            <?php checked(in_array('education', $display_fields)); ?> >
                        <?php _e('Education', CD_TEXT_DOMAIN); ?></label></p>
                <p><label><input type="checkbox" name="cd_display_fields[occupation_type]" value="1"
                            <?php checked(in_array('occupation_type', $display_fields)); ?> >
                        <?php _e('Occupation Type', CD_TEXT_DOMAIN); ?></label></p>
                <p><label><input type="checkbox" name="cd_display_fields[mobile]" value="1"
                            <?php checked(in_array('mobile', $display_fields)); ?> >
                        <?php _e('Mobile Number', CD_TEXT_DOMAIN); ?></label></p>
                <p><label><input type="checkbox" name="cd_display_fields[photo]" value="1"
                            <?php checked(in_array('photo', $display_fields)); ?> >
                        <?php _e('Family Photo', CD_TEXT_DOMAIN); ?></label></p>
                <p><label><input type="checkbox" name="cd_display_fields[business_brochure]" value="1"
                            <?php checked(in_array('business_brochure', $display_fields)); ?> >
                        <?php _e('Business Brochure', CD_TEXT_DOMAIN); ?></label></p>

                <h2><?php _e('Education Options', CD_TEXT_DOMAIN); ?></h2>
                <div id="education-options-container">
                    <?php foreach ($education_options as $index => $option) : ?>
                        <div class="education-option mb-2 flex items-center gap-2">
                            <input type="text" name="cd_education_options[]" value="<?php echo esc_attr($option); ?>" class="p-2 border w-full" />
                            <button type="button" class="button remove-education-option"><?php _e('Remove', CD_TEXT_DOMAIN); ?></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-education-option" class="button"><?php _e('Add Education Option', CD_TEXT_DOMAIN); ?></button>

                <h2><?php _e('Business Industry Options', CD_TEXT_DOMAIN); ?></h2>
                <div id="business-industry-options-container">
                    <?php foreach ($business_industry_options as $index => $option) : ?>
                        <div class="business-industry-option mb-2 flex items-center gap-2">
                            <input type="text" name="cd_business_industry_options[]" value="<?php echo esc_attr($option); ?>" class="p-2 border w-full" />
                            <button type="button" class="button remove-business-industry-option"><?php _e('Remove', CD_TEXT_DOMAIN); ?></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-business-industry-option" class="button"><?php _e('Add Business Industry Option', CD_TEXT_DOMAIN); ?></button>

                <h2><?php _e('Relation Options', CD_TEXT_DOMAIN); ?></h2>
                <div id="relation-options-container">
                    <?php foreach ($relation_options as $index => $option) : ?>
                        <div class="relation-option mb-2 flex items-center gap-2">
                            <input type="text" name="cd_relation_options[]" value="<?php echo esc_attr($option); ?>" class="p-2 border w-full" />
                            <button type="button" class="button remove-relation-option"><?php _e('Remove', CD_TEXT_DOMAIN); ?></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-relation-option" class="button"><?php _e('Add Relation Option', CD_TEXT_DOMAIN); ?></button>

                <h2><?php _e('Frontend Display Mode', CD_TEXT_DOMAIN); ?></h2>
                <p><label><input type="radio" name="cd_display_mode" value="card" <?php checked($display_mode, 'card'); ?> >
                        <?php _e('Card View', CD_TEXT_DOMAIN); ?></label></p>
                <p><label><input type="radio" name="cd_display_mode" value="row" <?php checked($display_mode, 'row'); ?> >
                        <?php _e('Row View', CD_TEXT_DOMAIN); ?></label></p>

                <h2><?php _e('Family Registration Approval', CD_TEXT_DOMAIN); ?></h2>
                <p><label><input type="radio" name="cd_auto_publish" value="1" <?php checked(get_option('cd_auto_publish', '0'), '1'); ?>> <?php _e('Auto-Publish', CD_TEXT_DOMAIN); ?></label></p>
                <p><label><input type="radio" name="cd_auto_publish" value="0" <?php checked(get_option('cd_auto_publish', '0'), '0'); ?>> <?php _e('Require Admin Approval', CD_TEXT_DOMAIN); ?></label></p>
                <p class="description"><?php _e('When enabled, new family registrations will be automatically published without requiring admin approval.', CD_TEXT_DOMAIN); ?></p>

                <?php submit_button(); ?>
            </form>
        </div>
<script>
    jQuery(document).ready(function($) {
        $('#add-education-option').on('click', function() {
            var newOption = $('<div class="education-option mb-2 flex items-center gap-2">' +
                '<input type="text" name="cd_education_options[]" class="p-2 border w-full" />' +
                '<button type="button" class="button remove-education-option"><?php _e('Remove', CD_TEXT_DOMAIN); ?></button>' +
                '</div>');
            $('#education-options-container').append(newOption);
        });

        $(document).on('click', '.remove-education-option', function() {
            $(this).closest('.education-option').remove();
        });

        $('#add-business-industry-option').on('click', function() {
            var newOption = $('<div class="business-industry-option mb-2 flex items-center gap-2">' +
                '<input type="text" name="cd_business_industry_options[]" class="p-2 border w-full" />' +
                '<button type="button" class="button remove-business-industry-option"><?php _e('Remove', CD_TEXT_DOMAIN); ?></button>' +
                '</div>');
            $('#business-industry-options-container').append(newOption);
        });

        $(document).on('click', '.remove-business-industry-option', function() {
            $(this).closest('.business-industry-option').remove();
        });

        $('#add-relation-option').on('click', function() {
            var newOption = $('<div class="relation-option mb-2 flex items-center gap-2">' +
                '<input type="text" name="cd_relation_options[]" class="p-2 border w-full" />' +
                '<button type="button" class="button remove-relation-option"><?php _e('Remove', CD_TEXT_DOMAIN); ?></button>' +
                '</div>');
            $('#relation-options-container').append(newOption);
        });

        $(document).on('click', '.remove-relation-option', function() {
            $(this).closest('.relation-option').remove();
        });

        // Handle auto-publish radio buttons
        $('input[name="cd_auto_publish"]').on('change', function() {
            var newValue = $(this).val();

            // Show confirmation alert
            var confirmMessage = newValue === '1' ?
                '<?php _e('Are you sure you want to enable Auto-Publish? All pending family registrations will be automatically approved.', CD_TEXT_DOMAIN); ?>' :
                '<?php _e('Are you sure you want to disable Auto-Publish? New registrations will require admin approval.', CD_TEXT_DOMAIN); ?>';

            if (confirm(confirmMessage)) {
                // Send AJAX request to update setting and auto-approve pending families
                $.ajax({
                    url: cd_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'cd_toggle_auto_publish',
                        nonce: cd_ajax.nonce,
                        auto_publish: newValue
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                        } else {
                            alert('<?php _e('Error updating setting.', CD_TEXT_DOMAIN); ?>');
                            // Revert the radio button if there was an error
                            $('input[name="cd_auto_publish"][value="' + (newValue === '1' ? '0' : '1') + '"]').prop('checked', true);
                        }
                    },
                    error: function() {
                        alert('<?php _e('Error updating setting.', CD_TEXT_DOMAIN); ?>');
                        // Revert the radio button if there was an error
                        $('input[name="cd_auto_publish"][value="' + (newValue === '1' ? '0' : '1') + '"]').prop('checked', true);
                    }
                });
            } else {
                // Revert the radio button if user cancelled
                $('input[name="cd_auto_publish"][value="' + (newValue === '1' ? '0' : '1') + '"]').prop('checked', true);
            }
        });
    });
</script>
<?php
    }

    public static function register_settings()
    {
        register_setting('cd_settings_group', 'cd_display_fields', array(
            'sanitize_callback' => function ($value) {
                return is_array($value) ? array_keys(array_filter($value)) : array();
            }
        ));
        register_setting('cd_settings_group', 'cd_display_mode', array('sanitize_callback' => 'sanitize_text_field'));
        register_setting('cd_settings_group', 'cd_education_options', array(
            'sanitize_callback' => function ($value) {
                if (is_array($value)) {
                    return array_filter(array_map('sanitize_text_field', $value));
                }
                return array();
            }
        ));
        register_setting('cd_settings_group', 'cd_business_industry_options', array(
            'sanitize_callback' => function ($value) {
                if (is_array($value)) {
                    return array_filter(array_map('sanitize_text_field', $value));
                }
                return array();
            }
        ));
        register_setting('cd_settings_group', 'cd_relation_options', array(
            'sanitize_callback' => function ($value) {
                if (is_array($value)) {
                    return array_filter(array_map('sanitize_text_field', $value));
                }
                return array();
            }
        ));
        register_setting('cd_settings_group', 'cd_auto_publish', array('sanitize_callback' => 'sanitize_text_field'));
    }

    public static function handle_toggle_approval()
    {
        check_ajax_referer('cd_nonce', 'nonce');

        $post_id = intval($_POST['post_id']);
        $approved = sanitize_text_field($_POST['approved']);

        if ($post_id && current_user_can('manage_options')) {
            update_post_meta($post_id, 'cd_approved', $approved);

            // Update post status based on approval
            if ($approved === '1') {
                wp_update_post(array(
                    'ID'          => $post_id,
                    'post_status' => 'publish'
                ));
            } else {
                wp_update_post(array(
                    'ID'          => $post_id,
                    'post_status' => 'pending'
                ));
            }

            wp_send_json_success(array('message' => 'Approval status updated successfully'));
        } else {
            wp_send_json_error(array('message' => 'Permission denied or invalid post ID'));
        }
    }

    public static function handle_toggle_auto_publish()
    {
        check_ajax_referer('cd_nonce', 'nonce');

        $auto_publish = sanitize_text_field($_POST['auto_publish']);

        if (current_user_can('manage_options')) {
            update_option('cd_auto_publish', $auto_publish);

            if ($auto_publish === '1') {
                // Auto-approve all pending families
                $args = array(
                    'post_type'      => 'family_head',
                    'post_status'    => 'pending',
                    'posts_per_page' => -1,
                    'meta_query'     => array(
                        array(
                            'key'     => 'cd_approved',
                            'value'   => '0',
                            'compare' => '='
                        )
                    )
                );
                $pending_families = get_posts($args);

                foreach ($pending_families as $family) {
                    update_post_meta($family->ID, 'cd_approved', '1');
                    wp_update_post(array(
                        'ID'          => $family->ID,
                        'post_status' => 'publish'
                    ));
                }

                wp_send_json_success(array('message' => __('Auto-Publish enabled. All pending families have been approved.', CD_TEXT_DOMAIN)));
            } else {
                wp_send_json_success(array('message' => __('Require Admin Approval enabled. New registrations will require approval.', CD_TEXT_DOMAIN)));
            }
        } else {
            wp_send_json_error(array('message' => __('Permission denied.', CD_TEXT_DOMAIN)));
        }
    }

    public static function handle_admin_form_submission()
    {
        if (isset($_POST['cd_admin_registration_nonce']) && wp_verify_nonce($_POST['cd_admin_registration_nonce'], 'cd_admin_registration')) {
            $head_details = array_map('sanitize_text_field', wp_unslash($_POST['cd_head_details']));
            $family_members = isset($_POST['cd_family_members']) ? array_map(function ($member) {
                return array_map('sanitize_text_field', wp_unslash($member));
            }, $_POST['cd_family_members']) : array();

            // Handle profile picture upload
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

                $allowed_types = array('application/pdf');
                if (! in_array($file['type'], $allowed_types)) {
                    wp_die(__('Error: Only PDF files are allowed for Business Brochure.', CD_TEXT_DOMAIN));
                }

                if ($file['size'] > 5 * 1024 * 1024) {
                    wp_die(__('Error: Business Brochure file size must be below 5 MB.', CD_TEXT_DOMAIN));
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
                        $head_details['business_brochure_id'] = $attachment_id;
                    }
                } else {
                    wp_die(__('Error uploading Business Brochure: ', CD_TEXT_DOMAIN) . (isset($movefile['error']) ? $movefile['error'] : 'Unknown error'));
                }
            }



            // Admin submissions are always approved and published
            $post_status = 'publish';
            $approved_status = '1';

            $post_id = wp_insert_post(array(
                'post_type'   => 'family_head',
                'post_status' => $post_status,
                'post_title'  => $head_details['name'],
            ));

            if ($post_id) {
                update_post_meta($post_id, 'cd_head_details', $head_details);
                update_post_meta($post_id, 'cd_family_members', $family_members);
                update_post_meta($post_id, 'cd_approved', $approved_status);

                wp_redirect(admin_url('admin.php?page=cd_directory&message=success'));
                exit;
            }
        }
    }
}
