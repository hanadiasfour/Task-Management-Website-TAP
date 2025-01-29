<?php session_start();


//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])||(isset($_SESSION["type"]) && $_SESSION["type"] != "Manager")){
    header("Location:tapSys.php");
    exit;
}

require_once "ProjectGateway.php";

//coming from form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $project_gw = new ProjectGateway();

    if (validateInputs($project_gw)) {

        //save project and go to confirmation
        
        //fill container with the file names
        $file_container = array();

        if(isset($_SESSION["data"]["file1"]))
        $file_container[] = $_SESSION["data"]["file1"];
        
        if(isset($_SESSION["data"]["file2"]))
        $file_container[] = $_SESSION["data"]["file2"];
        
        if(isset($_SESSION["data"]["file3"]))
        $file_container[] = $_SESSION["data"]["file3"];

        $fileNames = "NULL"; // assuming no files uploaded

        if (count($file_container)>0) { //construct string with names in csv form from array
            $fileNames = implode(',', $file_container);
        }

        $params = [
            ":project_id" => $_SESSION["data"]['project_id'],
            ":projectTitle" => $_SESSION["data"]['projectTitle'],
            ":projectDescription" => $_SESSION["data"]['projectDescription'],
            ":budget" => $_SESSION["data"]['budget'],
            ":startDate" => $_SESSION["data"]['startDate'],
            ":endDate" => $_SESSION["data"]['endDate'],
            ":clientName" => $_SESSION["data"]['clientName'],
            ":files" => $fileNames
        ];

        $project_gw->InsertProject($params); // add project to database

        //clean up resources used
        unset($_SESSION["data"]);
        unset($_SESSION["error"]);
        header("Location: alert.php?code=addition_successful");
        exit();

    } else {
        // relocate to same step to fix problems
        header("Location:" . $_SERVER['PHP_SELF']);
        exit();
    }
}

