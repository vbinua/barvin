<?php

/*if (!is_user_logged_in()) {
    header("Location: /");
}*/

$has_result = false;

$book_id = isset($_GET['edit-book']) ? intval($_GET['edit-book']) : 0;
$book_link = $book_id ? get_permalink($book_id) : '#';

$term = get_queried_object();

$questions = get_field('questions', $term);
$title = get_field('title_template', $term);

$questions = is_array($questions) ? $questions : [];
$sizeOfQuestions = count($questions);

$actual_link = get_term_link($term->term_id);

$currentBookIdIsSet = isset($_COOKIE['currentBook']);

$answers = [];

foreach ($questions as $question) {
    if (!empty($question['question'])) {
        $answers[$question['question']] = '';
    }
}

$editBookId = isset($_GET['edit-book']) ? intval($_GET['edit-book']) : 0;

if ($editBookId) {
    $currentBookId = $editBookId;
    $answers = get_post_meta($currentBookId, 'answers', true);
} elseif ($currentBookIdIsSet) {
    $currentBookId = intval($_COOKIE['currentBook']);
    $answers = get_post_meta($currentBookId, 'answers', true);
}

switch ($term->slug) {
    case 'new-baby':
        $count = 1;
        break;
    case 'starting-school':
        $count = 1;
        break;
    default:
        $count = 10;
}

$step = isset($_GET['step']) ? max(1, intval($_GET['step'])) : 1;
$steps = $count ? ceil($sizeOfQuestions / $count) : 1;

$start = ($step - 1) * $count;
$end = min($start + $count - 1, $sizeOfQuestions - 1);
?>

<?php get_header(); ?>
<style>
    .remove_image {
        position: relative;
        z-index: 9999;
        margin-left: 23px;
        color: red;
        font-size: 11px;
        line-height: 1.2;
    }

    .quiz .quiz__body .body .body__form form label .quiz__upload {
        width: 600px;
    }
