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
$portal = $parents[count($parents)-3];
$portalTitle = '';
if (!empty($portal->title)){
    $portalTitle = $portal->title;
}
if ($portalTitle == 'Digitalisierung und Medienkompetenz'){
    //$portalTitle = 'Medienkompetenz';
}

//$portalID = get_page_by_title($portalTitle, OBJECT, 'portal')->ID;

$breadcrumbs = Array();
if (!empty($parents)){
    $breadcrumbs_parents = array_slice(array_reverse($parents), 3);
    foreach ($breadcrumbs_parents as $node) {
        if ($node->title == 'Portale'){
            $breadcrumbs[] = ['Fachportale', get_page_link(9930)];
        }else{
            $breadcrumbs[] = [$node->title, str_replace('https://wirlernenonline.de/', 'https://medien.kita.bayern/', $node->properties->{'cclom:location'}[0])];
        }
    }
    //$breadcrumbs = array_reverse($breadcrumbs);
}

$portalUrl = '#';
if (!empty($portal->properties->{'cclom:location'}[0])){
    $portalUrl = $portal->properties->{'cclom:location'}[0];
    $portalUrl = str_replace('https://wirlernenonline.de/', 'https://medien.kita.bayern/', $portalUrl);
    $page = get_page_by_path(basename($portalUrl), OBJECT, 'portal');
    $portalID =$page->ID;
}else{
    $portalID = get_page_by_title($portalTitle, OBJECT, 'portal')->ID;
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

//$backgroundColor = wloSubjectType($portalTitle)['color'];
$backgroundColor = get_field('background_color', $portalID);
$rgbBackgroundColor = hex2rgb($backgroundColor);
$fontColor = (!empty($backgroundColor) && helper_useLightColor($backgroundColor)) ? "#313131" : "#ffffff";

$GLOBALS['wlo_fachportal'] = array(
        'title' => $portalTitle,
        'backgroundColor' => $backgroundColor,
        'rgbBackgroundColor' => $rgbBackgroundColor
);


$accordionID = uniqid();
$zmfContentID = uniqid('zmf-content-');
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
                <a style="color: <?php echo $fontColor ?> !important;" href="<?php echo $portalUrl; ?>"><?php echo $portalTitle; ?></a>
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
                                //$ccm_location = str_replace('https://wirlernenonline.de/', 'https://dev.wirlernenonline.de/', $collection->properties->{'cclom:location'}[0]);
                                $ccm_location = str_replace('https://wirlernenonline.de/', 'https://medien.kita.bayern/', $collection->properties->{'cclom:location'}[0]);?>
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

                                $ccm_location = str_replace('https://wirlernenonline.de/', 'https://medien.kita.bayern/', $collection->properties->{'cclom:location'}[0]);

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
            <!--
            <div class="header" style="color: <?php /*echo $fontColor */?> !important;">
                Geprüfte Inhalte
            </div>

            <div class="diagram"  style="background-color:rgba(255, 255, 255, 0.1);">
                <div class="diagram-content">
                    <canvas width="80%" height="80%" id="contentChart"></canvas>
                </div>
            </div>

            <div class="diagram-legend">
                <div class="diagram-legend-entry Wissen" style="color: <?php /*echo $fontColor */?> !important;">Gut zu Wissen <div class="diagram-legend-color"></div></div>
                <div class="diagram-legend-entry Lerninhalte" style="color: <?php /*echo $fontColor */?> !important;"><div class="diagram-legend-color"></div> Material</div>
                <div class="diagram-legend-entry Methoden" style="color: <?php /*echo $fontColor */?> !important;">Unterrichtsplanung <div class="diagram-legend-color"></div></div>
                <div class="diagram-legend-entry Tools" style="color: <?php /*echo $fontColor */?> !important;"><div class="diagram-legend-color"></div> Software und Tools</div>
            </div>
            -->
        </div>


        <div class="header-bottom"></div>
    </div>
</div>

<div class="fachportal-header-block fachportal-new-content">
    <div class="fachportal-header-wrapper" >
        <div class="fachportal-new-content-inner" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
            <button class="fachportal-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
                <h2>Die neusten geprüften Inhalte für dich!</h2>
                <img class="fachportal-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Die neusten Inhalte ein odder ausklappen">
            </button>

            <div class="content fachportal-accordion-content zmf-accordion-content" id="<?php echo $zmfContentID; ?>">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </div>

            <script type="text/javascript" >
                jQuery(document).ready(function($) {

                    var data = {
                        'action': 'zmf_content_block',
                        'collectionID': '<?php echo $collectionID; ?>',
                        'maxItems': '11',
                        'skipCount': '0',
                    };

                    jQuery.post(ajaxurl, data, function(response) {
                        jQuery('#<?php echo $zmfContentID; ?>').html(response);

                        jQuery('#<?php echo $zmfContentID; ?>').append('<div class="lds-ring"><div></div><div></div><div></div><div></div></div>');

                        var data2 = {
                            'action': 'zmf_content_block',
                            'collectionID': '<?php echo $collectionID; ?>',
                            'maxItems': '500',
                            'skipCount': '11',
                        };

                        jQuery.post(ajaxurl, data2, function(response) {
                            jQuery('.lds-ring').remove();
                            jQuery('#<?php echo $zmfContentID; ?>').append(response);

                            new Freezeframe({
                                overlay: true,
                                warnings: false
                            });
                        });

                        new Freezeframe({
                            overlay: true,
                            warnings: false
                        });
                    });

                });

            </script>

        </div>

        <div class="header-bottom"></div>
    </div>
</div>

<script>
    /*
    function addData(chart, label, data, color, index) {
        //chart.data.labels.push(label);
        chart.data.labels[index] = label;
        chart.data.datasets.forEach((dataset) => {
            dataset.data[index] = data;
            dataset.backgroundColor[index] = color;
            dataset.borderWidth[index] = 0;
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
                jQuery('#<?php echo $zmfContentID?>').not('.slick-initialized').slick({
                    infinite: false,
                    slidesToShow: <?php echo $slidesToShow; ?>,
                    slidesToScroll: <?php echo $slidesToScroll; ?>,
                    arrows: true,
                    dots: true,
                    zIndex: 0,
                    responsive: [
                        {
                            breakpoint: 1230,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3
                            }
                        },
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

        //loadSlider();

        jQuery(window).on('resize', function(){
            //jQuery('#<?php echo $zmfContentID?>').slick( 'refresh' );
        });
    });

*/

    jQuery('#fachportal-accordion-<?php echo $accordionID; ?>').click(function(){
        jQuery(this).find("img").toggleClass("fachportal-accordion-icon-active");
        jQuery('#<?php echo $zmfContentID; ?>').slideToggle('slow');
        jQuery('#<?php echo $zmfContentID?>').slick( 'refresh' );
    });

    jQuery( "#sub-subjects-button" ).click(function() {
        jQuery('#hidden-sub-subjects-container').slideToggle('medium', function() {
            if (jQuery(this).is(':visible')){
                jQuery(this).css('display','flex');
            }
        });
        jQuery('#sub-subjects-button').hide();
    });

    jQuery( document ).ready(function() {
        //addData(contentChart, 'Tools', 25, 'rgba(255,255,255,0.8)');
        //addData(contentChart, 'Gut zu Wissen', 5, 'rgba(255,255,255,0.2)');
      /*  new Freezeframe({
            overlay: true,
            warnings: false
        });*/
    });

</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>

