<?php /* Template Name: About Us */ ?>
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
            <?php $text = get_field('title'); ?>
            <?php if($text){ ?>
                <div class="books__title">
                    <?php echo $text; ?>
                </div>
            <?php } ?>
            <?php $text = get_field('content'); ?>
            <?php if($text){
                echo $text;
             }?>
        </div>
        <div class="contant__offer">
            <div class="c-offer">
                <div class="offer__block">
                    <?php $text = get_field('title_build'); ?>
                    <?php if($text){ ?>
                        <div class="offer__mtitle">
                            <?php echo $text; ?>
                        </div>
                    <?php } ?>
                    <?php $text = get_field('buttontext'); ?>
                    <?php if($text){ ?>
                        <a href="<?php echo get_home_url(); ?>/#books" class="offer__button"> <?php echo $text; ?><img src="<?php echo get_template_directory_uri(); ?>/img/arrow-right-wite.svg" alt="arrow"></a>
                    <?php } ?>
                </div>
                <div class="offer__photo"> 
                    <?php $image = get_field('image'); ?>
                    <?php if($image){ ?>
                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="back-contant"></section>
<?php get_footer(); ?>