<?php
include 'top.php';

// Initialize
$dataIsGood = false;
$userId = null;
$netId = null;
$userName = '';
$firstName = '';
$lastName = '';
$email = '';

// Feedback messages
$message = '';
$errorMessage = '';

function verifyAlphaNum($testString)
{
    return (preg_match("/^([[:alnum:]]|-|\.| |\'|&|;|#)+$/", $testString));
}

// Sanitize function from the text
function getData($field)
{
    if (!isset($_POST[$field])) {
        $data = "";
    } else {
        $data = trim($_POST[$field]);
        $data = htmlspecialchars($data);
    }
    return $data;
}
?>

<main>
    <section class='beige-half'>
        <h2 class="header-grid">User Form Page</h2>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $dataIsGood = true;

            // Server side Sanitization
            $netId = getData("txtNetId");
            $userName = getData("txtUserName");
            $firstName = getData("txtFirstName");
            $lastName = getData("txtLastName");
            $email = getData("txtEmail");
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            // Server side Validation
            if ($netId == "") {
                $errorMessage .= '<p class="mistake">Please enter your NetId.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($netId)) {
                $errorMessage .= '<p class="mistake">Your NetId appears to have extra character.</p>';
                $dataIsGood = false;
            }

            if ($userName == "") {
                $errorMessage .= '<p class="mistake">Please enter your username.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($userName)) {
                $errorMessage .= '<p class="mistake">Your username appears to have extra character.</p>';
                $dataIsGood = false;
            }

            if ($firstName == "") {
                $errorMessage .= '<p class="mistake">Please enter your first name.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($firstName)) {
                $errorMessage .= '<p class="mistake">Your first name appears to have extra character.</p>';
                $dataIsGood = false;
            }

            if ($lastName == "") {
                $errorMessage .= '<p class="mistake">Please enter your last name.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($lastName)) {
                $errorMessage .= '<p class="mistake">Your last name appears to have extra character.</p>';
                $dataIsGood = false;
            }

            if ($email == "") {
                $errorMessage .= '<p class="mistake">Please enter your email address.</p>';
                $dataIsGood = false;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage .= '<p class="mistake">Your email address appears to be incorrect.</p>';
                $dataIsGood = false;
            }

            // Save the Data
            print '<!-- Start Saving -->';
            if ($dataIsGood) {
                try {
                    $sql = 'INSERT INTO tblUser (pmkUserId, fldNetId, fldUserName, fldFirstName, fldLastName, fldEmail) VALUES (?,?,?,?,?,?)';
                    $params = array($userId, $netId, $userName, $firstName, $lastName, $email);

                    if ($thisDataBaseWriter->insert($sql, $params)) {
                        // Send message to user once form is successfully filled out
                        $to = $email;
                        $from = 'Local Adventures <tlhakha1@uvm.edu>';
                        $subject = 'Local Adventure Sign Up';

                        $headers = "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=utf-8\r\n";
                        $headers .= "From: " . $from . "\r\n";

                        $message .= '<p style="font: 14pt futura;">Hi ' . strip_tags($firstName) . ', </p>';
                        $message .= '<p style="font: 14pt futura;">Thank you for filling out our sign up form. </p>';
                        $message .= '<table style="font: 12pt futura;">
                                        <tr>
                                            <td><strong>NetId:</strong> </td><td>' . strip_tags($netId) . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Username:</strong> </td><td>' . strip_tags($userName) . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>First Name:</strong> </td><td>' . strip_tags($firstName) . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Name:</strong> </td><td>' . strip_tags($lastName) . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong> </td><td>' . strip_tags($email) . '</td>
                                        </tr>
                                    </table>';
                        $message .= '<p style="font: 14pt futura;">Feel free to browse our website to learn more about the lastest Adventures in Vermont! </p>';
                        $message .= '<p style="font: 14pt futura;">Explore Vermont & Have Fun,<br>';
                        $message .= 'The Local Adventures Team</p>';

                        $mailSent = mail($to, $subject, $message, $headers);
                    } else {
                        $errorMessage .= '<p class="mistake">Record was NOT successfully saved.</p>';
                    }
                } catch (PDOException $e) {
                    $errorMessage .= '<p class="mistake">Couldn\'t insert the record, please contact someone :).</p>';
                }
            }
            print '<!-- End Saving -->';
        }
        if ($dataIsGood) {
            $message .= '<p>Record was successfully save!</p>';
            $message .= '<p>A copy has been sent to the email listed for you records.</p>';
            print $message;
        } else {
            print $errorMessage;
        }
        ?>

        <form action="<?php print $phpSelf; ?>" id="frmRegister" method="post">

            <fieldset class="contact">
                <legend>User Information</legend>
                <p>
                    <label class="required" for="txtNetId">NetId</label>
                    <input id="txtNetId" type="text" name="txtNetId" maxlength="100" tabindex="120" value="<?php print $netId; ?>" required>
                </p>
                <p>
                    <label class="required" for="txtUserName">User Name</label>
                    <input id="txtUserName" type="text" name="txtUserName" maxlength="100" tabindex="130" value="<?php print $userName; ?>" required>
                </p>

                <p>
                    <label class="required" for="txtFirstName">First Name</label>
                    <input id="txtFirstName" type="text" name="txtFirstName" maxlength="100" tabindex="140" value="<?php print $firstName; ?>" required>
                </p>

                <p>
                    <label class="required" for="txtLastName">Last Name</label>
                    <input id="txtLastName" type="text" name="txtLastName" maxlength="100" tabindex="150" value="<?php print $lastName; ?>" required>
                </p>

                <p>
                    <label class="required" for="txtEmail">Email</label>
                    <input id="txtEmail" maxlength="100" name="txtEmail" onfocus="this.select()" tabindex="160" type="email" value="<?php print $email; ?>" required>
                </p>

            </fieldset>

            <fieldset class="button">
                <input id="btnSubmit" name="btnSubmit" type="submit" tabindex="600" value="Register">
            </fieldset>
        </form>
    </section>
</main>

<?php
include 'footer.php';
?>