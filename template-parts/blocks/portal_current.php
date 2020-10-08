<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Inhaltesuche</div>';
}

require_once(get_template_directory().'/functions/wlo-config.php');

/* Filter Values prioritized GET/POST > Block-Setting > Portal-Setting */
/*
 * collectionUrl
 * disciplines
 * educationalContexts
 * intendedEndUserRoles
 * oer
 * objectTypes
 * learningResourceTypes
 * generalKeyword
 * oehWidgets
*/
/* ------------------------------------------------------------------- */
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

// echo '<pre style="background-color: lightgrey">' , var_dump($educational_filter_values) , '</pre>';
// echo '<script>console.log(' , json_encode($educational_filter_values) , ')</script>';

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

$block_var_topic = get_field('topic');
$topic = (!empty($block_var_topic)) ? $block_var_topic : get_post_meta($postID, 'topic', true);

$count = 5;
if (get_field('count')) {
    $count = intval(get_field('count'));
}

$filter_query = '';

$search_filter = WLO_SEARCH.'de/search?filters={';

if (!empty($disciplines)) {
    $filter_query .= '{ 
        facet: discipline, 
        terms: [
            ' . implode(',', array_map("map_vocab_disciplines", $disciplines)) . '
            ] 
        }';

    $search_filter .= getSearchFilterValues('discipline', $postID);
}

if (!empty($educationalContexts)) {
    $filter_query .= '{ 
        facet: educationalContext, 
        terms: [
            ' . implode(',', array_map("map_vocab_educationalContexts", $educationalContexts)) . '
            ] 
        }';

    $search_filter .= getSearchFilterValues('educationalContext', $postID);
}

if (!empty($intendedEndUserRoles)) {
    $filter_query .= '{ 
        facet: intendedEndUserRole, 
        terms: [
            ' . implode(',', array_map("map_vocab_intendedEndUserRoles", $intendedEndUserRoles)) . '
            ] 
        }';

    $search_filter .= getSearchFilterValues('intendedEndUserRoles', $postID);
}

if (!empty($learningResourceTypes)) {
    $filter_query .= '{ 
        facet: learningResourceType, 
        terms: [
            ' . implode(',', array_map("map_vocab_learningResourceTypes", $learningResourceTypes)) . '
            ] 
        }';

    $search_filter .= getSearchFilterValues('learningResourceTypes', $postID);
}

if (!empty($objectTypes)) {
    $filter_query .= '{
        facet: type, 
        terms: [
            ' . implode(',', array_map("map_vocab_value_to_quotes", $objectTypes)) . '
            ] 
        }';

    $search_filter .= getSearchFilterValues('objectTypes', $postID);
}
if (!empty($generalKeywords)) {
    $filter_query .= '{
        facet: keyword, 
        terms: [
            ' . implode(',', array_map("map_vocab_value_to_quotes", $generalKeywords)) . '
            ] 
        }';
}

if ($oer) {
    $filter_query .= '{ 
        facet: oer, 
        terms: [
            "ALL"
            ] 
        }';
}
if ($topic) {
    $search_string = $topic;
}
$search_filter = substr($search_filter, 0, -1);
$search_filter .= '}&q='.$search_string;

$search_query = '
        {
          search(
            searchString: "' . $search_string . '"
            from: 0 
            size: ' . intval($count) . '
            filters: [
              ' . $filter_query . '
            ]
            includeCollectionTags: true
          ) {
         
            hits {
              lom {
                general {
                  title
                  description
                }
                technical {
                  location
                }
              }
              previewImage {
                thumbnail {
                  __typename
                  ... on ExternalThumbnail{
                    url
                  }
                }
                url
              }
              id
              source{
                name
              }
              skos{
                learningResourceType{
                  label
                  id
                }
              }
            }
          }
        }
    ';

if (get_field('custom_search_active')) {
    $search_query = get_field('search_query');
}
?>

