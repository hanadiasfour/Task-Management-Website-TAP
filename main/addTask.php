<?php session_start();


//prevent non logged in users and non managers from accessing this page
if (!isset($_SESSION["loggedin"]) || (isset($_SESSION["type"]) && $_SESSION["type"] != "Project Leader")) {
    header("Location:tapSys.php");
    exit;
}


require_once "ProjectGateway.php";
require_once "TaskGateway.php";

$project_gw = new ProjectGateway();
$task_gw = new TaskGateway();

//coming from form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (validateInputs($project_gw, $task_gw)) {

        //save task and go to confirmation

        //fill container with the file names

        $params = [
            ":task_id" => $_SESSION["data"]['task_id'],
            ":taskName" => $_SESSION["data"]['taskName'],
            ":taskDescription" => $_SESSION["data"]['taskDescription'],
            ":effort" => $_SESSION["data"]['effort'],
            ":startDate" => $_SESSION["data"]['startDate'],
            ":endDate" => $_SESSION["data"]['endDate'],
            ":priority" => $_SESSION["data"]['priority'],
            ":status" => $_SESSION["data"]['status'],
            ":associatedProject" => $_SESSION["data"]['associatedProject']
        ];

        $task_gw->insertTask($params); // add task to database

        //clean up resources used
        unset($_SESSION["data"]);
        unset($_SESSION["error"]);
        header("Location: alert.php?code=addition_successful1");
        exit();
    } else {
        // relocate to same step to fix problems
        header("Location:" . $_SERVER['PHP_SELF']);
    }
}

function validateInputs(&$project_gw, &$task_gw)
{
    //reset errors array to check for any new errors
    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    if (!isset($_POST["task_id"]) || !preg_match("/^[A-Za-z0-9]{10}$/", $_POST['task_id'])) {
        $_SESSION["error"]["task_id"] = "1";
    } else { // id syntax is correct

        // making sure the id is unique to the database
        $data = $task_gw->getTaskByID($_POST["task_id"]);

        if (!$data) //unique
            $_SESSION["data"]["task_id"] = $_POST["task_id"];

        else //already used
            $_SESSION["error"]["task_id"] = "1";
    }

    $p_start = null;
    $p_end = null;


    if (!isset($_POST["associatedProject"])) {
        $_SESSION["error"]["associatedProject"] = "1";
    } else { // id syntax is correct
        // making sure the id is unique to the database
        $data = $project_gw->getProjectByID($_POST["associatedProject"]);

        if (!$data) // does not exist for some reason
            $_SESSION["error"]["associatedProject"] = "1";

        else { // exists
            $_SESSION["data"]["associatedProject"] = $_POST["associatedProject"];
            $p_start = $data->getStartDate();
            $p_end = $data->getEndDate();
        }
    }

    if (!isset($_POST["taskName"]) || $_POST['taskName'] == "")
        $_SESSION["error"]["taskName"] = "1";
    else
        $_SESSION["data"]['taskName'] = $_POST['taskName'];

    if (!isset($_POST["taskDescription"]) || $_POST['taskDescription'] == "")
        $_SESSION["error"]["taskDescription"] = "1";
    else
        $_SESSION["data"]['taskDescription'] = $_POST['taskDescription'];

    if (!isset($_POST["effort"]) || $_POST['effort'] == "" || !preg_match("/^\d+$/", $_POST['effort']))
        $_SESSION["error"]["effort"] = "1";
    else
        $_SESSION["data"]['effort'] = $_POST['effort'];

    if (isset($_POST["priority"]))
        $_SESSION["data"]['priority'] = $_POST['priority'];

    if (isset($_POST["status"]))
        $_SESSION["data"]['status'] = $_POST['status'];

    if (!isset($_POST["startDate"]) || $_POST['startDate'] < $p_start || $_POST['startDate'] > $p_end)
        $_SESSION["error"]["startDate"] = $p_start;

    else {
        $_SESSION["data"]['startDate'] = $_POST['startDate'];

        if (!isset($_POST["endDate"]) || $_POST['endDate'] < $_POST['startDate'] || $_POST['endDate'] > $p_end)
            $_SESSION["error"]["endDate"] = $p_end;

        else
            $_SESSION["data"]['endDate'] = $_POST['endDate'];
    }


    if (isset($_SESSION["error"])) // errors were detected
        return false;

    return true; // no errors were detected


}


