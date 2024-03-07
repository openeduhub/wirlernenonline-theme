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
$parents = getCollectionAncestors($collectionID);
$portal = $parents[1];
$breadcrumbs = getBreadcrumbs($parents);

$portalTitle = '';
if (isset($portal->title)) {
    $portalTitle = $portal->title;
}
$portalUrl = '#';
if (!empty($portal->properties->{'cclom:location'}[0])) {
    $portalUrl = $portal->properties->{'cclom:location'}[0];
}
$pageDiscipline = get_field('discipline', $postID)[0]['label'];

$portalID = wlo_getPortalPostId($portalTitle);

$career_post_id = createOrUpdateCareerPage($post, $portalID);

// Get authors field from root subject page
$authors = get_field('authors', $portalID);
update_field('authors', $authors, $postID);

$author_ids = get_field('authors', $postID);
if (empty($author_ids)) {
    $author_ids = array();
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
$addContentUrl = get_page_link(ADD_CONTENT_PAGE_ID) . '?collectionID=' . $collectionID . '&headline=' . get_the_title($postID) . '&pageDiscipline=' . $pageDiscipline;


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
$subCollectionsElements = callWloRestApi($url, 'POST', $body);

$contentArray = array();
if (!empty($subCollectionsElements->nodes)) {
    foreach ($subCollectionsElements->nodes as $reference) {
        $prop = $reference->properties;

        //check if deleted
        //TODO: is this still relevant?
        //if (empty($reference->originalId)) {
        //    continue;
        //}

        $title = $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0];
        foreach ($contentArray as $content) {
            if ($content['title'] == $title) {
                continue 2;
            }
        }

        $contentArray[] = processEduSharingNode($reference);
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

        $content = processEduSharingNode($reference);
        $themenseiten_contentArray[] = $content;

        if ($content['oer']) {
            $oerCount++;
        }

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
// The number of elements in sub collections of this collection that are _not_ also part of this
// "Themenseite".
$sumSubCollectionsElements = $subCollectionsElements->pagination->total - count($themenseiten_contentArray);

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
$searchVocabs = array();
if (!empty($searchContent->facets[0]->values)) {
    $searchVocabs = $searchContent->facets[0]->values;
}
$GLOBALS['wlo_themenseiten_searchVocabs'] = $searchVocabs;
$GLOBALS['wlo_themenseiten_searchTotal'] = $searchTotal;


$accordionID = uniqid();
$sliderId = uniqid('slider-');
$slidesToShow = 3;
$slidesToScroll = 3;

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
                    <div class="wlo-verified-flag-container">
                        <img class="wlo-verified-flag" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/QS-Faehnchen.svg">
                    </div>
                </div>

            </div>
        </div>

        <div class="portal-wrapper-header">
            <div class="fachportal-header-block" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">

                <div class="fachportal-header-wrapper themenseite-header-wrapper">

                    <div class="description description-full-width">

                        <div class="header-top-row">
                            <div class="description-content">
                                <a class="portal-page" href="<?php echo wlo_convert_dev_url($portalUrl); ?>">Themenseite im Fachportal <?php echo $portalTitle; ?></a>

                                <h1 class="title"><?php echo get_the_title($postID); ?></h1>
                                <div class="header-description"><?php echo $description; ?></div>
                            </div>

                            <?php
                            $careerAdviceId = uniqid('career-advice-');
                            ?>
                            <div class="career-advice-container">
                                <img class="lightbulb-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/lightbulb.svg" alt="">
                                <h2 class="section-title">Wofür ist das wichtig?</h2>

                                <p class="career-advice">
                                    <span id="<?php echo $careerAdviceId; ?>" class="career-advice-textbox">
                                        Lädt...
                                    </span>
                                    <script type="text/javascript">
                                        jQuery(document).ready(function() {
                                            const data = {
                                                action: 'wloAiCareerAdvice',
                                                collectionId: '<?php echo $collectionID; ?>',
                                            };
                                            jQuery.post(ajaxurl, data, function(response) {
                                                const element = jQuery('#<?php echo $careerAdviceId; ?>');
                                                element.text(response.description);
                                                // console.log('response', response);
                                            }).fail(() => {
                                                const element = jQuery('#<?php echo $careerAdviceId; ?>');
                                                element.addClass('failed-message-box');
                                                element.text('Fehler beim Laden der Daten.');
                                            });
                                        });
                                    </script>
                                </p>
                                <div class="links-container">
                                    <a href='<?php echo get_permalink($career_post_id); ?>'>mehr erfahren</a>
                                </div>
                                <p class="ai-generated-notice">
                                    <img class="robot-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot-white.svg" alt="">
                                    generiert mit ChatGPT
                                </p>
                            </div>
                        </div>

                        <div class="content-summary">
                            <p>
                                <span class="header-text">
                                    Auf dieser Seite findest du
                                    <?php echo count($themenseiten_contentArray); ?>
                                    redaktionell geprüfte Bildungsinhalte...
                                </span>
                                <?php if (count($themenseiten_contentArray) > 0) { ?>
                                    <button class="jump-to-first-content-button" onclick="
                                    jQuery('.fachportal-content-block .content')
                                        .parent()
                                        .get(0)
                                        .scrollIntoView({ behavior: 'smooth', block: 'start' });
                                    ">
                                        Springe zum ersten Inhalt
                                        <span class="arrow-down-icon material-icons">arrow_downward</span>
                                    </button>
                                <?php } ?>
                            </p>
                        </div>

                        <?php
                        $subCollections = getSubCollections($collectionID);
                        $maxSubCollections = 6;
                        if (get_field('maxSubCollections')) {
                            $maxSubCollections = get_field('maxSubCollections');
                        }
                        ?>
                        <div class="collections">
                            <?php if (!empty($subCollections)) : ?>
                                <div class="sub-subjects">
                                    <div class="sub-subjects-header">
                                        <p class="header-text"><?php echo $sumSubCollectionsElements; ?> weitere geprüfte Inhalte gibt es in Unterthemen:</p>
                                    </div>
                                    <div class="sub-subjects-container">
                                        <?php foreach (array_slice($subCollections, 0, $maxSubCollections) as $collection) {
                                            $ccm_location = $collection->properties->{'cclom:location'}[0];
                                            $title = getCollectionShortTitle($collection);
                                            $ccm_location = wlo_convert_dev_url($collection->properties->{'cclom:location'}[0]);
                                        ?>
                                            <div class="sub-subject">
                                                <a href="<?php echo $ccm_location; ?>">
                                                    <p><?php echo $title; ?> (<?php echo $collection->collection->childReferencesCount; ?>)</p>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                                </a>
                                            </div>
                                        <?php } ?>
                                        <?php if (count($subCollections) > $maxSubCollections) : ?>
                                            <div class="sub-subject">
                                                <a id="sub-subjects-button" href="#">
                                                    <p>mehr anzeigen</p>
                                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg">
                                                </a>
                                            </div>

                                        <?php endif; ?>
                                    </div>
                                    <div id="hidden-sub-subjects-container" class="sub-subjects-container">
                                        <?php foreach (array_slice($subCollections, $maxSubCollections) as $collection) {
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
                                In der Suche findest du <?php echo $searchTotal; ?> maschinell
                                <?php echo ($searchTotal != 1 ? 'geprüfte Inhalte' : 'geprüften Inhalt') ?>:
                            </p>
                            <div class="chip-link chip-link-highlight">
                                <a href="<?php echo WLO_SEARCH; ?>de/search?q=<?php echo get_the_title($postID); ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot-white.svg" alt="">
                                    <p>Zur WLO-Suchmaschine</p>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>



            </div>
        </div>

        <div class="portal-plus-filterbar-wrapper">
            <div class="fachportal-filterbar" <?php if (is_admin_bar_showing()) {
                                                echo 'style="top:80px"';
                                            } ?>>
            <div class="fachportal-filterbar-content">
                <div class="fachportal-filterbar-header-row">
                    <p class="header-text">
                        Filtere
                        <?php echo count($themenseiten_contentArray); ?>
                        qualitätsgesicherte Inhalte
                    </p>
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
                </div>

                <div class="filter-chips-container">
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
                            <?php
                            if (!empty($contentArray)) { ?>
                            <button class="wlo-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
                                <h2>Die neusten geprüften Inhalte im gesamten Themenbereich <?php echo get_the_title($postID); ?></h2>
                                <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg" alt="Inhalte ein odder ausklappen">
                            </button>

                            <div class="wlo-accordion-content" id="<?php echo $sliderId; ?>">

                            <?php
                                foreach (array_slice($contentArray, 0, get_field('content_count')) as $content) {
                                        printWloCard($content);
                                }
                            ?>
                            </div>
                            <?php } ?>

                            <div class="wlo-accordion-bottom"></div>

                        </div>
                    </div>

                    <?php the_content(); ?>

                </div>
                <?php get_template_part('template-parts/fachportal/portal-wrapper-right'); ?>

            </div>
        </div>
    </div>

    </div>

    <?php initSlick($sliderId, $slidesToShow, $slidesToScroll,  count($contentArray) + 1) ?>

    <script>
        jQuery("#sub-subjects-button").click(function() {
            jQuery('#hidden-sub-subjects-container').slideToggle('medium', function() {
                if (jQuery(this).is(':visible')) {
                    jQuery(this).css('display', 'flex');
                }
            });
            jQuery('#sub-subjects-button').hide();
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
            /**
             * @typedef FilterType
             * @type {'educationalcontext' | 'enduserrole' | 'oer'}
             */

            /**
             * @type {FilterType[]}
             */
            const FILTER_TYPES = ['educationalcontext', 'enduserrole', 'oer'];

            /**
             * @type {{[type in FilterType]: string[]}}
             */
            let activeFilters = {};
            /**
             * Maps slick-slider IDs to all of its tiles when no filters are active.
             *
             * @type {{[id: string]: HTMLDivElement[]}}
             */
            let tilesMap = null;

            /**
             * Gets all currently displayed tiles of all slick sliders.
             *
             * @returns {{[id: string]: HTMLDivElement[]}}
             */
            function getTilesMap() {
                const result = {};
                jQuery('.slick-slider').each(function() {
                    const id = this.id;
                    if (!id) {
                        console.warn('Slick slider has no ID. Cannot apply filters.', this);
                        return;
                    }
                    const tiles = jQuery(this).find('.slick-slide');
                    result[id] = tiles;
                });
                return result;
            }

            /**
             * Shows / hides content tiles depending on the contents of `activeFilters`.
             */
            function updateTiles(type) {
                if (!tilesMap) {
                    tilesMap = getTilesMap();
                }

                for (const [sliderId, tiles] of Object.entries(tilesMap)) {
                    const slickSlide = jQuery(`#${sliderId}`);
                    // Remove all slides
                    slickSlide.slick('removeSlide', null, null, true);
                    // Add matching slides again
                    const tilesToShow = tiles.filter(function() {
                        const tile = this;
                        return FILTER_TYPES.every(type => tileMatchesTypeFilter(tile, type)) ||
                            // Always include no-content tile ("Mitmachen!")
                            tile.classList.contains('no-widget-content');
                    });
                    tilesToShow.each(function() {
                        slickSlide.slick('addSlide', this);
                    })
                    updateHeadingCount(
                        slickSlide,
                        // Exclude no-content tile from count
                        tilesToShow.length - 1,
                    );
                }
            }

            /**
             * Returns true if `tile` matches active filters of `type`.
             */
            function tileMatchesTypeFilter(tile, type) {
                const typeFilters = activeFilters[type];
                if (!typeFilters?.length) {
                    // No filters defined for `type`.
                    return true;
                }
                const attributes = tile.getAttribute(`data-${type}`)?.split(' ').filter(w => !!w);
                return typeFilters.some(typeFilter => attributes?.includes(typeFilter));
            }


            /**
             * Updates the heading of a slick slide to print the correct number of tiles, if any.
             *
             * E.g. "Medien (23)"
             */
            function updateHeadingCount(slickSlide, count) {
                const heading = slickSlide.parent().find('.header h3');
                if (heading.length !== 1) {
                    // No unambiguous heading, not updating.
                    return;
                }
                const text = heading.text();
                const newText = text.replace(/\(\d+\)$/, `(${count})`);
                heading.text(newText);
            }

            /**
             * Shows / hides the filter chip for the filter value `filter` depending on `isActive`.
             */
            function setActiveState(filter, isActive) {
                if (isActive) {
                    let button = jQuery('#filter-tag-' + filter);
                    jQuery('#filter-tag-' + filter).addClass('active-btn');
                } else {
                    jQuery('#filter-tag-' + filter).removeClass('active-btn');
                }
                jQuery('.fachportal-filterbar-dropdowns select').each(function() {
                    jQuery(this).val("label"); // reset select
                });
            }

            /**
             * Activates / deactivates the filter given by `type` and `value` depending on its
             * current state.
             */
            function toggleFilter(button, type, value) {
                activeFilters[type] ??= [];
                if (activeFilters[type].includes(value)) {
                    activeFilters[type].splice(activeFilters[type].indexOf(value), 1);
                    setActiveState(value, false);
                } else {
                    activeFilters[type].push(value);
                    setActiveState(value, true);
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
