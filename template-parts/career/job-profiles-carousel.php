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
?>

<div id="<?php echo $sliderId; ?>" class="wlo-accordion-content">
    <?php
    foreach ($profiles as &$profile) {
        get_template_part('template-parts/career/job-profile-card', args: array(
            'imageUrl' => 'https://upload.wikimedia.org/wikipedia/en/8/80/Wikipedia-logo-v2.svg',
            'title' => $profile->descriptionShort,
            'description' => $profile->descriptionShort,
            'professionGroup' => $profile->professionGroup . ', ' . $profile->professionType,
            'websiteUrl' => $profile->url,
        ));
    }
    unset($profile);
    ?>
</div>

<?php initSlick($sliderId, 3, 3, count($profiles)); ?>