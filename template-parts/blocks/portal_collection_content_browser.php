<?php if (is_admin()) {
    echo '<div class="backend_border">';
    echo '<div class="backend_hint">Block: Inhalt</div>';
}; ?>
<?php

if (get_the_id()) {
    $postID = get_the_id();
} else {
    $postID = acf_editor_post_id();
}

if (get_field('active')) {

    $url = (!empty(get_field('collection_url'))) ? get_field('collection_url') : get_field('collection_url', $postID);

    $pattern = '/http.*\?id=(.*)(&|$)/';
    preg_match_all($pattern, $url, $matches);

    $url = 'https://redaktion.openeduhub.net/edu-sharing/rest/collection/v1/collections/-home-/' . $matches[1][0] . '/children/references';

    try {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json; charset=utf-8'
            )
        );
        $response = curl_exec($curl);
        if ($response === false) {
            echo 'curl error';
            return false;
        }
    } catch (Exception $e) {
        echo 'curl error: ' . $e->getMessage();
        return false;
    }
    curl_close($curl);

    $response = json_decode($response);

    //Top-Level


    $mediaTypes = array(
        "file-image"=> "Bild",
        "file-word"=> "Word-Dokument",
        "file-excel"=> "Tabelle",
        "file-powerpoint"=> "Präsentation",
        "file-odt"=> "OpenOffice Dokument",
        "file-ods"=> "OpenOffice Tabelle",
        "file-odp"=> "OpenOffice Präsentation",
        "file-zip"=> "Zip-Datei",
        "file-script"=> "Skript",
        "file-xml"=> "XML-Datei",
        "file-audio"=> "Audio",
        "file-txt"=> "Textdatei",
        "file-video"=> "Video",
        "file-qti"=> "ONYX-Datei",
        "file-h5p"=> "H5P-Datei",
        "file-pdf"=> "PDF-Datei",
        "link"=> "Website",
        "folder"=> "Ordner",
        "file"=> "Unbekannt",
        "collection"=> "Sammlung",
        "saved_search"=> "Suche"
    );

    if(!empty($response->references)) {
        echo '<div class="portal-collection-content-browser">';

        if (!empty(get_field('headline')))
            echo '<h3>' . get_field('headline')  . '</h3>';
        else
            echo '<h3>' . 'Materialien' . '</h3>';

        if (get_field('text')) {
            echo '<p>' . get_field('text') . '</p>';
        }

        echo '<div class="portal_content_grid">';
        foreach ($response->references as $reference) {
            ?>
            <a href="<?php echo $reference->content->url; ?>" target="_blank">
                <div class="portal_content_branch">
                    <?php if(!empty($reference->preview->url)){?><img src="<?php echo $reference->preview->url; ?>"><?php };?>
                    <div class="portal_search_text">
                        <h5><?php echo ($reference->properties->{'cclom:title'}[0]) ? $reference->properties->{'cclom:title'}[0] : $reference->properties->{'cm:name'}[0]; ?></h5>&nbsp;&nbsp;
                        <h5 class="media-type"><?php echo $mediaTypes[$reference->mediatype] ?></h5>
                    </div>
                </div>
            </a>
            <?php
        }
        echo '</div>';
        echo '</div>';
    }
}
?>
<?php if (is_admin()) {
    echo '</div>';
}; ?>
