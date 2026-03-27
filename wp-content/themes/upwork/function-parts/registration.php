<?php
add_action('wp_ajax_user_register_action', 'registerNewUser');
add_action('wp_ajax_nopriv_user_register_action', 'registerNewUser');

function registerNewUser()
{
    if (empty($_POST)) {
        wp_send_json_error(['message' => 'No data']);
    }

    $name = sanitize_text_field($_POST['name'] ?? '');
    $last_name = sanitize_text_field($_POST['last_name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $guest_id = sanitize_text_field($_POST['guestId'] ?? '');
    $term_id = sanitize_text_field($_POST['termId'] ?? '');
    $post_id = sanitize_text_field($_POST['postId'] ?? '');

    $errors = [];

    if (empty($name) || empty($last_name) || empty($email) || empty($password)) {
        $errors[] = 'All fields are required';
    }

    if (email_exists($email)) {
        $errors[] = 'Email already exists';
    }

    if (!empty($errors)) {
        wp_send_json([
            'status' => 'error',
            'data' => $errors,
        ]);
    }

    $userdata = [
        'user_login' => $email,
        'user_email' => $email,
        'user_pass' => $password,
        'display_name' => $name,
        'role' => 'contributor',
    ];

    $user_id = wp_insert_user($userdata);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => 'User creation failed']);
    }

    update_user_meta($user_id, 'first_name', $name);
    update_user_meta($user_id, 'last_name', $last_name);
    update_user_meta($user_id, 'cart', []);

    wp_signon([
        'user_login' => $email,
        'user_password' => $password,
        'remember' => true,
    ]);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    if (!empty($guest_id)) {
        $books = get_posts([
            'post_type' => 'books',
            'numberposts' => -1,
            'post_status' => ['draft', 'publish'],
            'meta_key' => '_guest_id',
            'meta_value' => $guest_id,
        ]);

        foreach ($books as $book) {
            wp_update_post([
                'ID' => $book->ID,
                'post_author' => $user_id,
            ]);

            delete_post_meta($book->ID, '_guest_id');
        }
    }

    if (!empty($term_id) && !empty($post_id)) {
        $post = get_post($post_id);

        if ($post && $post->post_type === 'books') {
            $postArr = [
                'ID' => $post_id,
                'post_type' => 'books',
                'tax_input' => [
                    'templates' => [$term_id],
                ],
            ];

            wp_update_post($postArr);
        }
    }

    sendMail($user_id, $email);

    wp_send_json([
        'status' => 'success',
        'user_id' => $user_id,
        '$guest_id' => $guest_id,
        '$books' => $books,
    ]);
}

function sendMail($user_id, $email)
{
    $first_name = get_user_meta($user_id, 'first_name', true);

    $subject = get_field('subject_welcome', 'option');
    $message = get_field('body_welcome', 'option');

    $message = strtr($message, [
        '[first_name]' => $first_name,
    ]);

    add_filter('wp_mail_content_type', function () {
        return 'text/html';
    });

    wp_mail($email, $subject, $message);
}

add_action('wp_ajax_user_logout', 'logoutUser');
add_action('wp_ajax_nopriv_user_logout', 'logoutUser');

function logoutUser()
{
    wp_logout();
    wp_send_json_success();
}

add_action('wp_ajax_update_user_info', 'updateInfo');
add_action('wp_ajax_nopriv_update_user_info', 'updateInfo');

function updateInfo()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not authorized']);
    }

    $current_user = wp_get_current_user();

    update_user_meta($current_user->ID, 'first_name', sanitize_text_field($_POST['first_name']));
    update_user_meta($current_user->ID, 'last_name', sanitize_text_field($_POST['last_name']));
    update_user_meta($current_user->ID, 'checkout_street', sanitize_text_field($_POST['checkout_street']));
    update_user_meta($current_user->ID, 'checkout_city', sanitize_text_field($_POST['checkout_city']));
    update_user_meta($current_user->ID, 'checkout_postcode', sanitize_text_field($_POST['checkout_postcode']));
    update_user_meta($current_user->ID, 'checkout_phone', sanitize_text_field($_POST['checkout_phone']));
    update_user_meta($current_user->ID, 'checkout_apartment', sanitize_text_field($_POST['checkout_apartment']));

    wp_update_user([
        'ID' => $current_user->ID,
        'user_email' => sanitize_email($_POST['email']),
        'display_name' => sanitize_text_field($_POST['first_name']) . ' ' . sanitize_text_field($_POST['last_name']),
    ]);

    wp_send_json_success();
}