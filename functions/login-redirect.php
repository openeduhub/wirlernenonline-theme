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
                'GROUP_ORG_Projektmanagement (GPM)' => 'https://redaktion.projektmanagement.wirlernenonline.de/edu-sharing/', // GPM
                'GROUP_ORG_Demo-Redaktion'=> 'https://redaktion.demo-redaktion.wirlernenonline.de/edu-sharing/',
                'GROUP_ORG_EbM - evidenzbasierte Medizin' => "https://ebm.wirlernenonline.de/edu-sharing/",
                null => WLO_REPO, // others or unknown
            ];
            $user = wp_get_current_user();
            $redirectURL = $MAPPINGS[null];

            try {
                $apiUrl = 'rest/iam/v1/people/-home-/-me-/memberships?maxItems=100&skipCount=0';
                $groups = callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET', $ticket)["groups"];
                foreach($MAPPINGS as $groupName => $repoContext) {
                    if($groupName == null) {
                        continue;
                    }
                    foreach ($groups as $group) {
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

