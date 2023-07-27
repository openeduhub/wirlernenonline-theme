<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: WLO Agreggierte Suchergebnise</div>';
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

$headline = 'Suchergebnisse zu '.esc_html( get_the_title($postID) );
if (get_field('headline')){
    $headline = get_field('headline');
}

$filter_query = '';

$search_query = '
        {
          search(
            searchString: "' . esc_html( get_the_title($postID) ) . '"
            from: 0 
            size: ' . 25 . '
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

    ?>

<div class="portal_block portal_search_results">
    <?php
    if( !empty($block['anchor']) ) {
        echo '<a name="'.$block['anchor'].'"></a>';
    }
    ?>
    <div class="portal_block_headline">
        <?php echo '<h3>'.$headline.'</h3>'; ?>
    </div>
    <hr class="big_hr">
    <?php
    $response = callWloGraphApi($search_query);

    if (!empty($response->data->search->hits)) {

        ?>
        <div class="portal_latest_search_results_grid">
            <?php foreach ($response->data->search->hits as $hit) {
                $contentUrl = WLO_SEARCH.'en-US/details/'.$hit->id;
                if (!empty($hit->lom->technical->location)){
                    $contentUrl = $hit->lom->technical->location;
                }
                ?>

                <div class="portal_content_branch">
                    <img src="<?php echo $hit->previewImage->thumbnail->url; ?>&crop=true&maxWidth=400&maxHeight=400" alt="">
                    <div class="portal_content_info">
                        <div class="portal_search_text">
                            <a href="<?php echo $contentUrl; ?>" target="_blank">
                                <h6><?php echo $hit->lom->general->title; ?></h6></a>
                            <p><?php echo $hit->lom->general->description ?></p>
                        </div>
                        <div class="portal_search_button">
                            <a class="button primary small" href="<?php echo $contentUrl; ?>" target="_blank">Material öffnen</a>
                        </div>
                    </div>
                </div>

                <?php
            }
            ?>
        </div>
    <?php


    } else {
    ?>
        <p class="primary">Leider gibt es für die Rubrik "<?php echo esc_html( get_the_title($postID) ); ?>" noch keine Inhalte.
            <a href="<?php echo get_permalink(get_page_by_path('tool-hinzufuegen')) ?>">Hilf uns dabei</a>, die
            neuesten Themen bereitzustellen.</p>
        <?php
    }
    ?>
</div>


<?php if (is_admin()){echo '</div>';};?>
