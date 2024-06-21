<?php
include 'top.php';


// Initialize
$dataIsGood = false;
$adventureId = null;
$organizerId = 0;
$categoryId = 0;
$categoryArray = array();
$municipalityId = 0;
$municipalityArray = array();
$address = '';
$adventureDescription = '';
$currentDate = date('Y-m-d');
$adventureDate = '';
$expectedFee = 0;
$adventureImg = '';
$adventureName = '';

$groupId = null;
$groupName = '';
$groupLimit = 0;

if ($adminLogged) {
    // Fetch user id from url
    $organizerId = isset($_GET['userId']) ? htmlspecialchars($_GET['userId']) : 0;
    // Fetch names before processing the form
    $categorySql = 'SELECT fldCategoryName, pmkCategoryId FROM tblCategory ORDER BY fldCategoryName ASC';
    $categoryArray = $thisDataBaseReader->select($categorySql);

    $municipalitySql = 'SELECT fldMunicipalityName, pmkMunicipalityId FROM tblMunicipality ORDER BY fldMunicipalityName ASC';
    $municipalityArray = $thisDataBaseReader->select($municipalitySql);
} else {
    print "<h2>Server Error</h2><p>We\'re sorry! The server has encountered an internal error and could not process your request. Please try again later.</p>";
    exit();
}

