<?php

/**
 * Family Tree Template
 */
get_header();

$family_id = isset($_GET['family_id']) ? intval($_GET['family_id']) : 0;

// Load required libraries
function load_family_tree_assets()
{
    // Raphael.js must load before Treant.js
    wp_enqueue_script('raphael', 'https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js', array(), '2.3.0');
    wp_enqueue_script('treant', 'https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0.0/Treant.min.js', array('raphael'), '1.0.0');
    wp_enqueue_style('treant-css', 'https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0.0/Treant.min.css');

    // Your custom styling
    wp_add_inline_style('treant-css', '
        .family-tree-container {
            width: 100%;
            height: 600px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin: 2rem 0;
            overflow: auto;
        }
        .family-node { padding: 10px; background: white; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .group-node { font-size: 12px; color: #666; font-weight: bold; background: #e2e8f0; }
        .member-node { font-size: 14px; color: #000; }
        .family-head-node { font-weight: bold; background: #dbeafe; }
    ');
}
add_action('wp_enqueue_scripts', 'load_family_tree_assets');
?>

<div class="mx-auto px-4 py-8 container">
    <?php if ($family_id && $head_details = get_post_meta($family_id, 'cd_head_details', true)) : ?>

    <h1 class="mb-6 font-bold text-gray-800 text-3xl"><?php echo esc_html($head_details['name']); ?>'s Family Tree</h1>

    <div id="family-tree-diagram" class="family-tree-container"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Treant === 'undefined') {
            console.error('Treant.js failed to load');
            document.getElementById('family-tree-diagram').innerHTML =
                '<div class="p-4 text-red-600 text-center">Error: Visualization library failed to load. Please refresh the page.</div>';
            return;
        }

        try {
            // Use the backend tree data with proper hierarchy
            const treeData = <?php echo json_encode(CD_Family_Tree::get_tree_data($family_id)); ?>;

            const config = {
                chart: {
                    container: "#family-tree-diagram",
                    levelSeparation: 40,
                    nodeAlign: "CENTER",
                    scrollbar: "resize",
                    connectors: {
                        type: "step",
                        style: {
                            "stroke-width": 2,
                            "stroke": "#94a3b8"
                        }
                    },
                    node: {
                        HTMLclass: "family-node",
                        collapsable: true
                    }
                },
                nodeStructure: treeData
            };

            new Treant(config);

        } catch (error) {
            console.error('Tree initialization error:', error);
            document.getElementById('family-tree-diagram').innerHTML =
                '<div class="p-4 text-red-600 text-center">Could not render family tree. ' + error.message +
                '</div>';
        }
    });
    </script>

    <?php else : ?>
    <div class="bg-yellow-100 p-4 border border-yellow-300 rounded alert alert-warning">
        No family selected or invalid family ID.
    </div>
    <?php endif; ?>
</div>

<?php
get_footer();
