<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: WLO-Suchanfrage</div>';
}

require_once(get_template_directory().'/functions/wlo-config.php');

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
// echo '<script>' , json_encode($educational_filter_values) , '</script>';

$collectionUrl = $educational_filter_values["collectionUrl"];
$disciplines = $educational_filter_values["disciplines"];
$educationalContexts = $educational_filter_values["educationalContexts"];
$intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
$oer = $educational_filter_values["oer"];
/* ------------------------------------------------------------------- */

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
            size: 50
            from: 0
            filters: [
               ' . $filter_query . '
            ]
          ) {
            hits {
              id
            }
          }
        }
    ';

$response = callWloGraphApi($search_query);
$total = (!empty($response->data->search->hits)) ? sizeof($response->data->search->hits) : 0;

$sources_search_query = '
        {
          facets(
            facets: [
              source
            ]
            searchString: ""
            size: 20
            filters: [
                ' . $filter_query . '
            ]
          ) {
            buckets{
              key
              doc_count
            }
            total_buckets
          }
        }
    ';

$sources_response = callWloGraphApi($sources_search_query);
$sources = $sources_response->data->facets[0]->buckets;

$disciplinesLabel = (!empty(get_field_object('discipline', $postID))) ? get_field_object('discipline', $postID)['choices'][$disciplines[0]] : '';
$educationalContextsLabel = (!empty(get_field_object('educationalContext', $postID))) ? get_field_object('educationalContext', $postID)['choices'][$educationalContexts[0]] : '';
$intendedEndUserRolesLabel = (!empty(get_field_object('intendedEndUserRole', $postID))) ? get_field_object('intendedEndUserRole', $postID)['choices'][$intendedEndUserRoles[0]] : '';


$filter = '{"discipline":["'.$disciplinesLabel.'"]}';

    ?>
<div class="portal_block portal_search">
    <form target="_blank" action='<?php echo WLO_SEARCH; ?>de/search' method="GET" class="home-hero__form">
        <div class="search-container">
            <p><?php the_field('search_description'); ?></p>
            <div class="portal-search-group">
                <input class="input-group-field" type="search" name="q" id="search" aria-label="Search" placeholder="<?php echo $total; ?> Ergebnisse für <?php echo $disciplinesLabel; ?><?php echo (!empty($educationalContextsLabel)) ? ' - ' . $educationalContextsLabel : ''; ?>" autocomplete="off">
                <input type="hidden" name="filters" value='<?php echo $filter; ?>' />
                <div class="input-group-button">
                    <input type="submit" class="button success" value="Suche">
                </div>
            </div>
        </div>
    </form>
    <?php if (get_field('search_sources')): ?>
    <div class="portal_search_text">
        <p>Für <?php echo $disciplinesLabel; ?><?php echo (!empty($educationalContextsLabel)) ? ', ' . $educationalContextsLabel : ''; ?> gibt es <span class="font-bold"><?php echo $total; ?></span> Ergebnisse von:</p>
        <div class="portal_search_sources">
            <?php
            if(!empty($sources))
            {
                foreach ($sources as $source){
                    echo '<p>'.$source->key.': <span class="font-bold">'.$source->doc_count.'</span></p>';
                }
            }
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>


<script type="text/javascript">
    jQuery(document).ready(function(){

    });
</script>
<?php if (is_admin()){echo '</div>';};?>
