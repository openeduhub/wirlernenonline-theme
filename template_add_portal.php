<?php /* Template Name: TP Inhalte Formular 3 */
get_header();
global $post;

?>
<style>
#mds-frame{
	width: 100%;
	min-height: 1400px;
	max-width: 900px;
	display: flex;
	justify-content: center;
}
.submit-btn{
	display: flex;
	width: 100%;
	max-width: 900px;
	justify-content: flex-end;
	margin: auto;
}
</style>
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

	if(!defined("WLO_REPO_UPLOAD_LOCATION")){
		$formErr = 'Interner Konfigurationsfehler, WLO_REPO_UPLOAD_LOCATION muss definiert sein';
	} else if($_POST['mds']){
        $formErr = $formOk = '';
        $success = false;
        $collectionID = $_POST['collectionID'];
        $widgetName = $_POST['widgetName'];
        $pageDiscipline = $_POST['pageDiscipline'];
        $pageTitle = $_POST['pageTitle'];
/*https://vocabs.openeduhub.de/w3id.org/openeduhub/vocabs/discipline/*/
		$mdsData = json_decode(stripslashes($_POST["mds"]), true);
		// print_r($mdsData);
		//first api-call to create node
		$mdsData["ccm:taxonid"] = ['http://w3id.org/openeduhub/vocabs/discipline/' . $pageDiscipline];

		if($mdsData['fileupload-filedata']){
			$mdsData["cm:name"] = $mdsData['fileupload-filename'];
		} else {
			$mdsData["ccm:wwwurl"] = $mdsData['fileupload-link'];
		}
		$apiUrl = 'rest/node/v1/nodes/-home-/'.WLO_REPO_UPLOAD_LOCATION.'/children?type=ccm%3Aio&renameIfExists=true';
		$nodeID = callRepoApi($apiUrl, json_encode($mdsData))['node']['ref']['id'];
		//second api-call for file upload
		if (!empty($nodeID)) {
			//echo 'Uploading File...<br>';

			$apiUrl = 'rest/node/v1/nodes/-home-/' . $nodeID . '/content?versionComment=MAIN_FILE_UPLOAD&mimetype=' . $mdsData['fileupload-filetype'][0];
			if($mdsData['fileupload-filedata']) {
				$uploadFile = tempnam(".", "upload_");
				$filedata = substr($mdsData['fileupload-filedata'][0], strpos($mdsData['fileupload-filedata'][0],',') + 1);
				if(file_put_contents($uploadFile,base64_decode($filedata)) !== false){
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
			if(!$formErr){
				$workflowComment = 'Für folgende Sammlung vorgeschlagen: ' . $pageDiscipline . ' > ' . $pageTitle . ' > ' . $widgetName . ' (ID: ' . $collectionID . ')';


				$emailBody = '<h3>Es wurde eine neute Datei ("'.[$_FILES['fileToUpload']['name']].'") hochgeladen.</h3>';
				$emailBody .= '<p>'.$workflowComment.'</p>';

				$data = [
					"receiver" => [[
						"authorityName" => "GROUP_ORG_WLO-Uploadmanager"
					]],
					"status" => "200_tocheck",
					"comment" => $workflowComment
				];
				$apiUrl = 'rest/node/v1/nodes/-home-/' . $nodeID . '/workflow';
				if (callRepoApi($apiUrl, json_encode($data), 'Content-Type: application/json', 'PUT') !== false) {
					//echo '<div>Workflow added<br>'.$workflowComment.'</div>';
					$success = true;

					// email data
					$to = get_bloginfo('admin_email'). ', redaktion@wirlernenonline.de';
					//$to = get_bloginfo('admin_email');
					$headers[] = 'From: wirlernenonline.de <redaktion@wirlernenonline.de>';
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$subject = 'Neuer Vorschlag für das Themenprotal: ' . $pageDiscipline . ' - ' . $pageTitle . ' - ' . $widgetName;
					$body = $emailBody;

					// send email
					//wp_mail($to, $subject, $body, $headers);
					$formOk = 'Vielen Dank für deinen Vorschlag!<br>Er wird jetzt von unserem Redaktionteam geprüft.';
		}
	}
	}else {
			$formErr = 'Interner Verarbeitungsfehler. Bitte später nochmal versuchen';
	}
    }

    ?>

    <h1>Material vorschlagen</h1>
    <h4>zum Thema <?php echo $widgetName; ?></h4>
	<?php if(!empty($formErr)) echo '<h4 class="portal_form_error">'.$formErr.'</h4>'; ?>

	<?php if(!empty($formOk)) echo '<h4 class="portal_form_succes">'.$formOk.'</h4>'; ?>

	<iframe id="mds-frame" src="https://redaktion-staging.openeduhub.net/edu-sharing/components/embed/mds?set=mds_oeh&group=sidebar&data=<?php
		echo urlencode(json_encode([
			"ccm:curriculum" => ['http://w3id.org/openeduhub/vocabs/oeh-topics/' . $collectionID],
		]));
	?>
	" frameborder=0></iframe>
    <form action="<?php echo get_page_link($post->ID); ?>"  method=post enctype="multipart/form-data" id="formAdd">
        <input type="hidden" id="collectionID" name="collectionID" value="<?php echo $collectionID; ?>">
        <input type="hidden" id="widgetName" name="widgetName" value="<?php echo $widgetName; ?>">
        <input type="hidden" id="pageDiscipline" name="pageDiscipline" value="<?php echo $pageDiscipline; ?>">
        <input type="hidden" id="pageTitle" name="pageTitle" value="<?php echo $pageTitle; ?>">
		<input type="hidden" id="formMds" name="mds">
	</form>
        <div class="portal_form_button submit-btn">
            <button class="button" id="mds-submit" onclick="submitForm()" style="display:none">Absenden</button>
        </div>


    <script type="application/javascript">
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
		function submitForm() {
			document.getElementById('mds-submit').disabled = true;
			document.getElementById('mds-frame').contentWindow.postMessage({event:'PARENT_FETCH_DATA'}, '*');
		}
		function receiveMessage(event){
			if(event.data.event === 'CONTENT_HEIGHT'){
				document.getElementById('mds-frame').style.height = event.data.data + 'px';
				// timeout to make sure mds is prepared by edu-sharing
				setTimeout(() => document.getElementById('mds-submit').style.display = '', 2000);
			}
			if(event.data.event !== 'CONTENT_HEIGHT') {
				console.log(event);
			}
			if(event.data.event == 'POST_DATA'){
				const mds = event.data.data;
				if(mds==null){
					alert('Bitte füllen Sie alle notwendigen Daten aus');
					document.getElementById('mds-submit').disabled = false;
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

<?php get_footer(); ?>
