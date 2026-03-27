<?php
$bookID = get_the_ID();
$bookMeta = get_post_meta($bookID, 'answers');
$step = get_post_meta($bookID, 'step');
$terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));
$editUrl = get_term_link($terms[0]);
if ($step[0] == '0') {
    $editUrl .= '/?edit-book=' . $bookID;
} else {
    $editUrl .= '/?edit-book=' . $bookID . '&step=' . $step[0];
}

?>

<div class="quiz__m-footer quiz__m-footer-top">
    <div class="m-footer">
        <div class="quiz__union">
            <div class="quiz__stap_navigation">
                <div class="navigation">
                    <a href="<?php echo $editUrl; ?>" class="navigation__left">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.96607 12L6.93502 11.0255L2.62319 6.68907L12 6.68907L12 5.31088L2.62319 5.31088L6.93502 0.974515L5.96607 -5.27503e-07L-4.2914e-07 6.00002L5.96607 12Z"
                                  fill="#999999"/>
                        </svg>
                        Edit book</a>
                    <?php if (get_post()->post_status == 'publish') {
                        ?>
                        <a href="#" data-book-id="<?php echo $bookID; ?>" class="navigation__right" id="addBook">Add to
                            cart
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 2.98934H5.25L4.8279 0.53418H1.09058C0.487319 0.53418 0 1.0224 0 1.62521V2.71579H2.99004L4.91395 13.9065C4.95652 14.1528 5.19339 14.3526 5.44384 14.3526H18.3637C18.9669 14.3526 19.4552 13.8648 19.4552 13.2606V12.171H6.8288L6.56295 10.6248H16.5457C17.7505 10.6248 18.8881 9.66099 19.087 8.47349L20 2.98934Z"
                                      fill="white"/>
                                <path d="M8.81796 19.467C9.85975 19.467 10.7043 18.6224 10.7043 17.5807C10.7043 16.5389 9.85975 15.6943 8.81796 15.6943C7.77618 15.6943 6.93164 16.5389 6.93164 17.5807C6.93164 18.6224 7.77618 19.467 8.81796 19.467Z"
                                      fill="white"/>
                                <path d="M13.4795 17.5801C13.4795 18.6213 14.3232 19.466 15.3645 19.466C16.4066 19.466 17.2521 18.6213 17.2521 17.5801C17.2521 16.5389 16.4066 15.6934 15.3645 15.6934C14.3232 15.6934 13.4795 16.5394 13.4795 17.5801Z"
                                      fill="white"/>
                            </svg>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>