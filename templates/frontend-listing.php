<?php if (! defined('ABSPATH')) exit; ?>
<div class="mx-auto p-4 container">
    <div class="flex md:flex-row flex-col gap-4 mb-4">
        <input type="text" id="cd-search" placeholder="<?php _e('Search by name', CD_TEXT_DOMAIN); ?>"
            class="flex-grow p-2 border">
        <select id="cd-city-filter" class="p-2 border">
            <option value=""><?php _e('Filter by City', CD_TEXT_DOMAIN); ?></option>
            <!-- Populate dynamically via AJAX or predefined options -->
        </select>
        <select id="cd-education-filter" class="p-2 border">
            <option value=""><?php _e('Filter by Education', CD_TEXT_DOMAIN); ?></option>
            <!-- Add education options -->
        </select>
        <select id="cd-occupation-filter" class="p-2 border">
            <option value=""><?php _e('Filter by Occupation', CD_TEXT_DOMAIN); ?></option>
            <!-- Add occupation options -->
        </select>
    </div>
    <div id="cd-family-list" class="gap-4 grid grid-cols-1 md:grid-cols-3">
        <?php
        $display_fields = get_option('cd_display_fields', array('name', 'address', 'education', 'occupation_type'));
        $display_mode = get_option('cd_display_mode', 'card');
        $args = array(
            'post_type'      => 'family_head',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        );
        $query = new WP_Query($args);
        while ($query->have_postsLe()) {
            $query->the_post();
            $head_details = get_post_meta(get_the_ID(), 'cd_head_details', true);
        ?>
        <div class="bg-white shadow p-4 rounded">
            <?php if (in_array('name', $display_fields)) : ?>
            <h3 class="font-bold text-lg"><?php echo esc_html($head_details['name']); ?></h3>
            <?php endif; ?>
            <?php if (in_array('address', $display_fields)) : ?>
            <p><?php echo esc_html($head_details['address']); ?></p>
            <?php endif; ?>
            <?php if (in_array('education', $display_fields)) : ?>
            <p><?php echo esc_html($head_details['education']); ?></p>
            <?php endif; ?>
            <?php if (in_array('occupation_type', $display_fields)) : ?>
            <p><?php echo esc_html($head_details['occupation_type']); ?></p>
            <?php endif; ?>
            <a href="<?php echo esc_url(add_query_arg('family_id', get_the_ID(), home_url('/family-tree'))); ?>"
                class="text-blue-500 hover:underline"><?php _e('View Family', CD_TEXT_DOMAIN); ?></a>
        </div>
        <?php
        }
        wp_reset_postdata();
        ?>
    </div>
</div>
<?php
if ($display_mode === 'row') {
    // Add row view styling or logic if needed
}
?>