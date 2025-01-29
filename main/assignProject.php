<?php session_start();

//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])||(isset($_SESSION["type"]) && $_SESSION["type"] != "Manager")){
    header("Location:tapSys.php");
    exit;
}


require_once "ProjectGateway.php";
require_once "UserGateway.php";

//initialize used gateways
$project_gw = new ProjectGateway();
$user_gw = new UserGateway();

//coming from dashboard get request link
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $project_id = $_GET["id"]; //extract project id

    $project = $project_gw->getProjectByID($project_id); // extract project object to display

    if (!$project) { // no project returned with this id (rise an error)
        header("Location: alert.php?code=undefined_project&id=$project_id");
        exit;
    }

    $teamLeader = $project->getTeamLeader(); //getting leader of this project

    //checking if the team leader was already assigned to this project, then rise an error that you can't reassign
    if ($teamLeader != null) {
        header("Location:alert.php?code=assigned_project&id=$project_id");
        exit;
    }
    // obtaining list of leaders from db using the gateway
    $users = $user_gw->getProjectLeaders();


    // post method when manager wants to assign the ticket to a staff member
} else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["leader_id"]) && isset($_POST["project_id"])) {

    // assign ticket by updating the ticket information in the data base
    $result = $project_gw->assignProject($_POST["project_id"], $_POST["leader_id"]);

    if ($result) { // update was successfully assigned to a staff, redirect to appropriate page
        header("Location:alert.php?code=assign_successful&p_id=" . $_POST['project_id'] . "&user_id=" . $_POST["leader_id"]);
        exit;
    } else { // redirect to error page when update not successful
        header("Location:alert.php?code=assign_unsuccessful&p_id=" . $_POST['project_id'] . "&user_id=" . $_POST["leader_id"]);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Assign Leader</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

    <aside>
        <nav>
            <ul>
                <li><a class='nav-link' href='Dashboard.php'>Dashboard</a></li>
                <li><a class="nav-link" href="addProject.php">Add Project</a></li>
                <li><a class="nav-link nav-link_selected" href="unassignedProjects.php">Unassigned Project List</a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Assign Leader</h1>
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
                <li>Assign a Team Leader to this Project:</li>
            </ul>
        </h3>

        <section class="form_input_container">

            <form method="POST" action='<?php echo $_SERVER["PHP_SELF"] ?>'>
                <fieldset>
                    <legend>Project Details</legend>
                    <div class="input-row">
                        <label for="projectID">Project ID:</label>
                        <input type="text" id="projectID" value="<?php echo $project->getProject_id(); ?>" disabled>
                    </div>
                    <div class="input-row">
                        <label for="projectTitle">Project Title:</label>
                        <input type="text" id="projectTitle" value="<?php echo $project->getTitle(); ?>" disabled>
                    </div>
                    <div class="input-row">
                        <label for="p_description">Project Description:</label>
                        <textarea type="text" id="p_description" rows="10" disabled><?php echo $project->getDescription(); ?></textarea>
                    </div>
                    <div class="input-row">
                        <label for="customerName">Client Name:</label>
                        <input type="text" id="customerName" value="<?php echo $project->getClient(); ?>" disabled>
                    </div>
                    <div class="input-row">
                        <label for="totalBudget">Total Budget:</label>
                        <input type="text" id="totalBudget" value="<?php echo $project->getBudget(); ?>" disabled>
                    </div>


                    </fieldset>

                    <fieldset>
                        <legend>Project Timeline</legend>
                    <div class="input-row">
                        <label for="startDate">Start Date:</label>
                        <input type="date" id="startDate" value="<?php echo $project->getStartDate(); ?>" disabled>
                    </div>
                    <div class="input-row">
                        <label for="endDate">End Date:</label>
                        <input type="date" id="endDate" value="<?php echo $project->getEndDate(); ?>" disabled>
                    </div>
                </fieldset>

                <!-- Team Leader Selection Section -->
                <fieldset>
                    <legend>Select Team Leader</legend>
                    <div class="input-row">
                        <label for="teamLeader">Team Leader:</label>
                        <select id="teamLeader" name="leader_id" required>
                            <option value="">*Select Team Leader*</option>
                            <?php
                            //filling the select input with leader users as options
                            foreach ($users as $leader) {
                                echo "<option value='" . ($leader->getUser_id()) . "'>" . ($leader->getName()) . " - " . ($leader->getUser_id()) . "</option>";
                            }
                            ?>

                        </select>
                    </div>
                </fieldset>

                <input type="hidden" name="project_id" value="<?php echo $project->getProject_id(); ?>">
                <input type="submit" class="clickable-button" value="Confirm Allocation">
            </form>


            <!-- Supporting Documents Section -->
            <div class="documents">
                <h3>Supporting Documents</h3>

                <?php
                echo $project->displayFileList();
                
                ?>
            </div>
        </section>




    </main>
    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>