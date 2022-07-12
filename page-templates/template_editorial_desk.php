<?php
/*
Template Name: WlO-Redaktionsumgebung
Template Post Type: page
 */

get_header('editorial-desk');
?>
<?php

if (function_exists('get_repo_ticket')){
    $ticket = get_repo_ticket();
}else{
    $ticket = '';
}

$user = wp_get_current_user();

$metaQS_pageID = '67870';  //live
if (get_site_url() != 'https://wirlernenonline.de'){
    $metaQS_pageID = '44703';  //dev
}

if(get_the_ID() == $metaQS_pageID){
    $mainSubject = get_the_author_meta( 'mainSubject', $user->ID );
    $apiUrl = 'rest/iam/v1/people/-home-/-me-/memberships?maxItems=100&skipCount=0';
    //$apiUrl = 'rest/iam/v1/people/-home-/'.$username.'/memberships?maxItems=100&skipCount=0';
    $memberships = callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET', $ticket);

    $groups = array();
    foreach ($memberships['groups'] as $group){
        if (isset($group['properties']['ccm:groupType'][0])){
            if ($group['properties']['ccm:groupType'][0] == 'EDITORIAL' &&
                $group['properties']['cm:authorityName'][0] != 'GROUP_513e4a78-4a56-103a-84e4-2b017690ecd2') {

                $name = str_replace('WLO-', '', $group['properties']['cm:authorityDisplayName'][0]);
                $groups[] = array(
                    'id' => wlo_getPortalIDbyName($name),
                    'name' => $name
                );
            }
        }
    }

    if (empty($groups)){
        $groups[] = array(
            'id' => '0',
            'name' => 'Keine EDITORIAL Gruppen'
        );;
    }

    if (isset($_GET["subject"])){
        $GLOBALS['wlo_redaktion'] = array(
            'subject' => $_GET["subject"],
        );
    }else if (!empty($mainSubject) && $mainSubject != 'Allgemein'){
        $GLOBALS['wlo_redaktion'] = array(
            'subject' => $mainSubject,
        );
    }else{
        $GLOBALS['wlo_redaktion'] = array(
            'subject' => $groups[0]['name'],
        );
    }
}


