<?php

function setWLOInbox(){
    // will be read in the order as defined
    // the first matching group the person is member of will map to the folder!
    $MAPPINGS = [
        'GROUP_c50f20c4-1541-103b-8884-2b6a146a3545' => null, // zmf -> do nothing
        'GROUP_7ea16e92-eadc-103a-81f6-9d9aea029be1' => 'TODO: ffec681b-0552-4ef2-b117-f9b0859b8460', // Schule im Aufbruch
        'GROUP_cf7c3cf8-41c5-103b-88aa-2b6a146a3545' => 'TODO: ffec681b-0552-4ef2-b117-f9b0859b8460', // rpi virtuell
        'GROUP_513e4a78-4a56-103a-84e4-2b017690ecd2' => 'TODO: baabf19f-e350-4a89-ada2-80ac0e135753', // WLO-Redaktion
        null => 'TODO: bf0f0b6c-30da-4610-aab0-a6f4fb29c492', // others or unknown
    ];

    $ticket = '';
    if (function_exists('get_repo_ticket')){
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
    if (empty($preferences->defaultInboxFolder) || $user->user_login == 'admin'){
        $inboxId = $MAPPINGS[null];
        error_log('default inbox: ' . $inboxId);
        try {
            $apiUrl = 'rest/iam/v1/people/-home-/-me-/memberships?maxItems=100&skipCount=0';
            foreach($MAPPINGS as $groupName => $folder) {
                if($groupName == null) {
                    continue;
                }
                foreach (callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET', $ticket)["groups"] as $group) {
                    if($groupName == $group["authorityName"]) {
                        error_log('user is in group ' . $group["profile"]["displayName"] . ' -> set home folder to ' . $folder);
                        if($folder == null) {
                            error_log('No home folder should be set for this group, finishing');
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
        error_log(json_encode($preferences));
        $apiUrl = 'rest/iam/v1/people/-home-/'.$username.'/preferences';
        if(callRepoApi($apiUrl, json_encode($preferences), 'Content-Type: application/json', 'PUT', $ticket)){
            error_log('added inbox');
        }else{
            error_log('error: adding inbox');
            return false;
        }
    }

    return true;
}

function wlo_registerInbox() {
    error_log('wlo_registerInbox 2.0');

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