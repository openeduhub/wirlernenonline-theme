<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Sammlungsinhalte</div>';
} ?>
<?php

/* Filter Values prioritized GET/POST > Block-Setting > Portal-Setting */
/*
 * collectionUrl
 * disciplines
 * educationalContexts
 * intendedEndUserRoles
 * oer
 * objectTypes
 * learningResourceTypes
 * generalKeyword
 * oehWidgets
*/
/* ------------------------------------------------------------------- */
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);
$collectionUrl = $educational_filter_values["collectionUrl"];

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);
$collectionID = $matches[1][0];
$collectionContentId = uniqid('collectionContentId-');

$headline = ((!empty(get_field('headline'))) ? get_field('headline') : 'Materialien');

?>

<div class="portal_collection_content_block">
    <?php
        if (!empty($block['anchor'])) {
            echo '<a name="' . $block['anchor'] . '"></a>';
        }
        echo '<div class="portal_collection_content_header">';
        echo '<div class="headline big">' . $headline . '</div>';
        if (!empty(get_field('text'))) {
            echo '<p>' . get_field('text') . '</p>';
        }
        echo '</div>';

        $layout = get_field('layout');
        $maxResults = get_field('maxResults');
        $slidesToShow = 1;
        $slidesToScroll = 1;
        if (get_field('slidesToShow')) {
            $slidesToShow = get_field('slidesToShow');
        }
        if (get_field('slidesToScroll')) {
            $slidesToScroll = get_field('slidesToScroll');
        }
        $horizontal_scroll = get_field('horizontal_scroll');

        ?>
    <div id="<?php echo $collectionContentId; ?>" >
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>
</div>

<script type="text/javascript" >
    jQuery(document).ready(function($) {

        var data = {
            'action': 'collection_content_browser',
            'postID': '<?php echo $postID; ?>',
            'collectionID': '<?php echo $collectionID; ?>',
            'educational_filter_values': '<?php echo htmlentities(json_encode($educational_filter_values)); ?>',
            'layout': '<?php echo $layout; ?>',
            'maxResults': '<?php echo $maxResults; ?>',
            'slidesToScroll': '<?php echo $slidesToScroll; ?>',
            'slidesToShow': '<?php echo $slidesToShow; ?>',
            'horizontal_scroll': '<?php echo $horizontal_scroll; ?>',
        };

    <?php if (is_admin()) { ?>
        jQuery.post(ajaxurl, data, function(response) {
            jQuery('#<?php echo $collectionContentId; ?>').html(response);
        });
    <?php }else{ ?>
        jQuery(window).scroll(function() {
            //check if your div is visible to user
            // CODE ONLY CHECKS VISIBILITY FROM TOP OF THE PAGE
            if (jQuery(window).scrollTop() + jQuery(window).height() >= jQuery('#<?php echo $collectionContentId; ?>').offset().top) {
                if(!jQuery('#<?php echo $collectionContentId; ?>').attr('loaded')) {
                    //not in ajax.success due to multiple sroll events
                    jQuery('#<?php echo $collectionContentId; ?>').attr('loaded', true);

                    jQuery.post(ajaxurl, data, function(response) {
                        jQuery('#<?php echo $collectionContentId; ?>').html(response);
                    });
                }
            }
        });
    <?php } ?>

});
</script>


<?php if (is_admin()) {
echo '</div>';
} ?>
