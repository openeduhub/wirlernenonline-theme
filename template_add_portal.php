<?php /* Template Name: TP Inhalte Formular 2 */

/*
 * This is the suggest-content form, that allows users to provide suggestions for new content,
 * tools, or sources to be listed on WLO.
 *
 * When this form is referenced to, query parameters can be used to provide context.
 *
 * Allowed query parameters are
 *  - type: 'material' | 'tool' | 'source'
 *  - pageDiscipline: Vocabs ID valid in http://w3id.org/openeduhub/vocabs/discipline/, e.g. 460 for
 *    "Physics"
 *  - collectionID: Collection ID for a "Themenportal" in edu-sharing, e.g.
 *    "2c8cd907-78b5-46f0-b3e7-7fe171633e44" for "Lyrische Texte"
 *  - lrtID: Complete vocabs URL under
 *    http://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/new_lrt, e.g.
 *    "http://w3id.org/openeduhub/vocabs/new_lrt/44868358-7b1f-42e4-a6b9-c3889d9d2623" for
 *    "Checkliste"
 *
 * Internally, we use an embedded edu-sharing view to provide show input fields to the user. On
 * submission, we read the fields' values and submit a POST request to this same page. When
 * receiving this POST request, we send the data back to edu-sharing.
 */

get_header();
global $post;

$title = get_the_title();
if (isset($_GET['type'])) {
    if ($_GET['type'] == 'source') {
        $title = 'Quellen vorschlagen';
    }
    if ($_GET['type'] == 'tool') {
        $title = 'Tools vorschlagen';
    }
}

?>
<style>
    #mds-frame {
        width: 100%;
        min-height: 1400px;
        display: flex;
        justify-content: center;
    }

    .submit-btn {
        display: flex;
        width: 100%;
        justify-content: flex-end;
        margin: 0.5em;
    }

    .submit-btn .wlo-button {
        margin: 1em;
        cursor: pointer;
    }
</style>

