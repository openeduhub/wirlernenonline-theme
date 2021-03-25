<?php

add_action('wp_head', 'wlo_ajaxurl');
function wlo_ajaxurl() {
    echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}


add_action( 'wp_ajax_wlo_submenu', 'wlo_submenu' );
add_action( 'wp_ajax_nopriv_wlo_submenu', 'wlo_submenu' );
function wlo_submenu() {
    global $wpdb; // this is how you get access to the database

    $nodeId =  $_POST['nodeID'];
    $educational_filter_json =  $_POST['educational_filter_values'];
    $educational_filter_values = json_decode(html_entity_decode($educational_filter_json), true);

    $disciplines = $educational_filter_values["disciplines"];
    $educationalContexts = $educational_filter_values["educationalContexts"];
    $intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];

    $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $nodeId . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true&fetchCounts=false&';
    $response = callWloRestApi($url);

    $submenu = '';

    if (!empty($response->collections)) {
        foreach ($response->collections as $collection) {
            if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated') {
                $prop = $collection->properties;
                $ccm_location = str_replace('dev.wirlernenonline.de', 'wirlernenonline.de', $collection->properties->{'cclom:location'}[0]);

                // Filter Disciplines
                if (!empty($prop->{'ccm:taxonid'})) {
                    if (wlo_edu_filter($prop->{'ccm:taxonid'}, $disciplines, "map_vocab_disciplines_value_only")) {
                        continue;
                    }
                }
                // Filter EducationalContext
                if (!empty($prop->{'ccm:educationalcontext'})) {
                    if (wlo_edu_filter($prop->{'ccm:educationalcontext'}, $educationalContexts, "map_vocab_educationalContexts_value_only")) {
                        continue;
                    }
                }
                // Filter IntendedEndUserRole
                if (!empty($prop->{'ccm:educationalintendedenduserrole'})) {
                    if (wlo_edu_filter($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, "map_vocab_intendedEndUserRoles_value_only")) {
                        continue;
                    }
                }

                $title = $collection->title;
                if (!empty($prop->{'ccm:collectionshorttitle'}[0])){
                    $title = $prop->{'ccm:collectionshorttitle'}[0];
                }
                $submenu .= '<a href="'.$ccm_location.'">';
                $submenu .= '<h6>'.$title.'</h6>';
                $submenu .= '</a>';

                }
            }

    }
    echo $submenu;
    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_collection_content_browser', 'collection_content_browser' );
