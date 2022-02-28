<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenseite: Inhaltsblock</div>';
} ?>
<?php

require_once(get_template_directory().'/functions/wlo-config.php');

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];

$collectionLevel = get_field('collection_level', $postID);

$pageTitle = get_the_title($postID);
$pageDiscipline = get_the_title($postID);

/* ------------------------------------------------------------------- */

$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

$fachportalContentId = uniqid('fachportalContentId-');

$contentCount = get_field('content_count');
$contentType = get_field('contentType');
$blockIcon = get_field('blockIcon')['url'];
$softmatch = get_field('softmatch');
$sorting = get_field('sorting');
$descrText = base64_encode(get_field('descrText'));

$headline = '';
if ($collectionLevel >= 1){
    $headline = get_field('contentType')['label'];
}
if(!empty(get_field('headline'))){
    $headline = get_field('headline');
}

$slidesToShow = 4;
$slidesToScroll = 4;
if (get_field('slidesToShow')) {
    $slidesToShow = get_field('slidesToShow');
}
if (get_field('slidesToScroll')) {
    $slidesToScroll = get_field('slidesToScroll');
}
$showSliderDots = 'true';



$disciplines = $educational_filter_values["disciplines"];
$educationalContexts = $educational_filter_values["educationalContexts"];
$intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
$oer = $educational_filter_values["oer"];
$objectTypes = $educational_filter_values["objectTypes"];
$learningResourceTypes = $educational_filter_values["learningResourceTypes"];
$generalKeywords = $educational_filter_values["generalKeyword"];
$oehWidgets = $educational_filter_values["oehWidgets"];

if ($collectionLevel >= 1){  // activate softmatch for 'themenseiten'
    $softmatch = '1';
}

if (empty($contentCount)){
    $contentCount = 500;
}

//$addContentPageID = 9614; //dev
$addContentPageID = 9933; //pre
//$addContentPageID = 9081; //local

$pageTitle = get_the_title($postID);
$pageDiscipline = get_field('discipline', $postID)[0]['label'];

//only content from the given collection
//$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID . '/children/references?sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
//$response = callWloRestApi($url);

$contentInfo = array(
        "addcontentPageID" => $addContentPageID,
        "pageTitle" => $pageTitle,
        "pageDiscipline" => $pageDiscipline,
        "collectionID" => $collectionID,
);


//$rgbBackgroundColor = $GLOBALS['wlo_fachportal']['rgbBackgroundColor'];
$rgbBackgroundColor = '255,255,255';

