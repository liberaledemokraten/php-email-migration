<?php
/**
 * This function calls the imapsync executable with the delivered args
 * @returns either the output or an error message
 */
function execImapsync($args, $actionid) {
    $logfile = "log/".$actionid.".log";
	system('imapsync '.escapeshellcmd($args)." 1>".$logfile, $ret);
    trimUnnecessaryInfo($logfile);
	return;
}

/**
 * If an actionid is delivered, don't start a new process but check the running process instead
 */
function getActionAndDie(){
    if(isset($_REQUEST['actionid']))
      displayStatusAndDie(5, $_REQUEST['actionid']);
    return;
}

/**
 * Displays the current status of a running action / process and returning a refresh.
 * Kills the entire PHP script after displaying the status if the mgration has finished with or without an error.
 *
 * @param time: time in seconds to kill the script after
 * @param actionid: The id of the action that shall be reloaded on
 */
function displayStatusAndDie($time, $actionid){
    $logfile = "log/".$actionid.".log";
    if (file_exists($logfile) == false) {
		setReturnHeader(404);
        echo "There is no process with Action ID ".$actionid;
        die();
    }
	
	$contents = file_get_contents($logfile);
    if (strpos($contents, "Exiting with return value ") == false) {
		sleep($time);
		setReturnHeader(102); // HTTP 102 PROCESSING (commonly used by WebDAV)
		header("Refresh: 5; url=migrate.php?actionid=".$actionid); // reload page
        echo "The process is still running. Please wait.\n\nAction ID: ".$actionid."\n\nStatus:\n".$contents;
		die();
    }
    
	$errorcodePosition = strpos($contents, "Exiting with return value");
	$errorcode = str_replace(" ", "", substr($contents, $errorcodePosition+26, 2));
	setReturnHeader(intval($errorcode));
echo "The migration with Action ID ".$actionid." is finished.\n\n ".errorMessageOf($errorcode)."\n\nContact the system admin if necessary. Provide them the following data:\n\n"."Action ID: ".$actionid."\nStatus: ".errorMessageOf($errorcode)."\n".$contents;
    die();
}

/**
* Set the HTTP status code
*/
function setReturnHeader($statuscode){
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	switch ($statuscode){
	case 200:
		;
	case 0:
		header($protocol." 200 OK");
		return;
	case 202:
	    header($protocol." 202 ACCEPTED");
		return;
	case 400:
	    header($protocol." 400 BAD REQUEST");
		return;
	case 401:
	    header($protocol." 401 UNAUTHORIZED");
		return;
	case 403:
	    header($protocol." 403 FORBIDDEN");
		return;
	case 404:
		header($protocol." 404 NOT FOUND");
		return;
	case 503:
	    header($protocol." 503 SERVICE UNAVAILABLE");
		return;
	case 102:
		header($protocol." 102 PROCESSING");
		return;
	case 16:
	    header($protocol." 401 ".errorMessageOf($statuscode));
		return;
	case 10:
	    header($protocol." 400 ".errorMessageOf($statuscode)."(host/provider may not exist)");
		return;
	default:
	    header($protocol." 500 ".errorMessageOf($statuscode));
		return;
	}
}


function getLastLine($filename){
    $line = '';
    $f = fopen($filename, 'r');
    $cursor = -1;
    fseek($f, $cursor, SEEK_END);
    $char = fgetc($f);
    //Trim trailing newline characters in the file
    while ($char === "\n" || $char === "\r") {
       fseek($f, $cursor--, SEEK_END);
       $char = fgetc($f);
    }
    //Read until the next line of the file begins or the first newline char
    while ($char !== false && $char !== "\n" && $char !== "\r") {
       //Prepend the new character
       $line = $char . $line;
       fseek($f, $cursor--, SEEK_END);
       $char = fgetc($f);
    }
    return $line;
}
?>