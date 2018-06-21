jQuery(document).ready(function(){

    var now = new Date();
    var mois    = ("0" + (now.getMonth() + 1)).slice(-2);
    var annee = now.getFullYear();

    ajax_get_calendar(jQuery("#mois").data("datemois"), jQuery("#annee").data("dateannee"));

    function ajax_get_calendar(mois_now, annee_now){
        if ( ( mois_now >= mois && annee_now == annee ) || ( annee_now > annee ) ){

            jQuery(".loader-overlay").addClass('visible');

            jQuery.ajax( {

                method: "POST",
                url: ajaxurlcalendar,
                data: { mois: mois_now, annee: annee_now, action: "init_calendar" }

            }).done(function( html ) {
                jQuery("#jours_cal").html( html );
                jQuery("#mois_interactif").html( jQuery("#jours_cal .jours_cal").data("interactif") );
                jQuery(".loader-overlay").removeClass('visible');
            });
        }
    }
    jQuery("#prev").click(function(){
        ajax_get_calendar(jQuery("#jours_cal .jours_cal").data("moisprec"), jQuery("#jours_cal .jours_cal").data("anneeprec"));
    });

    jQuery("#next").click(function(){
        ajax_get_calendar(jQuery("#jours_cal .jours_cal").data("moissuivant"), jQuery("#jours_cal .jours_cal").data("anneesuivant"));
    });
});
