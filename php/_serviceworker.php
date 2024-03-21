<?php
# Link requirements
require("_functions.php");
# Define sql-arguments
$sqlargs = array("localhost","root","","pagehost");
# Create connectionInstances
$conRet = validateDBConnection( connectDB($sqlargs) );
if (is_string($conRet)) { echo $conRet;$dbInstance=null; } else { $dbInstance = $conRet; }

$from = $_POST;

if (isset($_GET) && !empty($_GET)) {
    if (isset($_GET["get"])) {
        $from = $_GET;
    }
}

function joinKeyedArrayToString($array) {
    $result = '';
    foreach ($array as $key => $value) {
        $result .= "$key=$value;";
    }
    // Remove the last semicolon
    $result = rtrim($result, ';');
    return $result;
}

function formatToDisp($todisp,bool $maybenum) {
    if ($todisp == null) {
        return "NULL@";
    } elseif (is_array($todisp)) {
        return "ARRAY@" . joinKeyedArrayToString($todisp);
    } elseif (is_string($todisp)) {
        return "STRING@" . $todisp;
    } elseif (is_int($todisp)) {
        if ($maybenum == true) {
            return "INT@" . var_export($todisp, true);
        } else {
            if ($todisp == 0) {
                return "BOOL@FALSE";
            } elseif ($todisp == 1) {
                return "BOOL@TRUE";
            } else {
                return "INT@" . var_export($todisp, true);
            }
        }
    } elseif ($todisp == true) {
        return "BOOL@TRUE";
    } elseif ($todisp == false) {
        return "BOOL@FALSE";
    } else {
        return "UNKNOWN@" . var_export($todisp, true);
    }
}

if (isset($from) && !empty($from)) {
    $gets = $from;
    # Check for internal call
    if (isset($from["internal-call"]) && ($from["internal-call"] == "true" || $from["internal-call"] == true)) {

        $todisp = null;
        $maybenum = false;

        # Handle sending-mode get-accessees
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "get-accessees") {
            # ! CODE FOR GETTING ACCESSES HERE !
            $postID = $from["service_postid"];
            $todisp = getPostAccessees($dbInstance,"posts","accessees",$postID);
            $maybenum = true;
        }

        # Handle sending-mode add-accessee
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "add-accessee") {
            # ! CODE FOR ADDING ACCESSES HERE !
            $postID = $from["service_postid"];
            $clientID = $from["service_clientid"];
            $topdisp = addPostAccessee($dbInstance,"accessees",$postID,$clientID);
        }

        # Handle sending-mode rem-accessee
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "rem-accessee") {
            # ! CODE FOR REMOVING ACCESSES HERE !
            $postID = $from["service_postid"];
            $clientID = $from["service_clientid"];
            $topdisp = remPostAccessee($dbInstance,"accessees",$postID,$clientID);
        }

        # Handle sending-mode check-id-is-accessee
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "check-id-is-accessee") {
            # ! CODE FOR CHECKING IF A USERID IS ACCESSES HERE !
            $postID = $from["service_postid"];
            $clientID = $from["service_clientid"];
            $todisp = checkIfClientIsAccesseeByID($dbInstance,"accessees",$postID,$clientID);
        }

        # Handle sending-mode client-id-from-username
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "client-id-from-username") {
            # ! CODE FOR GETTING A clientID FROM USERNAME HERE !
            $username = $from["service_username"];
            $todisp = getclientIDFromName($dbInstance,"users",$username);
            $maybenum = true;
        }

        # Handle sending-mode client-id-from-dispname
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "client-id-from-dispname") {
            # ! CODE FOR GETTING A clientID FROM USERNAME HERE !
            $dispname = $from["service_dispname"];
            $todisp = getclientIDFromDispName($dbInstance,"users",$dispname);
            $maybenum = true;
        }

        # Handle sending-mode client-username-from-id
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "client-username-from-id") {
            # ! CODE FOR GETTING A USERNAME FROM clientID HERE !
            $clientID = $from["service_clientid"];
            $todisp = getClientNameFromID($dbInstance,"users",$clientID);
        }

        # Handle sending-mode add-comment
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "add-comment") {
            # ! CODE FOR ADDING A COMMENT HERE !
            $parentID = $from["service_parentid"];
            $authorID = $from["service_authorid"];
            $content = $from["service_content"];
            $isForPost = true;
            if (isset($from["service_isforpost"]) && !empty($from["service_isforpost"]) && ($from["service_isforpost"] == "false" || $from["service_isforpost"] == false)) {
                $isForPost = false;
            }
            $todisp = addComment($dbInstance,"comments",$parentID,$authorID,$content,$isForPost);
        }

        # Handle sending-mode set-comment
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "set-comment") {
            # ! CODE FOR SETTING A COMMENT HERE !
            $commentID = $from["service_commentid"];
            $content = null;
            $authorID = null;
            $parentID = null;
            $isForPost = null;
            if (isset($from["service_content"]) && !empty($from["service_content"])) {
                $content = $from["service_content"];
            }
            if (isset($from["service_authorid"]) && !empty($from["service_authorid"])) {
                $authorID = $from["service_authorid"];
            }
            if (isset($from["service_parentid"]) && !empty($from["service_parentid"])) {
                $parentID = $from["service_parentid"];
            }
            if (isset($from["service_isforpost"]) && !empty($from["service_isforpost"])) {
                if ($from["service_isforpost"] == "true" || $from["service_isforpost"] == true) {
                    $isForPost = true;
                } else {
                    $isForPost = false;
                }
            }
            $todisp = setComment($dbInstance,"comments",$commentID,$content,$authorID,$parentID,$isForPost);
        }

        # Handle sending-mode rem-comment
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "rem-comment") {
            # ! CODE FOR REMOVING A COMMENT HERE !
            $commentID = $from["service_commentid"];
            $todisp = remComment($dbInstance,"comments",$commentID);
        }

        echo formatToDisp($todisp,$maybenum);

    }

}