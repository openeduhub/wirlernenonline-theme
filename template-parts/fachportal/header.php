<?php
if (!function_exists('fachportalHeaderGetDescription')) {
    /**
     * Extracts the "Fachportal" description from `fachseite-header-block` from the page's content.
     *
     * Defaults to a hardcoded default string if `fachseite-header-block` is not present in the
     * page's content or its `description` field is not set.ss
     */
    function fachportalHeaderGetDescription()
    {
        $content = get_post_field('post_content');
        foreach (parse_blocks($content) as &$block) {
            if (
                $block['blockName'] == 'acf/fachseite-header-block'
            ) {
                $headerBlock = $block;
                continue;
            }
        }
        unset($block);
        if (!empty($headerBlock)) {
            $description = $headerBlock['attrs']['data']['description'];
            // Imitate ACF's processing of whitespace in its HTML editor.
            //
            // Wrap all lines separated by at least one empty line in <p> tags.
            $description = '<p>' . preg_replace("/(\r?\n){2,}/", "</p><p>", trim($description)) . '</p>';
            // Convert single new-lines to <br> tags.
            $description = preg_replace("/(\r?\n)/", "<br>", $description);
        }
        if (empty($description)) {
            $postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
            $portalTitle = get_the_title($postID);
            $description = '
                <p>Herzlich willkommen auf der Fachseite für ' . $portalTitle . '!</p>

                <p>Du findest hier neben von unserer Redaktion sorgfältig ausgewählten Materialien für den 
                Präsenzunterricht, den Online-Unterricht oder das hybride Klassenzimmer auch Informationen zu 
                Events, Fortbidungsangeboten und zum Neusten aus ' . $portalTitle . '. Über “unsere Themen” kannst du auch 
                tiefer in Lehrplanthemen eintauchen und spezielle Materialien finden. Lass dich inspirieren!</p>
                
                <p>Für jeden und jede ist etwas dabei und es soll noch viel mehr werden – dafür brauchen wir deine 
                Unterstützung, <a href="' . get_page_link(97) . '">werde Teil der Community</a>! Du kannst in Redaktionen mitarbeiten und eigene Inhalte 
                hochladen und der Community zur Verfügung stellen.</p>';
        }
        return $description;
    }
}

$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$portalTitle = get_the_title($postID);
$backgroundColor = wloSubjectType($portalTitle)['color'];
$rgbBackgroundColor = hex2rgb($backgroundColor);
$description = fachportalHeaderGetDescription();

$GLOBALS['wlo_fachportal'] = array(
    'title' => $portalTitle,
    'backgroundColor' => $backgroundColor,
    'rgbBackgroundColor' => $rgbBackgroundColor
);

?>
<div class="fachportal-header-block" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">
    <div class="fachportal-header-wrapper">
        <div class="description">
            <div class="description-content">
                <h1 class="title"><?php echo $portalTitle; ?></h1>
                <div class="header-description header-description-mobile">
                    <?php echo $description; ?>
                </div>
                <?php if (strlen($description) >= 190) { ?>
                    <button class="header-description-button" onclick="toggleDescription(this)">
                        <div>
                            <span>Mehr</span>
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down_white.svg" alt="">
                        </div>
                    </button>
                <?php } ?>
            </div>
        </div>
        <div class="content-stats"></div>
    </div>
</div>

<script>
    function toggleDescription(button) {
        if (
            jQuery(button).closest('.description-content').find('.header-description').height() <= '145'
        ) {
            jQuery(button).closest('.description-content').find('.header-description').css('max-height', 1000);
            jQuery(button).closest('.description-content').find('.header-description-button div img').css("transform", "rotate(180deg)");
            jQuery(button).closest('.description-content').find('.header-description-button div span').html("Weniger");
        } else {
            jQuery(button).closest('.description-content').find('.header-description').css('max-height', '9em');
            jQuery(button).closest('.description-content').find('.header-description-button div img').css("transform", "rotate(0deg)");
            jQuery(button).closest('.description-content').find('.header-description-button div span').html("Mehr");
        }
    }
</script>