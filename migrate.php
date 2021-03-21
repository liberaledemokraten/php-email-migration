<?php

/**
 * Sets:
 * Content-type: text/plain; charset=utf-8
 * Content-language: en, Locale: en_US.UTF-8
 * ignore_user_abort(true)
 */
include 'headers.php';

/**
 * Defines:
 * Array: TRUSTEDDOMAINS, HOSTS, USERISEMAIL, BLOCKEDVALUES, TARGETHOST
 */
include 'define_vars.php';

/**
 * Defines:
 * function: allRequiredArgsSet(), getLowerCaseArray($email1, $email2, $password1, $password2),
 *           isHostValid($host, $hosts_array), hasNoBlockedValues($args_array, $blockedvalues),
 *           getDomainName($email), isInTrustedDomain($email, $trusteddomains), getActionID(),
 *           hostOf($email, $hosts_array, $host = false), getUsername($email, $userpart_array),
 *           createImapsyncArgs($email1, $email2, $password1, $password2, $hosts_array,
 *                              $userpart_array, $host1 = false, $host2 = false)
 */
include 'preprocessing_functions.php';

/**
 * Defines:
 * function: execImapsync($args, $actionid), addLastLine($logfile), displayStatus($actionid),
 *           getActionAndDie(), setReturnHeader($statuscode)
 */
include 'execution_functions.php';

/**
 * Defines:
 * function: trimUnnecessaryInfo($file), getErrorCode($ret), errorMessageOf($errorcode)
 */
include 'imapsync_result_processing_functions.php';


/**
 * Turn on Maintenance Mode
 */
// include 'maintenance.php';


// starting processing
getActionAndDie(); // if 'actionid' is set, return the status of the action and die
$actionid = getActionID(); // define actionid if the action doesn't exist yet
$logfile = "log/".$actionid.".log";
global $logfile;

/**
 * Checks if all required arguments are set with the HTTP request and executes the 'imapsync' process afterwards.
 * Writes the message to output in '$message'.
 */
if (allRequiredArgsSet()) {
$email1 = $_REQUEST['email1'];
$email2 = $_REQUEST['email2'];
$passwd1 = $_REQUEST['password1'];
$passwd2 = $_REQUEST['password2'];
$host1 = ( isHostValid($_REQUEST['host1'], $HOSTS) )? $_REQUEST['host1'] : false;

unset($_REQUEST['password1']);
unset($_REQUEST['password2']);
	
    if (isInTrustedDomain($email1, $TRUSTEDDOMAINS) && isInTrustedDomain($email2, $TRUSTEDDOMAINS)) {
        $args = getLowerCaseArray($email1, $email2, $passwd1, $passwd2);
        if(hasNoBlockedValues($args, $BLOCKEDVALUES)){
            $args = createImapsyncArgs($args["email1"], $args["email2"], $passwd1, $passwd2, $USERISEMAIL, $HOSTS, $host1, $TARGETHOST);
            system("echo ' ' > ".$logfile);
			setReturnHeader(202); // HTTP 202 Accept : request accepted for processing
			$curPageURL = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$actionUrl = $curPageURL."?actionid=".$actionid;
			header("url: ".$actionUrl);
            header("Refresh: 5; url=".$actionUrl);
            echo "Starting process. You may find updates under:\n\n".$actionUrl."\n\n";
            execImapsync($args, $actionid);
        } else {
            // blocked values were entered
			setReturnHeader(400);
            echo "Forbidden characters have been encountered. The request won't be processed any further.";
			die();
        }
    } else {
        // Domain not trusted
		setReturnHeader(400);
        echo "The input is not valid. Please check the email addresses entered.";
		die();
    }
} else {
// not all required args are set
setReturnHeader(400);
echo "Incomplete input. Please send all necessary data with your request.";
die();
}
?>