<?php

session_start();

if (isset($_SESSION['userEmail'])) {

    if (isset($_GET['action'])) {

        session_destroy();

        header("Location: home.php");

        exit;

    } else {

        echo "<script>window.location.href='profile.php'</script>";

    }

    exit;

}

// Define a constant or variable to allow access to the config file
define('AppOasis_APP_CONFIG', true);

// Include the config file
$config = include 'config.php';

// Retrieve the encryption key
$encryptionKey = $config['encryption_key'];

function encryptString($string, $encryptionKey)
{

    $ivLength = openssl_cipher_iv_length('AES-256-CBC');

    $iv = openssl_random_pseudo_bytes($ivLength);

    $encryptedString = openssl_encrypt(
        $string,
        'AES-256-CBC',
        $encryptionKey,
        OPENSSL_RAW_DATA,
        $iv
    );

    $encryptedString = base64_encode($iv . $encryptedString);

    return $encryptedString;

}

function decryptString($encryptedString, $encryptionKey)
{

    $encryptedString = base64_decode($encryptedString);

    $ivLength = openssl_cipher_iv_length('AES-256-CBC');

    $iv = substr($encryptedString, 0, $ivLength);

    $encryptedString = substr($encryptedString, $ivLength);

    $decryptedString = openssl_decrypt(
        $encryptedString,
        'AES-256-CBC',
        $encryptionKey,
        OPENSSL_RAW_DATA,
        $iv
    );

    return $decryptedString;

}

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

