<?php
$pdf = new FPDF('P','in',[8.5,8.5]);
$bookID   = get_the_ID();
$bookMeta = get_post_meta( $bookID, 'answers' );
$answers  = $bookMeta[0];
$pages    = 24;
$terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));
$currentTerm = $terms[0];
$imgBG = esc_url( get_field( 'book_background_image', $currentTerm )['url'] );
$imgBGArray = explode('?',$imgBG);
if(array_key_exists(0,$imgBGArray))
{
	$imgBG = $imgBGArray[0];
}

$kid     = $answers['answer_0']['value'];
$pronoun = $answers['answer_1']['value'];
$sub     = '';
$pos     = '';
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

$kid     		 = $answers['answer_0']['value'];
$kid1SibType     = $answers['answer_2']['value'];
$adult1          = $answers['answer_3']['value'];
$adult2          = $answers['answer_4']['value'];
$photoKidSibType = $answers['answer_5']['value'];
$photoNewborn    = $answers['answer_6']['value'];
$photoKidHelp1   = $answers['answer_7']['value'];
$photoKidCry     = $answers['answer_8']['value'];
$photoKidHelp2   = $answers['answer_9']['value'];
$photoKidLove    = $answers['answer_10']['value'];
$photoKidSad     = $answers['answer_11']['value'];
$adult3_4        = $answers['answer_12']['value'];
$photoKidAdult3  = $answers['answer_13']['value'];
$condition1      = $answers['answer_14']['value'];
$adult1SibName   = $answers['answer_15']['value'];
$photoAdult1Si   = $answers['answer_16']['value'];
$condition2      = $answers['answer_17']['value'];
$adult2SibName   = $answers['answer_18']['value'];
$photoAdult2Sib  = $answers['answer_19']['value'];
$photoCover      = $answers['answer_20']['value'];

$pdf->AddFont('ArialRoundedMT','B','arialroundedmtbold.php');
$pdf->AddPage();
centerImage( get_template_directory() . '/img/logobook.png' ,$pdf,816,816,true);
$pdf->SetFont('ArialRoundedMT','B',42);
$margin = margin_right( $photoCover );
$pdf->SetMargins(0.5,$margin,0.8);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
right_image($photoCover,$pdf,816,816,true);
$pdf->MultiCell(0,0.5,$kid . ' and the New Baby',0,'C');
$pdf->SetMargins(0.5,3.8,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$pdf->SetFont('ArialRoundedMT','B',27);
$text = 'A new baby is coming, and '. $kid.' will be a '.$kid1SibType.'.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage($photoKidSibType,$pdf);
$pdf->SetMargins(0.5,3.8,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'When the baby is ready to be born, '.$adult1.' and ' . $adult2.' will go to the hospital. '.$kid.' will wait for the baby with '.$adult3_4.'.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage($photoKidAdult3,$pdf);
$pdf->SetMargins(0.5,2.8,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = $adult1.' and ' . $adult2.' will bring the baby home to live with '.$adult1.', ' . $adult2 .', and '.$kid.'. 

This is '.$kid.' when '.$sub.' was a newborn baby.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoNewborn ,$pdf);
$pdf->SetMargins(0.5,2,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = "Little babies need grown ups and big kids to do everything for them. Babies don't know how to eat food, so grown ups will feed the baby. Babies don't know how to walk, so grown ups will carry the baby. 

".$adult1.' and ' . $adult2 .' fed and carried '.$kid.' when '.$sub.' was a baby, too.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKidHelp1 ,$pdf);
$pdf->SetMargins(0.5,2.8,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = "Babies don't know how to talk, so they cry to let others know they need to eat, burp, sleep, or have their diaper changed.

".$kid." cried when ".$sub." was a baby, too.";
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKidCry ,$pdf);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'To show the baby love, '.$kid.' can touch the baby gently. '.ucfirst( $sub ).' can help the baby learn and grow by singing and talking to the baby. '.ucfirst( $sub )." can also help with the baby's bath time and by being quiet while the baby is sleeping. Babies sleep a lot!";
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKidHelp2 ,$pdf);
$pdf->SetMargins(0.5,3.8,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'The baby will get presents that '.$kid.' can help open.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( ABSPATH.'/wp-content/uploads/2022/09/New-Baby_STOCK_IMAGE_GIFTS-1-1-scaled-1.png',$pdf,816,816,true);
$pdf->SetMargins(0.5,3,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'Sometimes it can be hard having a new baby in the family. '.$adult1 .' and ' . $adult2 .' may be tired or busy helping the baby when '.$kid." wants to play or snuggle. It's OK to feel sad or mad when this happens.";
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKidSad ,$pdf);
$pdf->SetMargins(0.5,3,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'As the baby grows up, the baby will be able to smile, laugh, and play with '.$kid.'. Having a sibling can be lots of fun! 

'.$adult1.' and '.$adult1SibName.' are siblings.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoAdult1Si ,$pdf);
if ( $condition2 == 'Yes' && $adult2 )
{
	$pdf->SetMargins(0.5,4,0.5);
	$pdf->AddPage();
	$pdf->Image($imgBG,0,0,8.5,8.5);
	$text = $adult2 . ' and ' . $adult2SibName.' are siblings.';
	str_replace('â€™',"'",$text);
	$pdf->MultiCell(0,0.5,$text,0,'L');
	$pdf->AddPage();
	$pdf->Image($imgBG,0,0,8.5,8.5);
	centerImage( $photoAdult2Sib ,$pdf);
}
$pdf->SetMargins(0.5,2.8,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'Having a new baby in the family is a big change. ';
$text = $text. $kid.' will be a great '.$kid1SibType.'. ';
$text = $text. $kid.' is loved and '. $adult1.' and ' . $adult2 .' can give hugs to '.$kid.' and the baby at the same time. ';
$text = $text.'And '.$kid.' will still have '.$pos.' own special time with '.$adult1.' and ' . $adult2 .'.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKidLove ,$pdf);
$pdf->Output('i');
?>
