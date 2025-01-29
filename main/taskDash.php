<?php session_start();

//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])||(isset($_SESSION["type"]) && $_SESSION["type"] != "Project Leader")){
    header("Location:tapSys.php");
    exit;
}

require_once "ProjectGateway.php";
require_once "TaskGateway.php";

$showTasks = false;//when first loading the page show the project select option only

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["project_id"]) && $_POST["project_id"]!=""){//coming from select project form
        $showTasks = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Tasks List</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

 
<aside>
        <nav>
            <ul>
                <li><a class='nav-link' href='Dashboard.php'>Dashboard</a></li>
                <li><a class="nav-link " href="addTask.php">Add Task</a></li>
                <li><a class="nav-link nav-link_selected" href="taskDash.php">Task Dashboard</a></li>
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
        <section class="form_input_container">
            <form method="POST" action='<?php echo $_SERVER["PHP_SELF"] ?>'>
                <div class="input-row">
                    <label for="project">Select a Project:</label>
                    <select name="project_id" id="project">
                        <option value="" <?php echo ($showTasks? "": "selected")?>>*Select*</option>

                            <!--FIX THIS DYNAMIC FUNCTIONALITY  -->
                            <?php 
                                $project_gw = new ProjectGateway();
                                $projects = $project_gw->getProjectByLeader($_SESSION["user_id"]);
                                
                                foreach ($projects as $p){
                                    echo "<option value='" . ($p->getProject_id()) . "' ".(($showTasks && ($p->getProject_id() == $_POST["project_id"]))?"selected":"") .">" . ($p->getTitle()) . "</option>";
                                } 
                            ?>
                    </select>
                </div>
                <input type="submit" value="View Associated Tasks" class="clickable-button">
            </form>
        </section>

        <?php if($showTasks):?>


        <h3>
            <ul>
                <li>Tasks associated with the selected project named <em><?php 
                
                $p = $project_gw->getProjectByID($_POST["project_id"]);
                
                echo $p->getTitle();
                    ?></em></li>
            </ul>
        </h3>

        <section class="form_input_container">
            <table>
                <!--table headers with data categories-->
                <thead>
                    <tr><!--adding the scope makes the table more accessible-->
                        <th scope="col"><em>Task ID</em></th>
                        <th scope="col"><em>Task Name</em></th>
                        <th scope="col"><em>Start Date</em></th>
                        <th scope="col"><em>Status</em></th>
                        <th scope="col"><em>Priority</em></th>
                        <th scope="col"><em>Team Allocation Action</em></th>
                    </tr>
                </thead>    
                <tbody>

                <?php 
                
                    $task_gw = new TaskGateway();//creating new gateway instance

                    // get tickets with no assigned leader the those that are assigned 
                    $unassignedTasks = $task_gw->getUnassignedTasks($_POST["project_id"]);
                    $assignedTasks = $task_gw->getAssignedTasks($_POST["project_id"]);


                    //checking if there is any returned data rows to display 
                    //(giving the user context instead of rendering an empty table)
                    if ((count($unassignedTasks) + count($assignedTasks))  == 0) {
                        echo "<tr><td colspan='6'>No Tasks yet allocated to this project.</td></tr>";
                        
                    } else {
                        //displaying table rows
                        foreach($unassignedTasks as $t){
                            echo $t->displayTaskRow();
                        }

                        //displaying table rows
                        foreach($assignedTasks as $t){
                            echo $t->displayTaskRow();
                        }
                    }

                ?>

                </tbody>
            </table>
        </section>

        <?php endif;?>

    </main>
    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>