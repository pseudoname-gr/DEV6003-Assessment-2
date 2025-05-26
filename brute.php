<?php

if (isset($_GET['Login'])) {
    // Initialize attempts and lockout time if not set
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = 0;
    }

    // Lockout parameters
    $max_attempts = 50;
    $lockout_duration = 15 * 60; // 15 minutes in seconds

    $current_time = time();

    // Check if user is currently locked out
    if ($_SESSION['login_attempts'] >= $max_attempts && ($current_time - $_SESSION['last_attempt_time']) < $lockout_duration) {
        $remaining = $lockout_duration - ($current_time - $_SESSION['last_attempt_time']);
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;
        $html .= "<pre>Too many failed login attempts. Please try again in {$minutes} minutes and {$seconds} seconds.</pre>";
    } else {
        // If lockout expired, reset counters
        if (($current_time - $_SESSION['last_attempt_time']) >= $lockout_duration) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = 0;
        }

        // Get username
        $user = $_GET['username'];

        // Get password and hash it
        $pass = $_GET['password'];
        $pass = md5($pass);

        // Check the database
        $query  = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";
        $result = mysqli_query($GLOBALS["___mysqli_ston"], $query) or die('<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');

        if ($result && mysqli_num_rows($result) == 1) {
            // Success: reset lockout
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = 0;

            // Welcome user
            $row    = mysqli_fetch_assoc($result);
            $avatar = $row["avatar"];

            $html .= "<p>Welcome to the password protected area {$user}</p>";
            $html .= "<img src=\"{$avatar}\" />";
        } else {
            // Failed login
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            $html .= "<pre>Username and/or password incorrect. Attempt #{$_SESSION['login_attempts']}.</pre>";

         
            }
        }
    }

    // Close DB connection
    ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}
?>