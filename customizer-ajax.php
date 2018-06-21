<?php

//Initialisation Calendrier Salle Communale

add_action('wp_ajax_init_calendar_salle', 'init_calendar_salle');
add_action('wp_ajax_nopriv_init_calendar_salle', 'init_calendar_salle');

function init_calendar_salle() {

    create_calendar_salle($_POST['mois'], $_POST['annee'], $_POST['page_id']);

    die();
}

if (basename(get_page_template_slug()) == 'page-salle-communale.php') {
    create_calendar_salle($_POST['mois'], $_POST['annee'], $_POST['page_id']);
}

//Initialisation Calendrier Evénement

add_action('wp_ajax_init_calendar', 'init_calendar');
add_action('wp_ajax_nopriv_init_calendar', 'init_calendar');

function init_calendar() {

    create_calendar($_POST['mois'], $_POST['annee']);

    die();
}

if (is_post_type_archive('manifestation')) {
    create_calendar($_POST['mois'], $_POST['annee']);
}

// Calendar Evenement

function create_calendar($month = null, $year = null) {
    global $wpdb;

    $prefix_table = $wpdb->prefix;

    if (!$month) {
        $month = date("m");
    }

    if (!$year) {
        $year = date("Y");
    }

    $dateDebut = new DateTimeFr($year . "-" . $month . "-01");
    //définir la date en fonction des parametres

    $moisActuel = intval($dateDebut->format("n"));

    //Mois Précédent / Année Précédente

    $moisPrec = clone $dateDebut;
    $moisPrec->sub(new DateInterval("P1M"));

    //Mois Suivant / Annnée Suivante

    $moisSuivant = clone $dateDebut;
    $moisSuivant->add(new DateInterval("P1M"));


    echo '<ul class="jours_cal" data-moisprec="' . $moisPrec->format('m') . '" data-anneeprec="' . $moisPrec->format('Y') . '" data-moissuivant="' . $moisSuivant->format('m') . '" data-anneesuivant="' . $moisSuivant->format('Y') . '" data-interactif="' . $dateDebut->format('F') . " " . $dateDebut->format('Y') . '">';

    $compteur = 1;

    $donnees = $wpdb->get_results("SELECT P.ID, PM.meta_value as date_debut, PM_datefin.meta_value as date_fin
		FROM " . $prefix_table . "posts P
		LEFT JOIN " . $prefix_table . "postmeta PM ON PM.post_id = P.ID  AND PM.meta_key = 'date_debut'
		LEFT JOIN " . $prefix_table . "postmeta PM_datefin ON PM_datefin.post_id = P.ID  AND PM_datefin.meta_key = 'date_fin'
		WHERE P.post_status = 'publish'
		AND P.post_type = 'manifestation'
		AND SUBSTR(PM.meta_value, 5, 2) = '" . $dateDebut->format("m") . "' AND SUBSTR(PM.meta_value, 1, 4) = '" . $dateDebut->format("Y") . "'");


    foreach ($donnees as $key => $value) {

        $dateTimeDebut = DateTime::createFromFormat('Ymd', $value->date_debut);
        $dateTimeFin = DateTime::createFromFormat('Ymd', $value->date_fin);

        $dates_evenements[$value->date_debut] = $dateTimeDebut;


        if (!empty($value->date_fin)) {
            while ($dateTimeDebut <= $dateTimeFin) {
                //traitement
                $dates_evenements[$dateTimeDebut->format('Ymd')] = $dateTimeDebut;
                $dateTimeDebut->add(new DateInterval("P1D"));
            }
        }
    }


    while ($compteur != $dateDebut->format("N")) {
        echo "<li class='case_blank'></li>";
        $compteur = $compteur + 1;
    }

    while (intval($dateDebut->format("n")) === $moisActuel) {
        //Traitement du mois
        if (isset($dates_evenements[$dateDebut->format("Ymd")])) {
            echo "<li><a class='circle' href='" . get_manifestation_day_link($dateDebut->format("Y"), $dateDebut->format("m"), $dateDebut->format("d")) . "'><span>" . $dateDebut->format("d") . "</span></a></li>";
        } else {
            echo "<li>" . $dateDebut->format("d") . "</li>";
        }

        $dateDebut->add(new DateInterval("P1D"));
    }

    $dateDebut->sub(new DateInterval("P1D"));
    $lastJour = $dateDebut->format("N");

    while ($lastJour != 7) {
        echo "<li class='case_blank'></li>";
        $lastJour = $lastJour + 1;
    }
    echo "</ul>";
}

// Calendar Salle

function create_calendar_salle($month = null, $year = null, $page_id) {
    global $wpdb;

    $prefix_table = $wpdb->prefix;

    if (!$month) {
        $month = date("m");
    }

    if (!$year) {
        $year = date("Y");
    }

    $dateDebut = new DateTimeFr($year . "-" . $month . "-01");
    //définir la date en fonction des parametres

    $moisActuel = intval($dateDebut->format("n"));

    //Mois Précédent / Année Précédente

    $moisPrec = clone $dateDebut;
    $moisPrec->sub(new DateInterval("P1M"));

    //Mois Suivant / Annnée Suivante

    $moisSuivant = clone $dateDebut;
    $moisSuivant->add(new DateInterval("P1M"));


    echo '<ul class="jours_cal" data-moisprec="' . $moisPrec->format('m') . '" data-anneeprec="' . $moisPrec->format('Y') . '" data-moissuivant="' . $moisSuivant->format('m') . '" data-anneesuivant="' . $moisSuivant->format('Y') . '" data-interactif="' . $dateDebut->format('F') . " " . $dateDebut->format('Y') . '">';

    $compteur = 1;

    $donnees = $wpdb->get_results("SELECT PM.meta_value
                                    FROM " . $prefix_table . "postmeta PM, " . $prefix_table . "posts P
                                    WHERE PM.post_id = P.ID
                                    AND `meta_key` LIKE 'dates_occupation_%'
                                    AND P.ID = " . $page_id . "
                                    AND P.post_status = 'publish'
                                    AND SUBSTR(PM.meta_value, 5, 2) = '" . $dateDebut->format("m") . "' AND SUBSTR(PM.meta_value, 1, 4) = '" . $dateDebut->format("Y") . "'");


    foreach ($donnees as $key => $value) {

        $dateTime = DateTime::createFromFormat('Ymd', $value->meta_value);
        $dates_evenements[$value->meta_value] = $dateTime;
    }


    while ($compteur != $dateDebut->format("N")) {
        echo "<li class='case_blank'></li>";
        $compteur = $compteur + 1;
    }

    while (intval($dateDebut->format("n")) === $moisActuel) {
        //Traitement du mois
        if (isset($dates_evenements[$dateDebut->format("Ymd")])) {
            echo "<li><p class='circle' href='#'><span>" . $dateDebut->format("d") . "</span><span class='tooltip'>Réservé</span></p></li>";
        } else {
            echo "<li>" . $dateDebut->format("d") . "</li>";
        }

        $dateDebut->add(new DateInterval("P1D"));
    }

    $dateDebut->sub(new DateInterval("P1D"));
    $lastJour = $dateDebut->format("N");

    while ($lastJour != 7) {
        echo "<li class='case_blank'></li>";
        $lastJour = $lastJour + 1;
    }
    echo "</ul>";
}


