<?php session_start();

//prevent non logged in users and non managers from accessing this page
if (!isset($_SESSION["loggedin"])) {
    header("Location:tapSys.php");
    exit;
}

require_once "TaskGateway.php";

$task_gw = new TaskGateway(); //creating new gateway instances

//coming from dashboard get request link
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $task_id = $_GET["id"]; //extract task id

    $task = $task_gw->getTaskByID($task_id); // extract task object to display

    if (!$task) { // no task returned with this id (rise an error)
        header("Location: alert.php?code=undefined_task&id=$task_id");
        exit();
    }

    // obtaining list of members from db using the gateway
    $allocations = $task_gw->getAllocation($task_id);


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
    <title>TAP | View Task</title>
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

    <main class="details_container">


    <article class="task-details">
        <h1>Task Details</h1>
        <dl>
            <dt>Task ID:</dt>
            <dd><?php echo $task->getTaskId();?></dd>
            
            <dt>Task Name:</dt>
            <dd><?php echo $task->getName();?></dd>
            
            <dt>Description:</dt>
            <dd><?php echo $task->getDescription();?></dd>
            
            <dt>Project:</dt>
            <dd><?php echo $task_gw->getProjectNameByTaskID($task->getTaskID()); ?></dd>
            
            <dt>Start Date:</dt>
            <dd><?php echo $task->getStartDate();?></dd>
            
            <dt>End Date:</dt>
            <dd><?php echo $task->getEndDate();?></dd>
            
            <dt>Completion Percentage:</dt>
            <dd><?php echo ($task->getProgress() ?? 0);?></dd>
            
            <dt>Status:</dt>
            <dd><?php echo $task->getStatus();?></dd>
            
            <dt>Priority:</dt>
            <dd><?php echo $task->getPriority();?></dd>
        </dl>

    </article>

    <article>
        <h2>Team Members</h2>
        <table>
            <thead>
                <tr><!--adding the scope makes the table more accessible-->
                    <th scope="col"><em>Photo</em></th>
                    <th scope="col"><em>Member ID</em></th>
                    <th scope="col"><em>Name</em></th>
                    <th scope="col"><em>Start Date</em></th>
                    <th scope="col"><em>End Date</em></th>
                    <th scope="col"><em>Effort Allocated (%)</em></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (count($allocations) == 0) {
                            echo "<tr><td colspan='6'>No Member assigned to this task yet.</td></tr>";

                        } else {
                            //displaying table rows
                            foreach ($allocations as $a) {
                                echo $a->displayRow();
                            }
                        }
                        ?>
            </tbody>
        </table>
    </article>


</main>
    <footer><!--footer with policies and contact info-->
        <?php include_once "footer.html"; ?>
    </footer>
</body>

</html>
<? unset($_SESSION["Dash"]); ?>