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

                            <h2 style="color: #333;">AppOasis: Embark on a Digital Journey of Knowledge with our Online
                                Learning Platform.</h2>

                            <p style="color: #555;">The online learning platform where knowledge
                                becomes an exciting digital adventure. Explore diverse courses, learn from experts, and
                                unlock limitless learning opportunities!</p>

                            <button class="btn"
                                style="background-color: royalblue; outline-color: royalblue; color: white; padding: 15px 50px;"
                                onclick="window.location.href='courses.php'">

                                <div style=" font-size: 1.2rem; font-weight: bold;">Explore Courses</div>

                            </button>

                        </section>

                        <!-- Overview Image Section -->
                        <div class="overview-figure">

                            <div class="overview-figure-container" style="outline-color: royalblue;">

                                <img src="assets/images/wallpaper1.PNG" alt="Portrait Image" style="height: 100%;" />

                            </div>

                        </div>

                    </section>

                    <!-- Title Section -->
                    <section class="title-section" style="margin-top: 50px; margin-bottom: 50px;">

                        <h2>Suggestions</h2>

                        <p>Explore our carefully curated selection of recommended courses. Enjoy a high-quality learning
                            experience and deepen your knowledge in exciting areas. Choose from a variety of engaging
                            courses and enhance your learning journey today.</p>

                    </section>

                    <!-- Available Courses Content -->
                    <section class="content-section">

                        <ul class="courses-grid">

                            <?php

                            $conn = new mysqli($servername, $username, $password, $database);

                            if ($conn->connect_error) {

                                die("Connection failed: " . $conn->connect_error);

                            }

                            $sql = "SELECT * FROM courses ORDER BY RAND() LIMIT 6";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {

                                // Output data for each row
                            
                                while ($row = $result->fetch_assoc()) {

                                    $encrypted_id = base64_encode(encryptString($row['id'], $encryptionKey));

                                    $encoded_id = urlencode($encrypted_id);

                                    $price = 0;

                                    if ($row['plan'] == 'paid')
                                        $price = $row['price'];

                                    echo "
                                        <li onclick=\"window.location.href='course-details.php?id='+ '" . $encrypted_id . "'\">
                                        
                                            <article>

                                                <div class='image-cover'>

                                                    <img src='" . $row['thumbnail_image_link'] . "'
                                                        alt='Course Image Cover' />

                                                </div>

                                                <div class='course-overview'>

                                                    <h2>" . htmlspecialchars_decode($row['short_title']) . "</h2>

                                                    <p>" . htmlspecialchars_decode($row['description']) . "</p>

                                                    <span class='course-price'>
                                                    
                                                    <sup>$</sup>

                                                    <strong>" . $price . "</strong>

                                                    </span>

                                                </div>

                                            </article>
                                        
                                        </li>";

                                }

                            }

                            $conn->close();

                            ?>

                        </ul>

                    </section>

                    <!-- Title Section -->
                    <section class="title-section" style="margin-top: 50px;">

                        <h2>Overview</h2>

                        <p>If you require any assistance or have any inquiries, please don't hesitate to contact us. Our
                            dedicated team is ready to provide you with the support you need. We value your feedback and
                            are committed to ensuring your experience is seamless. Feel free to reach out to us through
                            our contact information provided below. We are here to help and look forward to hearing from
                            you!</p>

                    </section>

                    <!-- Overview Section -->
                    <section class="overview" style="background-color: transparent;">

                        <!-- Overview Image Section -->
                        <div class="overview-figure">

                            <div class="overview-figure-container" style="outline-color: royalblue;">

                                <img src="assets/images/wallpaper2.PNG" alt="Portrait Image" style="height: 100%;" />

                            </div>

                        </div>

                        <!-- Overview Details Section -->
                        <section class="overview-details">

                            <h2 style="color: #333;">Empowering Digital Learners to Explore, Engage, and Excel in Online
                                Education.</h2>

                            <p style="color: #555;">Contact us today and let's start a conversation! We're here to
                                assist you and answer any questions you may have. Reach out to our team through our
                                contact page and discover how we can support your learning goals.</p>

                            <button class="btn"
                                style="background-color: royalblue; outline-color: royalblue; color: white; padding: 15px 50px;"
                                onclick="window.location.href='about.php'">

                                <div style=" font-size: 1.2rem; font-weight: bold;">Contact Us</div>

                            </button>

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