<?php
# Link requirements
require("_functions.php");
# Define sql-arguments
$sqlargs = array("localhost","root","","pagehost");
# Create connectionInstances
$conRet = validateDBConnection( connectDB($sqlargs) );
if (is_string($conRet)) { echo $conRet;$dbInstance=null; } else { $dbInstance = $conRet; }

$from = $_POST;

if (isset($from) && !empty($from)) {
    $gets = $from;
    # Check for internal call
    if (isset($from["internal-call"]) && $from["internal-call"] == "true") {

        # Handle sending-mode create-new
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "get-accessees") {
            # ! CODE FOR GETTING ACCESSES HERE !
        }

        # Handle sending-mode create-new
        if (isset($from["sendingmode"]) && $from["sendingmode"] == "add-accessee") {
            # ! CODE FOR GETTING ACCESSES HERE !
        }

    }

}