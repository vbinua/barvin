<?php

    // Register Custom Taxonomy
    function custom_taxonomy() {

        $labels = array(
            'name'                       => 'Templates',
            'singular_name'              => 'Template',
            'menu_name'                  => 'Template',
            'all_items'                  => 'All Templates',
            'new_item_name'              => 'New Template Name',
            'add_new_item'               => 'Add New Template',
            'edit_item'                  => 'Edit Template',
            'update_item'                => 'Update Template',
            'view_item'                  => 'View Template',
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'publicly_queryable'         => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'templates', array( 'books', 'quiz' ), $args );

    }
    add_action( 'init', 'custom_taxonomy', 0 );

function custom_post_type() {

    $labelsBooks = array(
		'name'                  => 'Quiz',
		'singular_name'         => 'Quiz',
		'menu_name'             => 'Quiz',
		'all_items'             => 'All Quiz',
		'add_new_item'          => 'Add new Quiz',
		'add_new'               => 'Add New',
		'new_item'              => 'New Quiz',
		'edit_item'             => 'Edit Quiz',
		'update_item'           => 'Update Quiz',
		'view_item'             => 'View Quiz',
        'taxonomies'            => array('templates')
	);
	$argsBooks = array(
		'label'                 => 'Quiz',
		'labels'                => $labelsBooks,
		'supports'              => false,
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
        'supports'              => array('title')
	);
	register_post_type( 'quiz', $argsBooks );


	$labelsBooks = array(
		'name'                  => 'Books',
		'singular_name'         => 'Book',
		'menu_name'             => 'Books',
		'all_items'             => 'All books',
		'add_new_item'          => 'Add new book',
		'add_new'               => 'Add New',
		'new_item'              => 'New Book',
		'edit_item'             => 'Edit Book',
		'update_item'           => 'Update Book',
		'view_item'             => 'View Book',
        'taxonomies'            => array('templates')
	);
	$argsBooks = array(
		'label'                 => 'Books',
		'labels'                => $labelsBooks,
		'supports'              => false,
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
        'supports'              => array('title', 'thumbnail', 'author')
	);
	register_post_type( 'books', $argsBooks );

    $labelsOrders = array(
		'name'                  => 'Orders',
		'singular_name'         => 'Order',
		'menu_name'             => 'Orders',
		'all_items'             => 'All Orders',
		'add_new_item'          => 'Add new Order',
		'add_new'               => 'Add New',
		'new_item'              => 'New Order',
		'edit_item'             => 'Edit Order',
		'update_item'           => 'Update Order',
		'view_item'             => 'View Order',
	);

	$argsOrders = array(
		'label'                 => 'Orders',
		'labels'                => $labelsOrders,
		'supports'              => false,
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
	);
	register_post_type( 'orders', $argsOrders );

}
add_action( 'init', 'custom_post_type', 0 );
