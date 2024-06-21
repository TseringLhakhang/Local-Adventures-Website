<?php
include 'top.php';

$adventureId = 0;
$groupId = 0;
$totalMembers = 0;

if ($adminLogged) {
    // Get the adventure ID and group ID from the URL
    $adventureId = isset($_GET['adventureId']) ? htmlspecialchars($_GET['adventureId']) : 0;
    $groupId = isset($_GET['groupId']) ? htmlspecialchars($_GET['groupId']) : 0;
    $totalMembers = isset($_GET['totalMembers']) ? htmlspecialchars($_GET['totalMembers']) : 0;

    // Check if adventure ID and group ID are valid
    if ($adventureId > 0 && $groupId > 0) {
        print "<h3 class='mistake'>Are you sure you want to delete adventure with ID: $adventureId?</h3>";
        
        // Check if the group has no members before deleting
        if ($totalMembers == 0) {
            print "<h3 class='mistake'>This will delete the corresponding group with ID: $groupId?</h3>";
        } else {
            print '<p class="mistake">Invalid Group ID or Group has members and thus cannot be deleted.</p>';
            exit();
        }
    } else {
        print '<p class="mistake">Invalid adventure ID or Group ID.</p>';
        exit();
    }
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
    <section class="beige-half">
        <h2 class="header-grid">Delete Adventure Page</h2>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $dataIsGood = true;
            $adventureId = getData("numAdventureId");
            $groupId = getData("numGroupId");

            // Verify Adventure ID
            if ($adventureId == "") {
                print '<p class="mistake">No Adventure Id found.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($adventureId)) {
                print '<p class="mistake">Your Adventure Id appears to have extra characters.</p>';
                $dataIsGood = false;
            } elseif ($adventureId < 0) {
                print '<p class="mistake">Your Adventure Id must be greater than 0.</p>';
                $dataIsGood = false;
            }

            // Verify Group ID
            if ($groupId == "") {
                print '<p class="mistake">No Group Id found.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($groupId)) {
                print '<p class="mistake">Your Group Id appears to have extra characters.</p>';
                $dataIsGood = false;
            } elseif ($groupId < 0) {
                print '<p class="mistake">Your Group Id must be greater than 0.</p>';
                $dataIsGood = false;
            }

            print '<!-- Start Deleting -->';
            if ($dataIsGood) {
                try {
                    // Must delete group first 
                    $deleteGroup = 'DELETE FROM tblGroup WHERE pmkGroupId = ?';
                    $deleteGroupParam = array($groupId);
                    if ($thisDataBaseWriter->delete($deleteGroup, $deleteGroupParam)) {
                        // Delete adventure last otherwise integrity constraint violation
                        $deleteAdventure = 'DELETE FROM tblAdventure WHERE pmkAdventureId = ?';
                        $deleteAdventureParam = array($adventureId);
                        if ($thisDataBaseWriter->delete($deleteAdventure, $deleteAdventureParam)) {
                            print '<p>Record was successfully deleted.</p>';
                        } else {
                            print '<p>Failed to delete adventure record.</p>';
                        }
                    } else {
                        print '<p>Failed to delete group record.</p>';
                    }
                } catch (PDOException $e) {
                    print '<p>Couldn\'t delete the record, please contact someone :).</p>';
                }
            }
            print '<!-- End Deleting -->';
        }
        ?>
        <form action="<?php echo $phpSelf . '?adventureId=' . $adventureId . '&groupId=' . $groupId . '&totalMembers=' . $totalMembers; ?>" id="frmDeleteAdventure" method="post">
            <fieldset class="deleteAdventure">
                <legend>Deleting Adventure: <?php print $adventureId; ?></legend>
                <p>
                    <input id="numAdventureId" type="hidden" name="numAdventureId" tabindex="1" value="<?php print $adventureId; ?>">
                </p>

                <p>
                    <input id="numGroupId" type="hidden" name="numGroupId" tabindex="2" value="<?php print $groupId; ?>">
                </p>
            </fieldset>
            <fieldset class="button">
                <input id="btnDelete" name="btnDelete" type="submit" tabindex="2" value="Delete">
            </fieldset>
        </form>
        <a href="display-adventures.php">Go Back</a>
    </section>
</main>

<?php include "footer.php"; ?>
