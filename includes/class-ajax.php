<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class CD_AJAX
{
    public static function register()
    {
        add_action('wp_ajax_cd_search_filter', array(__CLASS__, 'handle_search_filter'));
        add_action('wp_ajax_nopriv_cd_search_filter', array(__CLASS__, 'handle_search_filter'));
    }

    public static function handle_search_filter()
    {
        check_ajax_referer('cd_nonce', 'nonce');

        $search = sanitize_text_field($_POST['search'] ?? '');
        $city = sanitize_text_field($_POST['city'] ?? '');
        $education = sanitize_text_field($_POST['education'] ?? '');
        $occupation = sanitize_text_field($_POST['occupation'] ?? '');

        $args = array(
            'post_type'      => 'family_head',
            'posts_per_page' => -1,
            's'              => $search,
            'meta_query'     => array('relation' => 'AND'),
            'orderby'        => 'title',
            'order'          => 'ASC',
        );

        if ($city) {
            $args['meta_query'][] = array(
                'key'     => 'cd_head_details',
                'value'   => $city,
                'compare' => 'LIKE',
            );
        }
        if ($education) {
            $args['meta_query'][] = array(
                'key'     => 'cd_head_details',
                'value'   => $education,
                'compare' => 'LIKE',
            );
        }
        if ($occupation) {
            $args['meta_query'][] = array(
                'key'     => 'cd_head_details',
                'value'   => $occupation,
                'compare' => 'LIKE',
            );
        }

        $query = new WP_Query($args);
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            $head_details = get_post_meta(get_the_ID(), 'cd_head_details', true);
?>
<div class="bg-white shadow p-4 rounded">
    <h3 class="font-bold text-lg"><?php echo esc_html($head_details['name']); ?></h3>
    <p><?php echo esc_html($head_details['address']); ?></p>
    <p><?php echo esc_html($head_details['education']); ?></p>
    <p><?php echo esc_html($head_details['occupation_type']); ?></p>
    <a href="<?php echo esc_url(add_query_arg('family_id', get_the_ID(), home_url('/family-tree'))); ?>"
        class="text-blue-500 hover:underline"><?php _e('View Family', CD_TEXT_DOMAIN); ?></a>
</div>
<?php
        }
        wp_reset_postdata();
        wp_send_json_success(ob_get_clean());
    }
}
?>