<?php

function setWLOInbox(){
    // will be read in the order as defined
    // the first matching group the person is member of will map to the folder!
    $MAPPINGS = [
        'GROUP_c50f20c4-1541-103b-8884-2b6a146a3545' => '790e22a2-f5cb-4561-ac2e-e40f8adf73f4', // zmf -> do nothing
        'GROUP_7ea16e92-eadc-103a-81f6-9d9aea029be1' => '80151374-f53a-49f2-8516-0a6ae41d7a05', // Schule im Aufbruch
        'GROUP_cf7c3cf8-41c5-103b-88aa-2b6a146a3545' => '627d249d-61e4-4860-b739-bb3c8b21946e', // rpi virtuell
        'GROUP_513e4a78-4a56-103a-84e4-2b017690ecd2' => 'f036ab86-7089-4130-8d4e-c89d5bb169f0', // WLO-Redaktion
        'GROUP_ORG_Projektmanagement (GPM)' => 'c99b5081-0c3e-4ad4-b5ef-fcf8d8ef40f9', // GPM
        'GROUP_ORG_Seitenstark' => '06fd787f-7fe5-4e16-bff6-591a71b2631f',// Seitenstark
        'GROUP_ORG_Demo-Redaktion' =>'090a8fdd-e3ec-4b2a-9e51-35de533010d8',//demo Readaktion
        'GROUP_ORG_EbM - evidenzbasierte Medizin' => '272b5022-3d4b-46b1-8adf-a2dcdb82b2a3', //EbM - evidenzbasierte Medizin
        null => '3f8cb71e-f508-434c-99f9-098bd7164305', // others or unknown
    ];

    $ticket = '';
    if (function_exists('get_repo_ticket') && is_user_logged_in()){
        $ticket = get_repo_ticket();
    }
    if(!$ticket) {
        return true;
    }

    $username = '-me-';
    $apiUrl = 'rest/iam/v1/people/-home-/'.$username.'/preferences';
    $wloUserData = callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET', $ticket);
    $preferences = json_decode($wloUserData['preferences']);
    $user = wp_get_current_user();

    //if (empty($preferences->defaultInboxFolder) || $user->user_login == 'admin'){
    if (empty($preferences->defaultInboxFolder) || true ){
        $inboxId = $MAPPINGS[null];
        //error_log('default inbox: ' . $inboxId);
        try {
            $apiUrl = 'rest/iam/v1/people/-home-/-me-/memberships?maxItems=100&skipCount=0';
            $groups = callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET', $ticket)["groups"];
            foreach($MAPPINGS as $groupName => $folder) {
                if($groupName == null) {
                    continue;
                }
                foreach ($groups as $group) {
                    if($groupName == $group["authorityName"]) {
                        //error_log('user is in group ' . $group["profile"]["displayName"] . ' -> set home folder to ' . $folder);
                        if($folder == null) {
                            //error_log('No home folder should be set for this group, finishing');
                            return true;
                        }
                        $inboxId = $folder;
                        break;
                    }
                }
            }

        }catch(Exception $e) {
            error_log('could not fetch groups for user ' . $user->user_login . ', defaulting to unknown');
        }
        if(!$preferences) {
            $preferences = new stdClass();
        }
        $preferences->defaultInboxFolder = $inboxId;
        //error_log(json_encode($preferences));
        $apiUrl = 'rest/iam/v1/people/-home-/'.$username.'/preferences';
        if(callRepoApi($apiUrl, json_encode($preferences), 'Content-Type: application/json', 'PUT', $ticket)){
            //error_log('added inbox');
        }else{
            error_log('error: adding inbox');
            return false;
        }
    }

    return true;
}

function wlo_registerInbox() {
    //error_log('wlo_registerInbox 2.0');

    /*
    $user = wp_get_current_user();
    $user_roles = $user->roles;
    //error_log(print_r($user_roles, true));
    if ( in_array( 'uploadtahon_user', $user_roles, true ) ) {
        error_log('user has role uploadathon');
    }
    */
    setWLOInbox();

}
add_action( 'set_current_user', 'wlo_registerInbox', 10, 0 );
