<?php
// Include the loader file which might contain necessary configurations and initializations
include 'config/loader.php';

// Check if the 'namefull' and 'mobile' cookies are set, and if so, redirect to the index page
if(isset($_COOKIE['namefull']) && isset($_COOKIE['mobile'])){
  redirect("index.php");
}

// Prepare SQL query to find the user by the reset token
$sql = "SELECT * FROM users WHERE reset_token = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $_GET['token']);
$stmt->execute();
$result = $stmt->get_result();

// Check if the token is not set, has an invalid length, or doesn't match any user
if(!isset($_GET['token']) || strlen($_GET['token']) != 16 || $result->num_rows == 0){
  // If any condition fails, redirect to the login page
  redirect("login.php");
}

// Check if the password reset form is submitted
if(isset($_POST['forgotpasssubmit'])){

  // Include the Validation helper for form validation
  require('helpers/Validation.php');
  $val = new Validation();
  $password = htmlspecialchars($_POST['password']);
  $passwordconf = htmlspecialchars($_POST['passwordconf']);
// Validate the new password
$val->name('password')
->value($password)
->min(8) // Minimum length of 8 characters
->max(20) // Maximum length of 20 characters
->required(); // Field is required

// Validate the confirmation password
$val->name('repeat password')
->value($passwordconf)
->min(8) // Minimum length of 8 characters
->max(20) // Maximum length of 20 characters
->equal($password) // Must match the original password
->required(); // Field is required

// If validation is successful
if($val->isSuccess()){
$token = $_POST['token']; // Get the token from the form
// Hash the new password
$new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Update the user's password and reset the token
$sql = "UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $new_password, $token);
$stmt->execute();

// Set a success message
$_SESSION['success'] = 'Your password has been successfully changed';
} else {
// If validation fails, store the error message in the session
$_SESSION['error'] = $val->displayError();
}
}

?>
<!doctype html>
<html dir="rtl" lang="fa">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Forgot Password</title>

    <link href="./assets/css/fonts.css" rel="stylesheet" />
    <link href="./assets/css/app.css" rel="stylesheet" />
    <link href="./assets/css/theme.css" rel="stylesheet" />

    <script src="./assets/scripts/mount.js"></script>
</head>

