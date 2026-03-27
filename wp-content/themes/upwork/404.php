<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package upwork
 */

get_header();
?>
	<main id="primary" class="site-main">
		<section class="error-404 not-found">
			 <h1 class="error__title">
              <?php the_field('title-404','option'); ?>
			 </h1>
			 <div class="error__description">
			  <?php the_field('description-404','option'); ?>
			 </div>
		</section>  
	</main> 
<?php
get_footer();