function verifyAlphaNum($testString)
{
    return (preg_match("/^([[:alnum:]]|,|-|\.| |\'|&|:|;|#)+$/", $testString));
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
        <h2 class="header-grid">Insert New Adventure</h2>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $dataIsGood = true;

            // Server side Sanitization
            $organizerId = getData("numOrganizerId");
            $categoryId = getData("lstCategory");
            $municipalityId = getData("lstMunicipality");
            $address = getData("txtAddress");
            $adventureDescription = getData("txtAdventureDescription");
            $adventureDate = getData("dteAdventureDate");
            $expectedFee = getData("numExpectedFee");
            $adventureImg = getData("fleAdventureImg");
            $adventureName = getData("txtAdventureName");

            $groupName = getData("txtGroupName");
            $groupLimit = getData("numGroupLimit");

            // Server side Validation
            if ($organizerId == "") {
                print '<p class="mistake">Organizer Id is blank.</p>';
                $dataIsGood = false;
            } elseif ($organizerId < 0) {
                print '<p class="mistake">Organizer Id can not be less than 0</p>';
                $dataIsGood = false;
            }

            if ($categoryId == "" || !in_array($categoryId, array_column($categoryArray, 'pmkCategoryId'))) {
                print '<p class="mistake">Please select a valid Category.</p>';
                $dataIsGood = false;
            }

            if ($municipalityId == "" || !in_array($municipalityId, array_column($municipalityArray, 'pmkMunicipalityId'))) {
                print '<p class="mistake">Please select a valid Municipality.</p>';
                $dataIsGood = false;
            }

            if ($address == "") {
                print '<p class="mistake">Please enter the Address of the event.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($address)) {
                print '<p class="mistake">Your address appears to have extra character.</p>';
                $dataIsGood = false;
            }

            if ($adventureDescription == "") {
                print '<p class="mistake">Please enter your Adventure Description.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($adventureDescription)) {
                print '<p class="mistake">Your Adventure Description appears to have extra character.</p>';
                $dataIsGood = false;
            }

            if ($adventureDate == "") {
                print '<p class="mistake">Please enter your Adventure Date.</p>';
                $dataIsGood = false;
            } elseif ($adventureDate < $currentDate) {
                print '<p class="mistake">Your new Adventure Date cannot fall in the past.</p>';
                $dataIsGood = false;
            }
            

            if ($expectedFee == "") {
                print '<p class="mistake">Please enter your Expected Fee.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($expectedFee)) {
                print '<p class="mistake">Your expected fee must be a number.</p>';
                $dataIsGood = false;
            } elseif ($expectedFee < 0) {
                print '<p class="mistake">Your expected fee not be a negative number.</p>';
                $dataIsGood = false;
            }

            // Check if file was uploaded without errors
            if (isset($_FILES["fleAdventureImg"]) && $_FILES["fleAdventureImg"]["error"] == 0) {
                $targetDir = "./images/";
                $targetFile = $targetDir . basename($_FILES["fleAdventureImg"]["name"]);

                // Check if file already exists
                if (file_exists($targetFile)) {
                    print '<p class="mistake">Sorry, this image file already exists.</p>';
                    $dataIsGood = false;
                } else {
                    // Moving file to images directory
                    if (move_uploaded_file($_FILES["fleAdventureImg"]["tmp_name"], $targetFile)) {
                        $adventureImg = $targetFile; // Store the path to the image file
                    } else {
                        print '<p class="mistake">Sorry, there was an error uploading your image file.</p>';
                        $dataIsGood = false;
                        
                    }
                }
            } elseif($_FILES["fleAdventureImg"]["size"] > 8000000) { // 1MB limit
                print '<p class="mistake">Sorry, your image file is too large. Maximum size allowed is 1 MB.</p>';
                $dataIsGood = false;
            } else {
                print '<p class="mistake">No file were uploaded. Please select an Adventure Image</p>';
                $dataIsGood = false;
            }

            if ($adventureName == "") {
                print '<p class="mistake">Please enter your Adventure Name.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($adventureName)) {
                print '<p class="mistake">Your Adventure Name appears to have extra character.</p>';
                $dataIsGood = false;
            }

            if ($groupName == "") {
                print '<p class="mistake">Please enter the Group Name.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($groupName)) {
                print '<p class="mistake">Your Group Name appears to have extra character.</p>';
                $dataIsGood = false;
            }

            if ($groupLimit == "") {
                print '<p class="mistake">Please enter your Group Size Limit.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($groupLimit)) {
                print '<p class="mistake">Your Group size limit must be a number.</p>';
                $dataIsGood = false;
            }

            // Save the Data
            print '<!-- Start Saving -->';
            if ($dataIsGood) {
                try {
                    $adventureSql = 'INSERT INTO tblAdventure (pmkAdventureId, fnkOrganizerId, fnkCategoryId, fnkMunicipalityId, fldAddress, fldAdventureDescription, fldAdventureDate, fldExpectedFee, fldAdventureImg, fldAdventureName) VALUES (?,?,?,?,?,?,?,?,?,?)';
                    $adventureParams = array($adventureId, $organizerId, $categoryId, $municipalityId, $address, $adventureDescription, $adventureDate, $expectedFee, $adventureImg, $adventureName);

                    // Insert adventure record
                    if ($thisDataBaseWriter->insert($adventureSql, $adventureParams)) {
                        // Retrieve the pmkAdventureId of the inserted adventure record
                        $adventureId = $thisDataBaseWriter->lastInsert();

                        // Insert group record
                        $groupSql = 'INSERT INTO tblGroup (pmkGroupId, fldGroupName, fldGroupLimit, fnkAdventureId) VALUES (?,?,?,?)';
                        $groupParams = array($groupId, $groupName, $groupLimit, $adventureId);

                        if ($thisDataBaseWriter->insert($groupSql, $groupParams)) {
                            print '<p>Record was successfully saved.</p>';
                        } else {
                            print '<p>Failed to insert group record.</p>';
                        }
                    } else {
                        print '<p>Failed to insert adventure record.</p>';
                    }
                } catch (PDOException $e) {
                    print '<p>Couldn\'t insert the record, please contact someone :).</p>';
                }
            }
            print '<!-- End Saving -->';
        }
        if ($dataIsGood) {
            print '<h2>Thank you, your information has been received.</h2>';
        }
        ?>

        <form action="<?php print $phpSelf; ?>" id="frmInsertAdventure" method="post" enctype="multipart/form-data">

            <fieldset class="newAdventure">
                <legend>Adding New Adventure</legend>

                <p>
                    <input id="numOrganizerId" type="hidden" name="numOrganizerId" value="<?php print $organizerId; ?>">
                </p>

                <fieldset class="listbox">
                <legend>Select a Category</legend>
                <p>
                    <select id="lstCategory" name="lstCategory" tabindex="1">
                        <?php
                        foreach ($categoryArray as $category) {
                            $selected = (isset($_POST['lstCategory']) && $_POST['lstCategory'] == $category['pmkCategoryId']) ? 'selected' : '';
                            echo "<option value=\"{$category['pmkCategoryId']}\" $selected>{$category['fldCategoryName']}</option>";
                        }
                        ?>
                    </select>
                </p>
                </fieldset>

                <fieldset class="listbox">
                <legend>Select the Municipality</legend>
                <p>
                    <select id="lstMunicipality" name="lstMunicipality" tabindex="2">
                        <?php
                        foreach ($municipalityArray as $municipality) {
                            $selected = (isset($_POST['lstMunicipality']) && $_POST['lstMunicipality'] == $municipality['pmkMunicipalityId']) ? 'selected' : '';
                            echo "<option value=\"{$municipality['pmkMunicipalityId']}\" $selected>{$municipality['fldMunicipalityName']}</option>";
                        }
                        ?>
                    </select>
                </p>
                </fieldset>

                <p>
                    <label class="required" for="txtAddress">Address</label>
                    <input id="txtAddress" type="text" name="txtAddress" maxlength="100" tabindex="3" value="<?php print $address; ?>" required>
                </p>

                <p>
                    <label class="required" for="txtAdventureDescription">Adventure Description</label>
                    <textarea id="txtAdventureDescription" name="txtAdventureDescription" rows="10" cols="40" maxlength="400" tabindex="4" required><?php print $adventureDescription; ?></textarea>
                </p>

                <p>
                    <label class="required" for="dteAdventureDate">Adventure Date</label>
                    <input id="dteAdventureDate" type="date" name="dteAdventureDate" tabindex="5" min="<?php print $currentDate; ?>" value="<?php print $adventureDate; ?>" required>
                </p>

                <p>
                    <label class="required" for="numExpectedFee">Expected Fee</label>
                    <input id="numExpectedFee" type="number" min="0" name="numExpectedFee"  tabindex="6" value="<?php print $expectedFee; ?>" required>
                </p>

                <p>
                    <label class="required" for="fleAdventureImg">Adventure Image (PNG, JPEG, JPG)</label>
                    <input id="fleAdventureImg" type="file" name="fleAdventureImg"  accept="image/*" tabindex="7" required>
                </p>

                <p>
                    <label class="required" for="txtAdventureName">Adventure Name</label>
                    <input id="txtAdventureName" type="text" name="txtAdventureName" maxlength="50" tabindex="8" value="<?php print $adventureName; ?>" required>
                </p>

                <p>
                    <label class="required" for="txtGroupName">Group Name</label>
                    <input id="txtGroupName" type="text" name="txtGroupName" maxlength="50" tabindex="9" value="<?php print $groupName; ?>" required>
                </p>

                <p>
                    <label class="required" for="numGroupLimit">Group Size Limit</label>
                    <input id="numGroupLimit" type="number" min="2" name="numGroupLimit"  tabindex="10" value="<?php print $groupLimit; ?>" required>
                </p>

            </fieldset>

            <fieldset class="button">
                <input id="btnSubmit" name="btnSubmit" type="submit" tabindex="11" value="Insert">
            </fieldset>
        </form>
    </section>
</main>

<?php
include 'footer.php';
?>