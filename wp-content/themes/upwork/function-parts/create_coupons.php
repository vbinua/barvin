<?php

function create_coupons_post_type(): void
{
    $labels = array(
        'name'               => 'Coupons',
        'singular_name'      => 'Coupon',
        'menu_name'          => 'Coupons',
        'name_admin_bar'     => 'Coupons',
        'add_new'            => 'Add new coupon',
        'add_new_item'       => 'Add new coupon',
        'new_item'           => 'New coupon',
        'edit_item'          => 'Edit coupon',
        'view_item'          => 'View coupon',
        'all_items'          => 'All coupons',
        'search_items'       => 'Search coupon',
        'parent_item_colon'  => 'Parent coupon:',
        'not_found'          => 'Coupons not found.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array('title')
    );

    register_post_type( 'coupons', $args );
}

add_action( 'init', 'create_coupons_post_type' );

add_action('wp_ajax_set_coupon', 'set_coupon');

function set_coupon()
{
    $couponValue = $_POST['couponCode'] ?? null;

    if($couponValue)
    {
        $args = array(
            'post_type' => 'coupons',
            's' => $couponValue,
            'posts_per_page' => 1,
            'exact' => true,
        );

        $query = new WP_Query($args);

        if($query->have_posts())
        {
            $coupon = $query->posts[0];

            $coupon_id = $coupon->ID;

            $status = get_field('status',$coupon_id);

            if($status == 'on')
            {
                $expire_date = get_field('expire_date',$coupon_id);

                $timestamp_expire = strtotime(str_replace('/', '-', $expire_date));

                $current_date = time();

                $last_valid_second = strtotime('tomorrow', $timestamp_expire) - 1;

                if ($current_date <= $last_valid_second)
                {
                    $type = get_field('type',$coupon_id);

                    if($type == 'fixed')
                    {
                        wp_send_json(
                            [
                                'status' => 'success',
                                'message'=> 'Coupon is applied',
                                'type'   => 'fixed',
                                'discount'  => get_field('discount_amount',$coupon_id)
                            ]);
                    }
                    else
                    {
                        wp_send_json(
                            [
                                'status' => 'success',
                                'message'=> 'Coupon is applied',
                                'type'   => 'percentage',
                                'discount_percent'  => get_field('percent_discount',$coupon_id)
                            ]);
                    }
                }
                else
                {
                    wp_send_json(
                        [
                            'status' => 'error',
                            'message'=> 'Coupon is expired'
                        ]);
                }
            }
            else
            {
                wp_send_json(
                    [
                        'status' => 'error',
                        'message'=> 'Coupon is disabled'
                    ]);
            }
        }
        else
        {
            wp_send_json(
                [
                    'status' => 'error',
                    'message'=> 'Coupon not found'
                ]);
        }
    }
}