<?php
/**
 * Plugin Name: Create PDF Intex Agency
 * Description: Create PDF.
 * Version: 1.0.0
 * Author: Zakordonets Vasyl
 * License: GPL2
 */
require 'vendor/autoload.php';

use Fpdf\Fpdf;

class CreatePDF
{
    private function create_pdf_directory(Fpdf $pdf, $bookID, $book_name)
    {
        $file_name = $book_name . '.pdf';
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];
        $base_url = $upload_dir['baseurl'];

        $directory = $base_dir . '/pdf/' . $bookID . '/';
        $url_directory = $base_url . '/pdf/' . $bookID . '/';

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                sendTelegramMessage("track_book_status_change failed to create directory: " . $directory);
                return;
            }
        }

        $full_path = $directory . $file_name;

        try {
            $pdf->Output('F', $full_path);
            update_post_meta($bookID, 'book_pdf', $url_directory . $file_name);
            update_post_meta($bookID, 'book_pdf_path', $full_path);
        } catch (Exception $e) {
            sendTelegramMessage("track_book_status_change PDF creation error: " . $e->getMessage());
        }
    }

    public function __construct()
    {
    }

    private function get_height_margin($img)
    {
        $img = $this->is_file_exist($img, true);

        $data = [];

        list($width, $height) = getimagesize($img);

        $data['img_height'] = $height * 0.01;

        if ($width > 600 || $height > 600) {
            $resize_image = image_resize($img, 550, 550);

            if (!is_object($resize_image)) {
                list($width, $height) = getimagesize($resize_image);

                $y_in_inch = ((816 - $height) / 2) * 0.010;

                if ($y_in_inch < 1.8) {
                    $y_in_inch = 0.8;
                }
                $data['y_in_inch'] = $y_in_inch;
                $data['img_height'] = $height * 0.01;;
            }

        } else {
            $y_in_inch = ((816 - $height) / 2) * 0.010;

            if ($y_in_inch < 1.8) {
                $y_in_inch = 0.4;
            }

            $data['y_in_inch'] = $y_in_inch;
        }

        return $data;
    }

    private function is_file_exist($file, $get_file = false, $url = false)
    {
        if (!$url) {
            if (file_exists(get_attached_file($file))) {
                if ($url) {
                    return $file;
                } else {
                    if ($get_file) {
                        return get_attached_file($file);
                    }
                    return wp_get_attachment_url($file);
                }
            } else {
                if ($get_file) {
                    return get_template_directory() . '/img/noimage.png';
                }
                return 'https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg';
            }
        } else {
            if (file_exists($file)) {
                if ($url) {
                    return $file;
                } else {
                    if ($get_file) {
                        return get_attached_file($file);
                    }
                    return wp_get_attachment_url($file);
                }
            } else {
                if ($get_file) {
                    return get_template_directory() . '/img/noimage.png';
                }
                return 'https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg';
            }
        }
    }

    private function right_image($img, Fpdf $pdf, $page_width = 816, $page_height = 816)
    {
        $img = $this->is_file_exist($img, true);

        list($width, $height) = getimagesize($img);

        if ($width > 600 || $height > 600) {
            $resize_image = image_resize($img, 550, 550);

            if (!is_object($resize_image)) {
                list($width, $height) = getimagesize($resize_image);

                $x_in_inch = (($page_height - $width) / 2) * 0.010;

                $y_in_inch = (($page_width - $height) / 2) * 0.010;

                if ($y_in_inch < 1.8) {
                    $y_in_inch = 0.8;
                }

                $pdf->Image($resize_image, $x_in_inch, $y_in_inch);
            }
        } else {
            $x_in_inch = (($page_height - $width) / 2) * 0.010;

            $y_in_inch = (($page_width - $height) / 2) * 0.010;

            if ($y_in_inch < 1.8) {
                $y_in_inch = 0.4;
            }

            $pdf->Image($img, $x_in_inch, $y_in_inch);
        }
    }

    private function margin_right($img, $page_width = 816)
    {
        $img = $this->is_file_exist($img, true);

        $height = getimagesize($img)[1];

        if ($height > 600) {
            $resize_image = image_resize($img, 550, 550);

            if (!is_object($resize_image)) {
                $height = getimagesize($resize_image)[1];

                $y_in_inch = (($page_width - $height) / 2) * 0.010;

                if ($y_in_inch < 1.8) {
                    $y_in_inch = 0.4;
                }

                $margin_from = $height * 0.0105 + $y_in_inch;
                error_log('margin_from5 :' . $margin_from);

            }
        } else {
            $y_in_inch = (($page_width - $height) / 2) * 0.010;

            if ($y_in_inch < 1.8) {
                $y_in_inch = 0.5;
            }

            $margin_from = $height * 0.010 + $y_in_inch;
            error_log('margin_from4 :' . $margin_from);
        }
        if ($y_in_inch == 0.5) {
            $margin_from = $margin_from * 0.2 + $margin_from;
            error_log('margin_from3 :' . $margin_from);
        } else {
            $margin_from = $margin_from * 0.05 + $margin_from;
        }
        return $margin_from;
    }

    private function centerImage($img, Fpdf $pdf, $page_width = 816, $page_height = 816, $url = false): void
    {
        if ($url) {
            $img = $this->is_file_exist($img, true, true);
        } else {
            $img = $this->is_file_exist($img, true);
        }
        list($width, $height) = getimagesize($img);

        if ($width > 600 || $height > 600) {
            $resize_image = image_resize($img, 550, 550);

            if (!is_object($resize_image)) {
                list($width, $height) = getimagesize($resize_image);

                $x_in_inch = (($page_height - $width) / 2) * 0.010;

                $y_in_inch = (($page_width - $height) / 2) * 0.010;

                $pdf->Image($resize_image, $x_in_inch, $y_in_inch);
            }

        } else {
            $x_in_inch = (($page_height - $width) / 2) * 0.010;

            $y_in_inch = (($page_width - $height) / 2) * 0.010;

            $pdf->Image($img, $x_in_inch, $y_in_inch);
        }
    }

    public function coloring($id, $type)
    {
        $book_name = 'Coloring';

        $pdf = new FPDF('P', 'in');

        $bookID = $id;

        $terms = wp_get_object_terms($bookID, 'templates', ['fields' => 'all']);
        $currentTerm = $terms[0] ?? null;

        $imgBG = '';
        if ($currentTerm) {
            $bg_field = get_field('book_background_image', $currentTerm);
            if ($bg_field && isset($bg_field['ID'])) {
                $imgBG = get_attached_file($bg_field['ID']);
                $imgBG = $this->convert_png_to_8bit($imgBG);
            } else {
                sendTelegramMessage("track_book_status_change no background image found");
            }
        }

        $tasks = get_post_meta($bookID, 'book_coloring_tasks', true);
        $images = [];

        if (is_array($tasks)) {
            foreach ($tasks as $task) {
                if ($type === 'watermark') {
                    $attachment_id = $task['watermark'] ?? $task['original'] ?? null;
                } else if ($type === 'result') {
                    $attachment_id = $task['result'] ?? $task['original'] ?? null;
                } else {
                    $attachment_id = $task['original'] ?? null;
                }

                if ($attachment_id && is_numeric($attachment_id)) {
                    $img_path = get_attached_file($attachment_id);

                    if (!$img_path || !file_exists($img_path)) {
                        $img_url = wp_get_attachment_url($attachment_id);
                        $img_path = str_replace(site_url('/'), ABSPATH, $img_url);
                    }

                    if ($img_path && file_exists($img_path)) {
                        $images[] = $this->convert_png_to_8bit($img_path);
                    } else {
                        sendTelegramMessage("track_book_status_change image missing or not found: " . $img_path);
                    }
                }
            }
        }

        $fontFile = get_template_directory() . '/fonts/arialroundedmtbold.php';
        if (file_exists($fontFile)) {
            $pdf->AddFont('Arial', 'B', 'arialroundedmtbold.php');
        }

        $pdf->AddPage('P');
        $logoPath = get_template_directory() . '/img/logobook.png';
        if (file_exists($logoPath)) {
            $this->centerImage($logoPath, $pdf, 816, 816, true);
        } else {
            sendTelegramMessage("track_book_status_change logo not found: " . $logoPath);
        }

        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetY(7.2);
        $pdf->SetTextColor(64, 140, 92);
        $pdf->MultiCell(0, 0.5, "Coloring book by Barvin", 0, 'C');
        $pdf->SetTextColor(0, 0, 0);

        foreach ($images as $imgPath) {
            if (!file_exists($imgPath)) {
                sendTelegramMessage("track_book_status_change skipping missing image: " . $imgPath);
                continue;
            }

            $imgSize = @getimagesize($imgPath);
            if (!$imgSize) {
                sendTelegramMessage("track_book_status_change invalid image file: " . $imgPath);
                continue;
            }

            [$imgW, $imgH] = $imgSize;
            $orientation = ($imgW > $imgH) ? 'L' : 'P';
            $pageW = ($orientation === 'L') ? 11.69 : 8.27;
            $pageH = ($orientation === 'L') ? 8.27 : 11.69;

            $pdf->AddPage($orientation);

            if ($imgBG && file_exists($imgBG)) {
                $pdf->Image($imgBG, 0, 0, $pageW, $pageH);
            }

            $scale = $pageW / $imgW;
            $newW = $pageW;
            $newH = $imgH * $scale;
            $x = 0;
            $y = ($pageH - $newH) / 2;

            $pdf->Image($imgPath, $x, $y, $newW, $newH);
        }

        try {
            $this->create_pdf_directory($pdf, $bookID, $book_name);
        } catch (Exception $e) {
            sendTelegramMessage("track_book_status_change PDF creation error: " . $e->getMessage());
        }
    }

    public function starting_school($id)
    {
        $book_name = 'Starting-School';
        $pdf = new Fpdf('P', 'in', [8.5, 8.5]);
        $bookID = $id;
        $bookMeta = get_post_meta($bookID, 'answers');
        $answers = $bookMeta[0];
        $pages = 18;
        $terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));
        $currentTerm = $terms[0];
        $imgBG = esc_url(get_field('book_background_image', $currentTerm)['url']);
        $imgBGArray = explode('?', $imgBG);
        if (array_key_exists(0, $imgBGArray)) {
            $imgBG = $imgBGArray[0];
        }
        $kid = $answers['answer_0']['value'];
        $pronoun = $answers['answer_1']['value'];
        $sub = '';
        $pos = '';
        $obj = '';

        switch ($pronoun) {
            case 'he/him/his':
                $sub = 'he';
                $obj = 'him';
                $pos = 'his';
                break;
            case 'she/her/hers':
                $sub = 'she';
                $obj = 'her';
                $pos = 'her';
                break;
            case 'they/them/theirs':
                $sub = 'they';
                $obj = 'them';
                $pos = 'their';
                break;
        }
        $adult1 = $answers['answer_2']['value'];
        $adult2 = $answers['answer_3']['value'];
        $condition1 = $answers['answer_4']['value'];
        $adultSitter = $answers['answer_5']['value'];
        $photoHomeAct1 = $answers['answer_6']['value'];
        $schoolOld = $answers['answer_7']['value'];
        $photoActSchool1 = $answers['answer_8']['value'];
        $schoolNew = $answers['answer_9']['value'];
        $photoSchoolNew = $answers['answer_10']['value'];
        $teacherName = $answers['answer_11']['value'];
        $photoTeacher = $answers['answer_12']['value'];
        $photoActivitySchool2 = $answers['answer_13']['value'];
        $photoSchoolAccessories = $answers['answer_14']['value'];
        $adultDropOff = $answers['answer_15']['value'];
