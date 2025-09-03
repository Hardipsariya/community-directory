<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class CD_CPT
{
    public static function register()
    {
        add_action('init', array(__CLASS__, 'register_cpt'));
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        add_action('transition_post_status', array(__CLASS__, 'handle_status_transition'), 10, 3);
    }

    public static function register_cpt()
    {
        $args = array(
            'public'       => true,
            'label'        => __('Family Heads', CD_TEXT_DOMAIN),
            'supports'     => array('title'),
            'show_in_menu' => false,
            'rewrite'      => array('slug' => 'family-head'),
        );
        register_post_type('family_head', $args);

        // Register meta fields.
        register_post_meta('family_head', 'cd_head_details', array(
            'type'         => 'array',
            'single'       => true,
            'show_in_rest' => true,
        ));
        register_post_meta('family_head', 'cd_family_members', array(
            'type'         => 'array',
            'single'       => true,
            'show_in_rest' => true,
        ));
    }

    public static function add_meta_boxes()
    {
        add_meta_box(
            'cd_head_details',
            __('Head of Family Details', CD_TEXT_DOMAIN),
            array(__CLASS__, 'render_head_meta_box'),
            'family_head',
            'normal',
            'high'
        );
        add_meta_box(
            'cd_family_members',
            __('Family Members', CD_TEXT_DOMAIN),
            array(__CLASS__, 'render_members_meta_box'),
            'family_head',
            'normal',
            'high'
        );
        add_meta_box(
            'cd_approval_status',
            __('Approval Status', CD_TEXT_DOMAIN),
            array(__CLASS__, 'render_approval_meta_box'),
            'family_head',
            'side',
            'high'
        );
    }

    public static function render_head_meta_box($post)
    {
        wp_nonce_field('cd_save_meta', 'cd_nonce');
        $head_details = get_post_meta($post->ID, 'cd_head_details', true) ?: array();
?>
        <p><label><?php _e('Family Name', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[family_name]"
                    value="<?php echo esc_attr($head_details['family_name'] ?? ''); ?>" class="widefat" required></label></p>
        <p><label><?php _e('Family Head Name', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[name]"
                    value="<?php echo esc_attr($head_details['name'] ?? ''); ?>" class="widefat" required></label></p>
        <p><label><?php _e('Contact Number', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[mobile]"
                    value="<?php echo esc_attr($head_details['mobile'] ?? ''); ?>" class="widefat" required></label></p>
        <p><label><?php _e('Email', CD_TEXT_DOMAIN); ?>: <input type="email" name="cd_head_details[email]"
                    value="<?php echo esc_attr($head_details['email'] ?? ''); ?>" class="widefat"></label></p>
        <p><label><?php _e('Full Address', CD_TEXT_DOMAIN); ?>: <textarea name="cd_head_details[address]" class="widefat"
                    required><?php echo esc_textarea($head_details['address'] ?? ''); ?></textarea></label></p>
        <p><label><?php _e('City', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[city]"
                    value="<?php echo esc_attr($head_details['city'] ?? ''); ?>" class="widefat" required></label></p>
        <p><label><?php _e('Education', CD_TEXT_DOMAIN); ?>:
                <select name="cd_head_details[education]" class="widefat" required>
                    <option value=""><?php _e('Select Education', CD_TEXT_DOMAIN); ?></option>
                    <?php
                    $education_options = get_option('cd_education_options', array('High School', 'Bachelor', 'Master', 'PhD'));
                    foreach ($education_options as $option) {
                        echo '<option value="' . esc_attr($option) . '" ' . selected($head_details['education'] ?? '', $option, false) . '>' . esc_html($option) . '</option>';
                    }
                    ?>
                </select></label></p>
        <p><label><?php _e('Occupation Type', CD_TEXT_DOMAIN); ?>:
                <select name="cd_head_details[occupation_type]" class="widefat" required>
                    <option value="job" <?php selected($head_details['occupation_type'] ?? '', 'job'); ?>>
                        <?php _e('Job', CD_TEXT_DOMAIN); ?></option>
                    <option value="business" <?php selected($head_details['occupation_type'] ?? '', 'business'); ?>>
                        <?php _e('Business', CD_TEXT_DOMAIN); ?></option>
                </select></label></p>
        <div id="cd-job-fields" class="<?php echo (($head_details['occupation_type'] ?? '') === 'job') ? '' : 'hidden'; ?>">
            <p><label><?php _e('Job Title', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[job_title]"
                        value="<?php echo esc_attr($head_details['job_title'] ?? ''); ?>" class="widefat"></label></p>
            <p><label><?php _e('Company Name', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[company_name]"
                        value="<?php echo esc_attr($head_details['company_name'] ?? ''); ?>" class="widefat"></label></p>
            <p><label><?php _e('Company Location', CD_TEXT_DOMAIN); ?>: <input type="text"
                        name="cd_head_details[company_location]"
                        value="<?php echo esc_attr($head_details['company_location'] ?? ''); ?>" class="widefat"></label></p>
        </div>
        <div id="cd-business-fields"
            class="<?php echo (($head_details['occupation_type'] ?? '') === 'business') ? '' : 'hidden'; ?>">
            <p><label><?php _e('Business Name', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[business_name]"
                        value="<?php echo esc_attr($head_details['business_name'] ?? ''); ?>" class="widefat" required></label></p>
            <p><label><?php _e('Business Industry', CD_TEXT_DOMAIN); ?>:
                    <select name="cd_head_details[business_type]" class="widefat" required>
                        <option value=""><?php _e('Select Business Industry', CD_TEXT_DOMAIN); ?></option>
                        <?php
                        $business_industry_options = get_option('cd_business_industry_options', array('Furniture', 'Tailor'));
                        foreach ($business_industry_options as $option) {
                            echo '<option value="' . esc_attr($option) . '" ' . selected($head_details['business_type'] ?? '', $option, false) . '>' . esc_html($option) . '</option>';
                        }
                        ?>
                    </select></label></p>
            <p><label><?php _e('Business City', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[business_city]"
                        value="<?php echo esc_attr($head_details['business_city'] ?? ''); ?>" class="widefat" required></label></p>
            <p><label><?php _e('Business Website', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[business_website]"
                        value="<?php echo esc_attr($head_details['business_website'] ?? ''); ?>" class="widefat"></label></p>
            <p><label><?php _e('Business Brochure (PDF below 5 MB)', CD_TEXT_DOMAIN); ?>: <button type="button" class="button" id="upload-business-brochure"><?php _e('Choose File', CD_TEXT_DOMAIN); ?></button></label></p>
            <input type="hidden" name="cd_head_details[business_brochure_id]" id="business-brochure-id" value="<?php echo esc_attr($head_details['business_brochure_id'] ?? ''); ?>">
            <div id="current-brochure">
                <?php if (!empty($head_details['business_brochure_id'])): ?>
                    <p><?php _e('Current Brochure', CD_TEXT_DOMAIN); ?>: <a href="<?php echo wp_get_attachment_url($head_details['business_brochure_id']); ?>" target="_blank"><?php _e('View', CD_TEXT_DOMAIN); ?></a></p>
                <?php endif; ?>
            </div>
        </div>
        <p><label><?php _e('Family Photo', CD_TEXT_DOMAIN); ?>: <button type="button" class="button" id="upload-profile-picture"><?php _e('Choose File', CD_TEXT_DOMAIN); ?></button></label></p>
        <input type="hidden" name="cd_head_details[profile_picture_id]" id="profile-picture-id" value="<?php echo esc_attr($head_details['profile_picture_id'] ?? ''); ?>">
        <div id="current-photo">
            <?php if (!empty($head_details['profile_picture_id'])): ?>
                <p><?php _e('Current Photo', CD_TEXT_DOMAIN); ?>: <img src="<?php echo wp_get_attachment_url($head_details['profile_picture_id']); ?>" alt="Profile Picture" style="max-width: 100px;"></p>
            <?php endif; ?>
        </div>
        <p><label><?php _e('Mosal Category', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[mosal_category]"
                    value="<?php echo esc_attr($head_details['mosal_category'] ?? ''); ?>" class="widefat"></label></p>
        <script>
        jQuery(document).ready(function($) {
            function toggleOccupationFields() {
                var occupationType = $('select[name="cd_head_details[occupation_type]"]').val();
                if (occupationType === 'job') {
                    $('#cd-job-fields').show();
                    $('#cd-business-fields').hide();
                    $('#cd-business-fields input, #cd-business-fields select').prop('required', false);
                } else if (occupationType === 'business') {
                    $('#cd-job-fields').hide();
                    $('#cd-business-fields').show();
                    $('#cd-business-fields input[name="cd_head_details[business_name]"]').prop('required', true);
                    $('#cd-business-fields select[name="cd_head_details[business_type]"]').prop('required', true);
                    $('#cd-business-fields input[name="cd_head_details[business_city]"]').prop('required', true);
                } else {
                    $('#cd-job-fields').hide();
                    $('#cd-business-fields').hide();
                    $('#cd-business-fields input, #cd-business-fields select').prop('required', false);
                }
            }
            $('select[name="cd_head_details[occupation_type]"]').change(toggleOccupationFields);
            toggleOccupationFields(); // Initial call

            // Media uploader for profile picture
            $('#upload-profile-picture').click(function(e) {
                e.preventDefault();
                var custom_uploader = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                })
                .on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#profile-picture-id').val(attachment.id);
                    $('#current-photo').html('<p><?php _e('Current Photo', CD_TEXT_DOMAIN); ?>: <img src="' + attachment.url + '" alt="Profile Picture" style="max-width: 100px;"></p>');
                })
                .open();
            });

            // Media uploader for business brochure
            $('#upload-business-brochure').click(function(e) {
                e.preventDefault();
                var custom_uploader = wp.media({
                    title: 'Choose PDF',
                    button: {
                        text: 'Choose PDF'
                    },
                    multiple: false,
                    library: {
                        type: 'application/pdf'
                    }
                })
                .on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#business-brochure-id').val(attachment.id);
                    $('#current-brochure').html('<p><?php _e('Current Brochure', CD_TEXT_DOMAIN); ?>: <a href="' + attachment.url + '" target="_blank"><?php _e('View', CD_TEXT_DOMAIN); ?></a></p>');
                })
                .open();
            });
        });
        </script>
    <?php
    }

    public static function render_members_meta_box($post)
    {
        $members = get_post_meta($post->ID, 'cd_family_members', true) ?: array();
        if (empty($members)) {
            $members = array(array()); // Ensure at least one empty member
        }
    ?>
        <div id="cd-family-members-admin">
            <?php foreach ($members as $index => $member) : ?>
                <div class="mb-2 p-2 border family-member">
                    <p><label><?php _e('Name', CD_TEXT_DOMAIN); ?>: <input type="text"
                                name="cd_family_members[<?php echo $index; ?>][name]"
                                value="<?php echo esc_attr($member['name'] ?? ''); ?>" class="widefat"></label></p>
                    <p><label><?php _e('Gender', CD_TEXT_DOMAIN); ?>:
                            <select name="cd_family_members[<?php echo $index; ?>][gender]" class="widefat">
                                <option value=""><?php _e('Select Gender', CD_TEXT_DOMAIN); ?></option>
                                <option value="male" <?php selected($member['gender'] ?? '', 'male'); ?>>
                                    <?php _e('Male', CD_TEXT_DOMAIN); ?></option>
                                <option value="female" <?php selected($member['gender'] ?? '', 'female'); ?>>
                                    <?php _e('Female', CD_TEXT_DOMAIN); ?></option>
                            </select></label></p>
                    <p><label><?php _e('Relation with Head', CD_TEXT_DOMAIN); ?>: <input type="text"
                                name="cd_family_members[<?php echo $index; ?>][relation]"
                                value="<?php echo esc_attr($member['relation'] ?? ''); ?>" class="widefat"></label></p>
                    <button type="button" class="remove-member button"><?php _e('Remove Member', CD_TEXT_DOMAIN); ?></button>
                </div>
            <?php endforeach; ?>
            <button type="button" class="add-member button"><?php _e('Add Member', CD_TEXT_DOMAIN); ?></button>
        </div>
        <script>
        jQuery(document).ready(function($) {
            var memberIndex = <?php echo count($members); ?>;

            $('#cd-family-members-admin').on('click', '.add-member', function() {
                var newMember = $('.family-member').first().clone();
                newMember.find('input, select').val('').attr('name', function(i, name) {
                    return name.replace(/\[\d+\]/, '[' + memberIndex + ']');
                });
                $('#cd-family-members-admin .add-member').before(newMember);
                memberIndex++;
            });

            $('#cd-family-members-admin').on('click', '.remove-member', function() {
                if ($('.family-member').length > 1) {
                    $(this).closest('.family-member').remove();
                } else {
                    alert('<?php _e('At least one family member is required.', CD_TEXT_DOMAIN); ?>');
                }
            });
        });
        </script>
<?php
    }

    public static function render_approval_meta_box($post)
    {
        $approved = get_post_meta($post->ID, 'cd_approved', true);
        $approved = $approved === '1' ? '1' : '0';
    ?>
        <p>
            <label style="display: inline-block; margin-right: 15px;"><input type="radio" name="cd_approval_status" value="1" <?php checked($approved, '1'); ?>> <?php _e('Approved', CD_TEXT_DOMAIN); ?></label>
            <label style="display: inline-block;"><input type="radio" name="cd_approval_status" value="0" <?php checked($approved, '0'); ?>> <?php _e('Pending', CD_TEXT_DOMAIN); ?></label>
        </p>
    <?php
    }

    public static function save_meta($post_id)
    {
        if (! isset($_POST['cd_nonce']) || ! wp_verify_nonce($_POST['cd_nonce'], 'cd_save_meta')) {
            return;
        }
        if (isset($_POST['cd_head_details'])) {
            $head_details = get_post_meta($post_id, 'cd_head_details', true) ?: array();
            $new_head_details = array_map('sanitize_text_field', wp_unslash($_POST['cd_head_details']));
            $head_details = array_merge($head_details, $new_head_details);

            update_post_meta($post_id, 'cd_head_details', $head_details);
        }
        if (isset($_POST['cd_family_members'])) {
            $members = array_map(function ($member) {
                return array_map('sanitize_text_field', wp_unslash($member));
            }, $_POST['cd_family_members']);
            update_post_meta($post_id, 'cd_family_members', $members);
        }
        if (isset($_POST['cd_approval_status'])) {
            $approval_status = sanitize_text_field($_POST['cd_approval_status']);
            update_post_meta($post_id, 'cd_approved', $approval_status);

            // Update post status based on approval
            $current_status = get_post_status($post_id);
            if ($approval_status === '1' && $current_status !== 'publish') {
                wp_update_post(array('ID' => $post_id, 'post_status' => 'publish'));
            } elseif ($approval_status === '0' && $current_status !== 'pending') {
                wp_update_post(array('ID' => $post_id, 'post_status' => 'pending'));
            }
        }
    }

    public static function handle_status_transition($new_status, $old_status, $post)
    {
        if ($post->post_type === 'family_head') {
            if ($new_status === 'publish' && $old_status !== 'publish') {
                update_post_meta($post->ID, 'cd_approved', '1');
            } elseif ($new_status === 'pending' && $old_status !== 'pending') {
                update_post_meta($post->ID, 'cd_approved', '0');
            }
        }
    }

    public static function activate()
    {
        self::register_cpt();
        flush_rewrite_rules();
    }

    public static function enqueue_scripts($hook)
    {
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            wp_enqueue_media();
        }
    }

    public static function deactivate()
    {
        flush_rewrite_rules();
        $posts = get_posts(array('post_type' => 'family_head', 'posts_per_page' => -1));
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }
}
