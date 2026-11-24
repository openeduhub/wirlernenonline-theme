<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];
$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

$educationalContexts = $educational_filter_values["educationalContexts"];
$accordionID = uniqid();

$url = WLO_REPO . 'rest/collection/v1/collections/local/' . $collectionID . '/children/collections?scope=MY&skipCount=0&maxItems=1247483647&sortProperties=cm%3Aname&sortAscending=true';
$subCollections = callWloRestApi($url);

$subSubjects = [];
foreach ($subCollections->collections ?? [] as $collection) {
    // Filter hidden collections and collections without a page
    if (($collection->properties->{'ccm:editorial_state'}[0] ?? null) !== 'activated' || empty($collection->properties->{'cclom:location'}[0])) {
        continue;
    }

    // Filter educationalContexts (collections without educationalcontext are kept)
    if (!empty($educationalContexts) && !empty($collection->properties->{'ccm:educationalcontext'})
        && !checkPropertyMatch($collection->properties->{'ccm:educationalcontext'}, $educationalContexts, true)) {
        continue;
    }

    $title = $collection->title;
    if (!empty($collection->properties->{'ccm:collectionshorttitle'}[0])) {
        $title = $collection->properties->{'ccm:collectionshorttitle'}[0];
    }
    $subSubjects[] = [
        'title' => $title,
        'url' => wlo_convert_dev_url($collection->properties->{'cclom:location'}[0]),
    ];
}

// Without sub-topics to link to (e.g. on Themenseiten) the block would only be an empty accordion
if (empty($subSubjects) && !is_admin()) {
    return;
}

if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachseite: Header' . (empty($subSubjects) ? ' (keine Unterthemen, wird auf der Seite nicht angezeigt)' : '') . '</div>';
}
?>

<div class="fachportal-header-block fachportal-new-content">
    <div class="fachportal-header-wrapper" >
        <div class="fachportal-new-content-inner" style="background-color: #F4F4F4">
            <button class="fachportal-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
                <h2>Durchstöbere hier unsere Lehrplanthemen</h2>
                <img class="fachportal-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Inhalte ein odder ausklappen">
            </button>
            <div class="fachportal-accordion-content" id="fachportal-accordion-content-<?php echo $accordionID; ?>">
                <div class="collections">
                    <?php if (!empty($subSubjects)) : ?>
                        <div class="sub-subjects">
                            <div class="sub-subjects-container">
                                <?php foreach ($subSubjects as $subSubject) : ?>
                                    <div class="sub-subject">
                                        <a href="<?php echo $subSubject['url']; ?>">
                                            <p><?php echo $subSubject['title']; ?></p>
                                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<!--        <div class="header-bottom"></div>-->
    </div>
</div>

<script>
    jQuery('#fachportal-accordion-<?php echo $accordionID; ?>').click(function(){
        jQuery(this).find("img").toggleClass("fachportal-accordion-icon-active");
        jQuery('#fachportal-accordion-content-<?php echo $accordionID; ?>').slideToggle('medium');
    });

    // educationalcontext filter
    function educationalcontextFilter(educationalcontext){
        let url =  window.location.href.split('?')[0];
        let educationalcontexts = [<?php
                                        foreach ($educationalContexts as $context){
                                            echo '"'.$context.'",';
                                        }
                                    ?>];

        if (educationalcontexts.includes(educationalcontext)){
            educationalcontexts.forEach(function(item, index, array) {
                if (item == educationalcontext){
                    //console.log('same');
                }else {
                    if (url.indexOf('?') > -1){
                        url += '&educationalContext[]=' + item;
                    }else{
                        url += '?educationalContext[]=' + item;
                    }
                }
            })
        }else {
            educationalcontexts.forEach(function(item, index, array) {
                if (url.indexOf('?') > -1){
                    url += '&educationalContext[]=' + item;
                }else{
                    url += '?educationalContext[]=' + item;
                }
            })
            if (url.indexOf('?') > -1){
                url += '&educationalContext[]=' + educationalcontext;
            }else{
                url += '?educationalContext[]=' + educationalcontext;
            }
        }

        window.location.href = url;
    }
</script>


<?php if (is_admin()) {
    echo '</div>';
} ?>

