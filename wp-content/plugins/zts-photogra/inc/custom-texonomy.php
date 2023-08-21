<?php
/**
 * Register Custom Texomony For Locations.
 *
 * @since    1.0
 */
add_action( 'init',  'zp_custom_location_taxonomy' );
function zp_custom_location_taxonomy() {
    $labels = array(
        'name'              => _x( 'Locations', 'taxonomy general name' ),
        'singular_name'     => _x( 'Location', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Locations' ),
        'all_items'         => __( 'All Locations' ),
        'parent_item'       => __( 'Parent Location' ),
        'parent_item_colon' => __( 'Parent Location:' ),
        'edit_item'         => __( 'Edit Location' ),
        'update_item'       => __( 'Update Location' ),
        'add_new_item'      => __( 'Add New Location' ),
        'new_item_name'     => __( 'New Location Name' ),
        'menu_name'         => __( 'Locations' ),
    );

    $args = array(
        'hierarchical'      => true, // Set to true for two levels
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'location' ),
    );

    register_taxonomy( 'location', 'post', $args );
}
