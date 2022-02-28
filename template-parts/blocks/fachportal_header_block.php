<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenseite: Header</div>';
} ?>

<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];

$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID;
$response = callWloRestApi($url);



$url = WLO_REPO . 'rest/node/v1/nodes/-home-/' . $collectionID . '/parents?propertyFilter=-all-&fullPath=false';
$parents = callWloRestApi($url)->nodes;
$portal = $parents[count($parents)-2];
$portalTitle = '';
if (!empty($portal->title)){
    $portalTitle = $portal->title;
}
$portalUrl = '#';
if (!empty($portal->properties->{'cclom:location'}[0])){
    $portalUrl = $portal->properties->{'cclom:location'}[0];
}

$breadcrumbs = Array();
if (!empty($parents)){
    foreach ($parents as $node) {
        if ($node->title == 'Portale'){
            $breadcrumbs[] = ['Fachportale', get_page_link(9930)];
        }else{
            $breadcrumbs[] = [$node->title, str_replace('dev.wirlernenonline.de', 'wirlernenonline.de', $node->properties->{'cclom:location'}[0])];
        }
    }
    $breadcrumbs = array_reverse($breadcrumbs);
}

if (empty($response->collection->properties->{'cm:description'}[0])){
    $description = '
                    Hier findest du zahlreiches kostenloses Material für '.$portalTitle.'!<br><br>
                    Neben sorgfältig ausgewählten Inhalten für jede Art von Unterricht findest du auch kurzweilige 
                    Inhalte für '.$portalTitle.' zum eigenständigen Lernen.<br><br>
                    Du kennst tolle Inhalte? Dann bringe dich und dein Wissen ein! Hilf mit, die besten Inhalte zu sammeln 
                    und zu teilen, empfehle dein persönliches Fach-Highlight oder 
                    <a href="'.get_page_link(97).'">mach mit</a> in unserer Fachredaktion!
    ';
}else{
    $description = $response->collection->properties->{'cm:description'}[0];
}

if (!function_exists('helper_useLightColor')) {
    function helper_useLightColor($bgColor){
        $color = ($bgColor[0] === '#') ? substr($bgColor, 1, 7) : $bgColor;
        $r = intval(substr($color, 0, 2), 16); // hexToR
        $g = intval(substr($color, 2, 4), 16); // hexToG
        $b = intval(substr($color, 4, 6), 16); // hexToB
        //$value = (($r * 0.299) + ($g * 0.587) + ($b * 0.114));
        $value = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;
        return $value > 140;
    }
}

$backgroundColor = wloSubjectType($portalTitle)['color'];
$rgbBackgroundColor = hex2rgb($backgroundColor);
$fontColor = (!empty($backgroundColor) && helper_useLightColor($backgroundColor)) ? "#313131" : "#ffffff";

$GLOBALS['wlo_fachportal'] = array(
        'title' => $portalTitle,
        'backgroundColor' => $backgroundColor,
        'rgbBackgroundColor' => $rgbBackgroundColor
);

// newest contents
$url = WLO_REPO . 'rest/search/v1/queriesV2/-home-/mds_oeh/wlo_collection?contentType=FILES&maxItems=8&skipCount=0&sortProperties=cm%3Amodified&sortAscending=false&propertyFilter=-all-';
$body = '{
  "criterias": [
    {
      "property": "collection",
      "values": [
        "'.$collectionID.'"
      ]
    }
  ],
  "facettes": [
  ]
}';
$newestContent = callWloRestApi($url, 'POST', $body);

