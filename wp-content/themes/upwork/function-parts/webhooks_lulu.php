<?php
//add_action('rest_api_init', 'get_status_lulu_endpoint');
//
//function get_status_lulu_endpoint(): void
//{
//    register_rest_route('api/v1', '/change_status', array(
//        'methods'  => 'POST',
//        'callback' => 'handle_get_change_status',
//        /*'permission_callback' => 'todolist_api_request',*/
//    ));
//}
//
//function handle_get_change_status()
//{
//    $itemJson = file_get_contents('php://input');
//
//    error_log($itemJson);
//
//    $data =
//        [
//            'status' => 'success'
//        ];
//    return new WP_REST_Response($data, 200);
//}