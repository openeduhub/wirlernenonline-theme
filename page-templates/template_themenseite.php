<?php
/*
Template Name: WLO-Themenseite
Template Post Type: post, page, portal
 */

get_header();
?>
<?php

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];

$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

# Get collection from edu-sharing. Used to display description.
$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID;
$response = callWloRestApi($url);

# Get parents of collection from edu-sharing. Used for breadcrumbs.
$url = WLO_REPO . 'rest/node/v1/nodes/-home-/' . $collectionID . '/parents?propertyFilter=-all-&fullPath=false';
$parents = callWloRestApi($url);
if (isset($parents->nodes)) {
    $parents = $parents->nodes;
    # Root subject portal
    $portal = $parents[count($parents) - 2];
}

$portalTitle = '';
if (isset($portal->title)) {
    $portalTitle = $portal->title;
}
$portalUrl = '#';
if (!empty($portal->properties->{'cclom:location'}[0])) {
    $portalUrl = $portal->properties->{'cclom:location'}[0];
}
$pageDiscipline = get_field('discipline', $postID)[0]['label'];

$portalID = get_page_by_title($portalTitle, OBJECT, 'portal')->ID;
switch ($portalID) {
    case 62890:
        $portalID = 54293; // Musik
        break;
    case 21284:
        $portalID = 47664; // Wirtschaft
        break;
    case 21281:
        $portalID = 6509; // Geschichte
        break;
    case 21276:
        $portalID = 21348; // Religion
        break;
}
// Get authors field from root subject page
$authors = get_field('authors', $portalID);
update_field('authors', $authors, $postID);

$author_ids = get_field('authors', $postID);
if (empty($author_ids)) {
    $author_ids = array();
}

// Collect breadcrumbs data
$breadcrumbs = array();
if (!empty($parents)) {
    foreach ($parents as $node) {
        if (is_object($node)) {
            if ($node->title == 'Portale') {
                $breadcrumbs[] = ['Fachportale', get_page_link(55115)];
            } else {
                $title = $node->title;
                if (!empty($node->properties->{'ccm:collectionshorttitle'}[0])) {
                    $title = $node->properties->{'ccm:collectionshorttitle'}[0];
                }
                $breadcrumbs[] = [$title, wlo_convert_dev_url($node->properties->{'cclom:location'}[0])];
            }
        }
    }
    $breadcrumbs = array_reverse($breadcrumbs);
}

// Use collection description from edu-sharing; default to standard text if no description available
if (empty($response->collection->properties->{'cm:description'}[0])) {
    $description = '
                    Hier findest du zahlreiches kostenloses Material für ' . $portalTitle . '!<br><br>
                    Neben sorgfältig ausgewählten Inhalten für jede Art von Unterricht findest du auch kurzweilige 
                    Inhalte für ' . $portalTitle . ' zum eigenständigen Lernen.<br><br>
                    Du kennst tolle Inhalte? Dann bringe dich und dein Wissen ein! Hilf mit, die besten Inhalte zu sammeln 
                    und zu teilen, empfehle dein persönliches Fach-Highlight oder 
                    <a href="' . get_page_link(97) . '">mach mit</a> in unserer Fachredaktion!
    ';
} else {
    $description = $response->collection->properties->{'cm:description'}[0];
}

// Construct URL for "Inhalte vorschlagen". URL is extended to reflect swimlane.
$addContentUrl = get_page_link(9933) . '?collectionID=' . $collectionID . '&headline=' . get_the_title($postID) . '&pageDiscipline=' . $pageDiscipline;


if (!function_exists('helper_useLightColor')) {
    // Fix contrast issues depending on background color.
    function helper_useLightColor($bgColor)
    {
        $color = ($bgColor[0] === '#') ? substr($bgColor, 1, 7) : $bgColor;
        $r = intval(substr($color, 0, 2), 16); // hexToR
        $g = intval(substr($color, 2, 4), 16); // hexToG
        $b = intval(substr($color, 4, 6), 16); // hexToB
        //$value = (($r * 0.299) + ($g * 0.587) + ($b * 0.114));
        $value = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;
        return $value > 140;
    }
}

