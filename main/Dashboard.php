<?php session_start();

//prevent non logged in users and non managers from accessing this page
if (!isset($_SESSION["loggedin"])) {
    header("Location:tapSys.php");
    exit;
}

require_once "TaskGateway.php";
require_once "ProjectGateway.php";

$task_gw = new TaskGateway(); //creating new gateway instance
$project_gw = new ProjectGateway(); //creating new gateway instance

function isManager()
{
    return $_SESSION["type"] == "Manager";
}
function isLeader()
{
    return $_SESSION["type"] == "Project Leader";
}
function isMember()
{
    return $_SESSION["type"] == "Team Member";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Dashboard</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

    <aside>
        <?php $_SESSION["Dash"] = "1";
        include_once "asideNav.php"; ?>
    </aside>


    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Tasks List</h1>
        <div class="top-nav">
            <img src="../images/line.png" alt="line" width="5" height="70">
            <a href="profile.php" class="user-card">
                <img src="../images/profile.jpg" alt="User Photo" class="user-photo">
                <span class="styled-link"><?php echo $_SESSION['name'] ?></span>
            </a>
            <img src="../images/line.png" alt="line" width="5" height="70">
            <a class="styled-link" href="logout.php">Logout</a>
        </div>
    </header>

    <main>
        <section><!--searching section-->
            <form action=<?php echo '"' . $_SERVER['PHP_SELF'] . '"'; ?> method="POST">
                <fieldset>
                    <legend>My Tasks Search</legend>

                    <label for="priority">Task Priority:</label>
                    <select id="priority" name="priority">
                        <option value="">*Select Priority*</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>

                    <label for="status">Task Status:</label>
                    <select id="status" name="status">
                        <option value="">*Select Status*</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>

                    <label>Due Date Range:</label>
                    <input type="date" name="start_date" placeholder="Start Date">
                    <input type="date" name="end_date" placeholder="End Date">

                    <label for="project">Project:</label>
                    <input type="text" id="project" name="project" placeholder="Enter project name">


                    <input type="submit" value="Filter" class="clickable-button">
            </form>
            </fieldset>

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
                        <th scope="col"><em>Priority</em></th>
                        <th scope="col"><em>Start Date</em></th>
                        <th scope="col"><em>End Date</em></th>
                        <th scope="col"><em>Completion Percentage</em></th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    // not first time loading page => show filtered tasks
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {

                        //obtaining the form data, if empty then assign as null
                        $priority = null;
                        $status = null;
                        $startDate = null;
                        $endDate = null;
                        $projectTitle = null;

                        //getFilteredTasksForTeamLeader($teamLeader, $priority = null, $status = null, $startDate = null, $endDate = null, $projectTitle = null)
                        // set variables when submitted with data (not empty)
                        if (isset($_POST['priority']) && $_POST['priority'] != "") {
                            $priority = $_POST['priority'];
                        }
                        if (isset($_POST['status']) && $_POST['status'] != "") {
                            $status = $_POST['status'];
                        }

                        if (isset($_POST['start_date']) && $_POST['start_date'] != "") {
                            $startDate = $_POST['start_date'];
                        }
                        
                        if (isset($_POST['end_date']) && $_POST['end_date'] != "") {
                            $endDate = $_POST['end_date'];
                        }

                        if (isset($_POST['project']) && $_POST['project'] != "") {
                            $projectTitle = $_POST['project'];
                        }

                        //getFilteredTasksForTeamLeader($teamLeader, $priority = null, $status = null, $startDate = null, $endDate = null, $projectTitle = null)

                        $filteredTasks = null;
                        if(isManager()){
                            // get all tasks in the system 
                            $filteredTasks = $task_gw->getFilteredTasks(0,$_SESSION["user_id"],$priority,$status,$startDate,$endDate,$projectTitle);

                        }else if(isLeader()){
                            // get all tasks from project leaded by this user
                            $filteredTasks = $task_gw->getFilteredTasks(1,$_SESSION["user_id"],$priority,$status,$startDate,$endDate,$projectTitle);

                        }else if(isMember()){
                            // get all tasks accepted by this member
                            $filteredTasks = $task_gw->getFilteredTasks(2,$_SESSION["user_id"],$priority,$status,$startDate,$endDate,$projectTitle);
                        }


                        //checking if there is any returned data rows to display 
                        //(giving the user context instead of rendering an empty table)
                        if (count($filteredTasks) == 0) {
                            echo "<tr><td colspan='8'>No tasks match the specified criteria.</td></tr>";
                        } else {
                            //displaying table rows
                            foreach ($filteredTasks as $task) {
                                echo $task->displayTaskDetails($task_gw->getProjectNameByTaskID($task->getTaskID()));
                            }
                        }
                    } else { // first time loading page => show all tasks depending on user type

                        $tasks = null;
                        if(isManager()){
                            // get all tasks in the system 
                            $tasks = $task_gw->getAllTasks();

                        }else if(isLeader()){
                            // get all tasks from project leaded by this user
                            $tasks = $task_gw->getLeaderProjectTasks($_SESSION["user_id"]);

                        }else if(isMember()){
                            // get all tasks accepted by this member
                            $tasks = $task_gw->getAcceptedMemberTasks($_SESSION["user_id"]);
                        }


                        //checking if there is any returned data rows to display 
                        //(giving the user context instead of rendering an empty table)
                        if (count($tasks) == 0) {
                            echo "<tr><td colspan='8'>No tasks match the specified criteria.</td></tr>";
                        } else {
                            //displaying table rows
                            foreach ($tasks as $t) {
                                //get project name linked to this task then print row
                                $projectName = $task_gw->getProjectNameByTaskID($t->getTaskId());
                                echo $t->displayTaskDetails($projectName);
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
<? unset($_SESSION["Dash"]); ?>