</style>
<img src="<?= get_home_url(); ?>/wp-content/uploads/2025/12/b-loader.gif" class="main-loader" alt="loader">
<div class="quiz quiz-edit hide" data-term-id="<?= $term->term_id ?>" data-quiz-slug="<?= $term->slug ?>">
    <div class="quiz__header">
        <div class="header">
            <div class="header__navigation_step">
                <div class="header__block">
                    <h1 class="header__title">
                        <input class="header__title" id="header__title" type="text"
                               value="<?= $title ?>">
                        <a href="#" class="header__edit" id="header__edit">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.3594 0.641026C14.9491 0.230674 14.3926 0.000186218 13.8123 0.000253181C13.2317 -0.0014209 12.6747 0.229469 12.2655 0.641394L4.7387 8.16754C4.67625 8.23046 4.62915 8.30693 4.60099 8.39097L3.45815 11.8195C3.35841 12.1189 3.5203 12.4425 3.81972 12.5422C3.87781 12.5615 3.93865 12.5714 3.99986 12.5715C4.06119 12.5714 4.12216 12.5615 4.18042 12.5424L7.60896 11.3995C7.69316 11.3714 7.76967 11.3241 7.83238 11.2613L15.3592 3.7345C16.2134 2.88032 16.2135 1.49531 15.3594 0.641026Z"
                                      fill="#383838"/>
                                <rect y="14" width="16" height="2" fill="#383838"/>
                            </svg>
                        </a>
                    </h1>
                </div>
                <div class="header__stap">
                    <div class="stap">
                        <div class="stap__point stap__point_active">
                            <div class="stap__number stap__number_active">
                                <svg width="19" height="14" viewBox="0 0 19 14" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.5 7.41663L6.5 12.4166L17.3333 1.16663" stroke="#408D5C"
                                          stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="stap__title_active">
                                Select the theme
                            </div>
                        </div>
                        <div class="stap__point stap__point_next">
                            <div class="stap__number stap__number_next">
                                2
                            </div>
                            <div class="stap__title_next">
                                Creating a coloring page
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="quiz__body">
        <div class="body">
            <div class="body__form" data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                <?php
                if (isset($_GET['edit-book'])) {
                    $post_id = intval($_GET['edit-book']);
                } else {
                    $post_id = get_the_ID();
                }

                $tasks = get_post_meta($post_id, 'book_coloring_tasks', true);
                if (!is_array($tasks)) {
                    $tasks = [];
                }

                $tasks[] = [
                    'original' => null,
                    'result' => null,
                    'status' => 'pending'
                ];
                ?>
                <form action="#" enctype="multipart/form-data" id="quizForm">
                    <div class="body__uploader">
                        <div class="uploader__content">
                            <span class="uploader__content_label">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.5377 12.0764C22.5377 12.0848 22.5377 12.0925 22.5323 12.1009L20.8 20.0345C20.7467 20.3139 20.5976 20.5659 20.3784 20.7471C20.1593 20.9284 19.8837 21.0275 19.5993 21.0274H8.40428C8.12001 21.0273 7.84466 20.9281 7.62565 20.7469C7.40663 20.5656 7.25765 20.3137 7.20435 20.0345L5.47205 12.1009C5.47205 12.0925 5.46823 12.0848 5.4667 12.0764C5.41929 11.8138 5.45918 11.5428 5.58027 11.3049C5.70137 11.0671 5.89701 10.8754 6.1373 10.7592C6.37759 10.643 6.64932 10.6086 6.91096 10.6614C7.17261 10.7142 7.40979 10.8512 7.58625 11.0514L10.158 13.8233L12.8924 7.69069C12.8925 7.68815 12.8925 7.6856 12.8924 7.68305C12.9902 7.47098 13.1467 7.29136 13.3433 7.16547C13.54 7.03957 13.7687 6.97266 14.0022 6.97266C14.2357 6.97266 14.4644 7.03957 14.661 7.16547C14.8577 7.29136 15.0142 7.47098 15.112 7.68305C15.1119 7.6856 15.1119 7.68815 15.112 7.69069L17.8464 13.8233L20.4181 11.0514C20.595 10.8526 20.8318 10.717 21.0928 10.6651C21.3537 10.6132 21.6244 10.6478 21.8639 10.7638C22.1033 10.8798 22.2984 11.0707 22.4194 11.3076C22.5405 11.5445 22.5809 11.8145 22.5346 12.0764H22.5377Z"
                                  fill="#33333B"></path>
                        </svg>
                                <span>3</span> Free images
                            </span>
                            <div class="uploader__preview_area">
                                <div class="uploader__drop_area">
                                    <button type="button" class="uploader__button">
                                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <g id="SVGRepo_iconCarrier">
                                                <path d="M12.5535 2.49392C12.4114 2.33852 12.2106 2.25 12 2.25C11.7894 2.25 11.5886 2.33852 11.4465 2.49392L7.44648 6.86892C7.16698 7.17462 7.18822 7.64902 7.49392 7.92852C7.79963 8.20802 8.27402 8.18678 8.55352 7.88108L11.25 4.9318V16C11.25 16.4142 11.5858 16.75 12 16.75C12.4142 16.75 12.75 16.4142 12.75 16V4.9318L15.4465 7.88108C15.726 8.18678 16.2004 8.20802 16.5061 7.92852C16.8118 7.64902 16.833 7.17462 16.5535 6.86892L12.5535 2.49392Z"
                                                      fill="#1C274C"></path>
                                                <path d="M3.75 15C3.75 14.5858 3.41422 14.25 3 14.25C2.58579 14.25 2.25 14.5858 2.25 15V15.0549C2.24998 16.4225 2.24996 17.5248 2.36652 18.3918C2.48754 19.2919 2.74643 20.0497 3.34835 20.6516C3.95027 21.2536 4.70814 21.5125 5.60825 21.6335C6.47522 21.75 7.57754 21.75 8.94513 21.75H15.0549C16.4225 21.75 17.5248 21.75 18.3918 21.6335C19.2919 21.5125 20.0497 21.2536 20.6517 20.6516C21.2536 20.0497 21.5125 19.2919 21.6335 18.3918C21.75 17.5248 21.75 16.4225 21.75 15.0549V15C21.75 14.5858 21.4142 14.25 21 14.25C20.5858 14.25 20.25 14.5858 20.25 15C20.25 16.4354 20.2484 17.4365 20.1469 18.1919C20.0482 18.9257 19.8678 19.3142 19.591 19.591C19.3142 19.8678 18.9257 20.0482 18.1919 20.1469C17.4365 20.2484 16.4354 20.25 15 20.25H9C7.56459 20.25 6.56347 20.2484 5.80812 20.1469C5.07435 20.0482 4.68577 19.8678 4.40901 19.591C4.13225 19.3142 3.9518 18.9257 3.85315 18.1919C3.75159 17.4365 3.75 16.4354 3.75 15Z"
                                                      fill="#1C274C"></path>
                                            </g>
                                        </svg>
                                        Select images <span>or</span> Drop here
                                    </button>
                                    <input type="file" id="fileInput" multiple style="display:none"/>
                                </div>
                            </div>
                        </div>
                        <div class="uploader__generate">
                            <div class="generate__result">
                                <div class="result__title">
                                    Convert options
                                </div>
                                <div class="result__information_box">
                                    <div class="result__information">
                                        We will create a coloring page from each uploaded image
                                    </div>
                                    <div class="result__ico">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64"
                                             enable-background="new 0 0 64 64" xml:space="preserve"
                                             fill="rgb(152 152 152)"><g
                                                    id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <rect x="1" y="16" fill="none" stroke="rgb(152 152 152)"
                                                          stroke-width="2"
                                                          stroke-miterlimit="10" width="52" height="40"></rect>
                                                </g>
                                                <polyline fill="none" stroke="rgb(152 152 152)" stroke-width="2"
                                                          stroke-miterlimit="10"
                                                          points="10,14 10,8 63,8 63,48 55,48 "></polyline>
                                                <polyline fill="none" stroke="rgb(152 152 152)" stroke-width="2"
                                                          stroke-miterlimit="10"
                                                          points="1,46 15,32 29,48 39,42 53,54 "></polyline>
                                                <circle fill="none" stroke="rgb(152 152 152)" stroke-width="2"
                                                        stroke-miterlimit="10" cx="40" cy="29" r="5"></circle>
                                            </g></svg>
                                    </div>
                                </div>
                                <img src="<?= get_home_url(); ?>/wp-content/uploads/2025/12/b-loader.gif"
                                     alt="" class="result__loader hide">
                                <div class="result__items hide">
                                </div>
                                <div class="result__message">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="quiz__navigation">
        <div class="navigation">
            <div class="navigation__arrow">
                <div class="arrow">
                    <?php if ($step > 1) : ?>
                        <?php $actual_link .= '?edit-book=' . $_GET['edit-book'] . '&step=' . ($step - 1); ?>
                        <a href="<?php echo $actual_link; ?>" class="arrow__left" id="prev_step" style="display: none">
                                <span><svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                           xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.95759 15.5L9.16878 14.2819L3.77899 8.86134L15.5 8.86134L15.5 7.13861L3.77899 7.13861L9.16878 1.71814L7.95759 0.499998L0.499999 8.00003L7.95759 15.5Z"
                                      fill="#383838"/>
                                </svg>
                                </span>
                            <div class="arrow__text">Previous Step</div>
                        </a>
                        <svg class="next_step_right_svg" style="display: none" xmlns:svg="http://www.w3.org/2000/svg"
                             xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0"
                             width="80px" height="69px" viewBox="0 0 128 35"
                             xml:space="preserve"><rect x="0" y="0" width="100%" height="100%"
                                                        fill="transparent"/>
                            <g>
                                <circle fill="#3f8d5b" cx="17.5" cy="17.5" r="17.5"/>
                                <animate attributeName="opacity" dur="2700ms" begin="0s"
                                         repeatCount="indefinite" keyTimes="0;0.167;0.5;0.668;1"
                                         values="0.3;1;1;0.3;0.3"/>
                            </g>
                            <g>
                                <circle fill="#3f8d5b" cx="110.5" cy="17.5" r="17.5"/>
                                <animate attributeName="opacity" dur="2700ms" begin="0s"
                                         repeatCount="indefinite" keyTimes="0;0.334;0.5;0.835;1"
                                         values="0.3;0.3;1;1;0.3"/>
                            </g>
                            <g>
                                <circle fill="#3f8d5b" cx="64" cy="17.5" r="17.5"/>
                                <animate attributeName="opacity" dur="2700ms" begin="0s"
                                         repeatCount="indefinite"
                                         keyTimes="0;0.167;0.334;0.668;0.835;1"
                                         values="0.3;0.3;1;1;0.3;0.3"/>
                            </g>
