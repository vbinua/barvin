<?php
function coloring_save_uploaded_file(array $file, int $post_id, string $field_id)
{
    if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        throw new Exception('Temporary file not found');
    }

    $file_content = file_get_contents($file['tmp_name']);
    if ($file_content === false) {
        throw new Exception('Failed to read uploaded file content');
    }

    $upload = wp_upload_bits($file['name'], null, $file_content);
    if ($upload['error']) {
        throw new Exception('Upload error: ' . $upload['error']);
    }

    $attachment = [
        'post_mime_type' => $file['type'],
        'post_title' => sanitize_file_name($file['name']),
        'post_status' => 'inherit'
    ];

    $attachment_id = wp_insert_attachment($attachment, $upload['file']);
    if (!$attachment_id || is_wp_error($attachment_id)) {
        throw new Exception('Failed to create attachment');
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata(
        $attachment_id,
        wp_generate_attachment_metadata($attachment_id, $upload['file'])
    );

    $tasks = get_post_meta($post_id, 'book_coloring_tasks', true);
    if (!is_array($tasks)) {
        $tasks = [];
    }

    $tasks[$field_id] = [
        'original' => $attachment_id,
        'watermark' => null,
        'result' => null,
        'status' => 'pending',
        'counted_ids' => []
    ];

    update_post_meta($post_id, 'book_coloring_tasks', $tasks);

    return [
        'field_id' => $field_id,
        'attachment_id' => $attachment_id,
        'original_url' => wp_get_attachment_url($attachment_id),
    ];
}

function send_coloring_task_to_node(array $tasks)
{
    if (empty($tasks)) {
        return;
    }

    try {
        $body = [];

        foreach ($tasks as $task) {
            $body[] = [
                'images' => [$task['image_url']],
                'clientId' => 'Barvin',
                'orderId' => (string)$task['post_id'],
                'domain' => get_site_url(),
                'acfField' => $task['field_id'],
                'quizSlug' => $task['quizSlug'],
                'postId' => $task['post_id'],
            ];
        }

        $response = wp_remote_post(COLORINGENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . COLORINGKEY
            ],
            'body' => wp_json_encode($body),
            'timeout' => 120
        ]);

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

    } catch (Exception $e) {
        sendTelegramMessage("Error in send_coloring_tasks_to_node_batch: {$e->getMessage()}");
    }
}

add_filter('manage_users_columns', function ($columns) {
    $columns['coloring_trial_left'] = 'Trial left';
    return $columns;
});

add_filter('manage_users_custom_column', function ($value, $column_name, $user_id) {

    if ($column_name !== 'coloring_trial_left') {
        return $value;
    }

    $used = (int)get_user_meta($user_id, 'coloring_trial_requests', true);

    return sprintf(
        '%d / %d',
        $used,
        COLORINGTRIALREQUESTLIMIT
    );

}, 10, 3);

add_action('admin_head', function () {
    echo '<style>
        .column-coloring_trial_left {
            font-weight: bold;
        }
    </style>';
});

add_filter('manage_users_columns', function ($columns) {

    if (isset($columns['posts'])) {
        $columns['posts'] = 'Coloring Sets';
    }

    return $columns;
});

function escapeTelegramHtml($text)
{
    if (!$text) return '';
    return str_replace(
        ['&', '<', '>'],
        ['&amp;', '&lt;', '&gt;'],
        $text
    );
}

function sendTelegramMessage($message)
{
    $chatId = '-5096821437';
    $token = '8592731096:AAEFIXYDfz0vfqDCwTvI38UzVz5vqUJyrM4';

    if (!$chatId || !$token) {
        error_log('Telegram env variables not set');
        return false;
    }

    $url = "https://api.telegram.org/bot{$token}/sendMessage";

    $safeMessage = mb_substr((string)$message, 0, 4000);
    $safeMessage = escapeTelegramHtml($safeMessage);

    $data = [
        'chat_id' => $chatId,
        'text' => $safeMessage,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_TIMEOUT => 80
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        error_log('Telegram cURL error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    return true;
}

add_action('wp_ajax_get_unlocked_colorings', 'get_unlocked_colorings');
add_action('wp_ajax_nopriv_get_unlocked_colorings', 'get_unlocked_colorings');

function get_unlocked_colorings()
{
    check_ajax_referer('nonce', 'nonce');
    $post_id = intval($_POST['post_id']);

    $is_paid = get_post_meta($post_id, 'is_coloring_paid', true);

    if (!$is_paid) {
        wp_send_json_error(['message' => 'Not paid yet']);
    }

    $tasks = get_post_meta($post_id, 'book_coloring_tasks', true);
    $clean_urls = [];

    if (is_array($tasks)) {
        foreach ($tasks as $task) {
            if (!empty($task['result']) && is_array($task['result'])) {
                foreach ($task['result'] as $attachment_id) {
                    $url = wp_get_attachment_url($attachment_id);
                    if ($url) {
                        $clean_urls[] = $url;
                    }
                }
            }
        }
    }

    wp_send_json_success(['urls' => $clean_urls]);
}
