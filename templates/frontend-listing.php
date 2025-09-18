<?php if (! defined('ABSPATH')) exit;

// Define variables at the top before they're used in HTML
$display_fields = get_option('cd_display_fields', array('name', 'address', 'education', 'occupation_type'));
$display_mode = get_option('cd_display_mode', 'card');
?>
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
    <div id="cd-family-list" class="<?php echo $display_mode === 'row' ? 'cd-row-view' : 'gap-4 grid grid-cols-1 md:grid-cols-3'; ?>">
        <?php
        $args = array(
            'post_type'      => 'family_head',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => 'cd_approved',
                    'value'   => '1',
                    'compare' => '='
                )
            ),
            'orderby'        => 'title',
            'order'          => 'ASC',
        );
        $query = new WP_Query($args);

        while ($query->have_posts()) {
            $query->the_post();
            $head_details = get_post_meta(get_the_ID(), 'cd_head_details', true);

            if ($display_mode === 'row') {
                // Row View Layout
                ?>
                <div class="cd-family-row bg-white shadow p-4 rounded">
                    <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                        <?php if (in_array('photo', $display_fields)) : ?>
                            <div class="flex-shrink-0">
                                <?php
                                $photo_id = !empty($head_details['profile_picture_id']) ? $head_details['profile_picture_id'] : '';
                                if ($photo_id) {
                                    echo wp_get_attachment_image($photo_id, 'thumbnail', false, ['class' => 'w-16 h-16 rounded-full object-cover']);
                                } else {
                                    echo '<img src="https://placehold.co/64x64" alt="' . esc_attr($head_details['name']) . ' profile photo" class="rounded-full w-16 h-16 object-cover">';
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex-grow min-w-0">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-2 lg:gap-4 mb-2">
                                <?php if (in_array('name', $display_fields)) : ?>
                                    <h3 class="font-bold text-lg break-words"><?php echo esc_html($head_details['name']); ?></h3>
                                <?php endif; ?>

                                <div class="flex flex-wrap gap-2 lg:gap-4 text-sm text-gray-600">
                                    <?php if (in_array('city', $display_fields)) : ?>
                                        <span class="break-words"><?php echo esc_html($head_details['city']); ?></span>
                                    <?php endif; ?>
                                    <?php if (in_array('education', $display_fields)) : ?>
                                        <span class="break-words"><?php echo esc_html($head_details['education']); ?></span>
                                    <?php endif; ?>
                                    <?php if (in_array('occupation_type', $display_fields)) : ?>
                                        <span class="break-words"><?php echo esc_html($head_details['occupation_type']); ?></span>
                                    <?php endif; ?>
                                    <?php if (in_array('mobile', $display_fields)) : ?>
                                        <span class="break-words"><?php echo esc_html($head_details['mobile']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (in_array('address', $display_fields)) : ?>
                                <p class="text-sm text-gray-600 mb-2 break-words"><?php echo esc_html($head_details['address']); ?></p>
                            <?php endif; ?>

                            <?php if (in_array('business_brochure', $display_fields)) : ?>
                                <div class="mb-2">
                                    <?php
                                    $brochure_id = !empty($head_details['business_brochure_id']) ? $head_details['business_brochure_id'] : '';
                                    if ($brochure_id && wp_get_attachment_url($brochure_id)) {
                                        $brochure_url = wp_get_attachment_url($brochure_id);
                                        echo '<a href="' . esc_url($brochure_url) . '" target="_blank" class="text-blue-600 underline text-sm break-words">' . __('View Business Brochure', CD_TEXT_DOMAIN) . '</a>';
                                    } else {
                                        echo '<span class="text-gray-500 text-sm">' . __('No Business Brochure uploaded', CD_TEXT_DOMAIN) . '</span>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-shrink-0">
                            <a href="#" class="text-blue-500 hover:underline cd-view-tree whitespace-nowrap" data-family-id="<?php echo get_the_ID(); ?>"><?php _e('View Family Tree', CD_TEXT_DOMAIN); ?></a>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                // Card View Layout (existing code)
                ?>
                <div class="bg-white shadow p-4 rounded">
                    <?php if (in_array('name', $display_fields)) : ?>
                        <h3 class="font-bold text-lg"><?php echo esc_html($head_details['name']); ?></h3>
                    <?php endif; ?>
                    <?php if (in_array('address', $display_fields)) : ?>
                        <p><?php echo esc_html($head_details['address']); ?></p>
                    <?php endif; ?>
                    <?php if (in_array('city', $display_fields)) : ?>
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
                            $photo_id = !empty($head_details['profile_picture_id']) ? $head_details['profile_picture_id'] : '';
                            if ($photo_id) {
                                echo wp_get_attachment_image($photo_id, 'thumbnail', false, ['class' => 'w-24 h-24 rounded-full object-cover']);
                            } else {
                                echo '<img src="https://placehold.co/96x96" alt="' . esc_attr($head_details['name']) . ' profile photo" class="rounded-full w-24 h-24 object-cover">';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if (in_array('business_brochure', $display_fields)) : ?>
                        <div class="mb-3">
                            <?php
                            $brochure_id = !empty($head_details['business_brochure_id']) ? $head_details['business_brochure_id'] : '';

                            if ($brochure_id && wp_get_attachment_url($brochure_id)) {
                                $brochure_url = wp_get_attachment_url($brochure_id);
                                echo '<a href="' . esc_url($brochure_url) . '" target="_blank" class="text-blue-600 underline">' . __('View Business Brochure', CD_TEXT_DOMAIN) . '</a>';
                            } else {
                                echo '<span class="text-gray-500">' . __('No Business Brochure uploaded', CD_TEXT_DOMAIN) . '</span>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <a href="#" class="text-blue-500 hover:underline cd-view-tree" data-family-id="<?php echo get_the_ID(); ?>"><?php _e('View Family Tree', CD_TEXT_DOMAIN); ?></a>
                </div>
                <?php
            }
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

<!-- Family Tree Modal -->
<div id="cd-family-tree-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-xl font-bold" id="modal-family-title"><?php _e('Family Tree', CD_TEXT_DOMAIN); ?></h2>
                <button id="cd-close-modal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-4 overflow-auto max-h-[calc(90vh-120px)]">
                <div id="cd-family-tree-container" class="w-full h-96 bg-gray-50 rounded border">
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                            <p class="text-gray-600"><?php _e('Loading family tree...', CD_TEXT_DOMAIN); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