</svg>
                    <?php endif; ?>
                    <?php if ($step != $steps) : ?>
                        <?php $actual_link .= '?edit-book='; ?>
                        <?php $actual_link .= $_GET['edit-book'] ?? $_COOKIE['currentBook'] ?>
                        <?php $actual_link .= '&step=' . ($step + 1) ?>

                        <a href="<?php echo $actual_link; ?>" <?= (!is_user_logged_in()) ? 'style="display: none"' : '' ?>
                           class="arrow__right"
                           id="next_step">
                            <div class="arrow__text">Next Step</div>
                            <span><svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.04241 0.5L6.83122 1.71814L12.221 7.13866L0.5 7.13866L0.5 8.86139L12.221 8.86139L6.83122 14.2819L8.04241 15.5L15.5 7.99997L8.04241 0.5Z"
                                          fill="#383838"/>
                                    </svg>
                        </a>

                        <a href="javascript:void(0);" <?= (is_user_logged_in()) ? 'style="display: none"' : '' ?>
                           class="arrow__right"
                           id="next_step_auth" data-link="<?php echo $actual_link; ?>">
                            <div class="arrow__text">Next Step</div>
                            <span><svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.04241 0.5L6.83122 1.71814L12.221 7.13866L0.5 7.13866L0.5 8.86139L12.221 8.86139L6.83122 14.2819L8.04241 15.5L15.5 7.99997L8.04241 0.5Z"
                                          fill="#383838"/>
                                    </svg>
                        </a>

                        <svg class="next_step_svg" style="display: none" xmlns:svg="http://www.w3.org/2000/svg"
                             xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0"
                             width="80px" height="69px" viewBox="0 0 128 35"
                             xml:space="preserve"><rect x="0" y="0" width="100%" height="100%"
                                                        fill="transparent"/>
                            <g>
                                <circle fill="#3f8d5b" cx="17.5" cy="17.5" r="17.5"/>
                                <animate attributeName="opacity" dur="2700ms" begin="0s"
                                         repeatCount="indefinite" keyTimes="0;0.167;0.5;0.668;1"
                                         values="0.3;1;1;0.3;0.3"/>
                            </g>
                            <g>
                                <circle fill="#3f8d5b" cx="110.5" cy="17.5" r="17.5"/>
                                <animate attributeName="opacity" dur="2700ms" begin="0s"
                                         repeatCount="indefinite" keyTimes="0;0.334;0.5;0.835;1"
                                         values="0.3;0.3;1;1;0.3"/>
                            </g>
                            <g>
                                <circle fill="#3f8d5b" cx="64" cy="17.5" r="17.5"/>
                                <animate attributeName="opacity" dur="2700ms" begin="0s"
                                         repeatCount="indefinite"
                                         keyTimes="0;0.167;0.334;0.668;0.835;1"
                                         values="0.3;0.3;1;1;0.3;0.3"/>
                            </g>
                    </svg>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <div class="quiz__count" style="display: none">
            Section <span>1</span> / <span
                    class="quiz__count_all">1</span>
        </div>
    </div>
    <div class="quiz__m-footer quiz__m-footer-edit">
        <div class="m-footer">
            <div class="quiz__union">
                <div class="quiz__information__about_generation">
                    *You have <span><span></span> free</span> photo generations available
                </div>
                <div class="quiz__box_buttons">
                    <a href="#" class="quiz__button quiz__start-creating">
                        Start creating
                    </a>
                    <a href="#" class="quiz__button quiz__get-colorings">
                        Get colorings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="imageModal">
    <span class="closeBtn">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier"
                                                                                   stroke-width="0"></g><g
                    id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g
                    id="SVGRepo_iconCarrier"> <path
                        d="M20.7457 3.32851C20.3552 2.93798 19.722 2.93798 19.3315 3.32851L12.0371 10.6229L4.74275 3.32851C4.35223 2.93798 3.71906 2.93798 3.32854 3.32851C2.93801 3.71903 2.93801 4.3522 3.32854 4.74272L10.6229 12.0371L3.32856 19.3314C2.93803 19.722 2.93803 20.3551 3.32856 20.7457C3.71908 21.1362 4.35225 21.1362 4.74277 20.7457L12.0371 13.4513L19.3315 20.7457C19.722 21.1362 20.3552 21.1362 20.7457 20.7457C21.1362 20.3551 21.1362 19.722 20.7457 19.3315L13.4513 12.0371L20.7457 4.74272C21.1362 4.3522 21.1362 3.71903 20.7457 3.32851Z"
                        fill="#0F0F0F"></path> </g></svg>
    </span>
    <img src="" alt="preview">
