<?php if (! defined('ABSPATH')) exit; ?>
<div class="mx-auto p-4 container">
    <h2 class="mb-4 text-2xl"><?php _e('Family Tree', CD_TEXT_DOMAIN); ?></h2>
    <div id="cd-family-tree" style="height: 500px;"></div>
    <script>
    var tree_config = {
        chart: {
            container: "#cd-family-tree",
            connectors: {
                type: 'step'
            },
            node: {
                HTMLclass: 'node'
            }
        },
        nodeStructure: <?php echo json_encode(CD_Family_Tree::get_tree_data(get_query_var('family_id'))); ?>
    };
    new Treant(tree_config);
    </script>
</div>
?>