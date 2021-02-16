<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Inhaltsblock</div>';
} ?>
<?php

require_once(get_template_directory().'/functions/wlo-config.php');

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];
$objectTypes = $educational_filter_values["objectTypes"];
$oehWidgets = $educational_filter_values["oehWidgets"];

$pageTitle = get_the_title($postID);
$pageDiscipline = get_field('discipline', $postID)[0]['label'];

/* ------------------------------------------------------------------- */

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);

$collectionID = $matches[1][0];

//$addContentPageID = 9614; //dev
$addContentPageID = 9933; //pre
//$addContentPageID = 9081; //local

//$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID . '/children/references';
//$response = callWloRestApi($url);

$url = WLO_REPO . 'rest/search/v1/queriesV2/-home-/mds_oeh/wlo_collection?contentType=FILES&maxItems=5000&skipCount=0&propertyFilter=-all-';
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
$response = callWloRestApi($url, 'POST', $body);

$headline = get_field('contentType')['label'];
if(!empty(get_field('headline'))){
    $headline = get_field('headline');
}

//$rgbBackgroundColor = $GLOBALS['wlo_fachportal']['rgbBackgroundColor'];
$rgbBackgroundColor = '255,255,255';

switch (get_field('contentType')['value']){
    case 0: // lerninhalte
        $diagramColor = 'rgba('.$rgbBackgroundColor.', 0.8)';
        break;
    case 1: // tools
        $diagramColor = 'rgba('.$rgbBackgroundColor.', 0.6)';
        break;
    case 2: // methoden
        $diagramColor = 'rgba('.$rgbBackgroundColor.', 0.4)';
        break;
    case 3: // gut zu wissen
        $diagramColor = 'rgba('.$rgbBackgroundColor.', 0.2)';
        break;
    default:
        $diagramColor = 'rgb(250, 250, 250)';

}

$contentArray = array();
if (!empty($response->nodes)){
    foreach ($response->nodes as $reference) {
        $prop = $reference->properties;

        //check if deleted
        if($reference->originalId == null){
            continue;
        }

        // Filter oehWidgets
        $propOehWidgets = $prop->{'ccm:oeh_widgets'};
        $oehWidgetsVocab = array_map("map_vocab_oeh_widgets_value_only", $oehWidgets);
        if (empty( array_intersect($propOehWidgets, $oehWidgetsVocab) ) ) {
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
$showSliderDots = 'true';
if (count($contentArray) <= 4){
    $showSliderDots = 'false';
}

?>

<div class="fachportal-content-block" id="<?php echo str_replace(' ', '-', get_field('contentType')['label']); ?>">
    <div class="header">
        <h2><?php echo $headline.' ('.count($contentArray).')'; ?></h2>
    </div>

    <div class="content" id="<?php echo $sliderId; ?>">
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
                                <img class="badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge_red.svg"  alt="Auszeichnung: geprüfter Inhalt">
                                <?php if ($content['oer']){ ?>
                                    <div class="badge ">OER</div>
                                <?php } ?>
                            </div>
                            <div class="content-title"><?php echo $content['title']; ?></div>
                            <p class="content-description"><?php echo $content['description'] ?></p>
                            <div class="content-meta">
                                <?php if (!empty($content['resourcetype'])){
                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/img_icon.svg" alt="Materialart">';
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
                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/subject_icon.svg" alt="Fächer">';
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
                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/class_icon.svg" alt="Bildungsebene">';
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
        }else{ ?>

        <div class="widget-content no-widget-content">
            <img class="main-image" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/no-content.png" alt="Cover: Keine Inhalte">
            <div class="content-info no-content-info">
                <div class="content-title">Noch kein Inhalt?</div>
                <p class="content-description">Füge Inhalte zu diesem Thema hinzu...</p>
                <a class="content-button no-content-button" href="<?php echo get_page_link($addContentPageID) . '?collectionID=' . $collectionID . '&headline=' . $pageTitle .'&pageDiscipline=' . $pageDiscipline; ?>" target="_blank">
                    <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/plus.svg" alt="Icon: Plus"> Inhalte vorschlagen
                </a>
            </div>
        </div>

        <?php } ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(function () {
        // Handler for .ready() called. Put the Slick Slider etc. init code here.
        function loadSearchSlider() {
            if (typeof jQuery().slick === "function") {
                jQuery('#<?php echo $sliderId?>').not('.slick-initialized').slick({
                    infinite: false,
                    slidesToShow: <?php echo $slidesToShow; ?>,
                    slidesToScroll: <?php echo $slidesToScroll; ?>,
                    arrows: true,
                    dots:  <?php echo $showSliderDots; ?>,
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

        loadSearchSlider();

        jQuery(window).on('resize', function(){
            jQuery('#<?php echo $sliderId?>').slick( 'refresh' );
        });
    });

    jQuery( document ).ready(function() {
        jQuery('#<?php echo str_replace(' ', '-', get_field('contentType')['label']); ?>-count').html('<?php echo count($contentArray); ?>');
        let currentCount = parseInt(jQuery('.diagram-count-total').first().text());
        jQuery( '<div class="diagram-count-total">' + (currentCount + <?php echo count($contentArray); ?>) + '</div>' ).replaceAll( ".diagram-count-total" );


        addData(contentChart, '<?php echo get_field('contentType')['label']; ?>', <?php echo count($contentArray); ?>, '<?php echo $diagramColor; ?>');

    });

</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>
