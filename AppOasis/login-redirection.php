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

    <div class="popup-container">

        <div class="popup">

            <h1>Login Redirection</h1>

            <p>This is a paid course, you should login first and purchase it if you haven't already done so to continue!
            </p>

            <button class="btn" onclick="NavigateToLoginPage()">

                <div>My Account</div>

            </button>

        </div>

    </div>

    <script>

        function NavigateToLoginPage() {

            const urlParams = new URLSearchParams(window.location.search);

            const nextParam = urlParams.get('next');


            window.location.href = 'account.php?next=' + nextParam;

        }

    </script>

</body>

</html>