//$condition2                = $answers['answer_16']['value'];
        $adultPickUp = $answers['answer_16']['value'];
        $transport = $answers['answer_17']['value'];
        $photoTransport = $answers['answer_18']['value'];
        $afterSchoolActivity = $answers['answer_19']['value'];
        $photoAfterSchoolActivity1 = $answers['answer_20']['value'];
        $photoCover = $answers['answer_21']['value'];

        $pdf->AddFont('ArialRoundedMT', 'B', 'arialroundedmtbold.php');
        $pdf->AddPage();
        $this->centerImage(get_template_directory() . '/img/logobook.png', $pdf, 816, 816, true);
        $pdf->SetFont('ArialRoundedMT', 'B', 24);
        $pdf->SetY(7.2);
        $pdf->SetTextColor(64, 140, 92);
        $pdf->MultiCell(0, 0.5, 'upandcomingbooks.com', 0, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('ArialRoundedMT', 'B', 42);
        $data_margin = $this->get_height_margin($photoCover);
        $text = $kid . ' Goes to School';
        $y_inch = $data_margin['y_in_inch'];
        $img_height = $data_margin['img_height'];
        if (strlen($text) > 36) {
            $widthTextInInch = 0.38;
        } else {
            $widthTextInInch = 0.58;
        }
        $margin = $y_inch + $img_height + $widthTextInInch;
        if ($margin > 6.6 && strlen($text) > 36) {
            $pdf->SetFont('ArialRoundedMT', 'B', 34);
        }
        $pdf->SetMargins(0.5, $margin, 0.8);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->right_image($photoCover, $pdf, 816, 816, true);
        $pdf->MultiCell(0, 0.5, $text, 0, 'C');
        $pdf->SetMargins(0.5, 3.8, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        if ($condition1 != 'At home') {
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = 'During the week, ' . $kid . ' goes to ' . $schoolOld . '. ' . $kid . ' has so much fun learning and playing with ' . $pos . ' friends and teachers from ' . $schoolOld . '.';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage($photoActSchool1, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        } else {
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = 'During the week, ' . $kid . ' spends time with ' . $adultSitter . '.' . $kid . ' has so much fun learning and playing with ' . $adultSitter . '.';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage($photoHomeAct1, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        }
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'Soon, ' . $kid . ' will start at a new school called ' . $schoolNew . '.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoSchoolNew, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = $kid . ' will have new teachers, like ' . $teacherName . ',and ' . $sub . ' will make new friends.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoTeacher, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = $kid . ' will learn many new things, make art, read stories, sing songs, and play.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoActivitySchool2, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = $kid . ' will eat lunch at school with ' . $pos . ' new friends. ' . ucfirst($sub) . ' will take naps at school and go to the bathroom at school.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoSchoolAccessories, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'On school days, ' . $kid . ' will ' . $transport . ' to school with ' . $adultDropOff . '. At the end of the day, ' . $adultPickUp . ' will pick ' . $obj . ' up.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoTransport, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'After school, ' . $kid . ' will ' . $afterSchoolActivity;
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoAfterSchoolActivity1, $pdf);
        $pdf->SetMargins(0.5, 2.5, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        if ($condition1 !== 'At home') {
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = 'Going to a new school is a big change. Sometimes ' . $kid . ' will want ' . $adult1;
            if ($adult2) {
                $text = $text . ' and ' . $adult2;
            }
            $text = $text . ' during the school day. ' . ucfirst($sub) . ' may miss ' . $pos . ' friends and teachers from ' . $schoolOld . '. ' . $kid . "'s teachers and friends from " . $schoolNew . ' will help ' . $obj . ' feel better and have fun!';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage(get_template_directory() . '/img/Starting-School_STOCK_IMAGE_KIDS-1.jpg', $pdf, 816, 816, true);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        } else {
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = 'Going to a new school is a big change. Sometimes ' . $kid . ' will want ' . $adult1;
            if ($adult2) {
                $text = $text . ' and ' . $adult2;
            }
            $text = $text . ' during the school day. Sometimes ' . $kid . ' will miss playing with ' . $adultSitter . ' and napping in ' . $pos . ' own bed. ' . $kid . "'s teachers and friends from " . $schoolNew . ' will help ' . $obj . ' feel better and have fun!';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage(get_template_directory() . '/img/Starting-School_STOCK_IMAGE_KIDS-1.jpg', $pdf, 816, 816, true);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        }
        $this->create_pdf_directory($pdf, $bookID, $book_name);
    }

    public function the_potty($id)
    {
        $book_name = 'Potty';
        $pdf = new Fpdf('P', 'in', [8.5, 8.5]);

        $bookID = $id;
        $bookMeta = get_post_meta($bookID, 'answers');
        $answers = $bookMeta[0];
        $pages = 22;
        $terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));
        $currentTerm = $terms[0];
        $imgBG = esc_url(get_field('book_background_image', $currentTerm)['url']);
        $imgBGArray = explode('?', $imgBG);
        if (array_key_exists(0, $imgBGArray)) {
            $imgBG = $imgBGArray[0];
        }

        $kid1 = $answers['answer_0']['value'];
        $pronoun = $answers['answer_1']['value'];
        $sub = '';
        $pos = '';
        $obj = '';

        switch ($pronoun) {
            case 'he/him/his':
                $sub = 'he';
                $obj = 'him';
                $pos = 'his';
                break;
            case 'she/her/hers':
                $sub = 'she';
                $obj = 'her';
                $pos = 'her';
                break;
            case 'they/them/theirs':
                $sub = 'they';
                $obj = 'them';
                $pos = 'their';
                break;
        }

        $adult1 = $answers['answer_2']['value'];
        $adult2 = $answers['answer_3']['value'];
        $photoKid1Baby = $answers['answer_4']['value'];
        $photoKid1Big = $answers['answer_5']['value'];
        $photoKid1GrownUp = $answers['answer_6']['value'];
        $photoKid1PottySteps = $answers['answer_7']['value'];
        $photoKid1AfterPotty = $answers['answer_8']['value'];
        $photoBathroomHome = $answers['answer_9']['value'];
        $condition1 = $answers['answer_10']['value'];
        $teachersNames = $answers['answer_11']['value'];
        $photoBathroomSchool = $answers['answer_12']['value'];
        $condition2 = $answers['answer_13']['value'];
        $photoTravelPotty = $answers['answer_14']['value'];
        $photoKid1PottyTimes = $answers['answer_15']['value'];
        $photoNewClothes = $answers['answer_16']['value'];
        $photoKid1Smiles = $answers['answer_17']['value'];
        $photoCover = $answers['answer_18']['value'];

        $pdf->AddFont('ArialRoundedMT', 'B', 'arialroundedmtbold.php');
        $pdf->AddPage();
        $this->centerImage(get_template_directory() . '/img/logobook.png', $pdf, 816, 816, true);
        $pdf->SetFont('ArialRoundedMT', 'B', 24);
        $pdf->SetY(7.2);
        $pdf->SetTextColor(64, 140, 92);
        $pdf->MultiCell(0, 0.5, 'upandcomingbooks.com', 0, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('ArialRoundedMT', 'B', 42);
        $data_margin = $this->get_height_margin($photoCover);
        $text = $kid1 . ' Uses the Potty';
        $y_inch = $data_margin['y_in_inch'];
        $img_height = $data_margin['img_height'];
        if (strlen($text) > 36) {
            $widthTextInInch = 0.38;
        } else {
            $widthTextInInch = 0.58;
        }
        $margin = $y_inch + $img_height + $widthTextInInch;
        if ($margin > 6.6 && strlen($text) > 36) {
            $pdf->SetFont('ArialRoundedMT', 'B', 34);
        }
        $pdf->SetMargins(0.5, $margin, 0.8);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->right_image($photoCover, $pdf, 816, 816, true);
        $pdf->MultiCell(0, 0.5, $text, 0, 'C');
        $pdf->SetMargins(0.5, 3, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        if ($adult2) {
            $text = 'When ' . $kid1 . ' was a baby and before ' . $sub . ' learned to speak, ' . $sub . " couldn't tell " . $adult1 . ' and ' . $adult2 . ' when ' . $sub . ' had to go pee and poop. ' . $kid1 . ' would wear a diaper to catch pee and poop and keep' . $pos . ' clothes clean.';
        } else {
            $text = 'When ' . $kid1 . ' was a baby and before ' . $sub . ' learned to speak, ' . $sub . " couldn't tell " . $adult1 . ' when ' . $sub . ' had to go pee and poop. ' . $kid1 . ' would wear a diaper to catch pee and poop and keep' . $pos . ' clothes clean.';
        }
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetMargins(0.5, 3.8, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoKid1Baby, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $verb = 'knows';
        if ($pronoun == 'they/them/their') {
            $verb = 'know';
        }
        $text = 'Now ' . $kid1 . ' is a big kid. ' . ucfirst($sub) . ' ' . $verb . ' how ask for things ' . $pos . ' body needs, like water, food, hugs, and the potty.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoKid1Big, $pdf);
        $pdf->SetMargins(0.5, 2.8, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'When ' . $kid1 . ' feels a pee or poop ready to come out, ' . $sub . ' will tell a grown up who can help ' . $obj . " go to the potty. It's good to take a break from playing, eating, or story time to go to the potty!";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoKid1GrownUp, $pdf);
        $pdf->SetMargins(0.5, 3.2, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'When ' . $kid1 . ' gets to the bathroom, ' . $sub . ' will pull down ' . $pos . ' underwear, go pee or poop in the potty, flush the pee or poop down the toilet, dress, and wash hands.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoKid1PottySteps, $pdf);
        $pdf->SetMargins(0.5, 3.5, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'After washing hands, ' . $kid1 . ' can go back to playing or eating or story time. ' . ucfirst($pos) . ' toys, food, or books will be waiting where ' . $sub . ' left them.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoKid1AfterPotty, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'At home, ' . $adult1 . ' and ' . $adult2 . ' will help ' . $kid1 . ' use the potty in this bathroom.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoBathroomHome, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        if ($condition1 == 'Yes') {
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = 'At school, ' . $teachersNames . ' will help ' . $kid1 . ' use the potty in this bathroom.';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage($photoBathroomSchool, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        }
        if ($condition2 == "Yes") {
            $pdf->SetMargins(0.5, 2.8, 0.5);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = "In other places, like the park, the grocery store, or a friend's house, there will be other bathrooms that " . $kid1 . ' can use. ' . $adult1 . ' and ' . $adult2 . 'will also carry a travel potty for ' . $kid1 . "to use in places that don't have a bathroom.";
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);

            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            if ($photoTravelPotty) {
                $this->centerImage($photoBathroomSchool, $pdf);
            } else {
                $this->centerImage(ABSPATH . '/wp-content/uploads/2022/05/Potty_STOCK_RESTROOM_SIGN-1-scaled.jpg', $pdf, 816, 816, true);
            }
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        } else {
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = "In other places, like the park, the grocery store, or a friend's house, there will be other bathrooms that " . $kid1 . " can use. Many bathrooms have a sign on their door like this one.";
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage(838, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        }
        $verb = "doesn't";
        if ($pronoun == 'they/them/their') {
            $verb = "don't";
        }
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'There will be times when grown ups tell ' . $kid1 . ' to sit on the potty even if ' . $sub . ' ' . $verb . " feel a pee or poop coming. It's good to try peeing or pooping in the potty before going to sleep, after waking up, before going outside, and after coming inside.";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoKid1PottyTimes, $pdf);
        $pdf->SetMargins(0.5, 3.2, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'If ' . $kid1 . " doesn't get to the potty in time and pees or poops in " . $pos . ' underwear, ' . $sub . ' will ask a grown up to help ' . $obj . ' clean up and change into clean, dry clothes.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoNewClothes, $pdf);
        $pdf->SetMargins(0.5, 3.5, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'But most of the time, ' . $kid1 . ' will pee and poop in the potty. It feels good to use the potty and have clean, dry underwear!';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoKid1Smiles, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $this->create_pdf_directory($pdf, $bookID, $book_name);
    }

    public function wedding_book($id)
    {
        $book_name = 'In-A-Wedding';
        $pdf = new Fpdf('P', 'in', [8.5, 8.5]);
        $bookID = $id;
        $bookMeta = get_post_meta($bookID, 'answers');
        $answers = $bookMeta[0];
        $pages = 24;
        $terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));
        $currentTerm = $terms[0];
        $imgBG = esc_url(get_field('book_background_image', $currentTerm)['url']);
        $imgBGArray = explode('?', $imgBG);
        if (array_key_exists(0, $imgBGArray)) {
            $imgBG = $imgBGArray[0];
        }


        $kid = rtrim($answers['answer_0']['value']);
        $pronoun = $answers['answer_1']['value'];
        $sub = '';
        $pos = '';
        switch ($pronoun) {
            case 'he/him/his':
                $sub = 'he';
                $obj = 'him';
                $pos = 'his';
                break;
            case 'she/her/hers':
                $sub = 'she';
                $obj = 'her';
                $pos = 'hers';
                break;
            case 'they/them/theirs':
                $sub = 'they';
                $obj = 'them';
                $pos = 'theirs';
                break;
        }

        $couple_1 = $answers['answer_2']['value'];
        $photo_couple = $answers['answer_3']['value'];
        $wedding_role = $answers['answer_4']['value'];
        $ring_taker = $answers['answer_5']['value'];
        $photoWeddingNew = $answers['answer_6']['value'];
        $isFirstWedding = $answers['answer_7']['value'];
        $couple_2 = $answers['answer_8']['value'];
        $photoKidCouple = $answers['answer_9']['value'];
        $photoWedding = $answers['answer_10']['value'];
        $weddingAttire = $answers['answer_11']['value'];
        $photoWeddingAttire = $answers['answer_12']['value'];
        $photoVenue = $answers['answer_13']['value'];
        $rehersalDinner = $answers['answer_14']['value'];
        $photoRehersal = $answers['answer_15']['value'];
        $photoCover = $answers['answer_16']['value'];

        $pdf->AddFont('ArialRoundedMT', 'B', 'arialroundedmtbold.php');
        $pdf->AddPage();
        $this->centerImage(get_template_directory() . '/img/logobook.png', $pdf, 816, 816, true);
        $pdf->SetFont('ArialRoundedMT', 'B', 24);
        $pdf->SetY(7.2);
        $pdf->SetTextColor(64, 140, 92);
        $pdf->MultiCell(0, 0.5, 'upandcomingbooks.com', 0, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('ArialRoundedMT', 'B', 42);
        $data_margin = $this->get_height_margin($photoCover);
        $text = $kid . ' in the Wedding';
        $y_inch = $data_margin['y_in_inch'];
        $img_height = $data_margin['img_height'];
        if (strlen($text) > 36) {
            $widthTextInInch = 0.38;
        } else {
            $widthTextInInch = 0.58;
        }
        $margin = $y_inch + $img_height + $widthTextInInch;
        if ($margin > 6.6 && strlen($text) > 36) {
            $pdf->SetFont('ArialRoundedMT', 'B', 34);
        }
        $pdf->SetMargins(0.5, $margin, 0.8);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->right_image($photoCover, $pdf, 816, 816, true);
        $pdf->MultiCell(0, 0.5, $text, 0, 'C');
        $pdf->SetMargins(0.5, 3.8, 0.5);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $text = $couple_1 . ' are getting married!';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_couple, $pdf);
        $pdf->SetMargins(0.5, 3.2, 0.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = $couple_1 . ' gave ' . $kid . ' the important role of ' . lcfirst($wedding_role) . ' in their wedding ceremony. The ' . lcfirst($wedding_role) . ' is a special helper who walks down the wedding aisle holding';
        switch ($wedding_role) {
            case 'Ring bearer':
                $text = $text . ' a little pillow with wedding rings.';
                break;
            case 'Flower girl':
                $text = $text . ' a basket of flower petals.';
                break;
            case 'Train bearer':
                $text = $text . " the bride's train.";
                break;
            case 'Sign carrier':
                $text = $text . ' a sign.';
                break;
            default:
                break;
        }
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoWeddingNew, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        if ($isFirstWedding == 'No') {
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = $kid . ' has been to a wedding before. ' . ucfirst($sub) . ' went to ' . $couple_2 . "'s wedding when " . $sub . ' was younger.';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage($photoKidCouple, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        }
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'For ' . $couple_1 . "'s wedding, " . $kid . " will wear ";
        switch ($weddingAttire) {
            case 'dress':
                $text = $text . 'a special dress. Other guests at the wedding will be wearing dresses, too.';
                break;
            case 'suit':
                $text = $text . 'a special suit. Other guests at the wedding will be wearing suits, too.';
                break;
            case 'tuxedo':
                $text = $text . 'a special black and white suit called a tuxedo. Other guests at the wedding will be wearing tuxedos, too.';
                break;
            case 'fancy clothes':
                $text = $text . 'fancy clothes. Other guests at the wedding will be wearing fancy clothes, too.';
                break;
            default:
                break;
        }
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoWeddingAttire, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'Before ' . $couple_1 . "'s wedding, there will be a rehearsal where " . $kid . ' will practice walking down the aisle holding ';
        switch ($wedding_role) {
            case 'Ring bearer':
                $text = $text . 'the ring pillow.';
                break;
            case 'Flower girl':
                $text = $text . 'the basket and dropping flower petals.';
                break;
            case 'Train bearer':
                $text = $text . "the bride's train.";
                break;
            case 'Sign carrier':
                $text = $text . 'the sign.';
                break;
            default:
                break;
        }
        $text = $text . ' The rehearsal helps everyone know what to do on the day of the wedding.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoVenue, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        if ($rehersalDinner == 'Yes') {
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = 'After the rehearsal, there will be a yummy meal with ' . $couple_1 . "'s family and friends. Then everyone will head to bed to get a good night's sleep, so they have lots of energy for the wedding the next day.";
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage($photoRehersal, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        }
        if ($isFirstWedding == 'No') {
            $pdf->SetMargins(0.5, 3, 0.5);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = 'On the day of the wedding, ' . $kid . ' will get dressed in ' . $pos . ' ' . $weddingAttire . '. ' . $kid . ' will pose for photos with ' . $couple_1 . '. The photographer will ask ' . $kid . ' to stand still and smile for the camera. All of the photographs will help ' . $couple_1 . ' remember their special day.';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage($photoWedding, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        }
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = "When it's time for the wedding to start, " . $kid . ', ' . $couple_1 . ' and other people in the ceremony will line up and wait for their turn to walk down the aisle.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photoVenue, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        if ($wedding_role == 'Ring bearer') {
            $pdf->SetMargins(0.5, 2.2, 0.5);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = "When it's " . $kid . "'s turn, " . $sub . ' will walk down the aisle holding the ring pillow. ' . $couple_1 . "'s friends and family will be watching and smiling at " . $kid . '. ' . $kid . ' can smile back. At the end of the aisle, ' . $kid . ' will give the ring pillow to ' . $ring_taker . '. Then ' . $kid . ' will find ' . $pos . ' seat and sit quietly for the rest of the wedding ceremony.';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage($photoWeddingNew, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        } else {
            $pdf->SetMargins(0.5, 2.5, 0.5);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $text = "When it's " . $kid . "'s turn, " . $sub . " will walk down the aisle holding ";
            switch ($wedding_role) {
                case 'Ring bearer':
                    $text = $text . 'the ring pillow.';
                    break;
                case 'Flower girl':
                    $text = $text . 'the basket and dropping flower petals.';
                    break;
                case 'Train bearer':
                    $text = $text . "the bride's train.";
                    break;
                case 'Sign carrier':
                    $text = $text . 'the sign.';
                    break;
                default:
                    break;
            }
            $text = $text . ' ' . $couple_1 . "'s friends and family will be watching and smiling at " . $kid . '.' . $kid . ' can smile back. At the end of the aisle, ' . $kid . ' will find ' . $pos . ' seat and sit quietly for the rest of the wedding ceremony.';
            str_replace('â€™', "'", $text);
            $pdf->MultiCell(0, 0.5, $text, 0, 'L');
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
            $pdf->SetMargins(0.5, 3, 0.5);
            $pdf->AddPage();
            $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
            $this->centerImage($photoWeddingNew, $pdf);
            $pdf->SetFont('ArialRoundedMT', 'B', 22);
            $pdf->SetY(7.2);
            $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
            $pdf->SetFont('ArialRoundedMT', 'B', 27);
        }
        $pdf->SetMargins(0.5, 2.8, 0.5);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = 'The ceremony is over when ' . $couple_1 . ' kiss and walk back up the aisle together. After the ceremony, all the wedding guests will go to the reception, a fun party with food, music, and dancing to celebrate ' . $couple_1 . '.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_couple, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $this->create_pdf_directory($pdf, $bookID, $book_name);
    }

    public function pacifier_book($id)
    {
        $book_name = 'Bye-Pacifier';
        $pdf = new Fpdf('P', 'in', [8.5, 8.5]);
        $bookID = $id;
        $bookMeta = get_post_meta($bookID, 'answers');
        $answers = $bookMeta[0];
        $pages = 18;
        $terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));
        $currentTerm = $terms[0];
        $imgBG = esc_url(get_field('book_background_image', $currentTerm)['url']);
        $imgBGArray = explode('?', $imgBG);
        if (array_key_exists(0, $imgBGArray)) {
            $imgBG = $imgBGArray[0];
        }


        $kid = rtrim($answers['answer_0']['value']);
        $pronoun = $answers['answer_1']['value'];
        $sub = '';
        $pos = '';
        $was = '';
        $obj = '';
        $is = '';
        switch ($pronoun) {
            case 'he/him/his':
                $sub = 'he';
                $obj = 'him';
                $pos = 'his';
                $was = 'was';
                $is = 'is';
                $need = 'needs';
                break;
            case 'she/her/hers':
                $sub = 'she';
                $obj = 'her';
                $pos = 'hers';
                $was = 'was';
                $is = 'is';
                $need = 'needs';
                break;
            case 'they/them/theirs':
                $sub = 'they';
                $obj = 'them';
                $pos = 'theirs';
                $was = 'where';
                $is = 'are';
                $need = 'need';
                break;
        }


        $pacifier7_name = $answers['answer_2']['value'];
        $photo_child_using_a_pacifier = $answers['answer_3']['value'];
        $child_uses_a_pacifier = $answers['answer_4']['value'];
        $photo_recent = $answers['answer_5']['value'];
        $grownups = $answers['answer_6']['value'];
        $photo_snuggling = $answers['answer_7']['value'];
        $photo_favorite_doll = $answers['answer_8']['value'];
        $photo_eating = $answers['answer_9']['value'];
        $photo_talking = $answers['answer_10']['value'];
        $photo_giving_a_kiss = $answers['answer_11']['value'];
        $photo_looking_relaxed = $answers['answer_12']['value'];
        $photo_smiling = $answers['answer_13']['value'];
        $photoCover = $answers['answer_14']['value'];

        $pdf->AddFont('ArialRoundedMT', 'B', 'arialroundedmtbold.php');
        $pdf->AddPage();
        $this->centerImage(get_template_directory() . '/img/logobook.png', $pdf, 816, 816, true);
        $pdf->SetFont('ArialRoundedMT', 'B', 24);
        $pdf->SetY(7.2);
        $pdf->SetTextColor(64, 140, 92);
        $pdf->MultiCell(0, 0.5, 'upandcomingbooks.com', 0, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('ArialRoundedMT', 'B', 42);
        $data_margin = $this->get_height_margin($photoCover);
        $text = $kid . ' is Too Big for ' . ucfirst($pacifier7_name);
        $y_inch = $data_margin['y_in_inch'];
        $img_height = $data_margin['img_height'];
        if (strlen($text) > 36) {
            $widthTextInInch = 0.38;
        } else {
            $widthTextInInch = 0.58;
        }
        $margin = $y_inch + $img_height + $widthTextInInch;
        if ($margin > 6.6 && strlen($text) > 36) {
            $pdf->SetFont('ArialRoundedMT', 'B', 34);
        }
        $pdf->SetMargins(0.5, $margin, 0.8);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->right_image($photoCover, $pdf, 816, 816, true);
        $pdf->MultiCell(0, 0.5, $text, 0, 'C');
        $pdf->SetMargins(0.5, 3.8, 0.5);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $text = $kid . ' has used a ' . $pacifier7_name . ' since ' . $sub . ' ' . $was . ' a little baby.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_child_using_a_pacifier, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->SetMargins(0.5, 3.2, 0.5);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = $kid . ' still uses a ' . $pacifier7_name . ' ' . $child_uses_a_pacifier . ' but ' . $pos . ' ' . $is . ' growing and getting too big for ' . $pacifier7_name . '.';
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_recent, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = "It's easier to do big kid things without a $pacifier7_name. $kid $need $pos mouth to eat and drink.";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_eating, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = "$kid $need $pos mouth to talk, sing songs, and laugh. ";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_talking, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = "$kid $need $pos mouth to give kisses, brush teeth, and blow bubbles. $kid is getting too big for a $pacifier7_name.";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_giving_a_kiss, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = "It's hard to say bye-bye to a $pacifier7_name, but big kids can do hard things. And $kid has other ways to feel happy, calm, and safe. $kid can snuggle $grownups.";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_snuggling, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = "$kid can squeeze $pos dolls and stuffies.";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_favorite_doll, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = "$kid can close $pos eye and take deep breathes in and out, in and out. ";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_looking_relaxed, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $text = "$kid does not need $pos $pacifier7_name anymore. $sub $is ready to say 'Bye-Bye, $pacifier7_name!'";
        str_replace('â€™', "'", $text);
        $pdf->MultiCell(0, 0.5, $text, 0, 'L');
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $pdf->SetFont('ArialRoundedMT', 'B', 27);
        $pdf->AddPage();
        $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
        $this->centerImage($photo_smiling, $pdf);
        $pdf->SetFont('ArialRoundedMT', 'B', 22);
        $pdf->SetY(7.2);
        $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 2, 0, 'C');
        $this->create_pdf_directory($pdf, $bookID, $book_name);

    }

    private function convert_png_to_8bit($file)
    {
        $info = pathinfo($file);
        if (strtolower($info['extension']) !== 'png') return $file;

        $img = @imagecreatefrompng($file);
        if (!$img) return $file;

        $dest = $info['dirname'] . '/' . $info['filename'] . '_8bit.png';
        imagepng($img, $dest);
        imagedestroy($img);
        return $dest;
    }
}

new CreatePDF();