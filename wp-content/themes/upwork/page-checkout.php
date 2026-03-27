<?php $currentUser = wp_get_current_user();
$cartItems = get_user_meta($currentUser->ID, 'cart')[0];
$have_book = false;
if ($cartItems) {
    foreach ($cartItems as $item) {
        if (isset($item[2])) {
            if (!$item[2]) {
                $have_book = true;
                break;
            }
        }
    }
}
// var_dump($cartItems);
// print_r($cartItems);
$total = 0;
$total_images_count = 0;
?>
<?php get_header(); ?>
<style>
    .tabs {
        display: flex;
        position: absolute;
        width: 100%;
        top: 0;
        left: 0;
        justify-content: space-between;
    }

    .tab {
        width: calc(33% - 5px);
        background: #F1F0E9;
        padding: 16px;
        text-align: center;
        border-radius: 12px;
        cursor: pointer;
    }

    .tab.active {
        background: #3f8d5b;
        color: #fff;
    }

    .checkout_form {
        position: relative;
    }

    .hidden_inp {
        display: none;
    }

    .loader {
        top: 0;
        left: 0;
        z-index: 100000;
        position: fixed;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, .6);
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        visibility: hidden;
    }

    .loader svg {
        transform: scale(.5);
    }

    .loader.active {
        opacity: 1;
        visibility: visible;
    }
</style>
<div class="loader">
    <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg"
         xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="252px" height="69px" viewBox="0 0 128 35"
         xml:space="preserve">
      <rect x="0" y="0" width="100%" height="100%" fill="transparent"></rect>
        <g>
            <circle fill="#3f8d5b" cx="17.5" cy="17.5" r="17.5"></circle>
            <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite"
                     keyTimes="0;0.167;0.5;0.668;1" values="0.3;1;1;0.3;0.3"></animate>
        </g>
        <g>
            <circle fill="#3f8d5b" cx="110.5" cy="17.5" r="17.5"></circle>
            <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite"
                     keyTimes="0;0.334;0.5;0.835;1" values="0.3;0.3;1;1;0.3"></animate>
        </g>
        <g>
            <circle fill="#3f8d5b" cx="64" cy="17.5" r="17.5"></circle>
            <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite"
                     keyTimes="0;0.167;0.334;0.668;0.835;1" values="0.3;0.3;1;1;0.3;0.3"></animate>
        </g>
   </svg>
