<?php
if (!is_user_logged_in()) {
    wp_redirect(home_url('/'));
    exit;
}

get_header();

$current_user = wp_get_current_user();

/* Books */
$books = get_posts([
    'numberposts' => -1,
    'author' => $current_user->ID,
    'post_status' => ['draft', 'publish'],
    'post_type' => 'books',
]);

/* Orders */
$orders = get_posts([
    'numberposts' => -1,
    'author' => $current_user->ID,
    'post_type' => 'orders',
]);

/* Templates term */
$te = wp_get_object_terms(192, 'templates');

$img = '';
$alt = '';

if (!is_wp_error($te) && isset($te[0])) {
    $image = get_field('image', $te[0]);

    if (is_array($image)) {
        $img = esc_url($image['url'] ?? '');
        $alt = esc_attr($image['alt'] ?? '');
    }
}

$user_meta = get_user_meta($current_user->ID);
?>
    <style>


        .box__table_third .checkout__form_union label {
            width: 100%;

        }

        .box__table_third label {
            display: flex;
            flex-direction: column;
            font-family: "ArialRounded";
            position: relative;
            color: #383838;
            font-size: calc(12px + 2 * ((100vw - 320px) / 1120));
            padding: 0 60px;
        }

        .box__table_third label .error {
            border: 1px solid #EE8168;
        }

        .box__table_third label .error__text {
            position: absolute;
            bottom: -5px;
            right: 0;
            color: #EE8168;
            font-weight: 600;
            font-size: calc(10px + 0 * ((100vw - 320px) / 1120));
        }

        .box__table_third label input {
            background: #F1F0E9;
            border: 1px solid #000000;
            box-sizing: border-box;
            border-radius: 12px;
            padding: 5px 15px;
            color: #383838;
            height: 56px;
            margin-bottom: 21px;
            width: 100%;
        }

        .box__table_third label input::-webkit-input-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label input:-ms-input-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label input::-moz-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label input:-moz-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label textarea {
            background: #F1F0E9;
            border: 1px solid #000000;
            box-sizing: border-box;
            border-radius: 12px;
            padding: 15px 15px;
            color: #383838;
            height: 130px !important;
            width: 580px !important;
        }

        @media (max-width: 600px) {
            .box__table_third label textarea {
                width: 100% !important;
            }
        }

        .box__table_third label textarea::-webkit-input-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label textarea:-ms-input-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label textarea::-moz-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label textarea:-moz-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label select {
            background: #F1F0E9;
            border: 1px solid #000000;
            box-sizing: border-box;
            border-radius: 12px;
            padding: 5px 15px;
            color: #383838;
            height: 56px;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("../img/arrow-black-select.svg");
            background-repeat: no-repeat;
            background-position-x: 97%;
            background-position-y: 23px;
            margin-bottom: 21px;
        }

        .box__table_third label select::-webkit-input-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label select:-ms-input-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label select::-moz-placeholder {
            color: #BDBCB2;
        }

        .box__table_third label select:-moz-placeholder {
            color: #BDBCB2;
        }
    </style>

    <main id="primary" class="site-main">
        <section class="profile">
            <div class="profile__header">
                <div class="header">
                    <h1 class="header__title">
                        Hi <?php echo $current_user->first_name . ' ' . $current_user->last_name; ?>
                    </h1>
                    <div class="header__description">
                        Welcome back!
                    </div>
                </div>
            </div>
            <div class="profile__body">
                <div class="body">
                    <h2 class="body__title">
                        My Account
                    </h2>
                    <div class="body__box">
                        <div class="box">
                            <div class="box__navigations_mob">
                                <div class="box__navigation_box">
                                    <div class="box__links">
                                        <div class="box__link box__navigation_active"><img
                                                    src="<?php echo get_template_directory_uri(); ?>/img/people.svg"
                                                    alt="people"> My Account
                                        </div>
                                        <div class="box__block" id="links">
                                            <a href="#"
                                               class="box__navigation box__navigation_1 box__navigation_active">
                                                <svg width="20"
                                                     height="20" viewBox="0 0 20 20" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                            d="M20 2.98934H5.25L4.8279 0.53418H1.09058C0.487319 0.53418 0 1.0224 0 1.62521V2.71579H2.99004L4.91395 13.9065C4.95652 14.1528 5.19339 14.3526 5.44384 14.3526H18.3637C18.9669 14.3526 19.4552 13.8648 19.4552 13.2606V12.171H6.8288L6.56295 10.6248H16.5457C17.7505 10.6248 18.8881 9.66099 19.087 8.47349L20 2.98934Z"
                                                            fill="white"/>
                                                    <path
                                                            d="M8.81796 19.467C9.85975 19.467 10.7043 18.6224 10.7043 17.5807C10.7043 16.5389 9.85975 15.6943 8.81796 15.6943C7.77618 15.6943 6.93164 16.5389 6.93164 17.5807C6.93164 18.6224 7.77618 19.467 8.81796 19.467Z"
                                                            fill="white"/>
                                                    <path
                                                            d="M13.4795 17.5801C13.4795 18.6213 14.3232 19.466 15.3645 19.466C16.4066 19.466 17.2521 18.6213 17.2521 17.5801C17.2521 16.5389 16.4066 15.6934 15.3645 15.6934C14.3232 15.6934 13.4795 16.5394 13.4795 17.5801Z"
                                                            fill="white"/>
                                                </svg>
                                                Orders
                                            </a>
                                            <a href="#" class="box__navigation box__navigation_2">
                                                <svg width="15" height="17"
                                                     viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                            d="M3.75 11.6082H13.75C14.4403 11.6082 15 11.1053 15 10.4849V1.12338C15 0.502922 14.4403 0 13.75 0H11.25V4.49351L9.375 3.37013L7.5 4.49351V0H2.5C1.11937 0 0 1.00584 0 2.24676V11.6082V12.7316C0 14.59 1.68215 16.1017 3.75 16.1017H13.75C14.4403 16.1017 15 15.5988 15 14.9784C15 14.358 14.4403 13.855 13.75 13.855H3.75C3.06063 13.855 2.5 13.3512 2.5 12.7316C2.5 12.1121 3.06063 11.6082 3.75 11.6082Z"
                                                            fill="#383838"/>
                                                </svg>
                                                Drafts
                                            </a>
                                            <a href="#" class="box__navigation box__navigation_3">
                                                <svg width="20" height="20"
                                                     viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                            d="M18.9041 8.02341H17.844C17.6469 7.25513 17.3393 6.53558 16.9382 5.87736L17.7004 5.11344C17.9062 4.90766 18.0193 4.63315 18.0193 4.34168C18.0193 4.05064 17.9053 3.77569 17.7004 3.57036L16.4301 2.30135C16.0181 1.88893 15.2981 1.88937 14.8879 2.30092L14.0935 3.09051C13.4427 2.71116 12.7449 2.41881 11.9762 2.23087V1.0963C11.9762 0.494638 11.4954 0 10.8938 0H9.09795C8.49629 0 8.02297 0.494638 8.02297 1.0963V2.23044C7.25469 2.41794 6.55254 2.71072 5.90086 3.09051L5.10691 2.30092C4.69536 1.8885 3.97494 1.88893 3.56209 2.30048L2.29265 3.56949C2.08905 3.77265 1.9729 4.05368 1.9729 4.34081C1.9729 4.63185 2.08557 4.90592 2.29178 5.1117L3.05397 5.87736C2.6533 6.53558 2.34529 7.25513 2.14865 8.02341H1.08803C0.485937 8.02341 0 8.50717 0 9.10752V10.9021C0 11.5041 0.485937 11.977 1.08803 11.977H2.14865C2.34529 12.7453 2.65286 13.4692 3.0531 14.1274L2.29091 14.8944C2.0847 15.1002 1.97159 15.3755 1.97159 15.667C1.97159 15.9585 2.08514 16.2339 2.29091 16.4396L3.56122 17.7095C3.76743 17.9153 4.04107 18.0284 4.33211 18.0284C4.62315 18.0284 4.89722 17.9149 5.10343 17.7095L5.90086 16.9195C6.55254 17.2993 7.25469 17.5916 8.02297 17.78V18.9128C8.02297 19.5145 8.49629 19.9996 9.09795 19.9996H10.8938C11.4954 19.9996 11.9762 19.5145 11.9762 18.9128V17.7796C12.7449 17.5916 13.4427 17.2993 14.0944 16.9195L14.8848 17.7078C15.091 17.9144 15.3651 18.0275 15.657 18.0275C15.9485 18.0275 16.223 17.914 16.4288 17.7087L17.6991 16.4396C17.9049 16.2343 18.0175 15.9598 18.0184 15.6688C18.0184 15.3773 17.9044 15.1036 17.6991 14.8974L16.9378 14.1274C17.338 13.4688 17.6456 12.7444 17.8426 11.977H18.9033C19.5049 11.977 19.9996 11.5041 19.9996 10.9021V9.10752C20 8.50717 19.5058 8.02341 18.9041 8.02341ZM9.99543 13.4209C8.09084 13.4209 6.54645 11.8909 6.54645 10.005C6.54645 8.11781 8.09084 6.58995 9.99543 6.58995C11.9005 6.58995 13.4457 8.11781 13.4457 10.005C13.4453 11.8913 11.9005 13.4209 9.99543 13.4209Z"
                                                            fill="#383838"/>
                                                </svg>
                                                Settings
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box__navigations">
                                <a href="#" class="box__navigation box__navigation_1 box__navigation_active">
                                    <svg width="20" height="20"
                                         viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                                d="M20 2.98934H5.25L4.8279 0.53418H1.09058C0.487319 0.53418 0 1.0224 0 1.62521V2.71579H2.99004L4.91395 13.9065C4.95652 14.1528 5.19339 14.3526 5.44384 14.3526H18.3637C18.9669 14.3526 19.4552 13.8648 19.4552 13.2606V12.171H6.8288L6.56295 10.6248H16.5457C17.7505 10.6248 18.8881 9.66099 19.087 8.47349L20 2.98934Z"
                                                fill="white"/>
                                        <path
                                                d="M8.81796 19.467C9.85975 19.467 10.7043 18.6224 10.7043 17.5807C10.7043 16.5389 9.85975 15.6943 8.81796 15.6943C7.77618 15.6943 6.93164 16.5389 6.93164 17.5807C6.93164 18.6224 7.77618 19.467 8.81796 19.467Z"
                                                fill="white"/>
                                        <path
                                                d="M13.4795 17.5801C13.4795 18.6213 14.3232 19.466 15.3645 19.466C16.4066 19.466 17.2521 18.6213 17.2521 17.5801C17.2521 16.5389 16.4066 15.6934 15.3645 15.6934C14.3232 15.6934 13.4795 16.5394 13.4795 17.5801Z"
                                                fill="white"/>
                                    </svg>
                                    Orders
                                </a>
                                <a href="#" class="box__navigation box__navigation_2">
                                    <svg width="15" height="17" viewBox="0 0 15 17"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                                d="M3.75 11.6082H13.75C14.4403 11.6082 15 11.1053 15 10.4849V1.12338C15 0.502922 14.4403 0 13.75 0H11.25V4.49351L9.375 3.37013L7.5 4.49351V0H2.5C1.11937 0 0 1.00584 0 2.24676V11.6082V12.7316C0 14.59 1.68215 16.1017 3.75 16.1017H13.75C14.4403 16.1017 15 15.5988 15 14.9784C15 14.358 14.4403 13.855 13.75 13.855H3.75C3.06063 13.855 2.5 13.3512 2.5 12.7316C2.5 12.1121 3.06063 11.6082 3.75 11.6082Z"
                                                fill="#383838"/>
                                    </svg>
                                    Drafts
                                </a>
                                <a href="#" class="box__navigation box__navigation_3">
                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                         fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                                d="M18.9041 8.02341H17.844C17.6469 7.25513 17.3393 6.53558 16.9382 5.87736L17.7004 5.11344C17.9062 4.90766 18.0193 4.63315 18.0193 4.34168C18.0193 4.05064 17.9053 3.77569 17.7004 3.57036L16.4301 2.30135C16.0181 1.88893 15.2981 1.88937 14.8879 2.30092L14.0935 3.09051C13.4427 2.71116 12.7449 2.41881 11.9762 2.23087V1.0963C11.9762 0.494638 11.4954 0 10.8938 0H9.09795C8.49629 0 8.02297 0.494638 8.02297 1.0963V2.23044C7.25469 2.41794 6.55254 2.71072 5.90086 3.09051L5.10691 2.30092C4.69536 1.8885 3.97494 1.88893 3.56209 2.30048L2.29265 3.56949C2.08905 3.77265 1.9729 4.05368 1.9729 4.34081C1.9729 4.63185 2.08557 4.90592 2.29178 5.1117L3.05397 5.87736C2.6533 6.53558 2.34529 7.25513 2.14865 8.02341H1.08803C0.485937 8.02341 0 8.50717 0 9.10752V10.9021C0 11.5041 0.485937 11.977 1.08803 11.977H2.14865C2.34529 12.7453 2.65286 13.4692 3.0531 14.1274L2.29091 14.8944C2.0847 15.1002 1.97159 15.3755 1.97159 15.667C1.97159 15.9585 2.08514 16.2339 2.29091 16.4396L3.56122 17.7095C3.76743 17.9153 4.04107 18.0284 4.33211 18.0284C4.62315 18.0284 4.89722 17.9149 5.10343 17.7095L5.90086 16.9195C6.55254 17.2993 7.25469 17.5916 8.02297 17.78V18.9128C8.02297 19.5145 8.49629 19.9996 9.09795 19.9996H10.8938C11.4954 19.9996 11.9762 19.5145 11.9762 18.9128V17.7796C12.7449 17.5916 13.4427 17.2993 14.0944 16.9195L14.8848 17.7078C15.091 17.9144 15.3651 18.0275 15.657 18.0275C15.9485 18.0275 16.223 17.914 16.4288 17.7087L17.6991 16.4396C17.9049 16.2343 18.0175 15.9598 18.0184 15.6688C18.0184 15.3773 17.9044 15.1036 17.6991 14.8974L16.9378 14.1274C17.338 13.4688 17.6456 12.7444 17.8426 11.977H18.9033C19.5049 11.977 19.9996 11.5041 19.9996 10.9021V9.10752C20 8.50717 19.5058 8.02341 18.9041 8.02341ZM9.99543 13.4209C8.09084 13.4209 6.54645 11.8909 6.54645 10.005C6.54645 8.11781 8.09084 6.58995 9.99543 6.58995C11.9005 6.58995 13.4457 8.11781 13.4457 10.005C13.4453 11.8913 11.9005 13.4209 9.99543 13.4209Z"
                                                fill="#383838"/>
                                    </svg>
                                    Settings
                                </a>
                            </div>
                            <div class="box__table box__table_first">
                                <div class="table">
                                    <?php if ($orders): ?>
                                        <div class="table__union">
                                            <div class="table__title">
                                                Order Date
                                            </div>
                                            <div class="table__title">
                                                Order Number
                                            </div>
                                            <div class="table__title">
                                                Order Amount
                                            </div>
                                            <div class="table__title table__title_completed">
                                                Completed
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="table__contant">
                                        <?php if ($orders): ?>
                                            <?php foreach ($orders as $order): ?>
                                                <?php $orderMeta = get_post_meta($order->ID, 'books')[0];
                                                $orderTotal = get_post_meta($order->ID, 'total')[0];

                                                $orderTotal = str_replace(['$', ' '], '', $orderTotal);
                                                ?>
                                                <div class="table__body_union">
                                                    <div class="table__date">
                                                        <?php echo get_the_date('m/d/Y', $order->ID) ?>
                                                    </div>
                                                    <div class="table__number">
                                                        <?php
                                                        echo sprintf("%'.06d\n", $order->ID);
                                                        ?>
                                                    </div>
                                                    <div class="table__amount">
                                                        <?php echo '$' . $orderTotal; ?>
                                                    </div>
                                                    <div class="table__completed">
                                                        <div class="completed">
                                                            <div class="completed__date">

                                                                <?= get_field('status', $order->ID); ?>
                                                                <!--<svg width="57" height="57" viewBox="0 0 57 57" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M25.1324 21.2134C27.6822 20.9289 30.3178 20.9289 32.8676 21.2134C33.8249 21.3202 34.6653 21.8371 35.1969 22.5877L28.5871 29.1867L26.5476 27.1505C26.3057 26.909 25.9135 26.909 25.6716 27.1505C25.4298 27.392 25.4298 27.7835 25.6716 28.025L28.1491 30.4985C28.391 30.74 28.7832 30.74 29.0251 30.4985L35.727 23.8074C35.7456 23.8977 35.7604 23.9894 35.7713 24.0824C36.0762 26.6853 36.0762 29.3147 35.7713 31.9176C35.5939 33.4322 34.3759 34.6183 32.8676 34.7866C30.3178 35.0711 27.6822 35.0711 25.1324 34.7866C23.6241 34.6183 22.4061 33.4322 22.2287 31.9176C21.9238 29.3147 21.9238 26.6853 22.2287 24.0824C22.4061 22.5678 23.6241 21.3817 25.1324 21.2134Z" fill="#408D5C"/>
                                                                </svg>-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>


                                        <?php else: ?>
                                            <div class="table__contant-empty">
                                                <div class="empty">
                                                    <div class="empty__box">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/img/ico-empty.svg"
                                                             alt="empty" class="empty__ico">
                                                        <div class="empty__title">
                                                            You don`t have orders
                                                        </div>
                                                        <a href="<?php echo get_home_url(); ?>#books"
                                                           class="empty__button">
                                                            Get started
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                 height="12"
                                                                 viewBox="0 0 12 12" fill="none">
                                                                <path d="M6.03393 0.00012207L5.06498 0.974637L9.37681 5.31105L2.32148e-07 5.31105L2.32148e-07 6.68924L9.37681 6.68924L5.06498 11.0256L6.03393 12.0001L12 6.0001L6.03393 0.00012207Z"
                                                                      fill="white"/>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="box__table box__table_second">
                                <div class="table">
                                    <?php if (!empty($books)): ?>
                                        <div class="table__header">
                                            <div class="table__preview table__title">Preview</div>
                                            <div class="table__title_main table__title">Title</div>
                                            <a href="#" class="table__created table__title">
                                                Created
                                                <svg width="15" height="9" viewBox="0 0 15 9" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15 1.21119L13.7819 0L7.5 6.1687L1.21814 0L0 1.21119L7.50003 8.66878L15 1.21119Z"
                                                          fill="#383838"/>
                                                </svg>
                                            </a>
                                            <a href="#" class="table__edited table__title">
                                                Last edited
                                                <svg width="15" height="9" viewBox="0 0 15 9" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15 1.21119L13.7819 0L7.5 6.1687L1.21814 0L0 1.21119L7.50003 8.66878L15 1.21119Z"
                                                          fill="#383838"/>
                                                </svg>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="table__box">
                                        <?php if (!empty($books)): ?>
                                            <?php foreach ($books as $book): ?>
                                                <?php
                                                $tax = wp_get_object_terms($book->ID, 'templates');

                                                if (is_wp_error($tax) || empty($tax)) {
                                                    continue;
                                                }

                                                $term = $tax[0];

                                                $tax_link = get_term_link($term);

                                                if (is_wp_error($tax_link)) {
                                                    continue;
                                                }
                                                $image = get_field('image', $term);

                                                if (!is_array($image) || empty($image['url'])) {
                                                    continue;
                                                }

                                                $image_url = esc_url($image['url']);
                                                $image_alt = esc_attr($image['alt'] ?? '');

                                                $step = get_post_meta($book->ID, 'step', true);

                                                if (empty($step)) {
                                                    continue;
                                                }

                                                $tax_link .= '?edit-book=' . $book->ID;
                                                if ($step != 1) {
                                                    $tax_link .= '&step=' . $step;
                                                }
                                                ?>
                                                <div class="table__body">
                                                    <div class="table__preview">
                                                        <a href="<?php echo get_permalink($book->ID); ?>">
                                                            <img src="<?php echo $image_url; ?>"
                                                                 alt="<?php echo $image_alt; ?>">
                                                        </a>
                                                        <select name="type" class="select2">
                                                            <option value="pdf">PDF book</option>
                                                            <option value="print">Print book</option>
                                                        </select>
                                                    </div>
                                                    <div class="table__title_main">
                                                        <div class="table__title_main_draft"
                                                            <?php if ($book->post_status === 'publish'): ?>
                                                                style="border-color:#0e7b17;color:#0e7b17"
                                                            <?php endif; ?>>
                                                            <?php echo esc_html($book->post_status); ?>
                                                        </div>
                                                        <span>
                                                            <a href="<?php echo get_permalink($book->ID); ?>">
                                                                <?php echo esc_html(get_the_title($book->ID)); ?>
                                                            </a>
                                                        </span>
                                                    </div>
                                                    <div class="table__created">
                                                        <span class="table__title_mob"><b>Created:</b></span>
                                                        <?php echo get_the_date('m/d/Y', $book->ID); ?>
                                                    </div>
                                                    <div class="table__editeds">
                                                        <span class="table__title_mob"><b>Last edited:</b></span>
                                                        <?php echo get_the_modified_date('m/d/Y', $book->ID); ?>
                                                    </div>
                                                    <div class="table__edited">
                                                        <div class="edited">
                                                            <span><?php echo get_the_modified_date('m/d/Y', $book->ID); ?></span>
                                                            <div class="edited__box">
                                                                <a href="<?php echo esc_url($tax_link); ?>"
                                                                   class="edited__edit"
                                                                   data-book-id="<?php echo $book->ID; ?>">
                                                                    Edit
                                                                </a>
                                                                <a href="#"
                                                                   class="edited__add <?php echo ($book->post_status !== 'publish') ? 'isDisabled' : ''; ?>"
                                                                   data-book-id="<?php echo $book->ID; ?>">
                                                                    Add to cart
                                                                </a>
                                                                <a href="#"
                                                                   class="edited__delete"
                                                                   data-book-id="<?php echo $book->ID; ?>">
                                                                    Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="table__contant-empty">
                                                <div class="empty">
                                                    <div class="empty__box">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/img/ico-empty.svg"
                                                             alt="empty">
                                                        <div class="empty__title">
                                                            You don’t have books designed
                                                        </div>
                                                        <a href="<?php echo home_url('/'); ?>#books"
                                                           class="empty__button">
                                                            Get started
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="box__table box__table_third">
                                <form>
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
                                        <input type="text" placeholder="House number and street name"
                                               id="checkout_street"
                                               value="<?php echo get_user_meta($current_user->ID, 'checkout_street')[0]; ?>">
                                    </label>
                                    <label for="#" class="hidden_inp">
                                        <span>State</span>
                                        <select id="checkout_apartment"
                                                value="<?php echo get_user_meta($current_user->ID, 'checkout_apartment')[0]; ?>">
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
                                            <option value="IL" data-select2-id="select2-data-95-eu8t">"IL"</option>
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
                                            <input type="text" placeholder="House number and street name"
                                                   id="checkout_city"
                                                   value="<?php echo get_user_meta($current_user->ID, 'checkout_city')[0]; ?>">
                                        </label>
                                        <label for="#" class="hidden_inp">
                                            <span>Zip</span>
                                            <input type="text" placeholder="enter your post code" id="checkout_postcode"
                                                   value="<?php echo get_user_meta($current_user->ID, 'checkout_postcode')[0]; ?>">
                                        </label>
                                    </div>
                                    <div class="checkout__form_union">
                                        <label for="#">
                                            <span>Phone</span>
                                            <input type="text" placeholder="343 435 325" id="checkout_phone"
                                                   value="<?php echo get_user_meta($current_user->ID, 'checkout_phone')[0]; ?>">
                                        </label>
                                        <label for="#">
                                            <span>Email</span>
                                            <input type="text" placeholder="email@email.com" id="checkout_email"
                                                   value="<?php echo $current_user->user_email; ?>">
                                        </label>
                                    </div>
                                    <button class="authorization__button" id="update_information">Update account <img
                                                src="https://secureservercdn.net/45.40.150.54/c5j.a1b.myftpupload.com/wp-content/themes/upwork/img/arrow-right.svg"
                                                alt="arrow"></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php
get_footer();
