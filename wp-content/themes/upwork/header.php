<?php
?>
<script>
    window.addEventListener('beforeunload', function (event) {
        event.stopImmediatePropagation();
    });
</script>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico">
    <?php wp_head(); ?>
</head>
<style>
    body > br {
        display: none;
    }
</style>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<main>
    <header class="header">
        <div class="header__union">
            <a href="<?php echo get_home_url(); ?>" class="header__logo">
                <?php $image = get_field('logotype', 'option'); ?>
                <?php if ($image) { ?>
                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                <?php } ?>
            </a>
            <?php if (is_user_logged_in()) { ?>
                <?php $current_user = wp_get_current_user(); ?>
                <?php $cartItems = get_user_meta($current_user->ID, 'cart')[0]; ?>
                <?php $total = 0; ?>
                <?php if ($cartItems) {
                    foreach ($cartItems as $item) {
                        $total += intval($item[1]);
                    }
                } ?>
                <div class="header__profile">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/people.svg" alt="people">
                    <span><?php echo $current_user->first_name; ?></span>
                    <div class="header__arrow"></div>
                    <div class="header__profile_block">
                        <ul>
                            <?php if (have_rows('user_menu', 'option')) { ?>
                                <?php while (have_rows('user_menu', 'option')) {
                                    the_row(); ?>
                                    <li>
                                        <a href="<?php the_sub_field('item_link'); ?> "
                                           class="<?php the_sub_field('item_class'); ?>"><?php the_sub_field('item_text'); ?></a>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                            <li>
                                <a href="javascript:void(0);" class="header__logout"><img
                                            src="<?php echo get_template_directory_uri(); ?>/img/logout.svg"
                                            alt="logout"><?php the_field('log_out', 'option'); ?></a>
                            </li>
                        </ul>

                    </div>
                </div>
                <a href="/cart" class="header__cart">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/cart.svg" alt="cart">
                    <?php if (intval($total) != 0) { ?>
                        <span style="text-align:center;"><?php echo $total; ?></span>
                    <?php } ?>
                </a>
            <?php } else { ?>
                <a href="javascript:void(0);" class="header__profile_login open_form" id="open_form">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/people.svg" alt="people">
                    <span><?php the_field('log_in', 'option'); ?></span>
                </a>
            <?php } ?>
        </div>
    </header>