<div class="portal_block">
    <?php
    if( !empty($block['anchor']) ) {
       echo '<a name="'.$block['anchor'].'"></a>';
    }
    ?>
    <div class="portal_block_headline">
        <?php
        if (!empty(get_field('headline')))
            echo '<h3>' . get_field('headline') . '</h3>';
        else
            echo '<h3>Neuigkeiten</h3>';
        ?>
        <a href='<?php echo $search_filter; ?>' target="_blank">Alle anzeigen</a>
    </div>
    <?php
    //var_dump('Search-Query: '.$search_query);
    $response = callWloGraphApi($search_query);

    if (!empty($response->data->search->hits)) {
        if (get_field('layout') == 'list') {
            ?>
            <div class="portal_latest_search_results_block">
                <div class="portal_latest_search_results_list"><?php
                    foreach ($response->data->search->hits as $hit) {
                        $contentUrl = WLO_SEARCH.'en-US/details/'.$hit->id;
                        if (!empty($hit->lom->technical->location)){
                            $contentUrl = $hit->lom->technical->location;
                        }
                    ?>

                    <div class="portal_latest_search_results_list_content">
                        <a href="<?php echo $contentUrl; ?>">
                            <img src="<?php echo $hit->previewImage->thumbnail->url; ?>" alt="">
                        </a>
                        <div class="portal_latest_search_results_list_content_text">
                            <a href="<?php echo $contentUrl; ?>"><h5><?php echo $hit->lom->general->title; ?></h5></a>
                            <p><?php echo $hit->lom->general->description; ?></p>
                        </div>
                    </div>

                    <?php
                    }
                    ?>
                </div>
            </div>
        <?php
        } elseif (get_field('layout') == 'grid'){?>
            <div class="portal_latest_search_results_grid"><?php
                foreach ($response->data->search->hits as $hit) {
                    $contentUrl = WLO_SEARCH.'en-US/details/'.$hit->id;
                    if (!empty($hit->lom->technical->location)){
                        $contentUrl = $hit->lom->technical->location;
                    }
                    ?>
                <div class="portal_latest_search_results_grid_container">
                    <div class="portal_latest_search_results_grid_content">
                        <a href="<?php echo $contentUrl; ?>">
                            <img src="<?php echo $hit->previewImage->thumbnail->url; ?>" alt="">
                        </a>
                        <div class="portal_latest_search_results_grid_content_text">
                            <a href="<?php echo $contentUrl; ?>"><h5><?php echo $hit->lom->general->title; ?></h5></a>
                            <p><?php echo $hit->lom->general->description; ?></p>
                        </div>
                    </div>
                </div>

                    <?php
                }
                ?>
            </div>
        <?php
        } elseif (get_field('layout') == 'slider'){
        $sliderId = uniqid('slider-');
        $slider_cards = 3;
        if (get_field('slider_cards')){
            $slider_cards = get_field('slider_cards');
        }
        $slider_count = 1;
        if (get_field('slider_count')){
            $slider_count = get_field('slider_count');
        }

        $json = file_get_contents("https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/learningResourceType/index.json");
        $skoData = json_decode($json, true);
        $lrtDictionary = $skoData["hasTopConcept"];
        ?>
            <div class="portal_latest_search_results_slider" id="<?php echo $sliderId ?>">
                <?php
                foreach ($response->data->search->hits as $hit) {
                    $contentUrl = WLO_SEARCH.'en-US/details/'.$hit->id;
                    if (!empty($hit->lom->technical->location)){
                        $contentUrl = $hit->lom->technical->location;
                    }
                    ?>
                    <div>
                        <div class="portal_latest_search_results_slider_content">
                            <a href="<?php echo $contentUrl; ?>"
                               target="_blank">
                                <img src="<?php echo $hit->previewImage->thumbnail->url; ?>" alt="">
                            </a>
                            <div class="portal_latest_search_results_slider_content_text">
                                <a href="<?php echo $contentUrl; ?>" target="_blank">
                                    <h6><?php echo $hit->lom->general->title; ?></h6>
                                </a>
                                <p><?php echo $hit->lom->general->description; ?></p>
                            </div>
                            <?php

                            $learningResourceType = $hit->skos->learningResourceType[0]->id;
                            $filteredValues = array_filter($lrtDictionary, function($lrtDicItem) use ($learningResourceType) {
                                return $lrtDicItem['id'] == $learningResourceType;
                            });
                            $learningResourceTypeLabel = (!empty($filteredValues)) ? end($filteredValues)['prefLabel']['de'] : 'Inhalt';


                            ?>
                            <div class="portal_latest_search_results_slider_content_bottom">
                                <a href="<?php echo $contentUrl; ?>" class="button primary small" target="_blank"><?php echo $learningResourceTypeLabel?> öffnen</a>
                                <?php if (!empty($hit->source->name) && false ) : ?>
                                <p>Quelle: <?php echo $hit->source->name?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <script type="text/javascript">
                jQuery(function () {
                    // Handler for .ready() called. Put the Slick Slider etc. init code here.
                    function loadSearchSlider() {
                        if (typeof jQuery().slick === "function") {
                            jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                                infinite: false,
                                slidesToShow: <?php echo $slider_cards; ?>,
                                slidesToScroll: <?php echo $slider_count; ?>,
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
                });
            </script>
        <?php
        }

    } else {
    ?>
        <p class="primary">Leider gibt es in dieser Rubrik keine Neuigkeiten. <a
                    href="<?php echo get_permalink(get_page_by_path('tool-hinzufuegen')) ?>">Hilf uns dabei</a>, die
            neuesten Themen bereitzustellen.</p>
        <?php
    }
    ?>
</div>
<?php if (is_admin()) {
    echo '</div>';
}; ?>

