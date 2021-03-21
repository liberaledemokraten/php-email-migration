<?php
// NOTE: All keys and values MUST be in lower case here.

/**
* The target IMAP server
*/
global $TARGETHOST;
$TARGETHOST = "mail.example.org";

/**
 * An array of trusted domains.
 * Requests with any domains not in this array shall be denied
 */
$TRUSTEDDOMAINS = [
    "example.org",
    "example.com"
];
global $TRUSTEDDOMAINS;

/**
 * An array to find a mailserver by domain or provider name
 */
global $HOSTS;
$HOSTS = array(
    'example.org' => 'mail.example.org',
    'example.com' => 'imap.example.com',
    'examplemail' => 'imap.example.net',
);


/**
 * An array defining if the username equals to the email address
 * or if the username is the user-part in user@domain.tld
 * 
 * true, if the username is the same as the email address
 * false, if the username is the user-part in 'user@domain.tld'
 */
global $USERISEMAIL;
$USERISEMAIL = array(
    'example.org' => true,
	'example.com' => true
);


/**
 * An array defining blocked values.
 * This list is utilized to ensure that no unwanted
 * commands are smuggled in as a user-provided value.
 *
 * Regardless of this list, any arguments to be passed to
 * the system MUST be used with 'escapeshellcmd($args)'.
 */
global $BLOCKEDVALUES;
$BLOCKEDVALUES = array(
    '\s',
    '\n',
	' ',
    'sudo ',
    'su ',
    'rm ',
    '|'
);
?>