<?php
$title = "Événement";
$imageHeader = get_theme_mod("theme_img_events");

$now = date('Y-m-d H:i:s');
$format_in = 'Y-m-d H:i:s';
$format_out = 'l j F Y  à H:i:s';

if(get_the_post_thumbnail_url(null, "full")){
    $imageHeader = get_the_post_thumbnail_url(null, "full");
}

$categories = get_the_terms(get_the_ID(), 'type-manifestation');
$nbCategories = count($categories);
if( $categories ){
    $categorie = "";
    foreach( $categories as $i => $catAss ){
        $categorie .= "<p class='tag-primary fat'>". $catAss->name. "</p>";
    }
}else{
    $categorie = "";
}
$visuel    = get_field('visuel');
$datedebut = get_field("date_debut");
$datefin   = get_field("date_fin");
$dates     = get_field('dates');
$lieux     = get_field('lieux');
$infos     = get_field('infos');

$galerie_liee = get_field("galerie_photo");
$document_lie = get_field("document_lie");

$puriel="";
if((count($document_lie) > 1)){
    $puriel = "s";
}
$datesPuriel="";
if((count($dates) > 1)){
    $datesPuriel = "s";
}

if(!empty($datefin)){
    $datedebut = DateTimeFr::createFromFormat('Y-m-d', $datedebut);
    $datedebut_format = $datedebut->format('l j F');

    $datefin = DateTimeFr::createFromFormat('Y-m-d', $datefin);
    $datefin_format = $datefin->format('l j F Y');

    $the_date = "Du ".$datedebut_format." au ".$datefin_format;
}else {
    $datedebut = DateTimeFr::createFromFormat('Y-m-d', $datedebut);
    $the_date = $datedebut->format('l j F Y');
}
?>
<header class="bandeau-titre-page">
    <div class="wrapper-large bordered-bottom">
        <div class="colorFilter imageHeader imgLiquidFill">
            <img src="<?php echo $imageHeader ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" />
        </div>
        <div class="container">
            <div class="row">
                <div class="titre-page col-12 col-md-8 push-md-2">
                    <?php if(!empty($title)){ ?>
                    <h1 class="entry-title"><span class="entry-subtitle"><?php echo $title; ?></span><?php the_title(); ?></h1>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="wrapper-large colored">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- fil d'ariane -->
                <?php if ( function_exists('yoast_breadcrumb') ) { ?>
                    <?php yoast_breadcrumb('<p id="breadcrumbs">','</p>'); ?>
                <?php } ?>
                <!-- /fil d'ariane -->
            </div>
        </div>
    </div>
</section>

<section class="">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 col-xl-9 single-post">
                <?php if($categorie) { ?>
                    <div class="meta-actu">
                        <?php echo $categorie; ?>
                    </div>
                <?php } ?>

                <div class="resume-event">
                    <div class="row">
                        <div class="col-12<?php if(!empty($visuel)){ ?> col-md-6 col-lg-6<?php } ?>">
                            <p class="titre"><?php the_title(); ?></p>
                        <?php if(!empty($dates)) { ?>
                            <p class="sous-titre">Date<?php echo $datesPuriel; ?></p>
                            <ul class="liste-dates">
                                <?php foreach ($dates as $date) { ?>
                                    <?php if(!empty($date["date"])){ ?>
                                    <?php $date_display = DateTimeFr::createFromFormat('Y-m-d H:i:s', $date["date"]); ?>
                                    <li>
                                        <i class="far fa-clock fa-fw"></i>
                                        <?php if($now > $date["date"] ) { ?>
                                            <s>
                                        <?php } ?>

                                        <?php echo $date_display->format('l j F Y à H:i:s'); ?>

                                        <?php if($now > $date["date"] ) { ?>
                                            </s>
                                        <?php } ?>
                                    </li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        <?php }else{ ?>
                            <p class="sous-titre">Date</p>
                            <ul class="liste-dates">
                                <li><i class="far fa-clock fa-fw"></i> <?php echo $the_date ; ?></li>
                            </ul>
                        <?php } ?>

                        <?php if(!empty($lieux)) { ?>
                            <p class="sous-titre">Lieux</p>
                            <p class="detail-event">
                                <i class="fas fa-map-marker-alt fa-fw"></i>
                                <?php echo $lieux; ?>
                            </p>
                        <?php } ?>

                        <?php if(!empty($infos)) { ?>
                            <p class="sous-titre">Infos pratiques</p>
                            <p class="detail-event">
                                <i class="fas fa-info-circle fa-fw"></i>
                                <?php echo $infos; ?>
                            </p>
                        <?php } ?>

                        </div>
                        <?php if(!empty($visuel)){ ?>
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="img-single-event imgLiquidFill">
                                <img src="<?php echo $visuel['url']; ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>"/>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="contenu">
                    <?php the_content(); ?>
                </div>

                <?php if(!empty($document_lie)){ ?>
                <!-- Document(s) lié(s) -->
                <div class="documents-lie">
                    <p class="block-title sm">Fichier<?php echo $puriel; ?> à télécharger</p>
                    <ul class="liste-documents">
                    <?php foreach($document_lie as $document) {
                        $post = $document['doc_lie'];
                        if( $post ){ setup_postdata($post); ?>
                            <li>
                                <?php get_template_part( 'entry', 'document-lie' ); ?>
                            </li>
                        <?php wp_reset_postdata(); }
                    } ?>
                    </ul>
                </div>
                <!-- /Document(s) lié(s) -->
                <?php } ?>

                <?php if(!empty($galerie_liee)){ ?>
                <!-- Galerie photos liée -->
                <div class="galerie-lie">
                    <p class="block-title sm">Galerie photos</p>
                    <ul class="liste-photos">
                    <?php $post = $galerie_liee;
                        if( $post ){ setup_postdata($post); ?>
                            <?php get_template_part( 'entry', 'galerie-liee' ); ?>
                        <?php wp_reset_postdata(); } ?>
                    </ul>
                </div>
                <!-- /Galerie photos liée -->
                <?php } ?>

                <?php get_template_part( 'share-buttons' ); ?>
            </div>
            <div class="col-12 col-lg-4 col-xl-3">
                <div class="sidebar sidebar-actu">
                    <p class="sidebar-title">Thématiques</p>
                    <ul class="liste-categories">
                        <li><a href="<?php echo get_post_type_archive_link('manifestation');?>">Toutes</a></li>
                        <?php wp_list_categories( array(
                            'taxonomy' => 'type-manifestation',
                            'orderby' => 'name',
                            'title_li' => ''
                        )); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
