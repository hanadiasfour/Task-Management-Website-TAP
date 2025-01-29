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




//coming from dashboard get request link
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $task_id = $_GET["id"]; //extract task id

    $task = $task_gw->getTaskByID($task_id); // extract task object to display

    if (!$task) { // no task returned with this id (rise an error)
        header("Location: alert.php?code=undefined_task&id=$task_id");
        exit();
    }

    // post method when manager wants to assign the task to a member
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["decision"]) && isset($_POST["task_id"])) {

    $result = null;

    if ($_POST["decision"] == "yes") { //accepted=> change status to "In Progress" and flag accept in table
        $params = [
            ":task_id" => $_POST["task_id"],
            ":status" => "In Progress"
        ];

        $result = $task_gw->updateStatus($params);//change task to in progress

        $params2 = [
            ":task_id" => $_POST["task_id"],
            ":member_id" => $_SESSION["user_id"]
        ];

        $result2 = $task_gw->acceptTask($params2);//change allocation to accepted

        $result = $result && $result2;

        if ($result) { // decision processed successfully
            header("Location:alert.php?code=task_accepted");
            exit();
        }
    } else { //rejected=> delete from allocation table (remove link between user and task)
        $params = [
            ":task_id" => $_POST["task_id"],
            ":member_id" => $_SESSION["user_id"]
        ];

        $result = $task_gw->deleteAllocation($params);

        if ($result) { // decision processed successfully
            header("Location:alert.php?code=task_rejected");
            exit();
        }
    }

    if (!$result) { // not successful redirect to error
        header("Location:alert.php?code=decision_error&p_id=" . $_POST['task_id']);
        exit();
    }
} else { // incase there is no id in the get method

    header("Location: alert.php?code=undefined_task&id=UNDEFINED");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Accept/Reject Task</title>
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
        <h1>Accept/Reject Task</h1>
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
                <li>Choose to Accept or Reject This Task:</li>
            </ul>
        </h3>

        <section class="form_input_container">

            <form method="POST" action='<?php echo $_SERVER["PHP_SELF"] ?>'>
                <fieldset>
                    <legend>Task Details</legend>
                    <div class="input-row">
                        <label for="taskID">Task ID:</label>
                        <input type="text" id="taskID" value="<?php echo $task->getTaskId(); ?>" disabled>
                    </div>
                    <div class="input-row">
                        <label for="taskName">Task Name:</label>
                        <input type="text" id="taskName" value="<?php echo $task->getName(); ?>" disabled>
                    </div>

                    <div class="input-row">
                        <label for="p_description">Task Description:</label>
                        <textarea type="text" id="p_description" rows="10" disabled><?php echo $task->getDescription(); ?></textarea>
                    </div>

                    <div class="input-row">
                        <label for="t_eff">Task Effort:</label>
                        <input type="text" id="t_eff" value="<?php echo $task->getEffort(); ?>" disabled>
                    </div>

                    <div class="input-row">
                        <label for="t_prio">Task Priority:</label>
                        <input type="text" id="t_prio" value="<?php echo $task->getPriority(); ?>" disabled>
                    </div>

                    <div class="input-row">
                        <label for="t_stat">Task Status:</label>
                        <input type="text" id="t_stat" value="<?php echo $task->getStatus(); ?>" disabled>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Task Timeline</legend>

                    <div class="input-row">
                        <label for="t_start">Task Start Date:</label>
                        <input type="text" id="t_start" value="<?php echo $task->getStartDate(); ?>" disabled>
                    </div>

                    <div class="input-row">
                        <label for="t_end">Task End Date:</label>
                        <input type="text" id="t_end" value="<?php echo $task->getEndDate(); ?>" disabled>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Allocation Details</legend>

                    <?php $role = $task_gw->getRoleByMemberAndTask($_SESSION["user_id"], $task->getTaskId()); ?>

                    <div class="input-row">
                        <label for="role">Role:</label>
                        <input type="text" id="role" value="<?php echo $role; ?>" disabled>
                    </div>

                </fieldset>



                <fieldset>
                    <legend>User Decision</legend>

                    <div class="input-row">
                        <input type="radio" id="accept" name="decision" value="yes" required>
                        <label for="accept" class="positive">Accept Task</label>
                    </div>
                    <div class="input-row">
                        <input type="radio" id="reject" name="decision" value="no">
                        <label for="reject" class="negative">Reject Task</label>
                    </div>
                </fieldset>

                <input type="hidden" value="<?php echo $task->getTaskId(); ?>" name="task_id">
                <input type="submit" value="Submit Decision" class="clickable-button">


            </form>
        </section>




    </main>
    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>