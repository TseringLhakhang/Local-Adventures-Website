<?php
$phpSelf = htmlspecialchars($_SERVER['PHP_SELF']);
$pathParts = pathinfo($phpSelf);
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Local Adventures</title>
    <meta name="author" content="Tsering Lhakhang">
    <meta name="description" content="Final Project where user can find local adventures in Vermont and go with groups">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Yeseva+One">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Merriweather+Sans">
    <link rel="stylesheet" href="css/custom.css?version=<?php print time(); ?>" type="text/css">
    <link rel="stylesheet" media="(max-width: 820px)" href="css/tablet.css?version=<?php print time(); ?>" type="text/css">
    <link rel="stylesheet" media="(max-width: 430px)" href="css/phone.css?version=<?php print time(); ?>" type="text/css">
</head>

<?php
include 'lib/database-connect.php';
include 'lib/Paging.php'; 
print '<body class="' . $pathParts['filename'] . '">';
include 'header.php';

// Get the authenticated user's NetID
$netId = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");
// Initialize admin logged to false
$adminLogged = false;

// Query to check if the user allowed 
$userSelect = 'SELECT 
                    pmkUserId,
                    fldFirstName,
                    fldLastName
                FROM
                    tblUser 
                WHERE
                    fldNetId = ?';

$data = array($netId);
$admin = $thisDataBaseReader->select($userSelect, $data);

// Setting adminLogged to true if their netid's within table
if (!empty($admin)) {
    $adminLogged = true;
    include 'admin-nav.php';
    echo "<h3 class='login-message'>Welcome, {$admin[0]['fldFirstName']} {$admin[0]['fldLastName']}!</h3>";
} else {
    $adminLogged = false;
    include 'nav.php';
}
?>
