<section class="quiz">
    <div class="quiz__header">
        <?php get_template_part('templates/partials/header'); ?>
    </div>
    <div class="pdf-wrap">
        <?php
        $book_pdf_url = get_post_meta(get_the_ID(), 'book_pdf', true);

        if ($book_pdf_url) {
            echo do_shortcode("[pdfjs-viewer url=$book_pdf_url  viewer_height=700 fullscreen=false download=false print=false]");
            ?>
            <!--            <div class="pdf-wrap__loader">-->
            <!--                <div class="lds-roller">-->
            <!--                    <div></div>-->
            <!--                    <div></div>-->
            <!--                    <div></div>-->
            <!--                    <div></div>-->
            <!--                    <div></div>-->
            <!--                    <div></div>-->
            <!--                    <div></div>-->
            <!--                    <div></div>-->
            <!--                </div>-->
            <!--            </div>-->
            <?php
        } else {
            ?>
            <h3 class="quiz__description">
                No photos have been added yet. Please go back to the creation step and upload your photos.
            </h3>
            <?php
        }
        ?>
    </div>
    <?php get_template_part('templates/partials/footer'); ?>
</section>
