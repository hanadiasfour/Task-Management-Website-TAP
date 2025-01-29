<?php
session_start();

require_once "UserGateway.php";

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]) {
    header("Location:tapSys.php");
    exit;
}


// $fillUsingSession = false; // true=> to keep form data from getting erased incase of an invalid input, false=> no need for filling in any data
$step = isset($_SESSION['step']) ? $_SESSION['step'] : "1"; // take session saved step, otherwise initialize to first step

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // when 0 then first time so we go too first step no verification
    // when 1 we verify that the inputted data in the session is valid and go to step 2, when not valid then we must return to step 1 in a get request (wont com ein this switch case) and put up the corresponding step in session (which should be 1 in this case)
    // but what if we type in the url for the first time and there is no type in the session, then we give it the first step
    // when getting to step 2 we would have came from a get request with step = 1 meaning we will show step 2
    // after submitting step two using post, the switch case will be entered and with step number 2 so we can validate inputs correlated to that step only
    // if validated, then we go using get to step 3, otherwise we will have to go to step 2 again using get (both cases have type as 2)
    // so come with post ==> check step we need to validate for ==> validate for that step (correct==> change step number in session to go to next step using get, incorrect==> get request with same step and add a problem session values)
    // come with get ==> check type ==> (if problem ==> is filled with something then we need to fill input that are valid and mark the ones that are not, if no problem ==> show form of step in the session)  


    // step represents what step we need to validate inputs for
    if (validateInputs($_POST["step"])) {

        //save data to session
        // saveData($_POST["step"]); // maybe you can validate uniqueness here

        if ($_POST['step'] == "3") {
            $_SESSION['step'] = "4"; //4th step is the confirmation page
            $step = "4";
        } else {
            // go to next step 1=>2, 2=>3, 3 never reaches here
            $_SESSION['step'] = ($_POST['step'] == "1") ? "2" : "3";

            // relocate to next step
            header("Location:" . $_SERVER['PHP_SELF']);
            exit();
        }
    } else { // don't change step number and send get request to that step
        // relocate to same step to fix problems
        header("Location:" . $_SERVER['PHP_SELF']);
        exit();
    }
}

