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

// Get page by title
$query = new WP_Query(
    array(
        'post_type'              => 'portal',
        'title'                  => $portalTitle,
        'posts_per_page'         => 1,
        'no_found_rows'          => true,
        'ignore_sticky_posts'    => true,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
    )
);
$portalID = $query->post->ID;

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

$breadcrumbs[] = [$topic . ' im Beruf', get_permalink()];

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
);

$GLOBALS['wloCareerInfo'] = array(
    'topicPostId' => $topicPostId,
);

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
                            <a class="portal-page" href="<?php echo wlo_convert_dev_url($portalUrl); ?>">Berufsseite im Fachportal <?php echo $portalTitle; ?></a>
                            <h1><?php echo $topic; ?> im Beruf</h1>
                            <div class="header-description">
                                <p>Du möchtest wissen, welche Berufe es zum Thema <?php echo $topic; ?> gibt? Du wirst überrascht sein, was du mit deinem Interesse dafür alles machen kannst.</p>
                                <p>Wir haben dir hier passende Berufsprofile zusammengestellt. Künstliche Intelligenz hat uns dabei geholfen, dir einen guten Überblick zu verschaffen. Außerdem findest du hilfreiche Tipps und Infos rund um deine ersten Schritte in die Berufswelt, z. B. zu Praktika, Bewerbung schreiben und vieles mehr.</p>
                            </div>
                        </div>

                        <div class="scroll-down-chips">
                            <button class="wlo-chip-light-blue" onclick="
                            jQuery('.job-profiles-header')
                                .get(0)
                                .scrollIntoView({ behavior: 'smooth', block: 'start' });
                            ">
                                <span>Berufsprofile</span>
                                <span class="arrow-down-icon material-icons">arrow_downward</span>
                            </button>
                            <button class="wlo-chip-light-blue" onclick="
                            jQuery('.wlo-events-map')
                                .get(0)
                                .scrollIntoView({ behavior: 'smooth', block: 'start' });
                            ">
                                <span>Veranstaltungen und Lernorte</span>
                                <span class="arrow-down-icon material-icons">arrow_downward</span>
                            </button>
                            <button class="wlo-chip-light-blue" onclick="
                            jQuery('.fachportal-content-block')
                                .get(0)
                                .scrollIntoView({ behavior: 'smooth', block: 'start' });
                            ">
                                <span>Tipps zum Start</span>
                                <span class="arrow-down-icon material-icons">arrow_downward</span>
                            </button>
                        </div>

                        <p class="more-career-pages">
                            Weitere Berufsthemen zu <?php echo $topic; ?>:
                            <span id="sub-career-pages-links">Lädt...</span>
                            <script>
                                jQuery.ajax({
                                    url: ajaxurl,
                                    data: {
                                        action: 'wloSubCareerPagesLinks',
                                        collectionId: '<?php echo $collectionID; ?>',
                                        subjectPortalId: '<?php echo $portalID; ?>',
                                    },
                                    success: (html) => {
                                        jQuery('#sub-career-pages-links').html(html);
                                    },
                                });
                            </script>
                        </p>

                        <p>
                            WirLernenOnline sammelt für dich Bildungs- und Wissensinhalte sowie Informationen aus Internetportalen, Datenbanken und anderen Quellen. Dies werden automatisch erschlossen und mit künstlicher Intelligenz so verschlagwortet, dass die Inhalte auf Seiten wie diesen erscheinen. Trotz redaktioneller Stichproben können sich da Fehler einschleichen.
                        </p>

                        <?php
                        $reportProblemUrl = add_query_arg(
                            array(
                                'pageTitle' => urlencode(get_the_title()),
                                'pageUrl' => urlencode(get_permalink()),
                            ),
                            get_site_url(path: 'problem-mit-seite-melden'),
                        );
                        ?>
                        <!-- <a class="wlo-chip-gray" href="<?php echo $reportProblemUrl; ?>" target="_blank">
                            <span>Problem melden</span>
                            <span class="material-icons">flag</span>
                        </a> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="fachportal-filterbar" <?php if (is_admin_bar_showing()) {
                                                echo 'style="top:80px"';
                                            } ?>>
            <?php
            $educationalcontextArray = ['foo' => 42]
            ?>
            <div class="fachportal-filterbar-content">
                <div class="fachportal-filterbar-header-row">
                    <p class="header-text">Filtere die Berufsprofile:</p>
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
                </div>
            </div>
        </div>

        <div class="portal-wrapper portal-wrapper-themenseite">
            <div class="portal-wrapper-left">
                <?php
                get_template_part('template-parts/career/job-profiles', args: array(
                    'topicPostId' => $topicPostId,
                    'topic' => $topic,
                    'color' => $rgbBackgroundColor,
                ));
                ?>
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
                            jQuery(".portal-wrapper-right").hide('slow');
                            jQuery(".detail-view-popup").hide('slow');
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php
endwhile;
get_footer();

?>
<script>
    function showContentPopup(nodeID) {
        document.getElementsByTagName("oeh-details-embedded")[0].setAttribute("node-id", nodeID);
        jQuery(".detail-view-popup").css('display', 'flex');
        jQuery(".portal-wrapper-right").show('slow');
    }
</script>