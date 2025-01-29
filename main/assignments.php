<?php session_start();


//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])||(isset($_SESSION["type"]) && $_SESSION["type"] != "Team Member")){
    header("Location:tapSys.php");
    exit;
}


require_once "TaskGateway.php";

$task_gw = new TaskGateway(); //creating new gateway instance

//gets newly tasks allocated to this member but not yet confirmed
$unacceptedTasks = $task_gw->getUnacceptedMemberTasks($_SESSION["user_id"]);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Task Assignments</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">


    <aside>
        <nav>
            <ul>
                <!-- HERE MAKE IT BOLD AND YELLOW HIGHLIGHT -->
                <li><a class='nav-link' href='Dashboard.php'>Dashboard</a></li>
                <li><a class="nav-link nav-link_selected" href="assignments.php"><?php echo ((count($unacceptedTasks) > 0) ? "<strong><mark>Task Assignments</mark></strong>" : "Task Assignments"); ?></a></li>
                <li><a class="nav-link" href="taskProgress.php">Task Progress</a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Assigned Tasks</h1>
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

        <h3>
            <ul>
                <li>Select Task to Accept/Reject:</li>
            </ul>
        </h3>

        <section class="form_input_container">

            <table>
                <!--table headers with data categories-->
                <thead>
                    <tr><!--adding the scope makes the table more accessible-->
                        <th scope="col"><em>Task ID</em></th>
                        <th scope="col"><em>Task Name</em></th>
                        <th scope="col"><em>Project Name</em></th>
                        <th scope="col"><em>Start Date</em></th>
                        <th scope="col"><em>Confirm Action</em></th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    //checking if there is any returned data rows to display 
                    //(giving the user context instead of rendering an empty table)
                    if ((count($unacceptedTasks))  == 0) {
                        echo "<tr><td colspan='6'>No Tasks yet allocated to this project.</td></tr>";
                    } else {
                        //displaying table rows
                        foreach ($unacceptedTasks as $t) {
                            //get project name linked to this task then print row
                            $projectName = $task_gw->getProjectNameByTaskID($t->getTaskId());
                            echo $t->displayTaskRowMember($projectName);
                        }
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>