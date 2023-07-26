<?php

function wlo_add_swimlane_content($contentArray, $slidesToShow, $slidesToScroll, $contentInfo, $lrtID = '', $searchLrtID = '', $type = 'material')
{
    $sliderId = uniqid('slider-');

    if (!empty($contentArray)) {
        echo '<div class="content" id="' . $sliderId . '">';
        foreach ($contentArray as $contentItem) {
            printWloCard($contentItem);
        }
        echo getAddNewContentTile($contentInfo, $lrtID,  $type);
    } else {
        echo '<div class="no-swimlane-content">';

        echo getAddNewContentTile($contentInfo, $lrtID,  $type);

        $emptySwimlaneId = uniqid('emptySwimlaneId-');

        echo '<div class="fachportal-content-block" id="' . $emptySwimlaneId . '">';
        echo '<div class="lds-ring"><div></div><div></div></div>';
        echo '</div>';

        echo '<script type="text/javascript">
                        jQuery(document).ready(function($) {
                            var data = {
                                "action": "emptySwimlaneContent",
                                "collectionID": "' . $contentInfo['collectionID'] . '",
                                "pageTitle": "' . $contentInfo['pageTitle'] . '",
                                "lrtID": "' . $lrtID . '",
                                "searchLrtID": "' . htmlentities(json_encode($searchLrtID)) . '",
                            };
                            jQuery.post(ajaxurl, data, function(response) {
                                jQuery("#' . $emptySwimlaneId . '").html(response);
                            });
                        });
                    </script>';
    }
    echo '</div>';

    initSlick($sliderId, $slidesToShow, $slidesToScroll, count($contentArray) + 1);
}


/**
 * Creates HTML code for a tile that allows the user to propose new content for the place specified
 * by the function's arguments.
 */
function getAddNewContentTile($contentInfo, $lrtID,  $type)
{
    $contentTitle = 'Mitmachen!';
    switch ($type) {
        case 'material':
            $buttonText = 'Inhalte vorschlagen';
            break;
        case 'tool':
            $buttonText = 'Tool vorschlagen';
            break;
        case 'source':
            $buttonText = 'Quelle vorschlagen';
            break;
    }

    $addContentUrl = get_page_link(ADD_CONTENT_PAGE_ID)
        . '?collectionID=' . $contentInfo['collectionID']
        . '&headline=' . $contentInfo['pageTitle']
        . '&pageDiscipline=' . $contentInfo['pageDiscipline']
        . '&lrtID=' . $lrtID
        . '&type=' . $type;
    $content = '<div class="widget-content no-widget-content">';
    $content .= '<button onclick="showNoContentPopup()">';
    $content .= '<img class="main-image" src="' . get_template_directory_uri() . '/src/assets/img/mitmachen-3.png" alt="Cover: Keine Inhalte">';
    $content .= '<div class="content-info no-content-info">';
    $content .= '<div class="content-title">' . $contentTitle . '</div>';
    $content .= '<p class="content-description">Füge Inhalte zu diesem Thema hinzu...</p>';
    $content .= '<a class="content-button no-content-button" href="' . $addContentUrl . '" target="_blank">';
    $content .= '<img src="' . get_template_directory_uri() . '/src/assets/img/plus.svg" alt="Icon: Plus"> ' . $buttonText;
    $content .= '</a>';
    $content .= '</div>';
    $content .= '</button>';
    $content .= '</div>';
    return $content;
}

function wloFilterSwimlane($contentArray, $vocabs)
{
    $filtered_content = array();
    foreach ($contentArray as $key => $content) {
        array_merge(array('added' => false), $content);
        $added = false;
        foreach ($content['oeh_lrt'] as $lrt) {
            if (!empty(array_search($lrt, array_column($vocabs, 'id'))) && !$added) {
                $added = true;
                $contentArray[$key]['added'] = true;
                $filtered_content[] = $content;
            }
        }
    }
    return array("filtered_content" => $filtered_content, "contentArray" => $contentArray);
}

