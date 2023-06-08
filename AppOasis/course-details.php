<?php

session_start();

if (isset($_POST['start-course']) || isset($_POST['pricing-start-course'])) {

    if (isset($_GET['id'])) {

        header("Location: course-viewer.php?id=" . $_GET['id']);

        exit;

    } else {

        //handle error

    }

}

?>

<?php

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


//Course Fields array
$row = "";

$already_purchased = false;

if (isset($_GET['id'])) {

    $encoded_id = urldecode($_GET['id']);

    $encrypted_id = base64_decode($encoded_id);

    $courseId = decryptString($encrypted_id, $encryptionKey);

    if (empty($courseId)) {

        header("Location: home.php");

        exit;

    } else {

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {

            die("Connection failed: " . $conn->connect_error);

        }

        $sql = "SELECT * FROM courses where id = " . $courseId;

        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        if ($result->num_rows > 0) {

            if (isset($_SESSION['userEmail'])) {

                $userEmail = decryptString($_SESSION['userEmail'], $encryptionKey);


                $sql = "select * from purchased_courses where lower(trim(user_email)) = lower(trim('" . $userEmail . "')) and course_id = " . $courseId;

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {

                    $already_purchased = true;

                }

            }


        } else {

            header("Location: home.php");

        }

    }

} else {

    header("Location: home.php");

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
                    <section class="overview">

                        <!-- Overview Details Section -->
                        <section class="overview-details">

                            <h2>
                                <?php echo $row['long_title']; ?>
                            </h2>

                            <p>
                                <?php echo $row['description']; ?>
                            </p>

                            <span class="overview-details-price">

                                <?php

                                $price = "Free Course";

                                if ($row['plan'] == 'paid') {

                                    if (!$already_purchased) {

                                        echo "<sup>$</sup>";

                                    }

                                    $price = $row['price'];

                                }

                                if ($already_purchased == true) {

                                    $price = "Already Purchased";

                                }

                                echo "<strong>" . $price . "</strong>";

                                ?>

                            </span>

                            <form method="POST" target="_self">

                                <button class="btn" id="start-course" name="start-course">

                                    <div>Start Course</div>

                                </button>

                            </form>

                        </section>

                        <!-- Overview Image Section -->
                        <div class="overview-figure">

                            <div class="overview-figure-container">

                                <?php

                                echo "<img src='" . $row['thumbnail_image_link'] . "'
                                        alt='Course Image Cover' />";

                                ?>

                            </div>

                        </div>

                    </section>

                    <!-- Course Materials Section -->
                    <section class="course-materials">

                        <ul class="course-materials-grid">

                            <li>

                                <figure>
                                    <img src="assets/icons/clean.png" alt="Clean Code" />
                                    <figcaption>Clean Code</figcaption>
                                </figure>

                            </li>

                            <li>

                                <figure>
                                    <img src="assets/icons/security.png" alt="Security Integrated" />
                                    <figcaption>Security Integrated</figcaption>
                                </figure>

                            </li>

                            <li>

                                <figure>
                                    <img src="assets/icons/code.png" alt="Codebase" />
                                    <figcaption>Codebase Provided</figcaption>
                                </figure>

                            </li>

                            <li>

                                <figure>
                                    <img src="assets/icons/support.png" alt="Support" />
                                    <figcaption>Free Support</figcaption>
                                </figure>

                            </li>

                        </ul>

                    </section>

                    <!-- Course Video Section -->
                    <section class="course-video-introduction">

                        <header>

                            <div>

                                <img src="assets/icons/brief.png" alt="Introduction" />

                                <h2>Little Introduction</h2>

                            </div>

                            <p>
                                <?php echo $row['introduction_text']; ?>
                            </p>

                        </header>

                        <?php

                        echo "<iframe width='100%' src='https://www.youtube.com/embed/" . $row['introduction_video_id'] . "' frameborder='0'
                            allowfullscreen></iframe>";

                        ?>

                    </section>

                    <!-- Course Objectives section -->
                    <section class="text-container" id="course-objectives">

                        <header>

                            <div>

                                <img src="assets/icons/brief.png" alt="Introduction" />

                                <h2>Course Objectives</h2>

                            </div>

                        </header>

                        <div class="text-container-content">

                            <p>
                                <?php echo $row['objectives']; ?>
                            </p>

                        </div>

                    </section>

                    <!-- Course Prerequisites Section -->
                    <section class="text-container" id="course-prerequisites">

                        <header>

                            <div>

                                <img src="assets/icons/brief.png" alt="Introduction" />

                                <h2>Course Prerequisites</h2>

                            </div>

                        </header>

                        <div class="text-container-content">

                            <p>
                                <?php echo $row['prerequisites']; ?>
                            </p>

                        </div>

                    </section>

                    <!-- Pricing Section -->
                    <section class="pricing">

                        <ul class="pricing-grid">

                            <li class="pricing-card-item">

                                <section>

                                    <h2>Explore Our Competitive Pricing Options for
                                        Online Courses!</h2>

                                    <p>Our online learning platform offers competitive pricing for our courses, providing excellent value and accessibility. With a single pricing option, learners gain unlimited access to a comprehensive course catalog, enabling them to learn at their own pace and explore various subjects.</p>

                                </section>

                            </li>

                            <li class="pricing-card-item">

                                <section class="pricing-card">

                                    <p>Course Price</p>

                                    <h2 class="price" style="font-size: 3rem;">

                                        <sup class="coin">$</sup>

                                        <?php

                                        if ($row['plan'] == 'paid') {

                                            if (!$already_purchased) {

                                                echo $row['price'];

                                            } else {

                                                echo "0";

                                            }

                                        } else {

                                            echo "0";

                                        }

                                        ?>

                                    </h2>

                                    <ul>

                                        <li class="check-list">

                                            <img src="assets/icons/check.png" alt="Check Icon" />

                                            Clean and commented source code.

                                        </li>

                                        <li class="check-list">

                                            <img src="assets/icons/check.png" alt="Check Icon" />

                                            Security integrated.

                                        </li>

                                        <li class="check-list">

                                            <img src="assets/icons/check.png" alt="Check Icon" />

                                            Source code provided to download.

                                        </li>

                                    </ul>

                                </section>

                            </li>

                            <li class="pricing-card-item">

                                <section class="pricing-card">

                                    <p>Additional</p>

                                    <h2 class="price" style="font-size: 2.7rem;">Features</h2>

                                    <ul>

                                        <li class="check-list">

                                            <img src="assets/icons/check.png" alt="Check Icon" />

                                            Get Unlimited Post-Purchase Support for Your Online Programming Course.

                                        </li>

                                    </ul>

                                    <form method="POST" target="_self" style="width: 100%">

                                        <button class="btn" name="pricing-start-course">

                                            <div>

                                                <?php

                                                if ($row['plan'] == 'free') {

                                                    echo "Start Course";

                                                } else {

                                                    if (!$already_purchased) {

                                                        echo "Purchase Now";

                                                    } else {

                                                        echo "Start Course";

                                                    }

                                                }

                                                ?>

                                            </div>

                                        </button>

                                    </form>

                                </section>

                            </li>

                        </ul>

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