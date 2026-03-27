<?php
$pdf = new FPDF('P', 'in', [8.5, 8.5]);
$bookID = get_the_ID();
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
switch ( $pronoun ) {
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

$couple_1 				= $answers['answer_2']['value'];
$photo_couple 			= $answers['answer_3']['value'];
$wedding_role 			= $answers['answer_4']['value'];
$ring_taker 			= $answers['answer_5']['value'];
$photoWeddingNew        = $answers['answer_6']['value'];
$isFirstWedding 		= $answers['answer_7']['value'];
$couple_2 				= $answers['answer_8']['value'];
$photoKidCouple 		= $answers['answer_9']['value'];
$photoWedding 			= $answers['answer_10']['value'];
$weddingAttire 			= $answers['answer_11']['value'];
$photoWeddingAttire 	= $answers['answer_12']['value'];
$photoVenue 			= $answers['answer_13']['value'];
$rehersalDinner 		= $answers['answer_14']['value'];
$photoRehersal 			= $answers['answer_15']['value'];
$photoCover 			= $answers['answer_16']['value'];

$pdf->AddFont('ArialRoundedMT', 'B', 'arialroundedmtbold.php');
$pdf->AddPage();
$pdf->SetMargins(0.5, 3.8, 0.5);
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
$pdf->SetFont('ArialRoundedMT', 'B', 27);
$text = $couple_1 . ' are getting married!';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0, 0.5, $text, 0, 'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
centerImage($photo_couple, $pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->SetMargins(0.5, 3.2, 0.5);
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
str_replace('â€™',"'",$text);
$pdf->MultiCell(0, 0.5, $text, 0, 'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
centerImage($photoWeddingNew, $pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
if ($isFirstWedding == 'No') {
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    $text = $kid . ' has been to a wedding before. ' . ucfirst($sub) . ' went to ' . $couple_2 . "'s wedding when " . $sub . ' was younger.';
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0, 0.5, $text, 0, 'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    centerImage($photoKidCouple, $pdf);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
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
}str_replace('â€™',"'",$text);
$pdf->MultiCell(0, 0.5, $text, 0, 'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
centerImage($photoWeddingAttire, $pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
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
str_replace('â€™',"'",$text);
$pdf->MultiCell(0, 0.5, $text, 0, 'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
centerImage($photoVenue, $pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
if ($rehersalDinner == 'Yes') {
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    $text = 'After the rehearsal, there will be a yummy meal with ' . $couple_1 . "'s family and friends. Then everyone will head to bed to get a good night's sleep, so they have lots of energy for the wedding the next day.";
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0, 0.5, $text, 0, 'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    centerImage($photoRehersal, $pdf);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
}
if ($isFirstWedding == 'No') {
    $pdf->SetMargins(0.5, 3, 0.5);
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    $text = 'On the day of the wedding, ' . $kid . ' will get dressed in ' . $pos . ' ' . $weddingAttire . '. ' . $kid . ' will pose for photos with ' . $couple_1 . '. The photographer will ask ' . $kid . ' to stand still and smile for the camera. All of the photographs will help ' . $couple_1 . ' remember their special day.';
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0, 0.5, $text, 0, 'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    centerImage($photoWedding, $pdf);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
}
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
$text = "When it's time for the wedding to start, " . $kid . ', ' . $couple_1 . ' and other people in the ceremony will line up and wait for their turn to walk down the aisle.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0, 0.5, $text, 0, 'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
centerImage($photoVenue, $pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
if ($wedding_role == 'Ring bearer') {
    $pdf->SetMargins(0.5, 2.2, 0.5);
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    $text = "When it's " . $kid . "'s turn, " . $sub . ' will walk down the aisle holding the ring pillow. ' . $couple_1 . "'s friends and family will be watching and smiling at " . $kid . '. ' . $kid . ' can smile back. At the end of the aisle, ' . $kid . ' will give the ring pillow to ' . $ring_taker . '. Then ' . $kid . ' will find ' . $pos . ' seat and sit quietly for the rest of the wedding ceremony.';
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0, 0.5, $text, 0, 'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    centerImage($photoWeddingNew, $pdf);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
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
    str_replace('â€™',"'",$text);
    $pdf->MultiCell(0, 0.5, $text, 0, 'L');
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
    $pdf->SetMargins(0.5, 3, 0.5);
    $pdf->AddPage();
    $pdf->Image($imgBG, 0, 0, 8.5, 8.5);
    centerImage($photoWeddingNew, $pdf);
    $pdf->SetFont('ArialRoundedMT','B',22);
    $pdf->SetY(7.2);
    $pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
    $pdf->SetFont('ArialRoundedMT','B',27);
}
$pdf->SetMargins(0.5,2.8, 0.5);
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
$text = 'The ceremony is over when ' . $couple_1 . ' kiss and walk back up the aisle together. After the ceremony, all the wedding guests will go to the reception, a fun party with food, music, and dancing to celebrate ' . $couple_1 . '.';
str_replace('â€™',"'",$text);
$pdf->MultiCell(0, 0.5, $text, 0, 'L');
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->SetFont('ArialRoundedMT','B',27);
$pdf->AddPage();
$pdf->Image($imgBG, 0, 0, 8.5, 8.5);
centerImage($photo_couple, $pdf);
$pdf->SetFont('ArialRoundedMT','B',22);
$pdf->SetY(7.2);
$pdf->MultiCell(0, 0.5, $pdf->PageNo() - 1, 0, 'C');
$pdf->Output('i');
?>