<div class="wlo-page">

    <div class="wlo-header">
        <div class="wlo-header-wrapper wlo-form">
            <div class="wlo-header-content">
                <h1><?php echo $title; ?></h1>
                <?php the_excerpt(); ?>
            </div>
            <div class="wlo-header-bottom"></div>
        </div>
    </div>

    <div class="wlo-wrapper wlo-form-wrapper">

        <?php

        // GET-Params
        if (isset($_GET['collectionID'])) {
            $collectionID = $_GET['collectionID'];
        } else {
            $collectionID = null;
        }
        $pageTitle = '';
        if (isset($_GET['pageTitle'])) {
            $pageTitle = $_GET['pageTitle'];
        }
        $pageDiscipline = '';
        if (isset($_GET['pageDiscipline'])) {
            $pageDiscipline = $_GET['pageDiscipline'];
        }
        $lrtID = array();
        if (isset($_GET['lrtID'])) {
            $lrtID = explode(',', $_GET['lrtID']);
        }

        $mdsGroup = 'wlo_upload_content';
        if (isset($_GET['type'])) {
            if ($_GET['type'] == 'source') {
                $mdsGroup = 'wlo_upload_source';
            } else if ($_GET['type'] == 'tool') {
                $mdsGroup = 'wlo_upload_tool';
            } else if ($_GET['type'] == 'event') {
                $mdsGroup = 'wlo_upload_event';
            }
        }

        if (!defined("WLO_REPO_UPLOAD_LOCATION")) {
            $formErr = 'Interner Konfigurationsfehler, WLO_REPO_UPLOAD_LOCATION muss definiert sein';
        } else if (isset($_POST['mds'])) {
            $formErr = $formOk = '';
            $success = false;
            $collectionID = $_POST['collectionID'];
            $pageDiscipline = $_POST['pageDiscipline'];
            $pageTitle = $_POST['pageTitle'];
            /*https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/discipline/*/
            $mdsData = json_decode(stripslashes($_POST["mds"]), true);
            // print_r($mdsData);
            //first api-call to create node
            if ($pageDiscipline) {
                $mdsData["ccm:taxonid"] = ['http://w3id.org/openeduhub/vocabs/discipline/' . $pageDiscipline];
            }

            if (isset($mdsData['fileupload-filedata'])) {
                $mdsData["cm:name"] = $mdsData['fileupload-filename'];
            } else if (isset($mdsData['fileupload-link'])) {
                $mdsData["ccm:wwwurl"] = $mdsData['fileupload-link'];
            }
            // prefix with http if missing
            if ($mdsData["ccm:wwwurl"] && strtolower(substr($mdsData["ccm:wwwurl"][0], 0, 4)) != 'http') {
                $mdsData["ccm:wwwurl"][0] = 'http://' . $mdsData["ccm:wwwurl"][0];
            }
            // $mdsData["ccm:objecttype"] = [$objectType];
            $mdsData["ccm:editorial_checklist"] = ['0'];
            // to generate thumbs
            $mdsData["ccm:linktype"] = ['USER_GENERATED'];

            // unfold license
            if (!empty($mdsData["ccm:custom_license"][0])) {
                preg_match('/.*\/(.*)/', $mdsData["ccm:custom_license"][0], $license);
                $license = $license[1];
                if (substr($license, -3) == '_40') {
                    $mdsData['ccm:commonlicense_key'] = [substr($license, 0, -3)];
                    $mdsData['ccm:commonlicense_cc_version'] = ['4.0'];
                } else if ($license == 'OTHER') {
                    // do nothing
                } else {
                    $mdsData['ccm:commonlicense_key'] = [$license];
                }
            }
            // clean up empty keyword
            $kw = @$mdsData['cclom:general_keyword'];
            if (!$kw || count($kw) == 0) {
                unset($mdsData['cclom:general_keyword']);
            }

            // get geo coordinates
            if (!empty($mdsData['ccm:oeh_geographical_location_address_freetext'][0])) {
                $results = callGeocodingApi($mdsData['ccm:oeh_geographical_location_address_freetext'][0]);
                if (!empty($results)) {
                    // Just take the first result for now
                    $location = $results[0]['geometry']['location'];
                    $formattedAddress = $results[0]['formatted_address'];
                    $mdsData['ccm:oeh_geographical_location_lat'] = [$location['lat']];
                    $mdsData['ccm:oeh_geographical_location_lng'] = [$location['lng']];
                    $mdsData['ccm:oeh_geographical_location_address_formatted'] = [$formattedAddress];
                }
            }

            if (!empty($mdsData['virtual:email'])) {
                $mdsData['ccm:metadatacontributer_creator'] = ["BEGIN:VCARD\nVERSION:3.0\nN:Upload;WLO\nFN:WLO Upload\nORG:\nURL:\nTITLE:\nTEL;TYPE=WORK,VOICE:\nADR;TYPE=intl,postal,parcel,work:;;;;;;\nX-ES-LOM-CONTRIBUTE-DATE:\nEMAIL;TYPE=PREF,INTERNET:" . $mdsData['virtual:email'][0] . "\nEND:VCARD\n"];
                if (isset($mdsData['virtual:newsletter']) && $mdsData['virtual:newsletter'][0] == 'true') {
                    $data = array(
                        'fields[email]' => $mdsData['virtual:email'][0],
                        'ml-submit' => 1
                    );
                    $url = 'https://static.mailerlite.com/webforms/submit/c6v7a9';
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/x-www-form-urlencoded',
                        'Accept: /',
                    ]);
                    $response = curl_exec($ch);
                    curl_close($ch);
                }
            }

            $location = @$mdsData["virtual:publish_location"][0];
            if (isset($location) && !empty(trim($location[0]))) {
                preg_match('/.*\/(.*)/', $location[0], $matches);
                $location = $matches[1];
            } else {
                $location = "Ohne Kategorie";
            }

            $apiUrl = 'rest/node/v1/nodes/-home-/' . WLO_REPO_UPLOAD_LOCATION . '/children';
            $nodes = callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET')['nodes'];
            $parent = null;
            foreach ($nodes as $n) {
                if (strpos($n['name'], $location) !== FALSE) {
                    $parent = $n['ref']['id'];
                    break;
                }
            }
            if (!$parent) {
                $formErr = 'Interner Verarbeitungsfehler: Gruppenordner ' . $location . ' nicht gefunden';
            } else {
                $apiUrl = 'rest/node/v1/nodes/-home-/' . $parent . '/children?type=ccm:io&renameIfExists=true';
                error_log('mdsData: ' . json_encode($mdsData));
                $response = callRepoApi($apiUrl, json_encode($mdsData));
                $nodeID = $response['node']['ref']['id'];
                //second api-call for file upload
                if (!empty($nodeID)) {
                    //echo 'Uploading File...<br>';
                    // fix auto-generated www title, description, etc.
                    if ($mdsData['ccm:wwwurl']) {
                        $apiUrl = 'rest/node/v1/nodes/-home-/' . $nodeID . '/metadata?versionComment=WLO-Uploadformular';
                        callRepoApi($apiUrl, json_encode($mdsData));
                    }
                    $apiUrl = 'rest/node/v1/nodes/-home-/' . $nodeID . '/content?versionComment=MAIN_FILE_UPLOAD' .
                        (!empty($mdsData['fileupload-filetype'][0]) ? '&mimetype=' . $mdsData['fileupload-filetype'][0] : '');
                    if (!empty($mdsData['fileupload-filedata'][0])) {
                        $uploadFile = tempnam(".", "upload_");
                        $filedata = substr($mdsData['fileupload-filedata'][0], strpos($mdsData['fileupload-filedata'][0], ',') + 1);
                        if (file_put_contents($uploadFile, base64_decode($filedata)) !== false) {
                            $fields = [
                                'file' => new \CurlFile($uploadFile, $mdsData['fileupload-filetype'][0], $mdsData['fileupload-filename'][0])
                            ];

                            if (callRepoApi($apiUrl, $fields, 'Content-Type: multipart/form-data') === false) {
                                $formErr = 'Fehler beim Dateiupload';
                            }
                        } else {
                            $formErr = 'Datei kann nicht geschrieben werden';
                        }
                        @unlink($uploadFile);
                    }
                    if (!$formErr) {
                        $workflowComment = 'Für folgende Sammlung(en) vorgeschlagen: ';
                        if (count(@$mdsData['ccm:curriculum'])) {
                            array_walk($mdsData['ccm:curriculum'], function (&$m) {
                                $m = explode('/', $m);
                                $m = $m[count($m) - 1];
                            });
                            $workflowComment .= implode(',', $mdsData['ccm:curriculum']);
                            foreach ($mdsData['ccm:curriculum'] as $node) {
                                if(trim($node)) {
                                    $apiUrl = 'rest/collection/v1/collections/-home-/' . rawurlencode(basename($node)) . '/references/' . $nodeID . '?asProposal=true';
                                    callRepoApi($apiUrl, null, 'Content-Type: application/json', 'PUT');
                                }
                            }
                        }

                        if (!empty($mdsData['fileupload-link'])) {
                            $emailBody = '<h3>Es wurde eine neuer Link vorgeschlagen.</h3>';
                            $emailBody .= '<p>Titel: ' . $mdsData['cclom:title'][0] . '</p>';
                            $emailBody .= '<p>Link: ' . $mdsData['fileupload-link'][0] . '</p>';
                        } else if ($mdsData['ccm:objecttype'][0] == 'SOURCE') {
                            $emailBody = '<h3>Es wurde eine neue Quelle vorgeschlagen ("' . $mdsData['cclom:title'][0] . '")</h3>';
                            $emailBody .= '<p>Quellen-Url: ' . $mdsData['ccm:wwwurl'][0] . '</p>';
                        } else if ($mdsData['ccm:objecttype'][0] == 'TOOL') {
                            $emailBody = '<h3>Es wurde eine neues Tool vorgeschlagen ("' . $mdsData['cclom:title'][0] . '")</h3>';
                            $emailBody .= '<p>Tool-Url: ' . $mdsData['ccm:wwwurl'][0] . '</p>';
                        } else {
                            if ($mdsData['cm:name']) {
                                $emailBody = '<h3>Es wurde eine neue Datei ("' . $mdsData["cm:name"][0] . '") hochgeladen.</h3>';
                            } else {
                                $emailBody = '<h3>Es wurde ein neuer Link ("' . $mdsData["ccm:wwwurl"][0] . '") vorgeschlagen.</h3>';
                            }

                            $emailBody .= '<h4>Zusätzliche Informationen:</h4>';
                            if (!empty($mdsData['ccm:curriculum'])) {
                                $emailBody .= '<p>Sammlung: ' . get_mds_values($mdsData['ccm:curriculum']) . '</p>';
                            }
                            if (!empty($mdsData['ccm:educationallearningresourcetype'])) {
                                $emailBody .= '<p>Materialart: ' . get_mds_values($mdsData['ccm:educationallearningresourcetype']) . '</p>';
                            }
                            if (!empty($mdsData['ccm:toolCategory'])) {
                                $emailBody .= '<p>Tool-Art: ' . get_mds_values($mdsData['ccm:toolCategory']) . '</p>';
                            }
                            if (!empty($mdsData['ccm:custom_license'])) {
                                $emailBody .= '<p>Lizenz: ' . get_mds_values($mdsData['ccm:custom_license']) . '</p>';
                            }
                            if (!empty($mdsData['ccm:author_freetext'])) {
                                $emailBody .= '<p>Autor:in/Ersteller:in: ' . get_mds_values($mdsData['ccm:author_freetext']) . '</p>';
                            }
                            if (!empty($mdsData['cclom:general_description'])) {
                                $emailBody .= '<p>Beschreibung: ' . get_mds_values($mdsData['cclom:general_description']) . '</p>';
                            }
                            if (!empty($mdsData['ccm:educationalcontext'])) {
                                $emailBody .= '<p>Bildunsgstufe: ' . get_mds_values($mdsData['ccm:educationalcontext']) . '</p>';
                            }
                            if (!empty($mdsData['ccm:educationalintendedenduserrole'])) {
                                $emailBody .= '<p>Zielgruppe: ' . get_mds_values($mdsData['ccm:educationalintendedenduserrole']) . '</p>';
                            }
                            if (!empty($mdsData['ccm:conditionsOfAccess'])) {
                                $emailBody .= '<p>Anmeldung: ' . get_mds_values($mdsData['ccm:conditionsOfAccess']) . '</p>';
                            }
                            if (!empty($mdsData['ccm:price'])) {
                                $emailBody .= '<p>Kosten: ' . get_mds_values($mdsData['ccm:price']) . '</p>';
                            }
                            if (!empty($mdsData['cclom:general_keyword'])) {
                                $emailBody .= '<p>Schlagwort: ' . get_mds_values($mdsData['cclom:general_keyword']) . '</p>';
                            }
                            if (!empty($mdsData['virtual:email'])) {
                                $emailBody .= '<p>Vorgeschlagen von: ' . get_mds_values($mdsData['virtual:email']) . '</p>';
                            }
                        }
                        //$emailBody .= '<pre>'.print_r($mdsData, true).'</pre>';

                        $data = [
                            "receiver" => [[
                                "authorityName" => "GROUP_ORG_WLO-Uploadmanager"
                            ]],
                            "status" => "200_tocheck",
                            //"comment" => $workflowComment,
                            "comment" => strip_tags($emailBody)
                        ];
                        $apiUrl = 'rest/node/v1/nodes/-home-/' . $nodeID . '/workflow';
                        if (callRepoApi($apiUrl, json_encode($data), 'Content-Type: application/json', 'PUT') !== false) {
                            // echo '<div>Workflow added<br>'.$workflowComment.'</div>';
                            $success = true;
                            // email data
                            $to = get_bloginfo('admin_email') . ', redaktion@wirlernenonline.de';
                            //$to = get_bloginfo('admin_email');
                            $headers[] = 'From: wirlernenonline.de <redaktion@wirlernenonline.de>';
                            $headers[] = 'Content-Type: text/html; charset=UTF-8';

                            $subject = 'Neuer Vorschlag über das Mitmachformular ';
                            if (!empty($mdsData['fileupload-link'])) {
                                $subject .= '(Link: ' . $mdsData['cclom:title'][0] . ')';
                            } else if ($mdsData['ccm:objecttype'][0] == 'SOURCE') {
                                $subject .= '(Quelle: ' . $mdsData['cclom:title'][0] . ')';
                            } else if ($mdsData['ccm:objecttype'][0] == 'TOOL') {
                                $subject .= '(Tool: ' . $mdsData['cclom:title'][0] . ')';
                            } else {
                                $subject .= '(Datei: ' . $mdsData['cclom:title'][0] . ')';
                            }
                            $body = $emailBody;

                            if (strpos(WLO_REPO, 'repository.staging') === false) {
                                // send email
                                wp_mail($to, $subject, $body, $headers);
                                $formOk = 'Vielen Dank für deinen Vorschlag!<br>Er wird jetzt von unserem Redaktionteam geprüft.';

                                $post = get_post(url_to_postid('inhalt-hinzugefuegt'));
                                wp_redirect(get_page_link($post->ID) . '?type=' . $_GET['type']);
                            } else {
                                echo '<h4 class="portal_form_succes">Staging Formular, E-Mail Benachrichtigung + Redirect wurde übersprungen</h4>';
                                echo '<pre>' . $body . '</pre>';
                            }
                        }
                    }
                } else {
                    $formErr = 'Interner Verarbeitungsfehler. Bitte später nochmal versuchen';
                }
            }
        }


        function get_mds_values($mds)
        {
            $values = '';
            if (is_array($mds)) {
                $numItems = count($mds);
                $i = 0;
                foreach ($mds as $value) {
                    if (strpos($value, 'w3id.org')) {
                        $value = basename($value);
                    }
                    if (++$i === $numItems) {
                        $values .= $value;
                    } else {
                        $values .= $value . ', ';
                    }
                }
            } else {
                $values = $mds;
            }
            return $values;
        }
        ?>

        <?php
        if (isset($pageTitle) && $pageTitle) {
            echo "<h4>Für das Thema $pageTitle</h4>";
        }
        ?>
        <?php if (!empty($formErr)) echo '<h4 class="portal_form_error">' . $formErr . '</h4>'; ?>
        <?php if (!empty($formOk)) echo '<h4 class="portal_form_succes">' . $formOk . '</h4>'; ?>

        <?php
        $iFrameSrc = WLO_REPO . 'components/embed/mds'
            . '?set=mds_oeh'
            . '&group=' . $mdsGroup
            . '&data=' . urlencode(json_encode([
                "ccm:curriculum" => (isset($collectionID)
                    ? ['http://w3id.org/openeduhub/vocabs/oeh-topics/' . $collectionID]
                    : []),
                "ccm:oeh_lrt" => (isset($lrtID)
                    ? $lrtID
                    : []),
            ]));
        ?>
        <iframe
            id="mds-frame"
            class="wlo-form-iframe"
            style="opacity: 0"
            src="<?php echo $iFrameSrc; ?>"
            frameborder=0>
        </iframe>
        <!--
            We listen for a message from the embedded edu-sharing page that indicates that the
            "post" button has been pressed and submit this form after validating the filled-in data.
        -->
        <form
            action="<?php echo get_page_link($post->ID); ?>?type=<?php echo isset($_GET['type']) ? $_GET["type"] : ''; ?>"
            method=post
            enctype="multipart/form-data"
            id="formAdd">
            <!-- Data received via query parameters -->
            <input type="hidden" id="i" name="collectionID" value="<?php echo @$collectionID; ?>">
            <input type="hidden" id="pageDiscipline" name="pageDiscipline" value="<?php echo @$pageDiscipline; ?>">
            <input type="hidden" id="lrtID" name="lrtID" value="<?php echo @implode(',', $lrtID); ?>">
            <input type="hidden" id="pageTitle" name="pageTitle" value="<?php echo @$pageTitle; ?>">
            <!-- Data from edu-sharing form as stringified JSON -->
            <input type="hidden" id="formMds" name="mds">
        </form>

        <script type="application/javascript">
            <?php
            if (!empty($consentErr)) {
                echo "jQuery('.portal_form_url').hide();";
                echo "jQuery('.portal_form_file').show();";
            }
            if (isset($success)) {
                echo "jQuery('.portal_form_url').hide();";
                echo "jQuery('.portal_form_file').hide();";
            }
            ?>

            function receiveMessage(event) {
                if (event.data.event === 'CONTENT_HEIGHT') {
                    document.getElementById('mds-frame').style.height = (event.data.data) + 'px';
                    // timeout to make sure mds is prepared by edu-sharing
                    setTimeout(() => document.getElementById('mds-frame').style.opacity = 1, 1000);
                } else if (event.data.event == 'POST_DATA') {
                    const mds = event.data.data;
                    console.log(event.data);
                    if (mds == null) {
                        alert(
                            'Bitte fügen Sie eine Datei/Link hinzu und geben dem Inhalt einen Titel.'
                        );
                        return;
                    }
                    // console.log(mds);
                    const size = mds['fileupload-filedata'] ? mds['fileupload-filedata'][0].length *
                        0.768 : 0;
                    // console.log(size / 1024 / 1024);
                    if (
                        (!mds['ccm:wwwurl'] && !mds['fileupload-link']) &&
                        !size || size > 1024 * 1024 * 100
                    ) {
                        alert('Die Größe von Dateien ist aktuell auf 100MB begrenzt');
                        return;
                    }
                    document.getElementById('formMds').value = JSON.stringify(mds);
                    document.getElementById('formAdd').submit();
                }
            }
            window.addEventListener("message", receiveMessage, false);
        </script>

        <?php

        while (have_posts()) : the_post();
            the_content();
        endwhile;
        ?>

    </div>
</div>

<?php get_footer(); ?>