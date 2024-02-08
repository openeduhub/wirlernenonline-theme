<div class="portal-wrapper-right">
    <div class="detail-view-popup">
        <script>
            window.__env = {
                EDU_SHARING_API_URL: '<?php echo WLO_REPO; ?>rest',
            };
        </script>
        <oeh-details-embedded></oeh-details-embedded>
        <script>
            document.getElementsByTagName('oeh-details-embedded')[0].addEventListener('closed', () => {
                //console.log('received closed event');
                jQuery(".portal-wrapper-right").hide('slow');
                jQuery(".detail-view-popup").hide('slow');
            });
        </script>
    </div>

    <div class="no-content-popup">
        <div class="close-no-content-popup">X</div>
        <h3>Mitmachen!</h3>
        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/mitmachen-popup.png">
        <p>
            Das Projekt funktioniert ähnlich wie Wikipedia: Alle können beitragen und alle profitieren davon!
            Mitmach-Aktivitäten sind zum Beispiel die Verbesserung der Suche, die Ergänzung von Links, die Bewertung
            von Materialien, das Melden von Fehlern oder auch tatkräftig die Fachredaktionen zu unterstützen.
        </p>
        <a class="content-button no-content-button" href="<?php echo $addContentUrl; ?>" target="_blank">
            Neuen Inhalt hinzufügen
        </a>
    </div>
</div>

<script>
    function showContentPopup(nodeID) {
        document.getElementsByTagName("oeh-details-embedded")[0].setAttribute("node-id", nodeID);
        jQuery(".no-content-popup").hide();
        jQuery(".detail-view-popup").css('display', 'flex');
        jQuery(".portal-wrapper-right").show('slow');
    }

    // jQuery(".fachportal-content-popup").click(function(e) {
    //     // Do something
    //     e.stopPropagation();
    // });

    function showNoContentPopup() {
        jQuery(".detail-view-popup").hide();
        jQuery(".no-content-popup").css('display', 'flex');;
        jQuery(".portal-wrapper-right").show('slow');
    }

    jQuery(".close-no-content-popup").click(function() {
        jQuery(".portal-wrapper-right").hide('slow');
        jQuery(".no-content-popup").hide('slow');
    });

    // BUGFIX: portal-wrapper-right is empty but shown on windows <= 1200px
    jQuery(".portal-wrapper-right").hide();
</script>