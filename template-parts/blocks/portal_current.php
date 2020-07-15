<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Suchinhalte</div>';
};

if (get_the_id()) {
    $postID = get_the_id();
} else {
    $postID = acf_editor_post_id();
}

if (get_field('active')) {

    $count = 5;
    if (get_field('count')) {
        $count = intval(get_field('count'));
    }

    //Disciplines
    $query_var_disciplines = (!empty(get_query_var('discipline', null))) ? explode(";", get_query_var('discipline', null)) : [];
    $block_var_disciplines = (!empty(get_field('discipline'))) ? get_field('discipline') : [];
    $portal_var_disciplines = get_field('discipline', $postID);

    $disciplines = (!empty($portal_var_disciplines)) ? $portal_var_disciplines : [];
    $disciplines = (!empty($block_var_disciplines)) ? $block_var_disciplines : $disciplines;
    $disciplines = (!empty($query_var_disciplines)) ? $query_var_disciplines : $disciplines;

    //EducationalContext
    $query_var_educationalContexts = (!empty(get_query_var('educationalContext', null))) ? explode(";", get_query_var('educationalContext', null)) : [];
    $block_var_educationalContexts = (!empty(get_field('educationalContext'))) ? get_field('educationalContext') : [];
    $portal_var_educationalContexts = get_field('educationalContext', $postID);

    $educationalContexts = (!empty($portal_var_educationalContexts)) ? $portal_var_educationalContexts : [];
    $educationalContexts = (!empty($block_var_educationalContexts)) ? $block_var_educationalContexts : $educationalContexts;
    $educationalContexts = (!empty($query_var_educationalContexts)) ? $query_var_educationalContexts : $educationalContexts;

    //intendedEndUserRole
    $query_var_intendedEndUserRoles = (!empty(get_query_var('intendedEndUserRole', null))) ? explode(";", get_query_var('intendedEndUserRole', null)) : [];
    $block_var_intendedEndUserRoles = (!empty(get_field('intendedEndUserRole'))) ? get_field('intendedEndUserRole') : [];
    $portal_var_intendedEndUserRoles = get_field('intendedEndUserRole', $postID);

    $intendedEndUserRoles = (!empty($portal_var_intendedEndUserRoles)) ? $portal_var_intendedEndUserRoles : [];
    $intendedEndUserRoles = (!empty($block_var_intendedEndUserRoles)) ? $block_var_intendedEndUserRoles : $intendedEndUserRoles;
    $intendedEndUserRoles = (!empty($query_var_intendedEndUserRoles)) ? $query_var_intendedEndUserRoles : $intendedEndUserRoles;

    //OER
    $query_var_oer = get_query_var('oer', false);
    $block_var_oer = get_field('oer');
    $portal_var_oer = get_field('oer', $postID);

    $oer = (!empty($portal_var_oer)) ? $portal_var_oer : false;
    $oer = (!empty($block_var_oer)) ? $block_var_oer : $oer;
    $oer = (!empty($query_var_oer)) ? $query_var_oer : $oer;


    //Map Disciplines
    if (!function_exists('map_disciplines')) {
        function map_disciplines($n)
        {
            return "\"https://w3id.org/openeduhub/vocabs/discipline/" . strval($n) . "\"";
        }
    }

    //Map EducationalContexts
    if (!function_exists('map_educationalContexts')) {
        function map_educationalContexts($n)
        {
            return "\"https://w3id.org/openeduhub/vocabs/educationalContext/" . strval($n) . "\"";
        }
    }

    //Map IntendedEndUserRoles
    if (!function_exists('map_intendedEndUserRoles')) {
        function map_intendedEndUserRoles($n)
        {
            return "\"https://w3id.org/openeduhub/vocabs/intendedEndUserRole/" . strval($n) . "\"";
        }
    }

    $oer_search = '';
    if ($oer) {
        $oer_search = '{ facet: oer, terms: ["ALL"] }';
    }
    $search_query = '
        {
          search(
            searchString: ""
            from: 0
            filters: [
              {
                facet: discipline
                terms: [
                    ' . implode('\n', array_map("map_disciplines", array_column($disciplines, "value"))) . '
                ]
              }
              {
                facet: educationalContext
                terms: [
                    ' . implode('\n', array_map("map_educationalContexts", array_column($educationalContexts, "value"))) . '
                ]
              }
              {
                facet: intendedEndUserRole
                terms: [
                    ' . implode('\n', array_map("map_intendedEndUserRoles", array_column($intendedEndUserRoles, "value"))) . '
                ]
              }
              ' . $oer_search . '
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

