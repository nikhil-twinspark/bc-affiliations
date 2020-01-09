<?php
function bc_affiliation_register_affiliation_type() {
    $labels = array( 
        'name' => __( 'Affiliations', BCAFFILIATIONDOMAIN ),
        'singular_name' => __( 'Affiliation', BCAFFILIATIONDOMAIN ),
        'archives' => __( 'Affiliation', BCAFFILIATIONDOMAIN ),
        'add_new' => __( 'Add New Affiliation', BCAFFILIATIONDOMAIN ),
        'add_new_item' => __( 'Add New Affiliation', BCAFFILIATIONDOMAIN ),
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'has_archive' => 'affiliation',
        'rewrite' => array( 'has_front' => true ),
        'menu_icon' => 'dashicons-groups',
        'supports' => false,
        'show_in_rest' => true,
    );
    register_post_type( 'bc_affiliations', $args );
}
