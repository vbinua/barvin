<?php 
$termId = $_GET['term'];
if(!$termId ){
    header('Location: /');
}
?>

<?php get_header(); ?>

<?php
    $term = get_term($termId);
    $questions = get_field('questions', $term);
//    var_dump($questions);

    foreach ($questions as $question):
?>

    <?php var_dump($question); ?>
    <?php if($question['acf_fc_layout'] == 'text'): ?>
        <?php echo $question['question']?>
    <?php endif; ?>
    <br>
    <?php endforeach; ?>


<?php get_footer(); ?>