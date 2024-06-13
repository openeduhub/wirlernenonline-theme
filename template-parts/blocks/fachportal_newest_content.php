<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Neueste Inhalte</div>';
} ?>

<?php
if(!function_exists('processEduSharingNode')) {
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
            'oeh_lrt' => !empty($prop->{'ccm:oeh_lrt'}) ? $prop->{'ccm:oeh_lrt'} : [],
            'oeh_lrt_aggregated' => !empty($prop->{'ccm:oeh_lrt_aggregated'}) ? $prop->{'ccm:oeh_lrt_aggregated'} : [],
            'duration' => !empty($prop->{'cclom:duration'}[0]) ? $prop->{'cclom:duration'}[0] : false,
            'enduserrole' => !empty($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'}) ? $prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'} : [],
            'oer' => isOer($prop),

            // 'replicationsource' => !empty($prop->{'ccm:replicationsource_DISPLAYNAME'}) ? $prop->{'ccm:replicationsource_DISPLAYNAME'} : [],
            'content_url' => !empty($prop->{'ccm:wwwurl'}[0]) ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,

            // 'source' => !empty($prop->{'ccm:author_freetext'}[0]) ? $prop->{'ccm:author_freetext'}[0] : '',
            // 'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
            // 'author' => !empty($prop->{'ccm:lifecyclecontributer_author'}) ? $prop->{'ccm:lifecyclecontributer_author'} : [],
            // 'mimetype' => $reference->mimetype,
            // 'widget' =>  !empty($reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]) ? $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0] : '',
        );
    }
}
if(!function_exists('isOer')) {
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
}


$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];

$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

$rgbBackgroundColor = $GLOBALS['wlo_fachportal']['rgbBackgroundColor'];
$accordionID = uniqid();
$sliderId = uniqid('slider-');
$slidesToShow = 3;
$slidesToScroll = 3;

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

?>

<div class="wlo-accordion-wrapper" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
    <?php
    if (!empty($contentArray)) { ?>
        <button class="wlo-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
            <h2>Die neusten geprüften Inhalte für dich!</h2>
            <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg" alt="Inhalte ein odder ausklappen">
        </button>

        <div class="wlo-accordion-content" id="<?php echo $sliderId; ?>">

            <?php
            foreach (array_slice($contentArray, 0, get_field('content_count')) as $content) { ?>
                <div class="widget-content">
                    <!-- onclick="showContentPopup('<?php echo $content['id']; ?>')" -->
                    <button>

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
            <?php } ?>
        </div>
    <?php } ?>

    <div class="wlo-accordion-bottom"></div>
</div>

<?php
if(!function_exists('initSlick')) {
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
}
?>

<?php initSlick($sliderId, $slidesToShow, $slidesToScroll,  count($contentArray) + 1) ?>

<script>
    jQuery(window).on('resize', function() {
        jQuery('#<?php echo $sliderId ?>').slick('refresh');
    });

    jQuery('#fachportal-accordion-<?php echo $accordionID; ?>').click(function() {
        jQuery(this).find("img").toggleClass("fachportal-accordion-icon-active");
        jQuery('#<?php echo $sliderId; ?>').slideToggle('slow');
        jQuery('#<?php echo $sliderId ?>').slick('refresh');
    });
</script>