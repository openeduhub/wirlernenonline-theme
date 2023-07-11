<?php
[
    'collectionId' => $collectionId,
    'subjectPortalId' => $subjectPortalId,
] = $args;

$subCollections = getSubCollections($collectionId);
?>

<ul class="sub-pages-links-list">
    <?php
    if (empty($subCollections)) {
    ?>
        <em>Keine weiteren Unterthemen</em>
    <?php
    }

    foreach ($subCollections as $collection) {
        $topicPageUrl = wlo_convert_dev_url($collection->properties->{'cclom:location'}[0]);
        $careerPageUrl = rtrim($topicPageUrl, "/") . '-berufsinfo';
        $title = getCollectionShortTitle($collection);

        $topicPost = get_post(url_to_postid($topicPageUrl));
        createOrUpdateCareerPage($topicPost, $subjectPortalId);
    ?>
        <li><a href="<?php echo $careerPageUrl; ?>"><?php echo $title; ?></a></li>
    <?php } ?>
    <!-- <a href="">Zeige mir alle Themen</a> -->
</ul>