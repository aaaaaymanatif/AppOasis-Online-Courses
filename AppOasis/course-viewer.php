<?php

session_start();

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

$row = "";

$video_link = "";

$source_code_link = "";

$can_proceed = false;

if (isset($_GET['id'])) {

    $encoded_id = urldecode($_GET['id']);

    $encrypted_id = base64_decode($encoded_id);

    $courseId = decryptString($encrypted_id, $encryptionKey);

    if (empty($courseId)) {

        echo "<script>window.location.href='home.php'</script>";

    } else {

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {

            die("Connection failed: " . $conn->connect_error);

        }

        $sql = "SELECT * FROM courses where id = " . $courseId;

        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        if ($result->num_rows > 0) {

            $video_link = $row['course_video_id'];

            $source_code_link = $row['course_source_code'];

            if ($row['plan'] == 'paid') {

                if (isset($_SESSION['userEmail'])) {

                    $userEmail = decryptString($_SESSION['userEmail'], $encryptionKey);

                    $sql = "select * from purchased_courses where lower(trim(user_email)) = lower(trim('" . $userEmail . "')) and course_id = " . $courseId;

                    $result = $conn->query($sql);

                    $row = $result->fetch_assoc();

                    if ($result->num_rows > 0) {

                        //User has purchased the course before

                        if ($row['course_price'] <= $row['amount_paid']) {

                            $can_proceed = true;

                        } else {

                            $can_proceed = false;

                        }

                    } else {

                        //User hasn't purchased the course yet
                        $can_proceed = false;

                        header("Location: purchase.php?id=" . $_GET['id']);

                    }

                } else {

                    header("Location: login-redirection.php?next=" . $_GET['id'] . "");

                    exit;

                }

            } else {

                //Free Course
                $can_proceed = true;

            }

        } else {

            echo "<script>window.location.href='home.php'</script>";

        }

        $conn->close();

    }

} else {

    echo "<script>window.location.href='home.php'</script>";

}

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
                    <form method="POST" target="_self" class="popup-container"
                        style="background-color: rgba(255,255,255,0.85); visibility: hidden" id="popup-parent">

                        <div class="popup" style="opacity:0; top:100px;" id="popup">

                            <h2 id="popup-title">
                                </h1>

                                <p id="popup-description"></p>

                                <script>
                                    //Basic Declarations
                                    const popupParent = document.getElementById("popup-parent");

                                    const popup = document.getElementById("popup");

                                    function ShowPopup(title, description, isSubmit) {

                                        document.cookie = "isSubmit=" + isSubmit;

                                        document.getElementById("popup-title").innerHTML = title;

                                        document.getElementById("popup-description").innerHTML = description;

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

                                            popupParent.style.visibility = "hidden";

                                        }, 250);

                                    }

                                </script>

                                <button type="submit" name="btnDismiss" class="btn" onclick="ClosePopup()">

                                    <div>Dismiss</div>

                                </button>

                        </div>

                        <?php

                        if ($row['course_price'] > $row['amount_paid']) {

                            echo "<script>ShowPopup('Error','This course price is \"$" . $row['course_price'] . "\", and you paid only \"$" . $row['amount_paid'] . "\", somehow you manipulated the payment to pay less, you need to click on dismiss and pay again to view this course!',true)</script>";

                        }

                        if (isset($_POST['btnDismiss'])) {

                            if (isset($_COOKIE['isSubmit'])) {

                                if ($_COOKIE['isSubmit'] == "true") {

                                    $conn = new mysqli($servername, $username, $password, $database);

                                    if ($conn->connect_error) {

                                        die("Connection failed: " . $conn->connect_error);

                                    }

                                    $sql = "delete from purchased_courses where id=" . $row['id'];

                                    if ($conn->query($sql) === TRUE) {

                                        echo "<script>window.location.href='purchase.php?id=" . $_GET['id'] . "'</script>";

                                    } else {

                                    }

                                }

                            }

                        }

                        ?>

                    </form>

                    <!-- Course Video Section -->
                    <section class="course-video-introduction" style="margin: 0; margin-bottom: 50px;">

                        <?php

                        if ($can_proceed) {

                            echo "<iframe style='margin: 0;' width='100%' src='https://www.youtube.com/embed/" . $video_link . "'
                                frameborder='0' allowfullscreen></iframe>";

                        }

                        ?>

                    </section>

                    <!-- Overview Section -->
                    <section class="overview" style="background-color: transparent;">

                        <!-- Overview Details Section -->
                        <section class="overview-details">

                            <h2 style="color: #333;">Download Source Code & Get Free Unlimited Support.</h2>

                            <p style="color: #555;">This course provides downloadable source code and dedicated support
                                for an enriched learning experience. Gain hands-on experience with practical
                                implementations using the source code, while receiving comprehensive support throughout
                                your journey.</p>

                            <?php

                            if ($can_proceed) {

                                echo
                                    "<a href='" . $source_code_link . "' class='btn'
                                    style='text-decoration:none;margin-top:30px;font-size:1.2rem;font-weight:bold;background-color: royalblue; outline-color: royalblue; color: white; padding: 15px 50px;'>

                                    Download

                                </a>";

                            }

                            ?>

                        </section>

                        <!-- Overview Image Section -->
                        <div class="overview-figure">

                            <div class="overview-figure-container" style="outline-color: royalblue;">

                                <img src="assets/images/wallpaper4.PNG" alt="Portrait Image" style="height: 100%;" />

                            </div>

                        </div>

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

                                        echo "<script>window.onload=()=>{ ShowPopup('Newsletter','You have been subscribed to newsletter!', false) };</script>";

                                    } else {

                                    }

                                }

                                $conn2->close();

                            }

                            ?>

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