switch ($contentType['value']){
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

/*
$contentArray = array();
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
            }
        }

        $contentArray[] = array(
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

    } //end foreach
}
*/

$contentArray = $GLOBALS['wlo_themenseiten_content'];


?>

<div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
    <div class="header">
        <h3>Medien</h3>
    </div>

    <?php
    $images_vocab = getNewLrtList('a6d1ac52-c557-4151-bc6f-0d99b0b96fb9');
    $video_vocab = getNewLrtList('7a6e9608-2554-4981-95dc-47ab9ba924de');
    $audio_vocab = getNewLrtList('ec2682af-08a9-4ab1-a324-9dca5151e99f');
    $interactive_vocab = getNewLrtList('4665caac-99d7-4da3-b9fb-498d8ece034f');
    $media_vocab = array_merge($images_vocab, $video_vocab, $audio_vocab, $interactive_vocab);

    $swimlane_content = wloFilterSwimlane($contentArray, $media_vocab);
    $contentArray = $swimlane_content['contentArray'];

    echo wlo_add_swimlane_content( $swimlane_content['filtered_content'], $slidesToShow, $slidesToScroll, $contentInfo );
    ?>

    <div class="fachportal-spacer"></div>
</div>

<div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
    <div class="header">
        <h3>Unterrichtsplanung</h3>
    </div>

    <?php
    $methods_vocab = getNewLrtList('7381f17f-50a6-4ce1-b3a0-9d85a482eec0');
    $lesson_planning_vocab = getNewLrtList('0a79a1d0-583b-47ce-86a7-517ab352d796');
    $lesson_planning_complete = array_merge($methods_vocab, $lesson_planning_vocab);

    $swimlane_content = wloFilterSwimlane($contentArray, $lesson_planning_complete);
    $contentArray = $swimlane_content['contentArray'];

    echo wlo_add_swimlane_content( $swimlane_content['filtered_content'], $slidesToShow, $slidesToScroll, $contentInfo );
    ?>

    <div class="fachportal-spacer"></div>
</div>

<div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
    <div class="header">
        <h3>Praxismaterialien</h3>
    </div>

    <?php
    $questionnaire_vocab = getNewLrtList('cd625d33-5d7b-4a86-a54a-9a897ded729f');
    $learning_materials_vocab = getNewLrtList('588efe4f-976f-48eb-84aa-8bcb45679f85');
    $practice_materials_complete = array_merge($questionnaire_vocab, $learning_materials_vocab);

    $swimlane_content = wloFilterSwimlane($contentArray, $practice_materials_complete);
    $contentArray = $swimlane_content['contentArray'];

    echo wlo_add_swimlane_content( $swimlane_content['filtered_content'], $slidesToShow, $slidesToScroll, $contentInfo );
    ?>

    <div class="fachportal-spacer"></div>
</div>



<div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
    <div class="header">
        <h3>Tools</h3>
    </div>

    <?php
    $tool_vocab = getNewLrtList('cefccf75-cba3-427d-9a0f-35b4fedcbba1');

    $swimlane_content = wloFilterSwimlane($contentArray, $tool_vocab);
    $contentArray = $swimlane_content['contentArray'];

    echo wlo_add_swimlane_content( $swimlane_content['filtered_content'], $slidesToShow, $slidesToScroll, $contentInfo );
    ?>

    <div class="fachportal-spacer"></div>
</div>



<div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
    <div class="header">
        <h3>Quellen</h3>
    </div>

    <?php
    $source_vocab = getNewLrtList('3869b453-d3c1-4b34-8f25-9127e9d68766');

    $swimlane_content = wloFilterSwimlane($contentArray, $source_vocab);
    $contentArray = $swimlane_content['contentArray'];

    echo wlo_add_swimlane_content( $swimlane_content['filtered_content'], $slidesToShow, $slidesToScroll, $contentInfo );
    ?>

    <div class="fachportal-spacer"></div>
</div>


<div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
    <div class="header">
        <h3>Bildungsangebote, Termine, Veranstaltungen</h3>
    </div>

    <?php
    $educational_offer_vocab = getNewLrtList('03ab835b-c39c-48d1-b5af-7611de2f6464');
    $event_meeting_vocab = getNewLrtList('955590ae-5f06-4513-98e9-91dfa8d5a05e');
    $event_complete = array_merge($educational_offer_vocab, $event_meeting_vocab);

    $swimlane_content = wloFilterSwimlane($contentArray, $event_complete);
    $contentArray = $swimlane_content['contentArray'];

    echo wlo_add_swimlane_content( $swimlane_content['filtered_content'], $slidesToShow, $slidesToScroll, $contentInfo );
    ?>
    <div class="fachportal-spacer"></div>
</div>


    <?php
    $other_content = array();
    foreach ($contentArray as $content) {
        if(!$content['added']){
            $other_content[] = $content;
        }
    }

    if (!empty($other_content)){ ?>

        <div class="fachportal-content-block" id="<?php echo $fachportalContentId; ?>">
            <div class="header">
                <h3>Weitere Inhalte</h3>
            </div>

            <?php echo wlo_add_swimlane_content( $other_content, $slidesToShow, $slidesToScroll, $contentInfo ); ?>
        </div>
    <?php } ?>







<?php if (is_admin()) {
    echo '</div>';
} ?>
