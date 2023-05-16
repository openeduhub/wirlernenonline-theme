<?php
[
    'imageUrl' => $imageUrl,
    'title' => $title,
    'description' => $description,
    'websiteUrl' => $websiteUrl,
    'professionGroup' => $professionGroup,
    // 'sourceName' => $sourceName,
    // 'sourceIcon' => $sourceIcon,
] = $args;
$sourceName = 'Bundesagentur für Arbeit';
$sourceIcon = 'agentur-fuer-arbeit.svg';
?>


<div class="wlo-card job-profile-card">
    <?php if (!empty($imageUrl)) { ?>
        <div class="wlo-card-image-container">
            <img class="wlo-card-image" src="<?php echo $imageUrl; ?>" alt="">
            <div class="media-type-icon-container">
                <img class="media-type-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/media-types/job-profile.svg">
            </div>
        </div>
    <?php } ?>
    <div class="wlo-card-body">
        <p class="media-summary">
            Berufsprofil
        </p>
        <div class="wlo-card-title">
            <h3><?php echo $title; ?></h3>
        </div>
        <p class="wlo-card-description">
            <?php echo $description; ?>
        </p>
        <div class="wlo-card-meta">
            <p><?php echo $professionGroup; ?></p>
        </div>
        <?php if (!empty($websiteUrl)) { ?>
            <a class="wlo-card-link-chip wlo-chip-dark-blue" href="<?php echo $websiteUrl; ?>">zur Webseite...</a>
        <?php } ?>
        <?php if (!empty($sourceName) || !empty($sourceIcon)) { ?>
            <div class="wlo-card-source-container">
                <?php
                if (!empty($sourceName)) { ?>
                    <p><?php echo $sourceName; ?></p>
                <?php } ?>
                <img class="source-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/sources/<?php echo $sourceIcon; ?>">
            </div>
        <?php } ?>
    </div>
</div>