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
    $disciplines =  $_POST['disciplines'];
    $educationalContexts =  $_POST['educationalContexts'];
    $intendedEndUserRoles =  $_POST['intendedEndUserRoles'];

    $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $nodeId . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true&fetchCounts=false&';
    $response = callWloRestApi($url);

    $submenu = '';

    if (!empty($response->collections)) {
        foreach ($response->collections as $collection) {
            if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated') {
                $prop = $collection->properties;
                $ccm_location = str_replace('dev.wirlernenonline.de', 'wirlernenonline.de', $collection->properties->{'cclom:location'}[0]);

                // Filter Disciplines
                if (wlo_edu_filter($prop->{'ccm:taxonid'}, $disciplines, "map_vocab_disciplines_value_only")) {
                    continue;
                }
                // Filter EducationalContext
                if (wlo_edu_filter($prop->{'ccm:educationalcontext'}, $educationalContexts, "map_vocab_educationalContexts_value_only")) {
                    continue;
                }
                // Filter IntendedEndUserRole
                if (wlo_edu_filter($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, "map_vocab_intendedEndUserRoles_value_only")) {
                    continue;
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

        //check if deleted
        if($reference->originalId == null){
            continue;
        }

        if (!checkPropertyMatch($prop->{'ccm:taxonid'}, $disciplines, true)) {
            continue;
        }
        if (!checkPropertyMatch($prop->{'ccm:educationalcontext'}, $educationalContexts, true)) {
            continue;
        }
        if (!checkPropertyMatch($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, true)) {
            continue;
        }

        // Filter ObjectType
        $propObjectType = $prop->{'ccm:objectType'};
        if ($propObjectType && !empty($propObjectType) && !empty($objectTypes) && !in_array($propObjectType, $objectTypes)) {
            continue;
        }

        $displayObjectType = (!empty($objectTypes)) ? array_intersect($propObjectType, $objectTypes)[0] : $propObjectType[0];

        // Filter LearningResourceType
        $propLearningResourceTypes = $prop->{'ccm:educationallearningresourcetype'};
        $propLearningResourceTypes = (!empty($propLearningResourceTypes)) ? array_filter($propLearningResourceTypes) : [];
        $propLearningResourceTypes = (!empty($propLearningResourceTypes)) ? trim_https_http_from_array($propLearningResourceTypes) : [];

        $learningResourceTypesVocab = (!empty($learningResourceTypes) && !empty(array_filter($learningResourceTypes))) ? array_map("map_vocab_learning_resource_types_value_only", $learningResourceTypes) : [];
        $learningResourceTypesVocab = (!empty($learningResourceTypesVocab)) ? array_filter($learningResourceTypesVocab) : [];
        $learningResourceTypesVocab = (!empty($learningResourceTypesVocab)) ? trim_https_http_from_array($learningResourceTypesVocab) : [];

        $intersectLearningResourceType = array_intersect($propLearningResourceTypes, $learningResourceTypesVocab);
        $filterLearningResourceTypes = (empty($propLearningResourceTypes)) ? true : empty($intersectLearningResourceType);
        if (!empty($learningResourceTypesVocab) && $filterLearningResourceTypes) {
            continue;
        }

        $displayLearningResourceType = (!empty($intersectLearningResourceType)) ? $intersectLearningResourceType[0] : $propLearningResourceTypes[0];
        $displayLearningResourceType = (!empty($displayLearningResourceType)) ? $displayLearningResourceType : 'Inhalt';

        // Filter LearningResourceType
        $propOehWidgets = $prop->{'ccm:oeh_widgets'};
        $propOehWidgets = (!empty($propOehWidgets)) ? array_filter($propOehWidgets) : [];
        $propOehWidgets = (!empty($propOehWidgets)) ? trim_https_http_from_array($propOehWidgets) : [];

        $oehWidgetsVocab = (!empty($oehWidgets) && !empty(array_filter($oehWidgets))) ? array_map("map_vocab_oeh_widgets_value_only", $oehWidgets) : [];
        $oehWidgetsVocab = (!empty($oehWidgetsVocab)) ? array_filter($oehWidgetsVocab) : [];
        $oehWidgetsVocab = (!empty($oehWidgetsVocab)) ? trim_https_http_from_array($oehWidgetsVocab) : [];

        $intersectOehWidgets = array_intersect($propOehWidgets, $oehWidgetsVocab);
        $filterOehWidgets = (empty($propOehWidgets)) ? true : empty($intersectOehWidgets);

        if (!empty($oehWidgetsVocab) && $filterOehWidgets) {
            continue;
        }

        $displayOehWidgets = (!empty($intersectOehWidgets)) ? $intersectOehWidgets[0] : $propOehWidgets[0];
        $displayOehWidgets = (!empty($displayOehWidgets)) ? $displayOehWidgets : 'Inhalt';

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