$backgroundColor = wloSubjectType($portalTitle)['color'];
$rgbBackgroundColor = hex2rgb($backgroundColor);
$fontColor = (!empty($backgroundColor) && helper_useLightColor($backgroundColor)) ? "#313131" : "#ffffff";

// Provide global context for other files used for swimlanes.
$GLOBALS['wlo_fachportal'] = array(
    'title' => $portalTitle,
    'backgroundColor' => $backgroundColor,
    'rgbBackgroundColor' => $rgbBackgroundColor
);

// newest contents
$url = WLO_REPO . 'rest/search/v1/queries/-home-/mds_oeh/wlo_collection?contentType=FILES&maxItems=8&skipCount=0&sortProperties=cm%3Amodified&sortAscending=false&propertyFilter=-all-';
$body = '{
  "criteria": [
    {
      "property": "collection",
      "values": [
        "' . $collectionID . '"
      ]
    }
  ],
  "facets": [
  ]
}';
$newestContent = callWloRestApi($url, 'POST', $body);

$contentArray = array();
if (!empty($newestContent->nodes)) {
    foreach ($newestContent->nodes as $reference) {
        $prop = $reference->properties;

        //check if deleted
        if ($reference->originalId == null) {
            continue;
        }

        $title = $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0];
        foreach ($contentArray as $content) {
            if ($content['title'] == $title) {
                continue 2;
            }
        }

        $oerLicenses = array('CC_0', 'CC_BY', 'CC_BY_SA', 'PDM');
        $nodeLicense = !empty($prop->{'ccm:commonlicense_key'}[0]) ? $prop->{'ccm:commonlicense_key'}[0] : '';
        $isOER = false;
        foreach ($oerLicenses as $license) {
            if ($nodeLicense == $license) {
                $isOER = true;
            }
        }

        $contentArray[] = array(
            //'id' => $prop->{'ccm:original'}[0] ? $prop->{'ccm:original'}[0] : $reference->ref->id,
            'id' => $reference->ref->id,
            'image_url' => $reference->preview->url,
            'content_url' => !empty($prop->{'ccm:wwwurl'}[0]) ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,
            'title' => $title,
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


// get themenseiten-content
$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID . '/children/references?sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
$response = callWloRestApi($url);
$themenseiten_contentArray = array();
$educationalcontextArray = array();
$enduserroleArray = array();
$oerCount = 0;
if (!empty($response->references)) {
    foreach ($response->references as $reference) {

        $prop = $reference->properties;

        // check if deleted
        if ($reference->originalId == null) {
            //echo 'skipped deleted';
            continue;
        }

        $oerLicenses = array('CC_0', 'CC_BY', 'CC_BY_SA', 'PDM');
        $nodeLicense = !empty($prop->{'ccm:commonlicense_key'}[0]) ? $prop->{'ccm:commonlicense_key'}[0] : '';
        $isOER = false;
        foreach ($oerLicenses as $license) {
            if ($nodeLicense == $license) {
                $isOER = true;
                $oerCount++;
            }
        }

        $themenseiten_contentArray[] = array(
            //'id' => $prop->{'ccm:original'}[0] ? $prop->{'ccm:original'}[0] : $reference->ref->id,
            'id' => $reference->ref->id,
            'image_url' => $reference->preview->url,
            'content_url' => !empty($prop->{'ccm:wwwurl'}[0]) ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,
            'title' => $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0],
            //'description' => !empty($prop->{'cclom:general_description'}) ? (implode("\n", $prop->{'cclom:general_description'})) : '',
            'description' => $prop->{'cclom:general_description'}[0] ? $prop->{'cclom:general_description'}[0] : $reference->ref->id,
            'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
            'subjects' => !empty($prop->{'ccm:taxonid_DISPLAYNAME'}) ? $prop->{'ccm:taxonid_DISPLAYNAME'} : [],
            //'resourcetype' => !empty($prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'}) ? $prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'} : [],
            'resourcetype' => !empty($prop->{'ccm:oeh_lrt_aggregated_DISPLAYNAME'}) ? $prop->{'ccm:oeh_lrt_aggregated_DISPLAYNAME'} : [],
            'educationalcontext' => !empty($prop->{'ccm:educationalcontext_DISPLAYNAME'}) ? $prop->{'ccm:educationalcontext_DISPLAYNAME'} : [],
            'enduserrole' => !empty($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'}) ? $prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'} : [],
            'oer' => $isOER,
            'widget' =>  !empty($reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]) ? $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0] : '',
            'oeh_lrt' =>  !empty($reference->properties->{'ccm:oeh_lrt'}) ? $reference->properties->{'ccm:oeh_lrt'} : '',
            'added' => false
        );

        if (!empty($prop->{'ccm:educationalcontext_DISPLAYNAME'})) {
            foreach ($prop->{'ccm:educationalcontext_DISPLAYNAME'} as $item) {
                if (!array_key_exists($item, $educationalcontextArray)) {
                    $educationalcontextArray[$item] = 1;
                } else {
                    $educationalcontextArray[$item] = $educationalcontextArray[$item] + 1;
                }
            }
        }
        if (!empty($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'})) {
            foreach ($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'} as $item) {
                if (!array_key_exists($item, $enduserroleArray)) {
                    $enduserroleArray[$item] = 1;
                } else {
                    $enduserroleArray[$item] = $enduserroleArray[$item] + 1;
                }
            }
        }
    } //end foreach
}

