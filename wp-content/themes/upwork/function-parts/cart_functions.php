<?php
add_action('wp_ajax_update_cart', 'UpdateCart'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_update_cart', 'UpdateCart'); 

function UpdateCart(){
    if($_POST){
        $item =$_POST['item'];
        $currentUser = wp_get_current_user();
        $cartItems = get_user_meta($currentUser->ID,'cart')[0];
        if($cartItems){
            $itemchecker = 0;
            foreach ($cartItems as $key => $cartItem){
                if($cartItem[0]==$item[0]){
                    $cartItems[$key][2] = $item[2];
                    $cartItems[$key][1] = $item[1];
                    if($item[1]!=0){
                        $cartItem[1]=$item[1];
                    }else{
                        array_splice($cartItems,$key,1);
                    }
                    $itemchecker = 1;
                }
            }
            if($itemchecker==0){
                array_push($cartItems,$item); 
            }
            update_user_meta($currentUser->ID,'cart',$cartItems);
        }else{
            update_user_meta($currentUser->ID,'cart',array($item));
        }
        $response = get_user_meta($currentUser->ID,'cart')[0];
        echo json_encode($response);   
        wp_die(); 
    }
}

add_action('wp_ajax_clear_cart', 'clearCart');
add_action('wp_ajax_nopriv_clear_cart', 'clearCart');

function clearCart()
{
    $currentUser = wp_get_current_user();

    delete_user_meta($currentUser->ID,'cart');

    die();
}

add_action('wp_ajax_handle_book_type', 'handle_select_change');

function handle_select_change() {
    $selectedType = sanitize_text_field($_POST['selectedType']);
    $bookId = $_POST['bookId'];
    $currentUser = wp_get_current_user();
    $cartItems = get_user_meta($currentUser->ID,'cart',true);
    $newCartItems = [];
    if ($cartItems) {
        foreach ($cartItems as $item) {
            if ($item[0] == $bookId) {
                switch ($selectedType) {
                    case 'print':
                    default:
                        $item[2] = 0;
                        break;
                    case 'pdf':
                        $item[2] = 1;
                        break;
                }
            }
            $newCartItems[] = $item;
        }
    }
    update_user_meta($currentUser->ID,'cart',$newCartItems);
    wp_die();
}