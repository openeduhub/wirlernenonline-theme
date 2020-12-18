<?php /* Template Name: TP Inhalte Formular */
get_header();
global $post;

?>
<div class="gutenberg-layout-container">

    <?php

    // GET-Params
    if (isset($_GET['collectionID'])) {
        $collectionID = $_GET['collectionID'];
    } else {
        $collectionID = ' ID unbekannt';
    }
    if (isset($_GET['headline'])) {
        $widgetName = $_GET['headline'];
    } else {
        $widgetName = 'Widget unbekannt';
    }
    if (isset($_GET['pageTitle'])) {
        $pageTitle = $_GET['pageTitle'];
    } else {
        $pageTitle = 'Unstersammlung unbekannt';
    }
    if (isset($_GET['pageDiscipline'])) {
        $pageDiscipline = $_GET['pageDiscipline'];
    } else {
        $pageDiscipline = 'Fach unbekannt';
    }

    $success = false;

    //POST
    if(isset($_POST['addUrl'])){

        $consentErr =  $formErr = '';
        $collectionID = $_POST['collectionID'];
        $widgetName = $_POST['widgetName'];
        $pageDiscipline = $_POST['pageDiscipline'];
        $pageTitle = $_POST['pageTitle'];

        $orgID = '0fa24aa1-709e-4e86-8ac0-a5bbc093e6cb';
        $workflowGroup = 'GROUP_ORG_WLO-Uploadmanager';

        switch ($pageDiscipline){
            case 'Chemie':
                $orgID = '';
                $workflowGroup = '';
                break;
            case 'Deutsch':
                $orgID = '';
                $workflowGroup = '';
                break;
            case 'Deutsch als Zweitsprache':
                $orgID = '';
                $workflowGroup = '';
                break;
            case 'Informatik':
                $orgID = '';
                $workflowGroup = '';
                break;
            case 'Kunst':
                $orgID = '';
                $workflowGroup = '';
                break;
            case 'Medienbildung':
                $orgID = '';
                $workflowGroup = '';
                break;
            case 'Physik':
                $orgID = '';
                $workflowGroup = '';
                break;
        }

        if (!empty($_FILES['fileToUpload']['name'])) {
            if ($_POST['consent']) {
                //first api-call to create node
                $data_array = array(
                    "cm:name" => [$_FILES['fileToUpload']['name']],
                    'ccm:taxonid' => $pageDiscipline,
                );
                $apiUrl = 'rest/node/v1/nodes/-home-/'.$orgID.'/children?type=ccm%3Aio&renameIfExists=true';
                $nodeID = callRepoApi($apiUrl, json_encode($data_array))['node']['ref']['id'];

                //second api-call for file upload
                if (!empty($nodeID)) {
                    //echo 'Uploading File...<br>';

                    $apiUrl = 'rest/node/v1/nodes/-home-/' . $nodeID . '/content?versionComment=MAIN_FILE_UPLOAD&mimetype=' . $_FILES['fileToUpload']['type'];
                    $fields = [
                        'file' => new \CurlFile($_FILES['fileToUpload']['tmp_name'], $_FILES['fileToUpload']['type'], $_FILES['fileToUpload']['name'])
                    ];

                    if (callRepoApi($apiUrl, $fields, 'Content-Type: multipart/form-data') !== false) {
                        //echo 'File successfully uploaded.<br>';

                        //third api-call to set workflow
                        $workflowComment = 'Für folgende Sammlung vorgeschlagen: ' . $pageDiscipline . ' > ' . $pageTitle . ' > ' . $widgetName . ' (ID: ' . $collectionID . ')';
                        if (($_POST['ccby'])){
                            $workflowComment .= ' - Lizens: CC BY';
                            if (!empty($_POST['author'])){
                                $workflowComment .= ' - Urherber:in: '.$_POST['author'];
                            }
                            if (!empty($_POST['personal_link'])){
                                $workflowComment .= ' - Persönlicher Link: '.$_POST['personal_link'];
                            }
                        }else if (($_POST['cczero'])){
                            $workflowComment .= ' - Lizens: CC Zero';
                        }
                        if (!empty($_POST['email'])){
                            $workflowComment .= ' - Email: '.$_POST['email'];

                            if (($_POST['newsletter'])){
                                $workflowComment .= ' - Newsletter Anmeldung: Ja!';
                            }
                        }


                        $emailBody = '<h3>Es wurde eine neute Datei ("'.[$_FILES['fileToUpload']['name']].'") hochgeladen.</h3>';
                        $emailBody .= '<p>'.$workflowComment.'</p>';

                        $data = '{"receiver":[{"authorityName":"'.$workflowGroup.'"}],"comment":' . $workflowComment . '","status":"200_tocheck"}';
                        $apiUrl = 'rest/node/v1/nodes/-home-/' . $nodeID . '/workflow';

                        if (callRepoApi($apiUrl, $data, 'Content-Type: application/json', 'PUT') !== false) {
                            //echo '<div>Workflow added<br>'.$workflowComment.'</div>';
                            echo '<div class="portal_form_succes">Vielen Dank für deinen Vorschlag!<br>Er wird jetzt von unserem Redaktionteam geprüft.</div>';
                            $success = true;

                            // email data
                            $to = get_bloginfo('admin_email'). ', redaktion@wirlernenonline.de';
                            //$to = get_bloginfo('admin_email');
                            $headers[] = 'From: wirlernenonline.de <redaktion@wirlernenonline.de>';
                            $headers[] = 'Content-Type: text/html; charset=UTF-8';
                            $subject = 'Neuer Vorschlag für das Themenprotal: ' . $pageDiscipline . ' - ' . $pageTitle . ' - ' . $widgetName;
                            $body = $emailBody;

                            // send email
                            wp_mail($to, $subject, $body, $headers);
                        }
                    }
                }
            }else {
                    $consentErr = 'Bitte zustimmen';
            }


        }else if (!empty($_POST['url'])){

            $data_array = array(
                "ccm:wwwurl" => array($_POST['url']),
            );
            $apiUrl = 'rest/node/v1/nodes/-home-/0fa24aa1-709e-4e86-8ac0-a5bbc093e6cb/children?type=ccm%3Aio&renameIfExists=true';
            $node = callRepoApi($apiUrl, json_encode($data_array));
            $nodeID = $node['node']['ref']['id'];

            if ( !empty($nodeID) ){
                $emailBody = '<h3>Es wurde eine neuer Link eingetragen: '.$_POST['url'].' </h3>';
                $workflowComment = 'Für folgende Sammlung vorgeschlagen: ' . $pageDiscipline . ' > ' . $pageTitle . ' > ' . $widgetName . ' (ID: ' . $collectionID . ')';
                $emailBody .= '<p>Für folgende Sammlung vorgeschlagen: '.  $pageDiscipline . ' > ' . $pageTitle . ' > ' . $widgetName.' (ID: '.$collectionID.')</p>';
                $emailBody .= '<p>Details:</p>';
                if (!empty($_POST['titel'])){
                    $workflowComment .= ' - Titel: '.$_POST['titel'];
                    $emailBody .= '<p>Titel: '.$_POST['titel'].'</p>';
                }
                if (!empty($_POST['description'])){
                    $workflowComment .= ' - Beschreibung: '.$_POST['description'];
                    $emailBody .= '<p>Beschreibung: '.$_POST['description'].'</p>';
                }
                if (!empty($_POST['email'])){
                    $workflowComment .= ' - Email: '.$_POST['email'];
                    $emailBody .= '<hr><p>Email: '.$_POST['email'].'</p>';

                    if (($_POST['newsletter'])){
                        $workflowComment .= ' - Newsletter Anmeldung: Ja!';
                        $emailBody .= '<p>Newsletter Anmeldung: Ja!</p>';
                    }
                }

                $data = '{"receiver":[{"authorityName":"GROUP_ORG_WLO-Uploadmanager"}],"comment":"'.$workflowComment.'","status":"200_tocheck"}';
                $apiUrl = 'rest/node/v1/nodes/-home-/'.$nodeID.'/workflow';

                if (callRepoApi($apiUrl, $data, 'Content-Type: application/json', 'PUT') !== false ){
                    //echo '<div>Workflow added<br>'.$workflowComment.'</div>';
                    echo '<div class="portal_form_succes">Vielen Dank für deinen Vorschlag!<br>Er wird jetzt von unserem Redaktionteam geprüft.</div>';
                    $success = true;

                    // email data
                    $to = get_bloginfo('admin_email'). ', redaktion@wirlernenonline.de';
                    //$to = get_bloginfo('admin_email');
                    $headers[] = 'From: wirlernenonline.de <redaktion@wirlernenonline.de>';
                    $headers[] = 'Content-Type: text/html; charset=UTF-8';
                    $subject = 'Neuer Vorschlag für das Themenprotal: ' . $pageDiscipline . ' - ' . $pageTitle . ' - ' . $widgetName;
                    $body = $emailBody;

                    // send email
                    wp_mail($to, $subject, $body, $headers);
                }
            }
        }else{
            $formErr = 'Bitte URL oder Datei angeben.';
        }
    }

    ?>

    <h1>Material vorschlagen</h1>
    <h4>zum Thema <?php echo $widgetName; ?></h4>


    <form action="<?php echo get_page_link($post->ID); ?>"  method=post enctype="multipart/form-data" class="portal_form">
        <?php if(!empty($formErr)) echo '<h4 class="portal_form_error">'.$formErr.'</h4>'; ?>
        <input type="hidden" id="collectionID" name="collectionID" value="<?php echo $collectionID; ?>">
        <input type="hidden" id="widgetName" name="widgetName" value="<?php echo $widgetName; ?>">
        <input type="hidden" id="pageDiscipline" name="pageDiscipline" value="<?php echo $pageDiscipline; ?>">
        <input type="hidden" id="pageTitle" name="pageTitle" value="<?php echo $pageTitle; ?>">
        <div class="portal_form_top_row">
            <div class="portal_form_url">
                <label for="url">Url eingeben:</label>
                <input type="url" id="url" name="url">
            </div>
            <p class="portal_form_url">oder</p>
            <div>
                <label for="file">Material hochladen:</label>
                <input type="file" id="fileToUpload" name="fileToUpload" class="button">
            </div>
        </div>
        <div class="portal_form_url">
            <h4>Optionale Angaben</h4>
            <div>
                <label for="titel">Titel</label>
                <input type="text" id="titel" name="titel">
            </div>
            <div>
                <label for="description">Kurzbeschreibung</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
        </div>

        <div class="portal_form_file">
            <div class="portal_form_checkbox">
                <input type="checkbox" id="consent" name="consent">
                <label for="consent">
                    Einverständniserklärung* <?php if (!empty($consentErr)) echo '<span class="portal_form_error">'.$consentErr.'</span>'; ?>
                    <span class="portal_form_small">Ja, mein Material darf geprüft, bei Bedarf verändert und anschließend unter der gewählten offenen Lizenz veröffentlicht werden. Ich versichere, dass ich alle Rechte habe.</span>
                </label>
            </div>

            <div class="portal_form_license">
                <div class="portal_form_licence_block">
                    <div class="portal_form_checkbox">
                        <input type="checkbox" id="portal_form_ccby" name="portal_form_ccby">
                        <label for="portal_form_ccby">CC BY</label>
                    </div>

                    <label for="author">Ich möchte dass die Urheber:in wie folgt genannt wird:</label>
                    <textarea id="author" name="author" rows="4" placeholder="Wie soll die Urheber:in genannt werden, z.B.  Berta Beispiellos für den Verein für Freie Inhalte"></textarea>

                    <label for="personal_link">Persönlicher Link (Optional)</label>
                    <input type="text" id="personal_link" name="personal_link" placeholder="z.B. zu deiner Webseite oder Twitter-Profil">
                </div>
                <div class="portal_form_licence_block">
                    <input type="checkbox" id="portal_form_cczero" name="portal_form_cczero">
                    <label for="portal_form_cczero">CC Zero</label>
                </div>

            </div>

        </div>


        <div>
            <label for="email">E-Mail</label>
            <input type="email" id="email" name="email">
            <div class="portal_form_checkbox">
                <input type="checkbox" id="newsletter" name="newsletter">
                <label for="newsletter">Newsletter Anmeldung?</label>
            </div>

        </div>
        <div class="portal_form_button">
            <input class="button" type=submit value="Absenden" name="addUrl" />
        </div>

    </form>

    <script type="application/javascript">
        jQuery('input[name=fileToUpload]').change(function(ev) {

            jQuery('.portal_form_url').hide('slow');
            jQuery('.portal_form_file').show('slow');
        });

        <?php
        if (!empty($consentErr)){
            echo "jQuery('.portal_form_url').hide();";
            echo "jQuery('.portal_form_file').show();";
        }
        if ($success){
            echo "jQuery('.portal_form_url').hide();";
            echo "jQuery('.portal_form_file').hide();";
        }
        ?>
    </script>

    <?php

    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php get_footer(); ?>
