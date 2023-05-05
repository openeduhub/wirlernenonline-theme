<?php

function wlo_add_swimlane_content($contentArray, $slidesToShow = 4, $slidesToScroll = 4, $contentInfo, $lrtID = '', $searchLrtID = '', $type = 'material')
{
    $sliderId = uniqid('slider-');
    $showSliderDots = 'true';
    if (count($contentArray) <= 4 && $slidesToShow >= 3) {
        $showSliderDots = 'false';
    }

    if (!empty($contentArray)) {
        echo '<div class="content" id="' . $sliderId . '">';
        foreach ($contentArray as $contentItem) {
            printWloCard($contentItem, $slidesToShow);
        }
        echo getAddNewContentTile($contentInfo, $lrtID,  $type);
    } else {
        echo '<div class="no-swimlane-content">';

        echo getAddNewContentTile($contentInfo, $lrtID,  $type);

        $emptySwimlaneId = uniqid('emptySwimlaneId-');

        echo '<div class="fachportal-content-block" id="' . $emptySwimlaneId . '">';
        echo '<div class="lds-ring"><div></div><div></div><div></div><div></div></div>';
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

    echo '<script type="text/javascript">  
        jQuery(function () {
            // Handler for .ready() called. Put the Slick Slider etc. init code here.
            function loadWLOSlider() {
                if (typeof jQuery().slick === "function") {
                    jQuery("#' . $sliderId . '").not(".slick-initialized").slick({
                        infinite: false,
                        slidesToShow: ' . $slidesToShow . ',
                        slidesToScroll: ' . $slidesToScroll . ',
                        arrows: true,
                        dots:  ' . $showSliderDots . ',
                        zIndex: 0,
                        responsive: [
                            {
                                breakpoint: 1230,
                                settings: {
                                    slidesToShow: ' . min($slidesToShow, 3) . ',
                                    slidesToScroll: ' . min($slidesToScroll, 3) . '
                                }
                            },
                            {
                                breakpoint: 950,
                                settings: {
                                    slidesToShow: ' . min($slidesToShow, 2) . ',
                                    slidesToScroll: ' . min($slidesToScroll, 2) . ',
                                }
                            },
                            {
                                breakpoint: 750,
                                settings: {
                                    slidesToShow: ' . min($slidesToShow, 1) . ',
                                    slidesToScroll: ' . min($slidesToScroll, 1) . ',
                                }
                            }
                        ]
                    });
                }
            }

            loadWLOSlider();

            jQuery(window).on("resize", function(){
                jQuery("#' . $sliderId . '").slick( "refresh" );
            });
        });
    </script>';
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
function printWloCard(mixed $item, int $slidesToShow): void
{
    $style = $slidesToShow == 1 ? 'margin: 12px 110px; max-width: 350px;' : '';
    $data = getWloCardDataAttributes($item);
?>
    <div class="widget-content" <?php echo $data; ?> style="<?php echo $style; ?>">
        <button onclick="showContentPopup('<?php echo $item['id']; ?>')">
            <?php if (!empty($item['image_url'])) { ?>
                <img class="main-image" src="<?php echo $item['image_url']; ?>&crop=true&maxWidth=300&maxHeight=300" alt="Cover: <?php echo $item['title']; ?>">
            <?php } ?>
            <div class="content-info">
                <div class="content-header">
                    <?php if ($item['source'] && false) { ?>
                        <p class="content-source">
                            <?php echo $item['source']; ?>
                        </p>
                    <?php } ?>
                    <img class="badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge_green.svg" alt="Auszeichnung: geprüfter Inhalt">
                    <?php if ($item['oer']) { ?>
                        <div class="badge ">OER</div>
                    <?php } ?>
                </div>
                <div class="content-title">
                    <?php echo $item['title']; ?>
                </div>
                <p class="content-description">
                    <?php echo  $item['description']; ?>
                </p>
                <div class="content-meta">
                    <?php if (!empty($item['resourcetype'])) { ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/img_icon.svg" alt="Materialart">
                        <p>
                            <?php echo implode(', ', $item['resourcetype']); ?>
                        </p>
                    <?php } ?>
                </div>
                <div class="content-meta">
                    <?php if (!empty($item['subjects'])) { ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/subject_icon.svg" alt="Fächer">
                        <p>
                            <?php echo implode(', ', $item['subjects']); ?>
                        </p>
                    <?php } ?>
                </div>
                <div class="content-meta">
                    <?php if (!empty($item['educationalcontext'])) { ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/class_icon.svg" alt="Bildungsebene">
                        <p>
                            <?php echo implode(', ', $item['educationalcontext']); ?>
                        </p>
                    <?php } ?>
                </div>
                <a class="content-button" href="<?php echo $item['content_url']; ?>" target="_blank" aria-label="Zum-Inhalt: <?php echo $item['title']; ?>">
                    Zum Inhalt
                </a>
            </div>
        </button>
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
        'oer' => isOer($prop),

        'replicationsource' => !empty($prop->{'ccm:replicationsource_DISPLAYNAME'}) ? $prop->{'ccm:replicationsource_DISPLAYNAME'} : [],
        'content_url' => !empty($prop->{'ccm:wwwurl'}[0]) ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,
        'source' => !empty($prop->{'ccm:author_freetext'}[0]) ? $prop->{'ccm:author_freetext'}[0] : '',
        // 'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
        'author' => !empty($prop->{'ccm:lifecyclecontributer_author'}) ? $prop->{'ccm:lifecyclecontributer_author'} : [],
        'enduserrole' => !empty($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'}) ? $prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'} : [],
        'mimetype' => $reference->mimetype,
        'widget' =>  !empty($reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]) ? $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0] : '',
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