$GLOBALS['wlo_themenseiten_content'] = $themenseiten_contentArray;

$noOerCount = count($themenseiten_contentArray) - $oerCount;
// The number of elements in sub collections of this collection.
$sumSubCollectionsElements = $newestContent->pagination->total - count($themenseiten_contentArray);

// content for diagram
$url = WLO_REPO . 'rest/search/v1/queries/local/mds_oeh/ngsearch/facets';
$url = WLO_REPO . 'rest/search/v1/queries/local/mds_oeh/ngsearch/?maxItems=0';
//$pageTitle = 'Grammatik';
$body = '{
          "facets": [
            "ccm:oeh_lrt"
          ],
          "facetMinCount": 1,
          "facetLimit": 50,
          "criteria": [
            {
              "property": "ngsearchword",
              "values": [
                "' . get_the_title($postID) . '"
              ]
            }]
        }';

$searchContent = callWloRestApi($url, 'POST', $body);
$searchTotal = $searchContent->pagination->total;
$searchAdditional = $searchTotal - $newestContent->pagination->total;
$searchVocabs = array();
if (!empty($searchContent->facets[0]->values)) {
    $searchVocabs = $searchContent->facets[0]->values;
}
$GLOBALS['wlo_themenseiten_searchVocabs'] = $searchVocabs;
$GLOBALS['wlo_themenseiten_searchTotal'] = $searchTotal;


$accordionID = uniqid();
$sliderId = uniqid('slider-');
$slidesToShow = 4;
$slidesToScroll = 4;

