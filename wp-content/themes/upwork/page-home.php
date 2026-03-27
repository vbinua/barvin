<?php /* Template Name: Home page */ ?>
<?php ob_start(); ?>
<?php get_header();
function compare_positions($a, $b)
{
    $position_a = get_field('position', $a);
    $position_b = get_field('position', $b);
    return $position_a - $position_b;
}

?>
<section class="offer">
    <div class="offer__container _container">
        <?php $text = get_field('title'); ?>
        <?php if ($text) { ?>
            <h1 class="offer__title">
                <?php echo $text; ?>
            </h1>
        <?php } ?>
        <div class="offer__buttons">
            <?php $text = get_field('get_started'); ?>
            <?php if ($text) { ?>
                <a href="#books" class="offer__button"><?php echo $text; ?><img
                            src="<?php echo get_template_directory_uri(); ?>/img/arrow-right-wite.svg" alt="arrow"></a>
            <?php } ?>
            <?php $text = get_field('how_it_works'); ?>
            <?php if ($text) { ?>
                <a href="#book-works" class="offer__button_work"><?php echo $text; ?></a>
            <?php } ?>
        </div>
    </div>
    <div class="offer__arrow_block">
        <a href="#books" class="offer__arrow"></a>
    </div>
</section>
<?php if (get_field('show_section') == 'yes'): ?>
    <section class="video">
        <div class="books__container _container">

            <?php if (get_field("title-video")): ?>
                <h2 class="video__title"><?php the_field("title-video"); ?></h2>
            <?php else: ?>

            <?php endif; ?>

            <?php if (get_field("description-video")): ?>
                <div class="video__description"><?php the_field("description-video"); ?></div>
            <?php else: ?>

            <?php endif; ?>

            <?php if (get_field("video_poster-video")): ?>
                <div class="video__box">
                    <img src="<?php the_field("video_poster-video"); ?>" alt="Photo">
                    <span class="video__play"><svg width="114" height="114" viewBox="0 0 114 114" fill="none"
                                                   xmlns="http://www.w3.org/2000/svg"> <circle cx="57.5" cy="57.5625"
                                                                                               r="55.7036"
                                                                                               fill="black"/> <circle
                                    cx="56.5" cy="56.5625" r="55.2036" fill="white" stroke="black"/> <path
                                    d="M74.9004 56.5625L47.3 72.4976L47.3 40.6274L74.9004 56.5625Z"
                                    fill="#408D5C"/> </svg></span>
                    <?php if (get_field("video_link")): ?>
                        <div class="video__box_active">
                            <video width="320" height="240" id="myVideo" controls>
                                <source src="<?php the_field("video_link"); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>


        </div>
    </section>
