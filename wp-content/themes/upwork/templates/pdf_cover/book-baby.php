<?php
$pdf = new FPDF('L','in',[17.2,8.7]);
$bookID   = get_the_ID();
$bookMeta = get_post_meta( $bookID, 'answers' );
$answers  = $bookMeta[0];
$pages    = 24;
$terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));
$currentTerm = $terms[0];
$imgBG = esc_url( get_field( 'cover_background_image', $currentTerm )['url'] );
$imgBGArray = explode('?',$imgBG);
if(array_key_exists(0,$imgBGArray))
{
	$imgBG = $imgBGArray[0];
}

$kid     = $answers['answer_0']['value'];
$photoCover      = /*get_attached_file(*/$answers['answer_20']['value']/*)*/;

$pdf->AddFont('ArialRoundedMT','B','arialroundedmtbold.php');
$pdf->SetFont('ArialRoundedMT','B',42);
$margin = margin_right( $photoCover );
$data_margin = get_height_margin($photoCover);
$text = $kid . ' and the New Baby';
$y_inch = $data_margin['y_in_inch'];
$img_height = $data_margin['img_height'];
$widthTextInInch = 0.58;
$margin = $y_inch + $img_height + $widthTextInInch;
if($margin > 6.8 && strlen($text) > 36)
{
    $pdf->SetFont('ArialRoundedMT','B',34);
}
$pdf->SetMargins(9.5,$margin,0.8);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,17,8.5);
centerImage( get_template_directory() . '/img/logobook.png' ,$pdf,816,816,true);
right_image($photoCover,$pdf);
$pdf->MultiCell(0,0.5,$text,0,'C');
$pdf->SetFont('ArialRoundedMT','B',24);
$pdf->SetY(7.2);
$pdf->SetX(2.2);
$pdf->SetTextColor(64, 140, 92);
$pdf->MultiCell(0, 0.5, 'upandcomingbooks.com', 0, 'L');
//$margin = margin($photoCover);
/*$pdf->SetMargins(0,$margin,0);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage($photoCover,$pdf);
$pdf->MultiCell(0,0.5,$kid . ' and the New Baby',0,'C');
$pdf->SetMargins(0.5,3.8,0.5);*/
$pdf->Output('i');
?>