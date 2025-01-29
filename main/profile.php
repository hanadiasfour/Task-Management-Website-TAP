<?php session_start();


//prevent non logged in users and non managers from accessing this page
if(!isset($_SESSION["loggedin"])){
    header("Location:tapSys.php");
    exit;
}

require_once "UserGateway.php";


$user_gw = new UserGateway();
$user = $user_gw->getUserByIUser_id($_SESSION["user_id"]);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>TAP | User Profile</title>
    <link rel="stylesheet" href="../style/styles.css">
</head>

<body class="logged_in_body">

    <aside>
    <?php include_once "asideNav.php"; ?> 
    </aside>

    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>User Profile</h1>
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
        <h1>User Details</h1>
    <form>
        <!-- Personal Information -->
        <fieldset>
            <legend>Personal Information</legend>
            
            <div class="input-row">
            <label for="user_id">User ID</label>
            <input type="text" id="user_id" value="<?php echo ($user->getUser_id()); ?>" readonly>
            </div>

            <div class="input-row">
            <label for="name">Name</label>
            <input type="text" id="name" value="<?php echo ($user->getName()); ?>" readonly>
            </div>

            <div class="input-row">
            <label for="dob">Date of Birth</label>
            <input type="text" id="dob" value="<?php echo ($user->getDob()); ?>" readonly>
            </div>
        </fieldset>

        <!-- Contact Information -->
        <fieldset>
            <legend>Contact Information</legend>
            <div class="input-row">
            <label for="email">Email</label>
            <input type="text" id="email" value="<?php echo ($user->getEmail()); ?>" readonly>
            </div>

            <div class="input-row">
            <label for="phone">Phone</label>
            <input type="text" id="phone" value="<?php echo ($user->getPhone()); ?>" readonly>
            </div>
        </fieldset>

        <!-- Address -->
        <fieldset>
            <legend>Address</legend>
            <div class="input-row">
            <label for="flat">Flat</label>
            <input type="text" id="flat" value="<?php echo ($user->getFlat()); ?>" readonly>
            </div>
            
            <div class="input-row">
            <label for="street">Street</label>
            <input type="text" id="street" value="<?php echo ($user->getStreet()); ?>" readonly>
            </div>
            
            <div class="input-row">
            <label for="city">City</label>
            <input type="text" id="city" value="<?php echo ($user->getCity()); ?>" readonly>
            </div>
            
            <div class="input-row">
            <label for="country">Country</label>
            <input type="text" id="country" value="<?php echo ($user->getCountry()); ?>" readonly>
            </div>
        </fieldset>

        <!-- Skills and Qualifications -->
        <fieldset>
            <legend>Skills and Qualifications</legend>
            <div class="input-row">
            <label for="p_description">Skills</label>
            <textarea type="text" id="p_description" rows="5" readonly> <?php echo ($user->getSkills()); ?></textarea>

            </div>

            <div class="input-row">
            <label for="qualification">Qualification</label>
            <input type="text" id="skills" value="<?php echo ($user->getQualification()); ?>" readonly>
            </div>
        </fieldset>

        <!-- Account Details -->
        <fieldset>
            <legend>Account Details</legend>
            <div class="input-row">
            <label for="role">Role</label>
            <input type="text" id="role" value="<?php echo ($user->getRole()); ?>" readonly>
            </div>

            <div class="input-row">
            <label for="username">Username</label>
            <input type="text" id="username" value="<?php echo ($user->getUsername()); ?>" readonly>
            </div>
            
        </fieldset>
    </form>
</section>

    </main>
    <footer><!--footer with policies and contact info-->
            <?php include_once "footer.html"; ?> 
    </footer>
</body>

</html>