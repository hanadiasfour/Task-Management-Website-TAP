<?php session_start();

//prevent non logged in users and non managers from accessing this page
if (!isset($_SESSION["loggedin"]) || (isset($_SESSION["type"]) && $_SESSION["type"] != "Team Member")) {
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
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["status"]) && isset($_POST["task_id"]) && isset($_POST["progress"])) {

    if (setProgress()) {

        $params = [
            ":task_id" => $_POST["task_id"],
            ":progress" => $_SESSION["data"]['progress'],
            ":status" => $_SESSION["data"]['status']
        ];


        // assign member to task by inserting into the allocation table in db
        $result = $task_gw->updateProgress($params);

        unset($_SESSION["data"]);
        unset($_SESSION["error"]);

        if ($result) { // assignment was successful redirect to appropriate page
            header("Location:alert.php?code=prog_successful");
            exit();
        } else { // not successful redirect to error
            header("Location:alert.php?code=prog_unsuccessful");
            exit();
        }
    } else { //reload page to display errors and correct values

        header("Location:" . $_SERVER['PHP_SELF'] . "?id=" . $_POST['task_id']);
        exit();
    }
} else { // incase there is no id in the get method

    header("Location:alert.php?code=undefined_task&id=UNDEFINED");
    exit();
}



function setProgress()
{

    //reset errors array to check for any new errors
    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    if ($_POST["progress"] == 0) {
        // pending resets progress to 0
        $_SESSION["data"]["progress"] = 0;
        $_SESSION["data"]["status"] = "Pending";
    } else if ($_POST["progress"] == 100) {
        // Progress of 100% automatically sets status to Completed
        $_SESSION["data"]["progress"] = 100;
        $_SESSION["data"]["status"] = "Completed";

    } else if ($_POST["progress"] > 0 && $_POST["progress"] < 100 && $_POST["status"]== "In Progress") {
            // In Progress so progress > 0
            $_SESSION["data"]["progress"] = $_POST["progress"];
            $_SESSION["data"]["status"] = "In Progress";
    } else {

        $_SESSION["error"] = "1"; //contradiction
    }
    


    if (isset($_SESSION["error"])) // errors were detected
        return false;

    return true; // no errors were detected


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
                <li><a class="nav-link" href="assignments.php"><?php echo ((count($unacceptedTasks) > 0) ? "<strong><mark>Task Assignments</mark></strong>" : "Task Assignments"); ?></a></li>
                <li><a class="nav-link nav-link_selected" href="taskProgress.php">Task Progress</a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Edit Task Progress</h1>
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

                    <?php $p_name = $task_gw->getProjectNameByTaskID($task->getTaskId()); ?>

                    <div class="input-row">
                        <label for="p_name">Project Name:</label>
                        <input type="text" id="p_name" value="<?php echo $p_name; ?>" disabled>
                    </div>

                </fieldset>



                <fieldset>
                    <legend>Task Progress</legend>

                    <!-- SRC1: https://stackoverflow.com/questions/11788005/how-to-get-fetch-html5-range-sliders-value-in-php -->
                    <!-- SRC2: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/range -->
                    <div class="input-row">
                        <label for="progress-slider">Progress:</label>
                        <input type="range" id="progress-slider" name="progress" min="0" max="100"
                            value="<?php echo ((!is_null($task->getProgress())) ? $task->getProgress() : 0); ?>"
                            oninput="updateProgressValue(this.value)">
                    </div>

                    <div class="input-row">
                        <div></div>
                        <label id="progress-value"> <?php echo ((!is_null($task->getProgress())) ? $task->getProgress() : 0); ?>% </label>
                        <span class="error-message <?php echo isset($_SESSION["error"]) ? "show" : ""; ?>">Status must be In progress when the value is greater than 0%.</span>
                    </div>

                    <script>
                        function updateProgressValue(value) {
                            document.getElementById("progress-value").innerText = value + "%";
                        }
                    </script>

                    <div class="input-row">
                        <label for="status-dropdown">Task Status:</label>
                        <select id="status-dropdown" name="status" required>
                            <option value="Pending" <?php echo ((($task->getStatus() == "Pending")) ? "selected" : ""); ?>>Pending</option>
                            <option value="In Progress" <?php echo ((($task->getStatus() == "In Progress")) ? "selected" : ""); ?>>In Progress</option>
                            <option value="Completed" <?php echo ((($task->getStatus() == "Completed")) ? "selected" : ""); ?>>Completed</option>
                        </select>
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