<?php
if (is_admin()) {
    echo '<div class="portal_backend_border">';
    echo '<div class="portal_backend_hint">Block: Suchinhalte</div>';
};

if ( get_the_id() ){
    $postID = get_the_id();
}else{
    $postID = acf_editor_post_id();
}

if (get_field('active')){

    $count = 5;
    if (get_field('count')){
        $count = intval( get_field('count') );
    }

    $search_subject = get_field('subject', $postID)['value'];
    $search_school_type = get_field('school_type', $postID)['value'];
    $search_school_role = get_field('role', $postID)['value'];
    $search_oer = get_field('oer', $postID);
    if (get_field('settings_active')){
        $search_subject = get_field('subject')['value'];
        $search_school_type = get_field('school_type')['value'];
        $search_school_role = get_field('role')['value'];
        $search_oer = get_field('oer');
    }

    $oer = '';
    if ($search_oer){
        $oer = '{ field: "license.oer", terms: ["ALL"] }';
    }
    $search_query = '
        {
          search(
            searchString: ""
            size: '.$count.'
            from: 0
            filters: [
              {
                field: "valuespaces.discipline.key.keyword"
                terms: ["https://w3id.org/openeduhub/vocabs/discipline/'.$search_subject.'"]
              }
              {
                field: "valuespaces.educationalContext.key.keyword"
                terms: [
                  "https://w3id.org/openeduhub/vocabs/educationalContext/'.$search_school_type.'"
                ]
              }
              {
                field: "valuespaces.intendedEndUserRole.key.keyword"
                terms: [
                  "https://w3id.org/openeduhub/vocabs/intendedEndUserRole/'.$search_school_role.'"
                ]
              }
              '.$oer.'
            ]
          ) {
            hits {
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
                thumbnail {
                  small
                  mimetype
                }
                id
              }
            }
          }
        }
    ';

    if (get_field('custom_search_active')){
        $search_query = get_field('search_query');
    }

    $response = callWloGraphApi($search_query);

    echo '<div class="portal_block">';
        echo '<h3>'.get_field('headline').'</h3>';
        echo '<div class="portal_block_slider">';
        foreach ($response->data->search->hits->hits as $hit){
            ?>
            <div>
                <div class="portal_block_slider_content">
                    <a href="https://staging.wirlernenonline.de/en-US/details/<?php echo $hit->id; ?>" target="_blank" class="portal_more_link">
                        <img src="data:<?php echo $hit->thumbnail->mimetype; ?>;base64, <?php echo $hit->thumbnail->small; ?>">
                    </a>
                    <div class="portal_block_slider_content_text">
                        <h3><?php echo $hit->lom->general->title; ?></h3>
                        <p><?php echo $hit->lom->general->description; ?></p>
                        <a href="https://staging.wirlernenonline.de/en-US/details/<?php echo $hit->id; ?>" target="_blank" class="portal_more_link">zur Quelle...</a>
                    </div>
                </div>
            </div>
        <?php
        }
        echo '</div>';
    echo '</div>';

}
?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.portal_block_slider').slick();
    });
</script>
<?php if (is_admin()){echo '</div>';};?>