function wloSearchContentSum($values, $vocabs)
{
    $sum = 0;
    foreach ($values as $value) {
        if (!empty(array_search($value->value, array_column($vocabs, 'id')))) {
            $sum = $sum + $value->count;
        }
    }
    return $sum;
}

/**
 * Returns a string with `data-` attributes to include with a swimlane tile.
 */
function getWloCardDataAttributes(array $item): string
{
    $data = '';
    if (!empty($item['educationalcontext'])) {
        $data .= 'data-educationalcontext="';
        foreach ($item['educationalcontext'] as $educationalContext) {
            $data .= preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($educationalContext)) . ' ';
        }
        $data .= '"';
    }
    if (!empty($item['enduserrole'])) {
        $data .= ' data-enduserrole="';
        foreach ($item['enduserrole'] as $endUserRole) {
            $data .= preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($endUserRole)) . ' ';
        }
        $data .= '"';
    }
    if ($item['oer']) {
        $data .= ' data-oer="oer"';
    } else {
        $data .= ' data-oer="no-oer"';
    }
    return $data;
}

/**
 * Prints the HTML for a swimlane card.
 */
function printWloCard(mixed $item): void
{
    $data = getWloCardDataAttributes($item);
?>
    <!-- FIXME: slick sets this div to tabindex="0" -->
    <div class="wlo-card wlo-card-interactive" <?php echo $data; ?>>
        <?php if (!empty($item['image_url'])) { ?>
            <div class="wlo-card-image-container">
                <img class="wlo-card-image"
                    src="<?php echo $item['image_url']; ?>&crop=true&maxWidth=300&maxHeight=300"
                    alt="Cover: <?php echo $item['title']; ?>">
                <?php $mediaIcon = getMediaTypeIcon($item['oeh_lrt_aggregated']);
                if ($mediaIcon) { ?>
                    <div class="media-type-icon-container">
                        <span class="material-icons media-type-icon">
                            <?php echo $mediaIcon; ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="wlo-card-body">
            <p class="media-summary">
                <!-- 20-minütiger TV-Beitrag -->
                <?php if (!empty($item['resourcetype'][0])) { ?>
                    <?php echo $item['resourcetype'][0] ?>
                <?php } ?>
                <?php if (!empty($item['duration'])) { ?>
                    (<?php echo getDurationString($item['duration']); ?>)
                <?php } ?>
            </p>
            <!--
                Make the title the button content to be sensible towards screen readers.

                Place the heading inside a button to be able to do text fading in the heading and
                still expand the clickable area of the button to the whole card.
            -->
            <button onclick="showContentPopup('<?php echo $item['id']; ?>')" class="wlo-card-title">
                <h3>
                    <?php echo $item['title']; ?>
                </h3>
            </button>
            <p class="wlo-card-description">
                <?php echo $item['description']; ?>
            </p>
            <div class="wlo-card-meta">
                <?php if (!empty($item['resourcetype'])) { ?>
                    <p>
                        <?php echo implode(', ', $item['resourcetype']); ?>
                    </p>
                <?php } ?>
                <p>
                    <?php
                    if (!empty($item['subjects'])) {
                        echo implode(', ', $item['subjects']);
                    }
                    if (!empty($item['subjects']) && !empty($item['educationalcontext'])) {
                        echo ': ';
                    }
                    if (!empty($item['educationalcontext'])) {
                        echo implode(', ', $item['educationalcontext']);
                    }
                    ?>
                </p>
            </div>
            <?php if (!empty($item['replicationsource']) || !empty($item['publisher'])) { ?>
                <div class="wlo-card-source-container">
                    <?php
                    if (!empty($item['publisher'])) { ?>
                        <p>
                            <?php echo $item['publisher']; ?>
                        </p>
                    <?php } ?>
                    <img class="source-icon" src="<?php echo getSourceLogo($item['publisher']); ?>" alt="">
                </div>
            <?php } ?>
        </div>
    </div>
<?php
}

const MEDIA_TYPE_ICONS = array(
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/38774279-af36-4ec2-8e70-811d5a51a6a1' => 'videocam',
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/c8e52242-361b-4a2a-b95d-25e516b28b45' => 'note_alt',
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/f1341358-3f91-449b-b6eb-f58636f756a0' => 'build',
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/2e678af3-1026-4171-b88e-3b3a915d1673' => 'source',
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/0b2d7dec-8eb1-4a28-9cf2-4f3a4f5a511b' => 'history_edu',
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/55761ec6-0cd4-4677-86ee-6f395934dae7' => 'web',
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/37a3ad9c-727f-4b74-bbab-27d59015c695' => 'hardware',
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/11f438d7-cb11-49c2-8e67-2dd7df677092' => 'tips_and_updates',
    'http://w3id.org/openeduhub/vocabs/new_lrt_aggregated/39197d6f-dfb1-4e82-92e5-79f906e9d2a9' => 'music_note'
);

/**
 * Gets the material icon for the given array of values oeh-lrt-aggregated vocab values.
 * 
 * Returns the icon of the first value, or false, if no icon was defined for that value.
 */
function getMediaTypeIcon(array $oehLrtAggregated): string | false
{
    if (!empty($oehLrtAggregated[0]) && array_key_exists($oehLrtAggregated[0], MEDIA_TYPE_ICONS)) {
        return MEDIA_TYPE_ICONS[$oehLrtAggregated[0]];
    } else {
        return false;
    }
}

// Always use lower case and dash (-) as separator for keys
const SOURCE_LOGOS = array(
    'agentur-für-arbeit' => 'agentur-fuer-arbeit.svg',
    'bpb' => 'BPB.png',
    'bundeszentrale-für-politische-bildung' => 'BPB.png',
    'br' => 'BR.jpg',
    'bayerischer-rundfunk' => 'BR.jpg',
    'digital-learning-lab' => 'Digital Learning Lab.png',
    'geogebra' => 'Geogebra.png',
    'klexikon' => 'Klexikon.png',
    'miniklexikon' => 'Klexikon.png',
    'learning-apps' => 'Learning Apps.png',
    'leifi-physik' => 'LEIFI Physik.png',
    'niedersachsen' => 'Niedersachsen.png',
    'niedersachsen-abituraufgaben' => 'Niedersachsen.png',
    'planet-schule' => 'Planet Schule.jpg',
    'planet-wissen' => 'Planetwissen.jpg',
    'planetwissen' => 'Planetwissen.jpg',
    'rpi-virtuell' => 'RPI Virtuell.png',
    'schulcampus-rlp' => 'Schulcampus RLP.png',
    'segu' => 'segu.jpg',
    'serlo' => 'serlo.png',
    'sodis' => 'Sodis.png',
    'tutory' => 'Tutory.png',
    'umwelt-im-unterricht' => 'Umwelt im Unterricht.png',
    'wlo' => 'WLO.png',
    'wirlernenonline' => 'WLO.png',
    'youtube' => 'Youtube.png',
    'zdf' => 'zdf.svg',
    'zum' => 'ZUM.png',
    'zum-unterrichten' => 'ZUM.png',
    'zum-apps' => 'ZUM.png',
    'zum-pad' => 'ZUM.png',
    'zum-projekte' => 'ZUM.png',
    'zum-grundschule' => 'ZUM.png',
    'zum-deutsch-lernen' => 'ZUM.png',
);

function getSourceLogo(string $sourceName): string | false
{
    $key = strtolower($sourceName);
    $key = str_replace([' ', '_'], '-', $key);
    if (array_key_exists($key, SOURCE_LOGOS)) {
        return get_template_directory_uri()
            . '/src/assets/img/sources/'
            . SOURCE_LOGOS[$key];
    } else {
        return false;
    }
}

function processEduSharingNode(mixed $reference): array
{
    // $content_url = $reference->content->url;
    // $content_url = str_replace('https://redaktion.openeduhub.net/edu-sharing/', 'https://materialkiste.kita.bayern/edu-sharing/', $content_url);
    $prop = $reference->properties;
    return array(
        'id' => $reference->ref->id,
        'title' => !empty($prop->{'cclom:title'}[0]) ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0],
        'description' => !empty($prop->{'cclom:general_description'}[0]) ? $prop->{'cclom:general_description'}[0] : $reference->ref->id,
        // 'description' => !empty($prop->{'cclom:general_description'}) ? (implode("\n", $prop->{'cclom:general_description'})) : '',
        'image_url' => $reference->preview->url,
        'resourcetype' => !empty($prop->{'ccm:oeh_lrt_aggregated_DISPLAYNAME'}) ? $prop->{'ccm:oeh_lrt_aggregated_DISPLAYNAME'} : [],
        // 'resourcetype' => !empty($prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'}) ? $prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'} : [],
        // 'resourcetype' => !empty($prop->{'ccm:oeh_lrt_DISPLAYNAME'}) ? $prop->{'ccm:oeh_lrt_DISPLAYNAME'} : [],
        'subjects' => !empty($prop->{'ccm:taxonid_DISPLAYNAME'}) ? $prop->{'ccm:taxonid_DISPLAYNAME'} : [],
        'educationalcontext' => !empty($prop->{'ccm:educationalcontext_DISPLAYNAME'}) ? $prop->{'ccm:educationalcontext_DISPLAYNAME'} : [],
        'publisher' => !empty($prop->{'ccm:oeh_publisher_combined'}[0]) ? $prop->{'ccm:oeh_publisher_combined'}[0] : false,
        'oeh_lrt' =>  !empty($prop->{'ccm:oeh_lrt'}) ? $prop->{'ccm:oeh_lrt'} : [],
        'oeh_lrt_aggregated' => !empty($prop->{'ccm:oeh_lrt_aggregated'}) ? $prop->{'ccm:oeh_lrt_aggregated'} : [],
        'duration' =>  !empty($prop->{'cclom:duration'}[0]) ? $prop->{'cclom:duration'}[0] : false,
        'enduserrole' => !empty($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'}) ? $prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'} : [],
        'oer' => isOer($prop),

        // 'replicationsource' => !empty($prop->{'ccm:replicationsource_DISPLAYNAME'}) ? $prop->{'ccm:replicationsource_DISPLAYNAME'} : [],
        // 'content_url' => !empty($prop->{'ccm:wwwurl'}[0]) ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,

        // 'source' => !empty($prop->{'ccm:author_freetext'}[0]) ? $prop->{'ccm:author_freetext'}[0] : '',
        // 'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
        // 'author' => !empty($prop->{'ccm:lifecyclecontributer_author'}) ? $prop->{'ccm:lifecyclecontributer_author'} : [],
        // 'mimetype' => $reference->mimetype,
        // 'widget' =>  !empty($reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]) ? $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0] : '',
    );
}

function isOer(mixed $prop): bool
{
    $oerLicenses = array('CC_0', 'CC_BY', 'CC_BY_SA', 'PDM');
    $nodeLicense = !empty($prop->{'ccm:commonlicense_key'}[0]) ? $prop->{'ccm:commonlicense_key'}[0] : '';
    $isOER = false;
    foreach ($oerLicenses as $license) {
        if ($nodeLicense == $license) {
            $isOER = true;
        }
    }
    return $isOER;
}

/**
 * Returns a human-readable string that represents a duration, reducing precision for large values.
 * 
 * @param int|string $duration given in seconds or ISO 8601 duration format.
 */
function getDurationString(int | string $duration): string
{
    if (is_numeric($duration)) {
        $seconds = $duration;
    } else {
        $seconds = getDurationSeconds($duration);
    }
    if ($seconds < 60) {
        return $seconds . ' Sekunden';
    } else if ($seconds < 180 * 60) {
        $minutes = intdiv($seconds, 60);
        if ($minutes == 1) {
            return '1 Minute';
        } else {
            return $minutes . ' Minuten';
        }
    } else {
        $hours = intdiv($seconds, 3600);
        return $hours . ' Stunden';
    }
}

/**
 * Returns the number of seconds from an ISO-8601-formatted duration string, e.g., 'PT2H3M4S'.
 */
function getDurationSeconds(string $duration): int
{
    $a = new \DateTime();
    $b = new \DateTime();
    $delta = new \DateInterval($duration);
    $b->add($delta);
    return $b->getTimestamp() - $a->getTimestamp();
}

function initSlick(string $sliderId, int $slidesToShow, int $slidesToScroll, int $contentCount)
{
?>
    <script type="text/javascript">
        jQuery(function() {
            function loadWLOSlider() {
                if (typeof jQuery().slick === "function") {
                    jQuery('#<?php echo $sliderId ?>').not('.slick-initialized').slick({
                        infinite: false,
                        slidesToShow: <?php echo $slidesToShow; ?>,
                        slidesToScroll: <?php echo $slidesToScroll; ?>,
                        arrows: true,
                        dots: <?php echo ($contentCount > $slidesToShow) ? 'true' : 'false'; ?>,
                        zIndex: 0,
                        responsive: [{
                                breakpoint: 1230,
                                settings: {
                                    slidesToShow: <?php echo min($slidesToShow, 3); ?>,
                                    slidesToScroll: <?php echo min($slidesToScroll, 3); ?>,
                                    dots: <?php echo ($contentCount > min($slidesToShow, 3)) ? 'true' : 'false'; ?>,
                                }
                            },
                            {
                                breakpoint: 950,
                                settings: {
                                    slidesToShow: <?php echo min($slidesToShow, 2); ?>,
                                    slidesToScroll: <?php echo min($slidesToScroll, 2); ?>,
                                    dots: <?php echo ($contentCount > min($slidesToShow, 2)) ? 'true' : 'false'; ?>,
                                }
                            },
                            {
                                breakpoint: 750,
                                settings: {
                                    slidesToShow: <?php echo min($slidesToShow, 1); ?>,
                                    slidesToScroll: <?php echo min($slidesToScroll, 1); ?>,
                                    dots: <?php echo ($contentCount > min($slidesToShow, 1)) ? 'true' : 'false'; ?>,
                                }
                            },
                        ]
                    });
                }
            }

            loadWLOSlider();

            jQuery(window).on('resize', function() {
                jQuery('#<?php echo $sliderId ?>').slick('refresh');
            });
        });
    </script>
<?php
}

/**
 * Retrieves relevant sub collections from the edu-sharing API.
 */
function getSubCollections(string $collectionId): array
{
    $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $collectionId . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
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

    return $filteredSubCollections;
}

/**
 * Returns the short title of a collection if defined, the normal title otherwise.
 */
function getCollectionShortTitle(mixed $collection): string
{
    if (!empty($collection->properties->{'ccm:collectionshorttitle'}[0])) {
        return $collection->properties->{'ccm:collectionshorttitle'}[0];
    } else {
        return $collection->title;
    }
}

/**
 * Creates or updates matching career pages for the given topic page as needed.
 *
 * Creates a career page if it doesn't exist or updates it, if the career-page template was updated.
 * 
 * @param mixed $post the post object of the corresponding topic page
 * @param int $subjectPortalId the post ID of the root subject portal to which the topic belongs
 * @return int the post ID of the career page
 */
function createOrUpdateCareerPage(mixed $post, int $subjectPortalId): int
{
    $educational_filter_values = get_educational_filter_values($post->ID);
    $collectionUrl = $educational_filter_values["collectionUrl"];

    $slug = $post->post_name . '-berufsinfo';
    $career_page = get_page_by_path($slug, OBJECT, 'portal');
    $updatedCareerPage = false;
    if (empty($career_page)) {
        $postParams = array(
            'post_author' => 'admin',
            'post_content_filtered' => '',
            'post_title' => 'Berufsinformationen zu ' . $post->post_title,
            'post_name' => $slug,
            'post_excerpt' => '',
            'post_status' => 'publish',
            'post_type' => 'portal',
            'comment_status' => '',
            'ping_status' => '',
            'post_password' => '',
            'to_ping' => '',
            'pinged' => '',
            'post_parent' => 0,
            'menu_order' => 0,
            'guid' => '',
            'import_id' => 0,
            'context' => '',
            'page_template'  => 'page-templates/template_career_info.php'
        );
        $career_post_id = wp_insert_post($postParams, true);
        update_field('template-date', get_post_modified_time(post: CAREER_PAGE_TEMPLATE_ID), $career_post_id);
        update_field('collection_url', $collectionUrl, $career_post_id);
        update_field('collection_level', get_field('collection_level', $post->ID), $career_post_id);
        update_field('discipline', get_field('discipline', $post->ID), $career_post_id);
        update_field('topic', get_field('topic', $post->ID), $career_post_id);
        error_log("Created new career page: " . $career_post_id);
        $updatedCareerPage = true;
    } else {
        $career_post_id = $career_page->ID;
        if (get_field('template-date', $career_post_id) != get_post_modified_time(post: CAREER_PAGE_TEMPLATE_ID)) {
            update_field('template-date', get_post_modified_time(post: CAREER_PAGE_TEMPLATE_ID), $career_post_id);
            error_log("Updated career page: " . $career_post_id);
            $updatedCareerPage = true;
        }
    }

    // Populate the career page's content.
    if ($updatedCareerPage) {
        $careerPageContent = get_post_field('post_content', CAREER_PAGE_TEMPLATE_ID);

        // Append the editors' information and participation invitation from the subject page to the
        // career page.
        $portalContent = get_post_field('post_content', $subjectPortalId);
        foreach (parse_blocks($portalContent) as &$block) {
            $innerBlock = $block;
            while (!empty($innerBlock['innerBlocks'])) {
                $innerBlock = $innerBlock['innerBlocks'][0];
            }
            if (
                $innerBlock['blockName'] == 'acf/fachportal-team-block'
            ) {
                $editorsBlock = $block;
                continue;
            }
        }
        unset($block);
        if (!empty($editorsBlock)) {
            $careerPageContent .= serialize_block($editorsBlock);
        }

        $postParams = array(
            'ID' => $career_post_id,
            'post_content' => $careerPageContent,
        );
        wp_update_post($postParams);
    }

    return $career_post_id;
}

/**
 * Returns ancestor nodes up to and including the given edu-sharing collection.
 * 
 * For "Themen-" and "Fachportale", this includes the root collection "Portale".
 * 
 * @return array nodes, starting with the root node
 */
function getCollectionAncestors($collectionId)
{
    $url = WLO_REPO
        . 'rest/node/v1/nodes/-home-/'
        . $collectionId
        . '/parents?propertyFilter=-all-&fullPath=false';
    $response = callWloRestApi($url);
    if (isset($response->nodes)) {
        return array_reverse($response->nodes);
    } else {
        return false;
    }
}

/**
 * Returns an array of pairs of labels and links to be displayed as breadcrumbs.
 * 
 * @param array $collectionAncestors as returned by `getCollectionAncestors`
 * @return array
 */
function getBreadcrumbs($collectionAncestors)
{
    $breadcrumbs = array();
    if (!empty($collectionAncestors)) {
        foreach ($collectionAncestors as $node) {
            if (is_object($node)) {
                if ($node->title == 'Portale') {
                    $breadcrumbs[] = ['Fachportale', get_page_link(55115)];
                } else {
                    $title = $node->title;
                    if (!empty($node->properties->{'ccm:collectionshorttitle'}[0])) {
                        $title = $node->properties->{'ccm:collectionshorttitle'}[0];
                    }
                    $breadcrumbs[] = [
                        $title,
                        wlo_convert_dev_url($node->properties->{'cclom:location'}[0]),
                    ];
                }
            }
        }
    }
    return $breadcrumbs;
}

/**
 * Returns the titles of ancestor collections up to and including the given collection, skipping the
 * root collection "Portale".
 */
function getPortalPath(string $collectionId): array | false
{
    $ancestors = getCollectionAncestors($collectionId);
    if (empty($ancestors)) {
        return false;
    }
    // Remove root "Portale" node.
    $ancestors = array_slice($ancestors, 1);
    $titles = array_map(fn ($node) => $node->title, $ancestors);
    return $titles;
}
