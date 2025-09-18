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

        // Categorize members by hierarchy level
        $grandparents = array();
        $parents = array();
        $siblings = array();
        $spouse = array();
        $children = array();
        $grandchildren = array();
        $others = array();

        foreach ($members as $member) {
            $relation = strtolower($member['relation']);

            switch ($relation) {
                case 'grandmother':
                case 'grandfather':
                    $grandparents[] = $member;
                    break;
                case 'mother':
                case 'father':
                    $parents[] = $member;
                    break;
                case 'brother':
                case 'sister':
                    $siblings[] = $member;
                    break;
                case 'wife':
                case 'husband':
                    $spouse[] = $member;
                    break;
                case 'son':
                case 'daughter':
                    $children[] = $member;
                    break;
                case 'grandson':
                case 'granddaughter':
                    $grandchildren[] = $member;
                    break;
                default:
                    $others[] = $member;
                    break;
            }
        }

        // Build tree with head as root
        $tree = array(
            'text' => array('name' => $head_details['name'], 'title' => 'Family Head'),
            'HTMLclass' => 'family-head-node',
            'children' => array()
        );

        $groups = array(
            'Grandparents' => $grandparents,
            'Parents' => $parents,
            'Siblings' => $siblings,
            'Spouse' => $spouse,
            'Children' => $children,
            'Grandchildren' => $grandchildren,
            'Others' => $others
        );

        foreach ($groups as $group_name => $members_list) {
            if (!empty($members_list)) {
                $group_node = array(
                    'text' => array('name' => $group_name),
                    'HTMLclass' => 'group-node',
                    'children' => array()
                );
                foreach ($members_list as $member) {
                    $group_node['children'][] = array(
                        'text' => array('name' => $member['name'], 'title' => $member['relation']),
                        'HTMLclass' => 'member-node',
                        'children' => array()
                    );
                }
                $tree['children'][] = $group_node;
            }
        }

        return $tree;
    }

    public static function get_ajax_tree_data()
    {
        if (!isset($_POST['family_id']) || !wp_verify_nonce($_POST['nonce'], 'cd_nonce')) {
            wp_die(__('Security check failed', CD_TEXT_DOMAIN));
        }

        $family_id = intval($_POST['family_id']);

        // Check if family exists and is approved
        if (get_post_status($family_id) !== 'publish') {
            wp_die(__('Family not found or not approved', CD_TEXT_DOMAIN));
        }

        $tree_data = self::get_tree_data($family_id);

        wp_send_json_success($tree_data);
    }
}