</div>
<div class="upload-limit-popup hide" id="uploadLimitPopup">
     <span class="closeBtn">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier"
                                                                                   stroke-width="0"></g><g
                    id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g
                    id="SVGRepo_iconCarrier"> <path
                        d="M20.7457 3.32851C20.3552 2.93798 19.722 2.93798 19.3315 3.32851L12.0371 10.6229L4.74275 3.32851C4.35223 2.93798 3.71906 2.93798 3.32854 3.32851C2.93801 3.71903 2.93801 4.3522 3.32854 4.74272L10.6229 12.0371L3.32856 19.3314C2.93803 19.722 2.93803 20.3551 3.32856 20.7457C3.71908 21.1362 4.35225 21.1362 4.74277 20.7457L12.0371 13.4513L19.3315 20.7457C19.722 21.1362 20.3552 21.1362 20.7457 20.7457C21.1362 20.3551 21.1362 19.722 20.7457 19.3315L13.4513 12.0371L20.7457 4.74272C21.1362 4.3522 21.1362 3.71903 20.7457 3.32851Z"
                        fill="#0F0F0F"></path> </g></svg>
    </span>
    <div class="upload-limit-popup__overlay"></div>
    <div class="upload-limit-popup__content">
        <h3>You’ve reached the upload limit</h3>
        <p>
            Guests can upload up to 3 images.<br>
            Please sign up or log in to upload more.
        </p>
        <button class="upload-limit-popup__btn open_form" id="open_form">Log in</button>
    </div>