add_action( 'wp_ajax_nopriv_collection_content_browser', 'collection_content_browser' );
function collection_content_browser() {
    global $wpdb; // this is how you get access to the database

    $postID =  $_POST['postID'];
    $collectionID =  $_POST['collectionID'];
    $educational_filter_json =  $_POST['educational_filter_values'];
    $layout =  $_POST['layout'];
    $maxResults =  $_POST['maxResults'];
    $slidesToShow =  $_POST['slidesToShow'];
    $slidesToScroll =  $_POST['slidesToScroll'];
    $horizontal_scroll =  $_POST['horizontal_scroll'];

    $educational_filter_values = json_decode(html_entity_decode($educational_filter_json), true);


    $collectionUrl = $educational_filter_values["collectionUrl"];
    $disciplines = $educational_filter_values["disciplines"];
    $educationalContexts = $educational_filter_values["educationalContexts"];
    $intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
    $oer = $educational_filter_values["oer"];
    $objectTypes = $educational_filter_values["objectTypes"];
    $learningResourceTypes = $educational_filter_values["learningResourceTypes"];
    $generalKeywords = $educational_filter_values["generalKeyword"];
    $oehWidgets = $educational_filter_values["oehWidgets"];

    //$addContentPageID = 9614; //dev
    $addContentPageID = 9933; //pre
    //$addContentPageID = 9081; //local

    /* ------------------------------------------------------------------- */

    $url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID . '/children/references';
    $response = callWloRestApi($url);

    $mediaTypes = array(
        "file-image" => "Bild",
        "file-word" => "Word-Dokument",
        "file-excel" => "Tabelle",
        "file-powerpoint" => "Präsentation",
        "file-odt" => "OpenOffice Dokument",
        "file-ods" => "OpenOffice Tabelle",
        "file-odp" => "OpenOffice Präsentation",
        "file-zip" => "Zip-Datei",
        "file-script" => "Skript",
        "file-xml" => "XML-Datei",
        "file-audio" => "Audio",
        "file-txt" => "Textdatei",
        "file-video" => "Video",
        "file-qti" => "ONYX-Datei",
        "file-h5p" => "H5P-Datei",
        "file-pdf" => "PDF-Datei",
        "link" => "Website",
        "folder" => "Ordner",
        "file" => "Unbekannt",
        "collection" => "Sammlung",
        "saved_search" => "Suche"
    );


    $contentArray = array();
    foreach ($response->references as $reference) {
        //var_dump($reference);
        $prop = $reference->properties;

        // check if deleted
        if($reference->originalId == null){
            continue;
        }

        if (!empty($prop->{'ccm:taxonid'})) {
            if (!checkPropertyMatch($prop->{'ccm:taxonid'}, $disciplines, true)) {
                continue;
            }
        }
        if (!empty($prop->{'ccm:educationalcontext'})) {
            if (!checkPropertyMatch($prop->{'ccm:educationalcontext'}, $educationalContexts, true)) {
                continue;
            }
        }
        if (!empty($prop->{'ccm:educationalintendedenduserrole'})) {
            if (!checkPropertyMatch($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, true)) {
                continue;
            }
        }

        // Filter ObjectType
        if (!empty($prop->{'ccm:objectType'})) {
            $propObjectType = $prop->{'ccm:objectType'};
            if ($propObjectType && !empty($propObjectType) && !empty($objectTypes) && !in_array($propObjectType, $objectTypes)) {
                continue;
            }
        }

        // Filter LearningResourceType
        if (!empty($prop->{'ccm:educationallearningresourcetype'})){
            if (wlo_edu_filter($prop->{'ccm:educationallearningresourcetype'}, $learningResourceTypes, "map_vocab_learning_resource_types_value_only")) {
                continue;
            }
        }

        // Filter Widgets
        if (!empty($prop->{'ccm:oeh_widgets'})){
            if (wlo_edu_filter($prop->{'ccm:oeh_widgets'}, $oehWidgets, "map_vocab_oeh_widgets_value_only")) {
                continue;
            }
        }

        $contentArray[] = array(
            'mediatype' => $mediaTypes[$reference->mediatype],
            'image_url' => $reference->preview->url,
            //'content_url' => $reference->content->url,
            'content_url' => $reference->properties->{'ccm:wwwurl'}[0] ? $reference->properties->{'ccm:wwwurl'}[0] : $reference->content->url,
            'title' => $reference->properties->{'cclom:title'}[0] ? $reference->properties->{'cclom:title'}[0] : $reference->properties->{'cm:name'}[0],
            'description' => !empty($reference->properties->{'cclom:general_description'}) ? (implode("\n", $reference->properties->{'cclom:general_description'})) : '',
            'source' => !empty($reference->properties->{'ccm:metadatacontributer_creatorFN'}) ? (implode("\n", $reference->properties->{'cclom:general_description'})) : '',
        );
    } //end foreach


    if (!empty($maxResults)){
        $contentArray = array_slice($contentArray, 0, $maxResults );
    }
    if (!empty($contentArray)){ ?>
        <div class="portal-collection-content-browser">
            <?php if ($layout == 'list') { ?>
                <div class="portal_content_list">
            <?php }else if ($layout == 'grid') { ?>
                <div class="portal_content_grid <?php echo ($horizontal_scroll == true) ? 'x-scroll' : '' ?>">
            <?php }else if ($layout == 'slider') {
                $sliderId = uniqid('slider-'); ?>
                <div class="portal_content_slider" id="<?php echo $sliderId; ?>">
            <?php }

                foreach ($contentArray as $content) { ?>

                    <div class="portal_content_branch">
                        <div class="media-type"><?php echo $content['mediatype'] ?></div>
                        <?php if (!empty($content['image_url'])) { ?>
                            <img src="<?php echo $content['image_url']; ?>&crop=true&maxWidth=400&maxHeight=400" alt="<?php echo $content['title']; ?>">
                        <?php } ?>
                        <div class="portal_content_info">
                            <div class="portal_search_text">
                                <a href="<?php echo $content['content_url']; ?>" target="_blank">
                                    <h6><?php echo $content['title']; ?></h6></a>
                                <h6 class="media-type"><?php echo $content['mediatype'] ?></h6>
                                <p><?php echo $content['description'] ?></p>
                            </div>
                            <div class="portal_search_button">
                                <a class="button primary small" href="<?php echo $content['content_url']; ?>" target="_blank">
                                    <?php echo $content['mediatype']; ?> öffnen
                                    <span class="hidden_context">für <?php echo $content['title']; ?></span>
                                </a>
                            </div>
                        </div>
                    </div>


                <?php } ?>

                    </div>
                </div>

                <script type="text/javascript">
                    jQuery(function () {
                        // Handler for .ready() called. Put the Slick Slider etc. init code here.
                        function loadSearchSlider() {
                            if (typeof jQuery().slick === "function") {
                                jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                                    infinite: false,
                                    slidesToShow: <?php echo $slidesToShow; ?>,
                                    slidesToScroll: <?php echo $slidesToScroll; ?>,
                                    arrows: true,
                                    dots: true,
                                    zIndex: 0,
                                    responsive: [
                                        {
                                            breakpoint: 950,
                                            settings: {
                                                slidesToShow: 2,
                                                slidesToScroll: 2
                                            }
                                        },
                                        {
                                            breakpoint: 750,
                                            settings: {
                                                slidesToShow: 1,
                                                slidesToScroll: 1
                                            }
                                        }
                                    ]
                                });
                            }
                        }

                        loadSearchSlider();

                        jQuery(window).on('resize', function(){
                            jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
                        });
                    });
                </script>


                <?php }



    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action( 'wp_ajax_source_table', 'source_table' );
