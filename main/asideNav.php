<?php
if (!isset($_SESSION["loggedin"])) {// not logged in
    header("Location:tapSys.php");
    exit;
}

if($_SESSION["type"] == "Manager"){

    echo "<nav><ul>
        <li><a class='nav-link".(isset($_SESSION['Dash'])?' nav-link_selected':'')."' href='Dashboard.php'>Dashboard</a></li>
        <li><a class='nav-link' href='addProject.php'>Add Project</a></li>
        <li><a class='nav-link' href='unassignedProjects.php'>Unassigned Project List</a></li></ul></nav>";

}else if($_SESSION["type"] == "Project Leader"){

    echo "<nav><ul>
        <li><a class='nav-link".(isset($_SESSION['Dash'])?' nav-link_selected':'')."' href='Dashboard.php'>Dashboard</a></li>
        <li><a class='nav-link' href='addTask.php'>Add Task</a></li>
        <li><a class='nav-link' href='taskDash.php'>Task Dashboard</a></li></ul></nav>";

}else{
    echo "<nav><ul>
        <li><a class='nav-link".(isset($_SESSION['Dash'])?' nav-link_selected':'')."' href='Dashboard.php'>Dashboard</a></li>
    <li><a class='nav-link' href='assignments.php'><strong><mark>Task Assignments</mark></strong></a></li>
    <li><a class='nav-link' href='taskProgress.php'>Task Progress</a></li></ul></nav>";

}


?>