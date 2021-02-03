<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Header</div>';
} ?>

<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);
$collectionID = $matches[1][0];

$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID;
$response = callWloRestApi($url);

if (empty($response->collection->properties->{'cm:description'}[0])){
    $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
}else{
    $description = $response->collection->properties->{'cm:description'}[0];
}


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

$backgroundColor = wloSubjectColors($portalTitle);
$rgbBackgroundColor = hex2rgb($backgroundColor);
$fontColor = (!empty($backgroundColor) && helper_useLightColor($backgroundColor)) ? "#313131" : "#ffffff";

$GLOBALS['wlo_fachportal'] = array(
        'title' => $portalTitle,
        'backgroundColor' => $backgroundColor,
        'rgbBackgroundColor' => $rgbBackgroundColor
) ;


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
$sliderId = uniqid('slider-');
$slidesToShow = 4;
$slidesToScroll = 4;
if (get_field('slidesToShow')) {
    $slidesToShow = get_field('slidesToShow');
}
if (get_field('slidesToScroll')) {
    $slidesToScroll = get_field('slidesToScroll');
}

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
            $maxSubCollections = 6;
            ?>
            <div class="collections">
                <?php if (!empty($subCollections->collections)) : ?>
                    <div class="sub-subjects">
                        <div class="sub-subjects-header">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/categories.svg">
                            <h2>Unterthemen <?php echo get_the_title($postID); ?></h2>
                            <?php if (count($subCollections->collections) > $maxSubCollections): ?>
                                <img id="sub-subjects-button" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg">
                            <?php endif; ?>
                        </div>
                        <div class="sub-subjects-container">
                            <?php foreach (array_slice($subCollections->collections, 0, $maxSubCollections) as $collection) {?>
                                <div class="sub-subject">
                                    <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>">
                                        <p><?php echo $collection->title; ?></p>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                        <div id="hidden-sub-subjects-container" class="sub-subjects-container">
                            <?php foreach (array_slice($subCollections->collections, $maxSubCollections) as $collection) { ?>
                                <div class="sub-subject">
                                    <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>">
                                        <p><?php echo $collection->title; ?></p>
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
                <div class="diagram-legend-entry Lerninhalte" style="color: <?php echo $fontColor ?> !important;"><div class="diagram-legend-color"></div> Lerninhalte</div>
                <div class="diagram-legend-entry Methoden" style="color: <?php echo $fontColor ?> !important;">Methoden <div class="diagram-legend-color"></div></div>
                <div class="diagram-legend-entry Tools" style="color: <?php echo $fontColor ?> !important;"><div class="diagram-legend-color"></div> Tools</div>
            </div>
        </div>


        <div class="header-bottom"></div>
    </div>
</div>

<div class="fachportal-header-block fachportal-new-content">
    <div class="fachportal-header-wrapper" >
        <div class="fachportal-new-content-inner" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
            <div class="fachportal-accordion">
                <h2>Die neusten geprüften Inhalte für dich!</h2>
                <img class="fachportal-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg">
            </div>

            <div class="content fachportal-accordion-content" id="<?php echo $sliderId; ?>">
                <?php
                if (!empty($contentArray)){
                    foreach (array_slice($contentArray, 0, get_field('content_count')) as $content) { ?>
                        <div class="widget-content <?php if (!empty($content['resourcetype'])){ foreach ($content['resourcetype'] as $type){ echo $type.' '; } } ?>">


                            <?php if (!empty($content['image_url'])) { ?>
                                <img class="main-image" src="<?php echo $content['image_url']; ?> alt="">
                            <?php } ?>
                            <div class="content-info">
                                <div class="content-header">
                                    <?php if ($content['source']){ ?>
                                        <p class="content-source"><?php echo $content['source']; ?></p>
                                    <?php } ?>
                                    <img class="badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge_red.svg">
                                    <?php if ($content['oer']){ ?>
                                        <div class="badge ">OER</div>
                                    <?php } ?>
                                </div>
                                <div class="content-title"><?php echo $content['title']; ?></div>
                                <p class="content-description"><?php echo $content['description'] ?></p>
                                <div class="content-meta">
                                    <?php if (!empty($content['resourcetype'])){
                                        echo '<img src="'. get_template_directory_uri() .'/src/assets/img/img_icon.svg">';
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
                                        echo '<img src="'. get_template_directory_uri() .'/src/assets/img/subject_icon.svg">';
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
                                        echo '<img src="'. get_template_directory_uri() .'/src/assets/img/class_icon.svg">';
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

                                <a class="content-button" href="<?php echo $content['content_url']; ?>" target="_blank">Zum Inhalt</a>

                            </div>


                        </div>
                    <?php }
                } ?>
            </div>
        </div>

        <div class="header-bottom"></div>
    </div>
</div>

<script>
    function addData(chart, label, data, color) {
        chart.data.labels.push(label);
        chart.data.datasets.forEach((dataset) => {
            dataset.data.push(data);
            dataset.backgroundColor.push(color);
            dataset.borderWidth.push(0);
        });
        chart.update();
    }

    const ctx = document.getElementById('contentChart').getContext('2d');
    let contentChart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'polarArea',
        //type: 'pie',
        //type: 'doughnut',

        // The data for our dataset
        data: {
            datasets: [{
                data: [],
                backgroundColor: [],
                borderAlign:[],
                borderColor: [],
                borderWidth: []
            }],
            labels: [],

        },

        // Configuration options go here
        options: {
            scale: {
                display: false
            },
            legend: {
                display: false
            }
        }
    });

    jQuery(function () {
        // Handler for .ready() called. Put the Slick Slider etc. init code here.
        function loadSlider() {
            if (typeof jQuery().slick === "function") {
                jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                    infinite: false,
                    slidesToShow: <?php echo $slidesToShow; ?>,
                    slidesToScroll: <?php echo $slidesToScroll; ?>,
                    arrows: true,
                    dots: true,
                    zIndex: 0,
                    responsive: [
                        {
                            breakpoint: 950,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 750,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            }
        }

        loadSlider();

        jQuery(window).on('resize', function(){
            jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
        });
    });

    jQuery(window).on('resize', function(){
        jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
    });



    var accordion = document.getElementsByClassName("fachportal-accordion-icon");
    var i;

    for (i = 0; i < accordion.length; i++) {
        accordion[i].addEventListener("click", function() {
            this.classList.toggle("fachportal-accordion-active");
            jQuery('#<?php echo $sliderId?>').toggle('slow');
            jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
        });
    }

    jQuery( "#sub-subjects-button" ).click(function() {
        jQuery('#hidden-sub-subjects-container').slideToggle('medium', function() {
            if (jQuery(this).is(':visible')){
                jQuery(this).css('display','flex');
            }
        });
        jQuery('#sub-subjects-button').toggleClass('sub-subjects-button-active');
    });

    jQuery( document ).ready(function() {

        //addData(contentChart, 'Tools', 25, 'rgba(255,255,255,0.8)');
        //addData(contentChart, 'Gut zu Wissen', 5, 'rgba(255,255,255,0.2)');

    });

</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>