add_action( 'wp_ajax_nopriv_source_table', 'source_table' );
function source_table() {

    $url = WLO_REPO . 'rest/search/v1/queriesV2/-home-/mds_oeh/ngsearch/?maxItems=5000&skipCount=0&propertyFilter=-all-';
    $search_criterias = '{"criterias":[{"property":"ccm:objecttype","values":["SOURCE"]}],"facettes":[]}';
    $response = callWloRestApi($url, 'POST', $search_criterias);

    if($response) {

        $data = '{
                  facet(
                    facet: source
                    skipOutputMapping: true
                  ) {
                    buckets {
                      key
                      doc_count
                    }
                  }
                }';
        $sources = callWloGraphApi($data)->data->facet->buckets;
        ?>

    <div class="filterable">
        <table class="wlo_source_dev table js-sort-table">
            <thead>
            <tr>
                <th class="wlo_big_header">Vorhandene Quellen</th>
                <th class="wlo_big_header js-sort-number">Erfasste Inhalte</th>
                <th class="wlo_big_header">Fächerzuordnung</th>
                <th class="wlo_big_header">Erschließungs-Status</th>
                <th class="wlo_big_header">Qualitätskriterien-Check</th>
            </tr>
            <tr class="filters">
                <th><input type="text" placeholder="Suche..." disabled></th>
                <th class="js-sort-number"></th>
                <th><input type="text" placeholder="Suche..." disabled></th>
                <th><input type="text" placeholder="Suche..." disabled></th>
                <th></th>
            </tr>

            </thead>
            <tbody>

                <?php
                foreach($response->nodes as $reference) {
                    $prop = $reference->properties;
                    ?>
                <tr>
                    <?php

                    echo '<td class="wlo_name"><a target="_blank" href="'.$reference->content->url.'">'.$reference->title.'</a></td>';

                    $sourceCount = 0;
                    if (!empty($sources)){

                        foreach ($sources as $source){
                            //if (strtolower($source->key) == strtolower($prop->{'cclom:title'}[0])){
                            if ($source->key == $prop->{'ccm:general_identifier'}[0]){
                                $sourceCount = $source->doc_count;
                            }
                        }
                    }
                    echo '<td class="wlo_count">'.$sourceCount.'</td>';
                    //echo '<script>console.log("'.$prop->{"ccm:general_identifier"}[0].'");</script>';

                    echo '<td class="wlo_subjects">';
                    if (!empty($prop->{'ccm:taxonid_DISPLAYNAME'})){

                            foreach ($prop->{'ccm:taxonid_DISPLAYNAME'} as $subject) {
                                echo '<span class="wlo_subject">' . $subject . '</span>';
                            }

                    }

                    '</td>';


                    if (empty($prop->{'ccm:editorial_checklist_DISPLAYNAME'}[0])){
                        echo '<td>1. redaktionelle Sichtung anstehend</td>';
                    }else{
                        echo '<td>'.$prop->{'ccm:editorial_checklist_DISPLAYNAME'}[0].'</td>';
                    }


                    echo '<td class="wlo_status">';

                    // Zugangsbedingungen (Login)
                    switch ($prop->{'ccm:conditionsOfAccess'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/no_login':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_GRUEN.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/login_for_additional_features':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_ORANGE.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/conditionsOfAccess/login':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_ROT.svg" title="'.$prop->{'ccm:conditionsOfAccess_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ANMELDEN_GRUEN.svg" title="Login unbekannt">';
                    }

                    // Enthält Werbung
                    switch ($prop->{'ccm:containsAdvertisement'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/containsAdvertisement/yes':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_ROT.svg" title="Enthält Werbung: '.$prop->{'ccm:containsAdvertisement_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/containsAdvertisement/no':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_GRUEN.svg" title="Enthält Werbung: '.$prop->{'ccm:containsAdvertisement_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/WERBUNG_GRUEN.svg" title="Werbung unbekannt">';
                    }

                    // Kosten
                    switch ($prop->{'ccm:price'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/price/no':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_GRUEN.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/price/yes_for_additional':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_ORANGE.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/price/yes':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_ROT.svg" title="Kosten: '.$prop->{'ccm:price_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/KOSTEN_GRUEN.svg" title="Kosten unbekannt">';
                    }

                    // Barrierefreiheit
                    switch ($prop->{'ccm:accessibilitySummary'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/a':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_GRUEN.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/aa':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_ORANGE.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/accessibilitySummary/aaa':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_ROT.svg" title="Barrierefreiheit: '.$prop->{'ccm:accessibilitySummary_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/ACCASSIBILITY_GRUEN.svg" title="Barrierefreiheit unbekannt">';
                    }

                    // DSGVO
                    switch ($prop->{'ccm:dataProtectionConformity'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/dataProtectionConformity/generalDataProtectionRegulation':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_GRUEN.svg" title="'.$prop->{'ccm:dataProtectionConformity_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/dataProtectionConformity/noGeneralDataProtectionRegulation':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_ROT.svg" title="'.$prop->{'ccm:dataProtectionConformity_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/DGSVO_GRUEN.svg" title="Nicht DSGVO geprüft">';
                    }

                    // FSK
                    switch ($prop->{'ccm:fskRating'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/0':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK0.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/6':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK6.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/12':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK12.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/16':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK16.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/fskRating/18':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK18.svg" title="'.$prop->{'ccm:fskRating_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/FSK_Gruen.svg" title="FSK unbekannt">';
                    }

                    // OER
                    switch ($prop->{'ccm:license_oer'}[0]){
                        case 'http://w3id.org/openeduhub/vocabs/oer/0':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_Gruen.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/oer/1':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_ORANGE.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                            break;
                        case 'http://w3id.org/openeduhub/vocabs/oer/2':
                            echo '<img class="wlo_source_icon" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_ROT.svg" title="'.$prop->{'ccm:license_oer_DISPLAYNAME'}[0].'">';
                            break;
                        default:
                            echo '<img class="wlo_source_icon grey" src="'.get_template_directory_uri().'/src/assets/img/table_icons/OER_Gruen.svg" title="OER unbekannt">';
                    }


                    echo '</td>';

                }
                echo '</tr>';
                }
                ?>

            </tbody>
        </table>
</div>

<script>
    function checkval() {
        1 == jQuery("tbody tr:visible").length && "No result found" == jQuery("tbody tr:visible td").html() ? jQuery("#rowcount").html("0") : jQuery("#rowcount").html(jQuery("tr:visible").length - 1);
    }
    function filterReady() {
        const t = jQuery(".filterable")
        const e = t.find(".filters input");
        const l = t.find(".table tbody");
        1 == e.prop("disabled") ? (e.prop("disabled", !1), e.first()) : (e.val("").prop("disabled", !0), l.find(".no-result").remove(), l.find("tr").show()), jQuery("#rowcount").html(jQuery(".filterable tr").length - 1);
    }
    jQuery(document).ready(function () {
        jQuery("#rowcount").html(jQuery(".filterable tr").length - 1),
            jQuery(".filterable .filters input").keyup(function (t) {
                if ("9" != (t.keyCode || t.which)) {
                    var e = jQuery(this),
                        l = e.val().toLowerCase(),
                        n = e.parents(".filterable"),
                        i = n.find(".filters th").index(e.parents("th")),
                        r = n.find(".table"),
                        o = r.find("tbody tr"),
                        d = o.filter(function () {
                            return -1 === jQuery(this).find("td").eq(i).text().toLowerCase().indexOf(l);
                        });
                    r.find("tbody .no-result").remove(),
                        o.show(),
                        d.hide(),
                    d.length === o.length && r.find("tbody").prepend(jQuery('<tr class="no-result text-center"><td colspan="' + r.find(".filters th").length + '">No result found</td></tr>'));
                }
                jQuery("#rowcount").html(jQuery("tr:visible").length - 1), checkval();
            });
        filterReady();
    });
</script>

<?php

    wp_die(); // this is required to terminate immediately and return a proper response
}




add_action( 'wp_ajax_fachportal_content_block', 'fachportal_content_block' );
add_action( 'wp_ajax_nopriv_fachportal_content_block', 'fachportal_content_block' );
function fachportal_content_block() {
    global $wpdb; // this is how you get access to the database

    $postID = $_POST['postID'];
    $collectionID = $_POST['collectionID'];
    $headline = $_POST['headline'];
    $descrText = base64_decode($_POST['descrText']);
    $collectionLevel = $_POST['collectionLevel'];
    $blockIcon = $_POST['blockIcon'];
    $softmatch = $_POST['softmatch'];
    $slidesToShow = $_POST['slidesToShow'];
    $slidesToScroll = $_POST['slidesToScroll'];
    $contentCount = $_POST['contentCount'];
    $contentType = json_decode(html_entity_decode($_POST['contentType']), true);
    $educational_filter_json = $_POST['educational_filter_values'];

    $educational_filter_values = json_decode(html_entity_decode($educational_filter_json), true);

    $disciplines = $educational_filter_values["disciplines"];
    $educationalContexts = $educational_filter_values["educationalContexts"];
    $intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
    $oer = $educational_filter_values["oer"];
    $objectTypes = $educational_filter_values["objectTypes"];
    $learningResourceTypes = $educational_filter_values["learningResourceTypes"];
    $generalKeywords = $educational_filter_values["generalKeyword"];
    $oehWidgets = $educational_filter_values["oehWidgets"];


    if ($collectionLevel >= 1){  // activate softmatch for 'themenseiten'
        $softmatch = '1';
    }

    if (empty($contentCount)){
        $contentCount = 500;
    }

    //$addContentPageID = 9614; //dev
    $addContentPageID = 9933; //pre
    //$addContentPageID = 9081; //local

    $pageTitle = get_the_title($postID);
    $pageDiscipline = get_field('discipline', $postID)[0]['label'];

    //only content from the given collection
    $url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID . '/children/references';
    $response = callWloRestApi($url);

    //also content from the sub-collections
    /*
    $url = WLO_REPO . 'rest/search/v1/queriesV2/-home-/mds_oeh/wlo_collection?contentType=FILES&maxItems=5000&skipCount=0&propertyFilter=-all-';
    $body = '{
      "criterias": [
        {
          "property": "collection",
          "values": [
            "'.$collectionID.'"
          ]
        }
      ],
      "facettes": [
      ]
    }';
    $response = callWloRestApi($url, 'POST', $body);
    */

    //$rgbBackgroundColor = $GLOBALS['wlo_fachportal']['rgbBackgroundColor'];
    $rgbBackgroundColor = '255,255,255';

    switch ($contentType['value']){
        case 0: // lerninhalte
            $diagramColor = 'rgba('.$rgbBackgroundColor.', 0.8)';
            break;
        case 1: // tools
            $diagramColor = 'rgba('.$rgbBackgroundColor.', 0.6)';
            break;
        case 2: // methoden
            $diagramColor = 'rgba('.$rgbBackgroundColor.', 0.4)';
            break;
        case 3: // gut zu wissen
            $diagramColor = 'rgba('.$rgbBackgroundColor.', 0.2)';
            break;
        default:
            $diagramColor = 'rgb(250, 250, 250)';

    }

    $contentArray = array();
    //if (!empty($response->nodes)){
    //    foreach ($response->nodes as $reference) {
    if (!empty($response->references)){
        foreach ($response->references as $reference) {

            $prop = $reference->properties;

            // check if deleted
            if($reference->originalId == null){
                continue;
            }

            // Filter disciplines
            if (!empty($disciplines)) {
                if (empty($prop->{'ccm:taxonid'}) && $softmatch !== '1'){ // skip empty?
                    continue;
                }else{
                    if (!checkPropertyMatch($prop->{'ccm:taxonid'}, $disciplines, true)) {
                        continue;
                    }
                }
            }

            // Filter educationalContexts
            if (!empty($educationalContexts)) {
                if (empty($prop->{'ccm:educationalcontext'}) && $softmatch !== '1'){ // skip empty?
                    continue;
                }else{
                    if (!checkPropertyMatch($prop->{'ccm:educationalcontext'}, $educationalContexts, true)) {
                        continue;
                    }
                }
            }

            // Filter intendedEndUserRoles
            if (!empty($intendedEndUserRoles)) {
                if (empty($prop->{'ccm:educationalintendedenduserrole'}) && $softmatch !== '1'){ // skip empty?
                    continue;
                }else{
                    if (!checkPropertyMatch($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, true)) {
                        continue;
                    }
                }
            }

            // Filter ObjectType
            if (!empty($prop->{'ccm:objectType'})) {
                $propObjectType = $prop->{'ccm:objectType'};
                if ($propObjectType && !empty($propObjectType) && !empty($objectTypes) && !in_array($propObjectType, $objectTypes)) {
                    continue;
                }
            }

            // Filter LearningResourceType
            if (!empty($prop->{'ccm:educationallearningresourcetype'})){
                if (wlo_edu_filter($prop->{'ccm:educationallearningresourcetype'}, $learningResourceTypes, "map_vocab_learning_resource_types_value_only")) {
                    continue;
                }
            }

            // Filter oehWidgets
            if (!empty($oehWidgets)){
                if (!empty($prop->{'ccm:oeh_widgets'})){
                    $propOehWidgets = $prop->{'ccm:oeh_widgets'};
                    $oehWidgetsVocab = array_map("map_vocab_oeh_widgets_value_only", $oehWidgets);
                    if (empty( array_intersect($propOehWidgets, $oehWidgetsVocab) ) ) {
                        continue;
                    }
                }
            }

            $oerLicenses = array('CC_0', 'CC_BY', 'CC_BY_SA', 'PDM');
            $nodeLicense = !empty($prop->{'ccm:commonlicense_key'}[0]) ? $prop->{'ccm:commonlicense_key'}[0] : '';
            $isOER = false;
            foreach ($oerLicenses as $license){
                if( $nodeLicense == $license){
                    $isOER = true;
                }
            }

            $contentArray[] = array(
                'id' => $reference->ref->id,
                'image_url' => $reference->preview->url,
                'content_url' => $prop->{'ccm:wwwurl'}[0] ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,
                'title' => $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0],
                'description' => !empty($prop->{'cclom:general_description'}) ? (implode("\n", $prop->{'cclom:general_description'})) : '',
                'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
                'subjects' => !empty($prop->{'ccm:taxonid_DISPLAYNAME'}) ? $prop->{'ccm:taxonid_DISPLAYNAME'} : [],
                'resourcetype' => !empty($prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'}) ? $prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'} : [],
                'educationalcontext' => !empty($prop->{'ccm:educationalcontext_DISPLAYNAME'}) ? $prop->{'ccm:educationalcontext_DISPLAYNAME'} : [],
                'oer' => $isOER,
                'widget' =>  !empty($reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]) ? $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0] : ''
            );

        } //end foreach
    }

    $sliderId = uniqid('slider-');
    $showSliderDots = 'true';
    if (count($contentArray) <= 4 && $slidesToShow >= 3){
        $showSliderDots = 'false';
    }
    ?>

        <div class="header">
            <?php if(!empty($blockIcon)){?>
                <img class="header-icon" src="<?php echo $blockIcon ?>" alt=""/>
            <?php }?>
            <h2>
            <?php echo $headline;
                if ($collectionLevel >= 1){
                    echo ' ('.count($contentArray).')';
                } ?>
            </h2>
            <?php if(!empty($descrText)){?>
                <p><?php echo $descrText ?></p>
            <?php }?>
        </div>

        <div class="content" id="<?php echo $sliderId; ?>">
            <?php
            if (!empty($contentArray)){
                foreach (array_slice($contentArray, 0,$contentCount) as $content) { ?>
                    <div class="widget-content" style="<?php if ($slidesToShow == 1){ echo 'margin: 12px 110px; max-width: 350px;'; } ?>">
                        <?php if (!empty($content['image_url'])) { ?>
                            <img class="main-image" src="<?php echo $content['image_url']; ?>" alt="Cover: <?php echo $content['title']; ?>">
                        <?php } ?>
                        <div class="content-info">
                            <div class="content-header">
                                <?php if ($content['source'] && false){ ?>
                                    <p class="content-source"><?php echo $content['source']; ?></p>
                                <?php } ?>
                                <img class="badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge_green.svg"  alt="Auszeichnung: geprüfter Inhalt">
                                <?php if ($content['oer']){ ?>
                                    <div class="badge ">OER</div>
                                <?php } ?>
                            </div>
                            <div class="content-title"><?php echo $content['title']; ?></div>
                            <p class="content-description"><?php echo $content['description'] ?></p>
                            <div class="content-meta">
                                <?php if (!empty($content['resourcetype'])){
                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/img_icon.svg" alt="Materialart">';
                                    echo '<p>';
                                    $i = 0;
                                    foreach ($content['resourcetype'] as $type){
                                        if(++$i === count($content['resourcetype'])) {
                                            echo $type;
                                        }else{
                                            echo $type.', ';
                                        }
                                    }
                                    echo '</p>';
                                } ?>
                            </div>
                            <div class="content-meta">
                                <?php if (!empty($content['subjects'])){
                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/subject_icon.svg" alt="Fächer">';
                                    echo '<p>';
                                    $i = 0;
                                    foreach ($content['subjects'] as $subject) {
                                        if(++$i === count($content['subjects'])) {
                                            echo $subject;
                                        }else{
                                            echo $subject.', ';
                                        }
                                    }
                                    echo '</p>';
                                } ?>
                            </div>
                            <div class="content-meta">
                                <?php if (!empty($content['educationalcontext'])){
                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/class_icon.svg" alt="Bildungsebene">';
                                    echo '<p>';
                                    $i = 0;
                                    foreach ($content['educationalcontext'] as $subject) {
                                        if(++$i === count($content['educationalcontext'])) {
                                            echo $subject;
                                        }else{
                                            echo $subject.', ';
                                        }
                                    }
                                    echo '</p>';
                                } ?>
                            </div>
                            <a class="content-button" href="<?php echo $content['content_url']; ?>" target="_blank" aria-label="Zum-Inhalt: <?php echo $content['title']; ?>">Zum Inhalt</a>
                        </div>
                    </div>
                <?php }
            }else{
                $contentTitle = 'Noch kein Inhalt?';
                $buttonText = 'Inhalte vorschlagen';
                //$addContentUrl = get_page_link($addContentPageID) . '?type=material&collectionID=' . $collectionID;
                $addContentUrl = get_page_link($addContentPageID) . '?collectionID=' . $collectionID . '&headline=' . $pageTitle .'&pageDiscipline=' . $pageDiscipline;
                if ($contentType['value'] == 1){
                    $contentTitle = 'Noch kein Tool?';
                    $buttonText = 'Tool vorschlagen';
                    //$addContentUrl = get_page_link($addContentPageID) . '?type=tool&collectionID=' . $collectionID;
                    $addContentUrl = get_page_link(2701) . '?type=tool&collectionID=' . $collectionID;
                }

                ?>
                <div class="widget-content no-widget-content">
                    <img class="main-image" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/no-content.png" alt="Cover: Keine Inhalte">
                    <div class="content-info no-content-info">
                        <div class="content-title"><?php echo $contentTitle; ?></div>
                        <p class="content-description">Füge Inhalte zu diesem Thema hinzu...</p>
                        <a class="content-button no-content-button" href="<?php echo $addContentUrl; ?>" target="_blank">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/plus.svg" alt="Icon: Plus"> <?php echo $buttonText; ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>

    <script type="text/javascript">
        jQuery(function () {
            // Handler for .ready() called. Put the Slick Slider etc. init code here.
            function loadWLOSlider() {
                if (typeof jQuery().slick === "function") {
                    jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                        infinite: false,
                        slidesToShow: <?php echo $slidesToShow; ?>,
                        slidesToScroll: <?php echo $slidesToScroll; ?>,
                        arrows: true,
                        dots:  <?php echo $showSliderDots; ?>,
                        zIndex: 0,
                        responsive: [
                            {
                                breakpoint: 1230,
                                settings: {
                                    slidesToShow: <?php echo min($slidesToShow, 3); ?>,
                                    slidesToScroll: <?php echo min($slidesToScroll, 3); ?>
                                }
                            },
                            {
                                breakpoint: 950,
                                settings: {
                                    slidesToShow: <?php echo min($slidesToShow, 2); ?>,
                                    slidesToScroll: <?php echo min($slidesToScroll, 2); ?>,
                                }
                            },
                            {
                                breakpoint: 750,
                                settings: {
                                    slidesToShow: <?php echo min($slidesToShow, 1); ?>,
                                    slidesToScroll: <?php echo min($slidesToScroll, 1); ?>,
                                }
                            }
                        ]
                    });
                }
            }

            loadWLOSlider();

            jQuery(window).on('resize', function(){
                jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
            });
        });

        jQuery( document ).ready(function() {

            <?php if($collectionLevel >= 1){ ?>
                jQuery('#<?php echo str_replace(' ', '-', $contentType['label']); ?>-count').html('<?php echo count($contentArray); ?>');
                let currentCount = parseInt(jQuery('.diagram-count-total').first().text());
                jQuery( '<div class="diagram-count-total">' + (currentCount + <?php echo count($contentArray); ?>) + '</div>' ).replaceAll( ".diagram-count-total" );

                addData(contentChart, '<?php echo $contentType['label']; ?>', <?php echo count($contentArray); ?>, '<?php echo $diagramColor; ?>', <?php echo $contentType['value']; ?>);
            <?php } ?>

        });

    </script>

<?php
    wp_die(); // this is required to terminate immediately and return a proper response
}
