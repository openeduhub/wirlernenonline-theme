<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Berufsseite: Veranstaltungs-Karte</div>';
} ?>

<?php $eventsMapContainerId = uniqid('wlo-events-map-container-'); ?>
<div id="<?php echo $eventsMapContainerId; ?>"></div>
<script>
    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'wloEventsMap',
            postId: <?php echo $GLOBALS['wloCareerInfo']['topicPostId']; ?>,
        },
        success: (html) => {
            jQuery('#<?php echo $eventsMapContainerId; ?>').html(html);
        },
    });
</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>