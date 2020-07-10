<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Banner</div>';
};
?>
<div class="portal-breadcrumbs">
    <ul class="portal-breadcrumbs-list">

<?php
if ( get_the_id() ){
    $postID = get_the_id();
}else{
    $postID = acf_editor_post_id();
}

$url = (!empty(get_field('url'))) ? get_field('url') : get_field('collection_url', $postID);
$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $url, $matches);

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

echo "<script>console.log(" . $response . ")</script>";
$response = json_decode($response);

//for($i = 0; $i < sizeof($response); $i++){
//    $node = $response[i];
//    echo $node->name;
//}

$nodes = Array();

foreach ($response->nodes as $node){
    $nodeEntry = [$node->title, $node->properties->{'cclom:location'}[0]];
    array_push($nodes, $nodeEntry);
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