<?php
add_action('wp_ajax_create_colorings', 'create_colorings');
add_action('wp_ajax_nopriv_create_colorings', 'create_colorings');
function create_colorings()
{
    try {
        check_ajax_referer('nonce', 'nonce');

        $guest_id = sanitize_text_field($_POST['guest_id'] ?? '');
        $post_id = intval($_POST['post_id'] ?? 0);
        $quiz_slug = sanitize_text_field($_POST['quizSlug'] ?? '');
        $count_to_create = intval($_POST['count'] ?? 0);

        $is_append = isset($_POST['is_append']) ? intval($_POST['is_append']) : 0;

        if (!$post_id || empty($_FILES['files'])) {
            throw new Exception('Invalid request data');
        }

        $existing_tasks = get_post_meta($post_id, 'book_coloring_tasks', true);

        $files = $_FILES['files'];
        $results = [];
        $tasks_for_node = [];

        $limit = $count_to_create > 0 ? $count_to_create : PHP_INT_MAX;
        $processed = 0;

        if ($is_append === 0 && is_array($existing_tasks) && !empty($existing_tasks)) {
            foreach ($existing_tasks as $task) {
                if (!empty($task['original'])) {
                    wp_delete_attachment((int)$task['original'], true);
                }
                if (!empty($task['watermark'])) {
                    wp_delete_attachment((int)$task['watermark'], true);
                }
                if (!empty($task['result'])) {
                    wp_delete_attachment((int)$task['result'], true);
                }
            }
            delete_post_meta($post_id, 'book_coloring_tasks');
            $existing_tasks = [];
        }

        foreach ($files['name'] as $index => $name) {
            if ($processed >= $limit) {
                break;
            }

            if ($files['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = [
                'name' => $files['name'][$index],
                'type' => $files['type'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'size' => $files['size'][$index],
            ];

            $field_id = uniqid('coloring_', true);

            $saved = coloring_save_uploaded_file($file, $post_id, $field_id);

            $results[] = $saved;

            $tasks_for_node[] = [
                'post_id' => $post_id,
                'field_id' => $saved['field_id'],
                'quizSlug' => $quiz_slug,
                'image_url' => $saved['original_url'],
            ];

            $processed++;
        }

        if (!$results) {
            throw new Exception('No files processed');
        }

        send_coloring_task_to_node($tasks_for_node);

        wp_send_json_success([
            'message' => 'The creation of coloring books has begun',
            'items' => $results,
            'created' => count($results)
        ]);

    } catch (Exception $e) {
        if (function_exists('sendTelegramMessage')) {
            sendTelegramMessage("Error in create_colorings: {$e->getMessage()}");
        }
        wp_send_json_error([
            'message' => $e->getMessage()
        ]);
    }
}