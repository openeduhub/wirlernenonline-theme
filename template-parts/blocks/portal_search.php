<?php

if ( get_the_id() ){
    $postID = get_the_id();
}else{
    $postID = acf_editor_post_id();
}

if (is_admin()){
    echo '<div class="portal_backend_hint">Suche/ Navigations Block</div>';
}

$oer = '';
if (get_field('oer', $postID)){
    $oer = '{ field: "license.oer", terms: ["ALL"] }';
}
$search_query = '
        {
          search(
            searchString: ""
            size: 0
            from: 0
            filters: [
              {
                field: "valuespaces.discipline.key.keyword"
                terms: ["https://w3id.org/openeduhub/vocabs/discipline/'.get_field('subject', $postID)['value'].'"]
              }
              {
                field: "valuespaces.educationalContext.key.keyword"
                terms: [
                  "https://w3id.org/openeduhub/vocabs/educationalContext/'.get_field('school_type', $postID)['value'].'"
                ]
              }
              {
                field: "valuespaces.intendedEndUserRole.key.keyword"
                terms: [
                  "https://w3id.org/openeduhub/vocabs/intendedEndUserRole/'.get_field('role', $postID)['value'].'"
                ]
              }
              '.$oer.'
            ]
          ) {
            hits {
              total {
                value
              }
            }
          }
        }
    ';

$response = callWloGraphApi($search_query);
$total = $response->data->search->hits->total->value;

$sources_search_query = '
        {
          facets(
            searchString: ""
            size: 20
            filters: [
              {
                field: "valuespaces.discipline.key.keyword"
                terms: ["https://w3id.org/openeduhub/vocabs/discipline/'.get_field('subject', $postID)['value'].'"]
              }
              {
                field: "valuespaces.educationalContext.key.keyword"
                terms: [
                  "https://w3id.org/openeduhub/vocabs/educationalContext/'.get_field('school_type', $postID)['value'].'"
                ]
              }
              {
                field: "valuespaces.intendedEndUserRole.key.keyword"
                terms: [
                  "https://w3id.org/openeduhub/vocabs/intendedEndUserRole/'.get_field('role', $postID)['value'].'"
                ]
              }
              '.$oer.'
            ]
          ) {
            sources {
              total_buckets
              buckets {
                key
                doc_count
              }
            }
          }
        }
    ';

$sources_response = callWloGraphApi($sources_search_query);
$sources = $sources_response->data->facets->sources->buckets;

    ?>
<div class="portal_search">
    <form target="_blank" action="https://staging.wirlernenonline.de/de/search/<?php echo get_field('school_type', $postID)['label']; ?>/<?php echo get_field('subject', $postID)['label']; ?>" method="GET" class="home-hero__form">
        <div class="search-container">
            <p><?php the_field('search_description'); ?></p>
            <div class="portal-search-group">
                <input class="input-group-field" type="search" name="q" id="search" aria-label="Search" placeholder="<?php echo $total; ?> Ergebnisse für <?php echo get_field('subject', $postID)['label']; ?> - <?php echo get_field('school_type', $postID)['label']; ?>" autocomplete="off">
                <div class="input-group-button">
                    <input type="submit" class="button success" value="Suche">
                </div>
            </div>
        </div>
    </form>
    <div class="portal_search_text" style="display: none;">
        <p>Für <?php echo get_field('subject', $postID)['label']; ?>, <?php echo get_field('school_type', $postID)['label']; ?> gibt es <span class="font-bold"><?php echo $total; ?></span> Ergebnisse von:</p>
        <div class="portal_search_sources">
            <?php
            foreach ($sources as $source){
                echo '<p>'.$source->key.': <span class="font-bold">'.$source->doc_count.'</span></p>';
            }
            ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function(){

    });
</script>
