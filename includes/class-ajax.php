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
                'value'   => sprintf(':"city";s:%d:"%s"', strlen($city), $city),
                'compare' => 'LIKE',
            );
        }
        if ($education) {
            $args['meta_query'][] = array(
                'key'     => 'cd_head_details',
                'value'   => sprintf(':"education";s:%d:"%s"', strlen($education), $education),
                'compare' => 'LIKE',
            );
        }
        if ($occupation) {
            $args['meta_query'][] = array(
                'key'     => 'cd_head_details',
                'value'   => sprintf(':"occupation_type";s:%d:"%s"', strlen($occupation), $occupation),
                'compare' => 'LIKE',
            );
        }

        $query = new WP_Query($args);
        $display_fields = get_option('cd_display_fields', array('name', 'address', 'education', 'occupation_type', 'mobile'));

        ob_start();

        if ($query->have_posts()) {
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
        } else {
            // Show message when no family is found
            $search_term = !empty($search) ? $search : '';
            $message = !empty($search_term)
                ? sprintf(__('No family found for "%s"', CD_TEXT_DOMAIN), esc_html($search_term))
                : __('No families found matching your criteria', CD_TEXT_DOMAIN);
            ?>
            <div class="col-span-full bg-white shadow p-8 rounded text-center">
                <div class="mb-4 text-gray-500">
                    <!-- <svg class="mx-auto mb-4 w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.5-.647-6.364-1.773M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg> -->


                    <svg class="mx-auto mb-4 w-16 h-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="m6.72 5.66 11.62 11.62A8.25 8.25 0 0 0 6.72 5.66Zm10.56 12.68L5.66 6.72a8.25 8.25 0 0 0 11.62 11.62ZM5.105 5.106c3.807-3.808 9.98-3.808 13.788 0 3.808 3.807 3.808 9.98 0 13.788-3.807 3.808-9.98 3.808-13.788 0-3.808-3.807-3.808-9.98 0-13.788Z" clip-rule="evenodd" />
                    </svg>

                </div>
                <h3 class="mb-2 font-semibold text-gray-700 text-xl"><?php echo $message; ?></h3>
                <p class="text-gray-500">
                    <?php _e('Try adjusting your search criteria or check for spelling errors.', CD_TEXT_DOMAIN); ?></p>
            </div>


<?php
        }
        wp_reset_postdata();
        wp_send_json_success(ob_get_clean());
    }
}
//         wp_reset_postdata();
//         wp_send_json_success(ob_get_clean());
//     }
// }
//         wp_reset_postdata();
//         wp_send_json_success(ob_get_clean());
//     }
// }       wp_send_json_success(ob_get_clean());
//     }
// }   }
// }       wp_send_json_success(ob_get_clean());
//     }
// }   }
// }       wp_send_json_success(ob_get_clean());
//     }
// }   }
// }       wp_send_json_success(ob_get_clean());
//     }
// }