function validateInputs(&$project_gw)
{
    //reset errors array to check for any new errors
    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    if (!isset($_POST["project_id"]) || !preg_match("/^[A-Z]{4}-\d{5}$/", $_POST['project_id'])){
        $_SESSION["error"]["project_id"] = "1";
        
    }else { // id syntax is correct

        // making sure the id is unique to the database
        $data = $project_gw->getProjectByID($_POST["project_id"]);

        if (!$data) //unique
            $_SESSION["data"]["project_id"] = $_POST["project_id"];

        else //already used
            $_SESSION["error"]["project_id"] = "1";            
        
    }

    if (!isset($_POST["projectTitle"]) || $_POST['projectTitle'] == "")
        $_SESSION["error"]["projectTitle"] = "1";
    else
        $_SESSION["data"]['projectTitle'] = $_POST['projectTitle'];

    if (!isset($_POST["projectDescription"]) || $_POST['projectDescription'] == "")
        $_SESSION["error"]["projectDescription"] = "1";
    else
        $_SESSION["data"]['projectDescription'] = $_POST['projectDescription'];

    if (!isset($_POST["clientName"]) || $_POST['clientName'] == "")
        $_SESSION["error"]["clientName"] = "1";
    else
        $_SESSION["data"]['clientName'] = $_POST['clientName'];

    if (!isset($_POST["budget"]) || $_POST['budget'] == "" || !preg_match("/^\d+$/", $_POST['budget']))
        $_SESSION["error"]["budget"] = "1";
    else
        $_SESSION["data"]['budget'] = $_POST['budget'];


    if (!isset($_POST["startDate"]) || $_POST['startDate'] < date('Y-m-d'))
        $_SESSION["error"]["startDate"] = "1";
    else {

        $_SESSION["data"]['startDate'] = $_POST['startDate'];

        if (!isset($_POST["endDate"]) || $_POST['endDate'] < $_POST['startDate'])
            $_SESSION["error"]["endDate"] = "1";

        else
            $_SESSION["data"]['endDate'] = $_POST['endDate'];
    }

    // mime type of allowed files (REF: https://developer.mozilla.org/en-US/docs/Web/HTTP/MIME_types/Common_types)
    $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/png', 'image/jpeg'];
    $maxSize = 2 * 1024 * 1024; // equivalent to 2MB

    // process files that were uploaded
    if (isset($_FILES['files']) && isset($_POST['fileTitles'])) {
        $files = $_FILES['files'];
        $titles = $_POST['fileTitles'];
        $numFiles = count($files['name']);

        for ($i = 0; $i < $numFiles; $i++) {
            $errorKey = "file" . ($i + 1);

            if (!empty($files['name'][$i])) {
                $orgName = $files['name'][$i];
                $fileType = $files['type'][$i];
                $fileSize = $files['size'][$i];
                $tmpName = $files['tmp_name'][$i];
                $fileTitle = $titles[$i];

                $fileParts = explode('.', $orgName);
                // getting the last item by using the count of the array - 1 as index
                $fileExtension = strtolower($fileParts[count($fileParts) - 1]); // make it case insensitive

                // make sure file type is allowed and size is valid
                if (!in_array($fileType, $allowedTypes) || ($fileSize > $maxSize)) {
                    $_SESSION["error"][$errorKey] = "1";
                    continue;
                }

                // only change to new name if a new title was given, else we will use the original name
                if (!empty($fileTitle)) {
                    $newFileName =  $fileTitle . "." . $fileExtension;
                } else {
                    $newFileName =  $orgName;
                }

                // move uploaded file to dedicated folder (might be unsuccessful)
                if (move_uploaded_file($tmpName, ("../uploads/" . $newFileName))) {
                    $_SESSION["data"][$errorKey] = $newFileName; // successful upload 
                } else {
                    $_SESSION["error"][$errorKey] = "1"; //failed upload
                }
            }
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
    <title>TAP | Add New Project</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

    <aside>
        <nav>
            <ul>
                <li><a class='nav-link' href='Dashboard.php'>Dashboard</a></li>
                <li><a class="nav-link nav-link_selected" href="addProject.php">Add Project</a></li>
                <li><a class="nav-link" href="unassignedProjects.php">Unassigned Project List</a></li>
            </ul>
        </nav>
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Add New Project</h1>
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
                <li>Please fill in this form to add a new project:</li>
            </ul>
        </h3>

        <section class="form_input_container">

            <form action='<?php echo $_SERVER["PHP_SELF"] ?>' method="POST" enctype="multipart/form-data">

                <fieldset>
                    <legend>Project Details</legend>
                    <div class="input-row <?php echo isset($_SESSION["error"]["project_id"]) ? "wrong-input" : ""; ?>">
                        <label for="p_id">Project ID:</label>
                        <input type="text" id="p_id" name="project_id" placeholder="AAAA-55555" pattern="[A-Z]{4}-\d{5}"
                            title="Must be 4 uppercase alphabetic characters followed by a dash (-) and 5 digits." value="<?php echo isset($_SESSION["data"]['project_id']) ? $_SESSION["data"]['project_id'] : ""; ?>" required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["project_id"]) ? "show" : ""; ?>">Project ID must be unique and not empty.</span>

                    </div>

                    <div class="input-row <?php echo isset($_SESSION["error"]["projectTitle"]) ? "wrong-input" : ""; ?>">
                        <label for="p_title">Project Title:</label>
                        <input type="text" id="p_title" name="projectTitle" placeholder="Short Title" maxlength="30" value="<?php echo isset($_SESSION["data"]['projectTitle']) ? $_SESSION["data"]['projectTitle'] : ""; ?>"  required>
                    </div>

                    <div class="input-row">
                        <label for="p_description">Project Description:</label>
                        <textarea id="p_description" name="projectDescription" rows="10"
                            placeholder="Write your explanation of the project here..." required><?php echo isset($_SESSION["data"]['projectDescription']) ? $_SESSION["data"]['projectDescription'] : ""; ?></textarea>
                    </div>
                    <div class="input-row <?php echo isset($_SESSION["error"]["clientName"]) ? "wrong-input" : ""; ?>">
                        <label for="c_name">Client Name:</label>
                        <input type="text" id="c_name" name="clientName" placeholder="Client Full Name" value="<?php echo isset($_SESSION["data"]['clientName']) ? $_SESSION["data"]['clientName'] : ""; ?>"  required>
                    </div>

                    <div class="input-row <?php echo isset($_SESSION["error"]["budget"]) ? "wrong-input" : ""; ?>">
                        <label for="p_budget">Total Budget:</label>
                        <input type="number" id="p_budget" name="budget" min="0" placeholder="Budget in USD$" value="<?php echo isset($_SESSION["data"]['budget']) ? $_SESSION["data"]['budget'] : ""; ?>"  required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["budget"]) ? "show" : ""; ?>">Budget must be positive number and not empty.</span>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Project Timeline</legend>


                    <div class="input-row <?php echo isset($_SESSION["error"]["startDate"]) ? "wrong-input" : ""; ?>">
                        <label for="startDate">Start Date:</label>
                        <input type="date" id="startDate" name="startDate" min="<?php echo date("Y-m-d")?>" value="<?php echo isset($_SESSION["data"]['startDate']) ? $_SESSION["data"]['startDate'] : ""; ?>"  required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["startDate"]) ? "show" : ""; ?>">Start date must be in the present and not empty.</span>

                    </div>

                    <div class="input-row <?php echo isset($_SESSION["error"]["endDate"]) ? "wrong-input" : ""; ?>">
                        <label for="endDate">End Date:</label>
                        <input type="date" id="endDate" name="endDate" value="<?php echo isset($_SESSION["data"]['endDate']) ? $_SESSION["data"]['endDate'] : ""; ?>" required>
                        <span class="error-message <?php echo isset($_SESSION["error"]["endDate"]) ? "show" : ""; ?>">End date must be after start date and not empty.</span>
                    </div>
                </fieldset>



                <fieldset>
                    <legend>Project Documentation</legend>
                    <div class="input-row">
                        <label>Supporting Documents (Optional):</label>

                        <?php
                        $file1Exists = isset($_SESSION["data"]["file1"]);
                        $file2Exists = isset($_SESSION["data"]["file2"]);
                        $file3Exists = isset($_SESSION["data"]["file3"]);
                        $error1Exists = isset($_SESSION["error"]["file1"]);
                        $error2Exists = isset($_SESSION["error"]["file2"]);
                        $error3Exists = isset($_SESSION["error"]["file3"]);
                        ?>

                        <div>
                            <div class="file-row <?php echo $error1Exists ? "wrong-input" : ""; ?>">
                                <input type="file" name="files[]" <?php echo $file1Exists? "disabled": "" ?>>
                                <input type="text" name="fileTitles[]" accept=".pdf, .docx, .png, .jpg" placeholder="Title for File 1" <?php  echo $file1Exists? ("value='".$_SESSION["data"]["file1"]. "' disabled"): "" ?>>
                                <span class="error-message <?php echo $error1Exists ? "show" : ""; ?>">Allowed file types are PDF, DOCX, PNG, and JPG. Max file size is 2MB.</span>
                            </div>

                            <div class="file-row <?php echo isset($_SESSION["error"]["file2"]) ? "wrong-input" : ""; ?>">
                                <input type="file" name="files[]" <?php  echo $file2Exists? "disabled": "" ?>>
                                <input type="text" name="fileTitles[]" accept=".pdf, .docx, .png, .jpg" placeholder="Title for File 2" <?php  echo $file2Exists? ("value='".$_SESSION["data"]["file2"]. "' disabled"): "" ?>>
                                <span class="error-message <?php echo isset($_SESSION["error"]["file2"]) ? "show" : ""; ?>">Allowed file types are PDF, DOCX, PNG, and JPG. Max file size is 2MB.</span>
                            </div>

                            <div class="file-row <?php echo isset($_SESSION["error"]["file3"]) ? "wrong-input" : ""; ?>">
                                <input type="file" name="files[]" <?php  echo $file3Exists? "disabled": "" ?>>
                                <input type="text" name="fileTitles[]" accept=".pdf, .docx, .png, .jpg" placeholder="Title for File 3" <?php  echo $file3Exists? ("value='".$_SESSION["data"]["file3"]. "' disabled"): "" ?>>
                                <span class="error-message <?php echo isset($_SESSION["error"]["file3"]) ? "show" : ""; ?>">Allowed file types are PDF, DOCX, PNG, and JPG. Max file size is 2MB.</span>
                            </div>

                        </div>
                    </div>
                </fieldset>

                <input type="submit" value="Add Project" class="clickable-button"></input>

            </form>

        </section>




    </main>
    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>