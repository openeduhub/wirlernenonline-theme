<?php
require_once(get_template_directory().'/functions/ldap.php');


add_action('um_submit_form_errors_hook_','um_custom_validate_username', 999, 1);
function um_custom_validate_username( $args ) {

    $ldapData = json_decode(file_get_contents('https://login.oer-contentbuffet.info/api.php?id='.$args['user_login']));

    if ( isset( $args['user_login'] ) && $ldapData->exists ) {
        UM()->form()->add_error( 'user_login', 'Dieser Benutzer existiert leider schon (ldap).' );
    }
}

add_action( 'um_custom_field_validation_wlo_email', 'um_custom_validate_wlo_email', 30, 3 );
function um_custom_validate_wlo_email( $key, $array, $args ) {

    $mailData = json_decode(file_get_contents('http://appserver8.metaventis.com/mailapi/api.php?name='.$args[$key].'&action=search'));

    if (isset( $args[$key] ) && ($mailData->check == 'false') ){
        UM()->form()->add_error( $key, __( 'Diese E-Mail ist leider schon vergeben. '.$mailData->name.' ist noch frei!', 'ultimate-member' ) );
    }

}

add_action( 'um_after_email_confirmation', 'wlo_after_email_confirmation', 10, 1 );
function wlo_after_email_confirmation( $user_id = null ) {
    error_log('wlo_after_email_confirmation');

    if (createWloMail($user_id) ) {
        error_log('WLO-Mail created');
    }

    if (wloInbox() ) {
        error_log('Inbox created');
    }
}

function createWloMail($user_id){
    um_fetch_user( $user_id );
    if ( !empty(um_user('user_mail_15')) ){
        $mailData = json_decode(file_get_contents('http://appserver8.metaventis.com/mailapi/api.php?wlomail='.um_user('user_mail_15').'&action=create&maildest='.um_user('user_email')));
        if ($mailData->created == 'true'){
            return true;
        }
        error_log($mailData->message);
    }
    return false;
}

function wloInbox(){
    $ticket = '';
    if (function_exists('get_repo_ticket')){
        $ticket = get_repo_ticket();
    }

    $username = '-me-';
    $apiUrl = 'rest/iam/v1/people/-home-/'.$username.'/preferences';
    $wloUserData = callRepoApi($apiUrl, null, 'Content-Type: application/json', 'GET', $ticket);
    $preferences = json_decode($wloUserData['preferences']);
    $inboxName = '4aa774e8-ba1b-43c2-9f60-780ef2882758';

    if (empty($preferences->defaultInboxFolder) || $preferences->defaultInboxFolder !== $inboxName){
        error_log('defaultInboxFolder: empty');

        $body = '{"defaultInboxFolder":"'.$inboxName.'"}';
        if(callRepoApi($apiUrl, $body, 'Content-Type: application/json', 'PUT', $ticket)){
            error_log('added inbox');
        }else{
            error_log('error: adding inbox');
            return false;
        }
    }

    return true;
}


add_action( 'user_register', 'wlo_register_ldap', 10, 1 );
function wlo_register_ldap( $user_id ) {
    error_log('wlo_register_ldap');
    $formId = $_POST['form_id'];

    $user = array();
    $user["email"] = $_POST['user_email-'.$formId];
    $user["login"] = $_POST['user_login-'.$formId];
    $user["firstName"] = $_POST['first_name-'.$formId];
    $user["lastName"] = $_POST['last_name-'.$formId];
    $user["password"] = $_POST['user_password-'.$formId];

    $ldap = new Wlo_ldap();
    $ldap->createUser($user);
}

add_filter('authenticate', 'wlo_login', 10, 3);
function wlo_login($user1, $username, $password, $already_md5 = false){
    error_log('wlo_login (authenticate)');
    $formId = $_POST['form_id'];

    if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $wp_user = get_user_by('email', $username);
    }else{
        $wp_user = get_user_by('login', $username);
    }

    if (!empty($_POST['pwd'])){
        $userPassword = $_POST['pwd'];
    }else{
        $userPassword = $_POST['user_password-'.$formId];
    }

    //error_log(print_r($_POST, true));

    $user = array();
    $user["login"] = $wp_user->user_login;
    $user["password"] = $userPassword;
    $user["firstName"] = $wp_user->first_name;
    $user["lastName"] = $wp_user->last_name;
    $user["email"] =  $wp_user->user_email;

    if ($wp_user && wp_check_password( $user["password"], $wp_user->user_pass, $wp_user->ID )){
        //error_log('wp_check_password PASSED ('.$user["login"].')');
        $ldap = new Wlo_ldap();

        if ($ldap->validateLogin($user["login"], $user["password"]) == false ){

            if ($ldap->userExists($user["login"])){
                error_log('ldap: editUser');
                $ldap->editUser($user);
            }else{
                error_log('ldap: createUser');
                $ldap->createUser($user);
            }
        }
    }else{
        error_log('wp_check_password FAILED');
        //error_log('attempted password: '.$user["password"]);
    }


    return $user;
}

function wlo_registerInbox() {
    error_log('wlo_registerInbox');

    $user = wp_get_current_user();
    $user_roles = $user->roles;
    //error_log(print_r($user_roles, true));
    if ( in_array( 'uploadtahon_user', $user_roles, true ) ) {
        error_log('user has role uploadathon');
        wloInbox();
    }

}
add_action( 'set_current_user', 'wlo_registerInbox', 10, 2 );

add_action( 'edit_user_profile_update', 'wlo_update_user', 10, 1 );
function wlo_update_user( $user_id ) {
    error_log('wlo_update_user');
    //$formId = $_POST['form_id'];
    $user_info = get_userdata( $_POST['user_id']);

    $user = array();
    $user["email"] = $_POST['email'];
    $user["login"] = $user_info->user_login;
    $user["firstName"] = $_POST['first_name'];
    $user["lastName"] = $_POST['last_name'];

    //error_log('wlo_update_user');
    //error_log(print_r($user, true));

    $ldap = new Wlo_ldap();
    $ldap->editUser($user);
}


add_action( 'password_reset', 'wlo_password_reset', 10, 2 );
function wlo_password_reset( $user, $new_pass ) {

    //error_log('################### WP-RESET_PW');
    //error_log('$new_pass: '.$new_pass);
    //error_log('login: '.$user->user_login);
    //error_log('first_name: '.$user->first_name);

    $wlo_user = array();
    $wlo_user["login"] = $user->user_login;
    $wlo_user["password"] = $new_pass;

    $ldap = new Wlo_ldap();
    $ldap->editUser($wlo_user);
}
