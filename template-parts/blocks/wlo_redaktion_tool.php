<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">WLO: Qualitätskontrolle für die Themenseiten</div>';
} ?>

<?php
$menuValues = array(
    "37163" => "Fachredaktion",
    "41022" => "Community-Redaktion",
    "42813" => "Inhalteredaktion",
);

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}

$username = wp_get_current_user()->user_login;
//$username = 'basti';

//$apiUrl = 'rest/iam/v1/people/-home-/-me-/memberships?maxItems=100&skipCount=0';
$apiUrl = 'rest/iam/v1/people/-home-/'.$username.'/memberships?maxItems=100&skipCount=0';
$memberships = callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET');

$groups = array();
foreach ($memberships['groups'] as $group){
    if ($group['properties']['ccm:groupType'][0] == 'EDITORIAL'){
        $groups[] = $group['properties']['cm:authorityDisplayName'][0];
    }

}
//print_r($groups);

?>

<div class="wlo-redaktion-tool">
    <h3>Kontrolliere die Qualität deiner Themenseiten</h3>

    <div class="wlo-redaktion-tool-list">
        <?php
        foreach ($groups as $subject){
            $subject = str_replace('WLO-', '', $subject);
            $subjectUrl = str_replace(' ', '-', $subject);
            if(strrpos( $subjectUrl, '/')){
                $subjectUrl = substr($subjectUrl, 0, strrpos( $subjectUrl, '/'));
            }
            switch ($subjectUrl) {
                case 'DaZ':
                    $subjectUrl = 'deutsch-als-zweitsprache';
                    break;
                case 'Türkisch':
                    $subjectUrl = 'tuerkisch';
                    break;
            }
            echo '<a class="wlo-block-button" href="http://metaqs.openeduhub.net/'.strtolower($subjectUrl).'" target="_blank">'.$subject.'</a>';
        }
        ?>
    </div>

    <iframe width="0" height="0" frameborder="0" style="position: absolute; left: -900000px" src="https://redaktion.openeduhub.net/edu-sharing/?ticket=<?php echo $ticket; ?>"></iframe>

</div>


<?php if (is_admin()) {
    echo '</div>';
} ?>
