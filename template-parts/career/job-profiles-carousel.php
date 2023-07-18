<?php
[
    'topic' => $topic,
] = $args;

// Fetch card data
$url = WLO_AI_PROMPT_SERVICE_URL . "/ai/prompt/profession/profile";
$headers = array(
    'ai-prompt-token' => WLO_AI_PROMPT_SERVICE_TOKEN,
    'Content-Type' => 'application/json',
);
$response = wp_remote_post($url, array(
    'headers' => $headers,
    'timeout' => 60,
    'body' => $topic,
));
if (is_wp_error($response) || $response['response']['code'] != 200) {
    error_log(print_r($response, true));
} else {
    $profiles = json_decode($response['body'])->profiles;
}

$sliderId = uniqid('slider-');
/** Maps profession groups to occurrences. */
$professionGroups = [];
?>

<div id="<?php echo $sliderId; ?>" class="wlo-accordion-content">
    <?php
    foreach ($profiles as &$profile) {
        // Populate $professionGroups
        if (empty($professionGroups[$profile->professionGroup])) {
            $professionGroups[$profile->professionGroup] = 1;
        } else {
            $professionGroups[$profile->professionGroup]
                = $professionGroups[$profile->professionGroup] + 1;
        }

        get_template_part('template-parts/career/job-profile-card', args: array(
            'imageUrl' => 'https://upload.wikimedia.org/wikipedia/en/8/80/Wikipedia-logo-v2.svg',
            'title' => $profile->descriptionShort,
            'description' => $profile->descriptionShort,
            'professionGroup' => $profile->professionGroup . ', ' . $profile->professionType,
            'websiteUrl' => $profile->url,
            'attributes' => 'data-profession-group="' . $profile->professionGroup . '"',
        ));
    }
    unset($profile);
    ?>
</div>

<?php initSlick($sliderId, 3, 3, count($profiles)); ?>

<script>
    (() => {
        /** The select element for the user to choose filters. */
        const select = jQuery('#profession-group-filters')[0];
        /** All cards' HTML elements. */
        const cardElements = jQuery('#<?php echo $sliderId; ?>').find('[data-profession-group]');

        /** Populates select element's options. */
        function populateFilterOptions() {
            select.options[0].text += ` (${<?php echo count($profiles); ?>})`
            const professionGroups = <?php echo json_encode($professionGroups); ?>;
            Object.keys(professionGroups).sort().forEach(professionGroup => {
                const nOccurrences = professionGroups[professionGroup];
                const option = new Option(`${professionGroup} (${nOccurrences})`, professionGroup);
                select.add(option);
            });
        }

        /**
         * To be called when the user selects an option.
         * 
         * @param value - the selected option value
         */
        function onOptionSelected(value) {
            const cardsContainer = jQuery('#<?php echo $sliderId; ?>');
            // Remove all slides
            cardsContainer.slick('removeSlide', null, null, true);
            // Add matching slides again
            if (value === 'all') {
                cardElements.each(function() {
                    cardsContainer.slick('addSlide', this)
                });
            } else {
                cardElements.filter(`[data-profession-group="${value}"]`).each(function() {
                    cardsContainer.slick('addSlide', this)
                });
            }
        }

        populateFilterOptions();
        select.onchange = function() {
            onOptionSelected(this.value);
        }
    })()
</script>