while (have_posts()) : the_post(); ?>
<div class="wlo-page">
    <div class="wlo-editorial-page">
        <div class="wlo-editorial-page-header">

            <div class="wlo-editorial-topbar">
                <h1 class="wlo-editorial-page-title">
                    <img class="editorial-menu-icon" src="<?php echo get_template_directory_uri(); ?>'/src/assets/img/menu_icon.svg'">
                    <?php the_title(); ?>
                </h1>

                <div class="wlo-editorial-user-wrapper">
                    <?php if (get_the_ID() == $metaQS_pageID): ?>
                        <div class="editorial-user-settings">
                            <label for="subject">Fach:</label>
                            <select name="subject <?php echo $GLOBALS['wlo_redaktion']['subject']; ?>" id="portal" onchange="metaQS_setCollectionID(this.value, jQuery(this).find('option:selected').attr('name'))">
                                <?php
                                    foreach ($groups as $subject){
                                        $subjectName = $subject['name'];

                                        if ($subjectName == $GLOBALS['wlo_redaktion']['subject'] ){
                                            echo '<option selected="selected" value="'.$subject['id'].'" name="'.$subjectName.'">'.$subjectName.'</option>';
                                        }else{
                                            echo '<option value="'.$subject['id'].'" name="'.$subjectName.'">'.$subjectName.'</option>';
                                        }
                                    }
                                ?>
                            </select>

                            <script>
                                function metaQS_setCollectionID(id, name){
                                    jQuery("app-meta-widget").each(function() {
                                        jQuery( this ).attr("collectionid", id);
                                    });

                                    jQuery(".metaqs-badge").each(function() {
                                        jQuery( this ).html(name);
                                    });
                                }
                            </script>
                        </div>
                    <?php endif; ?>
                    <div class="wlo-editorial-user">
                        <img class="editorial-user-image" src="<?php echo esc_url( get_avatar_url( $user->ID ) ); ?>">
                        <div class="editorial-user-name"><?php echo $user->user_nicename; ?></div>
                    </div>
                </div>


            </div>

            <div class="wlo-editorial-bottombar">

                <div class="wlo-new-content">
                    <button class="wlo-new-content-button">
                        <div>
                            <img src="<?php echo get_template_directory_uri(); ?>'/src/assets/img/plus.svg'">
                            <span>Neu</span>
                        </div>
                    </button>
                    <div class="wlo-new-content-dropdown">
                        <a href="<?php echo WLO_REPO.'components/upload?ticket='.$ticket; ?>" target="_blank">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/upload.svg"> Neues Material
                        </a>
                        <hr>
                        <a href="<?php echo WLO_REPO.'components/search?connector=H5P&ticket='.$ticket; ?>" target="_blank">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/edit.svg"> H5P
                        </a>
                        <a href="<?php echo WLO_REPO.'components/search?connector=ONLY_OFFICE&ticket='.$ticket; ?>" target="_blank">
                            <img src="<?php echo get_template_directory_uri(); ?>/src/assets/img/edit.svg"> OnlyOffice
                        </a>
                        <!--        <a href="--><?php //echo WLO_REPO.'components/workspace?connector=MOODLE&ticket='.$ticket; ?><!--" target="_blank">Moodle erstellen</a>-->
                    </div>
                </div>



            </div>

            <script>
                jQuery(document).ready(function(){

                    jQuery(".wlo-editorial-page-title").click( function (){
                        jQuery(".editorial-sidebar").toggleClass('hide-wlo-sidebar');
                        jQuery('#editorial-sidebar-blur').fadeToggle('fast');
                    });

                });

                function handleSubmit(event) {
                    event.preventDefault();

                    const data = new FormData(event.target);
                    let subject = jQuery('#subject__dropdown__selected span').html();
                    let educationalContext = jQuery('#educontext__dropdown__selected span').html();

                    if (subject === 'Fach'){
                        subject = null;
                    }

                    if (educationalContext === 'Bildungsstufe'){
                        educationalContext = null;
                    }

                    let search = '';
                    if (data.get('q')){
                        search += 'q='+data.get('q');
                    }

                    let filter = '';
                    if (subject){
                        filter += '"discipline":["'+subject+'"]';
                    }
                    if (educationalContext){
                        if (subject){
                            filter += ',';
                        }
                        filter += '"educationalContext":["'+educationalContext+'"]';
                    }
                    if (filter){
                        filter = 'filters={'+filter+'}&';
                    }

                    window.open("<?php echo WLO_SEARCH; ?>de/search?"+filter+search,"_self");
                }

                const form = document.querySelector('#wlo-frontpage-search-form');
                if(form){
                    form.addEventListener('submit', handleSubmit);
                }

                function toggleHiddenSubjects(){
                    if (window.innerWidth <= 900){
                        jQuery('#hidden-subjects').css('display', 'grid');
                    }else{
                        jQuery('#hidden-subjects').css('display', 'flex');
                    }
                    jQuery('#hidden-button').hide();
                }


                const SPACEBAR_KEY_CODE = [0, 32];
                const ENTER_KEY_CODE = 13;
                const DOWN_ARROW_KEY_CODE = 40;
                const UP_ARROW_KEY_CODE = 38;
                const ESCAPE_KEY_CODE = 27;


                const list = document.querySelectorAll('.dropdown__list');
                const listContainer = document.querySelectorAll(".dropdown__list-container");
                const dropdownArrow = document.querySelectorAll(".dropdown__arrow");
                const listItems = document.querySelectorAll(".dropdown__list-item");
                const dropdownSelectedNode = document.querySelectorAll(".dropdown__selected");
                const listItemIds = [];



                dropdownSelectedNode.forEach(item => item.addEventListener("keydown", e =>
                    onKeyDown(e)
                ));


                listItems.forEach(item => listItemIds.push(item.id));

                listItems.forEach(item => {
                    item.addEventListener("click", e => {
                        setSelectedListItem(e);
                        closeList();
                    });

                    item.addEventListener("keydown", e => {
                        switch (e.keyCode) {
                            case ENTER_KEY_CODE:
                                setSelectedListItem(e);
                                closeList();
                                return;

                            case DOWN_ARROW_KEY_CODE:
                                focusNextListItem(DOWN_ARROW_KEY_CODE);
                                return;

                            case UP_ARROW_KEY_CODE:
                                focusNextListItem(UP_ARROW_KEY_CODE);
                                return;

                            case ESCAPE_KEY_CODE:
                                closeList();
                                return;

                            default:
                                return;
                        }
                    });
                });

                window.addEventListener('click', function(e) {
                    var target = jQuery(e.target);
                    let targetIsButton = jQuery(target).closest('.dropdown').length;
                    let targetIsOpen = jQuery(target).closest('.dropdown').find('.open').length;
                    let targetIsMenu = jQuery(target).closest('.dropdown__list-container').length;

                    if (targetIsMenu){
                        return;
                    }

                    closeList();
                    if (targetIsButton){
                        if (!targetIsOpen){
                            openList(e);
                        }
                    }

                });

                function setSelectedListItem(e) {
                    let selectedTextToAppend = document.createTextNode(e.target.innerText);
                    e.target.closest('.dropdown').querySelector('.dropdown__selected span').innerHTML = null;
                    e.target.closest('.dropdown').querySelector('.dropdown__selected span').appendChild(selectedTextToAppend);
                }

                function closeList() {
                    dropdownArrow.forEach(item => item.classList.remove("expanded"));
                    listContainer.forEach(item => item.classList.remove("open"));
                }

                function openList(e){
                    const listContainer = jQuery(e.target).closest('.dropdown').find('.dropdown__list-container');
                    const dropdownArrow = jQuery(e.target).closest('.dropdown').find('.dropdown__arrow');

                    listContainer.addClass("open");
                    dropdownArrow.addClass("expanded");

                }

                function onKeyDown(e) {
                    let openDropDown = SPACEBAR_KEY_CODE.includes(e.keyCode) || e.keyCode === ENTER_KEY_CODE;

                    if (e.keyCode === ESCAPE_KEY_CODE) {
                        closeList();
                    }

                    if (openDropDown) {
                        //openList(e);
                    }

                    if (e.keyCode === DOWN_ARROW_KEY_CODE) {
                        focusNextListItem(DOWN_ARROW_KEY_CODE);
                        e.stopPropagation();
                        e.preventDefault();
                    }

                    if (e.keyCode === UP_ARROW_KEY_CODE) {
                        focusNextListItem(UP_ARROW_KEY_CODE);

                        e.stopPropagation();
                        e.preventDefault();
                    }
                }

                function focusNextListItem(direction) {
                    const activeElementId = document.activeElement.id;
                    if (activeElementId === "dropdown__selected") {
                        document.querySelector(`#${listItemIds[0]}`).focus();
                    } else {
                        const currentActiveElementIndex = listItemIds.indexOf(
                            activeElementId
                        );
                        if (direction === DOWN_ARROW_KEY_CODE) {
                            const currentActiveElementIsNotLastItem =
                                currentActiveElementIndex < listItemIds.length - 1;
                            if (currentActiveElementIsNotLastItem) {
                                const nextListItemId = listItemIds[currentActiveElementIndex + 1];
                                document.querySelector(`#${nextListItemId}`).focus();
                            }
                        } else if (direction === UP_ARROW_KEY_CODE) {
                            const currentActiveElementIsNotFirstItem =
                                currentActiveElementIndex > 0;
                            if (currentActiveElementIsNotFirstItem) {
                                const nextListItemId = listItemIds[currentActiveElementIndex - 1];
                                document.querySelector(`#${nextListItemId}`).focus();
                            }
                        }
                    }
                }
            </script>
        </div>

        <div class="wlo-wrapper">
            <?php the_content(); ?>
        </div>
    </div>
</div>

<?php
endwhile;
?>

</main><!-- .site-content -->


<?php wp_footer(); ?>
</body>
</html>
