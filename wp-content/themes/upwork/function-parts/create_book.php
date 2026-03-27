<?php
add_action('wp_ajax_create_book', 'updateBook');
add_action('wp_ajax_nopriv_create_book', 'updateBook');

function updateBook()
{
    if (empty($_POST['answers'])) {
        wp_send_json_error(['message' => 'No answers']);
    }

    $answers = (array)$_POST['answers'];
    $step = intval($_POST['step'] ?? 1);
    $postId = intval($_POST['postId'] ?? 0);
    $trigger = sanitize_text_field($_POST['trigger'] ?? '');
    $title = sanitize_text_field($_POST['title'] ?? '');
    $termId = intval($_POST['termId'] ?? 0);

    $guest_id = null;

    if (!is_user_logged_in() && !empty($_POST['guestId'])) {
        $guest_id = sanitize_text_field($_POST['guestId']);
    }

    $postArr = [
        'post_title' => $title,
        'post_status' => 'draft',
        'post_type' => 'books',
        'tax_input' => [
            'templates' => [$termId],
        ],
        'meta_input' => [
            'answers' => [],
            'step' => $step,
        ],
    ];

    $response = [
        'success' => false,
        'postId' => $postId,
    ];

    if ($postId > 0) {
        if (!is_user_logged_in()) {
            $post_guest_id = get_post_meta($postId, '_guest_id', true);

            if (!$post_guest_id || $post_guest_id !== $guest_id) {
                wp_send_json_error(['message' => 'Access denied']);
            }
        }

        $currentAnswers = get_post_meta($postId, 'answers', true);
        if (!is_array($currentAnswers)) {
            $currentAnswers = [];
        }

        $updatedAnswers = array_merge($currentAnswers, $answers);

        update_post_meta($postId, 'answers', $updatedAnswers);
        update_post_meta($postId, 'step', $step);

        wp_update_post([
            'ID' => $postId,
            'post_title' => $title,
        ]);

        $response['success'] = true;
        $response['result'] = 'Post updated';
        $response['code'] = 200;
    } else {
        $postId = wp_insert_post($postArr);

        if (is_wp_error($postId)) {
            wp_send_json_error(['message' => 'Insert failed']);
        }

        update_post_meta($postId, 'answers', $answers);
        update_post_meta($postId, 'step', $step);

        if ($guest_id) {
            update_post_meta($postId, '_guest_id', $guest_id);
        }

        $response['success'] = true;
        $response['answers'] = $answers;
        $response['code'] = 201;
    }

    $term = get_term($termId);

    if ($term && !is_wp_error($term)) {
        $response['postUrl'] = get_term_link($term) . '?edit-book=' . $postId;
    }

    $response['postPreview'] = get_permalink($postId);
    $response['postId'] = $postId;

    if ($trigger === 'next_step') {
        $response['nextUrl'] = $response['postUrl'] . '&step=3';
    }

    if ($trigger === 'prev_step') {
        $response['prevUrl'] = ($step == 2)
            ? $response['postUrl']
            : $response['postUrl'] . '&step=' . ($step - 1);
    }

    wp_send_json($response);
}


add_action('wp_ajax_create_book_success', 'updateSuccessBook');
add_action('wp_ajax_nopriv_create_book_success', 'updateSuccessBook');

function updateSuccessBook()
{
    $answers = $_POST['answers'] ?? [];
    $step = intval($_POST['step'] ?? 1);
    $postId = intval($_POST['postId'] ?? 0);
    $trigger = sanitize_text_field($_POST['trigger'] ?? '');
    $termId = intval($_POST['termId'] ?? 0);
    $title = sanitize_text_field($_POST['title'] ?? '');

    $normalizedAnswers = [];
    if (is_array($answers)) {
        foreach ($answers as $item) {
            if (!is_array($item)) continue;
            $cleanItem = [];
            if (isset($item['type'])) $cleanItem['type'] = sanitize_text_field($item['type']);
            if (isset($item['value'])) $cleanItem['value'] = sanitize_text_field($item['value']);
            if (!empty($cleanItem)) $normalizedAnswers[] = $cleanItem;
        }
    }

    $response = [
        'success' => false,
        'postId' => $postId,
    ];

    try {
        if ($postId > 0) {
            $update_post = [
                'ID' => $postId,
                'post_title' => $title,
                'post_status' => 'publish',
            ];

            $result = wp_update_post($update_post, true);

            if (is_wp_error($result)) {
                throw new Exception($result->get_error_message());
            }

            update_post_meta($postId, 'answers', $normalizedAnswers);
            update_post_meta($postId, 'step', $step);

            $response['success'] = true;
            $response['result'] = 'Post updated';
            $response['code'] = 200;

        } else {
            $postArr = [
                'post_title' => $title,
                'post_status' => 'publish',
                'post_type' => 'books',
                'tax_input' => [
                    'templates' => [$termId],
                ],
                'meta_input' => [
                    'answers' => $normalizedAnswers,
                    'step' => $step,
                ],
            ];

            $postId = wp_insert_post($postArr, true);

            if (is_wp_error($postId)) {
                throw new Exception($postId->get_error_message());
            }

            $response['success'] = true;
            $response['code'] = 201;
            $response['postId'] = $postId;
        }

        if ($termId) {
            $term = get_term($termId);
            if (!is_wp_error($term)) {
                $response['postUrl'] = get_term_link($term) . '?edit-book=' . $postId;
            }
        }

        $response['postPreview'] = get_permalink($postId);

        if ($trigger === 'next_step' && !empty($response['postUrl'])) {
            $response['nextUrl'] = $response['postUrl'] . '&step=3';
        }

        if ($trigger === 'prev_step' && !empty($response['postUrl'])) {
            $response['prevUrl'] = ($step === 2)
                ? $response['postUrl']
                : $response['postUrl'] . '&step=' . ($step - 1);
        }

    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
        sendTelegramMessage("Exception: " . $e->getMessage());
    }

    wp_send_json($response);
}


add_action('wp_ajax_delete_book_from_cart', 'deleteBook'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_delete_book_from_cart', 'deleteBook');

function deleteBook()
{
    $id = $_POST['id'];

    wp_delete_post($id);

    $currentUser = wp_get_current_user();

    $cartItems = get_user_meta($currentUser->ID, 'cart')[0];

    foreach ($cartItems as $key => $item) {
        if ($item[0] == $id) {
            unset($cartItems[$key]);
        }
    }

    update_user_meta($currentUser->ID, 'cart', $cartItems);
    die();
}


add_action('wp_ajax_delete_image', 'deleteImage');
add_action('wp_ajax_nopriv_delete_image', 'deleteImage');

function deleteImage()
{
    $id = $_POST['id'];

    $image_id = $_POST['image_id'];

    $post_id = $_POST['post_id'];

    $answers = get_post_meta($post_id, 'answers');

    wp_delete_attachment($image_id);

    unset($answers[0][$id]);

    update_post_meta($post_id, 'answers', $answers[0]);

    die();
}

?>