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
    }

    public static function render_head_meta_box($post)
    {
        wp_nonce_field('cd_save_meta', 'cd_nonce');
        $head_details = get_post_meta($post->ID, 'cd_head_details', true) ?: array();
?>
        <p><label><?php _e('Name', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[name]"
                    value="<?php echo esc_attr($head_details['name'] ?? ''); ?>" class="widefat" required></label></p>
        <p><label><?php _e('Mobile Number', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[mobile]"
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
                    <option value="high_school" <?php selected($head_details['education'] ?? '', 'high_school'); ?>>
                        <?php _e('High School', CD_TEXT_DOMAIN); ?></option>
                    <option value="bachelor" <?php selected($head_details['education'] ?? '', 'bachelor'); ?>>
                        <?php _e('Bachelor', CD_TEXT_DOMAIN); ?></option>
                    <option value="master" <?php selected($head_details['education'] ?? '', 'master'); ?>>
                        <?php _e('Master', CD_TEXT_DOMAIN); ?></option>
                    <option value="phd" <?php selected($head_details['education'] ?? '', 'phd'); ?>>
                        <?php _e('PhD', CD_TEXT_DOMAIN); ?></option>
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
                        value="<?php echo esc_attr($head_details['business_name'] ?? ''); ?>" class="widefat"></label></p>
            <p><label><?php _e('Business Type', CD_TEXT_DOMAIN); ?>:
                    <select name="cd_head_details[business_type]" class="widefat">
                        <option value=""><?php _e('Select Business Type', CD_TEXT_DOMAIN); ?></option>
                        <option value="furniture" <?php selected($head_details['business_type'] ?? '', 'furniture'); ?>>
                            <?php _e('Furniture', CD_TEXT_DOMAIN); ?></option>
                        <option value="tailor" <?php selected($head_details['business_type'] ?? '', 'tailor'); ?>>
                            <?php _e('Tailor', CD_TEXT_DOMAIN); ?></option>
                    </select></label></p>
            <p><label><?php _e('Business Address', CD_TEXT_DOMAIN); ?>: <textarea name="cd_head_details[business_address]"
                        class="widefat"><?php echo esc_textarea($head_details['business_address'] ?? ''); ?></textarea></label>
            </p>
            <p><label><?php _e('Business Contact Number', CD_TEXT_DOMAIN); ?>: <input type="text"
                        name="cd_head_details[business_contact]"
                        value="<?php echo esc_attr($head_details['business_contact'] ?? ''); ?>" class="widefat"></label></p>
        </div>
        <p><label><?php _e('Profile Picture ID', CD_TEXT_DOMAIN); ?>: <input type="text"
                    name="cd_head_details[profile_picture_id]"
                    value="<?php echo esc_attr($head_details['profile_picture_id'] ?? ''); ?>" class="widefat" readonly></label>
        </p>
    <?php
    }

    public static function render_members_meta_box($post)
    {
        $members = get_post_meta($post->ID, 'cd_family_members', true) ?: array();
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
                    <p><label><?php _e('Date of Birth', CD_TEXT_DOMAIN); ?>: <input type="date"
                                name="cd_family_members[<?php echo $index; ?>][dob]"
                                value="<?php echo esc_attr($member['dob'] ?? ''); ?>" class="widefat"></label></p>
                    <p><label><?php _e('Education', CD_TEXT_DOMAIN); ?>:
                            <select name="cd_family_members[<?php echo $index; ?>][education]" class="widefat">
                                <option value=""><?php _e('Select Education', CD_TEXT_DOMAIN); ?></option>
                                <option value="high_school" <?php selected($member['education'] ?? '', 'high_school'); ?>>
                                    <?php _e('High School', CD_TEXT_DOMAIN); ?></option>
                                <option value="bachelor" <?php selected($member['education'] ?? '', 'bachelor'); ?>>
                                    <?php _e('Bachelor', CD_TEXT_DOMAIN); ?></option>
                                <option value="master" <?php selected($member['education'] ?? '', 'master'); ?>>
                                    <?php _e('Master', CD_TEXT_DOMAIN); ?></option>
                                <option value="phd" <?php selected($member['education'] ?? '', 'phd'); ?>>
                                    <?php _e('PhD', CD_TEXT_DOMAIN); ?></option>
                            </select></label></p>
                    <p><label><?php _e('Occupation', CD_TEXT_DOMAIN); ?>: <input type="text"
                                name="cd_family_members[<?php echo $index; ?>][occupation]"
                                value="<?php echo esc_attr($member['occupation'] ?? ''); ?>" class="widefat"></label></p>
                    <p><label><?php _e('Relation with Head', CD_TEXT_DOMAIN); ?>: <input type="text"
                                name="cd_family_members[<?php echo $index; ?>][relation]"
                                value="<?php echo esc_attr($member['relation'] ?? ''); ?>" class="widefat"></label></p>
                    <p><label><?php _e('Photo ID', CD_TEXT_DOMAIN); ?>: <input type="text"
                                name="cd_family_members[<?php echo $index; ?>][photo_id]"
                                value="<?php echo esc_attr($member['photo_id'] ?? ''); ?>" class="widefat" readonly></label></p>
                    <button type="button" class="remove-member button"><?php _e('Remove Member', CD_TEXT_DOMAIN); ?></button>
                </div>
            <?php endforeach; ?>
            <button type="button" class="add-member button"><?php _e('Add Member', CD_TEXT_DOMAIN); ?></button>
        </div>
<?php
    }

    public static function save_meta($post_id)
    {
        if (! isset($_POST['cd_nonce']) || ! wp_verify_nonce($_POST['cd_nonce'], 'cd_save_meta')) {
            return;
        }
        if (isset($_POST['cd_head_details'])) {
            $head_details = array_map('sanitize_text_field', wp_unslash($_POST['cd_head_details']));
            update_post_meta($post_id, 'cd_head_details', $head_details);
        }
        if (isset($_POST['cd_family_members'])) {
            $members = array_map(function ($member) {
                return array_map('sanitize_text_field', wp_unslash($member));
            }, $_POST['cd_family_members']);
            update_post_meta($post_id, 'cd_family_members', $members);
        }
    }

    public static function activate()
    {
        self::register_cpt();
        flush_rewrite_rules();
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
