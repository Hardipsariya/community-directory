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
        add_submenu_page(
            'cd_directory',
            __('Family List', CD_TEXT_DOMAIN),
            __('Family List', CD_TEXT_DOMAIN),
            'manage_options',
            'cd_directory',
            array(__CLASS__, 'render_family_list')
        );
        add_submenu_page(
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
                <th><?php _e('Actions', CD_TEXT_DOMAIN); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
            <?php $head_details = get_post_meta(get_the_ID(), 'cd_head_details', true); ?>
            <tr>
                <td><?php echo esc_html($head_details['name'] ?? ''); ?></td>
                <td><?php echo esc_html($head_details['city'] ?? ''); ?></td>
                <td><?php echo esc_html($head_details['education'] ?? ''); ?></td>
                <td><?php echo esc_html($head_details['occupation_type'] ?? ''); ?></td>
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
                ?>
        <h2><?php _e('Frontend Display Fields', CD_TEXT_DOMAIN); ?></h2>
        <p><label><input type="checkbox" name="cd_display_fields[name]" value="1"
                    <?php checked(in_array('name', $display_fields)); ?>> <?php _e('Name', CD_TEXT_DOMAIN); ?></label>
        </p>
        <p><label><input type="checkbox" name="cd_display_fields[city]" value="1"
                    <?php checked(in_array('city', $display_fields)); ?>> <?php _e('City', CD_TEXT_DOMAIN); ?></label>
        </p>
        <p><label><input type="checkbox" name="cd_display_fields[education]" value="1"
                    <?php checked(in_array('education', $display_fields)); ?>>
                <?php _e('Education', CD_TEXT_DOMAIN); ?></label></p>
        <p><label><input type="checkbox" name="cd_display_fields[occupation_type]" value="1"
                    <?php checked(in_array('occupation_type', $display_fields)); ?>>
                <?php _e('Occupation Type', CD_TEXT_DOMAIN); ?></label></p>
        <p><label><input type="checkbox" name="cd_display_fields[mobile]" value="1"
                    <?php checked(in_array('mobile', $display_fields)); ?>>
                <?php _e('Mobile Number', CD_TEXT_DOMAIN); ?></label></p>
        <h2><?php _e('Frontend Display Mode', CD_TEXT_DOMAIN); ?></h2>
        <p><label><input type="radio" name="cd_display_mode" value="card" <?php checked($display_mode, 'card'); ?>>
                <?php _e('Card View', CD_TEXT_DOMAIN); ?></label></p>
        <p><label><input type="radio" name="cd_display_mode" value="row" <?php checked($display_mode, 'row'); ?>>
                <?php _e('Row View', CD_TEXT_DOMAIN); ?></label></p>
        <?php submit_button(); ?>
    </form>
</div>
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
    }
}