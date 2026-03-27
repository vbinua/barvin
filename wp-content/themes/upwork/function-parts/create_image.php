<?php
add_action('wp_ajax_save_image', 'file_upload_callback');
add_action('wp_ajax_nopriv_save_image', 'file_upload_callback');

function file_upload_callback()
{
    try {
        $file = $_FILES['file'] ?? null;
        if (!$file) {
            throw new Exception('No file uploaded');
        }
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
        $metadata = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        if (!$metadata || is_wp_error($metadata)) {
            throw new Exception('Failed to generate attachment metadata');
        }
        wp_update_attachment_metadata($attachment_id, $metadata);

        $post_id = intval($_POST['post_id']);
        $field_id = sanitize_text_field($_POST['id']);
        $quiz_slug = sanitize_text_field($_POST['quizSlug']);

        $tasks = get_post_meta($post_id, 'book_coloring_tasks', true);
        if (!is_array($tasks)) $tasks = [];

        $tasks[$field_id] = [
            'original' => $attachment_id,
            'watermark' => null,
            'result' => null,
            'status' => 'pending'
        ];

        update_post_meta($post_id, 'book_coloring_tasks', $tasks);

        send_coloring_task_to_node($post_id, $field_id, $quiz_slug, wp_get_attachment_url($attachment_id));

        wp_send_json_success([
            'original_url' => wp_get_attachment_url($attachment_id),
            'attachment_id' => $attachment_id
        ]);

    } catch (Exception $e) {
        sendTelegramMessage("Error in file_upload_callback: {$e->getMessage()}");
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}