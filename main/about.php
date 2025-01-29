<?php session_start();?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/styles.css">
    <link rel="icon" type="image/png" href="../images/logo_w.png">
    <title>About Us</title>
</head>

<body class="register-login-body">
    <?php if(!isset($_SESSION["loggedin"])):?>
    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>About Us</h1>
        <nav>
            <a class="styled-link" href="register.php">Register</a>
            <a class="styled-link" href="Login.php">Login</a>
        </nav>
    </header>
<?php else: ?>
    <header>
        <img src="../images/logo.png" alt="TAP Logo" title="TAP" width="80">
        <h1>About Us</h1>
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

    <?php endif;?>


    <!-- sides with logo -->
    <aside id="left">
        <div></div>
    </aside>

    <aside id="right">
        <div></div>
    </aside>


    <main>

    <section class="reg-container">

        <img src="../images/logo.png" alt="TAP logo" width="300">
        <h1>Welcome to TAP</h1>

        <p><strong>Task Allocator Pro (TAP)</strong> is a task management system designed to enhance task allocation and monitoring for small teams. It helps in assigning tasks efficiently, tracking progress, and reviewing completion status. Here's a look at the different functionalities within the system:</p>
    </section>

</main>

    <footer><!--footer with policies and contact info-->
        <?php include_once "footer.html"; ?>
    </footer>
</body>

</html>