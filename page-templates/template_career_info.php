<?php
/*
Template Name: WLO-Berufsinformationen
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

$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID;
$response = callWloRestApi($url);


$url = WLO_REPO . 'rest/node/v1/nodes/-home-/' . $collectionID . '/parents?propertyFilter=-all-&fullPath=false';
$parents = callWloRestApi($url);
if (isset($parents->nodes)) {
    $parents = $parents->nodes;
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

/** The title of the topic page to which this info page belongs. */
$topic = end($breadcrumbs)[0];
/** The post id of the topic page to which this info page belongs. */
$topicPostId = url_to_postid(end($breadcrumbs)[1]);

$url = WLO_REPO . 'rest/search/v1/queries/local/mds_oeh/ngsearch/?maxItems=0';
$body = '{
          "criteria": [
            {
              "property": "ngsearchword",
              "values": [
                "' . $topic . '"
              ]
            }]
        }';

$searchContent = callWloRestApi($url, 'POST', $body);
$searchTotal = $searchContent->pagination->total;


if (!function_exists('helper_useLightColor')) {
    function helper_useLightColor($bgColor)
    {
        $color = ($bgColor[0] === '#') ? substr($bgColor, 1, 7) : $bgColor;
        $r = intval(substr($color, 0, 2), 16); // hexToR
        $g = intval(substr($color, 2, 4), 16); // hexToG
        $b = intval(substr($color, 4, 6), 16); // hexToB
        $value = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;
        return $value > 140;
    }
}

$backgroundColor = wloSubjectType($portalTitle)['color'];
$rgbBackgroundColor = hex2rgb($backgroundColor);
$fontColor = (!empty($backgroundColor) && helper_useLightColor($backgroundColor)) ? "#313131" : "#ffffff";

$GLOBALS['wlo_fachportal'] = array(
    'title' => $portalTitle,
    'backgroundColor' => $backgroundColor,
    'rgbBackgroundColor' => $rgbBackgroundColor
);


$accordionID = uniqid();

while (have_posts()) : the_post(); ?>
    <div class="portal career-info-page">
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
                    <p>Forschungsprototyp!</p>
                    <div class="wlo-verified-flag-container">
                        <img class="wlo-verified-flag" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/KI-Faehnchen.svg">
                    </div>
                </div>
            </div>
        </div>

        <div class="portal-wrapper-header">
            <div class="fachportal-header-block" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">
                <div class="fachportal-header-wrapper themenseite-header-wrapper">
                    <div class="description">
                        <div class="description-content">
                            <a class="portal-page" href="<?php echo wlo_convert_dev_url($portalUrl); ?>">Informationsseite im Fachportal <?php echo $portalTitle; ?></a>

                            <h1>
                                <p class="subject">
                                    <?php echo $topic; ?>
                                </p>
                                <p class="career-info-title">Wofür ist das wichtig?</p>
                            </h1>
                            <div class="header-description">
                                <?php
                                $careerAdviceId = uniqid('career-advice-');
                                ?>
                                <p class="career-advice">
                                    <span id="<?php echo $careerAdviceId; ?>" class="career-advice-textbox">
                                        Lädt...
                                    </span>
                                    <script type="text/javascript">
                                        jQuery(document).ready(function() {
                                            const data = {
                                                action: 'wloAiCareerAdvice',
                                                postId: <?php echo $topicPostId; ?>,
                                            };
                                            jQuery.post(ajaxurl, data, function(response) {
                                                const element = jQuery('#<?php echo $careerAdviceId; ?>');
                                                element.text(response.description);
                                            }).fail(() => {
                                                const element = jQuery('#<?php echo $careerAdviceId; ?>');
                                                element.addClass('failed-message-box');
                                                element.text('Fehler beim Laden der Daten.');
                                            });
                                        });
                                    </script>
                                </p>
                                <p class="career-info-description">
                                    Auf dieser Seite haben wir mit KI Links zusammengestellt.<br>
                                    Wir verraten dir auch die Prompts, damit du KI nutzen lernst...
                                </p>
                            </div>
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
                                        <h2>Unterthemen <?php echo $topic; ?></h2>
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
                                                    <p><?php echo $title; ?></p>
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
                                    <div>
                                        <a class="wlo-chip-wide-red" href="<?php echo $portalUrl ?>">
                                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/categories_white.svg">
                                            <span>zur Themenübersicht <?php echo $portalTitle ?></span>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="search-results-container">
                            <p class="search-results-notice">
                                <?php if ($searchTotal == 1) { ?>
                                    Die Suche findet 1 maschinell geprüften Inhalt:
                                <?php } else if ($searchTotal > 1) { ?>
                                    Die Suche findet <?php echo $searchTotal; ?> maschinell geprüfte Inhalte:
                                <?php } ?>
                            </p>
                            <a class="wlo-chip-wide-red" href="<?php echo WLO_SEARCH; ?>de/search?q=<?php echo $topic; ?>" target="_blank">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/robot-white.svg">
                                <span>Zur WLO-Suchmaschine</span>
                            </a>
                        </div>
                        <?php
                        $reportProblemUrl = add_query_arg(
                            array(
                                'pageTitle' => urlencode(get_the_title()),
                                'pageUrl' => urlencode(get_permalink()),
                            ),
                            get_site_url(path: 'problem-mit-seite-melden'),
                        );
                        ?>
                        <a class="wlo-chip-gray" href="<?php echo $reportProblemUrl; ?>" target="_blank">
                            <span>Problem melden</span>
                            <span class="material-icons">flag</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <div class="portal-wrapper portal-wrapper-themenseite">
            <div class="portal-wrapper-left">
                <div class="fachportal-header-accordion">

                    <div class="wlo-accordion-wrapper" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
                        <button class="wlo-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
                            <h2>Lorem ipsum</h2>
                            <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg" alt="Inhalte ein odder ausklappen">
                            <script>
                                jQuery('#fachportal-accordion-<?php echo $accordionID; ?>').click(function() {
                                    jQuery(this).find("img").toggleClass("wlo-accordion-icon-active");
                                    jQuery('#fachportal-accordion-content-<?php echo $accordionID; ?>').slideToggle();
                                });
                            </script>
                        </button>

                        <div id="fachportal-accordion-content-<?php echo $accordionID; ?>" class="wlo-accordion-content career-info-chat-gpt-container">
                            <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Necessitatibus similique quas quis in, mollitia maiores distinctio. Aperiam impedit vero nostrum eligendi! Nulla beatae odit dolore aperiam ipsa sequi? Commodi, quis.</p>
                        </div>
                        <div class="wlo-accordion-bottom"></div>
                    </div>
                </div>

                <?php the_content(); ?>
            </div>
        </div>
    </div>
    </div>
<?php
endwhile;
get_footer();
