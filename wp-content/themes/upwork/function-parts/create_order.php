<?php
add_action('wp_ajax_create_order', 'createOrder');
add_action('wp_ajax_nopriv_create_order', 'createOrder');

function createOrder()
{
    if ($_POST) {
        $books = $_POST['books'];

        $total = $_POST['total'];
        if ($total) {
            $total = str_replace(['$', ' '], '', $total);
        }
        $coupon = $_POST['coupon'];
        $tax = $_POST['tax'];
        if ($tax) {
            $tax = str_replace(['$', ' '], '', $tax);
        }
        $postArr = [
            'post_title' => $_POST['firstName'] . ' ' . $_POST['lastName'],
            'post_status' => 'publish',
            'post_type' => 'orders',
        ];

        $postId = wp_insert_post($postArr);

        $currentUser = wp_get_current_user();

        update_post_meta($postId, 'books', json_encode($books));
        update_post_meta($postId, 'total', $total);
        update_post_meta($postId, 'coupon', $coupon);
        update_post_meta($postId, 'tax', $tax);
        update_post_meta($postId, 'status', 'In process');
        update_post_meta($postId, 'shipping_level', $_POST['shipping_level']);

        update_post_meta($postId, 'first_name', $_POST['firstName']);
        update_post_meta($postId, 'last_name', $_POST['lastName']);
        update_post_meta($postId, 'country', $_POST['country']);
        update_post_meta($postId, 'street', $_POST['street']);
        update_post_meta($postId, 'state_code', $_POST['apartment']);
        update_post_meta($postId, 'city', $_POST['city']);
        update_post_meta($postId, 'post_code', $_POST['postcode']);
        update_post_meta($postId, 'phone', $_POST['phone']);
        update_post_meta($postId, 'email', $_POST['email']);
        update_post_meta($postId, 'user_id', $currentUser->ID);

        delete_user_meta($currentUser->ID, 'cart');

        setcookie('post_id', $postId, time() + 600, '/');
    } else {
        $postId = 0;
    }
    echo json_encode($postId);
    die();
}

add_action('wp_ajax_get_order_price', 'getOrderPrice');
add_action('wp_ajax_nopriv_get_order_price', 'getOrderPrice');

function getOrderPrice()
{

    if ($_POST) {
        $url = "#";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Authorization: Basic ",
            "Content-Type: application/x-www-form-urlencoded",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = "grant_type=client_credentials";

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $accessObj = json_decode($resp);
        $aceessToken = $accessObj->access_token;


        $url = "#";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Authorization: Bearer " . $aceessToken,
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $lulu_obj = $_POST['lulu_obj'];

        $data = json_encode($lulu_obj);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        // $response = json_encode($_POST['lulu_obj']);
        echo $resp;
    }
    die();
}

function createLuluOrder($data, $user_id = null)
{

    $token_url = "#";
    $token_args = array(
        'headers' => array(
            'Authorization' => 'Basic #',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ),
        'body' => array(
            'grant_type' => 'client_credentials'
        ),
        'sslverify' => false
    );
    $token_response = wp_remote_post($token_url, $token_args);

    if (!is_wp_error($token_response)) {
        $access_token = json_decode(wp_remote_retrieve_body($token_response))->access_token;

        $print_job_url = "#";
        $print_job_args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($data),
            'sslverify' => false
        );
        $print_job_response = wp_remote_post($print_job_url, $print_job_args);

        if (!is_wp_error($print_job_response)) {

            if ($user_id) {
                saveUserData(json_decode($print_job_args['body']), $user_id);
            } else {
                saveUserData(json_decode($print_job_args['body']), get_current_user_id());
            }

            error_log(wp_remote_retrieve_body($print_job_response));
        } else {
            error_log('Помилка у виконанні запиту на створення друку: ' . $print_job_response->get_error_message());
        }
    } else {
        error_log('Помилка у виконанні запиту на отримання токена доступу: ' . $token_response->get_error_message());
    }
}

function saveUserData($data, $user_id): void
{
    update_user_meta($user_id, 'checkout_street', $data->shipping_address->street1);
    update_user_meta($user_id, 'checkout_apartment', $data->shipping_address->state_code);
    update_user_meta($user_id, 'checkout_city', $data->shipping_address->city);
    update_user_meta($user_id, 'checkout_postcode', $data->shipping_address->postcode);
    update_user_meta($user_id, 'checkout_phone', $data->shipping_address->phone_number);
}