<?php /* Template Name: Faq page */ ?>
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
      <div class="contant__faq">
        <?php if(have_rows('questions')){ ?>
            <?php while(have_rows('questions')){ the_row(); ?>
                <div class="faq">
                    <div class="faq__title">
                        <div class="faq__arrow"><svg width="15" height="9" viewBox="0 0 15 9" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                            d="M15 1.21119L13.7819 -5.32467e-08L7.5 6.1687L1.21814 -6.02424e-07L-1.00662e-06 1.21119L7.50003 8.66878L15 1.21119Z"
                            fill="#383838" />
                        </svg></div> <?php the_sub_field('question'); ?> 
                    </div>
                    <div class="faq__description">
                        <?php the_sub_field('answer'); ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
      </div>
      <div class="contant__offer">
            <div class="c-offer">
                <div class="offer__block">
                    <?php $text = get_field('title'); ?>
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