</div>
<section class="checkout back">
    <div class="_container">
        <h1 class="checkout__title">
            Checkout
        </h1>
        <h2 class="checkout__subtitle first-step">
            <span>1</span>
            Shipping address
        </h2>
        <div class="checkout__union">
            <div class="checkout__form">
                <form action="#" class="checkout_form">
                    <div class="checkout__form_union">
                        <label for="#">
                            <span>First Name </span>
                            <input type="text" placeholder="First Name" id="checkout_first_name"
                                   value="<?php echo $current_user->first_name; ?>">
                        </label>
                        <label for="#">
                            <span>Last Name</span>
                            <input type="text" placeholder="Last Name" id="checkout_last_name"
                                   value="<?php echo $current_user->last_name; ?>">
                        </label>
                    </div>
                    <?php if ($have_book) { ?>
                        <label for="#" class="hidden_inp" style="display:none">
                            <span>Country \ Region </span>
                            <select id="checkout_country">
                                <option value="US" selected="selected">US</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </label>
                        <label for="#" class="hidden_inp">
                            <span>Street address </span>
                            <input type="text" placeholder="House number and street name" id="checkout_street"
                                   value="<?php echo get_user_meta($current_user->ID, 'checkout_street', true); ?>">
                        </label>
                        <label for="#" class="hidden_inp">
                            <span>State</span>
                            <select id="checkout_apartment"
                                    value="<?php echo get_user_meta($current_user->ID, 'checkout_apartment', true); ?>">
                                <option value="AL" data-select2-id="select2-data-80-8qid">AL</option>
                                <option value="AK" data-select2-id="select2-data-81-j3d5">AK</option>
                                <option value="AZ" data-select2-id="select2-data-82-mgcs">AZ</option>
                                <option value="AR" data-select2-id="select2-data-83-o2gb">AR</option>
                                <option value="CA" data-select2-id="select2-data-84-4rhj">CA</option>
                                <option value="CZ" data-select2-id="select2-data-85-4msu">CZ</option>
                                <option value="CO" data-select2-id="select2-data-86-qcme">CO</option>
                                <option value="CT" data-select2-id="select2-data-87-b6rr">CT</option>
                                <option value="DE" data-select2-id="select2-data-88-jf8q">DE</option>
                                <option value="DC" data-select2-id="select2-data-89-70zr">DC</option>
                                <option value="FL" data-select2-id="select2-data-90-eyaw">FL</option>
                                <option value="GA" data-select2-id="select2-data-91-9xqh">GA</option>
                                <option value="GU" data-select2-id="select2-data-92-vxii">GU</option>
                                <option value="HI" data-select2-id="select2-data-93-768g">HI</option>
                                <option value="ID" data-select2-id="select2-data-94-9eqe">ID</option>
                                <option value="" il
                                ""="" data-select2-id="select2-data-95-eu8t">"IL"</option>
                                <option value="IN" data-select2-id="select2-data-96-l2zj">IN</option>
                                <option value="IA" data-select2-id="select2-data-97-b3vz">IA</option>
                                <option value="KS" data-select2-id="select2-data-98-i66j">KS</option>
                                <option value="KY" data-select2-id="select2-data-99-7bnx">KY</option>
                                <option value="LA" data-select2-id="select2-data-100-jhl2">LA</option>
                                <option value="ME" data-select2-id="select2-data-101-lb6g">ME</option>
                                <option value="MD" data-select2-id="select2-data-102-7vk0">MD</option>
                                <option value="MA" data-select2-id="select2-data-103-fh25">MA</option>
                                <option value="MI" data-select2-id="select2-data-104-o0mj">MI</option>
                                <option value="MN" data-select2-id="select2-data-105-rs5i">MN</option>
                                <option value="MS" data-select2-id="select2-data-106-3icw">MS</option>
                                <option value="MO" data-select2-id="select2-data-107-u08z">MO</option>
                                <option value="MT" data-select2-id="select2-data-108-epbx">MT</option>
                                <option value="NE" data-select2-id="select2-data-109-wadx">NE</option>
                                <option value="NV" data-select2-id="select2-data-110-lyvx">NV</option>
                                <option value="NH" data-select2-id="select2-data-111-xyrd">NH</option>
                                <option value="NJ" data-select2-id="select2-data-112-1afs">NJ</option>
                                <option value="NM" data-select2-id="select2-data-113-glxi">NM</option>
                                <option value="NY" data-select2-id="select2-data-114-yfl2">NY</option>
                                <option value="NC" data-select2-id="select2-data-115-ki6k">NC</option>
                                <option value="ND" data-select2-id="select2-data-116-i7v3">ND</option>
                                <option value="OH" data-select2-id="select2-data-117-9r3p">OH</option>
                                <option value="OK" data-select2-id="select2-data-118-xnwk">OK</option>
                                <option value="OR" data-select2-id="select2-data-119-2itd">OR</option>
                                <option value="PA" data-select2-id="select2-data-120-mesx">PA</option>
                                <option value="PR" data-select2-id="select2-data-121-q7xp">PR</option>
                                <option value="RI" data-select2-id="select2-data-122-moc9">RI</option>
                                <option value="SC" data-select2-id="select2-data-123-gzlk">SC</option>
                                <option value="SD" data-select2-id="select2-data-124-13cr">SD</option>
                                <option value="TN" data-select2-id="select2-data-125-tu8y">TN</option>
                                <option value="TX" data-select2-id="select2-data-126-rz07">TX</option>
                                <option value="UT" data-select2-id="select2-data-127-h9k9">UT</option>
                                <option value="VT" data-select2-id="select2-data-128-vwxg">VT</option>
                                <option value="VI" data-select2-id="select2-data-129-uxyt">VI</option>
                                <option value="VA" data-select2-id="select2-data-130-b3lo">VA</option>
                                <option value="WA" data-select2-id="select2-data-131-kh94">WA</option>
                                <option value="WV" data-select2-id="select2-data-132-frnn">WV</option>
                                <option value="WI" data-select2-id="select2-data-133-jvx7">WI</option>
                                <option value="WY" data-select2-id="select2-data-134-jzwp">WY</option>
                            </select>
                        </label>
                        <div class="checkout__form_union">
                            <label for="#" class="hidden_inp">
                                <span>City </span>
                                <input type="text" placeholder="House number and street name" id="checkout_city"
                                       value="<?php echo get_user_meta($current_user->ID, 'checkout_city', true); ?>">
                            </label>
                            <label for="#" class="hidden_inp">
                                <span>Zip</span>
                                <input type="text" placeholder="enter your post code" id="checkout_postcode"
                                       value="<?php echo get_user_meta($current_user->ID, 'checkout_postcode', true); ?>">
                            </label>
                        </div>
                        <div class="checkout__form_union">
                            <label for="#">
                                <span>Phone</span>
                                <input type="text" placeholder="343 435 325" id="checkout_phone"
                                       value="<?php echo get_user_meta($current_user->ID, 'checkout_phone', true); ?>">
                            </label>
                            <label for="#">
                                <span>Email</span>
                                <input type="text" placeholder="email@email.com" id="checkout_email"
                                       value="<?php echo $current_user->user_email; ?>">
                            </label>
                        </div>
                    <?php } ?>
                    <?php if (!$have_book) { ?>
                        <div class="checkout__form_union">
                            <label for="#">
                                <span>Email</span>
                                <input type="text" placeholder="email@email.com" id="checkout_email"
                                       value="<?php echo $current_user->user_email; ?>">
                            </label>
                        </div>
                    <?php } ?>
                    <h2 class="checkout__subtitle next-stage"><?php if ($have_book) { ?>   <span>2</span>Select shipping
                            <div class="light">(please enter your address first)</div>  <?php } ?>
                    </h2>
                    <div class="shipping" <?= !$have_book ? 'style="display:none;"' : '' ?>>
                        <label for="mail" class="radio radio-first"
                               style="background: rgb(241, 249, 244); border: 1px solid rgb(64, 141, 92);">
                            <span>12-13 business days </span>
                            <div class="shipping__price">
                                Free
                            </div>
                            <input type="radio" name="type_shipping" id="mail" data-price="$ 0.00"
                                   class="real-radio-btn" checked>
                            <span class="custom-radio-btn"></span>
                        </label>
                        <label for="express" class="radio">
                            <span>7-8 business days</span>
                            <div class="shipping__price">
                                $9.99
                            </div>
                            <input type="radio" name="type_shipping" id="express" data-price="$ 9.99"
                                   class="real-radio-btn">
                            <span class="custom-radio-btn"></span>
                        </label>
                        <label for="expedited" class="radio">
                            <span>6-7 business days</span>
                            <div class="shipping__price">
                                $17.99
                            </div>
                            <input type="radio" name="type_shipping" id="expedited" data-price="$ 17.99"
                                   class="real-radio-btn">
                            <span class="custom-radio-btn"></span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="checkout__order">
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
                    <?php
                    if ($cartItems) {
                        foreach ($cartItems as $item) {
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
                                $price = floatval(get_field('pdf_price', 'options'));
                                $itemTotal = $price * $images_count * $count;
                                $type_book = 'PDF book';
                            } else {
                                $price = $tax ? floatval(get_field('price', $tax)) : 0;
                                $itemTotal = $price * $count;
                                $type_book = 'Print book';
                            }
                            $total += $itemTotal;
                            ?>
                            <div class="order__union" data-type-book="<?= $type_book ?>"
                                 data-page-cover="<?php echo (get_post($item[0])->post_status == 'publish') ? get_the_permalink($item[0]) . '?print=cover' : ''; ?>"
                                 data-page-url="<?php echo (get_post($item[0])->post_status == 'publish') ? get_the_permalink($item[0]) . '?print=true' : ''; ?>"
                                 data-pages="<?php if ($tax->term_id == 3) {
                                     echo '22';
                                 } elseif ($tax->term_id == 4) {
                                     echo '16';
                                 } else {
                                     echo '20';
                                 } ?>">
                                <div class="order__box">
                                    <div class="box" data-book-id="<?php echo $item[0]; ?>"
                                         data-book-total="<?php echo $itemTotal; ?>">
                                        <?php $itemImage = get_field('image', $tax); ?>
                                        <?php if ($itemImage) { ?>
                                            <div class="box__photo"><img src="<?php echo esc_url($itemImage['url']); ?>"
                                                                         alt="<?php echo esc_attr($itemImage['alt']); ?>">
                                            </div>
                                        <?php } ?>
                                        <div class="box__union">
                                            <div class="box__block">
                                                <div class="box__title"><?php echo get_the_title($item[0]); ?></div>
                                            </div>
                                            <div class="box__block">
                                                <div class="box__qty">Qty: <span><?php echo $count; ?></span></div>
                                            </div>
                                            <div class="box__block">
                                                <div class="box__qty" id="book_type"><?= $type_book ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="order__bookprice">
                                    $
                                    <span class="price_total"><?php echo number_format((float)$itemTotal, 2, '.', ''); ?></span>
                                </div>
                            </div>
                        <?php } ?>
                    <?php }
                    ?>
                    <div class="order__union order__union_top">
                        <div class="order__subtotal">
                            Subtotal
                        </div>
                        <div class="order__subtotal_price">
                            $ <span><?php echo number_format($total, 2, '.', ' '); ?></span>
                        </div>
                    </div>
                    <div class="order__union order__union_b" <?= !$have_book ? 'style="display:none;"' : '' ?>>
                        <div class="order__shipping">
                            Shipping
                        </div>
                        <div class="order__shipping_price">
                            $ 0.00
                        </div>
                    </div>
                    <div class="order__union order__union_b">
                        <div class="order__shipping">
                            Tax
                        </div>
                        <div class="order__tax_price order__tax_price-default">
                            <?= ($have_book) ? '-' : '$ ' . number_format($total * get_field('tax_for_pdf', 'options') / 100, 2, '.', ' ') ?>
                        </div>
                        <div class="order__tax_price order__tax_price-info">
                            $ <?= ($have_book) ? '0.44' : number_format($total * get_field('tax_for_pdf', 'options') / 100, 2, '.', ' ') ?>
                        </div>
                    </div>
                    <div class="order__union order__union_b hide_block" style="display: none">
                        <div class="order__shipping">
                            Coupon Discount
                        </div>

                        <div class="order__coupon_price order__coupon_price-info">
                            $ 0.00
                        </div>
                    </div>
                    <div class="order__union order__union_top">
                        <div class="order__m-total">
                            Total
                        </div>
                        <div class=" order__order__m-total_price-default">
                            <span><?php echo ($have_book) ? '-' : '$ ' . number_format($total + ($total * get_field('tax_for_pdf', 'options') / 100), 2, '.', ' ') ?></span>
                        </div>
                        <div class="order__order__m-total_price order__order__m-total_price-info">
                            $
                            <span><?php echo ($have_book) ? number_format(($total + 7.99), 2, '.', ' ') : number_format($total + ($total * get_field('tax_for_pdf', 'options') / 100), 2, '.', ' ') ?></span>
                        </div>
                    </div>
                    <div class="order__promocode_box">
                        <div class="order__promocode">
                            Enter promocode
                        </div>
                        <div class="order__promocode">
                            <input type="text" id="coupon_code">
                            <input type="hidden" name="coupon_discount" value="0">
                            <a href="javascript:void(0);" class="order__button order__link btnCouponHide" id="coupon"
                               style="display: flex;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" rx="12" fill="white"/>
                                    <path d="M6 12.8L9.2 16L17.2 8" stroke="#408D5C" stroke-width="1.5"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Apply </a>
                        </div>
                        <a href="#" class="order__button_close">Cancel</a>
                    </div>
                    <a href="javascript:void(0);" class="order__button order__link" id="place_order">
                        Proceed to payment <img
                                src="<?php echo get_template_directory_uri(); ?>/img/arrow-right-wite.svg" alt="arrow">
                    </a>
                    <a href="javascript:void(0);" class="order__link btnOrderHide">
                        Proceed to payment <img
                                src="<?php echo get_template_directory_uri(); ?>/img/arrow-right-wite.svg" alt="arrow">
                    </a>
                    <a href="/cart/" class="order__linckback">Back to cart</a>
                    <div class="checkoutOrderLoading loading-wrap">
                        <svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="252px" height="69px"
                             viewBox="0 0 128 35" xml:space="preserve" style="display: flex;">
                     <rect x="0" y="0" width="100%" height="100%" fill="transparent"></rect>
                            <circle fill="#3f8d5b" cx="17.5" cy="17.5" r="17.5"></circle>
                            <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite"
                                     keyTimes="0;0.167;0.5;0.668;1" values="0.3;1;1;0.3;0.3"></animate>
                            <circle fill="#3f8d5b" cx="110.5" cy="17.5" r="17.5"></circle>
                            <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite"
                                     keyTimes="0;0.334;0.5;0.835;1" values="0.3;0.3;1;1;0.3"></animate>
                            <circle fill="#3f8d5b" cx="64" cy="17.5" r="17.5"></circle>
                            <animate attributeName="opacity" dur="2700ms" begin="0s" repeatCount="indefinite"
                                     keyTimes="0;0.167;0.334;0.668;0.835;1" values="0.3;0.3;1;1;0.3;0.3"></animate>
                  </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div style="display:none;">
    <?php echo do_shortcode('[wp_stripe_checkout_session name="My Product" price="2.99"]'); ?>
</div>
<?php get_footer(); ?>
