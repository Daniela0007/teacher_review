<?php

// function authentificate($user, $password) {

//     // function print_rr($arr){
//     //     echo "<pre>";
//     //     print_r($arr);
//     //     echo "</pre>";
//     // }

//     //session_start(); 
//     $errorMessage = '';

//     $ldapconfig['host'] = '10.100.40.15';    
//     $ldapconfig['port'] = '389';    
//     $ldapconfig['basedn'] = 'dc=info,dc=uaic,dc=ro';

//     $userPass = $password;
//     $userID = $user;
//     //$userID = "cosmin.varlan";
//     //$userPass = $_POST['old-password'];
//     //$newPassword = $_POST['new-password'];


//     $conexiune_ldap=ldap_connect($ldapconfig['host'], $ldapconfig['port'])
//             or die("Adresa de conexiune la LDAP nu este corecta.");

//     ldap_set_option($conexiune_ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
//     ldap_set_option($conexiune_ldap, LDAP_OPT_REFERRALS, 0);

    
//     $dn = 'cn=search-only,ou=ldap-services,dc=info,dc=uaic,dc=ro';
//     $bind=ldap_bind($conexiune_ldap, $dn, "NkWQxkQ5BKItdsP");

//     $filter = "(&(objectClass=inetOrgPerson)(uid=$userID))";    
//     $result = ldap_search($conexiune_ldap, "dc=info,dc=uaic,dc=ro", $filter);
//     $entries = ldap_get_entries($conexiune_ldap, $result);

//     //print_rr($entries);

    
//     if ($entries["count"] > 0) {
//         $userDn = $entries[0]["dn"]; 
//         echo 'This is the user dn' . $userDn;

//         $bind=ldap_bind($conexiune_ldap, $userDn, $userPass)
//             or die('autentificare esuata');
//         echo "<br>autentificat !";
//     }

// }

// authentificate("daniela.rusu", "Td7@a&wWsu>jN^");
header('Location: public/');
exit; 