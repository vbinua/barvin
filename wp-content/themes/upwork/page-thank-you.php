<?php
if (!is_user_logged_in()) {
    header("Location: /");
}
require_once "function-parts/create_order.php";
require_once "functions.php";
?>

<?php
function updateOrder($order_id, $session_id, $type = 'payed'): array
{
    $order = get_post($order_id);

    if ($order) {
        if ($session_id) {
            update_post_meta($order_id, 'session_id', $session_id);
        }

        if ($type == 'free') {
            update_post_meta($order_id, 'status', 'Completed');
            sendMailSuccess($order_id);
            $data = fillLuluData($order_id);
            if ($data['has_print']) {
                update_post_meta($order_id, 'status', 'Printing');
                createLuluOrder($data['lulu_object']);
            }
        }
    }
    //delete_user_meta($current_user->ID,'cart');
    return [
        'books' => json_decode(get_post_meta($order_id, 'books', true), ARRAY_A),
        'total' => get_post_meta($order_id, 'total', true),
        'discount' => get_post_meta($order_id, 'coupon', true),
        'shipping' => get_post_meta($order_id, 'tax', true)
    ];
}

if (isset($_GET['order_id'])) {
    $session_id = $_GET['session_id'] ?? null;

    $order_id = $_GET['order_id'];

    $type = $_GET['type'] ?? null;

    if ($type == 'free') {
        $data = updateOrder($order_id, $session_id, $type);
    } else {
        $data = updateOrder($order_id, $session_id);
    }

    $books = $data['books'];
    $total = $data['total'];
    $discount = $data['discount'];
    $shipping = $data['shipping'];
}
?>
<?php get_header();
?>
    <section class="thanks back">
        <div class="thanks__block">
            <div class="thanks__title">
                Thank You!
            </div>
            <div class="thanks__description">
                Your order has been received
            </div>
            <a href="/" class="thanks__button">Go to home</a>
        </div>
    </section>
    <section class="details">
        <div class="details__title">
            View details
        </div>
        <div class="details__block">
            <div class="order">
                <div class="order__title">
                    Order summary
                </div>
                <div class="order__union">
                    <div class="order__book">
                        Book
                    </div>
                    <div class="order__total">
                        Total
                    </div>
                </div>
                <?php foreach ($books as $key => $book): ?>
                    <?php
                    $tax = wp_get_object_terms($book['id'], 'templates', array('fields' => 'all'));
                    ?>
                    <div class="order__union">
                        <div class="order__box">
                            <div class="box">
                                <div class="box__photo"><img
                                            src="<?php echo esc_url(get_field('image', $tax[0])['url']); ?>" alt="book">
                                </div>
                                <div class="box__union">
                                    <div class="box__block">
                                        <div class="box__title"><?php echo $book['title'] ?></div>
                                    </div>
                                    <div class="box__block">
                                        <div class="box__qty">Qty: <span><?php echo $book['quontity'] ?></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="order__bookprice">
                            $ <?php echo $book['price']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="order__union order__union_top">
                    <div class="order__subtotal">
                        Subtotal
                    </div>
                    <div class="order__subtotal_price">
                        $ <?php echo $total; ?>
                    </div>
                </div>
                <div class="order__union order__union_b">
                    <div class="order__shipping">
                        Shipping
                    </div>
                    <div class="order__shipping_price">
                        $ <?= $shipping ?>
                    </div>
                </div>
                <?php if ($discount) { ?>
                    <div class="order__union order__union_top">
                        <div class="order__m-total">
                            Discount
                        </div>
                        <div class="order__order__m-total_price">
                            $ <?php echo $discount; ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="order__union order__union_top">
                    <div class="order__m-total">
                        Total
                    </div>
                    <div class="order__order__m-total_price">
                        $ <?php echo $total; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php get_footer(); ?>