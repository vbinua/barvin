<?php
add_action('wp_ajax_coloring_can_use_trial_ajax', 'coloring_can_use_trial_ajax');
add_action('wp_ajax_nopriv_coloring_can_use_trial_ajax', 'coloring_can_use_trial_ajax');

function coloring_can_use_trial_ajax()
{
    check_ajax_referer('nonce');

    $remaining = 0;

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $count = (int)get_user_meta($user_id, 'coloring_trial_requests', true);
    } else {
        $guest_id = sanitize_text_field($_POST['guest_id']);
        if (empty($guest_id)) {
            wp_send_json_error([
                'message' => 'Guest ID not found'
            ]);
        }

        $guests = get_option('coloring_trial_guests', []);

        if (!isset($guests[$guest_id])) {
            $guests[$guest_id] = 3;
            update_option('coloring_trial_guests', $guests);
        }

        $count = $guests[$guest_id];
    }

    wp_send_json_success([
        'remaining' => $count
    ]);
}

add_action('wp_ajax_check_coloring_result', 'check_coloring_result');
add_action('wp_ajax_nopriv_check_coloring_result', 'check_coloring_result');
function check_coloring_result()
{
    try {
        $post_id = intval($_POST['post_id']);
        $guest_id = sanitize_text_field($_POST['guest_id'] ?? '');

        $tasks = get_post_meta($post_id, 'book_coloring_tasks', true);
        if (empty($tasks) || !is_array($tasks)) {
            wp_send_json_error([]);
        }

        $results = [];
        $newly_counted = 0;

        foreach ($tasks as $field_id => &$task) {

            $watermark_ids = $task['watermark'] ?? [];
            if (empty($watermark_ids) || !is_array($watermark_ids)) {
                continue;
            }

            if (!isset($task['counted_ids']) || !is_array($task['counted_ids'])) {
                $task['counted_ids'] = [];
            }

            $urls = [];

            foreach ($watermark_ids as $wid) {
                $url = wp_get_attachment_url($wid);
                if (!$url) continue;

                $urls[] = $url;

                if (!in_array($wid, $task['counted_ids'], true)) {
                    $task['counted_ids'][] = $wid;
                    $newly_counted++;
                }
            }

            if ($urls) {
                $results[] = [
                    'field_id' => $field_id,
                    'urls' => $urls
                ];
            }
        }

        unset($task);
        update_post_meta($post_id, 'book_coloring_tasks', $tasks);

        if ($newly_counted > 0) {
            coloring_decrease_trial_requests($newly_counted, $guest_id);

            update_post_meta($post_id, 'is_coloring_paid', true);
        }

        if ($results) {
            wp_send_json_success($results);
        }

        wp_send_json_error([]);

    } catch (Throwable $e) {
        if (function_exists('sendTelegramMessage')) {
            sendTelegramMessage('check_coloring_result error: ' . $e->getMessage());
        }
        wp_send_json_error(['message' => 'Server error']);
    }
}

function coloring_decrease_trial_requests(int $count, string $guest_id = '')
{
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $used = (int)get_user_meta($user_id, 'coloring_trial_requests', true);

        if ($used <= 0) return;

        $used = max(0, $used - $count);

        update_user_meta($user_id, 'coloring_trial_requests', $used);
        return;
    }

    if (!$guest_id) return;

    $guests = get_option('coloring_trial_guests', []);
    $used = isset($guests[$guest_id]) ? (int)$guests[$guest_id] : 0;

    if ($used <= 0) return;

    $used = max(0, $used - $count);

    $guests[$guest_id] = $used;
    update_option('coloring_trial_guests', $guests, false);
}