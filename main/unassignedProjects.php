<?php session_start();


//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])||(isset($_SESSION["type"]) && $_SESSION["type"] != "Manager")){
    header("Location:tapSys.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | Project List</title>
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
        <h1>Unassigned Project List</h1>
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
                <li>Projects with no Team Leader:</li>
            </ul>
        </h3>

        <section class="form_input_container">

                    <table>
                        <!--table headers with data categories-->
                        <thead>
                            <tr><!--adding the scope makes the table more accessible-->
                                <th scope="col"><em>Project ID</em></th>
                                <th scope="col"><em>Project Title</em></th>
                                <th scope="col"><em>Start Date</em></th>
                                <th scope="col"><em>End Date</em></th>
                                <th scope="col"><em>Allocation Action</em></th>
                            </tr>
                        </thead>
    
                        <!--observations of data-->
                        <tbody>

                        <?php 
                        
                        require_once "ProjectGateway.php";// including the gateway to the project table

                        $project_gw = new ProjectGateway();//creating new gateway instance

                        // show pending for manager and all unassigned project

                                // get tickets with no assigned leader 
                                $unassignedProjects = $project_gw->getUnassignedProjects();

                                //checking if there is any returned data rows to display 
                                //(giving the user context instead of rendering an empty table)
                                if (count($unassignedProjects) == 0) {
                                    echo "<tr><td colspan='5'>No no tickets match the specified criteria.</td></tr>";
                                    
                                } else {
                                    //displaying table rows
                                    foreach($unassignedProjects as $project){
                                        echo $project->displayProjectRow();
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