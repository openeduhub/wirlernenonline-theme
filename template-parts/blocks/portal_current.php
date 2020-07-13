<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Suchinhalte</div>';
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

    $search_discipline = get_field('discipline', $postID)['value'];
    $search_edu_context = get_field('edu_context', $postID)['value'];
    $search_intended_end_user_role = get_field('intended_end_user_role', $postID)['value'];
    $search_oer = get_field('oer', $postID);
    if (get_field('settings_active')){
        $search_discipline = get_field('discipline')['value'];
        $search_edu_context = get_field('edu_context')['value'];
        $search_intended_end_user_role = get_field('intended_end_user_role')['value'];
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
                terms: ["https://w3id.org/openeduhub/vocabs/discipline/'.$search_discipline.'"]
              }
              {
                field: "valuespaces.educationalContext.key.keyword"
                terms: [
                  "https://w3id.org/openeduhub/vocabs/educationalContext/'.$search_edu_context.'"
                ]
              }
              {
                field: "valuespaces.intendedEndUserRole.key.keyword"
                terms: [
                  "https://w3id.org/openeduhub/vocabs/intendedEndUserRole/'.$search_intended_end_user_role.'"
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
                  educational {
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
        if(!empty(get_field('headline')))
            echo '<h3>' . get_field('headline') . '</h3>';
        else
            echo '<h3>Neuigkeiten</h3>';

        echo '<div class="portal_block_slider">';
        foreach ($response->data->search->hits->hits as $hit){
            ?>
            <div>
                <div class="portal_block_slider_content">
                    <a href="https://staging.wirlernenonline.de/en-US/details/<?php echo $hit->id; ?>" target="_blank">
                        <img src="data:<?php echo $hit->thumbnail->mimetype; ?>;base64, <?php echo $hit->thumbnail->small; ?>">
                    </a>
                    <div class="portal_block_slider_content_text">
                        <a href="https://staging.wirlernenonline.de/en-US/details/<?php echo $hit->id; ?>" target="_blank"><h5><?php echo $hit->lom->general->title; ?></h5></a>
                        <p><?php echo $hit->lom->educational->description; ?></p>
                        <!--<p><?php echo $hit->lom->general->description; ?></p>-->
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
        jQuery('.portal_block_slider').slick({
            infinite: true,
            slidesToShow: 2,
            slidesToScroll: 1,
            zIndex: 0
        });
    });
</script>
<?php if (is_admin()){echo '</div>';};?>

