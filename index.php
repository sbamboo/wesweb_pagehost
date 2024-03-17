<?php
# Link requirements
require("php/_functions.php");
# Define sql-arguments
$sqlargs_users = array("localhost","root","","pagehost","users");
$sqlargs_posts = array("localhost","root","","pagehost","posts");
$sqlargs_cmnts = array("localhost","root","","pagehost","comments");
$sqlargs_acces = array("localhost","root","","pagehost","accessees");
# Create connectionInstances
#list($conSuccess_users,$conMsg_users,$dbInstance_users) = connectDB($sqlargs_users);
#list($conSuccess_posts,$conMsg_posts,$dbInstance_posts) = connectDB($sqlargs_posts);
#list($conSuccess_cmnts,$conMsg_cmnts,$dbInstance_cmnts) = connectDB($sqlargs_cmnts);
#list($conSuccess_acces,$conMsg_acces,$dbInstance_acces) = connectDB($sqlargs_acces);
$value_users = validateDBConnection( connectDB($sqlargs_users) );
if (is_string($value_users)) { echo $value_users;$dbInstance_users=null; } else { $dbInstance_users = $value_users; }
$value_posts = validateDBConnection( connectDB($sqlargs_posts) );
if (is_string($value_posts)) { echo $value_posts;$dbInstance_posts=null; } else { $dbInstance_posts = $value_posts; }
$value_cmnts = validateDBConnection( connectDB($sqlargs_cmnts) );
if (is_string($value_cmnts)) { echo $value_cmnts;$dbInstance_cmnts=null; } else { $dbInstance_cmnts = $value_cmnts; }
$value_acces = validateDBConnection( connectDB($sqlargs_acces) );
if (is_string($value_acces)) { echo $value_acces;$dbInstance_acces=null; } else { $dbInstance_acces = $value_acces; }
print_r($dbInstance_users);
?>