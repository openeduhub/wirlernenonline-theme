<?php

class Wlo_ldap{
    private $ds; // LDAP Handle
    function __construct(){
        $this->ds=ldap_connect(LDAP_SERVER);
        ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r=ldap_bind($this->ds,LDAP_USERNAME,LDAP_PASSWORD);     // das ist ein "anonymer" bind,
    }
    function toLdapObject($user){
        $obj["objectClass"][]="inetOrgPerson";
        $obj["objectClass"][]="person";
        $obj["objectClass"][]="top";
        $obj["uid"]=$user["login"];
        if (isset($user["firstName"]))
            $obj["cn"]=$user["firstName"];
        if (isset($user["email"]))
            $obj["mail"]=$user["email"];
        if (isset($user["lastName"]))
            $obj["givenName"]=$user["lastName"];
        if (isset($user["lastName"]))
            $obj["sn"]=$user["lastName"];
        if (isset($user["org"]))
            $obj["o"]=$user["org"];
        if(isset($user["group"]))
            $obj["ou"]=$user["group"];
        $obj["description"]="inform=".(@$user["inform"] ? "true" : "false");
        if($user["password"])
            $obj["userPassword"]=$this->ldapPassword($user["password"]);
        return $obj;
    }
    function editUser($user){
        //error_log(print_r($user, true));
        $obj=$this->toLdapObject($user);
        return ldap_mod_replace($this->ds,"uid=".ldap_escape($obj["uid"]).",".LDAP_MAIN_ORG,$obj);
    }
    function resetPassword($mail){
        $password=$this->generatePassword();
        $obj["userPassword"]=$this->ldapPassword($password);
        ldap_mod_replace($this->ds,"uid=".ldap_escape($mail).",".LDAP_MAIN_ORG,$obj);
        return $password;
    }

    function createUser($user){
        $obj=$this->toLdapObject($user);
        return ldap_add($this->ds,"uid=".ldap_escape($obj["uid"]).",".LDAP_MAIN_ORG,$obj);
    }
    function addToGroup($user,$group,$password){
        $groups=$this->searchGroups("(&(cn=".ldap_escape($group).")(businessCategory=".ldap_escape($password)."))");
        if($groups["count"]==1){
            $obj["uniqueMember"]=$user["dn"];
            return ldap_mod_add($this->ds,$groups[0]["dn"],$obj);
        }
        else {
            return false;
        }
    }
    function callApi($url,$method='GET',$body=null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, API_ENDPOINT.$url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization: Basic '.base64_encode(API_AUTH),
                'Accept: application/json')
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST,$method);
        $data=curl_exec($curl);
        curl_close($curl);
        return $data;
    }
    function createGroup($group){
        // may the main org groups does not yet exists, we simply try to create an ou
        $obj=[];
        $obj["objectClass"][]="organizationalUnit";
        $obj["objectClass"][]="top";
        $obj["ou"]=substr(LDAP_MAIN_ORG_GROUPS,3,strpos(LDAP_MAIN_ORG_GROUPS,",")-3);
        // we can ignore errors
        @ldap_add($this->ds,"ou=".$obj["ou"].",".explode(",",LDAP_MAIN_ORG_GROUPS)[1],$obj);

        $obj=[];
        $obj["objectClass"][]="groupOfUniqueNames";
        $obj["objectClass"][]="top";
        $obj["cn"]=$group;
        $obj["organizationName"]="GROUP_ORG_".$group;
        $obj["businessCategory"]=$this->generatePassword();
        $obj["uniqueMember"]=null;

        $result=ldap_add($this->ds,"cn=".ldap_escape($group).",".LDAP_MAIN_ORG_GROUPS,$obj);
        if($result){
            return json_decode($this->callApi("organization/v1/organizations/-home-/".rawurlencode($group),'PUT'));
        }
        return $result;
    }
    function getUser($email){
        $sr=ldap_search($this->ds,LDAP_MAIN_ORG, "(uid=".ldap_escape($email).")");
        $data=ldap_get_entries($this->ds,$sr)[0];
        $details=explode("=",$data["description"][0]);
        for($i=0;$i<count($details);$i+=2){
            $data[$details[$i]]=$details[$i+1];
        }
        return $data;
    }
    function searchGroups($query="cn=*"){
        $sr=ldap_search($this->ds,LDAP_MAIN_ORG_GROUPS, $query);
        $data=ldap_get_entries($this->ds,$sr);
        return $data;
    }
    function searchUsers($query="cn=*"){
        $sr=ldap_search($this->ds,LDAP_MAIN_ORG, $query);
        $data=ldap_get_entries($this->ds,$sr);
        return $data;
    }
    function validateLogin($email,$password){
        //error_log('validating_login... ('.$email.')');
        $user=$this->getUser($email);
        if($user==0){
            error_log('validateLogin user not found! ');
            return false;
        }
        $pass=$user["userpassword"][0];
        if($pass!=$this->ldapPassword($password)){
            error_log('validateLogin wrong password! ');
            return false;
        }
        error_log('validating_login successful!');
        return $user;
    }

    function userExists($mail){
        $sr=ldap_search($this->ds,LDAP_MAIN_ORG, "(uid=".ldap_escape($mail).")");
        if(ldap_count_entries($this->ds,$sr)>0)
            return true;

        return false;
    }

    private function generatePassword(){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < DEFAULT_PASSWORD_LENGTH; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    private function ldapPassword($password){
        return '{MD5}'.base64_encode(md5($password,true));
    }
}
?>
