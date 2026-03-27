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
    .upload__wrapper {
        display: flex;
        align-items: center;
    }

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
                                Questions & photos
                            </div>
                        </div>
                        <div class="stap__point stap__point_next">
                            <div class="stap__number">
                                3
                            </div>
                            <div class="stap__title">
                                Preview book
                            </div>
                        </div>
                        <div class="stap__point stap__point_next">
                            <div class="stap__number">
                                4
                            </div>
                            <div class="stap__title">
                                Purchase
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="quiz__body">
        <div class="body">
            <div class="body__form" style="padding: 50px;" data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
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
                    <div class="quiz-images">
                        <?php foreach ($tasks as $field_id => $task):
                            $original_id = !empty($task['original']) ? (int)$task['original'] : 0;
                            $result_id = !empty($task['watermark']) ? (int)$task['watermark'] : 0;

                            $original_url = $original_id ? wp_get_attachment_url($original_id) : '';
                            $result_url = $result_id ? wp_get_attachment_url($result_id) : '';

                            if (!empty($result_url)) {
                                $has_result = true;
                            }

                            $status = $task['status'] ?? 'pending';
                            $attach_id = !empty($original_id) ? (int)$original_id : rand(1000, 9999);
                            ?>
                            <div class="quiz-image-item" data-atach-id="<?php echo esc_attr($field_id); ?>">
                                <label> <span>Upload image</span>
                                    <div class="quiz__upload form-group">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M0 12.3076H1.23077V14.1538H14.7692V12.3076H16V15.3845H0V12.3076Z"
                                                  fill="#999999"/>
                                            <path d="M2.46191 5.50736L3.36147 6.40178L7.36431 2.42163L7.36431 11.0771L8.63648 11.0771L8.63648 2.42163L12.6393 6.40178L13.5388 5.50736L8.00035 0.000222918L2.46191 5.50736Z"
                                                  fill="#999999"/>
                                        </svg>
                                        <div class="quiz__upload_text">Drop your image here</div>
                                        <span>or</span>
                                        <div class="upload__wrapper">
                                            <input
                                                    type="file"
                                                    name="images[<?php echo esc_attr($field_id); ?>]"
                                                    accept=".jpg,.jpeg,.png"
                                                    class="quiz__uploud_file"
                                                    data-attach-id="<?php echo esc_attr($attach_id); ?>"
                                                <?php if (!empty($result_url)) echo 'disabled'; ?>
                                            >
                                        </div>
                                    </div>
                                </label>
                                <div class="quiz-image-preview">
                                    <svg fill="#000000" viewBox="0 0 32 32" version="1.1"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                           stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path d="M21.434 11.975l8.602-8.549-0.028 4.846c-0.009 0.404 0.311 0.755 0.716 0.746l0.513-0.001c0.404-0.009 0.739-0.25 0.748-0.654l0.021-7.219c0-0.007-0.027-0.012-0.027-0.019l0.040-0.366c0.004-0.203-0.044-0.384-0.174-0.513-0.13-0.131-0.311-0.21-0.512-0.204l-0.366 0.009c-0.007 0-0.012 0.003-0.020 0.004l-7.172-0.032c-0.404 0.009-0.738 0.343-0.747 0.748l-0.001 0.513c0.061 0.476 0.436 0.755 0.84 0.746l4.726 0.013-8.572 8.52c-0.39 0.39-0.39 1.024 0 1.415s1.023 0.39 1.414 0zM10.597 20.025l-8.602 8.523 0.027-4.82c0.010-0.404-0.312-0.756-0.716-0.747l-0.544 0.001c-0.405 0.010-0.739 0.25-0.748 0.654l-0.021 7.219c0 0.007 0.028 0.011 0.028 0.019l-0.040 0.365c-0.005 0.203 0.043 0.385 0.174 0.514 0.129 0.131 0.311 0.21 0.512 0.205l0.366-0.009c0.007 0 0.012-0.003 0.020-0.003l7.203 0.032c0.404-0.010 0.738-0.344 0.748-0.748l0.001-0.514c-0.062-0.476-0.436-0.755-0.84-0.746l-4.726-0.012 8.571-8.518c0.39-0.39 0.39-1.023 0-1.414s-1.023-0.391-1.413-0zM32.007 30.855l-0.021-7.219c-0.009-0.404-0.343-0.645-0.747-0.654l-0.513-0.001c-0.404-0.009-0.725 0.343-0.716 0.747l0.028 4.846-8.602-8.549c-0.39-0.39-1.023-0.39-1.414 0s-0.39 1.023 0 1.414l8.571 8.518-4.726 0.012c-0.404-0.009-0.779 0.27-0.84 0.746l0.001 0.514c0.009 0.404 0.344 0.739 0.747 0.748l7.172-0.032c0.008 0 0.013 0.003 0.020 0.003l0.366 0.009c0.201 0.005 0.384-0.074 0.512-0.205 0.131-0.129 0.178-0.311 0.174-0.514l-0.040-0.365c0-0.008 0.027-0.012 0.027-0.019zM3.439 2.041l4.727-0.012c0.404 0.009 0.778-0.27 0.84-0.746l-0.001-0.513c-0.010-0.405-0.344-0.739-0.748-0.748l-7.204 0.031c-0.008-0.001-0.013-0.004-0.020-0.004l-0.366-0.009c-0.201-0.005-0.383 0.074-0.512 0.204-0.132 0.13-0.179 0.31-0.174 0.514l0.040 0.366c0 0.007-0.028 0.012-0.028 0.020l0.021 7.219c0.009 0.404 0.343 0.645 0.748 0.654l0.545 0.001c0.404 0.009 0.724-0.342 0.715-0.746l-0.028-4.819 8.602 8.523c0.39 0.39 1.024 0.39 1.414 0s0.39-1.024 0-1.415z"></path>
                                        </g>
                                    </svg>
                                    <div class="preview__box <?php if (empty($original_url)) {
                                        echo 'hide';
                                    } ?>">
                                        <img src="<?php echo esc_url($original_url); ?>" alt="Original image">
                                    </div>
                                </div>
                                <?php
                                if ($term->slug === 'new-year-style') {
                                    ?>
                                    <img src="<?= get_home_url(); ?>/wp-content/uploads/2025/12/loader-new-year.gif"
                                         class="quiz-image-loader hide" alt="loader">
                                    <?php
                                } else {
                                    ?>
                                    <img src="<?= get_home_url(); ?>/wp-content/uploads/2025/12/b-loader.gif"
                                         class="quiz-image-loader hide" alt="loader">
                                    <?php
                                }
                                ?>
                                <div class="quiz-image-preview-result">
                                    <svg fill="#000000" viewBox="0 0 32 32" version="1.1"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                           stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path d="M21.434 11.975l8.602-8.549-0.028 4.846c-0.009 0.404 0.311 0.755 0.716 0.746l0.513-0.001c0.404-0.009 0.739-0.25 0.748-0.654l0.021-7.219c0-0.007-0.027-0.012-0.027-0.019l0.040-0.366c0.004-0.203-0.044-0.384-0.174-0.513-0.13-0.131-0.311-0.21-0.512-0.204l-0.366 0.009c-0.007 0-0.012 0.003-0.020 0.004l-7.172-0.032c-0.404 0.009-0.738 0.343-0.747 0.748l-0.001 0.513c0.061 0.476 0.436 0.755 0.84 0.746l4.726 0.013-8.572 8.52c-0.39 0.39-0.39 1.024 0 1.415s1.023 0.39 1.414 0zM10.597 20.025l-8.602 8.523 0.027-4.82c0.010-0.404-0.312-0.756-0.716-0.747l-0.544 0.001c-0.405 0.010-0.739 0.25-0.748 0.654l-0.021 7.219c0 0.007 0.028 0.011 0.028 0.019l-0.040 0.365c-0.005 0.203 0.043 0.385 0.174 0.514 0.129 0.131 0.311 0.21 0.512 0.205l0.366-0.009c0.007 0 0.012-0.003 0.020-0.003l7.203 0.032c0.404-0.010 0.738-0.344 0.748-0.748l0.001-0.514c-0.062-0.476-0.436-0.755-0.84-0.746l-4.726-0.012 8.571-8.518c0.39-0.39 0.39-1.023 0-1.414s-1.023-0.391-1.413-0zM32.007 30.855l-0.021-7.219c-0.009-0.404-0.343-0.645-0.747-0.654l-0.513-0.001c-0.404-0.009-0.725 0.343-0.716 0.747l0.028 4.846-8.602-8.549c-0.39-0.39-1.023-0.39-1.414 0s-0.39 1.023 0 1.414l8.571 8.518-4.726 0.012c-0.404-0.009-0.779 0.27-0.84 0.746l0.001 0.514c0.009 0.404 0.344 0.739 0.747 0.748l7.172-0.032c0.008 0 0.013 0.003 0.020 0.003l0.366 0.009c0.201 0.005 0.384-0.074 0.512-0.205 0.131-0.129 0.178-0.311 0.174-0.514l-0.040-0.365c0-0.008 0.027-0.012 0.027-0.019zM3.439 2.041l4.727-0.012c0.404 0.009 0.778-0.27 0.84-0.746l-0.001-0.513c-0.010-0.405-0.344-0.739-0.748-0.748l-7.204 0.031c-0.008-0.001-0.013-0.004-0.020-0.004l-0.366-0.009c-0.201-0.005-0.383 0.074-0.512 0.204-0.132 0.13-0.179 0.31-0.174 0.514l0.040 0.366c0 0.007-0.028 0.012-0.028 0.020l0.021 7.219c0.009 0.404 0.343 0.645 0.748 0.654l0.545 0.001c0.404 0.009 0.724-0.342 0.715-0.746l-0.028-4.819 8.602 8.523c0.39 0.39 1.024 0.39 1.414 0s0.39-1.024 0-1.415z"></path>
                                        </g>
                                    </svg>
                                    <div class="preview__box <?php if (empty($result_url)) {
                                        echo 'hide';
                                    } ?>">
                                        <img src="<?php echo esc_url($result_url); ?>" alt="Result image">
                                    </div>
                                </div>
                                <input type="hidden"
                                       name="status[<?php echo esc_attr($field_id); ?>]"
                                       value="<?php echo esc_attr($status); ?>">
                            </div>
                        <?php endforeach; ?>
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
                <a href="#" data-steps="<?= $steps ?>" class="quiz__save" style="display: none">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.8453 3.35467L12.6453 0.154675C12.5955 0.105245 12.5364 0.0661387 12.4714 0.0395976C12.4064 0.0130565 12.3368 -0.000396895 12.2667 8.91428e-06H1.6C1.17565 8.91428e-06 0.768687 0.16858 0.468629 0.468638C0.168571 0.768696 0 1.17566 0 1.60001V14.4C0 14.8243 0.168571 15.2313 0.468629 15.5314C0.768687 15.8314 1.17565 16 1.6 16H14.4C14.8243 16 15.2313 15.8314 15.5314 15.5314C15.8314 15.2313 16 14.8243 16 14.4V3.73334C16.0004 3.66315 15.9869 3.59357 15.9604 3.52859C15.9339 3.46361 15.8948 3.40451 15.8453 3.35467ZM10.1333 1.06667V4.26667H5.86666V1.06667H10.1333ZM3.73333 14.9333V11.2C3.73333 11.0586 3.78952 10.9229 3.88954 10.8229C3.98956 10.7229 4.12522 10.6667 4.26666 10.6667H11.7333C11.8748 10.6667 12.0104 10.7229 12.1104 10.8229C12.2105 10.9229 12.2667 11.0586 12.2667 11.2V14.9333H3.73333ZM14.9333 14.4C14.9333 14.5414 14.8771 14.6771 14.7771 14.7771C14.6771 14.8771 14.5414 14.9333 14.4 14.9333H13.3333V11.2C13.3333 10.7757 13.1648 10.3687 12.8647 10.0686C12.5646 9.76857 12.1577 9.6 11.7333 9.6H4.26666C3.84232 9.6 3.43535 9.76857 3.13529 10.0686C2.83524 10.3687 2.66666 10.7757 2.66666 11.2V14.9333H1.6C1.45855 14.9333 1.32289 14.8771 1.22288 14.7771C1.12286 14.6771 1.06667 14.5414 1.06667 14.4V1.60001C1.06667 1.45856 1.12286 1.3229 1.22288 1.22288C1.32289 1.12287 1.45855 1.06667 1.6 1.06667H4.8V4.26667C4.8 4.54957 4.91238 4.82088 5.11242 5.02092C5.31245 5.22096 5.58377 5.33334 5.86666 5.33334H10.1333C10.4162 5.33334 10.6875 5.22096 10.8876 5.02092C11.0876 4.82088 11.2 4.54957 11.2 4.26667V1.06667H12.048L14.9333 3.95201V14.4Z"
                              fill="#383838"/>
                    </svg>
                    Save draft
                </a>
                <svg class="save_draft_svg" style="display: none" xmlns:svg="http://www.w3.org/2000/svg"
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
                <div class="quiz__stap_navigation">
                    <div class="quiz__stap_navigation_box">
                        <?php if (is_user_logged_in()): ?>
                            <div class="navigation">
                                <a href="<?php echo esc_url($has_result ? $book_link : '#'); ?>"
                                   class="navigation__right <?php echo $has_result ? '' : 'hide'; ?>">Preview
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.03393 0L5.06498 0.974515L9.37681 5.31093H2.32148e-07L2.32148e-07 6.68912L9.37681 6.68912L5.06498 11.0255L6.03393 12L12 5.99998L6.03393 0Z"
                                              fill="#F9F8F4"/>
                                    </svg>
                                </a>
                            </div>
                        <?php else: ?>
                        <?php endif; ?>
                    </div>
                    <?php if (is_user_logged_in()): ?>
                    <?php else: ?>
                        <div class="navigation__authentication">
                            <a href="#"
                               class="navigation__right_authentication open_form <?php echo $has_result ? '' : 'hide'; ?>"
                               id="open_form">Preview
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.03393 0L5.06498 0.974515L9.37681 5.31093H2.32148e-07L2.32148e-07 6.68912L9.37681 6.68912L5.06498 11.0255L6.03393 12L12 5.99998L6.03393 0Z"
                                          fill="#F9F8F4"/>
                                </svg>
                            </a>
                        </div>
                    <?php endif; ?>
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
