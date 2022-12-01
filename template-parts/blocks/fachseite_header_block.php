<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachseite: Header</div>';
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

$portalTitle = get_the_title($postID);

$author_ids = get_field('authors', $postID);
if (empty($author_ids)){
    $author_ids = array();
}

$educationalContexts = $educational_filter_values["educationalContexts"];

if (empty(get_field('description'))){
    $description = '
                    Herzlich willkommen auf der Fachseite für '.$portalTitle.'! <br>
                    <br>
                    Du findest hier neben von unserer Redaktion sorgfältig ausgewählten Materialien für den 
                    Präsenzunterricht, den Online-Unterricht oder das hybride Klassenzimmer auch Informationen zu 
                    Events, Fortbidungsangeboten und zum Neusten aus '.$portalTitle.'. Über “unsere Themen” kannst du auch 
                    tiefer in Lehrplanthemen eintauchen und spezielle Materialien finden. Lass dich inspirieren!<br>
                    <br>
                    Für jeden und jede ist etwas dabei und es soll noch viel mehr werden – dafür brauchen wir deine 
                    Unterstützung, <a href="'.get_page_link(97).'">werde Teil der Community</a>! Du kannst in Redaktionen mitarbeiten und eigene Inhalte 
                    hochladen und der Community zur Verfügung stellen.
    ';
}else{
    $description = get_field('description');
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
$accordionID = uniqid();

$GLOBALS['wlo_fachportal'] = array(
    'title' => $portalTitle,
    'backgroundColor' => $backgroundColor,
    'rgbBackgroundColor' => $rgbBackgroundColor
);
?>

<div class="fachportal-header-block" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">
    <div class="fachportal-header-bar">
        <div class="fachportal-header-bar-wrapper">

            <div class="portal-breadcrumbs">

                <ul class="portal-breadcrumbs-list">
                    <li class='portal-breadcrumbs-list-item'><a href='<?php echo get_page_link(55115); ?>'>Fachportale</a><span class='material-icons'>chevron_right</span></li>
                    <li class='portal-breadcrumbs-list-item'><a href='<?php echo get_page_link(); ?>'><?php echo $portalTitle; ?></a><span class='material-icons'>chevron_right</span></li>
                </ul>
            </div>


            <div class="wlo-tile-team">
                <p>Redaktion <?php echo $portalTitle; ?>:</p>
                <?php foreach ($author_ids as $author_id) {
                    echo $author_id['user_avatar'];
                } ?>
                <img class="wlo-team-bookmark" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/Bookmark.svg">
            </div>

        </div>
    </div>
    <div class="fachportal-header-wrapper">
        <div class="description">
            <div class="description-content">
                <h1 class="title"><?php echo $portalTitle; ?></h1>
                <div class="header-description header-description-mobile"><?php echo $description; ?></div>
                <?php if (strlen($description) >= 190){ ?>
                    <button class="header-description-button" onclick="toggleDescription(this)">
                        <div>
                            <span>Mehr</span>
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg" alt="">
                        </div>
                    </button>
                <?php } ?>
                <?php if (false): ?>
                    <div class="educational-context-filter">
                        <button onclick="educationalcontextFilter('grundschule')">
                            <div class="wlo-portals-filter-tag <?php if (in_array('grundschule', $educationalContexts)){echo 'active-btn';} ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Primarstufe
                            </div>
                        </button>
                        <button onclick="educationalcontextFilter('sekundarstufe_1')">
                            <div class="wlo-portals-filter-tag <?php if (in_array('sekundarstufe_1', $educationalContexts)){echo 'active-btn';} ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Sekundarstufe 1
                            </div>
                        </button>
                        <button onclick="educationalcontextFilter('sekundarstufe_2')">
                            <div class="wlo-portals-filter-tag <?php if (in_array('sekundarstufe_2', $educationalContexts)){echo 'active-btn';} ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Sekundarstufe 2
                            </div>
                        </button>
                        <button onclick="educationalcontextFilter('berufliche_bildung')">
                            <div class="wlo-portals-filter-tag <?php if (in_array('berufliche_bildung', $educationalContexts)){echo 'active-btn';} ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/checkmark.svg"  alt="">
                                Berufliche Bildung
                            </div>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="content-stats"></div>
        <div class="header-bottom" style="background-color: #F4F4F4"></div>
    </div>
</div>

<div class="fachportal-header-block fachportal-new-content">
    <div class="fachportal-header-wrapper" >
        <div class="fachportal-new-content-inner" style="background-color: #F4F4F4">
            <button class="fachportal-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
                <h2>Durchstöbere hier unsere Lehrplanthemen</h2>
                <img class="fachportal-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Inhalte ein odder ausklappen">
            </button>
            <div class="fachportal-accordion-content" id="fachportal-accordion-content-<?php echo $accordionID; ?>">
                <?php
                //$url = WLO_REPO . 'rest/collection/v1/collections/local/' . $collectionID . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
                $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $collectionID . '/children/collections?scope=MY&skipCount=0&maxItems=1247483647&sortProperties=cm%3Aname&sortAscending=true';
                $subCollections = callWloRestApi($url);
                ?>
                <div class="collections">
                    <?php if (!empty($subCollections->collections)) : ?>
                        <div class="sub-subjects">
                            <div class="sub-subjects-container">
                                <?php foreach ($subCollections->collections as $collection) {
                                    $ccm_location = wlo_convert_dev_url($collection->properties->{'cclom:location'}[0]);

                                    //$ccm_location = str_replace('https://wirlernenonline.de/', 'https://dev.wirlernenonline.de/', $collection->properties->{'cclom:location'}[0]);
                                    //$ccm_location = str_replace('https://wirlernenonline.de/', 'https://pre.wirlernenonline.de/', $collection->properties->{'cclom:location'}[0]);

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

                                    $title = $collection->title;
                                    if (!empty($collection->properties->{'ccm:collectionshorttitle'}[0])){
                                        $title =$collection->properties->{'ccm:collectionshorttitle'}[0];
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

    function toggleDescription(button){
        if (jQuery(button).closest('.description-content').find('.header-description').height() <= '145'){
            jQuery(button).closest('.description-content').find('.header-description').css('max-height', 1000);
            jQuery(button).closest('.description-content').find('.header-description-button div img').css("transform", "rotate(180deg)");
            jQuery(button).closest('.description-content').find('.header-description-button div span').html("Weniger");
        }else {
            //jQuery(button).closest('.wlo-partner').find('.wlo-partner-text').height('74');
            jQuery(button).closest('.description-content').find('.header-description').css('max-height', '9em');
            jQuery(button).closest('.description-content').find('.header-description-button div img').css("transform", "rotate(0deg)");
            jQuery(button).closest('.description-content').find('.header-description-button div span').html("Mehr");
        }
    }

</script>


<?php if (is_admin()) {
    echo '</div>';
} ?>

