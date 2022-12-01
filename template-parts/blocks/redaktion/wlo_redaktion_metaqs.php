<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: MetaQS-Block</div>';
} ?>

<?php

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}

$wloMetaQsId = uniqid('wlo-metaqs-');

$metaQsMode = get_field('mode')['value'];
$metaQsHeight = get_field('height');
if (empty($metaQsHeight)){
    $metaQsHeight = 400;
}

?>

<div class="wlo-redaktion-metaqs" id="<?php echo $wloMetaQsId; ?>">
    <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
</div>

<script type="text/javascript" >
    jQuery(document).ready(function($) {
        window.METAQS_API_URL = '<?php echo WLO_METAQS_URL; ?>';
        window.METAQS_EDUSHARING_URL = '<?php echo WLO_METAQS_EDUSHARING_URL; ?>';

        var data = {
            'action': 'wlo_metaQs',
            'metaQsMode': '<?php echo $metaQsMode; ?>',
            'metaQsHeight': '<?php echo $metaQsHeight; ?>',
            'ticket': '<?php echo base64_encode($ticket); ?>',
            'admin': '<?php echo is_admin(); ?>',
        };



        jQuery.fn.isInViewport = function() {
            const elementTop = jQuery(this).offset().top;
            const elementBottom = elementTop + jQuery(this).outerHeight();

            const viewportTop = jQuery(window).scrollTop();
            const viewportBottom = viewportTop + jQuery(window).height();

            if (elementBottom > viewportTop && viewportBottom > elementTop){
                return true;
            }
        };

        let notLoaded = false;

        if ( (notLoaded === false) && jQuery('#<?php echo $wloMetaQsId; ?>').isInViewport() ) {
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#<?php echo $wloMetaQsId; ?>').html(response);
            });
            notLoaded = true;
        }

        jQuery(window).on('resize scroll', function() {

            if ( (notLoaded === false) && jQuery('#<?php echo $wloMetaQsId; ?>').isInViewport() ) {
                jQuery.post(ajaxurl, data, function(response) {
                    jQuery('#<?php echo $wloMetaQsId; ?>').html(response);
                });
                notLoaded = true;
            } else {
                // do something else
            }
        });



    });

</script>
