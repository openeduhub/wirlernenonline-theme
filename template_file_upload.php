<?php /* Template Name: File Upload (Repo) */

acf_form_head();
get_header();



?>
    <script>
        var meta = null;

        window.addEventListener("message",function(msg){
            if(event.data.event=="POST_DATA") {

                if(event.data.data["cclom:title"])
                    meta = event.data.data;

                if(meta) {

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "<?php echo WLO_REPO; ?>rest/node/v1/nodes/-home-/-inbox-/children?type=ccm%3Aio&renameIfExists=true&versionComment=MAIN_FILE_UPLOAD", true);
                    xhr.setRequestHeader("Content-type", "application/json");
                    xhr.setRequestHeader("Accept", "application/json");
                    xhr.crossDomain = true;
                    xhr.withCredentials = true;
                    xhr.setRequestHeader("Authorization",  "Basic " + btoa("oer_uploader:oer_uploader"));
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status === 200) {
                            var data=xhr.responseText;
                            var jsonResponse = JSON.parse(data);
                            var nodeId = jsonResponse.node.ref.id;
                            var xhr2 = new XMLHttpRequest();

                            var formData = new FormData(document.querySelector('#uploadwlo'));
                            var mimetype = formData.get('file').type;

                            xhr2.open("POST", "<?php echo WLO_REPO; ?>rest/node/v1/nodes/-home-/"+nodeId+"/content?mimetype="+encodeURIComponent(mimetype), true);
                            xhr2.setRequestHeader("Authorization",  "Basic " + btoa("oer_uploader:oer_uploader"));
                            //xhr2.setRequestHeader("Content-type", "multipart/form-data");
                            xhr2.setRequestHeader("Accept", "application/json");
                            xhr2.onreadystatechange = function() {
                                if (xhr2.readyState == 4 && xhr2.status === 200) {
                                    var xhr3 = new XMLHttpRequest();
                                    var payload = '{"inherited":true,"permissions":[{"authority":{"authorityName":"GROUP_WLO-Redaktion","authorityType":"GROUP"},"permissions":["Coordinator"]}]}';
                                    xhr3.open("POST", "<?php echo WLO_REPO; ?>rest/node/v1/nodes/-home-/"+nodeId+"/permissions?mailtext=&sendMail=true", true);
                                    xhr3.setRequestHeader("Authorization",  "Basic " + btoa("oer_uploader:oer_uploader"));
                                    xhr3.setRequestHeader("Content-type", "application/json");
                                    xhr3.setRequestHeader("Accept", "application/json");
                                    xhr3.onreadystatechange = function() {
                                        if (xhr3.readyState == 4 && xhr3.status === 200) {
                                            var xhr4 = new XMLHttpRequest();
                                            var payload2 = '{"receiver":[{"authorityName":"GROUP_WLO-Redaktion"}],"comment":"Upload via Formular","status":"200_tocheck"}\n';
                                            xhr4.open("PUT", "<?php echo WLO_REPO; ?>rest/node/v1/nodes/-home-/"+nodeId+"/workflow", true);
                                            xhr4.setRequestHeader("Authorization",  "Basic " + btoa("oer_uploader:oer_uploader"));
                                            xhr4.setRequestHeader("Content-type", "application/json");
                                            xhr4.setRequestHeader("Accept", "application/json");
                                            xhr4.onreadystatechange = function() {
                                                if (xhr4.readyState == 4 && xhr4.status === 200) {
                                                    alert("Datei wurde hochgeladen!");
                                                } else {
                                                    console.log("Fehler intern");
                                                }
                                            }
                                            xhr4.send(payload2);
                                        } else {
                                            console.log("Fehler intern");
                                        }
                                    }
                                    xhr3.send(payload);
                                } else {
                                    console.log("Fehler intern");
                                }
                            }
                            xhr2.send(formData);
                        } else {
                            console.log("Fehler extern / OPTIONS");
                        }
                    }
                    xhr.send(JSON.stringify(meta));
                }
            }
        });

        function publish(){
            document.getElementById("mds").contentWindow.postMessage({event:"PARENT_FETCH_DATA"},'*');
        }
    </script>

    <div class="grid-container">
    <div class="grid-x grid-margin-x">
        <div class="medium-12 cell">
            <h1 class="page-title"><?php the_title(); ?></h1>
        </div>
        <?php
        while ( have_posts() ) : the_post(); ?>

        <div class="cell large-8">
            <?php
            the_content();

            ?>

        </div>
    <div class="wlo_file_upload">
        <form id="uploadwlo" action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" class="button" name="file" id="file">
            <input type="button" class="button success" onclick="document.getElementById('mds').contentWindow.postMessage({event:'PARENT_FETCH_DATA'},'*');" value="Datei hochladen" name="submit">
        </form>

        <iframe id="mds" class="upload-metadata" src="https://redaktion.openeduhub.net/edu-sharing/components/embed/mds?set=mds_oeh&group=io_wordpress" title="Metadaten" frameBorder="0"></iframe>
    </div>


        <?php endwhile;
        ?>
    </div>
</div>
</div>





<?php
get_footer();
