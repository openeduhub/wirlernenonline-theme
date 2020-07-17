<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Breadcrumbs</div>';
};
?>
<div class="portal-breadcrumbs">
    <ul class="portal-breadcrumbs-list">

<?php


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

    //echo '<pre style="background-color: lightgrey">' , var_dump($educational_filter_values) , '</pre>';

    $collectionUrl = $educational_filter_values["collectionUrl"];
    $disciplines = $educational_filter_values["disciplines"];
    $educationalContexts = $educational_filter_values["educationalContexts"];
    $intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
    $oer = $educational_filter_values["oer"];
    /* ------------------------------------------------------------------- */


$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);

$url = 'https://redaktion.openeduhub.net/edu-sharing/rest/node/v1/nodes/-home-/'.$matches[1][0].'/parents?propertyFilter=-all-&fullPath=false';

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
$nodes = Array();

foreach ($response->nodes as $node){
    $nodes[] = [$node->title, $node->properties->{'cclom:location'}[0]];
}
//Delete highest Level
array_pop($nodes);
$nodes = array_reverse($nodes);

foreach ($nodes as $node){
    echo "<li class='portal-breadcrumbs-list-item'><a href='". $node[1] . "'>" . $node[0] . "</a><span class='material-icons'>chevron_right</span></li>";
}

?>
    </ul>
</div>

<?php if (is_admin()){echo '</div>';};?>