while (have_posts()) : the_post(); ?>

    <div class="portal">

        <div class="fachportal-header-bar" <?php if (is_admin_bar_showing()) {
                                                echo 'style="top:23px"';
                                            } ?>>
            <div class="fachportal-header-bar-wrapper">
                <div class="portal-breadcrumbs">

                    <ul class="portal-breadcrumbs-list">
                        <?php
                        foreach ($breadcrumbs as $node) {
                            echo "<li class='portal-breadcrumbs-list-item'><a href='" . wlo_convert_dev_url($node[1]) . "'>" . $node[0] . "</a><span class='material-icons'>chevron_right</span></li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="wlo-tile-team <?php echo $portalID; ?>">
                    <p>Redaktion <?php echo $portalTitle; ?>:</p>
                    <?php foreach ($author_ids as $author_id) {
                        echo $author_id['user_avatar'];
                    } ?>
                    <img class="wlo-team-bookmark" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/QS-Faehnchen.svg">
                </div>

            </div>
        </div>

        <div class="portal-wrapper-header">
            <div class="fachportal-header-block" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">

                <div class="fachportal-header-wrapper themenseite-header-wrapper">

                    <div class="description">

                        <div class="description-content">
                            <a class="portal-page" href="<?php echo wlo_convert_dev_url($portalUrl); ?>">Themenseite im Fachportal <?php echo $portalTitle; ?></a>

                            <h1 class="title"><?php echo get_the_title($postID); ?></h1>
                            <div class="header-description"><?php echo $description; ?></div>
                        </div>

                        <div class="content-summary">
                            <p>
                                <span class="header-text">
                                    Auf dieser Seite findest du
                                    <?php echo count($themenseiten_contentArray); ?>
                                    redaktionell geprüfte Bildungsinhalte...
                                </span>
                                <!--
                                    FIXME: When we know the height of sticky / fixed elements, we
                                    can use `block: 'start'` and set the appropriate `scroll-margin`
                                    on the accordion, but currently, we don't know how far the
                                    filters will extend into the page. 
                                -->
                                <?php if (count($themenseiten_contentArray) > 0) { ?>
                                    <button class="jump-to-first-content-button" onclick="
                                    jQuery('.fachportal-content-block .content')
                                        .parent()
                                        .get(0)
                                        .scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    ">
                                        Springe zum ersten Inhalt
                                        <span class="arrow-down-icon material-icons">arrow_downward</span>
                                    </button>
                                <?php } ?>
                            </p>
                        </div>

                        <?php
                        $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $collectionID . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
                        $subCollections = callWloRestApi($url);
                        $filteredSubCollections = [];

                        if (!empty($subCollections->collections)) {
                            foreach ($subCollections->collections as $collection) {

                                // Filter hidden collections
                                if ($collection->properties->{'ccm:editorial_state'}[0] !== 'activated') {
                                    continue;
                                }

                                // Filter educationalContexts
                                if (!empty($educationalContexts)) {
                                    if (empty($collection->properties->{'ccm:educationalcontext'})) { // skip empty?
                                        //continue;
                                    } else {
                                        if (!checkPropertyMatch($collection->properties->{'ccm:educationalcontext'}, $educationalContexts, true)) {
                                            continue;
                                        }
                                    }
                                }

                                $filteredSubCollections[] = $collection;
                            }
                        }

                        $maxSubCollections = 6;
                        if (get_field('maxSubCollections')) {
                            $maxSubCollections = get_field('maxSubCollections');
                        }
                        ?>
                        <div class="collections">
                            <?php if (!empty($filteredSubCollections)) : ?>
                                <div class="sub-subjects">
                                    <div class="sub-subjects-header">
                                        <p class="header-text"><?php echo $sumSubCollectionsElements; ?> weitere geprüfte Inhalte gibt es in Unterthemen:</p>
                                    </div>
                                    <div class="sub-subjects-container">
                                        <?php foreach (array_slice($filteredSubCollections, 0, $maxSubCollections) as $collection) {
                                            $ccm_location = $collection->properties->{'cclom:location'}[0];

                                            $title = $collection->title;
                                            if (!empty($collection->properties->{'ccm:collectionshorttitle'}[0])) {
                                                $title = $collection->properties->{'ccm:collectionshorttitle'}[0];
                                            }
                                            $ccm_location = wlo_convert_dev_url($collection->properties->{'cclom:location'}[0]);
                                        ?>
                                            <div class="sub-subject">
                                                <a href="<?php echo $ccm_location; ?>">
                                                    <p><?php echo $title; ?> (<?php echo $collection->collection->childReferencesCount; ?>)</p>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                                </a>
                                            </div>
                                        <?php } ?>
                                        <?php if (count($filteredSubCollections) > $maxSubCollections) : ?>
                                            <div class="sub-subject">
                                                <a id="sub-subjects-button" href="#">
                                                    <p>mehr anzeigen</p>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg">
                                                </a>
                                            </div>

                                        <?php endif; ?>
                                    </div>
                                    <div id="hidden-sub-subjects-container" class="sub-subjects-container">
                                        <?php foreach (array_slice($filteredSubCollections, $maxSubCollections) as $collection) {
                                            $ccm_location = wlo_convert_dev_url($collection->properties->{'cclom:location'}[0]);
                                            $title = $collection->title;
                                            if (!empty($collection->properties->{'ccm:collectionshorttitle'}[0])) {
                                                $title = $collection->properties->{'ccm:collectionshorttitle'}[0];
                                            }
                                        ?>
                                            <div class="sub-subject">
                                                <a href="<?php echo $ccm_location; ?>">
                                                    <p><?php echo $title; ?></p>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="chip-link chip-link-highlight">
                            <a href="<?php echo wlo_convert_dev_url($portalUrl); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/categories-white.svg" alt="">
                                <p>Zur Themenübersicht <?php echo $portalTitle; ?></p>
                            </a>
                        </div>

                        <div class="go-to-search-container">
                            <p class="header-text">
                                Die Suche hat noch <?php echo $searchAdditional; ?> maschinell geprüfte
                                <?php echo ($searchTotal != 1 ? 'Inhalte' : 'Inhalt') ?>:
                            </p>
                            <div class="chip-link chip-link-highlight">
                                <a href="<?php echo WLO_SEARCH; ?>de/search?q=<?php echo get_the_title($postID); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot-white.svg" alt="">
                                    <p>Zur WLO-Suchmaschine</p>
                                </a>
                            </div>
                        </div>

                    </div>


                    <div class="content-stats">
                        <div class="header" style="color: <?php echo $fontColor ?> !important;">
                            Inhalte
                        </div>

                        <div class="diagram">
                            <div class="diagram-content">
                                <canvas width="80%" height="80%" id="contentChart"></canvas>
                            </div>
                        </div>

                        <div class="diagram-legend">
                            <div class="diagram-legend-entry Redaktionell" style="color: <?php echo $fontColor ?> !important;">
                                <div class="diagram-legend-color"></div> Redaktionell geprüfte Inhalte
                            </div>
                            <div class="diagram-legend-entry Maschinell" style="color: <?php echo $fontColor ?> !important;">
                                <div class="diagram-legend-color"></div> Maschinell erschlossene Inhalte in der Suche
                            </div>


                            <?php if ($searchTotal == 1) { ?>
                                <a class="diagram-legend-entry" href="<?php echo WLO_SEARCH; ?>de/search?q=<?php echo get_the_title($postID); ?>" target="_blank">
                                    <div class="diagram-legend-color search-link">
                                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot.svg" alt="Icon: Roboter">
                                    </div>
                                    1 weiteres Ergebnis in unserer Suchmaschine
                                </a>
                            <?php } else if ($searchTotal > 1) { ?>
                                <a class="diagram-legend-entry" href="<?php echo WLO_SEARCH; ?>de/search?q=<?php echo get_the_title($postID); ?>" target="_blank">
                                    <div class="diagram-legend-color search-link">
                                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot-white.svg" alt="Icon: Roboter">
                                    </div>
                                    <?php echo $searchTotal; ?> weitere Ergebnisse in unserer Suchmaschine
                                </a>
                            <?php } else { ?>

                            <?php } ?>


                        </div>


                    </div>


                </div>



            </div>
        </div>

        <div class="fachportal-filterbar" <?php if (is_admin_bar_showing()) {
                                                echo 'style="top:80px"';
                                            } ?>>

            <div class="fachportal-filterbar-content">

                <div>

                    <div class="fachportal-filterbar-dropdowns">
                        <select name="educationalcontext" id="educationalcontext" onchange="filterContentTiles(this, 'educationalcontext', this.value)">
                            <option value="label" selected disabled>Bildungsstufe</option>
                            <option disabled>──────────</option>
                            <?php foreach ($educationalcontextArray as $key => $value) { ?>
                                <option value="<?php echo preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key)); ?>">
                                    <?php echo $key . ' (' . $value . ')'; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <select name="enduserrole" id="enduserrole" onchange="filterContentTiles(this, 'enduserrole', this.value)">
                            <option value="label" selected disabled>Zielgruppe</option>
                            <option disabled>──────────</option>
                            <?php foreach ($enduserroleArray as $key => $value) { ?>
                                <option value="<?php echo preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key)); ?>">
                                    <?php echo $key . ' (' . $value . ')'; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <select name="oer" id="oer" onchange="filterContentTiles(this, 'oer', this.value)">
                            <option value="label" selected disabled>OER</option>
                            <option disabled>──────────</option>
                            <option value="oer">
                                OER (<?php echo $oerCount; ?>)
                            </option>
                            <option value="no-oer">
                                Kein OER (<?php echo $noOerCount; ?>)
                            </option>
                        </select>
                    </div>



                    <?php foreach ($educationalcontextArray as $key => $value) {
                        $id = preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key));
                    ?>
                        <button id="filter-tag-<?php echo $id; ?>" onclick="filterContentTiles(this, 'educationalcontext', '<?php echo $id; ?>')">
                            <div class="fachportal-filterbar-tag">
                                <?php echo $key; ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/close.svg" alt="">
                            </div>
                        </button>
                    <?php } ?>

                    <?php foreach ($enduserroleArray as $key => $value) {
                        $id = preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key));
                    ?>
                        <button id="filter-tag-<?php echo $id; ?>" onclick="filterContentTiles(this, 'enduserrole', '<?php echo $id; ?>')">
                            <div class="fachportal-filterbar-tag">
                                <?php echo $key; ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/close.svg" alt="">
                            </div>
                        </button>
                    <?php } ?>


                    <button id="filter-tag-oer" onclick="filterContentTiles(this, 'oer', 'oer')">
                        <div class="fachportal-filterbar-tag">
                            OER
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/close.svg" alt="">
                        </div>
                    </button>

                    <button id="filter-tag-no-oer" onclick="filterContentTiles(this, 'oer', 'no-oer')">
                        <div class="fachportal-filterbar-tag">
                            Kein OER
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/close.svg" alt="">
                        </div>
                    </button>
                </div>
            </div>

        </div>

        <div class="portal-wrapper portal-wrapper-themenseite">
            <div class="portal-wrapper-left">
                <div class="fachportal-header-accordion">

                    <div class="wlo-accordion-wrapper" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
                        <button class="wlo-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
                            <h2>Die neusten geprüften Inhalte für dich!</h2>
                            <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg" alt="Inhalte ein odder ausklappen">
                        </button>

                        <div class="wlo-accordion-content" id="<?php echo $sliderId; ?>">

                            <?php
                            if (!empty($contentArray)) {
                                foreach (array_slice($contentArray, 0, get_field('content_count')) as $content) { ?>
                                    <div class="widget-content<?php if (!empty($content['resourcetype'])) {
                                                                    foreach ($content['resourcetype'] as $type) {
                                                                        echo $type . ' ';
                                                                    }
                                                                } ?>">

                                        <button onclick="showContentPopup('<?php echo $content['id']; ?>')">

                                            <?php if (!empty($content['image_url'])) { ?>
                                                <img class="main-image" src="<?php echo $content['image_url']; ?>" alt="Cover: <?php echo $content['title']; ?>">
                                            <?php } ?>
                                            <div class="content-info">
                                                <div class="content-header">
                                                    <?php if ($content['source']) { ?>
                                                        <p class="content-source"><?php echo $content['source']; ?></p>
                                                    <?php } ?>
                                                    <img class="badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge_green.svg" alt="Auszeichnung: geprüfter Inhalt">
                                                    <?php if ($content['oer']) { ?>
                                                        <div class="badge ">OER</div>
                                                    <?php } ?>
                                                </div>
                                                <div class="content-title"><?php echo $content['title']; ?></div>
                                                <p class="content-description"><?php echo $content['description'] ?></p>
                                                <div class="content-meta">
                                                    <?php if (!empty($content['resourcetype'])) {
                                                        echo '<img src="' . get_template_directory_uri() . '/src/assets/img/img_icon.svg"  alt="Materialart">';
                                                        echo '<p>';
                                                        $i = 0;
                                                        foreach ($content['resourcetype'] as $type) {
                                                            if (++$i === count($content['resourcetype'])) {
                                                                echo $type;
                                                            } else {
                                                                echo $type . ', ';
                                                            }
                                                        }
                                                        echo '</p>';
                                                    } ?>
                                                </div>
                                                <div class="content-meta">
                                                    <?php if (!empty($content['subjects'])) {
                                                        echo '<img src="' . get_template_directory_uri() . '/src/assets/img/subject_icon.svg"  alt="Fächer">';
                                                        echo '<p>';
                                                        $i = 0;
                                                        foreach ($content['subjects'] as $subject) {
                                                            if (++$i === count($content['subjects'])) {
                                                                echo $subject;
                                                            } else {
                                                                echo $subject . ', ';
                                                            }
                                                        }
                                                        echo '</p>';
                                                    } ?>
                                                </div>
                                                <div class="content-meta">
                                                    <?php if (!empty($content['educationalcontext'])) {
                                                        echo '<img src="' . get_template_directory_uri() . '/src/assets/img/class_icon.svg"  alt="Bildungsebene">';
                                                        echo '<p>';
                                                        $i = 0;
                                                        foreach ($content['educationalcontext'] as $subject) {
                                                            if (++$i === count($content['educationalcontext'])) {
                                                                echo $subject;
                                                            } else {
                                                                echo $subject . ', ';
                                                            }
                                                        }
                                                        echo '</p>';
                                                    } ?>
                                                </div>

                                                <a class="content-button" href="<?php echo $content['content_url']; ?>" target="_blank" aria-label="Zum-Inhalt: <?php echo $content['title']; ?>">Zum Inhalt</a>

                                            </div>


                                    </div>
                            <?php }
                            } ?>
                        </div>

                        <div class="wlo-accordion-bottom"></div>

                    </div>
                </div>

                <?php the_content(); ?>

            </div>
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
        </div>

    </div>

    <script>
        function showContentPopup(nodeID) {
            document.getElementsByTagName("oeh-details-embedded")[0].setAttribute("node-id", nodeID);
            jQuery(".no-content-popup").hide();
            jQuery(".detail-view-popup").css('display', 'flex');
            jQuery(".portal-wrapper-right").show('slow');
        }

        jQuery(".fachportal-content-popup").click(function(e) {
            // Do something
            e.stopPropagation();
        });

        jQuery(".fachportal-content-popup").click(function() {
            //jQuery(".portal-wrapper-right").hide();
        });

        function showNoContentPopup() {
            jQuery(".detail-view-popup").hide();
            jQuery(".no-content-popup").css('display', 'flex');;
            jQuery(".portal-wrapper-right").show('slow');
        }

        jQuery(".close-no-content-popup").click(function() {
            jQuery(".portal-wrapper-right").hide('slow');
            jQuery(".no-content-popup").hide('slow');
        });

        jQuery("#sub-subjects-button").click(function() {
            jQuery('#hidden-sub-subjects-container').slideToggle('medium', function() {
                if (jQuery(this).is(':visible')) {
                    jQuery(this).css('display', 'flex');
                }
            });
            jQuery('#sub-subjects-button').hide();
        });

        jQuery(function() {
            // Handler for .ready() called. Put the Slick Slider etc. init code here.
            function loadSlider() {
                if (typeof jQuery().slick === "function") {
                    console.log('Load-Slider...');
                    jQuery('#<?php echo $sliderId ?>').not('.slick-initialized').slick({
                        infinite: false,
                        slidesToShow: <?php echo $slidesToShow; ?>,
                        slidesToScroll: <?php echo $slidesToScroll; ?>,
                        arrows: true,
                        dots: true,
                        zIndex: 0,
                        responsive: [{
                                breakpoint: 1230,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 3
                                }
                            },
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

            loadSlider();

            jQuery(window).on('resize', function() {
                jQuery('#<?php echo $sliderId ?>').slick('refresh');
            });
        });

        jQuery(window).on('resize', function() {
            jQuery('#<?php echo $sliderId ?>').slick('refresh');
        });

        jQuery('#fachportal-accordion-<?php echo $accordionID; ?>').click(function() {
            jQuery(this).find("img").toggleClass("fachportal-accordion-icon-active");
            jQuery('#<?php echo $sliderId; ?>').slideToggle('slow');
            jQuery('#<?php echo $sliderId ?>').slick('refresh');
        });
    </script>

    <script>
        filterContentTiles = (() => {
            let activeFilters = [];

            function updateTiles(type) {
                if (activeFilters.length === 0) {
                    jQuery('.widget-content').show('fast');
                    jQuery('.fachportal-content-block').show('fast');
                } else {
                    jQuery('.widget-content').hide();
                    jQuery('.fachportal-content-block').show();
                    activeFilters.forEach((filter) => {
                        //jQuery('[data-educationalcontext~="' + filter + '"]').show('fast');
                        //jQuery('[data-enduserrole~="' + filter + '"]').show('fast');
                        //jQuery('[data-oer="' + filter + '"]').show('fast');

                        switch (type) {
                            case "educationalcontext":
                                jQuery('[data-educationalcontext~="' + filter + '"]').show('fast');
                                break;
                            case "enduserrole":
                                jQuery('[data-enduserrole~="' + filter + '"]').show('fast');
                                break;
                            case "oer":
                                jQuery('[data-oer="' + filter + '"]').show('fast');
                                break;
                        }

                    });

                    jQuery('.slick-track').each(function() {
                        if (jQuery(this).find('.widget-content:visible').length == 0) {
                            jQuery(this).closest('.fachportal-content-block').hide();
                        }
                    });
                }
            }

            function setActiveState(filter, isActive) {
                if (isActive) {
                    let button = jQuery('#filter-tag-' + filter);
                    jQuery('#filter-tag-' + filter).find('.fachportal-filterbar-tag').addClass('active-btn');
                } else {
                    jQuery('#filter-tag-' + filter).find('.fachportal-filterbar-tag').removeClass('active-btn');
                }
                jQuery('.fachportal-filterbar-dropdowns select').each(function() {
                    jQuery(this).val("label"); // reset select
                });
            }

            function toggleFilter(button, type, filter) {
                if (activeFilters.includes(filter)) {
                    activeFilters.splice(activeFilters.indexOf(filter), 1);
                    setActiveState(filter, false);
                } else {
                    activeFilters.push(filter);
                    setActiveState(filter, true);
                }
                updateTiles(type);
            }
            return toggleFilter;
        })()
    </script>


    </div>
<?php
endwhile;
get_footer();
