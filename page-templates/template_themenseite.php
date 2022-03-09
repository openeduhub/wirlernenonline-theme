<?php
/*
Template Name: WLO-Themenseite
Template Post Type: post, page, portal
 */

get_header();
?>
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
$pageDiscipline = get_field('discipline', $postID)[0]['label'];

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

$addContentUrl = get_page_link(9933) . '?collectionID=' . $collectionID . '&headline=' . get_the_title($postID) .'&pageDiscipline=' . $pageDiscipline;


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
            //'resourcetype' => !empty($prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'}) ? $prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'} : [],
            'resourcetype' => !empty($prop->{'ccm:oeh_lrt_aggregated_DISPLAYNAME'}) ? $prop->{'ccm:oeh_lrt_aggregated_DISPLAYNAME'} : [],
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



while (have_posts()) : the_post(); ?>

    <div class="portal">

        <div class="fachportal-header-bar" <?php if (is_admin_bar_showing()){echo 'style="top:32px"';} ?>>
            <div class="fachportal-header-bar-wrapper">
                <div class="fachportal-header-bar-tab" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">
                    <a style="color: <?php echo $fontColor ?> !important;" href="<?php echo $portalUrl; ?>">Fachportal <?php echo $portalTitle; ?></a>
                </div>
                <div class="portal-breadcrumbs">
                    <ul class="portal-breadcrumbs-list">
                        <?php
                        foreach ($breadcrumbs as $node) {
                            echo "<li class='portal-breadcrumbs-list-item'><a href='" . $node[1] . "'>" . $node[0] . "</a><span class='material-icons'>chevron_right</span></li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="portal-wrapper-header">
            <div class="fachportal-header-block" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">

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
                            Inhalte
                        </div>

                        <div class="diagram">
                            <div class="diagram-content">
                                <canvas width="80%" height="80%" id="contentChart"></canvas>
                            </div>
                        </div>

                        <div class="diagram-legend">
                            <div class="diagram-legend-entry Redaktionell" style="color: <?php echo $fontColor ?> !important;">
                                <div class="diagram-legend-color"></div> Redaktionell geprüfte Inhalte
                            </div>
                            <div class="diagram-legend-entry Maschinell" style="color: <?php echo $fontColor ?> !important;">
                                <div class="diagram-legend-color"></div> Maschinell erschlossene Inhalte in der Suche
                            </div>
                        </div>


                    </div>


                </div>



            </div>
        </div>

        <div class="fachportal-filterbar" <?php if (is_admin_bar_showing()){echo 'style="top:80px"';} ?>>

            <div class="fachportal-filterbar-content">

                <div>

                    <div class="fachportal-filterbar-dropdowns">
                        <select name="educationalcontext" id="educationalcontext" onchange="filterContentTiles(this, 'educationalcontext', this.value)">
                            <option value="" selected disabled hidden>Bildungsstufe</option>
                            <?php foreach ($educationalcontextArray as $key => $value){ ?>
                                <option value="<?php echo preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key)); ?>">
                                    <?php echo $key.' ('.$value.')'; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <select name="enduserrole" id="enduserrole" onchange="filterContentTiles(this, 'enduserrole', this.value)">
                            <option value="" selected disabled hidden>Zielgruppe</option>
                            <?php foreach ($enduserroleArray as $key => $value){ ?>
                                <option value="<?php echo preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key)); ?>">
                                    <?php echo $key.' ('.$value.')'; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <select name="oer" id="oer" onchange="filterContentTiles(this, 'oer', this.value)">
                            <option value="" selected disabled hidden>OER</option>
                            <option value="oer">
                                OER (<?php echo $oerCount; ?>)
                            </option>
                            <option value="no-oer">
                                Kein OER
                            </option>
                        </select>
                    </div>



                    <?php foreach ($educationalcontextArray as $key => $value){
                        $id = preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key));
                        ?>
                        <button id="filter-tag-<?php echo $id; ?>" onclick="filterContentTiles(this, 'educationalcontext', '<?php echo $id; ?>')">
                            <div class="fachportal-filterbar-tag">
                                <?php echo $key; ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/close.svg"  alt="">
                            </div>
                        </button>
                    <?php } ?>

                    <?php foreach ($enduserroleArray as $key => $value){
                        $id = preg_replace('/[^a-zA-Z0-9-_]/', '-', urlencode($key));
                        ?>
                        <button id="filter-tag-<?php echo $id; ?>" onclick="filterContentTiles(this, 'enduserrole', '<?php echo $id; ?>')">
                            <div class="fachportal-filterbar-tag">
                                <?php echo $key; ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/close.svg"  alt="">
                            </div>
                        </button>
                    <?php } ?>


                    <button id="filter-tag-oer" onclick="filterContentTiles(this, 'oer', 'oer')">
                        <div class="fachportal-filterbar-tag">
                            OER
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/close.svg"  alt="">
                        </div>
                    </button>

                    <button id="filter-tag-no-oer" onclick="filterContentTiles(this, 'oer', 'no-oer')">
                        <div class="fachportal-filterbar-tag">
                            OER
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/close.svg"  alt="">
                        </div>
                    </button>
                </div>
            </div>

        </div>

        <div class="portal-wrapper portal-wrapper-themenseite">
            <div class="portal-wrapper-left">
                <div class="fachportal-header-accordion">

                    <div class="wlo-accordion-wrapper" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
                        <button class="wlo-accordion" id="wlo-accordion-<?php echo $accordionID; ?>">
                            <h2>Die neusten geprüften Inhalte für dich!</h2>
                            <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Inhalte ein odder ausklappen">
                        </button>

                        <div class="wlo-accordion-content" id="wlo-accordion-content-<?php echo $sliderId; ?>">

                            <?php
                            if (!empty($contentArray)){
                                foreach (array_slice($contentArray, 0, get_field('content_count')) as $content) { ?>
                                    <div class="widget-content <?php if (!empty($content['resourcetype'])){ foreach ($content['resourcetype'] as $type){ echo $type.' '; } } ?>">


                                        <?php if (!empty($content['image_url'])) { ?>
                                            <img class="main-image" src="<?php echo $content['image_url']; ?>" alt="Cover: <?php echo $content['title']; ?>">
                                        <?php } ?>
                                        <div class="content-info">
                                            <div class="content-header">
                                                <?php if ($content['source']){ ?>
                                                    <p class="content-source"><?php echo $content['source']; ?></p>
                                                <?php } ?>
                                                <img class="badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge_green.svg"  alt="Auszeichnung: geprüfter Inhalt">
                                                <?php if ($content['oer']){ ?>
                                                    <div class="badge ">OER</div>
                                                <?php } ?>
                                            </div>
                                            <div class="content-title"><?php echo $content['title']; ?></div>
                                            <p class="content-description"><?php echo $content['description'] ?></p>
                                            <div class="content-meta">
                                                <?php if (!empty($content['resourcetype'])){
                                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/img_icon.svg"  alt="Materialart">';
                                                    echo '<p>';
                                                    $i = 0;
                                                    foreach ($content['resourcetype'] as $type){
                                                        if(++$i === count($content['resourcetype'])) {
                                                            echo $type;
                                                        }else{
                                                            echo $type.', ';
                                                        }
                                                    }
                                                    echo '</p>';
                                                } ?>
                                            </div>
                                            <div class="content-meta">
                                                <?php if (!empty($content['subjects'])){
                                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/subject_icon.svg"  alt="Fächer">';
                                                    echo '<p>';
                                                    $i = 0;
                                                    foreach ($content['subjects'] as $subject) {
                                                        if(++$i === count($content['subjects'])) {
                                                            echo $subject;
                                                        }else{
                                                            echo $subject.', ';
                                                        }
                                                    }
                                                    echo '</p>';
                                                } ?>
                                            </div>
                                            <div class="content-meta">
                                                <?php if (!empty($content['educationalcontext'])){
                                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/class_icon.svg"  alt="Bildungsebene">';
                                                    echo '<p>';
                                                    $i = 0;
                                                    foreach ($content['educationalcontext'] as $subject) {
                                                        if(++$i === count($content['educationalcontext'])) {
                                                            echo $subject;
                                                        }else{
                                                            echo $subject.', ';
                                                        }
                                                    }
                                                    echo '</p>';
                                                } ?>
                                            </div>

                                            <a class="content-button" href="<?php echo $content['content_url']; ?>" target="_blank" aria-label="Zum-Inhalt: <?php echo $content['title']; ?>">Zum Inhalt</a>

                                        </div>


                                    </div>
                                <?php }
                            } ?>
                        </div>

                        <div class="wlo-accordion-bottom"></div>

                    </div>
                </div>

            <?php the_content(); ?>

            </div>
            <div class="portal-wrapper-right">
                <div class="detail-view-popup">
                    <script>
                        window.__env = {
                            EDU_SHARING_API_URL: '<?php echo WLO_REPO; ?>/rest',
                        };
                    </script>
                    <oeh-details-embedded></oeh-details-embedded>
                    <script>
                        document.getElementsByTagName('oeh-details-embedded')[0].addEventListener('closed', () => {
                            console.log('received closed event');
                            jQuery(".portal-wrapper-right").hide('slow');
                            jQuery(".detail-view-popup").hide('slow');
                        });
                    </script>
                </div>

                <div class="no-content-popup">
                    <div class="close-no-content-popup">X</div>
                    <h3>Mitmachen!</h3>
                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/mitmachen-popup.png">
                    <p>
                        Das Projekt funktioniert ähnlich wie Wikipedia: Alle können beitragen und alle profitieren davon!
                        Mitmach-Aktivitäten sind zum Beispiel die Verbesserung der Suche, die Ergänzung von Links, die Bewertung
                        von Materialien, das Melden von Fehlern oder auch tatkräftig die Fachredaktionen zu unterstützen.
                    </p>
                    <a class="content-button no-content-button" href="<?php echo $addContentUrl; ?>" target="_blank">
                        Neuen Inhalt hinzufügen
                    </a>
                </div>

            </div>
        </div>

    </div>

    <script>
        function showContentPopup(nodeID){
            document.getElementsByTagName("oeh-details-embedded")[0].setAttribute("node-id", nodeID);
            jQuery(".no-content-popup").hide();
            jQuery(".detail-view-popup").css('display', 'flex');
            jQuery(".portal-wrapper-right").show('slow');
        }

        jQuery(".fachportal-content-popup").click(function(e) {
            // Do something
            e.stopPropagation();
        });

        jQuery(".fachportal-content-popup").click(function(){
            //jQuery(".portal-wrapper-right").hide();
        });

        function showNoContentPopup(){
            jQuery(".detail-view-popup").hide();
            jQuery(".no-content-popup").css('display', 'flex');;
            jQuery(".portal-wrapper-right").show('slow');
        }

        jQuery(".close-no-content-popup").click(function(){
            jQuery(".portal-wrapper-right").hide('slow');
            jQuery(".no-content-popup").hide('slow');
        });
    </script>

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

                    jQuery('.slick-track').each(function() {
                        if (jQuery(this).find('.widget-content:visible').length == 0){
                            jQuery(this).closest('.fachportal-content-block').hide();
                        }
                    });
                }
            }
            function setActiveState(filter, isActive) {
                if (isActive){
                    console.log('activate-button:'+filter);
                    let button = jQuery('#filter-tag-'+filter);
                    console.log(button);
                    jQuery('#filter-tag-'+filter).find('.fachportal-filterbar-tag').addClass('active-btn');
                }else {
                    jQuery('#filter-tag-'+filter).find('.fachportal-filterbar-tag').removeClass('active-btn');
                }
            }
            function toggleFilter(button, type, filter) {
                console.log('toggleFilter');
                if (activeFilters.includes(filter)) {
                    activeFilters.splice(activeFilters.indexOf(filter), 1);
                    setActiveState(filter, false);
                } else {
                    activeFilters.push(filter);
                    setActiveState(filter, true);
                }
                updateTiles(type);
            }
            return toggleFilter;
        })()


    </script>


    </div>
<?php
endwhile;
get_footer();