$contentArray = array();
if (!empty($newestContent->nodes)){
    foreach ($newestContent->nodes as $reference) {
        $prop = $reference->properties;

        //check if deleted
        if($reference->originalId == null){
            continue;
        }

        $oerLicenses = array('CC_0', 'CC_BY', 'CC_BY_SA', 'PDM');
        $nodeLicense = !empty($prop->{'ccm:commonlicense_key'}[0]) ? $prop->{'ccm:commonlicense_key'}[0] : '';
        $isOER = false;
        foreach ($oerLicenses as $license){
            if( $nodeLicense == $license){
                $isOER = true;
            }
        }

        $contentArray[] = array(
            'id' => $reference->ref->id,
            'image_url' => $reference->preview->url,
            'content_url' => $prop->{'ccm:wwwurl'}[0] ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,
            'title' => $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0],
            'description' => !empty($prop->{'cclom:general_description'}) ? (implode("\n", $prop->{'cclom:general_description'})) : '',
            'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
            'subjects' => !empty($prop->{'ccm:taxonid_DISPLAYNAME'}) ? $prop->{'ccm:taxonid_DISPLAYNAME'} : [],
            'resourcetype' => !empty($prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'}) ? $prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'} : [],
            'educationalcontext' => !empty($prop->{'ccm:educationalcontext_DISPLAYNAME'}) ? $prop->{'ccm:educationalcontext_DISPLAYNAME'} : [],
            'oer' => $isOER,
            'widget' =>  !empty($reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]) ? $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0] : ''
        );
    } //end foreach
}


// get themenseiten-content
$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID . '/children/references?sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
$response = callWloRestApi($url);
$themenseiten_contentArray = array();
$educationalcontextArray = array();
$enduserroleArray = array();
$oerCount = 0;
if (!empty($response->references)){
    foreach ($response->references as $reference) {

        $prop = $reference->properties;

        // check if deleted
        if($reference->originalId == null){
            //echo 'skipped deleted';
            continue;
        }

        $oerLicenses = array('CC_0', 'CC_BY', 'CC_BY_SA', 'PDM');
        $nodeLicense = !empty($prop->{'ccm:commonlicense_key'}[0]) ? $prop->{'ccm:commonlicense_key'}[0] : '';
        $isOER = false;
        foreach ($oerLicenses as $license){
            if( $nodeLicense == $license){
                $isOER = true;
                $oerCount++;
            }
        }

        $themenseiten_contentArray[] = array(
            'id' => $reference->ref->id,
            'image_url' => $reference->preview->url,
            'content_url' => $prop->{'ccm:wwwurl'}[0] ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,
            'title' => $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0],
            //'description' => !empty($prop->{'cclom:general_description'}) ? (implode("\n", $prop->{'cclom:general_description'})) : '',
            'description' => $prop->{'cclom:general_description'}[0] ? $prop->{'cclom:general_description'}[0] : $reference->ref->id,
            'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
            'subjects' => !empty($prop->{'ccm:taxonid_DISPLAYNAME'}) ? $prop->{'ccm:taxonid_DISPLAYNAME'} : [],
            'resourcetype' => !empty($prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'}) ? $prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'} : [],
            'educationalcontext' => !empty($prop->{'ccm:educationalcontext_DISPLAYNAME'}) ? $prop->{'ccm:educationalcontext_DISPLAYNAME'} : [],
            'enduserrole' => !empty($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'}) ? $prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'} : [],
            'oer' => $isOER,
            'widget' =>  !empty($reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]) ? $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0] : '',
            'oeh_lrt' =>  !empty($reference->properties->{'ccm:oeh_lrt'}) ? $reference->properties->{'ccm:oeh_lrt'} : '',
            'added' => false
        );

        if (!empty($prop->{'ccm:educationalcontext_DISPLAYNAME'})){
            foreach ($prop->{'ccm:educationalcontext_DISPLAYNAME'} as $item){
                if (!array_key_exists($item, $educationalcontextArray)) {
                    $educationalcontextArray[$item] = 1;
                }else{
                    $educationalcontextArray[$item] = $educationalcontextArray[$item]+1;
                }
            }
        }
        if (!empty($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'})){
            foreach ($prop->{'ccm:educationalintendedenduserrole_DISPLAYNAME'} as $item){
                if (!array_key_exists($item, $enduserroleArray)) {
                    $enduserroleArray[$item] = 1;
                }else{
                    $enduserroleArray[$item] = $enduserroleArray[$item]+1;
                }
            }
        }

    } //end foreach
}

$GLOBALS['wlo_themenseiten_content'] = $themenseiten_contentArray;

