<?php /* Template Name: Privacy Policy */ ?>
<?php get_header(); ?>
<section class="maintitle">
<div class="maintitle__container _container">
    <h1 class="maintitle__title">
        <?php the_title(); ?>
    </h1>
</div>
</section>
<section class="contant">
<div class="contant__contsiner _container">
    <div class="contant__block"> 
    <?php the_content(); ?>
    </div>  
</div>
</section>
<?php get_footer(); ?>