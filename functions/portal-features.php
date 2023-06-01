<?php

function wlo_add_swimlane_content($contentArray, $slidesToShow = 4, $slidesToScroll = 4, $contentInfo, $lrtID = '', $searchLrtID = '', $type = 'material')
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

    $addContentUrl = get_page_link($contentInfo['addContentPageID']) . '?collectionID=' . $contentInfo['collectionID'] . '&headline=' . $contentInfo['pageTitle'] . '&pageDiscipline=' . $contentInfo['pageDiscipline'] . '&lrtID=' . $lrtID . '&type=' . $type;
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
    <div class="wlo-card" <?php echo $data; ?>>
        <?php if (!empty($item['image_url'])) { ?>
            <div class="wlo-card-image-container">
                <img class="wlo-card-image" src="<?php echo $item['image_url']; ?>&crop=true&maxWidth=300&maxHeight=300" alt="Cover: <?php echo $item['title']; ?>">
                <div class="media-type-icon-container">
                    <!-- TODO: choose correct icon -->
                    <img class="media-type-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/media-types/video.svg">
                </div>
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
                    <img class="source-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/sources/zdf.svg">
                </div>
            <?php } ?>
        </div>
    </div>
<?php
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
        'duration' =>  !empty($prop->{'cclom:duration'}[0]) ? $prop->{'cclom:duration'}[0] : false,
        'oer' => isOer($prop),

        // 'replicationsource' => !empty($prop->{'ccm:replicationsource_DISPLAYNAME'}) ? $prop->{'ccm:replicationsource_DISPLAYNAME'} : [],
        // 'content_url' => !empty($prop->{'ccm:wwwurl'}[0]) ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,

        // 'source' => !empty($prop->{'ccm:author_freetext'}[0]) ? $prop->{'ccm:author_freetext'}[0] : '',
        // 'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
        // 'author' => !empty($prop->{'ccm:lifecyclecontributer_author'}) ? $prop->{'ccm:lifecyclecontributer_author'} : [],
        // 'enduserrole' => !empty($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'}) ? $prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'} : [],
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
 */
function getDurationString(int $seconds): string
{
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

function initSlick(string $sliderId, int $slidesToShow = 3, int $slidesToScroll = 3, int $contentCount)
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
