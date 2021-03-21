<?php

/**
 * Generate and return the Action ID
 */
function getActionID() {
    return "".date('YmdHisv').rand(100,999);
}

/**
 * Checks if all required arguments are set with the HTTP request.
 * @returns true, if all required arguments are set
 *          false, otherwise
 */
function allRequiredArgsSet() {
    if (isset($_REQUEST['email1']) && isset($_REQUEST['email2']) && isset($_REQUEST['password1']) && isset($_REQUEST['password2']) && isset($_REQUEST['host1'])) {
        return true;
    }
    return false;
}

/**
 * Checks if the enteres domain is a valid host as defined in the 'hosts_array'.
 * @param host: The host/domain to be checked
 * @param hosts_array: String Array of hosts/domains that are valid
 * @returns true, if the host/domain is valid
 *          false, otherwise
 */
function isHostValid($host, $hosts_array){
    if ($host == false)
      return true;
    foreach(array_keys($hosts_array) as $validhost) {
        if (strcmp($host, $validhost) == 0)
          return true;
    }
    return false;
}

/**
 * Turns email1, email2, password1 and password2 into lowercase and returns as an array.
 * This is required as below functions will compare those to some pre-defined lowercase values.
 */
function getLowerCaseArray($email1, $email2, $password1, $password2){
    return array(
        'email1' => strtolower($email1),
        'email2' => strtolower($email2),
        'passwd1' => strtolower($password1),
        'passwd2' => strtolower($password2)
    );
}

/**
 * Checks if the arguments have blocked values.
 * @param args_array: String Array with arguments to be checked
 * @param blockedvalues: Array of String values to be blocked // TODO Remove
 * @returns true, if args has no blocked values
 *          false, otherwise
 */
function hasNoBlockedValues($args_array, $blockedvalues){
    foreach($args_array as $arg){
        foreach($blockedvalues as $blockedvalue){
            if(strpos($arg, $blockedvalue) != false){
                unset($arg);
                unset($blockedvalue);
                return false;
            }
        }
    }
    unset($arg);
    unset($blockedvalue);
    return true;
}

/**
 * @param email: The email address its domain name is to be retrieved
 * @returns the domain name in 'user@domain.tld'
 */
function getDomainName($email){
    $atPosition = stripos($email, "@", 0);
    return strtolower(substr($email, $atPosition+1));
}

/**
 * @param email: the email address to be checked
 * @param trusteddomains: String Array of trusted domains
 * @returns true, if email domain is in trusteddomains
 *          false, otherwise
 */
function isInTrustedDomain($email, $trusteddomains){
    for($i = 0; $i < count($trusteddomains); $i++){
        if(strcasecmp(getDomainName($email), $trusteddomains[$i]) == 0) {
            return true;
        }
    }
    return false;
}

/**
 * @param email: The email address its IMAP host is to be retrieved
 * @param hosts_array: String array containing IMAP hosts for domains and providers // TODO Remove
 * @param host: false, if the pre-defined remote host for the domain shall be used
 *                   else, the provider's name in lowercase String, if a predefined provider shall be used
 *                   default: false
 * @returns the IMAP host for the email address
 */
function hostOf($email, $hosts_array, $host = false){
    if($host != false) {
        return $hosts_array[$host];
    }
    return $hosts_array[getDomainName($email)];
}

/**
 * @param email: the email adddress of the user-part
 * @param userpart_array: An array telling if the domain requires the full email address
 *                          as the username or just the user-part in 'user@domain.tld' // TODO Remove
 * @returns the username to authenticate to the IMAP server with
 */
function getUsername($email, $userpart_array){
    if($userpart_array[getDomainName($email)]){
        return $email;
    }
    $atPosition = stripos($email, "@", 0);
    $email = substr($email, 0, $atPosition);
}

/**
 * Creates the arguments to be passed to 'imapsync'.
 *
 * Caution: Do NOT pass unchecked args. Always use 'isInTrustedDomain($email)' and 'hasNoBlockedValues($args_array)' first.
 *          Do NOT pass the returned arguments to the system without 'escapeshellcmd($args)'
 *
 * Assumes: the username to authenticate with the IMAP server is either the email address or the user-part in 'user@domain.tld'
 *
 * @param email1, email2: The source email address and the target email address
 * @param password1, password2: The source IMAP accounts password and the target IMAP accounts password
 * @param hosts_array: String array containing IMAP hosts for domains and providers // TODO remove
 * @param userpart_array:  An array telling if the domain requires the full email address // TODO Remove
 *                          as the username or just the user-part in 'user@domain.tld'
 * @param host1, host2: false, if the pre-defined remote host for the domain shall be used
 *                      else, the provider's name in lowercase String, if a predefined provider shall be used
 *                      default: false
 *
 * @returns the arguments to be passed to 'imapsync'
 */
function createImapsyncArgs($email1, $email2, $password1, $password2, $userpart_array, $hosts_array, $host1 = false, $host2 = false){
    $user1 = getUsername($email1, $userpart_array);
    $user2 = getUsername($email2, $userpart_array);
    $host1 = hostOf($email1, $hosts_array, $host1);
    $host2 = ($host2 == false)? hostOf($email1, $hosts_array, $host2) : $host2;
    return "--host1 ".$host1." --user1 ".$user1." --password1 ".$password1." --host2 ".$host2." --user2 ".$user2." --password2 ".$password2." --no-modulesversion --noid";
}
?>