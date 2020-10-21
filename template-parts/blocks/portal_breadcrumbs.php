<?php
if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Themenportal: Breadcrumbs</div>';
};
?>
    <div class="portal_block no-margin">
        <div class="portal-breadcrumbs">
            <ul class="portal-breadcrumbs-list">

                <?php


                /* Filter Values prioritized GET/POST > Block-Setting > Portal-Setting */
                /*
                * collectionUrl
                * disciplines
                * educationalContexts
                * intendedEndUserRoles
                * oer
                */
                /* ------------------------------------------------------------------- */
                $postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
                $educational_filter_values = get_educational_filter_values($postID);

                //echo '<pre style="background-color: lightgrey">' , var_dump($educational_filter_values) , '</pre>';

                $collectionUrl = $educational_filter_values["collectionUrl"];
                /*
                $disciplines = $educational_filter_values["disciplines"];
                $educationalContexts = $educational_filter_values["educationalContexts"];
                $intendedEndUserRoles = $educational_filter_values["intendedEndUserRoles"];
                $oer = $educational_filter_values["oer"];
                */
                /* ------------------------------------------------------------------- */


                $pattern = '/http.*\?id=(.*)(&|$)/';
                preg_match_all($pattern, $collectionUrl, $matches);

                $url = WLO_REPO . 'rest/node/v1/nodes/-home-/' . $matches[1][0] . '/parents?propertyFilter=-all-&fullPath=false';
                $response = callWloRestApi($url);

                $nodes = Array();

                foreach ($response->nodes as $node) {
                    if ($node->title == 'Portale'){
                        $nodes[] = [$node->title, get_page_link(9930)];
                    }else{
                        $nodes[] = [$node->title, str_replace('dev.wirlernenonline.de', 'wirlernenonline.de', $node->properties->{'cclom:location'}[0])];
                    }
                }
                $nodes = array_reverse($nodes);

                foreach ($nodes as $node) {
                    echo "<li class='portal-breadcrumbs-list-item'><a href='" . $node[1] . "'>" . $node[0] . "</a><span class='material-icons'>chevron_right</span></li>";
                }

                ?>
            </ul>
        </div>
    </div>

<?php if (is_admin()) {
    echo '</div>';
}; ?>
