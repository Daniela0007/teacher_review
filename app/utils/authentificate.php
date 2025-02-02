<?php

function authentificate($user, $password) {
    $ldapconfig['host'] = '10.100.40.15';    
    $ldapconfig['port'] = '389';    
    $ldapconfig['basedn'] = 'dc=info,dc=uaic,dc=ro';

    $conexiune_ldap = ldap_connect($ldapconfig['host'], $ldapconfig['port'])
        or die("Adresa de conexiune la LDAP nu este corecta.");

    ldap_set_option($conexiune_ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($conexiune_ldap, LDAP_OPT_REFERRALS, 0);

    $dn = 'cn=search-only,ou=ldap-services,dc=info,dc=uaic,dc=ro';
    $bind = ldap_bind($conexiune_ldap, $dn, "NkWQxkQ5BKItdsP");

    $filter = "(&(objectClass=inetOrgPerson)(uid=$user))";    
    $result = ldap_search($conexiune_ldap, $ldapconfig['basedn'], $filter);
    $entries = ldap_get_entries($conexiune_ldap, $result);

    if ($entries["count"] > 0) {
      $userDn = $entries[0]["dn"];

      if (strpos($userDn, 'ou=students') !== false) {
          $role = 'student';
      } elseif (strpos($userDn, 'ou=professors') !== false) {
          $role = 'professor';
      } else {
          return json_encode([
              'autentificat' => false,
              'rol' => null,
              'error' => 'Nu este student sau profesor.'
          ]);
      }

      $bind = @ldap_bind($conexiune_ldap, $userDn, $password);

      if ($bind) {
          return json_encode([
              'autentificat' => true,
              'rol' => $role
          ]);
      } else {
          return json_encode([
              'autentificat' => false,
              'rol' => null,
              'error' => 'Autentificare esuata.'
          ]);
      }
  } else {
      return json_encode([
          'autentificat' => false,
          'rol' => null,
          'error' => 'Utilizator inexistent.'
      ]);
  }
}