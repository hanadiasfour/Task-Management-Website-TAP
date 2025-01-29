<?php session_start();


//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])){
    header("Location:tapSys.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Confirmation</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

    <aside>
    <?php include_once "asideNav.php"; ?> 
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Confirmation</h1>
        <div class="top-nav">
            <img src="../images/line.png" alt="line" width="5" height="70">
            <a href="profile.php" class="user-card">
                <img src="../images/profile.jpg" alt="User Photo" class="user-photo">
                <span class="styled-link"><?php echo $_SESSION['name']?></span>
            </a>
            <img src="../images/line.png" alt="line" width="5" height="70">
            <a class="styled-link" href="logout.php">Logout</a>
        </div>
    </header>

    <main>

        <section class="reg-container">
            <?php
            if (isset($_GET['code'])) {

                switch ($_GET['code']) {

                    case "addition_successful": //added project successfully

                        echo '<img src="../images/success.png" alt="successful" width="150"><h2 id="fancy_text">Action was Successful!</h2>';
                        echo "<h2>Your Project Was Successfully Saved.</h2>";
                        echo "<p>Now that you have a new project, you can allocate a team leader in the <em>Allocate Team Leader</em> section</p>";
                        echo "<a href='unassignedProjects.php'><input class='clickable-button' type='button' value='Allocate a Leader'></a>";
                        break;


                    case "undefined_project": // error from undefined project id

                        echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">Action was Unsuccessful!</h2>';
                        echo "<h2>Non-existing Project.</h2>";
                        echo "<p>Im sorry, but the project you are trying to access with ID: " . (isset($_GET['id']) ? $_GET['id'] : "####") . " does not exist.</p>";
                        echo "<a href='unassignedProjects.php'><input class='clickable-button' type='button' value='Back to Project List'></a>";
                        break;


                    case "assigned_project": // error from already assigned project id

                        echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">Action was Unsuccessful!</h2>';
                        echo "<h2>Project Already Assigned.</h2>";
                        echo "<p>It seems like the project you are trying to access with ID: " . (isset($_GET['id']) ? $_GET['id'] : "####") . " was already assigned to a leader before.</p>";
                        echo "<a href='unassignedProjects.php'><input class='clickable-button' type='button' value='Back to Project List'></a>";
                        break;


                    case "assign_unsuccessful": // error when assigning the project was unsuccessful 

                        echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">Action was Unsuccessful!</h2>';
                        echo "<h2>Failed to Assign Leader.</h2>";
                        echo "<p>Uh Oh! There was a problem when trying to assign the leader " . (isset($_GET['user_id']) ? $_GET['user_id'] : "####") . " to project with ID: " . (isset($_GET['p_id']) ? $_GET['p_id'] : "####") . ".</p>";
                        echo "<a href='unassignedProjects.php'><input class='clickable-button' type='button' value='Back to Project List'></a>";
                        break;

                    case "assign_successful": // assigning leader to the project was successful 

                        echo '<img src="../images/success.png" alt="successful" width="150"><h2 id="fancy_text">Action was Successful!</h2>';
                        echo "<h2>Leader was Assigned!</h2>";
                        echo "<p>The user " . (isset($_GET['user_id']) ? $_GET['user_id'] : "####") . " was successfully assigned as Leader to project with ID: " . (isset($_GET['p_id']) ? $_GET['p_id'] : "####") . ".</p>";
                        echo "<a href='unassignedProjects.php'><input class='clickable-button' type='button' value='Back to Project List'></a>";
                        break;

                    case "addition_successful1": //added task successfully

                        echo '<img src="../images/success.png" alt="successful" width="150"><h2 id="fancy_text">Action was Successful!</h2>';
                        echo "<h2>Your Task Was Successfully Saved.</h2>";
                        echo "<p>Now that you have a new task, you can allocate a team members in the <em>Task Dashboard</em> section</p>";
                        echo "<a href='taskDash.php'><input class='clickable-button' type='button' value='Allocate Team Members'></a>";
                        break;

                    case "undefined_task": // error from undefined task id

                        echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">Action was Unsuccessful!</h2>';
                        echo "<h2>Non-existing Task.</h2>";
                        echo "<p>Im sorry, but the task you are trying to access with ID: " . (isset($_GET['id']) ? $_GET['id'] : "####") . " does not exist.</p>";
                        echo "<a href='taskDash.php'><input class='clickable-button' type='button' value='Back to Task List'></a>";
                        break;

                    case "Tassign_unsuccessful": // error when assigning the task was unsuccessful 

                        echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">Action was Unsuccessful!</h2>';
                        echo "<h2>Failed to Assign Member.</h2>";
                        echo "<p>Uh Oh! There was a problem when trying to assign the member " . (isset($_GET['user_id']) ? $_GET['user_id'] : "####") . " to task with ID: " . (isset($_GET['p_id']) ? $_GET['p_id'] : "####") . ".</p>";
                        echo "<a href='taskDash.php'><input class='clickable-button' type='button' value='Back to Task List'></a>";
                        break;

                    case "Tassign_successful": // assigning leader to the task was successful 

                        echo '<img src="../images/success.png" alt="successful" width="150"><h2 id="fancy_text">Action was Successful!</h2>';
                        echo "<h2>Member was Assigned!</h2>";
                        echo "<p>The user <strong>" . (isset($_GET['user_id']) ? $_GET['user_id'] : "####") . " </strong>was successfully assigned as a <strong><em>" . (isset($_GET['role']) ? $_GET['role'] : "####") . "</em></strong> to task with ID: <strong>" . (isset($_GET['p_id']) ? $_GET['p_id'] : "####") . "</strong>.</p>";
                        echo "<a href='taskDash.php'><input class='clickable-button change-color' type='button' value='Finish Allocation'></a>";
                        echo "<a href='assignTask.php?id=" . $_GET['p_id'] . "'><input class='clickable-button' type='button' value='Add Another Member'></a>";

                        break;


                    case "task_accepted": //accept decision was successful

                        echo '<img src="../images/success.png" alt="successful" width="150"><h2 id="fancy_text">Action was Successful!</h2>';
                        echo "<h2>Task Successfully Accepted and Activated.</h2>";
                        echo "<p>Now that you have accepted this task, you can view it and change your progress in it.</p>";
                        break;



                    case "task_rejected": //reject decision was successful

                        echo '<img src="../images/success.png" alt="successful" width="150"><h2 id="fancy_text">Action was Successful!</h2>';
                        echo "<h2>Task Assignment Successfully Rejected.</h2>";
                        echo "<p>Your allocation to this task was successfully deleted from the system.</p>";
                        break;




                    case "decision_error": //whole decision was unsuccessful

                        echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">Action was Unsuccessful!</h2>';
                        echo "<h2>Your Decision was Unsuccessfully Recorded.</h2>";
                        echo "<p>Woops! For some reason your decision was not processed correctly. Please try again.</p>";
                        echo "<a href='confirmTask.php?id=" . $_GET['p_id'] . "'><input class='clickable-button' type='button' value='Try Again'></a>";

                        break;


                        case "prog_unsuccessful": // error when assigning the task was unsuccessful 

                            echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">Action was Unsuccessful!</h2>';
                            echo "<h2>Failed to update Task.</h2>";
                            echo "<p>Uh Oh! There was a problem when trying to update the Task progress and status.</p>";
                            echo "<a href='taskProgress.php'><input class='clickable-button change-color' type='button' value='Try Again'></a>";
                            break;
    
                        case "prog_successful": // assigning leader to the task was successful 
    
                            echo '<img src="../images/success.png" alt="successful" width="150"><h2 id="fancy_text">Action was Successful!</h2>';
                            echo "<h2>Task updated successfully!</h2>";
                            echo "<p>Your Task progress and status were successfully updated. Update more tasks:</p>";
                            echo "<a href='taskProgress.php'><input class='clickable-button change-color' type='button' value='See More Tasks'></a>";
    
                            break;
    


                    default: //code not recognized
                    echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">ERROR</h2>';
                        echo "Error Code Not Recognized";
                        echo "<br><br> You will be redirected to another page shortly. </p>";
                        echo '<meta http-equiv="refresh" content="10;url=tapSys.php">';
                        break;
                }
            }else{
                echo '<img src="../images/failure.png" alt="unsuccessful" width="150"><h2 id="fancy_text">ERROR</h2>';
                echo "Error Code Not Recognized";
                echo "<br><br> You will be redirected to another page shortly. </p>";
                echo '<meta http-equiv="refresh" content="10;url=tapSys.php">';

            }
            ?>

        </section>

    </main>
    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>