<body style="opacity:0">

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

                                <li id="my-account"><a href="account.php">My Account</a></li>

                            </ul>

                        </nav>

                        <button class="btn" onclick="window.location.href='account.php'">

                            <div>My Account</div>

                        </button>

                    </div>

                    <img src="assets/icons/menu.png" alt="Menu" id="menu-btn" />

                </header>

                <!-- Main Section -->
                <main>

                    <!-- Title Section -->
                    <section class="title-section">

                        <h2>My Account</h2>

                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                            laboris nisi ut aliquip ex ea commodo consequat. </p>

                    </section>

                    <!-- Categories Section -->
                    <section class="navigation-section" style="justify-content: center">

                        <ul>

                            <li><button onclick="ShowFieldContainer('login-container', 'signup-container');">Log
                                    In</button></li>
                            <li><button onclick="ShowFieldContainer('signup-container', 'login-container');">Create new
                                    Account</button></li>

                        </ul>

                    </section>

                    <!-- Account Container -->
                    <section class="account-container">

                        <!-- Popup -->
                        <div class="popup-container"
                            style="background-color: rgba(255,255,255,0.85); visibility: hidden" id="popup-parent">

                            <div class="popup" style="opacity:0; top:100px;" id="popup">

                                <h2 id="popup-title">
                                    </h1>

                                    <p id="popup-description"></p>

                                    <button class="btn" onclick="ClosePopup()">

                                        <div>Dismiss</div>

                                    </button>

                            </div>

                        </div>

                        <!-- Login Container -->
                        <section class="field-container" id="login-container">

                            <form method="POST" target="_self">

                                <header>

                                    <div>

                                        <img src="assets/icons/logo.png" alt="Icon" />

                                        <h2>Login</h2>

                                    </div>

                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                        incididunt.</p>

                                </header>

                                <fieldset>

                                    <input name="login-email" type="email" placeholder="Email Address" required />

                                    <input name="login-password" type="password" placeholder="Password" />

                                    <button name="forgotPasswordBtn" type="submit" class="forgot-password"
                                        id="forgot-password-btn">Forgot
                                        Password?</button>

                                    <button class="btn" name="login-btn">

                                        <div>Log In</div>

                                    </button>

                                </fieldset>

                            </form>

                        </section>

                        <!-- Sign Up Container -->
                        <section class="field-container" id="signup-container" style="display: none;">

                            <form method="POST" target="_self">

                                <header>

                                    <div>

                                        <img src="assets/icons/logo.png" alt="Icon" />

                                        <h2>Sign Up</h2>

                                    </div>

                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                        incididunt.</p>

                                </header>

                                <fieldset>

                                    <input name="signup-fullname" type="text" placeholder="Full Name" required />

                                    <input name="signup-email" type="email" placeholder="Email Address" required />

                                    <input name="signup-password" type="password" placeholder="Password" required />

                                    <button class="btn" style="margin-top: 20px;" name="signup-btn">

                                        <div>Create Account</div>

                                    </button>

                                </fieldset>

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

                                <li><a href="account.php">My Account</a></li>

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

    <script>

        window.onload = () => {

            setTimeout(() => {

                ShowFieldContainer('login-container', 'signup-container');

            }, 100);

        };

    </script>

    <?php

    function Signup($name, $email, $pass)
    {

        if (empty(trim($email)) || empty(trim($pass)) || empty(trim($name))) {

            echo "<script>document.body.style.opacity='1'; ShowPopup('Empty Fields','You must fill in all the fields!');window.onload=()=>{ ShowFieldContainer('signup-container', 'login-container'); };</script>";

            return;

        }

        if (strlen($pass) < 6) {

            echo "<script>document.body.style.opacity='1'; ShowPopup('Invalid Password','The password must contain at least 6 characters!');window.onload=()=>{ ShowFieldContainer('signup-container', 'login-container'); };</script>";

            return;

        }

        global $servername, $username, $password, $database, $encryptionKey;

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {

            die("Connection failed: " . $conn->connect_error);

        }

        $sql = "SELECT * FROM users  where lower(trim(email)) = '" . $email . "'";

        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        if ($result->num_rows > 0) {

            echo "<script>document.body.style.opacity='1'; ShowPopup('Invalid Email','The email address you provided is already in use!');window.onload=()=>{ ShowFieldContainer('signup-container', 'login-container'); };</script>";

            return;

        } else {

            $sql = "INSERT INTO `users`( `email`, `password`, `role`, `name`) VALUES ('" . $email . "','" . $pass . "','user','" . $name . "')";

            if ($conn->query($sql) === TRUE) {

                $userEmail = encryptString($email, $encryptionKey);

                $_SESSION['userEmail'] = $userEmail;

                if (isset($_GET['next'])) {

                    echo "<script>window.location.href='course-viewer.php?id=" . $_GET['next'] . "'</script>";

                } else {

                    echo "<script>window.location.href='profile.php'</script>";

                }

                exit;

            } else {

            }

        }

    }

    function Login($email, $pass)
    {

        if (empty(trim($email)) || empty(trim($pass))) {

            echo "<script>document.body.style.opacity='1'; ShowPopup('Empty Fields','You must enter a valid email and password combination!');</script>";

            return;

        }

        global $servername, $username, $password, $database, $encryptionKey;

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {

            die("Connection failed: " . $conn->connect_error);

        }

        $sql = "SELECT * FROM users  where lower(trim(email)) = '" . $email . "' and password = '" . $pass . "'";

        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        if ($result->num_rows > 0) {

            $userEmail = encryptString($row['email'], $encryptionKey);

            $_SESSION['userEmail'] = $userEmail;

            if (isset($_GET['next'])) {

                echo "<script>window.location.href='course-viewer.php?id=" . $_GET['next'] . "'</script>";

            } else {

                echo "<script>window.location.href='profile.php'</script>";

            }

            exit;

        } else {

            echo "<script>document.body.style.opacity='1'; ShowPopup('Wrong Credentials','You must enter a valid email and password combination!');</script>";

        }

    }

    if (isset($_POST['login-btn'])) {

        $loginEmail = $_POST['login-email'];

        $loginPassword = $_POST['login-password'];

        Login($loginEmail, $loginPassword);

    } else if (isset($_POST['signup-btn'])) {

        $signupName = $_POST['signup-fullname'];

        $signupEmail = $_POST['signup-email'];

        $signupPassword = $_POST['signup-password'];

        Signup($signupName, $signupEmail, $signupPassword);

    } else if (isset($_POST['forgotPasswordBtn'])) {

        require 'PHPMailer-PHPMailer-827a549/src/PHPMailer.php';
        require 'PHPMailer-PHPMailer-827a549/src/SMTP.php';

        $loginEmail = $_POST['login-email'];

        if (empty(trim($loginEmail))) {

            echo "<script>window.onload=()=>{ document.body.style.opacity='1'; ShowPopup('Error','You should enter an email address!') };</script>";

        } else {

            global $servername, $username, $password, $database, $encryptionKey;

            $conn = new mysqli($servername, $username, $password, $database);

            if ($conn->connect_error) {

                die("Connection failed: " . $conn->connect_error);

            }

            $sql = "SELECT * FROM users  where lower(trim(email)) = '" . $loginEmail . "'";

            $result = $conn->query($sql);

            $row = $result->fetch_assoc();

            if ($result->num_rows > 0) {

                $mail = new PHPMailer\PHPMailer\PHPMailer();
                $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $mail->isSMTP();
                $mail->Host = 'smtp-mail.outlook.com';
                $mail->Port = 587;
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->SMTPAuth = true;
                $mail->Username = 'appoasis2023@outlook.com'; // Your Outlook email address
                $mail->Password = ''; // Your Outlook email password
                $mail->setFrom('appoasis2023@outlook.com', 'AppOasis');
                $mail->addAddress($loginEmail, $row['name']);
                $mail->Subject = 'Password Recovery';

                // Fetch the password and name from the $row array
                $password = $row['password'];
                $name = $row['name'];

                // Create the email body with HTML styling
                $body = "<html>
            <body style='font-family: Arial, sans-serif;'>
                <h2 style='color: #333;'>Dear {$name},</h2>
                <p style='color: #555;'>Your password is: <strong>{$password}</strong></p>
                <p style='color: #555;'>Thank you for using our service!</p>
                <p style='color: #555;'>Best regards,<br>AppOasis 2023</p>
            </body>
        </html>";

                $mail->Body = $body;

                $mail->isHTML(true); 

                $mail->send();

                echo "<script>window.onload=()=>{ document.body.style.opacity='1'; ShowPopup('Success','The password has been sent to yout email address!') };</script>";

            } else {

                echo "<script>window.onload=()=>{ document.body.style.opacity='1'; ShowPopup('Error','There is no account with the email you provided!') };</script>";

            }

        }

    } else {

        echo "<script>document.body.style.opacity='1';</script>";

    }

    ?>

</body>

</html>
