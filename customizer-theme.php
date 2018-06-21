<?php
require "customizer-config.php";
require "walker-theme.php";
require "customizer-acf.php";
require "customizer-ajax.php";
require "classes/DateTimeFr.php";

// session function
function register_my_session(){
    if( !session_id() ) {
        session_start();
    }
}
add_action('init', 'register_my_session');


/**
 * 1° Import des scripts et css spécifiques
 */
function wp_enqueue_scripts_theme_spe() {
    $tpl = basename(get_page_template_slug());
    $p_type = get_post_type();

    //wp_enqueue_script( 'agence-gmap', 'https://maps.googleapis.com/maps/api/js?key='.GOOGLE_API_MAP_KEY.'&callback=initContactMap#async#defer' );

    // CSS communs
    wp_enqueue_img_liquid();
    wp_enqueue_carousel();

    //JS communs
    if (is_front_page()) {
        wp_enqueue_script('home', get_template_directory_uri() . '/js/home.js');
    }
    if (is_page()) {
        wp_enqueue_lightbox();
        wp_enqueue_script('page', get_template_directory_uri() . '/js/page.js');
    }

    if(is_single() && get_post_type() != 'annonce'){
        wp_enqueue_lightbox();
        wp_enqueue_script( 'single-js', get_template_directory_uri() . '/js/single.js' );
    }

    //Appeler Ajax

    if (is_post_type_archive( 'manifestation' )) {
        wp_register_script( 'calendar', get_template_directory_uri() . '/js/calendar.js' );
        wp_localize_script( 'calendar', 'ajaxurlcalendar', admin_url( 'admin-ajax.php' ) );
        wp_enqueue_script( 'calendar' );

    }

    if (basename(get_page_template_slug()) == 'page-salle-communale.php') {
        wp_register_script( 'calendar-salle', get_template_directory_uri() . '/js/calendar-salle.js' );
        wp_localize_script( 'calendar-salle', 'ajaxurlcalendarsalle', admin_url( 'admin-ajax.php' ) );
        wp_enqueue_script( 'calendar-salle' );

    }

    if (is_post_type_archive( 'document' )) {
        wp_enqueue_script( 'document', get_template_directory_uri() . '/js/document.js' );
    }
    //main.js
    wp_enqueue_script( 'main-infocob-js', get_template_directory_uri() . '/js/main.js' );


}
add_action('wp_enqueue_scripts', 'wp_enqueue_scripts_theme_spe');

