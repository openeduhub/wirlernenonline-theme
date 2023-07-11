<?php
[
    'collectionId' => $collectionId,
] = $args;
$subCollections = getSubCollections($collectionId);
?>

<ul class="sub-pages-links-list">
    <?php foreach ($subCollections as $collection) {
        $url = wlo_convert_dev_url($collection->properties->{'cclom:location'}[0]);
        $url = rtrim($url, "/") . '-berufsinfo';
        $title = getCollectionShortTitle($collection);
    ?>
        <li><a href="<?php echo $url; ?>"><?php echo $title; ?></a></li>
    <?php } ?>
    <!-- <a href="">Zeige mir alle Themen</a> -->
</ul>