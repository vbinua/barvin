<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package upwork
 */
require __DIR__.'/fpdf/fpdf.php';


function is_file_exist($file, $get_file = false, $url = false)
{
    if(!$url)
    {
        if(file_exists( get_attached_file($file)))
        {
            if($url)
            {
                return $file;
            }
            else
            {
                if($get_file)
                {
                    return get_attached_file($file);
                }
                return wp_get_attachment_url($file);
            }
        }
        else
        {
            if($get_file)
            {
                return  get_template_directory() . '/img/noimage.png';
            }
            return 'https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg';
        }
    }
    else
    {
        if(file_exists($file))
        {
            if($url)
            {
                return $file;
            }
            else
            {
                if($get_file)
                {
                    return get_attached_file($file);
                }
                return wp_get_attachment_url($file);
            }
        }
        else
        {
            if($get_file)
            {
                return  get_template_directory() . '/img/noimage.png';
            }
            return 'https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg';
        }
    }
}

/**
 * @param $img
 * @param FPDF $pdf
 * @return void
 */
function right_image($img, FPDF $pdf,$page_width = 816, $page_height = 816, $is_front = false)
{
    $img = is_file_exist($img,true);

    list($width,$height) = getimagesize($img);

    $right = 0;

    if($width > 600 || $height > 600)
    {
        $resize_image = image_resize($img,550,550);

        if(!is_object($resize_image))
        {
            list($width,$height) = getimagesize($resize_image);

            $x_in_inch = (($page_height - $width) / 2 + 870)* 0.010 ;

            $y_in_inch = (($page_width - $height) / 2)* 0.010;

            if($y_in_inch < 1.8)
            {
                $y_in_inch = 0.8;
            }

            $pdf->Image($resize_image,$x_in_inch,$y_in_inch);
        }

    }
    else
    {
        $x_in_inch = (($page_height - $width) / 2+ 870)* 0.010;

        $y_in_inch = (($page_width - $height) / 2)* 0.010;

        if($y_in_inch < 1.8)
        {
            $y_in_inch = 0.5;
        }

        $pdf->Image($img,$x_in_inch,$y_in_inch);
    }
}

function get_height_margin($img)
{
    $img = is_file_exist($img,true);

    $data = [];

    list($width,$height) = getimagesize($img);

    $data['img_height'] = $height * 0.01;

    $right = 0;

    if($width > 600 || $height > 600)
    {
        $resize_image = image_resize($img,550,550);

        if(!is_object($resize_image))
        {
            list($width,$height) = getimagesize($resize_image);

            $x_in_inch = ((816 - $width) / 2 + $right)* 0.010 ;

            $y_in_inch = ((816 - $height) / 2)* 0.010;

            if($y_in_inch < 1.8)
            {
                $y_in_inch = 0.8;
            }
            $data['y_in_inch'] = $y_in_inch;
            $data['img_height'] = $height * 0.01;;
        }

    }
    else
    {
        $x_in_inch = ((816 - $width) / 2+ $right)* 0.010;

        $y_in_inch = ((816 - $height) / 2)* 0.010;

        if($y_in_inch < 1.8)
        {
            $y_in_inch = 0.5;
        }

        $data['y_in_inch'] = $y_in_inch;
    }

    return $data;
}
function margin_right($img, $page_width = 816)
{
    $img = is_file_exist($img,true);

    $height = getimagesize($img)[1];

    if($height > 600)
    {
        $resize_image = image_resize($img,550,550);

        if(!is_object($resize_image))
        {
            $height = getimagesize($resize_image)[1];

            $y_in_inch = (($page_width - $height) / 2)* 0.010;

            if($y_in_inch < 1.8)
            {
                $y_in_inch = 0.4;
            }

            $margin_from = $height * 0.0105 + $y_in_inch;

        }
    }
    else
    {
        $y_in_inch = (($page_width - $height) / 2) * 0.010;

        if($y_in_inch < 1.8)
        {
            $y_in_inch = 0.5;
        }

        $margin_from = $height * 0.010 + $y_in_inch;
    }
    if($y_in_inch == 0.5)
    {
        $margin_from = $margin_from * 0.2 + $margin_from;
    }
    else
    {
        $margin_from = $margin_from * 0.05 + $margin_from/* + 0.58*/;

    }
    return $margin_from;
}
function centerImage($img, FPDF $pdf, $page_width = 816, $page_height = 816, $url = false): void
{
    if($url)
    {
        $img = is_file_exist($img,true,true);
    }
    else
    {
        $img = is_file_exist($img,true);
    }
    list($width,$height) = getimagesize($img);

    if($width > 600 || $height > 600)
    {
        $resize_image = image_resize($img,550,550);

        if(!is_object($resize_image))
        {
            list($width,$height) = getimagesize($resize_image);

            $x_in_inch = (($page_height - $width) / 2)* 0.010;

            $y_in_inch = (($page_width - $height) / 2)* 0.010;

            $pdf->Image($resize_image,$x_in_inch,$y_in_inch);
        }

    }
    else
    {
        $x_in_inch = (($page_height - $width) / 2)* 0.010;

        $y_in_inch = (($page_width - $height) / 2)* 0.010;

        $pdf->Image($img,$x_in_inch,$y_in_inch);
    }
}

function margin($img, $page_width = 816)
{
    $img = is_file_exist($img,true);

    list($height) = getimagesize($img);

    if($height > 600)
    {
        $resize_image = image_resize($img,600,600);

        if(!is_object($resize_image))
        {
            list($height) = getimagesize($resize_image);

            $y_in_inch = (($page_width - $height) / 2);

            $margin_from = ($height + $y_in_inch) * 0.010;
        }
    }
    else
    {
        $y_in_inch = (($page_width - $height) / 2);

        $margin_from = ($height + $y_in_inch) * 0.010;
    }
    return $margin_from;
}

$bookID = get_the_ID();

$terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));

if(isset($_GET['print']))
{
    if($_GET['print'] != 'cover')
    {
        if ($terms[0]->term_id == 7){
            get_template_part( 'templates/pdf_template/the-potty' );
        }
        if ($terms[0]->term_id == 4){
            get_template_part( 'templates/pdf_template/starting-school' );
        }
        if ($terms[0]->term_id == 3){
            get_template_part( 'templates/pdf_template/book-baby' );
        }
        if ($terms[0]->term_id == 11){
            get_template_part( 'templates/pdf_template/wedding_book' );
        }
        if ($terms[0]->term_id == 12){
            get_template_part( 'templates/pdf_template/pacifier_book' );
        }
    }
    else
    {
        if ($terms[0]->term_id == 7){
            get_template_part( 'templates/pdf_cover/the-potty' );
        }
        if ($terms[0]->term_id == 4){
            get_template_part( 'templates/pdf_cover/starting-school' );
        }
        if ($terms[0]->term_id == 3){
            get_template_part( 'templates/pdf_cover/book-baby' );
        }
        if ($terms[0]->term_id == 11){
            get_template_part( 'templates/pdf_cover/wedding_book' );
        }
        if ($terms[0]->term_id == 12){
            get_template_part( 'templates/pdf_cover/pacifier_book' );
        }
    }
}
else
{
get_header();
get_template_part( 'templates/preview' );
get_footer();
}
?>