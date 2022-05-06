<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachseite: Inhalt des Monats</div>';
} ?>


<?php
$accordionID = uniqid();
$headline = 'Inhalt des Monats';
if (get_field('headline')) {
    $headline = get_field('headline');
}

$featureHeadline = 'Inhalt des Monats';
if (get_field('headline')) {
    $headline = get_field('headline');
}

$rgbBackgroundColor = '255,255,255';
if(!empty($GLOBALS['wlo_fachportal']['rgbBackgroundColor'])){
    $rgbBackgroundColor = $GLOBALS['wlo_fachportal']['rgbBackgroundColor'];
}

$nodeID = basename(get_field('nodeID'));

if(!empty($nodeID)){
    $url = WLO_REPO . 'rest/node/v1/nodes/-home-/' . $nodeID . '/metadata?propertyFilter=-all-';
    //$url = 'https://redaktion.openeduhub.net/edu-sharing/rest/node/v1/nodes/-home-/f4e6d2f7-57f3-443c-9a0b-2ead1e41a363/metadata?propertyFilter=-all-';
    $node = callWloRestApi($url)->node;

    //var_dump($node);

    $prop = $node->properties;

    $oerLicenses = array('CC_0', 'CC_BY', 'CC_BY_SA', 'PDM');
    $nodeLicense = !empty($prop->{'ccm:commonlicense_key'}[0]) ? $prop->{'ccm:commonlicense_key'}[0] : '';
    $isOER = false;
    foreach ($oerLicenses as $license){
        if( $nodeLicense == $license){
            $isOER = true;
        }
    }

    $nodeContent = array(
        'id' => $node->ref->id,
        'image_url' => $node->preview->url,
        'content_url' => $prop->{'ccm:wwwurl'}[0] ? $prop->{'ccm:wwwurl'}[0] : $node->content->url,
        'title' => $prop->{'cclom:title'}[0] ? $prop->{'cclom:title'}[0] : $prop->{'cm:name'}[0],
        'description' => !empty($prop->{'cclom:general_description'}) ? (implode("\n", $prop->{'cclom:general_description'})) : '',
        'source' => !empty($prop->{'ccm:metadatacontributer_creatorFN'}[0]) ? $prop->{'ccm:metadatacontributer_creatorFN'}[0] : '',
        'subjects' => !empty($prop->{'ccm:taxonid_DISPLAYNAME'}) ? $prop->{'ccm:taxonid_DISPLAYNAME'} : [],
        'resourcetype' => !empty($prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'}) ? $prop->{'ccm:educationallearningresourcetype_DISPLAYNAME'} : [],
        'educationalcontext' => !empty($prop->{'ccm:educationalcontext_DISPLAYNAME'}) ? $prop->{'ccm:educationalcontext_DISPLAYNAME'} : [],
        'oer' => $isOER,
        'widget' =>  !empty($reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0]) ? $reference->properties->{'ccm:oeh_widgets_DISPLAYNAME'}[0] : ''
    );

    //var_dump($nodeContent);
}

?>

<div class="wlo-accordion-wrapper" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
    <button class="wlo-accordion" id="wlo-accordion-<?php echo $accordionID; ?>">
        <h2><?php echo $headline; ?></h2>
        <img class="wlo-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg"  alt="Inhalte ein odder ausklappen">
    </button>

    <div class="wlo-accordion-content" id="wlo-accordion-content-<?php echo $accordionID; ?>">

        <?php if(!empty($nodeID)){ ?>
        <a class="wlo-feature-link" href="<?php echo $nodeContent['content_url']; ?>" target="_blank">
            <div class="wlo-feature-wrapper">
                <div class="wlo-feature-img">
                    <img src="<?php echo $nodeContent['image_url']; ?>" alt="Cover: <?php echo $nodeContent['title']; ?>">
                </div>
                <div class="wlo-feature-text">
                    <div class="content-header">
                        <?php if ($nodeContent['source']){ ?>
                            <p class="content-source"><?php echo $nodeContent['source']; ?></p>
                        <?php } ?>
                        <img class="badge" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/badge_green.svg"  alt="Auszeichnung: geprüfter Inhalt">
                        <?php if ($nodeContent['oer']){ ?>
                            <div class="badge ">OER</div>
                        <?php } ?>
                    </div>
                    <div class="content-title"><?php echo $nodeContent['title']; ?></div>
                    <p class="content-description"><?php echo $nodeContent['description'] ?></p>
                    <div class="content-meta">
                        <?php if (!empty($nodeContent['resourcetype'])){
                            echo '<img src="'. get_template_directory_uri() .'/src/assets/img/img_icon.svg" alt="Materialart">';
                            echo '<p>';
                            $i = 0;
                            foreach ($nodeContent['resourcetype'] as $type){
                                if(++$i === count($nodeContent['resourcetype'])) {
                                    echo $type;
                                }else{
                                    echo $type.', ';
                                }
                            }
                            echo '</p>';
                        } ?>
                    </div>
                    <div class="content-meta">
                        <?php if (!empty($nodeContent['subjects'])){
                            echo '<img src="'. get_template_directory_uri() .'/src/assets/img/subject_icon.svg" alt="Fächer">';
                            echo '<p>';
                            $i = 0;
                            foreach ($nodeContent['subjects'] as $subject) {
                                if(++$i === count($nodeContent['subjects'])) {
                                    echo $subject;
                                }else{
                                    echo $subject.', ';
                                }
                            }
                            echo '</p>';
                        } ?>
                    </div>
                    <div class="content-meta">
                        <?php if (!empty($nodeContent['educationalcontext'])){
                            echo '<img src="'. get_template_directory_uri() .'/src/assets/img/class_icon.svg" alt="Bildungsebene">';
                            echo '<p>';
                            $i = 0;
                            foreach ($nodeContent['educationalcontext'] as $subject) {
                                if(++$i === count($nodeContent['educationalcontext'])) {
                                    echo $subject;
                                }else{
                                    echo $subject.', ';
                                }
                            }
                            echo '</p>';
                        } ?>
                    </div>
                </div>
            </div>
        </a>
        <?php }else{ ?>

            <a class="wlo-feature-link" href="<?php echo get_field('featureUrl'); ?>" target="_blank">
                <div class="wlo-feature-wrapper">
                    <div class="wlo-feature-img">
                        <?php if (!empty(get_field('featureImage'))){ ?>
                            <img src="<?php echo get_field('featureImage')['url']; ?>" alt="Cover: <?php echo get_field('featureTitle'); ?>">
                        <?php } ?>
                    </div>
                    <div class="wlo-feature-text">
                        <div class="content-header">
                        </div>
                        <div class="content-title"><?php echo get_field('featureTitle'); ?></div>
                        <p class="content-description"><?php echo get_field('featureDescription') ?></p>
                    </div>
                </div>
            </a>

        <?php } ?>

    </div>
    <?php if (get_field('footer') || true){ ?>
        <div class="wlo-accordion-bottom"></div>
    <?php } ?>
</div>



<script>
    jQuery('#wlo-accordion-<?php echo $accordionID; ?>').click(function(){
        jQuery(this).find("img").toggleClass("wlo-accordion-icon-active");
        jQuery('#wlo-accordion-content-<?php echo $accordionID; ?>').slideToggle();
    });
</script>



<?php if (is_admin()) {
    echo '</div>';
} ?>