<body>
    <form method="post">
        <div class="sr-only">
            <svg>
                <symbol viewBox="0 0 24 24" id="svg-spinners-90-ring">
                    <path fill="currentColor"
                        d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z">
                        <animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate"
                            values="0 12 12;360 12 12"></animateTransform>
                    </path>
                </symbol>
                <symbol viewBox="0 0 24 24" id="svg-spinners-3-dots-scale">
                    <circle cx="4" cy="12" r="3" fill="currentColor">
                        <animate id="svgSpinners3DotsScale0" attributeName="r"
                            begin="0;svgSpinners3DotsScale1.end-0.25s" dur="0.75s" values="3;.2;3"></animate>
                    </circle>
                    <circle cx="12" cy="12" r="3" fill="currentColor">
                        <animate attributeName="r" begin="svgSpinners3DotsScale0.end-0.6s" dur="0.75s" values="3;.2;3">
                        </animate>
                    </circle>
                    <circle cx="20" cy="12" r="3" fill="currentColor">
                        <animate id="svgSpinners3DotsScale1" attributeName="r" begin="svgSpinners3DotsScale0.end-0.45s"
                            dur="0.75s" values="3;.2;3"></animate>
                    </circle>
                </symbol>
                <symbol viewBox="0 0 24 24" id="svg-spinners-3-dots-fade">
                    <circle cx="4" cy="12" r="3" fill="currentColor">
                        <animate id="svgSpinners3DotsFade0" fill="freeze" attributeName="opacity"
                            begin="0;svgSpinners3DotsFade1.end-0.25s" dur="0.75s" values="1;.2"></animate>
                    </circle>
                    <circle cx="12" cy="12" r="3" fill="currentColor" opacity=".4">
                        <animate fill="freeze" attributeName="opacity" begin="svgSpinners3DotsFade0.begin+0.15s"
                            dur="0.75s" values="1;.2"></animate>
                    </circle>
                    <circle cx="20" cy="12" r="3" fill="currentColor" opacity=".3">
                        <animate id="svgSpinners3DotsFade1" fill="freeze" attributeName="opacity"
                            begin="svgSpinners3DotsFade0.begin+0.3s" dur="0.75s" values="1;.2"></animate>
                    </circle>
                </symbol>
                <symbol viewBox="0 0 24 24" id="svg-spinners-6-dots-rotate">
                    <g>
                        <circle cx="12" cy="2.5" r="1.5" fill="currentColor" opacity=".14"></circle>
                        <circle cx="16.75" cy="3.77" r="1.5" fill="currentColor" opacity=".29"></circle>
                        <circle cx="20.23" cy="7.25" r="1.5" fill="currentColor" opacity=".43"></circle>
                        <circle cx="21.5" cy="12" r="1.5" fill="currentColor" opacity=".57"></circle>
                        <circle cx="20.23" cy="16.75" r="1.5" fill="currentColor" opacity=".71"></circle>
                        <circle cx="16.75" cy="20.23" r="1.5" fill="currentColor" opacity=".86"></circle>
                        <circle cx="12" cy="21.5" r="1.5" fill="currentColor"></circle>
                        <animateTransform attributeName="transform" calcMode="discrete" dur="0.75s"
                            repeatCount="indefinite" type="rotate"
                            values="0 12 12;30 12 12;60 12 12;90 12 12;120 12 12;150 12 12;180 12 12;210 12 12;240 12 12;270 12 12;300 12 12;330 12 12;360 12 12">
                        </animateTransform>
                    </g>
                </symbol>
                <symbol viewBox="0 0 24 24" id="svg-spinners-90-ring">
                    <path fill="currentColor"
                        d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z">
                        <animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate"
                            values="0 12 12;360 12 12"></animateTransform>
                    </path>
                </symbol>
                <symbol viewBox="0 0 24 24" id="svg-spinners-90-ring">
                    <path fill="currentColor"
                        d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z">
                        <animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate"
                            values="0 12 12;360 12 12"></animateTransform>
                    </path>
                </symbol>
                <symbol viewBox="0 0 24 24" id="svg-spinners-ring-resize">
                    <g stroke="currentColor">
                        <circle cx="12" cy="12" r="9.5" fill="none" stroke-linecap="round" stroke-width="3">
                            <animate attributeName="stroke-dasharray" calcMode="spline" dur="1.5s"
                                keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" keyTimes="0;0.475;0.95;1"
                                repeatCount="indefinite" values="0 150;42 150;42 150;42 150"></animate>
                            <animate attributeName="stroke-dashoffset" calcMode="spline" dur="1.5s"
                                keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" keyTimes="0;0.475;0.95;1"
                                repeatCount="indefinite" values="0;-16;-59;-59"></animate>
                        </circle>
                        <animateTransform attributeName="transform" dur="2s" repeatCount="indefinite" type="rotate"
                            values="0 12 12;360 12 12"></animateTransform>
                    </g>
                </symbol>
                <symbol viewBox="0 0 24 24" id="svg-spinners-180-ring">
                    <path fill="currentColor"
                        d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z">
                        <animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate"
                            values="0 12 12;360 12 12"></animateTransform>
                    </path>
                </symbol>
                <!-- ph-list-magnifying-glass-light -->
                <symbol viewBox="0 0 256 256" id="why-us">
                    <path fill="currentColor"
                        d="M34 64a6 6 0 0 1 6-6h176a6 6 0 0 1 0 12H40a6 6 0 0 1-6-6Zm6 70h72a6 6 0 0 0 0-12H40a6 6 0 0 0 0 12Zm88 52H40a6 6 0 0 0 0 12h88a6 6 0 0 0 0-12Zm108.24 10.24a6 6 0 0 1-8.48 0l-21.49-21.48a38.06 38.06 0 1 1 8.49-8.49l21.48 21.49a6 6 0 0 1 0 8.48ZM184 170a26 26 0 1 0-26-26a26 26 0 0 0 26 26Z">
                    </path>
                </symbol>
                <!-- ph-devices-light -->
                <symbol viewBox="0 0 256 256" id="how-to-buy">
                    <path fill="currentColor"
                        d="M224 74h-18V64a22 22 0 0 0-22-22H40a22 22 0 0 0-22 22v96a22 22 0 0 0 22 22h114v10a22 22 0 0 0 22 22h48a22 22 0 0 0 22-22V96a22 22 0 0 0-22-22ZM40 170a10 10 0 0 1-10-10V64a10 10 0 0 1 10-10h144a10 10 0 0 1 10 10v10h-18a22 22 0 0 0-22 22v74Zm194 22a10 10 0 0 1-10 10h-48a10 10 0 0 1-10-10V96a10 10 0 0 1 10-10h48a10 10 0 0 1 10 10Zm-100 16a6 6 0 0 1-6 6H88a6 6 0 0 1 0-12h40a6 6 0 0 1 6 6Zm80-96a6 6 0 0 1-6 6h-16a6 6 0 0 1 0-12h16a6 6 0 0 1 6 6Z">
                    </path>
                </symbol>

                <!-- ph-fire-light -->
                <symbol viewBox="0 0 256 256" id="special-sale">
                    <path fill="currentColor"
                        d="M181.92 153A55.58 55.58 0 0 1 137 197.92a7 7 0 0 1-1 .08a6 6 0 0 1-1-11.92c17.38-2.92 32.13-17.68 35.08-35.08a6 6 0 1 1 11.84 2Zm32.08-9a86 86 0 0 1-172 0c0-27.47 10.85-55.61 32.25-83.64a6 6 0 0 1 9-.67l26.34 25.56l23.09-63.31a6 6 0 0 1 9.47-2.56C163.72 37.33 214 85.4 214 144Zm-12 0c0-48.4-38.65-89.84-61.07-109.8l-23.29 63.86a6 6 0 0 1-9.82 2.25l-28-27.22C62.67 97.13 54 121 54 144a74 74 0 0 0 148 0Z">
                    </path>
                </symbol>
                <!-- carbon-recently-viewed -->
                <symbol id="recent" viewBox="0 0 32 32">
                    <path d="M20.59 22L15 16.41V7h2v8.58l5 5.01L20.59 22z" fill="currentColor"></path>
                    <path d="M16 2A13.94 13.94 0 0 0 6 6.23V2H4v8h8V8H7.08A12 12 0 1 1 4 16H2A14 14 0 1 0 16 2Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- system-uicons-filtering -->
                <symbol id="filter" viewBox="0 0 21 21">
                    <path
                        d="M6.5 4a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V5a1 1 0 0 1 1-1zm12 2h-11m-2 0h-3m4 8a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0v-2a1 1 0 0 1 1-1zm12 2h-11m-2 0h-3m12-7a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0v-2a1 1 0 0 1 1-1zm-1 2h-11m16 0h-3"
                        fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                </symbol>
                <!-- solar-sort-from-top-to-bottom-linear -->
                <symbol id="sort" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5">
                        <path d="M4 16h9m-7-5h7M8 6h5"></path>
                        <path d="M17 4v16l3-4" stroke-linejoin="round"></path>
                    </g>
                </symbol>
                <!-- mdi-clipboard-text-off-outline -->
                <symbol id="order-off" viewBox="0 0 24 24">
                    <path
                        d="M17 7V5h2v10.8l2 2V5a2 2 0 0 0-2-2h-4.18C14.25 1.44 12.53.64 11 1.2c-.86.3-1.5.96-1.82 1.8H6.2l4 4H17m-5-4c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1m2.2 8l-2-2H17v2h-2.8M2.39 1.73L1.11 3L3 4.9V19a2 2 0 0 0 2 2h14.1l1.74 1.73l1.27-1.27L2.39 1.73M5 19V6.89L7.11 9H7v2h2.11l2 2H7v2h6.11l4 4H5Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- tabler-clock-off -->
                <symbol id="recent-off" viewBox="0 0 24 24">
                    <path d="M5.633 5.64a9 9 0 1 0 12.735 12.72m1.674-2.32A9 9 0 0 0 7.96 3.958M12 7v1M3 3l18 18"
                        fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"></path>
                </symbol>
                <!-- ph-bell-simple-slash -->
                <symbol id="notification-off" viewBox="0 0 256 256">
                    <path
                        d="M53.92 34.62a8 8 0 1 0-11.84 10.76L58.82 63.8A79.59 79.59 0 0 0 48 104c0 35.34-8.26 62.38-13.81 71.94A16 16 0 0 0 48 200h134.64l19.44 21.38a8 8 0 1 0 11.84-10.76ZM48 184c7.7-13.24 16-43.92 16-80a63.65 63.65 0 0 1 6.26-27.62L168.09 184Zm120 40a8 8 0 0 1-8 8H96a8 8 0 0 1 0-16h64a8 8 0 0 1 8 8Zm46-44.75a8.13 8.13 0 0 1-2.93.55a8 8 0 0 1-7.44-5.08C196.35 156.19 192 129.75 192 104a64 64 0 0 0-95.57-55.69a8 8 0 0 1-7.9-13.91A80 80 0 0 1 208 104c0 35.35 8.05 58.59 10.52 64.88a8 8 0 0 1-4.52 10.37Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- mingcute-announcement-line -->
                <symbol id="announcement" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path
                            d="M24 0v24H0V0h24ZM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018Zm.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01l-.184-.092Z">
                        </path>
                        <path
                            d="M19 4.741V8a3 3 0 1 1 0 6v3c0 1.648-1.881 2.589-3.2 1.6l-2.06-1.546A8.658 8.658 0 0 0 10 15.446v2.844a2.71 2.71 0 0 1-5.316.744l-1.57-5.496a4.7 4.7 0 0 1 3.326-7.73l3.018-.168a9.344 9.344 0 0 0 4.19-1.259l2.344-1.368C17.326 2.236 19 3.197 19 4.741ZM5.634 15.078l.973 3.407A.71.71 0 0 0 8 18.29v-3.01l-1.56-.087a4.723 4.723 0 0 1-.806-.115ZM17 4.741L14.655 6.11A11.343 11.343 0 0 1 10 7.604v5.819c1.787.246 3.488.943 4.94 2.031L17 17V4.741ZM8 7.724l-1.45.08a2.7 2.7 0 0 0-.17 5.377l.17.015l1.45.08V7.724ZM19 10v2a1 1 0 0 0 .117-1.993L19 10Z"
                            fill="currentColor"></path>
                    </g>
                </symbol>
                <!-- clarity-process-on-vm-line -->
                <symbol id="order-process" viewBox="0 0 36 36">
                    <path class="clr-i-outline clr-i-outline-path-1"
                        d="M33.49 26.28a1 1 0 0 0-1.2-.7l-2.49.67a14.23 14.23 0 0 0 2.4-6.75a14.48 14.48 0 0 0-4.83-12.15a1 1 0 0 0-1.37.09a1 1 0 0 0 .09 1.41a12.45 12.45 0 0 1 4.16 10.46a12.19 12.19 0 0 1-2 5.74L28 22.54a1 1 0 1 0-1.95.16l.5 6.44l6.25-1.66a1 1 0 0 0 .69-1.2Z"
                        fill="currentColor"></path>
                    <path class="clr-i-outline clr-i-outline-path-2"
                        d="M4.31 17.08a1.06 1.06 0 0 0 .44.16a1 1 0 0 0 1.12-.85A12.21 12.21 0 0 1 18.69 5.84l-2.24 1.53a1 1 0 0 0 .47 1.79a1 1 0 0 0 .64-.16l5.33-3.66L18.33.76a1 1 0 1 0-1.39 1.38l1.7 1.7A14.2 14.2 0 0 0 3.89 16.12a1 1 0 0 0 .42.96Z"
                        fill="currentColor"></path>
                    <path class="clr-i-outline clr-i-outline-path-3"
                        d="M21.73 29.93a12 12 0 0 1-4.84.51a12.3 12.3 0 0 1-9.57-6.3l2.49.93a1 1 0 0 0 .69-1.84l-4.59-1.7L4.44 21l-1.11 6.35a1 1 0 0 0 .79 1.13h.17a1 1 0 0 0 1-.81l.42-2.4a14.3 14.3 0 0 0 11 7.14a13.91 13.91 0 0 0 5.63-.6a1 1 0 0 0-.6-1.9Z"
                        fill="currentColor"></path>
                    <path class="clr-i-outline clr-i-outline-path-4"
                        d="M22 13h-8a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-8a1 1 0 0 0-1-1Zm-1 8h-6v-6h6Z"
                        fill="currentColor"></path>
                    <path d="M0 0h36v36H0z" fill="none"></path>
                </symbol>
                <!-- carbon-checkmark-outline -->
                <symbol id="outline-check" viewBox="0 0 32 32">
                    <path d="m14 21.414l-5-5.001L10.413 15L14 18.586L21.585 11L23 12.415l-9 8.999z" fill="currentColor">
                    </path>
                    <path d="M16 2a14 14 0 1 0 14 14A14 14 0 0 0 16 2Zm0 26a12 12 0 1 1 12-12a12 12 0 0 1-12 12Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- carbon-warning -->
                <symbol id="warning" viewBox="0 0 32 32">
                    <path d="M16 2a14 14 0 1 0 14 14A14 14 0 0 0 16 2Zm0 26a12 12 0 1 1 12-12a12 12 0 0 1-12 12Z"
                        fill="currentColor"></path>
                    <path d="M15 8h2v11h-2zm1 14a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 16 22z" fill="currentColor"></path>
                </symbol>
                <!-- icon-park-outline-delivery -->
                <symbol id="order-delivered" viewBox="0 0 48 48">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="4">
                        <path d="m20 33l6 2s15-3 17-3s2 2 0 4s-9 8-15 8s-10-3-14-3H4"></path>
                        <path d="M4 29c2-2 6-5 10-5s13.5 4 15 6s-3 5-3 5M16 18v-8a2 2 0 0 1 2-2h24a2 2 0 0 1 2 2v16">
                        </path>
                        <path d="M25 8h10v9H25z"></path>
                    </g>
                </symbol>
                <!-- carbon-delivery -->
                <symbol id="order-sent" viewBox="0 0 32 32">
                    <path d="M4 16h12v2H4zm-2-5h10v2H2z" fill="currentColor"></path>
                    <path
                        d="m29.919 16.606l-3-7A.999.999 0 0 0 26 9h-3V7a1 1 0 0 0-1-1H6v2h15v12.556A3.992 3.992 0 0 0 19.142 23h-6.284a4 4 0 1 0 0 2h6.284a3.98 3.98 0 0 0 7.716 0H29a1 1 0 0 0 1-1v-7a.997.997 0 0 0-.081-.394ZM9 26a2 2 0 1 1 2-2a2.002 2.002 0 0 1-2 2Zm14-15h2.34l2.144 5H23Zm0 15a2 2 0 1 1 2-2a2.002 2.002 0 0 1-2 2Zm5-3h-1.142A3.995 3.995 0 0 0 23 20v-2h5Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- ep-circle-close -->
                <symbol id="order-canceled" viewBox="0 0 1024 1024">
                    <path
                        d="m466.752 512l-90.496-90.496a32 32 0 0 1 45.248-45.248L512 466.752l90.496-90.496a32 32 0 1 1 45.248 45.248L557.248 512l90.496 90.496a32 32 0 1 1-45.248 45.248L512 557.248l-90.496 90.496a32 32 0 0 1-45.248-45.248L466.752 512z"
                        fill="currentColor"></path>
                    <path
                        d="M512 896a384 384 0 1 0 0-768a384 384 0 0 0 0 768zm0 64a448 448 0 1 1 0-896a448 448 0 0 1 0 896z"
                        fill="currentColor"></path>
                </symbol>
                <!-- ic-baseline-pending-actions -->
                <symbol id="order-pending" viewBox="0 0 24 24">
                    <path
                        d="M17 12c-2.76 0-5 2.24-5 5s2.24 5 5 5s5-2.24 5-5s-2.24-5-5-5zm1.65 7.35L16.5 17.2V14h1v2.79l1.85 1.85l-.7.71zM18 3h-3.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H6c-1.1 0-2 .9-2 2v15c0 1.1.9 2 2 2h6.11a6.743 6.743 0 0 1-1.42-2H6V5h2v3h8V5h2v5.08c.71.1 1.38.31 2 .6V5c0-1.1-.9-2-2-2zm-6 2c-.55 0-1-.45-1-1s.45-1 1-1s1 .45 1 1s-.45 1-1 1z"
                        fill="currentColor"></path>
                </symbol>
                <!-- solar:bag-smile-broken -->
                <symbol id="order-current" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5">
                        <path d="M9 6V5a3 3 0 1 1 6 0v1m-5.83 9a3.001 3.001 0 0 0 5.66 0"></path>
                        <path
                            d="M20.224 12.526c-.586-3.121-.878-4.682-1.99-5.604C17.125 6 15.537 6 12.36 6h-.72c-3.176 0-4.764 0-5.875.922c-1.11.922-1.403 2.483-1.989 5.604c-.823 4.389-1.234 6.583-.034 8.029C4.942 22 7.174 22 11.639 22h.722c4.465 0 6.698 0 7.897-1.445c.696-.84.85-1.93.696-3.555">
                        </path>
                    </g>
                </symbol>
                <!-- solar-bag-check-broken -->
                <symbol id="order-delivered" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5">
                        <path d="m10 14.3l1.333 1.2l2.667-3" stroke-linejoin="round"></path>
                        <path
                            d="M9 6V5a3 3 0 1 1 6 0v1m5.224 6.526c-.586-3.121-.878-4.682-1.99-5.604C17.125 6 15.537 6 12.36 6h-.72c-3.176 0-4.764 0-5.875.922c-1.11.922-1.403 2.483-1.989 5.604c-.823 4.389-1.234 6.583-.034 8.029C4.942 22 7.174 22 11.639 22h.722c4.465 0 6.698 0 7.897-1.445c.696-.84.85-1.93.696-3.555">
                        </path>
                    </g>
                </symbol>
                <symbol id="order-returned" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m23.98,15.03c-.78-4.16-1.17-6.24-2.65-7.47-1.48-1.23-3.6-1.23-7.83-1.23h-.96c-4.23,0-6.35,0-7.83,1.23-1.48,1.23-1.87,3.31-2.65,7.47-1.1,5.85-1.65,8.78-.05,10.71,1.6,1.93,4.57,1.93,10.53,1.93h.96c5.95,0,8.93,0,10.53-1.93.93-1.12,1.13-2.57.93-4.74M9.01,6.33v-1.33c0-2.21,1.79-4,4-4s4,1.79,4,4v1.33"
                        fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="5.33"
                        stroke-width="2" />
                    <g>
                        <path d="m9.62,12.07l-2.16,1.85,2.16,2.16" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" />
                        <path d="m7.46,13.92h7.09c2.12,0,3.92,1.73,4.01,3.85.09,2.24-1.76,4.16-4.01,4.16h-5.24"
                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.3" />
                    </g>
                </symbol>

                <!-- solar-bag-cross-broken -->
                <symbol id="order-canceled" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5">
                        <path d="m14 12l-4 4m0-4l4 4M9 6V5a3 3 0 1 1 6 0v1"></path>
                        <path
                            d="M20.224 12.526c-.586-3.121-.878-4.682-1.99-5.604C17.125 6 15.537 6 12.36 6h-.72c-3.176 0-4.764 0-5.875.922c-1.11.922-1.403 2.483-1.989 5.604c-.823 4.389-1.234 6.583-.034 8.029C4.942 22 7.174 22 11.639 22h.722c4.465 0 6.698 0 7.897-1.445c.696-.84.85-1.93.696-3.555">
                        </path>
                    </g>
                </symbol>
                <!-- solar-lock-password-broken -->
                <symbol id="password-lock" viewBox="0 0 24 24">
                    <g fill="none">
                        <path
                            d="M9 16a1 1 0 1 1-2 0a1 1 0 0 1 2 0Zm4 0a1 1 0 1 1-2 0a1 1 0 0 1 2 0Zm4 0a1 1 0 1 1-2 0a1 1 0 0 1 2 0Z"
                            fill="currentColor"></path>
                        <path
                            d="M6 10V8c0-.34.028-.675.083-1M18 10V8A6 6 0 0 0 7.5 4.031M11 22H8c-2.828 0-4.243 0-5.121-.879C2 20.243 2 18.828 2 16c0-2.828 0-4.243.879-5.121C3.757 10 5.172 10 8 10h8c2.828 0 4.243 0 5.121.879C22 11.757 22 13.172 22 16c0 2.828 0 4.243-.879 5.121C20.243 22 18.828 22 16 22h-1"
                            stroke="currentColor" stroke-linecap="round" stroke-width="1.5"></path>
                    </g>
                </symbol>
                <!-- tabler-user-cog -->
                <symbol id="user-setting" viewBox="0 0 24 24">
                    <path
                        d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0-8 0M6 21v-2a4 4 0 0 1 4-4h2.5m4.501 4a2 2 0 1 0 4 0a2 2 0 1 0-4 0m2-3.5V17m0 4v1.5m3.031-5.25l-1.299.75m-3.463 2l-1.3.75m0-3.5l1.3.75m3.463 2l1.3.75"
                        fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"></path>
                </symbol>
                <!-- copy carbon-copy -->
                <symbol id="copy" viewBox="0 0 32 32">
                    <path
                        d="M28 10v18H10V10h18m0-2H10a2 2 0 0 0-2 2v18a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2Z"
                        fill="currentColor"></path>
                    <path d="M4 18H2V4a2 2 0 0 1 2-2h14v2H4Z" fill="currentColor"></path>
                </symbol>
                <!-- refund streamline-interface-time-rewind-back-return-clock-timer-countdown -->
                <symbol id="refund" viewBox="0 0 14 14">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M.5 7A6.5 6.5 0 1 0 7 .5a7.23 7.23 0 0 0-5 2"></path>
                        <path d="m2.5.5l-.5 2L4 3m3 .5v4L4.4 8.8"></path>
                    </g>
                </symbol>
                <!-- fast-delivery carbon-delivery-parcel -->
                <symbol id="fast-delivery" viewBox="0 0 32 32">
                    <path
                        d="m29.482 8.624l-10-5.5a1 1 0 0 0-.964 0l-10 5.5a1 1 0 0 0 0 1.752L18 15.591V26.31l-3.036-1.67L14 26.391l4.518 2.485a.998.998 0 0 0 .964 0l10-5.5A1 1 0 0 0 30 22.5v-13a1 1 0 0 0-.518-.876ZM19 5.142L26.925 9.5L19 13.858L11.075 9.5Zm9 16.767l-8 4.4V15.59l8-4.4Z"
                        fill="currentColor"></path>
                    <path d="M10 16H2v-2h8zm2 8H4v-2h8zm2-4H6v-2h8z" fill="currentColor"></path>
                </symbol>
                <!-- support -->
                <symbol aria-hidden="true" id="support" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20 11a8 8 0 1 0-16 0" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                            d="M2 15.438v-1.876a2 2 0 0 1 1.515-1.94l1.74-.436a.6.6 0 0 1 .745.582v5.463a.6.6 0 0 1-.746.583l-1.74-.435A2 2 0 0 1 2 15.439Zm20 0v-1.876a2 2 0 0 0-1.515-1.94l-1.74-.436a.6.6 0 0 0-.745.582v5.463a.6.6 0 0 0 .745.583l1.74-.435A2 2 0 0 0 22 15.439ZM20 18v.5a2 2 0 0 1-2 2h-3.5">
                        </path>
                        <path d="M13.5 22h-3a1.5 1.5 0 0 1 0-3h3a1.5 1.5 0 0 1 0 3Z"></path>
                    </g>
                </symbol>
                <!-- verified material-symbols-verified-user-outline-rounded -->
                <symbol id="verified" viewBox="0 0 24 24">
                    <path
                        d="m10.95 12.7l-1.4-1.4q-.3-.3-.7-.3t-.7.3q-.3.3-.3.713t.3.712l2.1 2.125q.3.3.7.3t.7-.3l4.25-4.25q.3-.3.3-.712t-.3-.713q-.3-.3-.713-.3t-.712.3L10.95 12.7ZM12 21.9q-.175 0-.325-.025t-.3-.075Q8 20.675 6 17.638T4 11.1V6.375q0-.625.363-1.125t.937-.725l6-2.25q.35-.125.7-.125t.7.125l6 2.25q.575.225.938.725T20 6.375V11.1q0 3.5-2 6.538T12.625 21.8q-.15.05-.3.075T12 21.9Zm0-2q2.6-.825 4.3-3.3t1.7-5.5V6.375l-6-2.25l-6 2.25V11.1q0 3.025 1.7 5.5t4.3 3.3Zm0-7.9Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- social-share mdi-share-variant-outline -->
                <symbol id="social-share" viewBox="0 0 24 24">
                    <path
                        d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81c1.66 0 3-1.34 3-3s-1.34-3-3-3s-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.15c-.05.21-.08.43-.08.66c0 1.61 1.31 2.91 2.92 2.91s2.92-1.3 2.92-2.91s-1.31-2.92-2.92-2.92M18 4c.55 0 1 .45 1 1s-.45 1-1 1s-1-.45-1-1s.45-1 1-1M6 13c-.55 0-1-.45-1-1s.45-1 1-1s1 .45 1 1s-.45 1-1 1m12 7c-.55 0-1-.45-1-1s.45-1 1-1s1 .45 1 1s-.45 1-1 1Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- compare mdi-select-compare -->
                <symbol id="compare" viewBox="0 0 24 24">
                    <path
                        d="M13 23h-2V1h2v22m-4-4H5V5h4V3H5c-1.11 0-2 .89-2 2v14a2 2 0 0 0 2 2h4v-2M19 7v2h2V7h-2m0-2h2a2 2 0 0 0-2-2v2m2 10h-2v2h2v-2m-2-4v2h2v-2h-2m-2-8h-2v2h2V3m2 18c1.11 0 2-.89 2-2h-2v2m-2-2h-2v2h2v-2Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- like solar-like-linear -->
                <symbol id="like" viewBox="0 0 24 24">
                    <path
                        d="m20.975 12.185l-.739-.128l.74.128Zm-.705 4.08l-.74-.128l.74.128ZM6.938 20.477l-.747.065l.747-.065Zm-.812-9.393l.747-.064l-.747.064Zm7.869-5.863l.74.122l-.74-.122Zm-.663 4.045l.74.121l-.74-.121Zm-6.634.411l-.49-.568l.49.568Zm1.439-1.24l.49.569l-.49-.568Zm2.381-3.653l-.726-.189l.726.189Zm.476-1.834l.726.188l-.726-.188Zm1.674-.886l-.23.714l.23-.714Zm.145.047l.229-.714l-.23.714ZM9.862 6.463l.662.353l-.662-.353Zm4.043-3.215l-.726.188l.726-.188Zm-2.23-1.116l-.326-.675l.325.675ZM3.971 21.471l-.748.064l.748-.064ZM3 10.234l.747-.064a.75.75 0 0 0-1.497.064H3Zm17.236 1.823l-.705 4.08l1.478.256l.705-4.08l-1.478-.256Zm-6.991 9.193H8.596v1.5h4.649v-1.5Zm-5.56-.837l-.812-9.393l-1.495.129l.813 9.393l1.494-.13Zm11.846-4.276c-.507 2.93-3.15 5.113-6.286 5.113v1.5c3.826 0 7.126-2.669 7.764-6.357l-1.478-.256ZM13.255 5.1l-.663 4.045l1.48.242l.663-4.044l-1.48-.243Zm-6.067 5.146l1.438-1.24l-.979-1.136L6.21 9.11l.979 1.136Zm4.056-5.274l.476-1.834l-1.452-.376l-.476 1.833l1.452.377Zm1.194-2.194l.145.047l.459-1.428l-.145-.047l-.459 1.428Zm-1.915 4.038a8.378 8.378 0 0 0 .721-1.844l-1.452-.377A6.878 6.878 0 0 1 9.2 6.11l1.324.707Zm2.06-3.991a.885.885 0 0 1 .596.61l1.452-.376a2.385 2.385 0 0 0-1.589-1.662l-.459 1.428Zm-.863.313a.515.515 0 0 1 .28-.33l-.651-1.351c-.532.256-.932.73-1.081 1.305l1.452.376Zm.28-.33a.596.596 0 0 1 .438-.03l.459-1.428a2.096 2.096 0 0 0-1.548.107l.65 1.351Zm2.154 8.176h5.18v-1.5h-5.18v1.5ZM4.719 21.406L3.747 10.17l-1.494.129l.971 11.236l1.495-.129Zm-.969.107V10.234h-1.5v11.279h1.5Zm-.526.022a.263.263 0 0 1 .263-.285v1.5c.726 0 1.294-.622 1.232-1.344l-1.495.13ZM14.735 5.343a5.533 5.533 0 0 0-.104-2.284l-1.452.377a4.03 4.03 0 0 1 .076 1.664l1.48.243ZM8.596 21.25a.916.916 0 0 1-.911-.837l-1.494.129a2.416 2.416 0 0 0 2.405 2.208v-1.5Zm.03-12.244c.68-.586 1.413-1.283 1.898-2.19L9.2 6.109c-.346.649-.897 1.196-1.553 1.76l.98 1.137Zm13.088 3.307a2.416 2.416 0 0 0-2.38-2.829v1.5c.567 0 1 .512.902 1.073l1.478.256ZM3.487 21.25c.146 0 .263.118.263.263h-1.5c0 .682.553 1.237 1.237 1.237v-1.5Zm9.105-12.105a1.583 1.583 0 0 0 1.562 1.84v-1.5a.083.083 0 0 1-.082-.098l-1.48-.242Zm-5.72 1.875a.918.918 0 0 1 .316-.774l-.98-1.137a2.418 2.418 0 0 0-.83 2.04l1.495-.13Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- dislike  solar-dislike-linear-->
                <symbol id="dislike" viewBox="0 0 24 24">
                    <path
                        d="m20.975 11.815l-.739.128l.74-.128Zm-.705-4.08l-.74.128l.74-.128ZM6.938 3.523l-.747-.065l.747.065Zm-.812 9.393l.747.064l-.747-.064Zm7.869 5.863l.74-.122l-.74.122Zm-.663-4.045l.74-.121l-.74.121Zm-6.634-.412l-.49.569l.49-.569Zm1.439 1.24l.49-.568l-.49.568Zm2.381 3.654l-.726.189l.726-.189Zm.476 1.834l.726-.188l-.726.188Zm1.674.886l-.23-.714l.23.714Zm.145-.047l.229.714l-.23-.714Zm-2.951-4.352l.662-.353l-.662.353Zm4.043 3.216l-.726-.189l.726.189Zm-2.23 1.115l-.326.675l.325-.675ZM3.971 2.529l-.748-.064l.748.064ZM3 13.766l.747.064a.75.75 0 0 1-1.497-.064H3Zm17.236-1.823l-.705-4.08l1.478-.256l.705 4.08l-1.478.256ZM13.245 2.75H8.596v-1.5h4.649v1.5Zm-5.56.838l-.812 9.392l-1.495-.129l.813-9.393l1.494.13Zm11.846 4.275c-.507-2.93-3.15-5.113-6.286-5.113v-1.5c3.826 0 7.126 2.669 7.764 6.357l-1.478.256ZM13.255 18.9l-.663-4.045l1.48-.242l.663 4.044l-1.48.243Zm-6.067-5.146l1.438 1.24l-.979 1.137l-1.438-1.24l.979-1.137Zm4.056 5.274l.476 1.834l-1.452.376l-.476-1.833l1.452-.377Zm1.194 2.194l.145-.047l.459 1.428l-.145.047l-.459-1.428Zm-1.915-4.038c.312.584.555 1.203.721 1.844l-1.452.377A6.877 6.877 0 0 0 9.2 17.89l1.324-.707Zm2.06 3.991a.885.885 0 0 0 .596-.61l1.452.376a2.385 2.385 0 0 1-1.589 1.662l-.459-1.428Zm-.863-.313a.513.513 0 0 0 .28.33l-.651 1.351a2.014 2.014 0 0 1-1.081-1.305l1.452-.376Zm.28.33a.596.596 0 0 0 .438.03l.459 1.428a2.096 2.096 0 0 1-1.548-.107l.65-1.351Zm2.154-8.176h5.18v1.5h-5.18v-1.5ZM4.719 2.594L3.747 13.83l-1.494-.129l.971-11.236l1.495.129Zm-.969-.107v11.279h-1.5V2.487h1.5Zm-.526-.022a.263.263 0 0 0 .263.285v-1.5c.726 0 1.294.622 1.232 1.344l-1.495-.13Zm11.511 16.192c.125.76.09 1.538-.104 2.284l-1.452-.377c.14-.543.167-1.11.076-1.664l1.48-.243ZM8.596 2.75a.916.916 0 0 0-.911.838l-1.494-.13A2.416 2.416 0 0 1 8.596 1.25v1.5Zm.03 12.244c.68.586 1.413 1.283 1.898 2.19l-1.324.707c-.346-.649-.897-1.196-1.553-1.76l.98-1.137Zm13.088-3.307a2.416 2.416 0 0 1-2.38 2.829v-1.5a.916.916 0 0 0 .902-1.073l1.478-.256ZM3.487 2.75a.263.263 0 0 0 .263-.263h-1.5c0-.682.553-1.237 1.237-1.237v1.5Zm9.105 12.105a1.583 1.583 0 0 1 1.562-1.84v1.5c-.05 0-.09.046-.082.098l-1.48.242Zm-5.72-1.875a.918.918 0 0 0 .316.774l-.98 1.137a2.418 2.418 0 0 1-.83-2.04l1.495.13Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- vertical-dot  mdi-dots-vertical-->
                <symbol id="vertical-dot" viewBox="0 0 24 24">
                    <path
                        d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- horizontal-dot mdi-dots-horizontal -->
                <symbol id="horizontal-dot" viewBox="0 0 24 24">
                    <path
                        d="M16 12a2 2 0 0 1 2-2a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2m-6 0a2 2 0 0 1 2-2a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2m-6 0a2 2 0 0 1 2-2a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- material-symbols-location-on-outline -->
                <symbol id="address" viewBox="0 0 24 24">
                    <path
                        d="M12 12q.825 0 1.413-.588T14 10q0-.825-.588-1.413T12 8q-.825 0-1.413.588T10 10q0 .825.588 1.413T12 12Zm0 7.35q3.05-2.8 4.525-5.088T18 10.2q0-2.725-1.738-4.462T12 4Q9.475 4 7.737 5.738T6 10.2q0 1.775 1.475 4.063T12 19.35ZM12 22q-4.025-3.425-6.012-6.362T4 10.2q0-3.75 2.413-5.975T12 2q3.175 0 5.588 2.225T20 10.2q0 2.5-1.988 5.438T12 22Zm0-12Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- address-add material-symbols-add-location-alt-outline -->
                <symbol id="address-add" viewBox="0 0 24 24">
                    <path
                        d="M12 22q-4.025-3.425-6.012-6.362T4 10.2q0-3.75 2.413-5.975T12 2h.5q.25 0 .5.05v2.025q-.25-.05-.488-.063T12 4Q9.475 4 7.737 5.738T6 10.2q0 1.775 1.475 4.063T12 19.35q3.05-2.8 4.525-5.088T18 10.2V10h2v.2q0 2.5-1.988 5.438T12 22Zm0-10q.825 0 1.413-.588T14 10q0-.825-.588-1.413T12 8q-.825 0-1.413.588T10 10q0 .825.588 1.413T12 12Zm0-2Zm6-2h2V5h3V3h-3V0h-2v3h-3v2h3v3Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- address-edit material-symbols-edit-location-alt-outline -->
                <symbol id="address-edit" viewBox="0 0 24 24">
                    <path
                        d="M12 22q-4.025-3.425-6.012-6.362T4 10.2q0-3.75 2.413-5.975T12 2q.675 0 1.338.113t1.287.312L13 4.075q-.25-.05-.488-.063T12 4Q9.475 4 7.737 5.738T6 10.2q0 1.775 1.475 4.063T12 19.35q3.05-2.8 4.525-5.088T18 10.2q0-.3-.025-.6t-.075-.575l1.65-1.65q.225.65.338 1.35T20 10.2q0 2.5-1.988 5.438T12 22Zm0-11.8Zm6.35-6.35L17.2 2.7L11 8.9V11h2.1l6.2-6.2l-.95-.95ZM20 4.1l.7-.7q.275-.275.275-.7T20.7 2l-.7-.7q-.275-.275-.7-.275t-.7.275l-.7.7L20 4.1Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- address-delete material-symbols-wrong-location-outline -->
                <symbol id="address-delete" viewBox="0 0 24 24">
                    <path
                        d="M12 22q-4.025-3.425-6.012-6.362T4 10.2q0-3.75 2.413-5.975T12 2q.25 0 .488.013t.512.062V4.1q-.25-.05-.5-.075T12 4Q9.475 4 7.737 5.737T6 10.2q0 1.775 1.475 4.063T12 19.35q3.05-2.8 4.525-5.088T18 10.2q0-.05-.013-.1t-.012-.1h2q0 .05.013.1t.012.1q0 2.5-1.988 5.438T12 22Zm0-11.25Zm4.875-2.7l2.1-2.1l2.1 2.1l1.4-1.4l-2.1-2.1l2.1-2.1l-1.4-1.4l-2.1 2.1l-2.1-2.1l-1.4 1.4l2.1 2.1l-2.1 2.1l1.4 1.4ZM12 12q.825 0 1.413-.588T14 10q0-.825-.588-1.413T12 8q-.825 0-1.413.588T10 10q0 .825.588 1.413T12 12Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- credit fluent-wallet-credit-card-32-regular -->
                <symbol id="credit" viewBox="0 0 32 32">
                    <path
                        d="M18.963 4.188a.5.5 0 0 1 .705-.08l1.08.866L16.799 10h2.544l2.968-3.776l2.504 2.006a.5.5 0 0 1 .066.717L23.973 10h2.614a2.5 2.5 0 0 0-.522-3.331l-5.147-4.122a2.5 2.5 0 0 0-3.522.399L11.809 10h2.551l4.603-5.812ZM21 19a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3ZM6 7a3 3 0 0 0-3 3v14.5A4.5 4.5 0 0 0 7.5 29h17a4.5 4.5 0 0 0 4.5-4.5v-9a4.5 4.5 0 0 0-4.5-4.5H6a1 1 0 1 1 0-2h4.58l1.596-2H6ZM5 24.5V12.83c.313.11.65.17 1 .17h18.5a2.5 2.5 0 0 1 2.5 2.5v9a2.5 2.5 0 0 1-2.5 2.5h-17A2.5 2.5 0 0 1 5 24.5Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- delivery-truck mdi-truck-fast-outline -->
                <symbol id="delivery-truck" viewBox="0 0 24 24">
                    <path
                        d="M.75 7.5h9.75l.75 1.5H1.5L.75 7.5m1 3h9.75l.75 1.5H2.5l-.75-1.5m16.25 8c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5s.67 1.5 1.5 1.5m1.5-9H17V12h4.46L19.5 9.5M8 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5s.67 1.5 1.5 1.5M20 8l3 4v5h-2c0 1.66-1.34 3-3 3s-3-1.34-3-3h-4c0 1.66-1.35 3-3 3c-1.66 0-3-1.34-3-3H3v-3.5h2V15h.76c.55-.61 1.35-1 2.24-1c.89 0 1.69.39 2.24 1H15V6H3c0-1.11.89-2 2-2h12v4h3Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- check material-symbols-fitbit-check-small-rounded -->

                <symbol id="check" viewBox="0 0 24 24">
                    <path
                        d="m10.5 13.4l4.9-4.9q.275-.275.7-.275t.7.275q.275.275.275.7t-.275.7l-5.6 5.6q-.3.3-.7.3t-.7-.3l-2.6-2.6q-.275-.275-.275-.7t.275-.7q.275-.275.7-.275t.7.275l1.9 1.9Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- available-in-store mdi-store-check-outline -->
                <symbol id="available-in-store" viewBox="0 0 24 24">
                    <path
                        d="M19 13c.7 0 1.37.13 2 .35V12l-1-5H4l-1 5v2h1v6h9.09c-.05-.33-.09-.66-.09-1c0-1.23.37-2.36 1-3.31V14h1.69c.95-.63 2.08-1 3.31-1m-7 5H6v-4h6v4m-6.96-6l.6-3h12.72l.6 3H5.04M20 6H4V4h16v2m2.5 11.25L17.75 22L15 19l1.16-1.16l1.59 1.59l3.59-3.59l1.16 1.41"
                        fill="currentColor"></path>
                </symbol>
                <!-- instagram mdi-instagram -->
                <symbol id="instagram" viewBox="0 0 24 24">
                    <path
                        d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8A1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5a5 5 0 0 1-5 5a5 5 0 0 1-5-5a5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- twitter ri-twitter-x-fill -->

                <symbol id="twitter" viewBox="0 0 24 24">
                    <path
                        d="M18.205 2.25h3.308l-7.227 8.26l8.502 11.24H16.13l-5.214-6.817L4.95 21.75H1.64l7.73-8.835L1.215 2.25H8.04l4.713 6.231l5.45-6.231Zm-1.161 17.52h1.833L7.045 4.126H5.078L17.044 19.77Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- aparat simple-icons-aparat -->
                <symbol id="aparat" viewBox="0 0 24 24">
                    <path
                        d="M12.001 1.594c-9.27-.003-13.913 11.203-7.36 17.758a10.403 10.403 0 0 0 17.76-7.355c0-5.744-4.655-10.401-10.4-10.403zM6.11 6.783c.501-2.598 3.893-3.294 5.376-1.103c1.483 2.19-.422 5.082-3.02 4.582A2.97 2.97 0 0 1 6.11 6.783zm4.322 8.988c-.504 2.597-3.897 3.288-5.377 1.096c-1.48-2.192.427-5.08 3.025-4.579a2.97 2.97 0 0 1 2.352 3.483zm1.26-2.405c-1.152-.223-1.462-1.727-.491-2.387c.97-.66 2.256.18 2.04 1.334a1.32 1.32 0 0 1-1.548 1.053zm6.198 3.838c-.501 2.598-3.893 3.293-5.376 1.103c-1.484-2.191.421-5.082 3.02-4.583a2.97 2.97 0 0 1 2.356 3.48zm-1.967-5.502c-2.598-.501-3.293-3.896-1.102-5.38c2.19-1.483 5.081.422 4.582 3.02a2.97 2.97 0 0 1-3.48 2.36zM13.59 23.264l2.264.61a3.715 3.715 0 0 0 4.543-2.636l.64-2.402a11.383 11.383 0 0 1-7.448 4.428zm7.643-19.665L18.87 2.97a11.376 11.376 0 0 1 4.354 7.62l.65-2.459A3.715 3.715 0 0 0 21.231 3.6zM.672 13.809l-.541 2.04a3.715 3.715 0 0 0 2.636 4.543l2.107.562a11.38 11.38 0 0 1-4.203-7.145zM10.357.702L8.15.126a3.715 3.715 0 0 0-4.547 2.637l-.551 2.082A11.376 11.376 0 0 1 10.358.702Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- chevron down mdi-chevron-down -->
                <symbol id="chevron-down" viewBox="0 0 24 24">
                    <path d="M7.41 8.58L12 13.17l4.59-4.59L18 10l-6 6l-6-6l1.41-1.42Z" fill="currentColor"></path>
                </symbol>
                <!-- chevron up mdi-chevron-up -->

                <symbol id="chevron-up" viewBox="0 0 24 24">
                    <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6l-6 6l1.41 1.41Z" fill="currentColor"></path>
                </symbol>
                <!-- chevron right mdi-chevron-right -->

                <symbol id="chevron-right" viewBox="0 0 24 24">
                    <path d="M8.59 16.58L13.17 12L8.59 7.41L10 6l6 6l-6 6l-1.41-1.42Z" fill="currentColor"></path>
                </symbol>
                <!-- chevron left mdi-chevron-left -->

                <symbol id="chevron-left" viewBox="0 0 24 24">
                    <path d="M15.41 16.58L10.83 12l4.58-4.59L14 6l-6 6l6 6l1.41-1.42Z" fill="currentColor"></path>
                </symbol>
                <!-- dashboard radix-icons:dashboard -->
                <symbol id="dashboard" viewBox="0 0 15 15">
                    <path clip-rule="evenodd"
                        d="M2.8 1h-.05c-.229 0-.426 0-.6.041A1.5 1.5 0 0 0 1.04 2.15c-.04.174-.04.37-.04.6v2.5c0 .229 0 .426.041.6A1.5 1.5 0 0 0 2.15 6.96c.174.04.37.04.6.04h2.5c.229 0 .426 0 .6-.041A1.5 1.5 0 0 0 6.96 5.85c.04-.174.04-.37.04-.6v-2.5c0-.229 0-.426-.041-.6A1.5 1.5 0 0 0 5.85 1.04C5.676 1 5.48 1 5.25 1H2.8Zm-.417 1.014c.043-.01.11-.014.417-.014h2.4c.308 0 .374.003.417.014a.5.5 0 0 1 .37.37c.01.042.013.108.013.416v2.4c0 .308-.003.374-.014.417a.5.5 0 0 1-.37.37C5.575 5.996 5.509 6 5.2 6H2.8c-.308 0-.374-.003-.417-.014a.5.5 0 0 1-.37-.37C2.004 5.575 2 5.509 2 5.2V2.8c0-.308.003-.374.014-.417a.5.5 0 0 1 .37-.37ZM9.8 1h-.05c-.229 0-.426 0-.6.041A1.5 1.5 0 0 0 8.04 2.15c-.04.174-.04.37-.04.6v2.5c0 .229 0 .426.041.6A1.5 1.5 0 0 0 9.15 6.96c.174.04.37.04.6.04h2.5c.229 0 .426 0 .6-.041a1.5 1.5 0 0 0 1.11-1.109c.04-.174.04-.37.04-.6v-2.5c0-.229 0-.426-.041-.6a1.5 1.5 0 0 0-1.109-1.11c-.174-.04-.37-.04-.6-.04H9.8Zm-.417 1.014c.043-.01.11-.014.417-.014h2.4c.308 0 .374.003.417.014a.5.5 0 0 1 .37.37c.01.042.013.108.013.416v2.4c0 .308-.004.374-.014.417a.5.5 0 0 1-.37.37c-.042.01-.108.013-.416.013H9.8c-.308 0-.374-.003-.417-.014a.5.5 0 0 1-.37-.37C9.004 5.575 9 5.509 9 5.2V2.8c0-.308.003-.374.014-.417a.5.5 0 0 1 .37-.37ZM2.75 8h2.5c.229 0 .426 0 .6.041A1.5 1.5 0 0 1 6.96 9.15c.04.174.04.37.04.6v2.5c0 .229 0 .426-.041.6a1.5 1.5 0 0 1-1.109 1.11c-.174.04-.37.04-.6.04h-2.5c-.229 0-.426 0-.6-.041a1.5 1.5 0 0 1-1.11-1.109c-.04-.174-.04-.37-.04-.6v-2.5c0-.229 0-.426.041-.6A1.5 1.5 0 0 1 2.15 8.04c.174-.04.37-.04.6-.04Zm.05 1c-.308 0-.374.003-.417.014a.5.5 0 0 0-.37.37C2.004 9.425 2 9.491 2 9.8v2.4c0 .308.003.374.014.417a.5.5 0 0 0 .37.37c.042.01.108.013.416.013h2.4c.308 0 .374-.004.417-.014a.5.5 0 0 0 .37-.37c.01-.042.013-.108.013-.416V9.8c0-.308-.003-.374-.014-.417a.5.5 0 0 0-.37-.37C5.575 9.004 5.509 9 5.2 9H2.8Zm7-1h-.05c-.229 0-.426 0-.6.041A1.5 1.5 0 0 0 8.04 9.15c-.04.174-.04.37-.04.6v2.5c0 .229 0 .426.041.6a1.5 1.5 0 0 0 1.109 1.11c.174.041.371.041.6.041h2.5c.229 0 .426 0 .6-.041a1.5 1.5 0 0 0 1.109-1.109c.041-.174.041-.371.041-.6V9.75c0-.229 0-.426-.041-.6a1.5 1.5 0 0 0-1.109-1.11c-.174-.04-.37-.04-.6-.04H9.8Zm-.417 1.014c.043-.01.11-.014.417-.014h2.4c.308 0 .374.003.417.014a.5.5 0 0 1 .37.37c.01.042.013.108.013.416v2.4c0 .308-.004.374-.014.417a.5.5 0 0 1-.37.37c-.042.01-.108.013-.416.013H9.8c-.308 0-.374-.004-.417-.014a.5.5 0 0 1-.37-.37C9.004 12.575 9 12.509 9 12.2V9.8c0-.308.003-.374.014-.417a.5.5 0 0 1 .37-.37Z"
                        fill="currentColor" fill-rule="evenodd"></path>
                </symbol>
                <!-- burger heroicons:bars-3 -->

                <symbol id="menu" viewBox="0 0 24 24">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" fill="none" stroke="currentColor"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                </symbol>
                <!--  Cart ph:shopping-cart-simple -->
                <symbol id="cart" viewBox="0 0 256 256">
                    <path
                        d="M96 216a16 16 0 1 1-16-16a16 16 0 0 1 16 16Zm88-16a16 16 0 1 0 16 16a16 16 0 0 0-16-16Zm47.65-125.65l-28.53 92.71A23.89 23.89 0 0 1 180.18 184H84.07A24.11 24.11 0 0 1 61 166.59L24.82 40H8a8 8 0 0 1 0-16h16.82a16.08 16.08 0 0 1 15.39 11.6L48.32 64H224a8 8 0 0 1 7.65 10.35ZM213.17 80H52.89l23.49 82.2a8 8 0 0 0 7.69 5.8h96.11a8 8 0 0 0 7.65-5.65Z"
                        fill="currentColor"></path>
                </symbol>
                <!--  order solar:bag-3-linear -->

                <symbol id="order" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                        <path
                            d="M3.742 18.555C4.942 20 7.174 20 11.639 20h.722c4.465 0 6.698 0 7.898-1.445m-16.517 0c-1.2-1.446-.788-3.64.035-8.03c.585-3.12.877-4.681 1.988-5.603M3.742 18.555Zm16.517 0c1.2-1.446.788-3.64-.035-8.03c-.585-3.12-.878-4.681-1.989-5.603m2.024 13.633ZM18.235 4.922C17.125 4 15.536 4 12.361 4h-.722c-3.175 0-4.763 0-5.874.922m12.47 0Zm-12.47 0Z">
                        </path>
                        <path d="M9.17 8a3.001 3.001 0 0 0 5.66 0" stroke-linecap="round"></path>
                    </g>
                </symbol>

                <!--  heart ph:heart-straight -->

                <symbol id="heart" viewBox="0 0 256 256">
                    <path
                        d="M223 57a58.07 58.07 0 0 0-81.92-.1L128 69.05l-13.09-12.19A58 58 0 0 0 33 139l89.35 90.66a8 8 0 0 0 11.4 0L223 139a58 58 0 0 0 0-82Zm-11.35 70.76L128 212.6l-83.7-84.92a42 42 0 0 1 59.4-59.4l.2.2l18.65 17.35a8 8 0 0 0 10.9 0l18.65-17.35l.2-.2a42 42 0 1 1 59.36 59.44Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- mdi-heart-off-outline -->
                <symbol id="heart-off" viewBox="0 0 24 24">
                    <path
                        d="M2.39 1.73L1.11 3l2.08 2.08C2.45 6 2 7.19 2 8.5c0 3.77 3.4 6.86 8.55 11.53L12 21.35l1.45-1.32c.87-.79 1.69-1.53 2.45-2.24L20 22l1.27-1.27m-9.17-2.18l-.1.1l-.11-.1C7.14 14.24 4 11.39 4 8.5c0-.76.22-1.44.61-2l9.89 9.87c-.76.69-1.55 1.41-2.4 2.18M8.3 5.1L6.33 3.13C6.7 3.05 7.1 3 7.5 3c1.74 0 3.41.81 4.5 2.08C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.41 22 8.5c0 2.34-1.31 4.42-3.53 6.77l-1.41-1.41C18.91 11.88 20 10.2 20 8.5c0-2-1.5-3.5-3.5-3.5c-1.4 0-2.76.83-3.39 2h-2.22c-.51-.94-1.5-1.66-2.59-1.9Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- User solar:user-outline -->
                <symbol id="user" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd"
                        d="M12 1.25a4.75 4.75 0 1 0 0 9.5a4.75 4.75 0 0 0 0-9.5ZM8.75 6a3.25 3.25 0 1 1 6.5 0a3.25 3.25 0 0 1-6.5 0ZM12 12.25c-2.313 0-4.445.526-6.024 1.414C4.42 14.54 3.25 15.866 3.25 17.5v.102c-.001 1.162-.002 2.62 1.277 3.662c.629.512 1.51.877 2.7 1.117c1.192.242 2.747.369 4.773.369s3.58-.127 4.774-.369c1.19-.24 2.07-.605 2.7-1.117c1.279-1.042 1.277-2.5 1.276-3.662V17.5c0-1.634-1.17-2.96-2.725-3.836c-1.58-.888-3.711-1.414-6.025-1.414ZM4.75 17.5c0-.851.622-1.775 1.961-2.528c1.316-.74 3.184-1.222 5.29-1.222c2.104 0 3.972.482 5.288 1.222c1.34.753 1.961 1.677 1.961 2.528c0 1.308-.04 2.044-.724 2.6c-.37.302-.99.597-2.05.811c-1.057.214-2.502.339-4.476.339c-1.974 0-3.42-.125-4.476-.339c-1.06-.214-1.68-.509-2.05-.81c-.684-.557-.724-1.293-.724-2.601Z"
                        fill="currentColor" fill-rule="evenodd" />
                </symbol>
                <!-- Close -->
                <symbol aria-hidden="true" fill="currentColor" id="close" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        fill-rule="evenodd"></path>
                </symbol>
                <!-- Search  iconoir:search -->
                <symbol id="search" viewBox="0 0 24 24">
                    <path d="m17 17l4 4M3 11a8 8 0 1 0 16 0a8 8 0 0 0-16 0Z" fill="none" stroke="currentColor"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                </symbol>
                <symbol id="exit" viewBox="0 0 20 20">
                    <path
                        d="M12.5 17a.5.5 0 0 0 0-1H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6.5a.5.5 0 0 0 0-1H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h6.5Zm1.146-10.854a.5.5 0 0 1 .708 0l3.5 3.5a.5.5 0 0 1 0 .708l-3.5 3.5a.5.5 0 0 1-.708-.708l2.647-2.646H7.5a.5.5 0 0 1 0-1h8.793l-2.647-2.646a.5.5 0 0 1 0-.708Z"
                        fill="currentColor"></path>
                </symbol>

                <!-- edit  solar:pen-new-square-broken -->
                <symbol id="edit" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5">
                        <path
                            d="M2 12c0 4.714 0 7.071 1.464 8.535C4.93 22 7.286 22 12 22c4.714 0 7.071 0 8.535-1.465C22 19.072 22 16.714 22 12v-1.5M13.5 2H12C7.286 2 4.929 2 3.464 3.464c-.973.974-1.3 2.343-1.409 4.536">
                        </path>
                        <path
                            d="m16.652 3.455l.649-.649A2.753 2.753 0 0 1 21.194 6.7l-.65.649m-3.892-3.893s.081 1.379 1.298 2.595c1.216 1.217 2.595 1.298 2.595 1.298m-3.893-3.893L10.687 9.42c-.404.404-.606.606-.78.829c-.205.262-.38.547-.524.848c-.121.255-.211.526-.392 1.068L8.412 13.9m12.133-6.552l-2.983 2.982m-2.982 2.983c-.404.404-.606.606-.829.78a4.59 4.59 0 0 1-.848.524c-.255.121-.526.211-1.068.392l-1.735.579m0 0l-1.123.374a.742.742 0 0 1-.939-.94l.374-1.122m1.688 1.688L8.412 13.9">
                        </path>
                    </g>
                </symbol>
                <!-- notification  ph:bell -->
                <symbol id="notification" viewBox="0 0 256 256">
                    <path
                        d="M221.8 175.94c-5.55-9.56-13.8-36.61-13.8-71.94a80 80 0 1 0-160 0c0 35.34-8.26 62.38-13.81 71.94A16 16 0 0 0 48 200h40.81a40 40 0 0 0 78.38 0H208a16 16 0 0 0 13.8-24.06ZM128 216a24 24 0 0 1-22.62-16h45.24A24 24 0 0 1 128 216Zm-80-32c7.7-13.24 16-43.92 16-80a64 64 0 1 1 128 0c0 36.05 8.28 66.73 16 80Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- home solar-home-smile-linear -->
                <symbol id="home" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-width="1">
                        <path
                            d="M2 12.204c0-2.289 0-3.433.52-4.381c.518-.949 1.467-1.537 3.364-2.715l2-1.241C9.889 2.622 10.892 2 12 2c1.108 0 2.11.622 4.116 1.867l2 1.241c1.897 1.178 2.846 1.766 3.365 2.715c.519.948.519 2.092.519 4.38v1.522c0 3.9 0 5.851-1.172 7.063C19.657 22 17.771 22 14 22h-4c-3.771 0-5.657 0-6.828-1.212C2 19.576 2 17.626 2 13.725v-1.521Z">
                        </path>
                        <path d="M9 16c.85.63 1.885 1 3 1s2.15-.37 3-1" stroke-linecap="round"></path>
                    </g>
                </symbol>
                <!-- shop solar-shop-2-outline -->
                <symbol id="shop" viewBox="0 0 24 24">
                    <path
                        d="M20.6 5.26a2.512 2.512 0 0 0-2.48-2.2H5.885a2.512 2.512 0 0 0-2.48 2.19l-.3 2.47a3.411 3.411 0 0 0 1.16 2.56v8.16a2.5 2.5 0 0 0 2.5 2.5h10.47a2.5 2.5 0 0 0 2.5-2.5v-8.16A3.411 3.411 0 0 0 20.9 7.72Zm-6.59 14.68h-4v-4.08a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5Zm4.73-1.5a1.5 1.5 0 0 1-1.5 1.5h-2.23v-4.08a2.5 2.5 0 0 0-2.5-2.5h-1a2.5 2.5 0 0 0-2.5 2.5v4.08H6.765a1.5 1.5 0 0 1-1.5-1.5v-7.57a3.223 3.223 0 0 0 1.24.24a3.358 3.358 0 0 0 2.58-1.19a.241.241 0 0 1 .34 0a3.358 3.358 0 0 0 2.58 1.19A3.393 3.393 0 0 0 14.6 9.92a.219.219 0 0 1 .16-.07a.238.238 0 0 1 .17.07a3.358 3.358 0 0 0 2.58 1.19a3.173 3.173 0 0 0 1.23-.24Zm-1.23-8.33a2.386 2.386 0 0 1-1.82-.83a1.2 1.2 0 0 0-.92-.43h-.01a1.194 1.194 0 0 0-.92.42a2.476 2.476 0 0 1-3.65 0a1.24 1.24 0 0 0-1.86 0A2.405 2.405 0 0 1 4.1 7.78l.3-2.4a1.517 1.517 0 0 1 1.49-1.32h12.23a1.5 1.5 0 0 1 1.49 1.32l.29 2.36a2.392 2.392 0 0 1-2.395 2.37Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- contact ph-phone-call-light -->

                <symbol id="contact" viewBox="0 0 256 256">
                    <path
                        d="M154.2 38.45a6 6 0 0 1 7.35-4.25a84.24 84.24 0 0 1 60.25 60.25a6 6 0 0 1-4.25 7.35a5.94 5.94 0 0 1-1.55.2a6 6 0 0 1-5.8-4.45a72.34 72.34 0 0 0-51.75-51.75a6 6 0 0 1-4.25-7.35Zm-3.75 39.35C165 81.68 174.32 91 178.2 105.55A6 6 0 0 0 184 110a5.94 5.94 0 0 0 1.55-.2a6 6 0 0 0 4.25-7.35c-5-18.71-17.54-31.25-36.25-36.25a6 6 0 1 0-3.1 11.6Zm79.44 97A54.25 54.25 0 0 1 176 222C97.7 222 34 158.3 34 80a54.25 54.25 0 0 1 47.17-53.89a14 14 0 0 1 14.56 8.39l21.1 47.1a14 14 0 0 1-1.12 13.28a6 6 0 0 1-.42.57l-21.07 25.06a1.89 1.89 0 0 0 0 1.67c7.66 15.68 24.1 32 40 39.65a1.88 1.88 0 0 0 1.68-.06l24.69-21a4.81 4.81 0 0 1 .56-.42a14 14 0 0 1 13.28-1.22l47.24 21.17a14 14 0 0 1 8.22 14.53ZM218 173.32a2 2 0 0 0-1.21-2l-47.25-21.17a1.92 1.92 0 0 0-1.6.1l-24.68 21c-.18.15-.37.29-.56.42a14 14 0 0 1-13.77 1c-18.36-8.87-36.66-27-45.53-45.19a14 14 0 0 1 .91-13.73a4.73 4.73 0 0 1 .43-.57l21.06-25.06a2 2 0 0 0 0-1.67L84.74 39.31A2 2 0 0 0 82.9 38h-.23A42.24 42.24 0 0 0 46 80c0 71.68 58.32 130 130 130a42.24 42.24 0 0 0 42-36.68Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- question ph-question-light -->

                <symbol id="question" viewBox="0 0 256 256">
                    <path
                        d="M138 180a10 10 0 1 1-10-10a10 10 0 0 1 10 10ZM128 74c-21 0-38 15.25-38 34v4a6 6 0 0 0 12 0v-4c0-12.13 11.66-22 26-22s26 9.87 26 22s-11.66 22-26 22a6 6 0 0 0-6 6v8a6 6 0 0 0 12 0v-2.42c18.11-2.58 32-16.66 32-33.58c0-18.75-17-34-38-34Zm102 54A102 102 0 1 1 128 26a102.12 102.12 0 0 1 102 102Zm-12 0a90 90 0 1 0-90 90a90.1 90.1 0 0 0 90-90Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- moon line-md-sunny-outline-to-moon-transition -->
                <symbol id="moon" viewBox="0 0 256 256">
                    <path
                        d="M240 96a8 8 0 0 1-8 8h-16v16a8 8 0 0 1-16 0v-16h-16a8 8 0 0 1 0-16h16V72a8 8 0 0 1 16 0v16h16a8 8 0 0 1 8 8Zm-96-40h8v8a8 8 0 0 0 16 0v-8h8a8 8 0 0 0 0-16h-8v-8a8 8 0 0 0-16 0v8h-8a8 8 0 0 0 0 16Zm72.77 97a8 8 0 0 1 1.43 8A96 96 0 1 1 95.07 37.8a8 8 0 0 1 10.6 9.06a88.07 88.07 0 0 0 103.47 103.47a8 8 0 0 1 7.63 2.67Zm-19.39 14.88c-1.79.09-3.59.14-5.38.14A104.11 104.11 0 0 1 88 64c0-1.79 0-3.59.14-5.38a80 80 0 1 0 109.24 109.24Z"
                        fill="currentColor"></path>
                </symbol>

                <!-- sun line-md-moon-to-sunny-outline-transition -->

                <symbol id="sun" viewBox="0 0 24 24">
                    <path
                        d="M12 5q-.425 0-.713-.288T11 4V2q0-.425.288-.713T12 1q.425 0 .713.288T13 2v2q0 .425-.288.713T12 5Zm4.95 2.05q-.275-.275-.275-.687t.275-.713l1.4-1.425q.3-.3.712-.3t.713.3q.275.275.275.7t-.275.7L18.35 7.05q-.275.275-.7.275t-.7-.275ZM20 13q-.425 0-.713-.288T19 12q0-.425.288-.713T20 11h2q.425 0 .713.288T23 12q0 .425-.288.713T22 13h-2Zm-8 10q-.425 0-.713-.288T11 22v-2q0-.425.288-.713T12 19q.425 0 .713.288T13 20v2q0 .425-.288.713T12 23ZM5.65 7.05l-1.425-1.4q-.3-.3-.3-.725t.3-.7q.275-.275.7-.275t.7.275L7.05 5.65q.275.275.275.7t-.275.7q-.3.275-.7.275t-.7-.275Zm12.7 12.725l-1.4-1.425q-.275-.3-.275-.713t.275-.687q.275-.275.688-.275t.712.275l1.425 1.4q.3.275.288.7t-.288.725q-.3.3-.725.3t-.7-.3ZM2 13q-.425 0-.713-.288T1 12q0-.425.288-.713T2 11h2q.425 0 .713.288T5 12q0 .425-.288.713T4 13H2Zm2.225 6.775q-.275-.275-.275-.7t.275-.7L5.65 16.95q.275-.275.687-.275t.713.275q.3.3.3.713t-.3.712l-1.4 1.4q-.3.3-.725.3t-.7-.3ZM12 18q-2.5 0-4.25-1.75T6 12q0-2.5 1.75-4.25T12 6q2.5 0 4.25 1.75T18 12q0 2.5-1.75 4.25T12 18Zm0-2q1.65 0 2.825-1.175T16 12q0-1.65-1.175-2.825T12 8q-1.65 0-2.825 1.175T8 12q0 1.65 1.175 2.825T12 16Zm0-4Z"
                        fill="currentColor"></path>
                </symbol>
                <!-- plus  -->
                <symbol aria-hidden="true" id="plus" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path d="M19 12.998h-6v6h-2v-6H5v-2h6v-6h2v6h6z" fill="currentColor"></path>
                </symbol>
                <!-- minus  mdi-minus -->

                <symbol id="minus" viewBox="0 0 24 24">
                    <path d="M19 13H5v-2h14v2Z" fill="currentColor"></path>
                </symbol>
                <!-- trash solar-trash-bin-trash-broken -->
                <symbol id="trash" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20.5 6h-17m6 5l.5 5m4.5-5l-.5 5" stroke-linecap="round"></path>
                        <path
                            d="M6.5 6h.11a2 2 0 0 0 1.83-1.32l.034-.103l.097-.291c.083-.249.125-.373.18-.479a1.5 1.5 0 0 1 1.094-.788C9.962 3 10.093 3 10.355 3h3.29c.262 0 .393 0 .51.019a1.5 1.5 0 0 1 1.094.788c.055.106.097.23.18.479l.097.291A2 2 0 0 0 17.5 6">
                        </path>
                        <path
                            d="M18.373 15.4c-.177 2.654-.265 3.981-1.13 4.79c-.865.81-2.195.81-4.856.81h-.774c-2.66 0-3.99 0-4.856-.81c-.865-.809-.953-2.136-1.13-4.79l-.46-6.9m13.666 0l-.2 3"
                            stroke-linecap="round"></path>
                    </g>
                </symbol>
            </svg>
        </div>
        <div class="flex min-h-screen items-center justify-center bg-background">
            <div class="container">
                <div class="mx-auto max-w-[450px] rounded-xl bg-muted p-5 shadow-base md:p-10">
                    <!-- Logo -->
                    <a href="./index.php">
                        <img src="./assets/images/logo.png" class="mx-auto mb-10 w-40" alt="" />
                    </a>
                    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                    <h1 class="mb-8 text-lg">Change password</h1>
                    <?php if (isset($_SESSION['error'])) { ?>
                    <p class="h-4 text-sm text-red-600 dark:text-red-400 text-center mb-4">
                        <?php echo htmlspecialchars($_SESSION['error']);
 unset($_SESSION['error']); ?>
                    </p>
                    <?php } ?>

                    <?php if (isset($_SESSION['success'])) { ?>
                    <p class="h-4 text-sm text-green-600 dark:text-green-400 text-center mb-5">
                        <?php echo htmlspecialchars($_SESSION['success']);
 unset($_SESSION['success']);
 echo'<meta http-equiv="refresh" content="2; url=login.php">';
 ?>
                    </p>
                    <?php } ?>
                    <div class="mb-4 space-y-4">
                        <label for="password" class="relative block rounded-lg border shadow-base">
                            <input type="password" id="password" dir="auto" name="password" required
                                value="<?php if(isset($_POST['password'])) echo $_POST['password']; ?>"
                                class="peer w-full rounded-lg border-none bg-transparent p-4 text-left placeholder-transparent focus:outline-none focus:ring-0"
                                placeholder="new password" />

                            <span
                                class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-muted px-2 py-0.5 text-sm text-text/90 transition-all peer-placeholder-shown:top -1/2 peer-placeholder-shown:text-base peer-focus:top-0 peer-focus:text-sm">
                                New password
                            </span>
                        </label>
                    </div>
                    <div class="mb-4 space-y-4">
                        <label for="confirm_password" class="relative block rounded-lg border shadow-base">
                            <input type="password" id="confirm_password" dir="auto" name="passwordconf" required
                                value="<?php if(isset($_POST['passwordconf'])) echo $_POST['passwordconf']; ?>"
                                class="peer w-full rounded-lg border-none bg-transparent p-4 text-left placeholder-transparent focus:outline-none focus:ring-0"
                                placeholder="Repeat new password" />

                            <span
                                class="pointer-events-none absolute start-2.5 top-0 -translate-y-1/2 bg-muted px-2 py-0.5 text-sm text-text/90 transition-all peer-placeholder-shown:top -1/2 peer-placeholder-shown:text-base peer-focus:top-0 peer-focus:text-sm">
                                Repeat the new password
                            </span>
                        </label>
                    </div>
                    <div>
                        <button type="submit" name="forgotpasssubmit" class="btn-primary w-full py-3">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>