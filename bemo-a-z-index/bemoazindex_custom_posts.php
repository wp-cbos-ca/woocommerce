<?php
add_action( 'init', 'create_bemoazindex_custom_posts' );
function create_bemoazindex_custom_posts() {
    register_post_type( 'azindexcustom',
        array(
            'labels' => array(
                'name' => 'A-Z Index Posts',
                'singular_name' => 'A-Z Index Post',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Post',
                'edit' => 'Edit',
                'edit_item' => 'Edit Post',
                'new_item' => 'New Post',
                'view' => 'View',
                'view_item' => 'View Post',
                'search_items' => 'Search Posts',
                'not_found' => 'No Posts found',
                'not_found_in_trash' => 'No Posts found in Trash',
                'parent' => 'Parent Post'
            ),
 
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'thumbnail'  ),
            'taxonomies' => array( '' ),
            'has_archive' => true
        )
    );
}

function create_bemoazindex_custom_post_categories() {
  $labels = array(
    'name'              => _x( 'Categories', 'azindexcustom_categories' ),
    'singular_name'     => _x( 'Category', 'azindexcustom_category' ),
    'search_items'      => __( 'Search Categories' ),
    'all_items'         => __( 'All Categories' ),
    'parent_item'       => __( 'Parent Category' ),
    'parent_item_colon' => __( 'Parent Category:' ),
    'edit_item'         => __( 'Edit Category' ), 
    'update_item'       => __( 'Update Category' ),
    'add_new_item'      => __( 'Add New Category' ),
    'new_item_name'     => __( 'New Category' ),
    'menu_name'         => __( 'Categories' ),
  );
  $args = array(
    'labels' => $labels,
    'hierarchical' => true,
  );
  register_taxonomy( 'azindexcustom_category', 'azindexcustom', $args );
}

add_action( 'init', 'create_bemoazindex_custom_post_categories', 0 );
?>