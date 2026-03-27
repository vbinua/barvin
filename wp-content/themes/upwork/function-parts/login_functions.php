<?php
add_action('wp_ajax_user_login_action', 'LoginUser');
add_action('wp_ajax_nopriv_user_login_action', 'LoginUser');

function LoginUser()
{
    $user_mail = sanitize_email($_POST['email'] ?? '');
    $user_pass = $_POST['password'] ?? '';
    $guest_id = sanitize_text_field($_POST['guestId'] ?? '');
    $term_id = sanitize_text_field($_POST['termId'] ?? '');
    $post_id = sanitize_text_field($_POST['postId'] ?? '');

    $errors = [];

    if (empty($user_mail)) {
        $errors[] = ['user_email', 'Email is required'];
    }

    if (empty($user_pass)) {
        $errors[] = ['user_pass', 'Password is required'];
    }

    if (!empty($errors)) {
        echo json_encode($errors);
        wp_die();
    }

    if (!email_exists($user_mail)) {
        echo json_encode([['user_email', 'Dont find user with this email']]);
        wp_die();
    }

    $user = wp_authenticate_email_password(null, $user_mail, $user_pass);

    if (is_wp_error($user)) {
        echo json_encode([['user_pass', 'Wrong password']]);
        wp_die();
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

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
                'post_author' => $user->ID,
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

    echo json_encode([
        'status' => 'success',
        'user_id' => $user->ID,
    ]);

    wp_die();
}

function validate_email_check($emailAddress)
{
    if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) === false) {
        return false;
    }
    $domain = explode("@", $emailAddress, 2);
    return checkdnsrr($domain[1]);
}

add_action('wp_ajax_user_mail', 'SendEmail');
add_action('wp_ajax_nopriv_user_mail', 'SendEmail');

function SendEmail()
{
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    $to = '#';
    $subject = 'Contact form request';

    $email_message = "
        Name: {$name}<br>
        Email: {$email}<br>
        Message: {$message}
    ";

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: Up&Coming <test12df432abc@gmail.com>'
    ];

    wp_mail($to, $subject, $email_message, $headers);

    echo json_encode('success');
    wp_die();
}

add_action('wp_ajax_user_reset_pass', 'SendPass');
add_action('wp_ajax_nopriv_user_reset_pass', 'SendPass');

function SendPass()
{
    $username = sanitize_email($_POST['email'] ?? '');
    $errors = [];

    if (empty($username)) {
        $errors[] = ['user_email1', 'Email is required'];
    }

    if (!validate_email_check($username)) {
        $errors[] = ['user_email1', 'Email is not valid'];
    }

    if (!email_exists($username)) {
        $errors[] = ['user_email1', 'Dont find user with this email'];
    }

    if (!empty($errors)) {
        echo json_encode($errors);
        wp_die();
    }

    $user = get_user_by('email', $username);
    $new_pass = wp_generate_password(8, true, false);
    wp_set_password($new_pass, $user->ID);

    $subject = get_field('reset_pass_subject', 'option');
    $message = get_field('body_reset_pass', 'option');

    $message = strtr($message, [
        '[password]' => $new_pass,
    ]);

    add_filter('wp_mail_content_type', function () {
        return 'text/html';
    });

    wp_mail($user->user_email, $subject, $message);

    echo json_encode([]);
    wp_die();
}

add_action('wp_ajax_user_logout1', 'logoutUser1');
add_action('wp_ajax_nopriv_user_logout1', 'logoutUser1');

function logoutUser1()
{
    wp_logout();
    echo json_encode('success');
    wp_die();
}