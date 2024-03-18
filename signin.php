<?php
# Link requirements
require("php/_functions.php");
# Define sql-arguments
$sqlargs = array("localhost","root","","pagehost");
# Create connectionInstances
$conRet = validateDBConnection( connectDB($sqlargs) );
if (is_string($conRet)) { echo $conRet;$dbInstance=null; } else { $dbInstance = $conRet; }

# Setup
session_start();

$mode = "login";
$retmsg = null;

$isLoggingOut = false;
if (isset($_POST["sendingmode"]) && $_POST["sendingmode"] == "logout") {
    $isLoggingOut = true;
}

if (isset($_SESSION["clientID"]) && $isLoggingOut == false) {
    header("Location:customerpage.php");
    exit;
} else {
    # Check for internal-call meaning we have actions to do.
    if (isset($_POST["internal-call"]) && $_POST["internal-call"] == "true") {
        # Handle sending-mode login
        if (isset($_POST["sendingmode"]) && $_POST["sendingmode"] == "login") {
            # Should we shich mode?
            if (isset($_POST["submit-switch-tosignup"])) {
                $mode = "signup";
            # If not validate login-credentails
            } else {
                $mode = "login";
                # Get from post
                $username = $_POST["username"];
                $password = $_POST["password"];
                # Check for empties
                if (empty($username) || $username == "" || $username == null ||  empty($password) || $password == "" || $password == null) {
                    $retmsg = "ERROR:Failed to login! (One or more inputs was empty)";
                # If no inputs where empty validate the credentials
                } else {
                    $valid = validateClientCredentials($dbInstance,"users",$username,$password);
                    # If it wasn't valid set retmsg
                    if (!$valid) {
                        $retmsg = "ERROR:Failed to login! (Invalid credentials)";
                    # If it was valid send user to customerpage.php
                    } else {
                        $_SESSION["clientID"] = getClientIDFromName($dbInstance,"users",$username);
                        header("Location:customerpage.php");
                        exit;
                    }
                }
            }
        }
        # Handle sending-mode signup
        if (isset($_POST["sendingmode"]) && $_POST["sendingmode"] == "signup") {
            # Should we shich mode?
            if (isset($_POST["submit-switch-tologin"])) {
                $mode = "login";
            # If not check if user exists
            } else {
                $mode = "signup";
                # Get username from post
                $username = $_POST["username"];
                $password = $_POST["password"];
                $dispname = $_POST["dispname"];
                # Check if username was empty
                if (empty($username) || $username == "" || $username == null || empty($password) || $password == "" || $password == null) {
                    $retmsg = "ERROR:Failed to signup! (Username or Password was empty)";
                # If it wasn't check if users exists already
                } else {
                    $exists = checkIfUsernameExists($dbInstance,"users",$username);
                    if ($exists) {
                        $retmsg = "ERROR:Failed to signup! (Account ".$username." already exists!)";
                    # If it dosen't exist add it
                    } else {
                        if (empty($dispname) || $dispname == "") {
                            $dispname = null;
                        }
                        addClient($dbInstance,"users",$username,$password,$dispname,null);
                    }
                }
            }
        }
        # Handle sending-mode logout
        if (isset($_POST["sendingmode"]) && $_POST["sendingmode"] == "logout") {
            $mode = "login";
            session_destroy();
            $retmsg = "Logged out!";
        }
    # Default
    } else {
        if (!isset($_GET["signup"]) || isset($_GET["login"])) {
            $mode = "login";
        } else {
            $mode = "signup";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PageHost</title>
    <link href="./images/favicon.png" type="image/png">
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="./css/ui.css">
    <link rel="stylesheet" href="./css/signin.css">
</head>
<body>
    <header>
        <div id="nav-bar" class="flex-horiz flex-maxwidth">
            <div id="nav-titling" class="flex-horiz">
                <div id="nav-logo-wrapper">
                    <img src="./images/favicon.png" alt="Logo">
                </div>
                <div id="nav-pagename">
                    <h1>PageHost</h1>
                </div>
            </div>
            <div id="button-wrapper">
                <div id="nav-home">
                    <div class="ui-button ui-size-button-big">
                        <a href="./index.php">Home</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div id="loginbox">
            <form method="post" action="signin.php" enctype="multipart/form-data">
                <?php
                # Login
                if ($mode == "login") {
                    echo '<h1>Login to your account</h1>';
                    echo '<input type="hidden" name="internal-call" value="true">';
                    echo '<input type="hidden" name="sendingmode" value="login">';
                    echo '<input type="text" name="username" placeholder="Username" require>';
                    echo '<input type="password" name="password" placeholder="Password" require>';
                    // check for return message
                    if ($retmsg != null) {
                        // If ERROR: in the msg add it with the correct id so it can be colored by CSS
                        if (strpos($retmsg, "ERROR:") !== false) {
                            $retmsg = str_ireplace("ERROR:", "", $retmsg);
                            echo '
                            <div id="err-msg">
                                <p>' . $retmsg . '</p>
                            </div>';
                        // No ERROR: then just a msg (still handlable with css)
                        } else {
                            echo '
                            <div id="suc-msg">
                                <p>' . $retmsg . '</p>
                            </div>';
                        }
                    // Just BR incase no msg
                    } else {
                        echo '<br>';
                    }
                    echo '<input type="submit" value="Login" name="submit-login">';
                    echo '<input type="submit" value="Signup Instead?" name="submit-switch-tosignup">';
                # signup
                } else {
                    echo '<h1>Create an account</h1>';
                    echo '<input type="hidden" name="internal-call" value="true">';
                    echo '<input type="hidden" name="sendingmode" value="signup">';
                    echo '<input type="text" name="username" placeholder="Username" require>';
                    echo '<input type="password" name="password" placeholder="Password" require>';
                    echo '<input type="text" name="dispname" placeholder="(Optional) Display Name">';
                    // check for return message
                    if ($retmsg != null) {
                        // If ERROR: in the msg add it with the correct id so it can be colored by CSS
                        if (strpos($retmsg, "ERROR:") !== false) {
                            $retmsg = str_ireplace("ERROR:", "", $retmsg);
                            echo '
                            <div id="err-msg">
                                <p>' . $retmsg . '</p>
                            </div>';
                        // No ERROR: then just a msg (still handlable with css)
                        } else {
                            echo '
                            <div id="suc-msg">
                                <p>' . $retmsg . '</p>
                            </div>';
                        }
                    // Just BR incase no msg
                    } else {
                        echo '<br>';
                    }
                    echo '<input type="submit" value="Signup" name="submit-signup">';
                    echo '<input type="submit" value="Login Instead?" name="submit-switch-tologin" value="true">';
                }
                ?>
            </form>
        </div>
    </main>
    <i id="background-notice">Background from: <a href="https://picsum.photos/" target='_blank' >Lorem Picsum</a></i>
</body>
</html>