function custom_post_type_theme() {

    //BLOCS CONTENUS
    //Blocs de contenus
    $labels = array(
        'name' => 'Blocs contenus',
        'singular_name' => 'Bloc contenu',
        'menu_name' => 'Blocs contenus',
        'name_admin_bar' => 'Blocs contenus',
        'add_new' => 'Ajouter',
        'add_new_item' => 'Ajouter',
        'new_item' => 'Ajouter',
        'edit_item' => 'Modifier',
        'view_item' => 'Voir',
        'all_items' => 'Tous les blocs',
        'search_items' => 'Rechercher',
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Tous les blocs',
        'public' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'bloc-contenu'),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-align-left',
        'supports' => array('title')
    );

    register_post_type('bloc-contenu', $args);

    //MANIFESTATIONS
    //Catégories des manifestations
    $labels = array(
       'name'              => 'Catégories des manifestations',
       'singular_name'     => 'Catégorie de manifestations',
       'search_items'      => 'Rechercher',
       'all_items'         => 'Tous',
       'parent_item'       => 'Parent',
       'parent_item_colon' => 'Parent',
       'edit_item'         => 'Modifier',
       'update_item'       => 'Mettre à jour',
       'add_new_item'      => 'Ajouter',
       'new_item_name'     => 'Ajouter',
       'menu_name'         => 'Catégories de manifestation',
   );

    $args = array(
       'hierarchical'      => true,
       'labels'            => $labels,
       'public'           => true,
       'show_ui'           => true,
       'show_admin_column' => true,
       'query_var'         => true,
       'rewrite'           => array( 'slug' => 'thematique-evenement' ),
   );

    register_taxonomy( 'type-manifestation', array( 'manifestation' ), $args );

    //Posts manifestations
    $labels = array(
        'name' => 'Calendrier des événements',
        'singular_name' => 'Manifestation',
        'menu_name' => 'Manifestations',
        'name_admin_bar' => 'Manifestations',
        'add_new' => 'Ajouter',
        'add_new_item' => 'Ajouter',
        'new_item' => 'Ajouter',
        'edit_item' => 'Modifier',
        'view_item' => 'Voir',
        'all_items' => 'Toutes les manifestations',
        'search_items' => 'Rechercher',
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Toutes les manifestations',
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'evenement'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type('manifestation', $args);

    //DOCUMENTS
    //Catégories des documents
    $labels = array(
       'name'              => 'Catégories des documents',
       'singular_name'     => 'Catégorie de documents',
       'search_items'      => 'Rechercher',
       'all_items'         => 'Tous',
       'parent_item'       => 'Parent',
       'parent_item_colon' => 'Parent',
       'edit_item'         => 'Modifier',
       'update_item'       => 'Mettre à jour',
       'add_new_item'      => 'Ajouter',
       'new_item_name'     => 'Ajouter',
       'menu_name'         => 'Catégories de document',
   );

    $args = array(
       'hierarchical'      => true,
       'labels'            => $labels,
       'public'           => true,
       'show_ui'           => true,
       'show_admin_column' => true,
       'query_var'         => true,
       'rewrite'           => array( 'slug' => 'type-document' ),
   );

    register_taxonomy( 'type-document', array( 'document' ), $args );

    //Posts documents
    $labels = array(
        'name' => 'Documents',
        'singular_name' => 'Document',
        'menu_name' => 'Documents',
        'name_admin_bar' => 'Documents',
        'add_new' => 'Ajouter',
        'add_new_item' => 'Ajouter',
        'new_item' => 'Ajouter',
        'edit_item' => 'Modifier',
        'view_item' => 'Voir',
        'all_items' => 'Tous les documents',
        'search_items' => 'Rechercher',
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Tous les documents',
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'document'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-media-default',
        'supports' => array('title')
    );

    register_post_type('document', $args);

    //ASSOCIATIONS
    //Catégories des associations
    $labels = array(
       'name'              => "Catégorie des associations",
       'singular_name'     => "Catégorie d'associations",
       'search_items'      => 'Rechercher',
       'all_items'         => 'Tous',
       'parent_item'       => 'Parent',
       'parent_item_colon' => 'Parent',
       'edit_item'         => 'Modifier',
       'update_item'       => 'Mettre à jour',
       'add_new_item'      => 'Ajouter',
       'new_item_name'     => 'Ajouter',
       'menu_name'         => "Catégories d'associations",
   );

    $args = array(
       'hierarchical'      => true,
       'labels'            => $labels,
       'public'           => true,
       'show_ui'           => true,
       'show_admin_column' => true,
       'query_var'         => true,
       'rewrite'           => array( 'slug' => 'associations' ),
   );

    register_taxonomy( 'type-association', array( 'association' ), $args );

    //Pages associations
    $labels = array(
        'name' => 'Annuaire des associations',
        'singular_name' => 'Association',
        'menu_name' => 'Associations',
        'name_admin_bar' => 'Associations',
        'add_new' => 'Ajouter',
        'add_new_item' => 'Ajouter',
        'new_item' => 'Ajouter',
        'edit_item' => 'Modifier',
        'view_item' => 'Voir',
        'all_items' => 'Toutes les associations',
        'search_items' => 'Rechercher',
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Toutes les associations',
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'annuaire-association'),
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-groups',
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type('association', $args);

    //GALERIES PHOTOS
    //Pages ssociations
    $labels = array(
        'name' => 'Galeries photos',
        'singular_name' => 'Galerie photos',
        'menu_name' => 'Galeries photos',
        'name_admin_bar' => 'Galeries photos',
        'add_new' => 'Ajouter',
        'add_new_item' => 'Ajouter',
        'new_item' => 'Ajouter',
        'edit_item' => 'Modifier',
        'view_item' => 'Voir',
        'all_items' => 'Toutes les galeries',
        'search_items' => 'Rechercher',
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Toutes les galeries photos',
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'galerie-photos'),
        'capability_type' => 'page',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-images-alt2',
        'supports' => array('title', 'editor', 'thumbnail')
    );

    register_post_type('galerie-photos', $args);

    //DERNIERES MINUTES
    //Pages dernieres minutes
    $labels = array(
        'name' => 'Dernières minutes',
        'singular_name' => 'Alerte',
        'menu_name' => 'Dernières minutes',
        'name_admin_bar' => 'Dernières minutes',
        'add_new' => 'Ajouter',
        'add_new_item' => 'Ajouter',
        'new_item' => 'Ajouter',
        'edit_item' => 'Modifier',
        'view_item' => 'Voir',
        'all_items' => 'Toutes les alertes',
        'search_items' => 'Rechercher',
    );

    $args = array(
        'labels' => $labels,
        'description' => 'Toutes les alertes',
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'alerte'),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => array('title', 'excerpt')
    );

    register_post_type('alerte', $args);
}

