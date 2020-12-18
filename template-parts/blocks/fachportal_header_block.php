<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Header</div>';
} ?>

<?php
$postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
$educational_filter_values = get_educational_filter_values($postID);

$collectionUrl = $educational_filter_values["collectionUrl"];

$pattern = '/http.*\?id=(.*)(&|$)/';
preg_match_all($pattern, $collectionUrl, $matches);
$collectionID = $matches[1][0];

$url = WLO_REPO . 'rest/collection/v1/collections/-home-/' . $collectionID;
$response = callWloRestApi($url);

if (empty($response->collection->properties->{'cm:description'}[0])){
    $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
}else{
    $description = $response->collection->properties->{'cm:description'}[0];
}

$backgroundColor = '#FFB930';
$rgbBackgroundColor = hex2rgb($backgroundColor);
//$rgbBackgroundColor = '255,185,48';

$url = WLO_REPO . 'rest/node/v1/nodes/-home-/' . $matches[1][0] . '/parents?propertyFilter=-all-&fullPath=false';
$parents = callWloRestApi($url)->nodes;
$portal = $parents[count($parents)-2];
?>

<div class="fachportal-header-block" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">
    <div class="fachportal-header-bar">
        <div class="fachportal-header-bar-wrapper">
            <div class="fachportal-header-bar-tab" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 1);">
                <a href="<?php echo $portal->properties->{'cclom:location'}[0]; ?>">Fachportal <?php echo $portal->title; ?></a>
            </div>
        </div>
    </div>
    <div class="fachportal-header-wrapper">

        <div class="description">

            <div class="description-content">
                <div class="title"><?php echo get_the_title($postID); ?></div>
                <div class="header-description"><?php echo $description; ?></div>
            </div>

            <?php
            $url = WLO_REPO . 'rest/collection/v1/collections/local/' . $collectionID . '/children/collections?scope=MY&&skipCount=0&maxItems=1247483647&sortProperties=ccm%3Acollection_ordered_position&sortAscending=true';
            $subCollections = callWloRestApi($url);
            ?>
            <div class="collections">
                <?php if (!empty($subCollections->collections)) : ?>
                    <div class="sub-subjects">
                        <div class="sub-subjects-header">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/categories.svg">
                            <h3>Unterthemen <?php echo get_the_title($postID); ?></h3>
                        </div>
                        <div class="sub-subjects-container">
                            <?php foreach ($subCollections->collections as $collection) {
                                $nodeId = $collection->ref->id; ?>
                                <div class="sub-subject">
                                    <a href="<?php echo $collection->properties->{'cclom:location'}[0]; ?>">
                                        <p><?php echo $collection->title; ?></p>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>


        <div class="content-info">
            <div class="header">
                <h3>Gerpüfte Inhalte</h3>
            </div>

            <div class="diagram"  style="background-color:rgba(255, 255, 255, 0.1);">
                <div class="diagram-content">
                    <canvas width="80%" height="80%" id="contentChart"></canvas>
                </div>
            </div>

            <div class="diagram-legend">
                <div class="diagram-legend-entry Lerninhalte">Lerninhalte <div class="diagram-legend-color"></div></div>
                <div class="diagram-legend-entry Methoden"><div class="diagram-legend-color"></div> Methoden</div>
                <div class="diagram-legend-entry Tools">Tools <div class="diagram-legend-color"></div></div>
                <div class="diagram-legend-entry Wissen"><div class="diagram-legend-color"></div> Gut zu Wissen</div>
            </div>
        </div>


        <div class="header-bottom"></div>
    </div>
</div>

<div class="fachportal-header-block fachportal-new-content">
    <div class="fachportal-header-wrapper" >
        <div class="fachportal-new-content-inner" style="background-color:rgba(<?php echo $rgbBackgroundColor; ?>, 0.2);">
            <h3>Die neusten geprüften Inhalte für dich!</h3>
        </div>

        <div class="header-bottom"></div>
    </div>
</div>

<script>
    function addData(chart, label, data, color) {
        chart.data.labels.push(label);
        chart.data.datasets.forEach((dataset) => {
            dataset.data.push(data);
            dataset.backgroundColor.push(color);
            dataset.borderWidth.push(0);
        });
        chart.update();
    }

    const ctx = document.getElementById('contentChart').getContext('2d');
    let contentChart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'polarArea',
        //type: 'pie',
        //type: 'doughnut',

        // The data for our dataset
        data: {
            datasets: [{
                data: [],
                backgroundColor: [],
                borderAlign:[],
                borderColor: [],
                borderWidth: []
            }],
            labels: [],

        },

        // Configuration options go here
        options: {
            scale: {
                display: false
            },
            legend: {
                display: false
            }
        }
    });

    jQuery( document ).ready(function() {

        //addData(contentChart, 'Tools', 25, 'rgba(255,255,255,0.8)');
        //addData(contentChart, 'Gut zu Wissen', 5, 'rgba(255,255,255,0.2)');

    });

</script>

<?php if (is_admin()) {
    echo '</div>';
} ?>

