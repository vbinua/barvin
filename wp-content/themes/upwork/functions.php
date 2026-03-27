<?php
/**
 * upwork functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package upwork
 */

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

function upwork_setup()
{
    load_theme_textdomain('upwork', get_template_directory() . '/languages');

    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    register_nav_menus(
        array(
            'menu-1' => esc_html__('Primary', 'upwork'),
        )
    );
}

add_action('after_setup_theme', 'upwork_setup');

/**
 * Enqueue scripts and styles.
 */
function upwork_scripts()
{
    wp_enqueue_script('plupload', 'https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.5/plupload.full.min.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_style('select2', get_stylesheet_directory_uri() . '/styles/select2.css', [], _S_VERSION);
    wp_enqueue_style('slick-style', get_stylesheet_directory_uri() . '/styles/style.css', [], _S_VERSION);
    wp_enqueue_style('upwork-custom', get_stylesheet_directory_uri() . '/styles/custom.css', [], _S_VERSION);

    wp_enqueue_script('jquery');

    wp_enqueue_script('valid', get_template_directory_uri() . '/js/pristine.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('select2', get_template_directory_uri() . '/js/modules/select2.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('pdf-script', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js', [], _S_VERSION, true);
    wp_enqueue_script('slick-slider', get_template_directory_uri() . '/js/modules/slick.min.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('main-script', get_template_directory_uri() . '/js/settings/main.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('login-script', get_template_directory_uri() . '/js/user_login.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('cart-script', get_template_directory_uri() . '/js/cart.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('create_book', get_template_directory_uri() . '/js/create_book.js', ['plupload'], _S_VERSION, true);
    wp_enqueue_script('create_order', get_template_directory_uri() . '/js/create_order.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('create_pdf', get_template_directory_uri() . '/js/create_pdf.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('js_to_pdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', [], _S_VERSION, true);
    wp_enqueue_script('dom_to_image', 'https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js', [], _S_VERSION, true);
    wp_enqueue_script('html2canvas', get_template_directory_uri() . '/js/modules/html2canvas.min.js', ['jquery'], _S_VERSION, true);
    wp_enqueue_script('stripe', 'https://js.stripe.com/v3/', [], null, true);
    wp_enqueue_script('functions', get_template_directory_uri() . '/js/functions.js', ['jquery', 'stripe'], _S_VERSION, true);

    $templates = get_terms([
        'taxonomy' => 'templates',
        'hide_empty' => false,
    ]);

    $template_data = [];

    if (!is_wp_error($templates)) {
        foreach ($templates as $template) {
            $price = get_field('field_624362b562548', 'templates_' . $template->term_id);
            $template_data[$template->term_id] = [
                'slug' => $template->slug,
                'price' => $price ? $price : 0
            ];
        }
    }

    $ajax_data = [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'themeUrl' => get_template_directory_uri(),
        'pColoring' => $template_data,
        'stripePublicKey' => get_field("public_key", 'option'),
        'nonce' => wp_create_nonce('nonce')
    ];

    $scripts = [
        'valid',
        'select2',
        'slick-slider',
        'main-script',
        'login-script',
        'cart-script',
        'create_book',
        'create_order',
        'create_pdf',
        'html2canvas',
        'functions',
    ];

    foreach ($scripts as $handle) {
        wp_localize_script($handle, 'ajaxData', $ajax_data);
    }
}

add_action('wp_enqueue_scripts', 'upwork_scripts');


if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Theme General Settings',
        'menu_title' => 'Theme Settings',
        'menu_slug' => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect' => false
    ));
    acf_add_options_page(array(
        'page_title' => 'Email Template',
        'menu_title' => 'Email Template',
        'menu_slug' => 'email-template',
        'capability' => 'edit_posts',
        'redirect' => false
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Theme Header Settings',
        'menu_title' => 'Header',
        'parent_slug' => 'theme-general-settings',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Theme Footer Settings',
        'menu_title' => 'Footer',
        'parent_slug' => 'theme-general-settings',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Book Prices',
        'menu_title' => 'Book Prices',
        'parent_slug' => 'edit.php?post_type=books',
        'capability' => 'manage_options',
        'redirect' => false,
    ));

}

add_filter('show_admin_bar', '__return_false');

add_action('admin_init', 'my_remove_menu_pages');
function my_remove_menu_pages()
{
    global $user_ID;

    if ($user_ID != 1) {
        remove_menu_page('edit.php'); // Posts
        remove_submenu_page('index.php', 'update-core.php');
        remove_menu_page('link-manager.php'); // Links
        remove_menu_page('edit-comments.php'); // Comments
        remove_menu_page('edit.php?post_type=acf-field-group'); // Pages
        remove_menu_page('edit.php?post_type=books'); // Pages
        remove_menu_page('edit.php?post_type=quiz'); // Pages
        remove_menu_page('edit.php?post_type=orders'); // Pages
        remove_menu_page('plugins.php'); // Plugins
        remove_menu_page('themes.php'); // Appearance
        remove_menu_page('users.php'); // Users
        remove_menu_page('tools.php'); // Tools
        remove_menu_page('options-general.php'); // Settings
    }
}

add_action('before_delete_post', function ($id) {
    $attachments = get_attached_media('', $id);
    foreach ($attachments as $attachment) {
        wp_delete_attachment($attachment->ID, 'true');
    }
});

require_once "function-parts/post_types.php";
require_once "function-parts/login_functions.php";
require_once "function-parts/registration.php";
require_once "function-parts/create_image.php";
require_once "function-parts/create_book.php";
require_once "function-parts/create_order.php";
require_once "function-parts/price_api.php";
require_once "function-parts/cart_functions.php";
require_once "function-parts/create_coupons.php";

function track_book_status_change($new_status, $old_status, $post)
{
    if ($post->post_type == 'books' && $new_status == 'publish' && $old_status != 'publish') {

        $terms = wp_get_post_terms($post->ID, 'templates');
        if (!empty($terms)) {
            create_pdf_file($terms, $post->ID);
        }
    }
}

add_action('transition_post_status', 'track_book_status_change', 10, 3);

function track_term_id_on_post_update($post_ID)
{
    $post = get_post($post_ID);

    if ($post->post_type == 'books' && $post->post_status == 'publish') {
        $terms = wp_get_post_terms($post_ID, 'templates');
        if (!empty($terms)) {
            create_pdf_file($terms, $post_ID);
        }
    }
}

add_action('save_post', 'track_term_id_on_post_update');

function create_pdf_file($terms, $post_id): void
{
    foreach ($terms as $term) {
        if ($term->term_id == 12) {
            if (class_exists('CreatePDF')) {
                $pdf = new CreatePDF();
                $pdf->pacifier_book($post_id);
            }
        }

        if ($term->term_id == 11) {
            if (class_exists('CreatePDF')) {
                $pdf = new CreatePDF();
                $pdf->wedding_book($post_id);
            }
        }

        if ($term->term_id == 7) {
            if (class_exists('CreatePDF')) {
                $pdf = new CreatePDF();
                $pdf->the_potty($post_id);
            }
        }

        if ($term->term_id == 4) {
            if (class_exists('CreatePDF')) {
                $pdf = new CreatePDF();
                $pdf->coloring($post_id, 'watermark');
            }
        }

        if ($term->term_id == 3) {
            if (class_exists('CreatePDF')) {
                $pdf = new CreatePDF();
                $pdf->coloring($post_id, 'watermark');
            }
        }
    }
}

add_action('rest_api_init', 'stripe_callback_endpoint');

function stripe_callback_endpoint(): void
{
    register_rest_route('api/v1', '/stripe_callback', array(
        'methods' => 'POST',
        'callback' => 'stripe_callback',
    ));
}

function stripe_callback(): void
{
    $input_data = file_get_contents('php://input');
    $stripe_response = json_decode($input_data, true);
    $order_data = $stripe_response['data']['object'];
    $status = $order_data['status'];
    $success_url = $order_data['success_url'];
    $query_string = parse_url($success_url, PHP_URL_QUERY);
    parse_str($query_string, $query_params);
    $order_id = $query_params['order_id'] ?? null;

    $order = [];
    if ($order_id) {
        $order = get_post($order_id);
    }

    if ($order) {
        if ($status == 'complete') {
            $user_id = get_post_meta($order_id, 'user_id', true);
            update_post_meta($order_id, 'status', 'Completed');
            sendMailSuccess($order_id, $user_id);
            $data = fillLuluData($order_id);
            if ($data['has_print']) {
                update_post_meta($order_id, 'status', 'Printing');
                createLuluOrder($data['lulu_object'], $user_id);
            }
        } else {
            update_post_meta($order_id, 'status', 'Failed');
        }
    }
}

function sendMailSuccess($post_id, $user_id = null): void
{
    $first_name = get_post_meta($post_id, 'first_name', true);

    $last_name = get_post_meta($post_id, 'last_name', true);

    if ($user_id) {
        $user = get_user_by('id', $user_id);
    } else {
        $user = wp_get_current_user();
    }

    $email = $user->user_email;

    $tax = get_post_meta($post_id, 'tax', true);

    $total = get_post_meta($post_id, 'total', true);

    $discount = get_post_meta($post_id, 'coupon', true);

    $discount = str_replace('-', '$ -', $discount);

    $books = json_decode(get_post_meta($post_id, 'books', true), ARRAY_A);

    $subject = get_field('subjet_order', 'option');

    $message = strip_tags(get_field('body_order', 'option'));

    $book_message = '<hr>';

    $attachments = [];

    foreach ($books as $book) {
        $book_pdf_path = get_post_meta($book['id'], 'book_pdf_path', true);
        if ($book_pdf_path) {
            $attachments[] = $book_pdf_path;
        }
        $book_message .= '<p>Title: ' . $book['title'] . '</p>';
        $book_message .= '<p>Quantity: ' . $book['quontity'] . '</p>';
        $book_message .= '<p>Price: $' . sprintf("%.2f", $book['price']) . '</p>';
        /*$book_message .= '<p><a href="' . $book_pdf_url . '" download>Download PDF</a></p>';*/
        $book_message .= '<hr>';
    }

    $replacements = array(
        '[first_name]' => $first_name,
        '[last_name]' => $last_name,
        '[books]' => $book_message,
        '[tax]' => '$ ' . $tax,
        '[total]' => '$ ' . $total,
        '[discount]' => $discount,
    );

    $message = strtr($message, $replacements);

    add_filter('wp_mail_content_type', function () {
        return 'text/html';
    });

    wp_mail($email, $subject, $message, '', $attachments);

    $static_email = 'upandcomingbooks@gmail.com';

    wp_mail($static_email, $subject, $message, '', $attachments);
}

function fillLuluData($post_id)
{
    $books = json_decode(get_post_meta($post_id, 'books', true), true);
    $books_array = [];
    $has_print = false;

    foreach ($books as $book) {
        if ($book['type'] == 'Print book') {
            $has_print = true;
            $books_array[] =
                [
                    'external_id' => 'item-reference-1',
                    'printable_normalization' =>
                        [
                            'cover' => [
                                'source_url' => get_permalink($book['id']) . '?print=cover'
                            ],
                            'interior' =>
                                [
                                    'source_url' => get_permalink($book['id']) . '?print=true'
                                ],
                            'pod_package_id' => '0850X0850FCPRESS080CW444MXX',
                        ],
                    'quantity' => $book['quontity'],
                    'title' => $book['title']
                ];
        }
    }
    $first_name = get_post_meta($post_id, 'first_name', true);
    $last_name = get_post_meta($post_id, 'last_name', true);
    $data['lulu_object'] = [
        'contact_email' => get_post_meta($post_id, 'email', true),
        'external_id' => $first_name . ' ' . $last_name . ' Books',
        'line_items' => $books_array,
        'production_delay' => 480,
        'shipping_address' =>
            [
                'city' => get_post_meta($post_id, 'city', true),
                'country_code' => get_post_meta($post_id, 'country', true),
                'name' => $first_name . ' ' . $last_name,
                'phone_number' => get_post_meta($post_id, 'phone', true),
                'postcode' => get_post_meta($post_id, 'post_code', true),
                'state_code' => get_post_meta($post_id, 'state_code', true),
                'street1' => get_post_meta($post_id, 'street', true)
            ],
        'shipping_level' => get_post_meta($post_id, 'shipping_level', true),
    ];
    $data['has_print'] = $has_print;
    return $data;
}

function schedule_order_check($post_id)
{
    if (get_post_type($post_id) === 'orders') {
        wp_schedule_single_event(time() + 1800, 'check_order_status_event', array($post_id));
    }
}

add_action('save_post', 'schedule_order_check');

function check_order_status_callback($order_id)
{
    $status = get_post_meta($order_id, 'status', true);

    if ($status === 'In process') {
        update_post_meta($order_id, 'status', 'Failed');
    }
}

add_action('check_order_status_event', 'check_order_status_callback');

add_action('add_meta_boxes', function () {
    add_meta_box(
        'book_coloring_tasks_box',
        'Coloring book images',
        'render_book_coloring_tasks_box',
        null,
        'normal',
        'default'
    );
});

function render_book_coloring_tasks_box($post)
{
    $tasks = get_post_meta($post->ID, 'book_coloring_tasks', true);

    if (empty($tasks) || !is_array($tasks)) {
        echo '<p><em>No coloring tasks found.</em></p>';
        return;
    }

    echo '<style>
        .coloring-task {
            display: flex;
            gap: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #ddd;
        }
        .coloring-task img {
            max-width: 150px;
            height: auto;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }
        .coloring-task .meta {
            font-size: 13px;
        }
        .coloring-task .status {
            font-weight: bold;
        }
        .status.done { color: green; }
        .status.pending { color: orange; }
        
        .coloring-author{position: absolute; background: #318934; display: inline-block; padding: 5px 10px; color: #ffffff; font-weight: 400; right: 10px;}
    </style>';

    echo '<div class="coloring-author">';
    $author_id = $post->post_author;
    if ($author_id && $author_id != 0) {
        $user = get_userdata($author_id);

        if ($user) {
            $first_name = get_user_meta($author_id, 'first_name', true);
            $last_name = get_user_meta($author_id, 'last_name', true);

            if ($first_name || $last_name) {
                $author_name = trim($first_name . ' ' . $last_name);
            } else {
                $author_name = $user->display_name;
            }

            echo '<strong>Author:</strong> ' . esc_html($author_name);
        }
    } else {
        $guestId = get_post_meta($post->ID, '_guest_id', true);
        if ($guestId) {
            echo '<strong>Author:</strong> Guest ';
        }
    }
    echo '</div>';

    foreach ($tasks as $field_id => $task) {
        $original_id = $task['original'] ?? null;
        $result_ids = $task['result'] ?? [];
        $status = $task['status'] ?? 'unknown';

        $original_url = $original_id ? wp_get_attachment_url($original_id) : '';

        echo '<div class="coloring-task">';
        echo '<div>';
        echo '<strong>Original</strong><br>';
        if ($original_url) {
            echo '<img src="' . esc_url($original_url) . '">';
        } else {
            echo '<em>—</em>';
        }
        echo '</div>';

        echo '<div>';
        echo '<strong>Result</strong><br>';
        if (!empty($result_ids) && is_array($result_ids)) {
            foreach ($result_ids as $rid) {
                $url = wp_get_attachment_url($rid);
                if ($url) {
                    echo '<img src="' . esc_url($url) . '" style="margin-bottom:5px;">';
                }
            }
        } else {
            echo '<em>Not ready</em>';
        }
        echo '</div>';

        echo '<div class="meta">';
        echo '<div><strong>Field ID:</strong> ' . esc_html($field_id) . '</div>';
        echo '<div class="status ' . esc_attr($status) . '">Status: ' . esc_html($status) . '</div>';
        echo '<div>Original ID: ' . esc_html($original_id) . '</div>';
        echo '<div>Result IDs: ' . (!empty($result_ids) ? implode(', ', $result_ids) : '—') . '</div>';
        echo '</div>';

        echo '</div>';
    }
}

require_once get_template_directory() . '/inc/coloring/coloring-api.php';
require_once get_template_directory() . '/inc/coloring/additional-functions.php';
require_once get_template_directory() . '/inc/coloring/checking.php';
require_once get_template_directory() . '/inc/coloring/generation.php';
require_once get_template_directory() . '/inc/coloring/process-payment.php';

//
//$user_id = 1;
//update_user_meta($user_id, 'coloring_trial_requests', 3);