</div>
<div class="pay-popup" id="payPopup">
     <span class="closeBtn">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier"
                                                                                   stroke-width="0"></g><g
                    id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g
                    id="SVGRepo_iconCarrier"> <path
                        d="M20.7457 3.32851C20.3552 2.93798 19.722 2.93798 19.3315 3.32851L12.0371 10.6229L4.74275 3.32851C4.35223 2.93798 3.71906 2.93798 3.32854 3.32851C2.93801 3.71903 2.93801 4.3522 3.32854 4.74272L10.6229 12.0371L3.32856 19.3314C2.93803 19.722 2.93803 20.3551 3.32856 20.7457C3.71908 21.1362 4.35225 21.1362 4.74277 20.7457L12.0371 13.4513L19.3315 20.7457C19.722 21.1362 20.3552 21.1362 20.7457 20.7457C21.1362 20.3551 21.1362 19.722 20.7457 19.3315L13.4513 12.0371L20.7457 4.74272C21.1362 4.3522 21.1362 3.71903 20.7457 3.32851Z"
                        fill="#0F0F0F"></path> </g></svg>
    </span>
    <div class="pay-popup__overlay"></div>
    <div class="pay-popup__content">
        <div class="content__box">
            <img src="<?= get_home_url(); ?>/wp-content/uploads/2025/12/b-loader.gif" alt="loader"
                 class="content__loader">
            <div class="content__box_block">
                <div class="content__title">
                    <div class="content__label"></div>
                    <h3></h3>
                </div>
                <div class="content__description">
                </div>
                <div class="content__price">Price with your discount applied: <span></span></div>
                <form id="payment-form">
                    <div style="margin: 15px 0;">
                        <div id="card-element"
                             style="padding: 12px; border: 1px solid #ccc; border-radius: 6px; background: #fff;"></div>
                        <div id="card-errors" role="alert"
                             style="color: #dc3545; font-size: 14px; margin-top: 5px;"></div>
                    </div>
                    <button type="submit" id="submit-pay-btn" class="form__button">Pay Now</button>
                </form>
                <div class="content__download">Download</div>
            </div>
        </div>
        <div class="content__img">
            <div class="img__title">
                Get coloring right now!
            </div>
        </div>
    </div>
</div>
<script>
    var myPageIsDirty = 1;
    window.addEventListener('beforeunload', function (e) {
        if (myPageIsDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
<?php get_footer(); ?>