?>

<div class="fachportal-header-block" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">
    <div class="fachportal-header-bar">
        <div class="fachportal-header-bar-wrapper">
            <div class="fachportal-header-bar-tab" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">
                <a style="color: <?php echo $fontColor ?> !important;" href="<?php echo $portalUrl; ?>">Fachportal <?php echo $portalTitle; ?></a>
            </div>
        </div>
    </div>
    <div class="fachportal-header-wrapper">

        <div class="description">

            <div class="description-content">
                <h1 class="title"><?php echo get_the_title($postID); ?></h1>
                <div class="header-description"><?php echo $description; ?></div>
            </div>

            <?php
            $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $collectionID . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
            $subCollections = callWloRestApi($url);
            $filteredSubCollections = [];

            if (!empty($subCollections->collections)){
                foreach ($subCollections->collections as $collection){

                    // Filter hidden collections
                    if ($collection->properties->{'ccm:editorial_state'}[0] !== 'activated' ) {
                        continue;
                    }

                    // Filter educationalContexts
                    if (!empty($educationalContexts)) {
                        if (empty($collection->properties->{'ccm:educationalcontext'})){ // skip empty?
                            //continue;
                        }else{
                            if (!checkPropertyMatch($collection->properties->{'ccm:educationalcontext'}, $educationalContexts, true)) {
                                continue;
                            }
                        }
                    }

                    $filteredSubCollections[] = $collection;
                }
            }

            $maxSubCollections = 6;
            if (get_field('maxSubCollections')) {
                $maxSubCollections = get_field('maxSubCollections');
            }
            ?>
            <div class="collections">
                <?php if (!empty($filteredSubCollections)) : ?>
                    <div class="sub-subjects">
                        <div class="sub-subjects-header">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/categories.svg" alt="Icon: Unterthemen">
                            <h2>Unterthemen <?php echo get_the_title($postID); ?></h2>
                        </div>
                        <div class="sub-subjects-container">
                            <?php foreach (array_slice($filteredSubCollections, 0, $maxSubCollections) as $collection) {
                                $ccm_location = $collection->properties->{'cclom:location'}[0];

                                $title = $collection->title;
                                if (!empty($collection->properties->{'ccm:collectionshorttitle'}[0])){
                                    $title = $collection->properties->{'ccm:collectionshorttitle'}[0];
                                }
                                $ccm_location = str_replace('https://wirlernenonline.de/', 'https://dev.wirlernenonline.de/', $collection->properties->{'cclom:location'}[0]);
                                //$ccm_location = str_replace('https://wirlernenonline.de/', 'https://pre.wirlernenonline.de/', $collection->properties->{'cclom:location'}[0]);?>
                                <div class="sub-subject">
                                    <a href="<?php echo $ccm_location; ?>">
                                        <p><?php echo $title; ?></p>
                                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                    </a>
                                </div>
                            <?php } ?>
                            <?php if (count($filteredSubCollections) > $maxSubCollections): ?>
                                <div class="sub-subject">
                                    <a id="sub-subjects-button" href="#">
                                        <p>mehr anzeigen</p>
                                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg">
                                    </a>
                                </div>

                            <?php endif; ?>
                        </div>
                        <div id="hidden-sub-subjects-container" class="sub-subjects-container">
                            <?php foreach (array_slice($filteredSubCollections, $maxSubCollections) as $collection) {
                                $ccm_location = $collection->properties->{'cclom:location'}[0];
                                $title = $collection->title;
                                if (!empty($collection->properties->{'ccm:collectionshorttitle'}[0])){
                                    $title = $collection->properties->{'ccm:collectionshorttitle'}[0];
                                }
                                ?>
                                <div class="sub-subject">
                                    <a href="<?php echo $ccm_location; ?>">
                                        <p><?php echo $title; ?></p>
                                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>


        <div class="content-stats">
            <div class="header" style="color: <?php echo $fontColor ?> !important;">
                Geprüfte Inhalte
            </div>

            <div class="diagram"  style="background-color:rgba(255, 255, 255, 0.1);">
                <div class="diagram-content">
                    <canvas width="80%" height="80%" id="contentChart"></canvas>
                </div>
            </div>

            <div class="diagram-legend">
                <div class="diagram-legend-entry Wissen" style="color: <?php echo $fontColor ?> !important;">Gut zu Wissen <div class="diagram-legend-color"></div></div>
                <div class="diagram-legend-entry Lerninhalte" style="color: <?php echo $fontColor ?> !important;"><div class="diagram-legend-color"></div> Material</div>
                <div class="diagram-legend-entry Methoden" style="color: <?php echo $fontColor ?> !important;">Unterrichtsplanung <div class="diagram-legend-color"></div></div>
                <div class="diagram-legend-entry Tools" style="color: <?php echo $fontColor ?> !important;"><div class="diagram-legend-color"></div> Software und Tools</div>
            </div>
        </div>


    </div>

    <div class="fachportal-filterbar">

        <div class="fachportal-filterbar-content">

            <div class="portal-breadcrumbs">
                <ul class="portal-breadcrumbs-list">
                    <?php
                    foreach ($breadcrumbs as $node) {
                        echo "<li class='portal-breadcrumbs-list-item'><a href='" . $node[1] . "'>" . $node[0] . "</a><span class='material-icons'>chevron_right</span></li>";
                    }
                    ?>
                </ul>
            </div>

            <div>
                <?php foreach ($educationalcontextArray as $key => $value){ ?>
                    <button onclick="filterContentTiles(this, 'educationalcontext', '<?php echo urlencode($key); ?>')">
                        <div class="fachportal-filterbar-tag">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                            <?php echo $key.' ('.$value.')'; ?>
                        </div>
                    </button>
                <?php } ?>

                <?php foreach ($enduserroleArray as $key => $value){ ?>
                    <button onclick="filterContentTiles(this, 'enduserrole', '<?php echo urlencode($key); ?>')">
                        <div class="fachportal-filterbar-tag">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                            <?php echo $key.' ('.$value.')'; ?>
                        </div>
                    </button>
                <?php } ?>


                <button onclick="filterContentTiles(this, 'oer', 'true')">
                    <div class="fachportal-filterbar-tag">
                        <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                        OER (<?php echo $oerCount; ?>)
                    </div>
                </button>
            </div>
        </div>

    </div>

</div>



<script>

filterContentTiles = ( () => {
    let activeFilters = [];
    function updateTiles(type) {
        if (activeFilters.length === 0) {
            jQuery('.widget-content').show('fast');
            jQuery('.fachportal-content-block').show('fast');
        } else {
            jQuery('.widget-content').hide();
            jQuery('.fachportal-content-block').show();
            activeFilters.forEach((filter) => {
                switch (type) {
                    case "educationalcontext":
                        jQuery('[data-educationalcontext~="' + filter + '"]').show('fast');
                        break;
                    case "enduserrole":
                        jQuery('[data-enduserrole~="' + filter + '"]').show('fast');
                        break;
                    case "oer":
                        jQuery('[data-oer="' + filter + '"]').show('fast');
                        break;
                }
            });
            jQuery('#<?php echo $sliderId?>').slick( 'refresh' );

            jQuery('.slick-track').each(function() {
                if (jQuery(this).find('.widget-content:visible').length == 0){
                    jQuery(this).closest('.fachportal-content-block').hide();
                }
            });
        }
    }
    function setActiveState(button, isActive) {
        if (isActive){
            jQuery(button.querySelector('.fachportal-filterbar-tag')).addClass('active-btn');
        }else {
            jQuery(button.querySelector('.fachportal-filterbar-tag')).removeClass('active-btn');
        }
    }
    function toggleFilter(button, type, filter) {
        if (activeFilters.includes(filter)) {
            activeFilters.splice(activeFilters.indexOf(filter), 1);
            setActiveState(button, false);
        } else {
            activeFilters.push(filter);
            setActiveState(button, true);
        }
        updateTiles(type);
    }
    return toggleFilter;
})()


</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>

