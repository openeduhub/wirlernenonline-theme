<?php
require_once(get_template_directory().'/functions/ldap.php');


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

        if ($ldap->validateLogin($user["login"], $user["password"]) == false ){;
            if ($ldap->userExists($user["login"])){
                ('ldap: editUser');
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
