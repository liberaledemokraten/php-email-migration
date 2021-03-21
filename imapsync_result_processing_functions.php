<?php

/**
 * imapsync adds details about the system, the user and the application itself.
 * Those don't need to be showed on the web, so we can just trim those lines.
 */
function trimUnnecessaryInfo($file){
    system("sed -i '/^Here is imapsync/d' ".$file);
    system("sed -i '/^with Perl/d' ".$file);
    system("sed -i '/^PID is/d' ".$file);
    system("sed -i '/^Load is/d' ".$file);
    system("sed -i '/^Current directory is/d' ".$file);
    system("sed -i '/^Real user id is/d' ".$file);
    system("sed -i '/^Effective user id is/d' ".$file);
    system("sed -i '/^\$RCSfile: imapsync/d' ".$file);
    system("sed -i '/^Command line used, run by/d' ".$file);
    system("sed -i '/^Temp directory is/d' ".$file);
    system("sed -i '/^kill /d' ".$file);
    system("sed -i '/^File /d' ".$file);
    system("sed -i '/^PID file is /d' ".$file);
    system("sed -i '/^Writing my PID/d' ".$file);
    system("sed -i '/^Writing also my logfile name in/d' ".$file);
    system("sed -i '/^Homepage: /d' ".$file);
    system("sed -i '/^Check if a new imapsync release/d' ".$file);
}

/**
 * @returns the exit status code of imapsync
 */
function getErrorCode($ret){
    $index = strrpos($ret, " ");
    return substr($ret, $index);
}

/**
 * @returns a string for common imapsync exit status codes
 */
function errorMessageOf($errorcode){
    switch($errorcode){
        case 0:
          return "OK (no errors)";
          break;
        case 64:
          return "command line usage error: please check the CLI commands";
          break;
        case 66:
          return "no input: cannot open input";
          break;
        case 69:
          return "service unavailable";
          break;
        case 70:
          return "internal software error";
          break;
        case 6:
          return "EXIT_BY_SIGNAL: hould be 128+n where n is the sig_num";
          break;
        case 8:
          return "EXIT_PID_FILE_ERROR";
          break;
        case 10:
          return "EXIT_CONNECTION_FAILURE";
          break;
        case 12:
          return "EXIT_TLS_FAILURE";
          break;
        case 16:
          return "EXIT_AUTHENTICATION_FAILURE";
          break;
        case 21:
          return "EXIT_SUBFOLDER1_NO_EXISTS";
          break;
        default:
          return "unknown: an unknown error has occured";
          break;
    }
    return "unknown error"; // this line should not be necessary
}
?>