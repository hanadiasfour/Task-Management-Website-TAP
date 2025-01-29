<?php session_start();

//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])||(isset($_SESSION["type"]) && $_SESSION["type"] != "Team Member")){
    header("Location:tapSys.php");
    exit;
}

require_once "TaskGateway.php";

$task_gw = new TaskGateway(); //creating new gateway instance

//gets newly tasks allocated to this member but not yet confirmed
$unacceptedTasks = $task_gw->getUnAcceptedMemberTasks($_SESSION["user_id"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Tasks Progress</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

    <aside>
        <nav>
            <ul>
                <!-- HERE MAKE IT BOLD AND YELLOW HIGHLIGHT -->
                <li><a class='nav-link' href='Dashboard.php'>Dashboard</a></li>
                <li><a class="nav-link " href="assignments.php"><?php echo ((count($unacceptedTasks) > 0) ? "<strong><mark>Task Assignments</mark></strong>" : "Task Assignments"); ?></a></li>
                <li><a class="nav-link nav-link_selected" href="taskProgress.php">Task Progress</a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Tasks List</h1>
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
        <section><!--searching section-->
            <form action=<?php echo '"'.$_SERVER['PHP_SELF'].'"'; ?> method="POST">
                <fieldset>
                    <legend>My Tasks Search</legend>

                    <label for="id">Task ID: </label>
                    <input type="text" name="t_id" id="id" placeholder="Search..." value="">

                    <label for="t_n">Task Name: </label>
                    <input type="text" name="t_name" id="t_n" placeholder="Search..." value="">

                    <label for="p_n">Project Name: </label>
                    <input type="text" name="p_name" id="p_n" placeholder="Search..." value=""><br>

                    <label for="stat">Status: </label>
                    <select name="status" id="stat">
                        <option value="" selected>All</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>

                    <input type="submit" value="Filter" class="clickable-button">

                </fieldset>
            </form>
        </section>

        <section class="form_input_container">
            <table>
                <!--table headers with data categories-->
                <thead>
                    <tr><!--adding the scope makes the table more accessible-->
                        <th scope="col"><em>Task ID</em></th>
                        <th scope="col"><em>Task Name</em></th>
                        <th scope="col"><em>Project Name</em></th>
                        <th scope="col"><em>Status</em></th>
                        <th scope="col"><em>Edit Progress Action</em></th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                        // not first time loading page => show filtered tasks
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {

                            //obtaining the form data, if empty then assign as null
                            $t_id = null;
                            $t_name = null;
                            $p_name = null;
                            $status = null;
                        
                            // set variables when submitted with data (not empty)
                            if (isset($_POST['t_id']) && $_POST['t_id'] != "") {
                                $t_id = $_POST['t_id'];
                            }
                            if (isset($_POST['t_name']) && $_POST['t_name'] != "") {
                                $t_name = $_POST['t_name'];
                            }

                            if (isset($_POST['p_name']) && $_POST['p_name'] != "") {
                                $p_name = $_POST['p_name'];
                            }

                            if (isset($_POST['status']) && $_POST['status'] != "") {
                                $status = $_POST['status'];
                            }

                            // get tickets matching filtered data
                            $filteredTasks = $task_gw->getMemberFilteredTasks($t_id, $t_name, $p_name, $status, $_SESSION["user_id"]);

                            //checking if there is any returned data rows to display 
                            //(giving the user context instead of rendering an empty table)
                            if (count($filteredTasks) == 0) {
                                echo "<tr><td colspan='5'>No tasks match the specified criteria.</td></tr>";

                            }else {
                                //displaying table rows
                                foreach($filteredTasks as $task){
                                    echo $task->displayTaskRowMember($task_gw->getProjectNameByTaskID($task->getTaskID()), 2);
                                }
                            }
                            
                        }else{// first time loading page => show all accepted tasks by this member
                            
                                // get tickets with pending status 
                                $acceptedTasks = $task_gw->getAcceptedMemberTasks($_SESSION["user_id"]);

                                //checking if there is any returned data rows to display 
                                //(giving the user context instead of rendering an empty table)
                                if (count($acceptedTasks) == 0) {
                                    echo "<tr><td colspan='5'>No tasks match the specified criteria.</td></tr>";
                                    
                                } else {
                                    //displaying table rows
                                    foreach ($acceptedTasks as $t) {
                                        //get project name linked to this task then print row
                                        $projectName = $task_gw->getProjectNameByTaskID($t->getTaskId());
                                        echo $t->displayTaskRowMember($projectName, 2);
                                    }
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