add_action('init', 'custom_post_type_theme');


/****************** FIX bug pagination custom post_type */
function toolset_fix_custom_posts_per_page( $query_string ){
    if( is_admin() || ! is_array( $query_string ) )
        return $query_string;

    $post_types_to_fix = array(
        array(
            'post_type' => 'manifestation',
            'posts_per_page' => 2
        ),
    );
    foreach( $post_types_to_fix as $fix ) {
        if( (array_key_exists( 'post_type', $query_string ) && $query_string['post_type'] == $fix['post_type']) || array_key_exists( 'type-manifestation', $query_string )){
            $query_string['posts_per_page'] = $fix['posts_per_page'];
            return $query_string;
        }
    }

    return $query_string;
}

add_filter( 'request', 'toolset_fix_custom_posts_per_page' );

/****************** CUSTOM image sizes */
add_action( 'after_setup_theme', 'wpdocs_theme_setup' );
function wpdocs_theme_setup() {
    add_image_size( 'galerie-thumb', 600, 500, false  ); // 600 x 500 pixels sans crop
}

/**************** Sidebar FOOTER */

add_action( 'widgets_init', 'theme_slug_widgets_init' );

function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Pied de page', 'theme-slug' ),
        'id' => 'footer-content',
        'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'theme-slug' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h2 class="widgettitle">',
	'after_title'   => '</h2>',
    ) );
}


function has_children($post_id = null) {
    global $post;

    $post_id = $post_id ?: $post->ID;

    $children = get_pages( array( 'child_of' => $post_id ) );
    if( count( $children ) == 0 ) {
        return false;
    } else {
        return true;
    }
}

/* BOUTONS CSS STYLE ADMIN */

/**
 * Add "Styles" drop-down
 */
add_filter( 'mce_buttons_2', 'tuts_mce_editor_buttons' );

function tuts_mce_editor_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
/**
 * Add styles/classes to the "Styles" drop-down
 */
add_filter( 'tiny_mce_before_init', 'tuts_mce_before_init' );

function tuts_mce_before_init( $settings ) {
    $style_formats = array(
        array(
            'title' => 'Bouton classique',
            'selector' => 'a',
            'classes' => 'btn'
            ),
        array(
            'title' => 'Bouton rouge',
            'selector' => 'a',
            'classes' => 'btn primary'
            ),
        array(
            'title' => 'Bouton contour',
            'selector' => 'a',
            'classes' => 'btn contour'
            ),
    );
    $settings['style_formats'] = json_encode( $style_formats );
    return $settings;
}

//*************************************************************//
//******************* DESACTIVER COMMENTAIRES WP
//*************************************************************//
// Dans le fichier functions.php de votre thème, collez les lignes suivantes :

add_filter('comments_open', 'wpc_comments_closed', 10, 2);

function wpc_comments_closed( $open, $post_id ) {
    $post = get_post( $post_id );
    // if ('post' == $post->post_type)
    $open = false;
    return $open;
}
