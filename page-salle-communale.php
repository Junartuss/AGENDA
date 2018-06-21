<?php
/**
 * Template Name: Page salle communale
 *
 */

get_header();

?>
<div id="content" role="main">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'entry', 'page-communale' ); ?>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
<?php  get_template_part( 'acces-rapide' ); ?>
<?php get_footer();