<?php endif; ?>
<section class="books books-home" id="books">
    <div class="books__container _container">
        <?php $text = get_field('title_books'); ?>
        <?php if ($text) { ?>
            <h2 class="books__title">
                <?php echo $text; ?>
            </h2>
        <?php } ?>
        <?php
        $terms = get_field('related_books');
        usort($terms, 'compare_positions');
        ?>
        <?php if ($terms): ?>
            <div class="books__union">
                <?php foreach ($terms as $term): ?>
                    <?php if (is_user_logged_in()): ?>
                        <a href="<?php echo get_term_link($term->term_id); ?>" class="books__block"
                           data-term="<?php echo $term->term_id; ?>">
                            <?php $img = get_field('image', $term); ?>
                            <div class="books__photo">
                                <?php if (!empty($img['url'])): ?>
                                    <img src="<?php echo esc_url($img['url']); ?>"
                                         alt="<?php echo esc_attr($img['alt']); ?>">
                                <?php endif; ?>
                                <div class="books__button">Create a book</div>
                            </div>
                            <div class="books__subtitle"><?php echo esc_html($term->name); ?></div>
                            <div class="books__description">
                                <?php $description = get_field('description', $term); ?>
                                <?php if ($description): echo esc_html($description); endif; ?>
                            </div>
                            <div class="books__price">
                                <?php $price = get_field('price', $term); ?>
                                <?php if ($price): ?>$ <?php echo esc_html($price); ?><?php endif; ?>
                            </div>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo get_term_link($term->term_id); ?>" class="books__block"
                           data-term="<?php echo $term->term_id; ?>">
                            <?php $img = get_field('image', $term); ?>
                            <div class="books__photo">
                                <?php if (!empty($img['url'])): ?>
                                    <img src="<?php echo esc_url($img['url']); ?>"
                                         alt="<?php echo esc_attr($img['alt']); ?>">
                                <?php endif; ?>
                                <div class="books__button">Create a book</div>
                            </div>
                            <div class="books__subtitle"><?php echo esc_html($term->name); ?></div>
                            <div class="books__description">
                                <?php $description = get_field('description', $term); ?>
                                <?php if ($description): echo esc_html($description); endif; ?>
                            </div>
                            <div class="books__price">
                                <?php $price = get_field('price', $term); ?>
                                <?php if ($price): ?>$ <?php echo esc_html($price); ?><?php endif; ?>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="book__variant">
            <label class="book__variant-item">
                <div class="book__variant-item-left">
                    <input type="radio" name="type" value="print" checked>
                    <span class="book__variant-bg"></span>
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M12.6518 6.52118C9.9845 4.92078 6.72234 4.65939 3.8342 5.81465C3.41354 5.98291 3.1377 6.39034 3.1377 6.84341V17.9471C3.1377 18.6909 3.8889 19.1995 4.57947 18.9232C6.94456 17.9772 9.61593 18.1913 11.8002 19.5018L12.8814 20.1505C12.9639 20.2 13.0521 20.2216 13.1377 20.2201C13.2233 20.2216 13.3115 20.2 13.394 20.1505L14.4752 19.5018C16.6595 18.1913 19.3308 17.9772 21.6959 18.9232C22.3865 19.1995 23.1377 18.6909 23.1377 17.9471V6.84341C23.1377 6.39034 22.8619 5.98291 22.4412 5.81465C19.5531 4.65939 16.2909 4.92078 13.6236 6.52118L13.1377 6.8127L12.6518 6.52118ZM13.852 8.7966C13.852 8.40211 13.5322 8.08231 13.1377 8.08231C12.7432 8.08231 12.4234 8.40211 12.4234 8.7966V17.8442C12.4234 18.2387 12.7432 18.5585 13.1377 18.5585C13.5322 18.5585 13.852 18.2387 13.852 17.8442V8.7966Z"
                              fill="#F9F8F4"/>
                        <path d="M4.30421 20.7416C6.23169 19.6172 8.61513 19.6172 10.5426 20.7416L11.5781 21.3456C12.5418 21.9078 13.7336 21.9078 14.6973 21.3456L15.7328 20.7416C17.6603 19.6172 20.0437 19.6172 21.9712 20.7416L22.069 20.7987C22.4098 20.9974 22.5249 21.4348 22.3261 21.7756C22.1273 22.1163 21.69 22.2314 21.3492 22.0326L21.2514 21.9756C19.7687 21.1107 17.9353 21.1107 16.4526 21.9756L15.4171 22.5796C14.0086 23.4012 12.2668 23.4012 10.8583 22.5796L9.82279 21.9756C8.34012 21.1107 6.5067 21.1107 5.02402 21.9756L4.92617 22.0326C4.58542 22.2314 4.14805 22.1163 3.94928 21.7756C3.75051 21.4348 3.86561 20.9974 4.20636 20.7987L4.30421 20.7416Z"
                              fill="#F9F8F4"/>
                    </svg>
                    <span class="book__variant-item-title">Print Book + PDF</span>
                </div>
                <div class="book__variant-item-right">
                    <span class="book__variant-item-price">$ <?= get_field('both_price', 'options') ?></span>
                </div>
            </label>
            <label class="book__variant-item">
                <div class="book__variant-item-left">
                    <input type="radio" name="type" value="pdf">
                    <span class="book__variant-bg"></span>
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2195_15)">
                            <path d="M20.506 8.59528L14.0547 2.14401V7.3049C14.0547 8.01858 14.6314 8.59528 15.3451 8.59528H20.506Z"
                                  fill="#E1E0D8"/>
                            <path d="M12.1198 16.3369C11.7652 16.3369 11.4746 16.6275 11.4746 16.9821V20.2081V22.1436C11.4746 22.4982 11.7652 22.7888 12.1198 22.7888C12.4744 22.7888 12.765 22.4982 12.765 22.1436V20.8532H13.4102C14.6564 20.8532 15.6683 19.8413 15.6683 18.5951C15.6683 17.3489 14.6564 16.3369 13.4102 16.3369H12.1198ZM13.4102 19.5629H12.765V17.6273H13.4102C13.9466 17.6273 14.378 18.0587 14.378 18.5951C14.378 19.1315 13.9466 19.5629 13.4102 19.5629ZM17.2813 16.3369C16.9267 16.3369 16.6361 16.6275 16.6361 16.9821V22.143C16.6361 22.4976 16.9267 22.7882 17.2813 22.7882H18.5717C19.64 22.7882 20.5073 21.9215 20.5073 20.8526V18.2718C20.5073 17.2036 19.6406 16.3363 18.5717 16.3363H17.2813V16.3369ZM18.5711 21.4984H17.9259V17.6273H18.5711C18.9257 17.6273 19.2163 17.9179 19.2163 18.2725V20.8532C19.2163 21.2078 18.9263 21.4984 18.5711 21.4984ZM21.797 16.9821V19.5629V22.1436C21.797 22.4982 22.0876 22.7888 22.4422 22.7888C22.7968 22.7888 23.0874 22.4982 23.0874 22.1436V20.2081H24.3778C24.7324 20.2081 25.023 19.9175 25.023 19.5629C25.023 19.2083 24.7324 18.9177 24.3778 18.9177H23.0874V17.6273H24.3778C24.7324 17.6273 25.023 17.3367 25.023 16.9821C25.023 16.6275 24.7324 16.3369 24.3778 16.3369H22.4422C22.0876 16.3369 21.797 16.6275 21.797 16.9821Z"
                                  fill="#F9F8F4"/>
                            <path d="M5.02246 4.72477C5.02246 3.30126 6.17971 2.14401 7.60322 2.14401H14.0545V7.3049C14.0545 8.01857 14.6312 8.59528 15.3449 8.59528H20.5058V14.4014H12.1189C10.6954 14.4014 9.53816 15.5586 9.53816 16.9821V22.7882H7.60322C6.17971 22.7888 5.02246 21.6316 5.02246 20.2081V4.72477Z"
                                  fill="#F9F8F4"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_2195_15">
                                <rect width="25" height="25" fill="white" transform="translate(0.522461 0.9664)"/>
                            </clipPath>
                        </defs>
                    </svg>
                    <span class="book__variant-item-title">PDF Only</span>
                </div>
                <div class="book__variant-item-right">
                    <span class="book__variant-item-price">$ <?= get_field('pdf_price', 'options') ?></span>
                </div>
            </label>
        </div>
        <?php $text = get_field('more_books'); ?>
        <?php if ($text) { ?>
            <a href="<?php the_field('more_bookslink'); ?>" class="books__linck">
                <?php echo $text; ?>
                <span>
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
               <path
                       d="M6.03393 0L5.06498 0.974515L9.37681 5.31093H2.32148e-07L2.32148e-07 6.68912L9.37681 6.68912L5.06498 11.0255L6.03393 12L12 5.99998L6.03393 0Z"
                       fill="#383838"/>
            </svg>
         </span>
            </a>
        <?php } ?>
        <div class="books__works">
            <div id="book-works"></div>
            <div class="works">
                <div class="works__union">
                    <div class="works__block">
                        <?php $text = get_field('title_how'); ?>
                        <?php if ($text) { ?>
                            <h2 class="works__title"><?php echo $text; ?></h2>
                        <?php } ?>
                        <?php $text = get_field('text_how'); ?>
                        <?php if ($text) { ?>
                            <?php echo $text; ?>
                        <?php } ?>
                    </div>
                    <div class="works__photo">
                        <?php $image = get_field('image_how'); ?>
                        <?php if ($image) { ?>
                            <img src="<?php echo esc_url($image['url']); ?>"
                                 alt="<?php echo esc_attr($image['alt']); ?>">
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="testimonials">
    <div class="testimonials__container _container">
        <?php $text = get_field('title_testimonials'); ?>
        <?php if ($text) { ?>
            <h2 class="testimonials__title"><?php echo $text; ?></h2>
        <?php } ?>
        <?php if (have_rows('testimonials')) { ?>
            <div class="testimonials__slider">
                <div class="slider slider-review">
                    <?php while (have_rows('testimonials')) {
                        the_row(); ?>
                        <div class="slider__block">
                            <div class="slider__description">
                                <?php the_sub_field('testimonial'); ?>
                            </div>
                            <div class="slider__title">
                                <?php the_sub_field('author'); ?>
                            </div>
                            <div class="slider__city">
                                <?php the_sub_field('city'); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<section class="contact-us">
    <div class="contact__container _container">
        <div class="contact">
            <div class="contact__union">
                <div class="contact__block">
                    <?php $text = get_field('title_contact'); ?>
                    <?php if ($text) { ?>
                        <div class="contact__title">
                            <?php echo $text; ?>
                        </div>
                    <?php } ?>
                    <?php $text = get_field('text_contact'); ?>
                    <?php if ($text) { ?>
                        <div class="contact__description">
                            <?php echo $text; ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="contact__form">
                    <form action="" id="mail_form">
                        <label for="your-name" class="form-group">
                            <span>Name *</span>
                            <input type="text" placeholder="Name" name="your-name" id="your-name" required>
                        </label>
                        <label for="your-email" class="form-group">
                            <span>Email *</span>
                            <input type="email" placeholder="Email" name="your-email" id="your-email" required>
                        </label>
                        <label for="your-message">
                            <span>Your Message</span>
                            <textarea name="notes" cols="30" rows="10" id="your-message"></textarea>
                        </label>
                        <button class="contact__button" id="send_mail">Send <span><img
                                        src="<?php echo get_template_directory_uri(); ?>/img/arrow-book-black.svg"
                                        alt="arrow"></span></button>
                        <div class="contact__form_info">Form sent successfully</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if (get_field('show_section') == 'yes'): ?>
    <div class="video__popup">
        <div class="video__popup_close">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="14" cy="14" r="14" fill="#FAFAFC"/>
                <path d="M10.4645 10.4647C10.7751 10.1541 11.2788 10.1541 11.5894 10.4647L17.5355 16.4109C17.8462 16.7215 17.8462 17.2252 17.5355 17.5358C17.2249 17.8465 16.7212 17.8465 16.4106 17.5358L10.4645 11.5897C10.1538 11.279 10.1538 10.7754 10.4645 10.4647Z"
                      fill="#73738D"/>
                <path d="M17.5355 10.4647C17.8462 10.7754 17.8462 11.279 17.5355 11.5897L11.5894 17.5358C11.2788 17.8465 10.7751 17.8465 10.4645 17.5358C10.1538 17.2252 10.1538 16.7215 10.4645 16.4109L16.4106 10.4647C16.7212 10.1541 17.2249 10.1541 17.5355 10.4647Z"
                      fill="#73738D"/>
            </svg>
        </div>
        <?php if (get_field("video_link")): ?>
            <div class="video__popup_block">
                <?php the_field("video_link"); ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php
require('fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
// $pdf->Cell(0,10,'Hello World!');
$pdf->Cell(0, 10, 'Hello World!');
//var_dump($pdf);
$upload_dir = wp_upload_dir();
$pdf_path = $upload_dir['path'] . '/abc.pdf';
$pdf->Output('F', $pdf_path);
?>
<?php get_footer(); ?>
