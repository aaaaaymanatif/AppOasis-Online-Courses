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

$userEmail = "";

if (!isset($_SESSION['userEmail'])) {

    header("Location: account.php");

} else {

    $userEmail = decryptString($_SESSION['userEmail'], $encryptionKey);

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

                    <!-- Title Section -->
                    <section class="title-section">

                        <h2>

                            <?php

                            $conn = new mysqli($servername, $username, $password, $database);

                            if ($conn->connect_error) {

                                die("Connection failed: " . $conn->connect_error);

                            }

                            $sql = "SELECT * FROM users WHERE lower(trim(email)) = lower(trim('" . $userEmail . "'))";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {

                                $row = $result->fetch_assoc();

                                echo $row['name'];

                            }

                            ?>

                        </h2>

                        <p>Welcome to your profile page! Here, you can access the courses you have purchased on our
                            learning platform. This is your personal hub where you can keep track of the courses you've
                            enrolled in and access all the valuable content they offer!</p>

                    </section>

                    <!-- Categories Section -->
                    <section class="navigation-section">

                        <!-- Search Section -->
                        <section class="search-section" id="search-container">

                            <form class="content-form">

                                <div class="content-form-image">

                                    <img src="assets/images/wallpaper5.PNG" alt="Decorative Image" />

                                </div>

                                <div class="form-fieldset">

                                    <img src="assets/icons/close.png" alt="Close Search Form" id="close-search-form" />

                                    <fieldset>

                                        <legend>Search for something</legend>

                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                            tempor incididunt ut labore.</p>

                                        <input type="text" id="search-text" />

                                        <button class="btn" type="button" onclick="SearchForSomething();">

                                            <div>Search Now</div>

                                        </button>

                                    </fieldset>

                                </div>

                            </form>

                        </section>

                        <!-- Filters Section -->
                        <section class="filter-section">

                            <ul>

                                <li>

                                    <button type="button" onclick="SearchByOrder('desc')">

                                        <img src="assets/icons/new.png" alt="New Courses" />

                                        <p>New Courses</p>

                                    </button>

                                </li>

                                <li>

                                    <button type="button" onclick="SearchByOrder('asc')">

                                        <img src="assets/icons/old.png" alt="Old Courses" />

                                        <p>Old Courses</p>

                                    </button>

                                </li>

                            </ul>

                            <img src="assets/icons/close.png" alt="Close Filter Section" id="close-filter-section" />

                        </section>

                        <!-- General Categories Section -->
                        <section class="general-categories-section">

                            <form class="content-form">

                                <div class="content-form-image">

                                    <img src="assets/images/wallpaper5.PNG" alt="Decorative Image" />

                                </div>

                                <div class="form-fieldset">

                                    <legend>More Categories</legend>

                                    <img src="assets/icons/close.png" alt="Close Categories Form"
                                        id="close-categories-form" />

                                    <ul style="padding: 0; margin: 0;">

                                        <?php

                                        $conn = new mysqli($servername, $username, $password, $database);

                                        if ($conn->connect_error) {

                                            die("Connection failed: " . $conn->connect_error);

                                        }

                                        $sql = "SELECT * FROM Categories WHERE platform_based = false";

                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {

                                            // Output data for each row
                                        
                                            while ($row = $result->fetch_assoc()) {

                                                echo "<li><button onclick=\"window.location.href = 'profile.php?category=" . urlencode($row['name']) . "'\" type='button' class='btn' style='background-color: royalblue; color: white; border-radius: 40px;'>" . $row['name'] . "</button></li>";

                                            }

                                        }

                                        $conn->close();

                                        ?>

                                    </ul>

                                </div>

                            </form>

                        </section>

                        <img src="assets/icons/search.png" alt="Search" class="icon" id="search-btn" />

                        <ul>

                            <li><button onclick="window.location.href='profile.php';">All</button></li>

                            <?php

                            $conn = new mysqli($servername, $username, $password, $database);

                            if ($conn->connect_error) {

                                die("Connection failed: " . $conn->connect_error);

                            }

                            $sql = "SELECT * FROM Categories WHERE platform_based = true";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {

                                // Output data for each row
                            
                                while ($row = $result->fetch_assoc()) {

                                    echo "<li><button onclick=\"window.location.href = 'profile.php?category=" . urlencode($row['name']) . "'\" type='button'>" . $row['name'] . "</button></li>";

                                }

                            }

                            $conn->close();

                            ?>

                            <li><button id="general-categories-btn">General</button></li>

                        </ul>

                        <img src="assets/icons/filter.png" alt="Filter" class="icon" id="filter-btn" />

                    </section>

                    <!-- Available Courses Content -->
                    <section class="content-section">

                        <ul class="courses-grid">

                            <?php

                            $conn = new mysqli($servername, $username, $password, $database);

                            if ($conn->connect_error) {

                                die("Connection failed: " . $conn->connect_error);

                            }

                            $sql = "";

                            if (isset($_GET['category'])) {

                                $category_name = urldecode($_GET['category']);

                                $sql = "SELECT * FROM Courses where category_id = (select id from categories WHERE lower(trim(name)) = lower(trim('" . $category_name . "'))) and id in(select course_id from purchased_courses where lower(trim(user_email)) = lower(trim('" . $userEmail . "')))";

                                if (isset($_GET['search-query'])) {

                                    $searchQuery = $_GET['search-query'];

                                    $sql .= " and (lower(trim(short_title)) like lower(trim('%" . $searchQuery . "%')) or lower(trim(`long_title`)) like lower(trim('%" . $searchQuery . "%')) or lower(trim(`description`)) like lower(trim('%" . $searchQuery . "%')))";

                                }


                            } else {

                                $sql = "SELECT * FROM Courses where id in(select course_id from purchased_courses where lower(trim(user_email)) = lower(trim('" . $userEmail . "')))";

                                if (isset($_GET['search-query'])) {

                                    $searchQuery = $_GET['search-query'];

                                    $sql .= " and (lower(trim(short_title)) like lower(trim('%" . $searchQuery . "%')) or lower(trim(`long_title`)) like lower(trim('%" . $searchQuery . "%')) or lower(trim(`description`)) like lower(trim('%" . $searchQuery . "%')))";

                                }

                            }

                            if (isset($_GET['order-by'])) {

                                $orderBy = $_GET['order-by'];

                                $sql .= " order by date " . $orderBy;

                            }

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

                                                </div>

                                            </article>
                                        
                                        </li>";

                                }

                            }

                            $conn->close();

                            ?>

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

    <script>

        /* Basic Declarations */
        const searchText = document.getElementById("search-text");

        /* Function to search for a course by text */
        function SearchForSomething() {

            let text = searchText.value;

            var url = new URL(window.location.href);

            var searchParams = new URLSearchParams(url.search);

            if (searchParams.has('search-query')) {

                searchParams.set('search-query', text);

            } else {

                searchParams.append('search-query', text);

            }

            var paramsArray = Array.from(searchParams.entries());

            var updatedQueryString = paramsArray.map(function (pair) {

                return pair[0] + '=' + encodeURIComponent(pair[1]);

            }).join('&');

            url.search = '?' + updatedQueryString;

            window.history.replaceState(null, '', url.toString());

            window.location.reload();

        }

        /* Search by order function (asc, desc) */
        function SearchByOrder(_case) {

            var url = new URL(window.location.href);

            var searchParams = new URLSearchParams(url.search);

            if (searchParams.has('order-by')) {

                searchParams.set('order-by', _case);

            } else {

                searchParams.append('order-by', _case);

            }

            var paramsArray = Array.from(searchParams.entries());

            var updatedQueryString = paramsArray.map(function (pair) {

                return pair[0] + '=' + encodeURIComponent(pair[1]);

            }).join('&');

            url.search = '?' + updatedQueryString;

            window.history.replaceState(null, '', url.toString());

            window.location.reload();

        }

    </script>

</body>

</html>