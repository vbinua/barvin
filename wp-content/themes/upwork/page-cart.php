<?php /* Template Name: Cart page */ ?>
<?php get_header(); ?>
<?php $currentUser = wp_get_current_user();
$cartItems = get_user_meta($currentUser->ID, 'cart')[0];
$total = 0; ?>

<section class="maincart">
    <div class="cart">
        <div class="cart__title">
            Your Cart
        </div>
        <div class="cart__union">
            <div class="cart__orders">
                <div class="orders">
                    <div class="orders__table">
                        <div class="table">
                            <div class="table__header">
                                <div class="table__union">
                                    <div class="table__product table__title">
                                        Product
                                    </div>
                                    <div class="table__price table__title">
                                        Price
                                    </div>
                                    <div class="table__quantity table__title">
                                        Quantity
                                    </div>
                                    <div class="table__total table__title">
                                        Total
                                    </div>
                                </div>
                            </div>
                            <div class="table__body">
                                <?php
                                if ($cartItems) { ?>
                                    <?php foreach ($cartItems as $item) {
                                        $post_id = intval($item[0]);
                                        $count = intval($item[1]);
                                        $is_pdf = isset($item[2]) ? (bool)$item[2] : false;

                                        $tax = get_the_terms($post_id, 'templates');
                                        $tax = is_array($tax) ? $tax[0] : null;

                                        $tasks = get_post_meta($post_id, 'book_coloring_tasks', true);
                                        $images_count = 0;

                                        if (is_array($tasks)) {
                                            foreach ($tasks as $task) {
                                                if (!empty($task['result']) || !empty($task['original'])) {
                                                    $images_count++;
                                                }
                                            }
                                        }

                                        if ($is_pdf) {
                                            $unitPrice = floatval(get_field('pdf_price', 'options'));
                                            $itemTotal = $unitPrice * $images_count * $count;
                                        } else {
                                            $unitPrice = $tax ? floatval(get_field('price', $tax)) : 0;
                                            $itemTotal = $unitPrice * $count;
                                        }
                                        $total += $itemTotal;
                                        ?>
                                        <div class="table__union cart_item" data-book="<?php echo $item[0]; ?>">
                                            <div class="table__product">
                                                <div class="product">
                                                    <?php $itemImage = get_field('image', $tax); ?>
                                                    <?php if ($itemImage) { ?>
                                                        <div class="product__photo">
                                                            <img src="<?php echo esc_url($itemImage['url']); ?>"
                                                                 alt="<?php echo esc_attr($itemImage['alt']); ?>">
                                                            <select name="type" class="select2">
                                                                <option value="pdf" <?= $is_pdf ? 'selected' : '' ?>>PDF
                                                                    book
                                                                </option>
                                                                <option value="print" <?= !$is_pdf ? 'selected' : '' ?>>
                                                                    Print book
                                                                </option>
                                                            </select>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="product__box">
                                                        <div class="product__title">
                                                            <?php echo get_the_title($item[0]); ?>
                                                        </div>
                                                        <div class="product__description">
                                                            <?php echo $tax->name; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table__price">
                                                <?php
                                                if ($is_pdf) {
                                                    echo number_format($unitPrice, 2, '.', '') . ' × ' . $images_count;
                                                } else {
                                                    echo number_format($unitPrice, 2, '.', '');
                                                }
                                                ?>
                                            </div>
                                            <div class="table__quantity">
                                                <div class="quantity">
                                                    <?php if (!$is_pdf) { ?>
                                                        <div class="quantity__minus">
                                                            -
                                                        </div>
                                                        <div class="quantity__count">
                                                            <?php echo $count; ?>
                                                        </div>
                                                        <div class="quantity__plus">
                                                            +
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="table__total">
                                                <?php echo number_format((float)$itemTotal, 2, '.', ''); ?>
                                            </div>
                                            <div class="table__delete" style="display:flex;"><img
                                                        src="<?php echo get_template_directory_uri(); ?>/img/delete.svg"
                                                        alt="delete"></div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cart__block">
                <div class="order">
                    <div class="order__title">
                        Order summary
                    </div>
                    <div class="order__union order__union_top">
                        <div class="order__subtotal">
                            Subtotal
                        </div>
                        <div class="order__subtotal_price">
                            $ <span><?php echo number_format($total, 2, '.', ' '); ?></span>
                        </div>
                    </div>
                    <div class="order__union order__union_b">
                        <div class="order__shipping">
                            Shipping
                        </div>
                        <div class="order__shipping_price">
                            Free
                        </div>
                    </div>
                    <div class="order__union order__union_top">
                        <div class="order__m-total">
                            Total
                        </div>
                        <div class="order__order__m-total_price">
                            $ <span><?php echo number_format(($total), 2, '.', ' '); ?></span>
                        </div>
                    </div>
                    <a href="/checkout" class="order__button" id="proceed_to_checkout">
                        Proceed to Checkout <img
                                src="<?php echo get_template_directory_uri(); ?>/img/arrow-right-wite.svg" alt="arrow">
                    </a>
                    <!-- <a href="#" class="order__linckback">Save and create another book</a> -->
                </div>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>
