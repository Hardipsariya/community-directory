<?php if (! defined('ABSPATH')) exit; ?>
<div class="mx-auto p-4 container">
    <div class="flex md:flex-row flex-col gap-4 mb-4">
        <input type="text" id="cd-search" placeholder="<?php _e('Search by name', CD_TEXT_DOMAIN); ?>"
            class="flex-grow p-2 border">
        <select id="cd-city-filter" class="p-2 border">
            <option value=""><?php _e('Filter by City', CD_TEXT_DOMAIN); ?></option>
            <?php
            // Dynamically populate city options
            $cities = array();
            $args = array(
                'post_type'      => 'family_head',
                'posts_per_page' => -1,
                'fields'         => 'ids', // Only get post IDs
            );
            $city_query = new WP_Query($args);

            if ($city_query->have_posts()) {
                foreach ($city_query->posts as $post_id) {
                    $head_details = get_post_meta($post_id, 'cd_head_details', true);
                    if (!empty($head_details['city'])) {
                        // Normalize city name to title case for consistency
                        $city_name = sanitize_text_field($head_details['city']);
                        $city_name = ucwords(strtolower($city_name));
                        $cities[] = $city_name;
                    }
                }
            }
            $unique_cities = array_unique($cities);
            sort($unique_cities); // Sort cities alphabetically

            foreach ($unique_cities as $city) {
                echo '<option value="' . esc_attr($city) . '">' . esc_html($city) . '</option>';
            }
            wp_reset_postdata();
            ?>
        </select>


        <select id="cd-education-filter" class="p-2 border">
            <option value=""><?php _e('Filter by Education', CD_TEXT_DOMAIN); ?></option>
            <?php
            // Dynamically populate education options
            $educations = array();
            $args = array(
                'post_type'      => 'family_head',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            );
            $education_query = new WP_Query($args);

            if ($education_query->have_posts()) {
                foreach ($education_query->posts as $post_id) {
                    $head_details = get_post_meta($post_id, 'cd_head_details', true);
                    if (!empty($head_details['education'])) {
                        // Normalize education name to title case for consistency
                        $education_name = sanitize_text_field($head_details['education']);
                        $education_name = ucwords(strtolower($education_name));
                        $educations[] = $education_name;
                    }
                }
            }
            $unique_educations = array_unique($educations);
            sort($unique_educations);

            foreach ($unique_educations as $education) {
                echo '<option value="' . esc_attr($education) . '">' . esc_html($education) . '</option>';
            }
            wp_reset_postdata();
            ?>
        </select>




        <select id="cd-occupation-filter" class="p-2 border">
            <option value=""><?php _e('Filter by Occupation', CD_TEXT_DOMAIN); ?></option>
            <?php
            // Dynamically populate occupation options
            $occupations = array();
            $args = array(
                'post_type'      => 'family_head',
                'posts_per_page' => -1,
                'fields'         => 'ids', // Only get post IDs
            );
            $occupation_query = new WP_Query($args);

            if ($occupation_query->have_posts()) {
                foreach ($occupation_query->posts as $post_id) {
                    $head_details = get_post_meta($post_id, 'cd_head_details', true);
                    if (!empty($head_details['occupation_type'])) {
                        // Normalize occupation name to title case for consistency
                        $occupation_name = sanitize_text_field($head_details['occupation_type']);
                        $occupation_name = ucwords(strtolower($occupation_name));
                        $occupations[] = $occupation_name;
                    }
                }
            }
            $unique_occupations = array_unique($occupations);
            sort($unique_occupations); // Sort occupations alphabetically

            foreach ($unique_occupations as $occupation) {
                echo '<option value="' . esc_attr($occupation) . '">' . esc_html($occupation) . '</option>';
            }
            wp_reset_postdata();
            ?>
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

        while ($query->have_posts()) {
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
                <?php if (in_array('city', $display_fields)) : // Display city if enabled 
                ?>
                    <p><?php echo esc_html($head_details['city']); ?></p>
                <?php endif; ?>
                <?php if (in_array('education', $display_fields)) : ?>
                    <p><?php echo esc_html($head_details['education']); ?></p>
                <?php endif; ?>
                <?php if (in_array('occupation_type', $display_fields)) : ?>
                    <p><?php echo esc_html($head_details['occupation_type']); ?></p>
                <?php endif; ?>
                <?php if (in_array('mobile', $display_fields)) : ?>
                    <p><?php echo esc_html($head_details['mobile']); ?></p>
                <?php endif; ?>
                <?php if (in_array('photo', $display_fields)) : ?>
                    <div class="mb-3">
                        <?php
                        $photo_id = !empty($head_details['photo']) ? $head_details['photo'] : '';
                        if ($photo_id) {
                            echo wp_get_attachment_image($photo_id, 'thumbnail', false, ['class' => 'w-24 h-24 rounded-full object-cover']);
                        } else {
                            // Default placeholder if no photo is set
                            echo '<img src="https://placehold.co/96x96" alt="' . esc_attr($head_details['name']) . ' profile photo" class="rounded-full w-24 h-24 object-cover">';
                        }
                        ?>
                    </div>
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