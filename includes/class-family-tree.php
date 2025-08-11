<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class CD_Family_Tree
{
    public static function register()
    {
        // No additional actions needed; Treant.js is handled via JS file.
    }

    public static function get_tree_data($post_id)
    {
        $head_details = get_post_meta($post_id, 'cd_head_details', true);
        $members = get_post_meta($post_id, 'cd_family_members', true) ?: array();
        $tree = array(
            'text' => array('name' => $head_details['name']),
            'children' => array(),
        );
        foreach ($members as $member) {
            $tree['children'][] = array(
                'text' => array(
                    'name'  => $member['name'],
                    'title' => $member['relation'],
                    'desc'  => $member['gender'] . ', ' . $member['education']
                ),
            );
        }
        return $tree;
    }
}