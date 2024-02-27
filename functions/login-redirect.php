<?php

function wlo_login_redirect($user_login,$user) {
    if (isset($user->roles) && is_array($user->roles)) {
        //check for admins
        if (in_array('administrator', $user->roles)) {
            // redirect them to the default place
            return $user_login;
        } else {
            if (function_exists('get_repo_ticket')) {
                $ticket = get_repo_ticket();
            } else {
                $ticket = '';
            }
            wp_redirect(WLO_REPO . "/components/start?ticket=" . $ticket);
            die();
        }
    }
}

// redirect logged in users to the editorial desk
add_action('wp_login', 'wlo_login_redirect', 12, 2);

