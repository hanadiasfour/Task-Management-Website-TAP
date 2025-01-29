<?php
session_start(); //start the session as 1st command


//checks the state of the user in the session 
//when logged_in, continue to  dashboard
//else, the user is taken to the login page

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
    header("Location:Dashboard.php");


} else { // user is not logged in

    header("Location:login.php");
    exit;
}
?>