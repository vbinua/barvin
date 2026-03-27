<?php
$pdf = new FPDF('P','in',[8.5,8.5]);

$bookID   = get_the_ID();
$bookMeta = get_post_meta( $bookID, 'answers' );
$answers  = $bookMeta[0];
$pages    = 22;
$terms = wp_get_object_terms($bookID, 'templates', array('fields' => 'all'));
$currentTerm = $terms[0];
$imgBG = esc_url( get_field( 'book_background_image', $currentTerm )['url'] );
$imgBGArray = explode('?',$imgBG);
if(array_key_exists(0,$imgBGArray))
{
    $imgBG = $imgBGArray[0];
}

$kid1    = $answers['answer_0']['value'];
$pronoun = $answers['answer_1']['value'];
$sub     = '';
$pos     = '';
$obj     = '';

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

$adult1              = $answers['answer_2']['value'];
$adult2              = $answers['answer_3']['value'];
$photoKid1Baby       = $answers['answer_4']['value'];
$photoKid1Big        = $answers['answer_5']['value'];
$photoKid1GrownUp    = $answers['answer_6']['value'];
$photoKid1PottySteps = $answers['answer_7']['value'];
$photoKid1AfterPotty = $answers['answer_8']['value'];
$photoBathroomHome   = $answers['answer_9']['value'];
$condition1          = $answers['answer_10']['value'];
$teachersNames       = $answers['answer_11']['value'];
$photoBathroomSchool = $answers['answer_12']['value'];
$condition2          = $answers['answer_13']['value'];
$photoTravelPotty    = $answers['answer_14']['value'];
$photoKid1PottyTimes = $answers['answer_15']['value'];
$photoNewClothes     = $answers['answer_16']['value'];
$photoKid1Smiles     = $answers['answer_17']['value'];
$photoCover          = $answers['answer_18']['value'];

$pdf->AddFont('ArialRoundedMT','B','arialroundedmtbold.php');
$pdf->AddPage();
$pdf->SetMargins(0.5,3,0.5);
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
if($adult2)
{
    $text = 'When '.$kid1.' was a baby and before '.$sub.' learned to speak, '. $sub." couldn't tell ".$adult1.' and ' . $adult2. ' when '. $sub.' had to go pee and poop. '.$kid1.' would wear a diaper to catch pee and poop and keep'.$pos.' clothes clean.';
}
else
{
    $text = 'When '.$kid1.' was a baby and before '.$sub.' learned to speak, '. $sub." couldn't tell ".$adult1. ' when '. $sub.' had to go pee and poop. '.$kid1.' would wear a diaper to catch pee and poop and keep'.$pos.' clothes clean.';
}
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->SetMargins(0.5,3.8,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKid1Baby ,$pdf);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$verb = 'knows';
if ($pronoun == 'they/them/their') {
    $verb = 'know';
}
$text = 'Now '.$kid1.' is a big kid. '.ucfirst( $sub ) . ' ' . $verb.' how ask for things '.$pos.' body needs, like water, food, hugs, and the potty.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKid1Big ,$pdf);
$pdf->SetMargins(0.5,2.8,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'When '.$kid1.' feels a pee or poop ready to come out, '. $sub.' will tell a grown up who can help '.$obj." go to the potty. It's good to take a break from playing, eating, or story time to go to the potty!";
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKid1GrownUp ,$pdf);
$pdf->SetMargins(0.5,3.2,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'When '.$kid1.' gets to the bathroom, '.$sub.' will pull down '.$pos.' underwear, go pee or poop in the potty, flush the pee or poop down the toilet, dress, and wash hands.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKid1PottySteps ,$pdf);
$pdf->SetMargins(0.5,3.5,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'After washing hands, '.$kid1.' can go back to playing or eating or story time. '.ucfirst( $pos ).' toys, food, or books will be waiting where '.$sub.' left them.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage($photoKid1AfterPotty,$pdf);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'At home, '.$adult1.' and '.$adult2.' will help '.$kid1.' use the potty in this bathroom.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage($photoBathroomHome ,$pdf);
if ( $condition1 == 'Yes' )
{
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    $text = 'At school, '.$teachersNames.' will help '.$kid1.' use the potty in this bathroom.';
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0,0.5,$text,0,'L');
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    centerImage($photoBathroomSchool,$pdf);
}
if ( $condition2 == "Yes" )
{
    $pdf->SetMargins(0.5,2.8,0.5);
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    $text = "In other places, like the park, the grocery store, or a friend's house, there will be other bathrooms that ".$kid1.' can use. '.$adult1. ' and ' . $adult2 . 'will also carry a travel potty for '.$kid1. "to use in places that don't have a bathroom.";
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0,0.5,$text,0,'L');


    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    if ( $photoTravelPotty )
    {
        centerImage( $photoBathroomSchool ,$pdf);
    }
    else
    {
        centerImage(ABSPATH.'/wp-content/uploads/2022/05/Potty_STOCK_RESTROOM_SIGN-1-scaled.jpg',$pdf,816,816,true);
    }

}
else
{
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    $text = "In other places, like the park, the grocery store, or a friend's house, there will be other bathrooms that ".$kid1." can use. Many bathrooms have a sign on their door like this one.";
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0,0.5,$text,0,'L');
    $pdf->AddPage();
    $pdf->Image($imgBG,0,0,8.5,8.5);
    centerImage( 838 ,$pdf);
}
$verb = "doesn't";
if ($pronoun == 'they/them/their'){
    $verb = "don't";
}
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'There will be times when grown ups tell '.$kid1.' to sit on the potty even if '. $sub . ' ' . $verb." feel a pee or poop coming. It's good to try peeing or pooping in the potty before going to sleep, after waking up, before going outside, and after coming inside.";
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKid1PottyTimes ,$pdf);
$pdf->SetMargins(0.5,3.2,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'If '.$kid1." doesn't get to the potty in time and pees or poops in ".$pos.' underwear, '.$sub.' will ask a grown up to help '.$obj.' clean up and change into clean, dry clothes.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoNewClothes ,$pdf);
$pdf->SetMargins(0.5,3.5,0.5);
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
$text = 'But most of the time, '.$kid1.' will pee and poop in the potty. It feels good to use the potty and have clean, dry underwear!';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0,0.5,$text,0,'L');
$pdf->AddPage();
$pdf->Image($imgBG,0,0,8.5,8.5);
centerImage( $photoKid1Smiles ,$pdf);
$pdf->Output('i');
?>