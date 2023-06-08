<?php

session_start();

// Define a constant or variable to allow access to the config file
define('AppOasis_APP_CONFIG', true);

// Include the config file
$config = include 'config.php';

// Retrieve the encryption key
$encryptionKey = $config['encryption_key'];

//Database configuration variables
$servername = $config['servername'];

$username = $config['username'];

$password = $config['password'];

$database = $config['database'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Metadata -->
    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/styles/style.css">

    <link rel="stylesheet" href="assets/styles/media-queries.css">

    <!-- Document Title -->
    <title>AppOasis</title>

</head>

<body>

    <!-- Wrapper Container -->
    <div class="wrapper">

        <!-- Layout Container -->
        <div class="layout">

            <!-- Container -->
            <div class="container">

                <!-- Semi Transparent Layer -->
                <div class="cover" id="layer"></div>

                <!-- Header Section -->
                <header>

                    <!-- Logo Container -->
                    <div class="logo-container">

                        <img src="assets/icons/logo.png" alt="Logo">

                        <h1>AppOasis</h1>

                    </div>

                    <!-- Header Wrapper -->
                    <div class="header-wrapper">

                        <nav>

                            <ul>

                                <li>

                                    <img src="assets/icons/close.png" alt="Close" id="close-menu" />

                                </li>

                                <li><a href="home.php">Home</a></li>
                                <li><a href="courses.php">Courses</a></li>
                                <li><a href="profile.php">My Profile</a></li>
                                <li><a href="about.php">About</a></li>

                                <?php

                                if (isset($_SESSION['userEmail'])) {

                                    echo "<li id='my-account'><a href='account.php?action=log-out'>Log Out</a></li>";

                                } else {

                                    echo "<li id='my-account'><a href='account.php'>My Account</a></li>";

                                }

                                ?>

                            </ul>

                        </nav>

                        <?php

                        if (isset($_SESSION['userEmail'])) {

                            echo "<button class='btn' onclick=\"window.location.href='account.php?action=log-out'\">

        <div>Log Out</div>

    </button>";

                        } else {

                            echo "<button class='btn' onclick=\"window.location.href='account.php'\">

        <div>My Account</div>

    </button>";

                        }

                        ?>

                    </div>

                    <img src="assets/icons/menu.png" alt="Menu" id="menu-btn" />

                </header>

                <!-- Main Section -->
                <main>

                    <!-- Popup -->
                    <div class="popup-container" style="background-color: rgba(255,255,255,0.85); visibility: hidden"
                        id="popup-parent">

                        <div class="popup" style="opacity:0; top:100px;" id="popup">

                            <h2 id="popup-title">
                                </h1>

                                <p id="popup-description"></p>

                                <button class="btn" onclick="ClosePopup()">

                                    <div>Dismiss</div>

                                </button>

                        </div>

                    </div>

                    <!-- Overview Section -->
                    <section class="overview" style="background-color: transparent;">

                        <!-- Overview Details Section -->
                        <section class="overview-details">

                            <h2 style="color: #333;">Ayman Atif: Crafting AppOasis, Where Empowered Learners Find Their
                                Digital Learning Oasis.</h2>

                            <p style="color: #555;">In my role as Ayman Atif, a skilled web developer, I have
                                passionately envisioned and meticulously crafted AppOasis—an extraordinary digital
                                learning destination designed to empower learners worldwide.</p>

                            <button class="btn"
                                style="background-color: royalblue; outline-color: royalblue; color: white; padding: 15px 50px;"
                                onclick="document.getElementById('contact-container').scrollIntoView({ behavior: 'smooth' });">

                                <div style=" font-size: 1.2rem; font-weight: bold;">Contact Me</div>

                            </button>

                        </section>

                        <!-- Overview Image Section -->
                        <div class="overview-figure">

                            <div class="overview-figure-container" style="outline-color: royalblue;">

                                <img src="assets/images/me.png" alt="Portrait Image" style="height: initial;" />

                            </div>

                        </div>

                    </section>

                    <!-- Overview Section -->
                    <section class="overview" style="background-color: transparent;">

                        <!-- Overview Image Section -->
                        <div class="overview-figure">

                            <div class="overview-figure-container" style="outline-color: royalblue;">

                                <img src="assets/images/wallpaper3.PNG" alt="Portrait Image" style="height: 100%;" />

                            </div>

                        </div>

                        <!-- Overview Details Section -->
                        <section class="overview-details" id="contact-container">

                            <h2 style="color: #333;">Send me a Message.</h2>

                            <p style="color: #555;">Feel free to reach out to me if you need any assistance, have
                                questions, or require further information. I am here to help!</p>

                            <form method="POST" target="_self">

                                <input name="name-textbox" type="text" placeholder="Your Name" />

                                <input name="email-textbox" type="email" placeholder="Your Email" />

                                <textarea name="message-textbox" placeholder="Your Message"></textarea>

                                <button type="submit" name="btnSendMessage" class="btn"
                                    style="background-color: royalblue; outline-color: royalblue; color: white; padding: 15px 50px;">

                                    <div style=" font-size: 1.2rem; font-weight: bold;">Send Message</div>

                                </button>

                                <?php

                                if (isset($_POST['btnSendMessage'])) {

                                    $name = $_POST['name-textbox'];

                                    $email = $_POST['email-textbox'];

                                    $message = $_POST['message-textbox'];

                                    $conn2 = new mysqli($servername, $username, $password, $database);

                                    if ($conn2->connect_error) {

                                        die("Connection failed: " . $conn2->connect_error);

                                    }

                                    // Prepare the SQL statement
                                    $statement = $conn2->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");

                                    // Bind the parameters
                                    $statement->bind_param("sss", $name, $email, $message);

                                    // Set the parameter values
                                    $name = $_POST['name-textbox'];
                                    $email = $_POST['email-textbox'];
                                    $message = $_POST['message-textbox'];

                                    // Execute the statement
                                    $statement->execute();

                                    // Close the statement and database connection
                                    $statement->close();
                                    $conn2->close();

                                    echo "<script>window.onload=()=>{ ShowPopup('Success','Your message has been sent successfully!') };</script>";

                                }

                                ?>

                            </form>

                        </section>

                    </section>

                </main>

                <!-- Footer Section -->
                <footer>

                    <!-- Footer Description Section -->
                    <section class="footer-description">

                        <h2>AppOasis: an Online eCourses Platform to Learn Programming</h2>

                        <div class="vertical-line"></div>

                        <p>Online learning, like AppOasis, is important in today's world, offering flexibility,
                            accessibility, and the opportunity to acquire valuable skills from anywhere. It breaks down
                            barriers, promotes self-paced learning, and empowers individuals personal and
                            professional growth.</p>

                    </section>

                    <!-- Footer Navigation Section -->
                    <section class="footer-navigation">

                        <h2>Site Navigation</h2>

                        <nav>

                            <ul>

                                <li><a href="home.php">Home</a></li>
                                <li><a href="courses.php">Courses</a></li>
                                <li><a href="profile.php">My Profile</a></li>
                                <li><a href="about.php">About</a></li>

                                <?php

                                if (isset($_SESSION['userEmail'])) {

                                    echo "<li><a href='account.php?action=log-out'>Log Out</a></li>";

                                } else {

                                    echo "<li><a href='account.php'>My Account</a></li>";

                                }

                                ?>

                            </ul>

                        </nav>

                    </section>

                    <!-- Footer Subscription Section -->
                    <section class="footer-subscribe">

                        <form method="POST" target="_self">

                            <h2>Subscribe</h2>

                            <p>Subscribe to our newsletter for the latest news and exclusive content!</p>

                            <input type="email" placeholder="Email Address" name="subscription-email" />

                            <button class="btn" name="btn-subscribe">

                                <div>Subscribe Now</div>

                            </button>

                            <?php

                            if (isset($_POST['btn-subscribe'])) {

                                $conn2 = new mysqli($servername, $username, $password, $database);

                                if ($conn2->connect_error) {

                                    die("Connection failed: " . $conn2->connect_error);

                                }

                                if (isset($_POST['subscription-email']) && !empty(trim($_POST['subscription-email']))) {

                                    $sql = "insert into subscriptions(email) values ('" . $_POST['subscription-email'] . "')";

                                    if ($conn2->query($sql) === TRUE) {

                                        echo "<script>window.onload=()=>{ ShowPopup('Newsletter','You have been subscribed to newsletter!') };</script>";

                                    } else {

                                    }

                                }

                                $conn2->close();

                            }

                            ?>

                            <script>

                                //Basic Declarations
                                const popupParent = document.getElementById("popup-parent");

                                const popup = document.getElementById("popup");

                                function ShowPopup(title, description) {

                                    document.getElementById("popup-title").innerHTML = title;

                                    document.getElementById("popup-description").innerHTML = description;

                                    pageLayer.style.visibility = "visible";

                                    pageLayer.style.opacity = "1";

                                    popupParent.style.visibility = "visible";

                                    setTimeout(() => {

                                        popup.style.top = "0";

                                        popup.style.opacity = "1";

                                    }, 100);

                                }

                                function ClosePopup() {

                                    popup.style.top = "100px";

                                    popup.style.opacity = "0";

                                    setTimeout(() => {

                                        pageLayer.style.visibility = "hidden";

                                        pageLayer.style.opacity = "0";

                                        popupParent.style.visibility = "hidden";

                                    }, 250);

                                }

                            </script>

                        </form>

                    </section>


                </footer>

            </div>

        </div>

    </div>

    <!-- Javascript Files -->
    <script src="assets/scripts/main.js"></script>

</body>

</html>