<?php

add_action('wp_head', 'wlo_ajaxurl');
function wlo_ajaxurl()
{
    echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}


add_action('wp_ajax_wlo_submenu', 'wlo_submenu');
add_action('wp_ajax_nopriv_wlo_submenu', 'wlo_submenu');
function wlo_submenu()
{
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
                if (!empty($prop->{'ccm:collectionshorttitle'}[0])) {
                    $title = $prop->{'ccm:collectionshorttitle'}[0];
                }
                $submenu .= '<a href="' . $ccm_location . '">';
                $submenu .= '<h6>' . $title . '</h6>';
                $submenu .= '</a>';
            }
        }
    }
    echo $submenu;
    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action('wp_ajax_collection_content_browser', 'collection_content_browser');
add_action('wp_ajax_nopriv_collection_content_browser', 'collection_content_browser');
function collection_content_browser()
{
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
        if ($reference->originalId == null) {
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
        if (!empty($prop->{'ccm:educationallearningresourcetype'})) {
            if (wlo_edu_filter($prop->{'ccm:educationallearningresourcetype'}, $learningResourceTypes, "map_vocab_learning_resource_types_value_only")) {
                continue;
            }
        }

        // Filter Widgets
        if (!empty($prop->{'ccm:oeh_widgets'})) {
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


    if (!empty($maxResults)) {
        $contentArray = array_slice($contentArray, 0, $maxResults);
    }
    if (!empty($contentArray)) { ?>
        <div class="portal-collection-content-browser">
            <?php
            $idString = '';
            if ($layout == 'list') {
                $classes = ['portal_content_list'];
            } else if ($layout == 'grid') {
                $classes = ['portal_content_grid'];
                if ($horizontal_scroll == true) {
                    $classes[] = 'x-scroll';
                }
            } else if ($layout == 'slider') {
                $classes = ['portal_content_slider'];
                $sliderId = uniqid('slider-');
                $idString = 'id="' . $sliderId . '"';
            } ?>
            <div <?php echo $idString; ?> class="<?php echo implode(' ', $classes); ?>">
                <?php foreach ($contentArray as $content) { ?>
                    <div class="portal_content_branch">
                        <div class="media-type"><?php echo $content['mediatype'] ?></div>
                        <?php if (!empty($content['image_url'])) { ?>
                            <img src="<?php echo $content['image_url']; ?>&crop=true&maxWidth=400&maxHeight=400" alt="<?php echo $content['title']; ?>">
                        <?php } ?>
                        <div class="portal_content_info">
                            <div class="portal_search_text">
                                <a href="<?php echo $content['content_url']; ?>" target="_blank">
                                    <h6><?php echo $content['title']; ?></h6>
                                </a>
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
            jQuery(function() {
                // Handler for .ready() called. Put the Slick Slider etc. init code here.
                function loadSearchSlider() {
                    if (typeof jQuery().slick === "function") {
                        jQuery('#<?php echo $sliderId ?>').not('.slick-initialized').slick({
                            infinite: false,
                            slidesToShow: <?php echo $slidesToShow; ?>,
                            slidesToScroll: <?php echo $slidesToScroll; ?>,
                            arrows: true,
                            dots: true,
                            zIndex: 0,
                            responsive: [{
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

                jQuery(window).on('resize', function() {
                    jQuery('#<?php echo $sliderId ?>').slick('refresh');
                });
            });
        </script>


    <?php }



    wp_die(); // this is required to terminate immediately and return a proper response
}


add_action('wp_ajax_fachportal_content_block', 'fachportal_content_block');
add_action('wp_ajax_nopriv_fachportal_content_block', 'fachportal_content_block');
function fachportal_content_block()
{
    $postID = $_POST['postID'];
    $collectionID = $_POST['collectionID'];
    $headline = base64_decode($_POST['headline']);
    $descrText = base64_decode($_POST['descrText']);
    $collectionLevel = $_POST['collectionLevel'];
    $blockIcon = $_POST['blockIcon'];
    $softmatch = $_POST['softmatch'];
    $sorting = $_POST['sorting'];
    $slidesToShow = $_POST['slidesToShow'];
    $slidesToScroll = $_POST['slidesToScroll'];
    $contentCount = $_POST['contentCount'];
    $contentType = json_decode(html_entity_decode($_POST['contentType']), true);
    $educational_filter_json = $_POST['educational_filter_values'];

    $educational_filter_values = json_decode(html_entity_decode($educational_filter_json), true);

    $disciplines = $educational_filter_values["disciplines"];
    $educationalContexts = $educational_filter_values["educationalContexts"];
    $intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
    $objectTypes = $educational_filter_values["objectTypes"];
    $learningResourceTypes = $educational_filter_values["learningResourceTypes"];
    $oehWidgets = $educational_filter_values["oehWidgets"];

    if ($collectionLevel >= 1) {  // activate softmatch for 'themenseiten'
        $softmatch = '1';
    }

    if (empty($contentCount)) {
        $contentCount = 500;
    }

    $pageTitle = get_the_title($postID);
    $pageDiscipline = get_field('discipline', $postID)[0]['label'];

    //only content from the given collection
    $url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID . '/children/references?sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
    $response = callWloRestApi($url);

    $contentArray = array();
    if (!empty($response->references)) {
        foreach ($response->references as $reference) {

            $prop = $reference->properties;

            // check if deleted
            if ($reference->originalId == null) {
                //echo 'skipped deleted';
                continue;
            }

            // Filter disciplines
            if (!empty($disciplines)) {
                if (empty($prop->{'ccm:taxonid'}) && $softmatch !== '1') { // skip empty?
                    continue;
                } else if (!empty($prop->{'ccm:taxonid'})) {
                    if (!checkPropertyMatch($prop->{'ccm:taxonid'}, $disciplines, true)) {
                        //echo 'skipped $disciplines';
                        continue;
                    }
                }
            }

            // Filter educationalContexts
            if (!empty($educationalContexts)) {
                if (empty($prop->{'ccm:educationalcontext'}) && $softmatch !== '1') { // skip empty?
                    continue;
                } else {
                    if (!checkPropertyMatch($prop->{'ccm:educationalcontext'}, $educationalContexts, true)) {
                        //echo 'skipped $educationalContexts';
                        continue;
                    }
                }
            }

            // Filter intendedEndUserRoles
            if (!empty($intendedEndUserRoles)) {
                if (empty($prop->{'ccm:educationalintendedenduserrole'}) && $softmatch !== '1') { // skip empty?
                    continue;
                } else {
                    if (!checkPropertyMatch($prop->{'ccm:educationalintendedenduserrole'}, $intendedEndUserRoles, true)) {
                        //echo 'skipped $intendedEndUserRoles';
                        continue;
                    }
                }
            }

            // Filter ObjectType
            if (!empty($prop->{'ccm:objectType'})) {
                $propObjectType = $prop->{'ccm:objectType'};
                if ($propObjectType && !empty($propObjectType) && !empty($objectTypes) && !in_array($propObjectType, $objectTypes)) {
                    //echo 'skipped $propObjectType';
                    continue;
                }
            }

            // Filter LearningResourceType
            if (!empty($prop->{'ccm:educationallearningresourcetype'})) {
                if (wlo_edu_filter($prop->{'ccm:educationallearningresourcetype'}, $learningResourceTypes, "map_vocab_learning_resource_types_value_only")) {
                    //echo 'skipped LearningResourceType';
                    continue;
                }
            }

            // Filter oehWidgets
            if (!empty($oehWidgets)) {
                if (!empty($prop->{'ccm:oeh_widgets'})) {
                    $propOehWidgets = $prop->{'ccm:oeh_widgets'};
                    $oehWidgetsVocab = array_map("map_vocab_oeh_widgets_value_only", $oehWidgets);
                    if (empty(array_intersect($propOehWidgets, $oehWidgetsVocab))) {
                        //echo 'skipped oehWidgets';
                        continue;
                    }
                }
            }


            $contentArray[] = processEduSharingNode($reference);
        } //end foreach
    }

    $sliderId = uniqid('slider-');
    ?>

    <div class="header">
        <?php if (!empty($blockIcon)) { ?>
            <img class="header-icon" src="<?php echo $blockIcon ?>" alt="" />
        <?php } ?>
        <h3>
            <?php echo $headline;
            if ($collectionLevel >= 1) {
                echo ' (' . count($contentArray) . ')';
            } ?>
        </h3>
        <?php if (!empty($descrText)) { ?>
            <p><?php echo $descrText ?></p>
        <?php } ?>
    </div>

    <div class="content" id="<?php echo $sliderId; ?>">
        <?php
        if (!empty($contentArray)) {
            if ($sorting == '2') {
                error_log('shuffeld content: ' . $headline);
                shuffle($contentArray);
            }
            foreach (array_slice($contentArray, 0, $contentCount) as $content) {
                printWloCard($content);
            }
        } else {
            $contentTitle = 'Noch kein Inhalt?';
            $buttonText = 'Inhalte vorschlagen';
            $addContentUrl = get_page_link(ADD_CONTENT_PAGE_ID) . '?collectionID=' . $collectionID . '&headline=' . $pageTitle . '&pageDiscipline=' . $pageDiscipline;
            if (!empty($contentType['value']) && $contentType['value'] == 1) {
                $contentTitle = 'Noch kein Tool?';
                $buttonText = 'Tool vorschlagen';
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

<?php
    initSlick($sliderId, $slidesToShow, $slidesToScroll, count($contentArray) + 1);
    wp_die(); // this is required to terminate immediately and return a proper response
}

add_action('wp_ajax_wlo_metaQs', 'wlo_metaQs');
add_action('wp_ajax_nopriv_wlo_metaQs', 'wlo_metaQs');
function wlo_metaQs()
{
    global $wpdb;

    $metaQsMode = $_POST['metaQsMode'];
    $metaQsHeight = $_POST['metaQsHeight'];
    $ticket = base64_decode($_POST['ticket']);
    $admin = $_POST['admin'];
    $widegtID = uniqid();
    $badgeID = uniqid();

?>

    <div class="metaqs-badge" id="<?php echo $badgeID; ?>"></div>
    <app-meta-widget id="<?php echo $widegtID; ?>" collectionid="" ticket="<?php echo $ticket; ?>" mode="<?php echo $metaQsMode; ?>" style="display:flex; height: <?php echo $metaQsHeight; ?>px"></app-meta-widget>

    <script>
        jQuery('#<?php echo $widegtID; ?>').attr("collectionid", jQuery('#portal option:selected').val());
        jQuery('#<?php echo $badgeID; ?>').html(jQuery('#portal').find('option:selected').attr('name'));
    </script>

    <?php if ($admin) { ?>
        <link rel="stylesheet" href="<?php echo WLO_METAQS_NG_URL; ?>/styles.css" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="<?php echo WLO_METAQS_NG_URL; ?>styles.css">
        </noscript>
        <script src="<?php echo WLO_METAQS_NG_URL; ?>/runtime.js" defer></script>
        <script src="<?php echo WLO_METAQS_NG_URL; ?>/polyfills.js" defer></script>
        <script src="<?php echo WLO_METAQS_NG_URL; ?>/main.js" defer></script>
    <?php echo '</div>';
    } ?>

    <?php
    wp_die();
}

add_action('wp_ajax_wloAiCareerAdvice', 'wloAiCareerAdvice');
add_action('wp_ajax_nopriv_wloAiCareerAdvice', 'wloAiCareerAdvice');
function wloAiCareerAdvice()
{
    $collectionId = $_POST['collectionId'];
    $ancestors = getCollectionAncestors($collectionId);
    if (empty($ancestors)) {
        wp_send_json_error(null, 404);
    } else {
        // Remove root "Portale" node.
        $ancestors = array_slice($ancestors, 1);
        $titles = array_map(fn ($node) => $node->title, $ancestors);
        $url = WLO_AI_PROMPT_SERVICE_URL . "/ai/prompt/profession/description";
        $headers = array(
            'ai-prompt-token' => WLO_AI_PROMPT_SERVICE_TOKEN,
            'Content-Type' => 'application/json',
        );
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'timeout' => 60,
            'body' => $titles[count($titles) - 1],
        ));
        if (is_wp_error($response) || $response['response']['code'] != 200) {
            error_log(print_r($response, true));
            wp_send_json_error(null, 500);
        }
        header('Content-Type: application/json');
        echo $response['body'];
    }
    wp_die();
}

add_action('wp_ajax_wloSubCareerPagesLinks', 'wloSubCareerPagesLinks');
add_action('wp_ajax_nopriv_wloSubCareerPagesLinks', 'wloSubCareerPagesLinks');
/** Prints HTML to render a link list of sub pages of a career page. */
function wloSubCareerPagesLinks()
{
    get_template_part('template-parts/career/sub-pages-links', args: array(
        'collectionId' => $_GET['collectionId'],
        'subjectPortalId' => $_GET['subjectPortalId'],
    ));
    wp_die();
}

add_action('wp_ajax_wloJobProfilesCarousel', 'wloJobProfilesCarousel');
add_action('wp_ajax_nopriv_wloJobProfilesCarousel', 'wloJobProfilesCarousel');
/** Prints HTML to render a job profiles carousel. */
function wloJobProfilesCarousel()
{
    // Use post id instead of passing the topic title directly to prevent forged requests for
    // arbitrary input.
    $postId = $_GET['postId'];
    $topic = get_the_title($postId);
    if (empty($topic)) {
        wp_send_json_error(null, 404);
    } else {
        get_template_part('template-parts/career/job-profiles-carousel', args: array(
            'topic' => $topic,
        ));
    }
    wp_die();
}

add_action('wp_ajax_wloEventsMap', 'wloEventsMap');
add_action('wp_ajax_nopriv_wloEventsMap', 'wloEventsMap');
/** Prints HTML to render a map for event locations. */
function wloEventsMap()
{
    /** The ID of the original topic page. */
    $postId = $_GET['postId'];
    $topic = get_the_title($postId);
    if (empty($topic)) {
        wp_send_json_error(null, 404);
    } else {
        get_template_part('template-parts/career/events-map', args: array(
            'topicPagePostId' => $postId,
            'topic' => $topic,
        ));
    }
    wp_die();
}

add_action('wp_ajax_wloEventLocations', 'wloEventLocations');
add_action('wp_ajax_nopriv_wloEventLocations', 'wloEventLocations');
/** Retrieves event locations for the topic given by `postId` in JSON format. */
function wloEventLocations()
{
    /** The ID of the original topic page. */
    $postId = $_GET['postId'];
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];
    $zoom = $_GET['zoom'];
    $topic = get_the_title($postId);
    if (empty($topic)) {
        wp_send_json_error(null, 404);
    } else {
        $eduSharingLocations = getEduSharingLocationData($postId);
        $testLocations = getTestLocationData();
        $locations = array_merge($eduSharingLocations, $testLocations);
        wp_send_json(array('eventLocations' => $locations));
    }
    wp_die();
}

function getEduSharingLocationData($postId)
{
    $collectionId = wlo_getPortalIdByPostId($postId);
    $url = add_query_arg(
        array(
            'contentType' => 'ALL',
            'skipCount' => 0,
            'maxItems' => 999,
            'propertyFilter' => '-all-',
        ),
        WLO_REPO . 'rest/search/v1/queries/-home-/mds_oeh/ngsearch/',
    );
    $body = <<<EOD
    {
        "criteria": [
            {
              "property": "virtual:collection_id",
              "values": [ "$collectionId" ]
            },
            {
              "property": "ccm:oeh_geographical_location_lat",
              "values": [ "exists" ]
            }
        ]
    }
    EOD;
    $response = callWloRestApi($url, 'POST', $body);
    // error_log(print_r($response, true));
    $eventLocations = [];
    foreach ($response->nodes as &$node) {
        $eventLocations[] = mapEduSharingNodeToEventLocation($node);
    }
    unset($node);
    return $eventLocations;
}

function mapEduSharingNodeToEventLocation($node)
{
    $props = $node->properties;
    // error_log(print_r($props, true));
    return array(
        'lat' => $props->{'ccm:oeh_geographical_location_lat'}[0] ?? 0,
        'lon' => $props->{'ccm:oeh_geographical_location_lng'}[0] ?? 0,
        'begin' => $props->{'ccm:oeh_event_begin'}[0],
        'end' => $props->{'ccm:oeh_event_end'}[0],
        'title' => $node->title,
        'location' => $props->{'ccm:oeh_geographical_location_address_formatted'}[0],
        'description' => $props->{'cclom:general_description'}[0],
        'educationalContext' => $props->{'ccm:educationalcontext'},
        'url' => $props->{'ccm:wwwurl'}[0],
    );
}

function getTestLocationData()
{
    $path = plugin_dir_path(__FILE__) . '../src/assets/data/organization.json';
    $jsonString = file_get_contents($path);
    $jsonData = json_decode($jsonString, true);
    $eventLocations = [];
    foreach ($jsonData as &$entry) {
        // error_log(print_r($entry, true));
        $eventLocations[] = mapTestLocationEntryToEventLocation($entry);
    }
    unset($entry);
    return $eventLocations;
}

function mapTestLocationEntryToEventLocation($entry)
{
    $location = '';
    if (!empty($entry['location']['address']['streetAddress'])) {
        $location .= $entry['location']['address']['streetAddress'] . ', ';
    }
    $location .= $entry['location']['address']['postalCode'] . ' '
        . $entry['location']['address']['addressLocality'] . ', '
        . $entry['location']['address']['addressCountry'];

    return array(
        'lat' => $entry['location']['geo']['latitude'],
        'lon' => $entry['location']['geo']['longitude'],
        'title' => $entry['name'],
        'location' => $location,
        'description' => !empty($entry['description']) ? $entry['description'] : 'Test Beschreibung',
        'educationalContext' => [$entry['additionalType']['id']],
        'url' => !empty($entry['url']) ? $entry['url'] : 'http://example.com',
    );
}

add_action('wp_ajax_emptySwimlaneContent', 'emptySwimlaneContent');
add_action('wp_ajax_nopriv_emptySwimlaneContent', 'emptySwimlaneContent');
function emptySwimlaneContent()
{
    global $wpdb;

    $collectionID = $_POST['collectionID'];
    $pageTitle = $_POST['pageTitle'];
    $lrtID = $_POST['lrtID'];
    $searchLrtID = json_decode(html_entity_decode($_POST['searchLrtID']));

    // Get sub collections
    $url = WLO_REPO . 'rest/search/v1/queries/-home-/mds_oeh/wlo_collection?contentType=FILES&maxItems=30&skipCount=0&sortProperties=cm%3Amodified&sortAscending=false&propertyFilter=-all-';
    $body = <<<END
    {
        "criteria": [
            {
                "property": "collection",
                "values": [
                    "$collectionID"
                ]
            }
        ],
        "facets": [
        ]
    }
    END;
    $subCollectionContent = callWloRestApi($url, 'POST', $body);

    // Populate $contentArray with filtered and slightly processed nodes from sub collections
    $contentArray = array();
    if (!empty($subCollectionContent->nodes)) {
        foreach ($subCollectionContent->nodes as $reference) {
            $prop = $reference->properties;
            $title = $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0];
            // Filter nodes with equal title
            foreach ($contentArray as $newContent) {
                if ($newContent['title'] == $title) {
                    continue 2;
                }
            }


            // Push to $contentArray
            $contentArray[] = processEduSharingNode($reference);
        } //end foreach
    }

    $vocab = array();
    $vocabIDs = explode(',', $lrtID);
    foreach ($vocabIDs as $id) {
        $new_vocab = getNewLrtList(basename($id));
        $vocab = array_merge($vocab, $new_vocab);
    }

    $swimlane_content = wloFilterSwimlane($contentArray, $vocab);

    if (!empty($swimlane_content['filtered_content'])) { ?>
        <div class="subcollections-alert">
            <p>Hier gibt es noch keine geprüften Inhalte.<br>Lass dir die geprüften Inhalte von Unterthemen anzeigen.</p>
            <button onclick="toggleSubcollections(this)">Inhalte anzeigen</button>
        </div>

        <div class="content-from-subcollections">
            <?php
            foreach (array_slice($swimlane_content['filtered_content'], 0, 3) as $contentItem) {
                printWloCard($contentItem);
            }
            ?>
        </div>
    <?php
    } else {
        $searchUrl = 'https://suche.wirlernenonline.de/de/search?q=' . $pageTitle . '&filters={"oehLrtAggregated":[';
        $numItems = count($searchLrtID);
        $i = 0;
        foreach ($searchLrtID as $id) {
            if (++$i === $numItems) {
                $searchUrl .= '"' . $id . '"';
            } else {
                $searchUrl .= '"' . $id . '",';
            }
        }
        $searchUrl .= ']}';
    ?>
        <div class="subcollections-alert">
            <p>Hier gibt es noch keine geprüften Inhalte.<br>Finde passende maschinell gesammelte Inhalte mit der Suche.</p>
            <a href="<?php echo htmlspecialchars($searchUrl); ?>" target="_blank">Suche öffnen</a>
        </div>
<?php
    }
    wp_die();
}
