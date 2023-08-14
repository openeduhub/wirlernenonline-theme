<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Fachportal: Swimlane</div>';
} ?>

<?php
// $postID = (!empty(get_the_id())) ? get_the_id() : acf_editor_post_id();
// $educational_filter_values = get_educational_filter_values($postID);
// $collectionUrl = $educational_filter_values["collectionUrl"];
// $url_components = parse_url($collectionUrl);
// parse_str($url_components['query'], $params);
// $collectionID = $params['id'];

$elementId = uniqid('swimlane-');
$queryId = 'ngsearch';
$criteria = get_field('criteria');

$pageVariablesDefinitions = file_get_contents(__DIR__ . '/../../src/assets/data/variables.json');
?>

<wlo-swimlane
    id="<?php echo $elementId; ?>"
    queryId="<?php echo $queryId; ?>"
    criteria='<?php echo $criteria; ?>'>
</wlo-swimlane>

<?php if (is_admin()) {
    echo '</div>';
} ?>

<script>
    jQuery(document).ready(() => {
        const pageVariablesDefinitions = <?php echo $pageVariablesDefinitions; ?>;
        // FIXME: allows javascript injection.
        const criteriaField = <?php echo $criteria; ?>;

        function addPageVariablesToCriteria(criteria, pageVariables) {
            const result = [
                ...criteria
            ];
            for (const [key, value] of Object.entries(pageVariables)) {
                const variableDefinition = pageVariablesDefinitions.find(d => d.key === key);
                if (variableDefinition.eduSharingPropertyKey) {
                    criterionIndex = result.findIndex(c =>
                        c.property === variableDefinition.eduSharingPropertyKey
                    );
                    if (criterionIndex === -1) {
                        criterionIndex = result.length;
                    }
                    result[criterionIndex] = {
                        property: variableDefinition.eduSharingPropertyKey,
                        values: [variableDefinition.vocabsUrl + value],
                    }
                }
            }
            return result;
        }

        if (window.pageVariablesSubject) {
            const swimlaneElement = jQuery('#<?php echo $elementId; ?>');
            window.pageVariablesSubject.subscribe(pageVariables => {
                const criteria = addPageVariablesToCriteria(criteriaField, pageVariables);
                swimlaneElement.attr('criteria', JSON.stringify(criteria));
            });
            swimlaneElement.on('itemClicked', function(event) {
                showContentPopup(event.detail.ref.id);
            })
        }
    })
</script>