<?php
add_action('rest_api_init', function () {
    register_rest_route('coloring/v1', '/result', [
        'methods' => 'POST',
        'callback' => 'save_coloring_result'
    ]);
});

function save_coloring_result(WP_REST_Request $request)
{
    try {
        $headers = $request->get_headers();
        $auth = $headers['authorization'][0] ?? '';
        if ($auth !== 'Bearer ' . COLORINGBACKENDKEY) {
            return new WP_Error('invalid_key', 'Invalid API key', ['status' => 401]);
        }

        @set_time_limit(0);
        @ini_set('max_execution_time', 0);
        @ini_set('memory_limit', '1024M');

        $post_id = intval($request['postId']);
        $tasks_input = $request['tasks'];

        if (!$post_id || !is_array($tasks_input)) {
            throw new Exception('Invalid payload');
        }

        $tasks = get_post_meta($post_id, 'book_coloring_tasks', true);
        if (!is_array($tasks)) $tasks = [];

        $response_items = [];

        foreach ($tasks_input as $field_id => $base64_images) {

            if (!isset($tasks[$field_id])) {
                continue;
            }

            if (!is_array($base64_images) || empty($base64_images)) {
                continue;
            }

            foreach ($base64_images as $index => $base64) {

                if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                    continue;
                }

                $ext = strtolower($type[1]);
                $data = base64_decode(substr($base64, strpos($base64, ',') + 1));
                if (!$data) continue;

                $filename = "coloring_{$post_id}_{$field_id}_{$index}.{$ext}";
                $upload = wp_upload_bits($filename, null, $data);
                if ($upload['error']) continue;

                $attachment_id = wp_insert_attachment([
                    'post_mime_type' => "image/{$ext}",
                    'post_title' => sanitize_file_name($filename),
                    'post_status' => 'inherit'
                ], $upload['file']);

                require_once ABSPATH . 'wp-admin/includes/image.php';
                wp_update_attachment_metadata(
                    $attachment_id,
                    wp_generate_attachment_metadata($attachment_id, $upload['file'])
                );

                $watermark_id = null;
                $image_path = get_attached_file($attachment_id);
                $watermark_path = ABSPATH . 'wp-content/uploads/2025/12/wmp1.png';

                if (file_exists($image_path) && file_exists($watermark_path)) {
                    $img = imagecreatefromstring(file_get_contents($image_path));
                    $wm = imagecreatefrompng($watermark_path);

                    if ($img && $wm) {
                        imagecopy(
                            $img,
                            $wm,
                            imagesx($img) - imagesx($wm) - 10,
                            imagesy($img) - imagesy($wm) - 10,
                            0, 0,
                            imagesx($wm),
                            imagesy($wm)
                        );

                        $upload_dir = wp_upload_dir();
                        $wm_file = wp_unique_filename(
                            $upload_dir['path'],
                            pathinfo($image_path, PATHINFO_FILENAME) . '_wm.png'
                        );

                        $wm_full = $upload_dir['path'] . '/' . $wm_file;
                        imagepng($img, $wm_full);

                        imagedestroy($img);
                        imagedestroy($wm);

                        $watermark_id = wp_insert_attachment([
                            'post_mime_type' => 'image/png',
                            'post_title' => sanitize_file_name($wm_file),
                            'post_status' => 'inherit'
                        ], $wm_full);

                        wp_update_attachment_metadata(
                            $watermark_id,
                            wp_generate_attachment_metadata($watermark_id, $wm_full)
                        );
                    }
                }

                $tasks[$field_id]['result'][] = $attachment_id;
                if ($watermark_id) {
                    $tasks[$field_id]['watermark'][] = $watermark_id;
                }

                $response_items[$field_id][] = [
                    'result' => wp_get_attachment_url($attachment_id),
                    'watermark' => $watermark_id ? wp_get_attachment_url($watermark_id) : null
                ];
            }

            $tasks[$field_id]['status'] = 'done';
        }

        update_post_meta($post_id, 'book_coloring_tasks', $tasks);

        return [
            'success' => true,
            'items' => $response_items
        ];

    } catch (Exception $e) {
        sendTelegramMessage("❌ save_coloring_result: {$e->getMessage()}");
        return new WP_Error('save_error', $e->getMessage(), ['status' => 500]);
    }
}