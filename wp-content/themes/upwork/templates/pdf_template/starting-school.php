<?php
$pdf = new FPDF('P','in',[8.5,8.5]);

$bookID      = get_the_ID();
$bookMeta    = get_post_meta( $bookID, 'answers' );
$answers     = $bookMeta[0];
$pages       = 18;
$terms       = wp_get_object_terms( $bookID, 'templates', array( 'fields' => 'all' ) );
$currentTerm = $terms[0];
$imgBG       = esc_url( get_field( 'book_background_image', $currentTerm )['url'] );
$imgBGArray = explode('?',$imgBG);
if(array_key_exists(0,$imgBGArray))
{
    $imgBG = $imgBGArray[0];
}
$kid         = $answers['answer_0']['value'];
$pronoun     = $answers['answer_1']['value'];
$sub         = '';
$pos         = '';
$obj         = '';

switch ( $pronoun ) {
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

$adult1                    = $answers['answer_2']['value'];
$adult2                    = $answers['answer_3']['value'];
$condition1                = $answers['answer_4']['value'];
$adultSitter               = $answers['answer_5']['value'];
$photoHomeAct1             = $answers['answer_6']['value'];
$schoolOld                 = $answers['answer_7']['value'];
$photoActSchool1           = $answers['answer_8']['value'];
$schoolNew                 = $answers['answer_9']['value'];
$photoSchoolNew            = $answers['answer_10']['value'];
$teacherName               = $answers['answer_11']['value'];
$photoTeacher              = $answers['answer_12']['value'];
$photoActivitySchool2      = $answers['answer_13']['value'];
$photoSchoolAccessories    = $answers['answer_14']['value'];
$adultDropOff              = $answers['answer_15']['value'];
//$condition2                = $answers['answer_16']['value'];
$adultPickUp               = $answers['answer_16']['value'];
$transport                 = $answers['answer_17']['value'];
$photoTransport            = $answers['answer_18']['value'];
$afterSchoolActivity       = $answers['answer_19']['value'];
$photoAfterSchoolActivity1 = $answers['answer_20']['value'];
$photoCover                = $answers['answer_21']['value'];

$pdf->AddFont('ArialRoundedMT','B','arialroundedmtbold.php');
$pdf->AddPage();
$pdf->SetMargins(0.5,3.8,0.5);
$pdf->SetFont('ArialRoundedMT','B',27);
if ( $condition1 != 'At home' ) {
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    $text = 'During the week, ' . $kid . ' goes to ' . $schoolOld . '. ' . $kid . ' has so much fun learning and playing with ' . $pos . ' friends and teachers from ' . $schoolOld . '.';
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0, 0.5, $text, 0, 'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    centerImage($photoActSchool1, $pdf);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
}else{
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    $text = 'During the week, '.$kid.' spends time with '.$adultSitter.'.'. $kid.' has so much fun learning and playing with '.$adultSitter.'.';
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0,0.5,$text,0,'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    centerImage( $photoHomeAct1,$pdf);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
}
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'Soon, '.$kid.' will start at a new school called '.$schoolNew.'.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoSchoolNew ,$pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = $kid.' will have new teachers, like '.$teacherName.',and '.$sub.' will make new friends.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoTeacher ,$pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = $kid.' will learn many new things, make art, read stories, sing songs, and play.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoActivitySchool2 ,$pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = $kid.' will eat lunch at school with '.$pos.' new friends. '.ucfirst( $sub ).' will take naps at school and go to the bathroom at school.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoSchoolAccessories ,$pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'On school days, '.$kid.' will '.$transport.' to school with '.$adultDropOff.'. At the end of the day, '.$adultPickUp.' will pick '.$obj.' up.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoTransport ,$pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'After school, '.$kid.' will '.$afterSchoolActivity;
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoAfterSchoolActivity1 ,$pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->SetMargins(0.5,2.5,0.5);
if ( $condition1 !== 'At home' )
{
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    $text = 'Going to a new school is a big change. Sometimes '.$kid.' will want '.$adult1;
    if($adult2)
    {
        $text = $text.' and '.$adult2;
    }
    $text = $text.' during the school day. '.ucfirst($sub).' may miss '.$pos.' friends and teachers from '.$schoolOld.'. '.$kid."'s teachers and friends from ".$schoolNew.' will help '.$obj.' feel better and have fun!';
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0,0.5,$text,0,'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    centerImage(get_template_directory() . '/img/Starting-School_STOCK_IMAGE_KIDS-1.jpg', $pdf,816,816,true);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
}
else
{
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'Going to a new school is a big change. Sometimes '.$kid.' will want '.$adult1;
if($adult2)
    {
    $text = $text.' and '.$adult2;
    }
$text = $text .' during the school day. Sometimes '.$kid.' will miss playing with '.$adultSitter.' and napping in '.$pos.' own bed. '.$kid."'s teachers and friends from ".$schoolNew.' will help '.$obj.' feel better and have fun!';
str_replace('â€™',"'",$text);
    $pdf->MultiCell(0,0.5,$text,0,'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    centerImage(get_template_directory() . '/img/Starting-School_STOCK_IMAGE_KIDS-1.jpg', $pdf,816,816,true);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
}
$pdf->Output('i');
?>