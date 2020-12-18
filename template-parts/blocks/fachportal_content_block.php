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

/* ------------------------------------------------------------------- */

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);

$collectionID = $matches[1][0];

$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $matches[1][0] . '/children/references';
$response = callWloRestApi($url);

$mediaTypes = array(
    "file-image" => "Bild",
    "file-word" => "Word-Dokument",
    "file-excel" => "Tabelle",
    "file-powerpoint" => "Präsentation",
    "file-odt" => "OpenOffice Dokument",
    "file-ods" => "OpenOffice Tabelle",
    "file-odp" => "OpenOffice Präsentation",
    "file-zip" => "Zip-Datei",
    "file-script" => "Skript",
    "file-xml" => "XML-Datei",
    "file-audio" => "Audio",
    "file-txt" => "Textdatei",
    "file-video" => "Video",
    "file-qti" => "ONYX-Datei",
    "file-h5p" => "H5P-Datei",
    "file-pdf" => "PDF-Datei",
    "link" => "Website",
    "folder" => "Ordner",
    "file" => "Unbekannt",
    "collection" => "Sammlung",
    "saved_search" => "Suche"
);

$headline = get_field('contentType')['label'];
if(!empty(get_field('headline'))){
    $headline = get_field('headline');
}

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
if (!empty($response->references)){
    foreach ($response->references as $reference) {
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
        $nodeLicense = $prop->{'ccm:commonlicense_key'}[0];
        $isOER = false;
        foreach ($oerLicenses as $license){
            if( $nodeLicense == $license){
                $isOER = true;
            }
        }

        $contentArray[] = array(
            'id' => $reference->ref->id,
            'mediatype' => $mediaTypes[$reference->mediatype],
            'image_url' => $reference->preview->url,
            'content_url' => $prop->{'ccm:wwwurl'}[0] ? $prop->{'ccm:wwwurl'}[0] : $reference->content->url,
            'title' => $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0],
            'description' => !empty($prop->{'cclom:general_description'}) ? (implode("\n", $prop->{'cclom:general_description'})) : '',
            'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}) ? (implode("\n", $prop->{'cclom:general_description'})) : '',
            'subjects' => $prop->{'ccm:taxonid_DISPLAYNAME'},
            'resourcetype' => $prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'},
            'educationalcontext' => $prop->{'ccm:educationalcontext_DISPLAYNAME'},
            'oer' => $isOER,
            'widget' =>  $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]
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

<div class="fachportal-content-block" id="<?php echo str_replace(' ', '-', get_field('contentType')['label']); ?>">
    <div class="header">
        <h3><?php echo $headline.' ('.count($contentArray).')'; ?></h3>
        <a href="#">Alle ansehen</a>
    </div>

    <div class="content" id="<?php echo $sliderId; ?>">
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
                                    foreach ($content['resourcetype'] as $type){
                                        echo $type.', ';
                                    }
                                    echo '</p>';
                                } ?>
                            </div>
                            <div class="content-meta">
                                <?php if (!empty($content['subjects'])){
                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/subject_icon.svg">';
                                    echo '<p>';
                                    foreach ($content['subjects'] as $subject) {
                                        echo $subject . ', ';
                                    }
                                    echo '</p>';
                                } ?>
                            </div>
                            <div class="content-meta">
                                <?php if (!empty($content['educationalcontext'])){
                                    echo '<img src="'. get_template_directory_uri() .'/src/assets/img/class_icon.svg">';
                                    echo '<p>';
                                    foreach ($content['educationalcontext'] as $subject) {
                                        echo  $subject . ', ';
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
