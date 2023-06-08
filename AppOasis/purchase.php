<?php

session_start();

if (!isset($_SESSION['userEmail'])) {

    header("Location: login-redirection.php?next=" . $_GET['id'] . "");

    exit;

}

if (isset($_POST['btnDismiss'])) {

    header("Location: course-viewer.php?id=" . $_GET['id'] . "");

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

if (isset($_GET['id'])) {

    $encoded_id = urldecode($_GET['id']);

    $encrypted_id = base64_decode($encoded_id);

    $courseId = decryptString($encrypted_id, $encryptionKey);

    if (empty($courseId)) {

        echo "<script>window.location.href='home.php'</script>";

    } else {

        $userEmail = decryptString($_SESSION['userEmail'], $encryptionKey);

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {

            die("Connection failed: " . $conn->connect_error);

        }

        $sql = "SELECT * FROM courses where id = " . $courseId;

        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

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

                                <button type="submit" name="btnDismiss" class="btn" onclick="ClosePopup()">

                                    <div>Dismiss</div>

                                </button>

                        </div>

                    </form>

                    <section class="payment-form">

                        <div class="inner">

                            <table>

                                <tr>

                                    <td>User</td>

                                    <td>Course</td>

                                    <td>Price</td>

                                </tr>

                                <tr>

                                    <td> <strong>
                                            <?php echo $userEmail ?>
                                        </strong> </td>

                                    <td> <strong>
                                            <?php echo $row['short_title'] ?>
                                        </strong> </td>

                                    <td> <strong> <sup>$</sup>
                                            <?php echo $row['price'] ?>
                                        </strong> </td>

                                </tr>

                            </table>

                            <div id="paypal-button-container"></div>

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

                                        echo "<script>window.onload=()=>{ ShowPopup('Newsletter','You have been subscribed to newsletter!') };</script>";

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

    <script
        src="https://www.paypal.com/sdk/js?client-id=AdCUflWFrNE6AoZPEakI6iihOwO0ayFptoTc2OgGjnBcr7qqDRDJ45TeVzGJEWB48LSU1kFdquIqLB7B&currency=USD"></script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

        paypal.Buttons({
            style: {
                color: 'blue',
                shape: 'pill'
            },
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $row['price']; ?>'
                        }
                    }]
                });
            },
            onApprove: function (data, actions) {
                // Retrieve the order ID from the data object
                var orderID = data.orderID;

                // Send the order ID to the server for verification
                $.ajax({
                    url: 'verify_payment_sandbox.php', // Replace with your PHP script endpoint
                    type: 'POST',
                    data: { orderID: orderID, price: '<?php echo $row['price']; ?>', courseId: '<?php echo $courseId; ?>' },
                    success: function (response) {
                        // Server verification successful
                        console.log('Payment verification successful');
                        console.log(response);
                        // Handle the server response as needed
                    },
                    error: function () {
                        // Server verification failed
                        console.log('Payment verification failed');
                    }
                });

                // Proceed with the payment capture and other necessary actions
                return actions.order.capture().then(function (details) {
                    // Payment captured on the client-side
                    //console.log(details);
                    var paidAmount = details.purchase_units[0].amount.value;
                    //console.log('Paid Amount: ' + paidAmount);
                    ShowPopup('Success', 'Congratulations, you have purchased this course');
                });
            }
        }).render('#paypal-button-container');
    </script>


</body>

</html>