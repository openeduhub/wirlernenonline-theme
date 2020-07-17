<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Suchinhalte</div>';
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

$collectionUrl = $educational_filter_values["collectionUrl"];
$disciplines = $educational_filter_values["disciplines"];
$educationalContexts = $educational_filter_values["educationalContexts"];
$intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
$oer = $educational_filter_values["oer"];
/* ------------------------------------------------------------------- */

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
        facet: educationalContext, 
        terms: [
            ' . implode('\n', array_map("map_vocab_intendedEndUserRoles", $intendedEndUserRoles)) . '
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

$response = callWloGraphApi($search_query);
if (!empty($response->data->search->hits)) {


    if (!empty(get_field('headline')))
        echo '<h3>' . get_field('headline') . '</h3>';
    else
        echo '<h3>Neuigkeiten</h3>';
    echo '<div class="portal_latest_search_results_slider">';
    foreach ($response->data->search->hits as $hit) {
        ?>
        <div>
            <div class="portal_latest_search_results_slider_content">
                <a href="https://staging.wirlernenonline.de/en-US/details/<?php echo $hit->id; ?>" target="_blank">
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
    echo '</div>';
}

?>

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
<?php if (is_admin()) {
    echo '</div>';
}; ?>