function validateInputs($step,$user_gw = new UserGateway())
{

    //reset errors array to check for any new errors
    if (isset($_SESSION["error"])) {
        unset($_SESSION["error"]);
    }

    switch ($step) {
        case "1":


            if (!isset($_POST["id"]) || !preg_match('/^\d{8}$/', $_POST['id']))
                $_SESSION["error"]["id"] = "1";

            else { // id syntax is correct
        
                // making sure the id is unique to the database
                $data = $user_gw->getUserById($_POST["id"]);
        
                if (!$data) //unique
                    $_SESSION["id"] = $_POST["id"];
        
                else //already used
                    $_SESSION["error"]["id"] = "1";            
                
            }
                

            if (!isset($_POST["name"]) || $_POST['name'] == "")
                $_SESSION["error"]["name"] = "1";
            else
                $_SESSION['name'] = $_POST['name'];

            if (!isset($_POST["dob"]) || $_POST['dob'] == "" ||  $_POST['dob'] >= date('Y-m-d'))
                $_SESSION["error"]["dob"] = "1";
            else
                $_SESSION['dob'] = $_POST['dob'];


            if (!isset($_POST["email"]) || $_POST['email'] == "" || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
                $_SESSION["error"]["email"] = "1";

            else{ // email syntax is correct
        
                // making sure the email is unique to the database
                $data = $user_gw->getUserByEmail($_POST["email"]);
        
                if (!$data) //unique
                $_SESSION['email'] = $_POST['email'];
        
                else //already used
                    $_SESSION["error"]["email"] = "1";            
            }


            if (!isset($_POST["phone"]) || !preg_match('/^\d{10}$/', $_POST['phone']))
                $_SESSION["error"]["phone"] = "1";
            else
                $_SESSION['phone'] = $_POST['phone'];

            if (!isset($_POST["flat"]) || $_POST['flat'] == "")
                $_SESSION["error"]["flat"] = "1";
            else
                $_SESSION['flat'] = $_POST['flat'];

            if (!isset($_POST["street"]) || $_POST['street'] == "")
                $_SESSION["error"]["street"] = "1";
            else
                $_SESSION['street'] = $_POST['street'];

            if (!isset($_POST["city"]) || $_POST['city'] == "")
                $_SESSION["error"]["city"] = "1";
            else
                $_SESSION['city'] = $_POST['city'];

            if (!isset($_POST["country"]) || $_POST['country'] == "")
                $_SESSION["error"]["country"] = "1";
            else
                $_SESSION['country'] = $_POST['country'];

            if (isset($_POST["skills"]) && $_POST['skills'] != "")
                $_SESSION['skills'] = $_POST['skills'];

            if (isset($_POST["qualification"]) && $_POST['qualification'] != "")
                $_SESSION['qualification'] = $_POST['qualification'];

            if (isset($_POST["role"]) && $_POST['role'] != "")
                $_SESSION['role'] = $_POST['role'];

            break;

        case "2":

            if (!isset($_POST["username"]) || $_POST['username'] == "" || strlen($_POST["username"]) < 6 || strlen($_POST["username"]) > 13 || !preg_match("/^[A-Za-z0-9]*$/", $_POST['username']))
                $_SESSION["error"]["username"] = "1";

            else{ // username syntax is correct
        
                // making sure the username is unique to the database
                $data = $user_gw->getUserByUsername($_POST["username"]);
        
                if (!$data) //unique
                $_SESSION["username"] = $_POST["username"];
        
                else //already used
                    $_SESSION["error"]["username"] = "1";            
            }

            if (!isset($_POST["password"]) || $_POST['password'] == "" || strlen($_POST["password"]) < 8 || strlen($_POST["password"]) > 12 || !preg_match("/(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]*$/", $_POST['password']))
                $_SESSION["error"]["password"] = "1";
            else {
                if (!isset($_POST["confirm"]) || $_POST['confirm'] != $_POST['password'])
                    $_SESSION["error"]["confirm"] = "1";
                else
                    $_SESSION["password"] = $_POST["password"];
            }

            break;

        case "3": // generate unique user id ave user to database


            $skills = implode(',', $_SESSION['skills']);

            $params = [
                ":id" => $_SESSION['id'],
                ":name" => $_SESSION['name'],
                ":dob" => $_SESSION['dob'],
                ":email" => $_SESSION['email'],
                ":phone" => $_SESSION['phone'],
                ":flat" => $_SESSION['flat'],
                ":street" => $_SESSION['street'],
                ":city" => $_SESSION['city'],
                ":country" => $_SESSION['country'],
                ":skills" => $skills,
                ":qualification" => $_SESSION['qualification'],
                ":role" => $_SESSION['role'],
                ":username" => $_SESSION['username'],
                ":password" => $_SESSION['password']
            ];

            $_SESSION["user_id"] = $user_gw->insertUser($params);

            break;
    }


    if (isset($_SESSION["error"]))
        return false; //detected invalid input

    $_SESSION['step'] = $_POST['step']; // store new step after all is validated and good
    return true; // no invalid inputs
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>Register | Step <?php echo $step; ?></title>
</head>

<body class="register-login-body">
    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>Registration</h1>
        <nav>
            <a class="styled-link" href="register.php">Register</a>
            <a class="styled-link" href="Login.php">Login</a>
        </nav>
    </header>


    <!-- sides with logo -->
    <aside id="left">
        <div></div>
    </aside>

    <aside id="right">
        <div></div>
    </aside>

    <main>

        <?php if ($step == "1"): ?>
            <section class="reg-container">

                <img src="../images/newUser.png" alt="new User">
                <h2>Step-1: User Information</h2>
                <p>Welcome! Let's get to know you better. Please fill out your basic details so we can personalize your
                    experience.</p>

                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <fieldset>
                        <legend>Personal Information</legend>

                        <div class="input-row <?php echo isset($_SESSION["error"]["id"]) ? "wrong-input" : ""; ?>">
                            <label for="id-input">ID Number:</label>
                            <input id="id-input" type="text" name="id" placeholder="8-digit ID" value="<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : ""; ?>" required>
                            <span class="error-message <?php echo isset($_SESSION["error"]["id"]) ? "show" : ""; ?>">ID must be unique 8 digit.</span>
                        </div>
                        <div class="input-row <?php echo isset($_SESSION["error"]["name"]) ? "wrong-input" : ""; ?>">
                            <label for="name-input">Full Name:</label>
                            <input id="name-input" type="text" name="name" placeholder="Enter Full Name" value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ""; ?>" required>
                            <span class="error-message <?php echo isset($_SESSION["error"]["name"]) ? "show" : ""; ?>">Name must not empty.</span>
                        </div>

                        <!--TODO: Handle date validation in php-->
                        <div class="input-row <?php echo isset($_SESSION["error"]["dob"]) ? "wrong-input" : ""; ?>">
                            <label for="dob-input">Date of Birth:</label>
                            <input id="dob-input" type="date" name="dob" max="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($_SESSION['dob']) ? $_SESSION['dob'] : ""; ?>" required>
                            <span class="error-message <?php echo isset($_SESSION["error"]["dob"]) ? "show" : ""; ?>">Date of Birth must be in the past and not empty.</span>
                        </div>

                    </fieldset>

                    <fieldset>
                        <legend>Contact Information</legend>

                        <div class="input-row <?php echo isset($_SESSION["error"]["email"]) ? "wrong-input" : ""; ?>">
                            <label for="email-input">E-mail Address:</label>
                            <input id="email-input" type="email" name="email" placeholder="aaa@bbb.ccc" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ""; ?>" required>
                            <span class="error-message <?php echo isset($_SESSION["error"]["email"]) ? "show" : ""; ?>">Email must be unique in form aaa@bbb.ccc.</span>
                        </div>

                        <div class="input-row <?php echo isset($_SESSION["error"]["phone"]) ? "wrong-input" : ""; ?>">
                            <label for="phone-input">Telephone:</label>
                            <input id="phone-input" type="text" name="phone" placeholder="0500 300 200" value="<?php echo isset($_SESSION['phone']) ? $_SESSION['phone'] : ""; ?>" required>
                            <span class="error-message <?php echo isset($_SESSION["error"]["phone"]) ? "show" : ""; ?>">Phone must be 10-digit non empty.</span>
                        </div>

                        <div class="address_div input-row">
                            <span>
                                <label>Address:</label>
                            </span>

                            <span>
                                <div class="input-row <?php echo isset($_SESSION["error"]["flat"]) ? "wrong-input" : ""; ?>">
                                    <label for="flat-input">Flat/House No:</label>
                                    <input id="flat-input" type="text" name="flat" placeholder="12A" value="<?php echo isset($_SESSION['flat']) ? $_SESSION['flat'] : ""; ?>" required>
                                    <span class="error-message <?php echo isset($_SESSION["error"]["flat"]) ? "show" : ""; ?>">Flat must not empty.</span>
                                </div>

                                <div class="input-row <?php echo isset($_SESSION["error"]["street"]) ? "wrong-input" : ""; ?>">
                                    <label for="street-input">Street:</label>
                                    <input id="street-input" type="text" name="street" value="<?php echo isset($_SESSION['street']) ? $_SESSION['street'] : ""; ?>" placeholder="Al-Quds Street"
                                        required>
                                    <span class="error-message <?php echo isset($_SESSION["error"]["street"]) ? "show" : ""; ?>">Street must not empty.</span>
                                </div>

                                <div class="input-row <?php echo isset($_SESSION["error"]["city"]) ? "wrong-input" : ""; ?>">
                                    <label for="city-input">City:</label>
                                    <input id="city-input" type="text" name="city" placeholder="Ramallah" value="<?php echo isset($_SESSION['city']) ? $_SESSION['city'] : ""; ?>" required>
                                    <span class="error-message <?php echo isset($_SESSION["error"]["city"]) ? "show" : ""; ?>">City must not empty.</span>
                                </div>

                                <div class="input-row <?php echo isset($_SESSION["error"]["country"]) ? "wrong-input" : ""; ?>">
                                    <label for="country-input">Country:</label>
                                    <input id="country-input" type="text" name="country" placeholder="Palestine" value="<?php echo isset($_SESSION['country']) ? $_SESSION['country'] : ""; ?>" required>
                                    <span class="error-message <?php echo isset($_SESSION["error"]["country"]) ? "show" : ""; ?>">Country must not empty.</span>
                                </div>
                            </span>
                        </div>
                    </fieldset>


                    <fieldset>

                        <legend>Specifications</legend>

                        <div class="input-row">
                            <label for="skill-input">Skills:</label>
                            <select id="skill-input" name="skills[]" multiple required>

                                <option value="Front-end Development">Front-end Development</option>
                                <option value="Back-end Development">Back-end Development</option>
                                <option value="Full-Stack Development">Full-Stack Development</option>
                                <option value="Database Management">Database Management</option>
                                <option value="DevOps">DevOps</option>
                                <option value="Cybersecurity">Cybersecurity</option>
                                <option value="Cloud Computing">Cloud Computing</option>
                                <option value="Machine Learning">Machine Learning</option>
                                <option value="Mobile App Development">Mobile App Development</option>
                                <option value="Game Development">Game Development</option>

                            </select>
                        </div>

                        <div class="input-row">
                            <label for="qualification-input">Qualification:</label>
                            <select id="qualification-input" name="qualification" required>

                                <option value="" selected>Select a Qualification</option>
                                <option value="High School">High School</option>
                                <option value="Bachelor's Degree">Bachelor's Degree</option>
                                <option value="Master's Degree">Master's Degree</option>
                                <option value="PhD">PhD</option>

                            </select>
                        </div>



                        <div class="input-row">
                            <label for="role-input">Role:</label>
                            <select id="role-input" name="role" required>

                                <option value="" selected>Select a Role</option>
                                <option value="Manager">Manager</option>
                                <option value="Project Leader">Project Leader</option>
                                <option value="Team Member">Team Member</option>

                            </select>
                        </div>


                    </fieldset>
                    <!-- Hidden step count -->
                    <input type="hidden" name="step" value="1">
                    <input type="submit" value="Proceed" class="clickable-button">

                </form>



            </section>



        <?php elseif ($step == "2"): ?>
            <section class="reg-container">
                <img src="../images/newUser.png" alt="new User">
                <h2>Step-2: Account Creation</h2>
                <p>You're doing great! Now, let’s secure your account. Create a username and password to continue.</p>

                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <fieldset>
                        <legend>Account Information</legend>

                        <div class="input-row <?php echo isset($_SESSION["error"]["username"]) ? "wrong-input" : ""; ?>">
                            <label for="username-input">Username:</label>
                            <input id="username-input" type="text" name="username" placeholder="Your Username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ""; ?>" required>
                            <span class="error-message <?php echo isset($_SESSION["error"]["username"]) ? "show" : ""; ?>">Username must be between 6-13 alphanumeric characters</span>
                        </div>
                        <div class="input-row <?php echo isset($_SESSION["error"]["password"]) ? "wrong-input" : ""; ?>">
                            <label for="password-input">Password:</label>
                            <input id="password-input" type="password" name="password" placeholder="Your Password" required>
                            <span class="error-message <?php echo isset($_SESSION["error"]["password"]) ? "show" : ""; ?>">Password must be between 8–12 characters. Must include letters and numbers.</span>
                        </div>

                        <!--TODO: Handle date validation in php-->
                        <div class="input-row <?php echo isset($_SESSION["error"]["confirm"]) ? "wrong-input" : ""; ?>">
                            <label for="confirm-input">Confirm Password:</label>
                            <input id="confirm-input" type="password" name="confirm" placeholder="Repeat Password" required>
                            <span class="error-message <?php echo isset($_SESSION["error"]["confirm"]) ? "show" : ""; ?>">Must match entered password.</span>
                        </div>

                    </fieldset>

                    <!-- Hidden step count -->
                    <input type="hidden" name="step" value="2">
                    <input type="submit" value="Proceed to Confirmation" class="clickable-button">
                </form>
            </section>


        <?php elseif ($step == "3"): ?>
            <section class="reg-container">
                <img src="../images/newUser.png" alt="new User">
                <h2>Step-3: Review and Confirm Your Information</h2>
                <p>We’re almost there! Take a moment to review the details you’ve entered and confirm everything looks
                    good.</p>

                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                    <fieldset>
                        <legend>Personal Information</legend>

                        <div class="input-row">
                            <label for="id-review">ID Number:</label>
                            <input id="id-review" name="id" type="text" value="<?php echo $_SESSION['id']; ?>" readonly>
                        </div>
                        <div class="input-row">
                            <label for="name-review">Full Name:</label>
                            <input id="name-review" name="name" type="text" value="<?php echo $_SESSION['name']; ?>" readonly>
                        </div>
                        <div class="input-row">
                            <label for="dob-review">Date of Birth:</label>
                            <input id="dob-review" name="dob" type="text" value="<?php echo $_SESSION['dob']; ?>" readonly>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Contact Information</legend>

                        <div class="input-row">
                            <label for="email-review">E-mail Address:</label>
                            <input id="email-review" name="email" type="email" value="<?php echo $_SESSION['email']; ?>" readonly>
                        </div>
                        <div class="input-row">
                            <label for="phone-review">Telephone:</label>
                            <input id="phone-review" name="phone" type="tel" value="<?php echo $_SESSION['phone']; ?>" readonly>
                        </div>
                        <div class="address_div input-row">
                            <span>
                                <label>Address:</label>
                            </span>

                            <span>
                                <div class="input-row">
                                    <label for="flat-input">Flat/House No:</label>
                                    <input id="flat-input" type="text" name="flat" value="<?php echo $_SESSION['flat']; ?>" readonly>
                                </div>

                                <div class="input-row">
                                    <label for="street-input">Street:</label>
                                    <input id="street-input" type="text" name="street" value="<?php echo $_SESSION['street']; ?>" readonly>
                                </div>

                                <div class="input-row">
                                    <label for="city-input">City:</label>
                                    <input id="city-input" type="text" name="city" value="<?php echo $_SESSION['city']; ?>" readonly>
                                </div>

                                <div class="input-row">
                                    <label for="country-input">Street:</label>
                                    <input id="country-input" type="text" name="country" value="<?php echo $_SESSION['country']; ?>" readonly>
                                </div>
                            </span>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Specifications</legend>

                        <div class="input-row">
                            <label for="skills-review">Skills:</label>
                            <textarea id="skills-review" rows="3" cols="100"
                                readonly><?php
                                            $skills = implode(',', $_SESSION['skills']);
                                            echo $skills; ?></textarea>
                        </div>
                        <div class="input-row">
                            <label for="qualification-review">Qualification:</label>
                            <input id="qualification-review" type="text" value="<?php echo $_SESSION['qualification']; ?>" readonly>
                        </div>
                        <div class="input-row">
                            <label for="role-review">Role:</label>
                            <input id="role-review" type="text" value="<?php echo $_SESSION['role']; ?>" readonly>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Account Information</legend>

                        <div class="input-row">
                            <label for="username-review">Username:</label>
                            <input id="username-review" type="text" value="<?php echo $_SESSION['username']; ?>" readonly>
                        </div>

                        <!-- Hidden password input -->
                        <input type="hidden" name="password" value="<?php echo $_SESSION['password']; ?>">
                    </fieldset>


                    <input type="hidden" name="step" value="3">
                    <input type="submit" value="Confirm" class="clickable-button">
                </form>

            </section>

        <?php else: ?>

            <section class="reg-container">
                <img src="../images/welcome.png" alt="Welcome User" width="200">
                <h2 id="fancy_text">Welcome To TAP</h2>
                <h2>Your Registration Was Successful</h2>
                <p>The User ID <?php echo $_SESSION['user_id']; ?> is Uniquely for You. Isn't that special? :D</p>
                <p>Thank you for becoming a part of the TAP family, let’s get you logged into your new account.</p>
                <a href="login.php"><input class="clickable-button" type="button" value="Login"></a>

            </section>

        <?php
            // unset information in the session for security 
            session_unset();
            session_destroy();

        endif;
        ?>

    </main>
    <footer><!--footer with policies and contact info-->
        <?php include_once "footer.html"; ?>
    </footer>

</body>

</html>