<?php if (is_admin()){
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Themenbaum</div>';
};?>
<?php

if (!function_exists('helper_useLightColor')) {
    function helper_useLightColor($bgColor)
    {
        $color = ($bgColor{0} === '#') ? substr($bgColor, 1, 7) : $bgColor;
        $r = intval(substr($color, 0, 2), 16); // hexToR
        $g = intval(substr($color, 2, 4), 16); // hexToG
        $b = intval(substr($color, 4, 6), 16); // hexToB
        return ((($r * 0.299) + ($g * 0.587) + ($b * 0.114)) > 186) ?
            false : true;
    }

    ;
}

if ( get_the_id() ){
    $postID = get_the_id();
}else{
    $postID = acf_editor_post_id();
}

if (get_field('active')){
    echo '<div class="portal-subject-tree">';

    $url = (!empty(get_field('url'))) ? get_field('url') : get_field('collection_url', $postID);

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

    //Top-Level
    if(!empty(get_field('headline')))
        echo '<h3>' . get_field('headline') . '</h3>';
    else
        echo '<h3>' . 'Sammlungen' . '</h3>';

    if (get_field('text')){
        echo '<p>'.get_field('text').'</p>';
    }

    if (get_field('next_level')){
        //Sub-Level
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
                $bgColor = $collection->properties->{'ccm:collectioncolor'}[0];
                $fontColor = (!empty($bgColor) && helper_useLightColor($bgColor)) ? "#000000" : "#ffffff";
                ?>
                <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>">
                    <div class="portal_tree_branch" style="
                            background: <?php echo $bgColor ?>;
                            color: <?php echo $fontColor ?>;
                            ">
                        <img width="70" height="50" src="<?php echo $collection->preview->url; ?>">
                        <div class="portal_search_text">
                            <h5><?php echo $collection->title; ?></h5>
                        </div>
                    </div>
                </a>
            <?php }
        }
        echo '</div>';
    }


    echo '</div>';
}
?>
<?php if (is_admin()){echo '</div>';};?>
