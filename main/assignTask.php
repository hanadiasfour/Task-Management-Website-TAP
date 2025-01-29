<?php session_start();

//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])||(isset($_SESSION["type"]) && $_SESSION["type"] != "Project Leader")){
    header("Location:tapSys.php");
    exit;
}

require_once "TaskGateway.php";
require_once "UserGateway.php";

//initialize used gateways
$task_gw = new TaskGateway();
$user_gw = new UserGateway();

//coming from dashboard get request link
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $task_id = $_GET["id"]; //extract task id

    $task = $task_gw->getTaskByID($task_id); // extract task object to display

    if (!$task) { // no task returned with this id (rise an error)
        header("Location: alert.php?code=undefined_task&id=$task_id");
        exit();
    }

    // obtaining list of members from db using the gateway
    $users = $user_gw->getTeamMembers();


    // post method when manager wants to assign the task to a member
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["member_id"]) && isset($_POST["task_id"])) {

    if (validateInputs($task_gw)) {

        $params = [
            ":task_id" => $_SESSION["data"]['task_id'],
            ":member_id" => $_SESSION["data"]['member_id'],
            ":startDate" => $_SESSION["data"]['startDate'],
            ":contribution" => $_SESSION["data"]['contribution'],
            ":role" => $_SESSION["data"]['role']
        ];


        // assign member to task by inserting into the allocation table in db
        $result = $task_gw->assignTask($params);

        unset($_SESSION["data"]);
        unset($_SESSION["error"]);

        if ($result) { // assignment was successful redirect to appropriate page
            header("Location:alert.php?code=Tassign_successful&p_id=" . $_POST['task_id'] . "&user_id=" . $_POST["member_id"] . "&role=" .$_POST["role"]);
            exit();
        } else { // not successful redirect to error
            header("Location:alert.php?code=Tassign_unsuccessful&p_id=" . $_POST['task_id'] . "&user_id=" . $_POST["member_id"]);
            exit();
        }
    } else { //reload page to display errors and correct values

        header("Location:" . $_SERVER['PHP_SELF'] . "?id=" . $_POST['task_id']);
        exit();
    }
} else { // incase there is no id in the get method

    header("Location: alert.php?code=undefined_task&id=UNDEFINED");
    exit();
}

function validateInputs(&$task_gw)
{
    //reset errors array to check for any new errors
    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    if (isset($_POST["task_id"]))
        $_SESSION["data"]["task_id"] = $_POST["task_id"];

    if (isset($_POST["member_id"]))
        $_SESSION["data"]["member_id"] = $_POST["member_id"];

    if (isset($_POST["role"]))
        $_SESSION["data"]["role"] = $_POST["role"];

    if (!isset($_POST["startDate"]) || $_POST['startDate'] < $_POST["taskStartDate"] ||  $_POST['startDate'] < date('Y-m-d')) {
        $_SESSION["error"]["startDate"] = $_POST["taskStartDate"];
    } else {
        $_SESSION["data"]['startDate'] = $_POST['startDate'];
    }


    if (!isset($_POST["contribution"])) { // not set for some reason
        $_SESSION["error"]["contribution"] = "1";
    } else { //must validate by keeping contribution inder 100% 
        $currentContribution = $task_gw->getCurrentTaskContribution($_POST["task_id"]);

        if (($_POST["contribution"] + $currentContribution) > 100) { // exceeded total contribution of 100
            // let them know how much contribution to this project is left to allocate
            $_SESSION["error"]["contribution"] = $currentContribution;
        } else { //good enough contribution for now
            $_SESSION["data"]["contribution"] = $_POST["contribution"];
        }
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
    <title>TAP | Assign Team Member</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

    <aside>
        <nav>
            <ul>
                <li><a class='nav-link' href='Dashboard.php'>Dashboard</a></li>
                <li><a class="nav-link" href="addTask.php">Add Task</a></li>
                <li><a class="nav-link nav-link_selected" href="taskDash.php">Task Dashboard</a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Assign Team Member</h1>
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
                <li>Assign a Team Member to this Task:</li>
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
                    <div class="input-row <?php echo isset($_SESSION["error"]["startDate"]) ? "wrong-input" : ""; ?>">
                        <label for="sDate">Start Date:</label>
                        <input type="date" id="sDate" name="startDate" value="<?php echo (isset($_SESSION['data']['startDate']) ? $_SESSION['data']['startDate'] : date('Y-m-d')); ?>">
                    </div>
                    <span class="error-message <?php echo isset($_SESSION["error"]["startDate"]) ? "show" : "";
                                                ?>">Date cannot be earlier than the Task start date <?php echo ($_SESSION["error"]["startDate"]); ?> and must not be empty.</span>


                </fieldset>

                <fieldset>
                    <legend>Allocation Details</legend>

                    <div class="input-row">
                        <label for="teamMember">Team Member:</label>
                        <select id="teamMember" name="member_id" required>
                            <option value="" <?php echo (isset($_SESSION["data"]["member_id"]) ? "" : "selected"); ?>>*Select Team Member*</option>
                            <?php
                            //filling the select input with members users as options
                            foreach ($users as $m) {
                                echo "<option value='" . ($m->getUser_id()) . "' " . ((isset($_SESSION["data"]["member_id"]) && ($m->getUser_id() == $_SESSION["data"]["member_id"])) ? "selected" : "") . ">" . ($m->getName()) . " - " . ($m->getUser_id()) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="input-row">
                        <label for="a_role">Role:</label>
                        <select id="a_role" name="role" required>
                            <option value="" <?php echo (isset($_SESSION["data"]["role"]) ? "" : "selected"); ?>>*Select*</option>
                            <option value="Developer" <?php echo ((isset($_SESSION["data"]["role"]) && "Developer" == $_SESSION["data"]["role"]) ? "selected" : ""); ?>>Developer</option>
                            <option value="Designer" <?php echo ((isset($_SESSION["data"]["role"]) && "Designer" == $_SESSION["data"]["role"]) ? "selected" : ""); ?>>Designer</option>
                            <option value="Tester" <?php echo ((isset($_SESSION["data"]["role"]) && "Tester" == $_SESSION["data"]["role"]) ? "selected" : ""); ?>>Tester</option>
                            <option value="Analyst" <?php echo ((isset($_SESSION["data"]["role"]) && "Analyst" == $_SESSION["data"]["role"]) ? "selected" : ""); ?>>Analyst</option>
                            <option value="Support" <?php echo ((isset($_SESSION["data"]["role"]) && "Support" == $_SESSION["data"]["role"]) ? "selected" : ""); ?>>Support</option>
                        </select>
                    </div>

                    <div class="input-row <?php echo isset($_SESSION["error"]["contribution"]) ? "wrong-input" : ""; ?>">
                        <label for="t_contribution">Effort:</label>
                        <input type="number" id="t_contribution" name="contribution" min="0" placeholder="Percentage Value"
                            value="<?php echo isset($_SESSION["data"]['contribution']) ? $_SESSION["data"]['contribution'] : "";
                                    ?>" required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["contribution"]) ? "show" : "";
                                                    ?>">Total contribution percentage is currently at <?php echo $_SESSION["error"]["contribution"]; ?>, pick a value that will cause it to sum up to 100% and not more.</span>
                    </div>
                </fieldset>

                <input type="hidden" value="<?php echo $task->getTaskId(); ?>" name="task_id">
                <input type="hidden" value="<?php echo $task->getStartDate(); ?>" name="taskStartDate">
                <input type="submit" class="clickable-button" value="Confirm Allocation">
            </form>
        </section>




    </main>
    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>