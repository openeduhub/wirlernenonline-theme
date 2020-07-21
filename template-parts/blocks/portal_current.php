<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Inhaltesuche</div>';
};

/* Filter Values prioritized GET/POST > Block-Setting > Portal-Setting */
/*
* collectionUrl
* disciplines
* educationalContexts
* intendedEndUserRoles
* oer
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
/* ------------------------------------------------------------------- */

$block_var_objectTypes = (!empty(get_field('objectTypes'))) ? get_field('objectTypes') : [];
$objectTypes = (!empty($block_var_objectTypes)) ? array_column($block_var_objectTypes, 'value') : [];
$objectTypes = (!empty($objectTypes)) ? $objectTypes : get_post_meta($postID, 'objectTypes', false)[0];

$block_var_learningResourceTypes = (!empty(get_field('learningResourceTypes'))) ? get_field('learningResourceTypes') : [];
$learningResourceTypes = (!empty($block_var_learningResourceTypes)) ? array_column($block_var_learningResourceTypes, 'value') : [];
$learningResourceTypes = (!empty($learningResourceTypes)) ? $learningResourceTypes : get_post_meta($postID, 'learningResourceTypes', false)[0];

$block_var_generalKeywords = get_field('generalKeyword');
$generalKeywords = (!empty($block_var_generalKeywords)) ? $block_var_generalKeywords : get_post_meta($postID, 'generalKeyword', false)[0];
$generalKeywords = (!empty($generalKeywords)) ? explode(",", $generalKeywords) : [];

$count = 5;
if (get_field('count')) {
    $count = intval(get_field('count'));
}

$filter_query = '';

if (!empty($disciplines)) {
    $filter_query .= '{ 
        facet: discipline, 
        terms: [
            ' . implode('\n', array_map("map_vocab_disciplines", $disciplines)) . '
            ] 
        }';
}

if (!empty($educationalContexts)) {
    $filter_query .= '{ 
        facet: educationalContext, 
        terms: [
            ' . implode('\n', array_map("map_vocab_educationalContexts", $educationalContexts)) . '
            ] 
        }';
}

if (!empty($intendedEndUserRoles)) {
    $filter_query .= '{ 
        facet: intendedEndUserRole, 
        terms: [
            ' . implode('\n', array_map("map_vocab_intendedEndUserRoles", $intendedEndUserRoles)) . '
            ] 
        }';
}

if (!empty($learningResourceTypes)) {
    $filter_query .= '{ 
        facet: learningResourceType, 
        terms: [
            ' . implode('\n', array_map("map_vocab_learningResourceTypes", $learningResourceTypes)) . '
            ] 
        }';
}

if (!empty($objectTypes)) {
    $filter_query .= '{
        facet: type, 
        terms: [
            ' . implode('\n', array_map("map_vocab_value_to_quotes", $objectTypes)) . '
            ] 
        }';
}
if (!empty($generalKeywords)) {
    $filter_query .= '{
        facet: keyword, 
        terms: [
            ' . implode('\n', array_map("map_vocab_value_to_quotes", $generalKeywords)) . '
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

$search_query = '
        {
          search(
            searchString: ""
            from: 0 
            size: ' . intval($count) . '
            filters: [
              ' . $filter_query . '
            ]
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
    if (!empty(get_field('headline')))
        echo '<h3>' . get_field('headline') . '</h3>';
    else
        echo '<h3>Neuigkeiten</h3>';
    $response = callWloGraphApi($search_query);
    if (!empty($response->data->search->hits)) {
        ?>
        <div class="portal_latest_search_results_slider">
            <?php
            foreach ($response->data->search->hits as $hit) {
                ?>
                <div>
                    <div class="portal_latest_search_results_slider_content">
                        <a href="https://staging.wirlernenonline.de/en-US/details/<?php echo $hit->id; ?>"
                           target="_blank">
                            <img src="<?php echo $hit->previewImage->thumbnail->url; ?>">
                        </a>
                        <div class="portal_latest_search_results_slider_content_text">
                            <a href="https://staging.wirlernenonline.de/en-US/details/<?php echo $hit->id; ?>"
                               target="_blank"><h5><?php echo $hit->lom->general->title; ?></h5></a>
                            <p><?php echo $hit->lom->general->description; ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery('.portal_latest_search_results_slider').slick({
                    infinite: true,
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    zIndex: 0
                });
            });
        </script>
        <?php
    } else {
        ?>
        <h6 class="primary">Leider gibt es in dieser Rubrik keine Neuigkeiten. <a
                    href="<?php echo get_permalink(get_page_by_path('tool-hinzufuegen')) ?>">Hilf' uns dabei</a>, die
            neuesten Themen bereitzustellen.</h6>
        <?php
    }
    ?>
</div>
<?php if (is_admin()) {
    echo '</div>';
}; ?>

