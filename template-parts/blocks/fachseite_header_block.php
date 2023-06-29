<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachseite: Header</div>';
} ?>

<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];
$url_components = parse_url($collectionUrl);
parse_str($url_components['query'], $params);
$collectionID = $params['id'];

$educationalContexts = $educational_filter_values["educationalContexts"];
$accordionID = uniqid();
?>

<div class="fachportal-header-block fachportal-new-content">
    <div class="fachportal-header-wrapper">
        <div class="fachportal-new-content-inner" style="background-color: #F4F4F4">
            <button class="fachportal-accordion" id="fachportal-accordion-<?php echo $accordionID; ?>">
                <h2>Durchstöbere hier unsere Lehrplanthemen</h2>
                <img class="fachportal-accordion-icon" src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_down.svg" alt="Inhalte ein odder ausklappen">
            </button>
            <div class="fachportal-accordion-content" id="fachportal-accordion-content-<?php echo $accordionID; ?>">
                <?php
                $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $collectionID . '/children/collections?scope=MY&skipCount=0&maxItems=1247483647&sortProperties=cm%3Aname&sortAscending=true';
                $subCollections = callWloRestApi($url);
                ?>
                <div class="collections">
                    <?php if (!empty($subCollections->collections)) : ?>
                        <div class="sub-subjects">
                            <div class="sub-subjects-container">
                                <?php foreach ($subCollections->collections as $collection) {
                                    $ccm_location = wlo_convert_dev_url($collection->properties->{'cclom:location'}[0]);

                                    //$ccm_location = str_replace('https://wirlernenonline.de/', 'https://dev.wirlernenonline.de/', $collection->properties->{'cclom:location'}[0]);
                                    //$ccm_location = str_replace('https://wirlernenonline.de/', 'https://pre.wirlernenonline.de/', $collection->properties->{'cclom:location'}[0]);

                                    // Filter hidden collections
                                    if ($collection->properties->{'ccm:editorial_state'}[0] !== 'activated') {
                                        continue;
                                    }

                                    // Filter educationalContexts
                                    if (!empty($educationalContexts)) {
                                        if (empty($collection->properties->{'ccm:educationalcontext'})) { // skip empty?
                                            //continue;
                                        } else {
                                            if (!checkPropertyMatch($collection->properties->{'ccm:educationalcontext'}, $educationalContexts, true)) {
                                                continue;
                                            }
                                        }
                                    }

                                    $title = $collection->title;
                                    if (!empty($collection->properties->{'ccm:collectionshorttitle'}[0])) {
                                        $title = $collection->properties->{'ccm:collectionshorttitle'}[0];
                                    }

                                ?>
                                    <div class="sub-subject">
                                        <a href="<?php echo $ccm_location; ?>">
                                            <p><?php echo $title; ?></p>
                                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/arrow_forward_white.svg">
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery('#fachportal-accordion-<?php echo $accordionID; ?>').click(function() {
        jQuery(this).find("img").toggleClass("fachportal-accordion-icon-active");
        jQuery('#fachportal-accordion-content-<?php echo $accordionID; ?>').slideToggle('medium');
    });

    // educationalcontext filter
    function educationalcontextFilter(educationalcontext) {
        let url = window.location.href.split('?')[0];
        let educationalcontexts = [<?php
                                    foreach ($educationalContexts as $context) {
                                        echo '"' . $context . '",';
                                    }
                                    ?>];

        if (educationalcontexts.includes(educationalcontext)) {
            educationalcontexts.forEach(function(item, index, array) {
                if (item == educationalcontext) {
                    //console.log('same');
                } else {
                    if (url.indexOf('?') > -1) {
                        url += '&educationalContext[]=' + item;
                    } else {
                        url += '?educationalContext[]=' + item;
                    }
                }
            })
        } else {
            educationalcontexts.forEach(function(item, index, array) {
                if (url.indexOf('?') > -1) {
                    url += '&educationalContext[]=' + item;
                } else {
                    url += '?educationalContext[]=' + item;
                }
            })
            if (url.indexOf('?') > -1) {
                url += '&educationalContext[]=' + educationalcontext;
            } else {
                url += '?educationalContext[]=' + educationalcontext;
            }
        }

        window.location.href = url;
    }
</script>


<?php if (is_admin()) {
    echo '</div>';
} ?>