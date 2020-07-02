<?php

if ( get_the_id() ){
    $postID = get_the_id();
}else{
    $postID = acf_editor_post_id();
}

if (is_admin()){
    echo '<div class="portal_backend_hint">Themenbaum Block</div>';
}

if (get_field('active')){
    echo '<div>';
    $url = get_field('url');
    $pattern = '/http.*\?id=(.*)(&|$)/';
    preg_match_all($pattern, $url, $matches);

    $url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/local/'.$matches[1][0].'/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true&';

    try {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json; charset=utf-8'
            )
        );
        $response = curl_exec($curl);
        if($response === false) {
            echo 'curl error';
            return false;
        }
    } catch (Exception $e) {
        echo 'curl error: '.$e->getMessage();
        return false;
    }
    curl_close($curl);

    $response = json_decode($response);

    if (get_field('headline')){
        echo '<h3>'.get_field('headline').'</h3>';
    }
    if (get_field('text')){
        echo '<p>'.get_field('text').'</p>';
    }

    if (get_field('next_level')){
        echo '<div class="portal_subject_grid">';
        foreach ($response->collections as $collection){
            $nodeId = $collection->ref->id;

            ?>
            <ul>
                <li>
                    <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>"><?php echo $collection->title; ?></a>


            <?php

            $url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/local/'.$nodeId.'/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true&';

            try {
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Accept: application/json',
                        'Content-Type: application/json; charset=utf-8'
                    )
                );
                $response = curl_exec($curl);
                if($response === false) {
                    echo 'curl error';
                    return false;
                }
            } catch (Exception $e) {
                echo 'curl error: '.$e->getMessage();
                return false;
            }
            curl_close($curl);

            $response = json_decode($response);
                echo '<ul>';
            foreach ($response->collections as $collection){
                if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated'){
                    ?>
                <li><a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>"><?php echo $collection->title; ?></a></li>
                <?php }
            }
            echo '</ul>';


            echo '</li>';
        echo '</ul>';
        }
        echo '</div>';
    }else{
        echo '<div class="portal_subject_grid">';
        foreach ($response->collections as $collection){
            if ($collection->properties->{'ccm:editorial_state'}[0] == 'activated'){
                ?>
                <div class="portal_tree_branch" style="background: <?php echo $collection->properties->{'ccm:collectioncolor'}[0]; ?>">
                    <img src="<?php echo $collection->preview->url; ?>">
                    <div class="portal_search_text">
                        <p><?php echo $collection->title; ?><p>
                            <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>">Link</a>
                    </div>
                </div>
            <?php }
        }
        echo '</div>';
    }


    echo '</div>';
}
?>

