<?php

function wlo_login_redirect($user_login,$user) {
    if (isset($user->roles) && is_array($user->roles)) {
        //check for admins
        if (in_array('administrator', $user->roles)) {
            // redirect them to the default place
            return $user_login;
        } else {
            if (function_exists('get_repo_ticket')) {
                $ticket = get_repo_ticket($user);
            } else {
                $ticket = '';
            }
            // the first matching group the person is member of will map to the URL!
            $MAPPINGS = [
//                'GROUP_7ea16e92-eadc-103a-81f6-9d9aea029be1' => '80151374-f53a-49f2-8516-0a6ae41d7a05', // Schule im Aufbruch
//                'GROUP_cf7c3cf8-41c5-103b-88aa-2b6a146a3545' => '627d249d-61e4-4860-b739-bb3c8b21946e', // rpi virtuell
//                'GROUP_513e4a78-4a56-103a-84e4-2b017690ecd2' => 'f036ab86-7089-4130-8d4e-c89d5bb169f0', // WLO-Redaktion
                'GROUP_ORG_Projektmanagement (GPM)' => 'https://redaktion.projektmanagement.wirlernenonline.de/edu-sharing/', // GPM
                null => WLO_REPO, // others or unknown
            ];
            $user = wp_get_current_user();
            $redirectURL = $MAPPINGS[null];

            try {
                $apiUrl = 'rest/iam/v1/people/-home-/-me-/memberships?maxItems=100&skipCount=0';
                foreach($MAPPINGS as $groupName => $repoContext) {
                    if($groupName == null) {
                        continue;
                    }
                    foreach (callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET', $ticket)["groups"] as $group) {
                        if($groupName == $group["authorityName"]) {
                            $redirectURL = $repoContext;
                            //error_log('user is in group ' . $group["profile"]["displayName"] . ' -> set home folder to ' . $folder);
                            break 2;
                        }
                    }
                }

            } catch(Exception $e) {
                error_log('could not fetch groups for user ' . $user->user_login . ', defaulting to unknown');
            }
            $redirectURL = $redirectURL . "components/start?ticket=" . $ticket;

            wp_redirect($redirectURL);
            die();
        }
    }
}

// redirect logged in users to the editorial desk
add_action('wp_login', 'wlo_login_redirect', 12, 2);