function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Add New Task</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

    <aside>
        <nav>
            <ul>
                <li><a class='nav-link' href='Dashboard.php'>Dashboard</a></li>
                <li><a class="nav-link nav-link_selected" href="addTask.php">Add Task</a></li>
                <li><a class="nav-link" href="taskDash.php">Task Dashboard</a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Add New Task</h1>
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
                <li>Please fill in this form to add a new task:</li>
            </ul>
        </h3>

        <section class="form_input_container">

            <form action='<?php echo $_SERVER["PHP_SELF"] ?>' method="POST">

                <fieldset>
                    <legend>Task Details</legend>
                    <div class="input-row <?php echo isset($_SESSION["error"]["task_id"]) ? "wrong-input" : ""; ?>">
                        <label for="t_id">Task ID:</label>
                        <input type="text" id="t_id" name="task_id" maxlength="10" placeholder="a12b4cd78f"
                            pattern="^[A-Za-z0-9]{10}$" title="Must be 10 alphanumeric characters."
                            value="<?php echo isset($_SESSION["data"]['task_id']) ? $_SESSION["data"]['task_id'] : generateRandomString(); ?>" required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["task_id"]) ? "show" : ""; ?>">Task ID must be unique 10 alphanumeric characters and not empty.</span>

                    </div>

                    <div class="input-row <?php echo isset($_SESSION["error"]["taskName"]) ? "wrong-input" : ""; ?>">
                        <label for="t_name">Task Name:</label>
                        <input type="text" id="t_name" name="taskName" placeholder="Short Name" maxlength="30"
                            value="<?php echo isset($_SESSION["data"]['taskName']) ? $_SESSION["data"]['taskName'] : ""; ?>" required>
                    </div>

                    <div class="input-row">
                        <label for="p_description">Task Description:</label>
                        <textarea id="p_description" name="taskDescription" rows="10"
                            placeholder="Write your explanation of the task here..."
                            required><?php echo isset($_SESSION["data"]['taskDescription']) ? $_SESSION["data"]['taskDescription'] : ""; ?></textarea>
                    </div>


                    <div class="input-row <?php echo isset($_SESSION["error"]["effort"]) ? "wrong-input" : ""; ?>">
                        <label for="t_effort">Effort:</label>
                        <input type="number" id="t_effort" name="effort" min="0" placeholder="Effort in Man-Months"
                            value="<?php echo isset($_SESSION["data"]['effort']) ? $_SESSION["data"]['effort'] : "";
                                    ?>" required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["effort"]) ? "show" : "";
                                                    ?>">Effort must be positive number and not empty.</span>
                    </div>


                    <div class="input-row">
                        <label for="t_priority">Priority:</label>
                        <select id="t_priority" name="priority" required>
                            <option value="" selected>*Select*</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>



                </fieldset>

                <fieldset>
                    <legend>Project Association</legend>

                    <div class="input-row">
                        <label for="project">Associated Project:</label>
                        <select id="project" name="associatedProject" required>
                            <option value="">*Select Project*</option>
                            <?php

                            $projects = $project_gw->getProjectByLeader($_SESSION["user_id"]);
                            //filling the select input with projects lead by this user
                            foreach ($projects as $p) {
                                echo "<option value='" . ($p->getProject_id()) . "'>" . ($p->getTitle()) . "</option>";
                            }
                            ?>

                        </select>
                    </div>
                </fieldset>


                <fieldset>
                    <legend>Task Timeline</legend>


                    <div class="input-row <?php echo isset($_SESSION["error"]["startDate"]) ? "wrong-input" : ""; ?>">
                        <label for="startDate">Start Date:</label>
                        <input type="date" id="startDate" name="startDate"
                            value="<?php echo isset($_SESSION['data']['startDate']) ? $_SESSION['data']['startDate'] : ''; ?>"
                            required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["startDate"]) ? "show" : "";
                                                    ?>">Start date cannot be earlier than the project’s start date <?php echo ($_SESSION["error"]["startDate"]); ?> and must not be empty.</span>

                    </div>

                    <div class="input-row <?php echo isset($_SESSION["error"]["endDate"]) ? "wrong-input" : ""; ?>">
                        <label for="endDate">End Date:</label>
                        <input type="date" id="endDate" name="endDate"
                            value="<?php echo isset($_SESSION["data"]['endDate']) ? $_SESSION["data"]['endDate'] : ""; ?>" required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["endDate"]) ? "show" : "";
                                                    ?>">End date cannot exceed the project’s end date <?php echo ($_SESSION["error"]["endDate"]); ?> and must not be empty.</span>
                    </div>
                </fieldset>


                <input type="hidden" name="status" value="Pending">
                <input type="submit" value="Create Task" class="clickable-button"></input>

            </form>

        </section>




    </main>
    <footer><!--footer with policies and contact info-->
        <?php include_once "footer.html"; ?>
    </footer>
</body>

</html>