<?php
[
    'topicPostId' => $topicPostId,
    'topic' => $topic,
    'color' => $color,
] = $args;

$jobProfilesCarouselId = uniqid('wlo-job-profiles-carousel-');
?>

<div class="fachportal-header-accordion">
    <div class="wlo-accordion-wrapper job-profiles" style="background-color:rgba(<?php echo $color; ?>, 0.2);">
        <div class="wlo-accordion job-profiles-header">
            <h2>Berufsprofile zum Thema <?php echo $topic; ?></h2>
            <p class="job-profiles-description job-profiles-description-bold">
                Wir haben ChatGPT (eine künstliche Intelligenz) gefragt, welche Berufe zu diesem Lehrplanthema passen.
            </p>
            <p class="job-profiles-description">
                Zu den vorgeschlagenen Berufen haben wir hier Informationen von ausgewählten Seiten für dich zusammengestellt.
            </p>
        </div>
        <div id="<?php echo $jobProfilesCarouselId; ?>" class="wlo-accordion-content">
            <div class="lds-ring">
                <div></div>
                <div></div>
            </div>

        </div>
    </div>
</div>

<script>
    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'wloJobProfilesCarousel',
            postId: <?php echo $topicPostId; ?>,
        },
        success: (html) => {
            jQuery('#<?php echo $jobProfilesCarouselId; ?>').html(html);
        },
    });
</script>