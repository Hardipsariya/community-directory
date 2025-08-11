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

        // Register meta fields for head details and family members.
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
            value="<?php echo esc_attr($head_details['name'] ?? ''); ?>" class="widefat"></label></p>
<p><label><?php _e('Mobile Number', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[mobile]"
            value="<?php echo esc_attr($head_details['mobile'] ?? ''); ?>" class="widefat"></label></p>
<p><label><?php _e('Email', CD_TEXT_DOMAIN); ?>: <input type="email" name="cd_head_details[email]"
            value="<?php echo esc_attr($head_details['email'] ?? ''); ?>" class="widefat"></label></p>
<p><label><?php _e('Full Address', CD_TEXT_DOMAIN); ?>: <textarea name="cd_head_details[address]"
            class="widefat"><?php echo esc_textarea($head_details['address'] ?? ''); ?></textarea></label></p>
<p><label><?php _e('Education', CD_TEXT_DOMAIN); ?>: <input type="text" name="cd_head_details[education]"
            value="<?php echo esc_attr($head_details['education'] ?? ''); ?>" class="widefat"></label></p>
<p><label><?php _e('Occupation Type', CD_TEXT_DOMAIN); ?>:
        <select name="cd_head_details[occupation_type]" class="widefat">
            <option value="job" <?php selected($head_details['occupation_type'] ?? '', 'job'); ?>>
                <?php _e('Job', CD_TEXT_DOMAIN); ?></option>
            <option value="business" <?php selected($head_details['occupation_type'] ?? '', 'business'); ?>>
                <?php _e('Business', CD_TEXT_DOMAIN); ?></option>
        </select></label></p>
<!-- Add conditional fields for Job/Business as needed -->
<?php
    }

    public static function render_members_meta_box($post)
    {
        $members = get_post_meta($post->ID, 'cd_family_members', true) ?: array();
    ?>
<div id="cd-family-members-admin">
    <?php foreach ($members as $index => $member) : ?>
    <div class="family-member">
        <p><label><?php _e('Name', CD_TEXT_BANK); ?>: <input type="text"
                    name="cd_family_members[<?php echo $index; ?>][name]"
                    value="<?php echo esc_attr($member['name'] ?? ''); ?>" class="widefat"></label></p>
        <!-- Add other member fields -->
    </div>
    <?php endforeach; ?>
    <button type="button" class="add-member"><?php _e('Add Member', CD_TEXT_DOMAIN); ?></button>
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
        // Optionally delete all family_head posts and meta data.
        $posts = get_posts(array('post_type' => 'family_head', 'posts_per_page' => -1));
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
    }
}
?>