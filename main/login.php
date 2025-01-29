<?php session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
    header("Location:tapSys.php"); //redirect to dashboard
    exit;
}

require_once "UserGateway.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    unset($_SESSION['error']);// clear all stored errors to reveal new ones

    if (isset($_POST["username"]) && isset($_POST["password"])) {

        // extract variables of email and password
        $username = $_POST["username"];
        $password = $_POST["password"];

        $user_gw = new UserGateway(); // create instance of the user table gateway

        $user = $user_gw->getUserByUsername($username); // getting user with matching email

        if ($user) { // user exists (function did not return false)

            if ($password == $user->getPassword()) { //password matches and user should be authenticated


                // setting needed session variables for loggedin user
                $_SESSION["loggedin"] = true;
                $_SESSION["user_id"] = $user->getUser_id();
                $_SESSION['name']= $user->getName();
                $_SESSION['type']= $user->getRole();

                unset($_SESSION['error']);// clear all stored errors since all was successful

                header("Location:tapSys.php"); //redirect to dashboard page
                exit;

            } else { //password does not match and user is not authenticated
                $_SESSION['error']['password'] = "1";
                header("Location:" . $_SERVER["PHP_SELF"]); //redirect to error and login page
                exit;
            }
        } else { //user does not exist(no match to username)

            $_SESSION['error']['username'] = "1";
            header("Location:" . $_SERVER["PHP_SELF"]); //redirect to error and login page
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>Login</title>
</head>

<body class="register-login-body">
<header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Login</h1>
        <nav>
            <a class="styled-link" href="register.php">Register</a>
            <a class="styled-link" href="Login.php">Login</a>
        </nav>
    </header>


    <!-- sides with logo -->
    <aside id="left">
        <div></div>
    </aside>

    <aside id="right">
        <div></div>
    </aside>


    <main>

        <section class="reg-container">

            <img src="../images/logo.png" alt="TAP logo" width="120">
            <h2>Login to TAP</h2>
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
                <fieldset>
                    <legend>Login Information</legend>

                    <div class="input-row">
                        <label for="username-input">Username:</label>
                        <input id="username-input" type="text" name="username" placeholder="Your Username" value="" required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["username"]) ? "show" : ""; ?>">Unrecognized Username.</span>
                    </div>
                    <div class="input-row">
                        <label for="password-input">Password:</label>
                        <input id="password-input" type="password" name="password" placeholder="Your Password" required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["password"]) ? "show" : ""; ?>">Incorrect Password for Username.</span>
                    </div>
                </fieldset>

                <input type="submit" value="Login" class="clickable-button">
            </form>
        </section>
    </main>

    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>