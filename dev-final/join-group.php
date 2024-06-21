<?php
include 'top.php';
// Initialize
$dataIsGood = false;
$userId = 0;
$groupId = 0;

if ($adminLogged) {
    // Fetch user id from url
    $userId = isset($_GET['userId']) ? htmlspecialchars($_GET['userId']) : 0;
    // Fetch groups names that user is not a member of 
    $groupSql = 'SELECT
                    grp.pmkGroupId,
                    grp.fldGroupName,
                    grp.fldGroupLimit
                FROM tblGroup AS grp
                LEFT JOIN tblUserGroupMember AS mem ON grp.pmkGroupId = mem.pfkGroupId AND mem.pfkUserId = ?
                WHERE mem.pfkUserId IS NULL
                ORDER BY fldGroupName ASC';
    $groupParam = array($userId);
    $groupArray = $thisDataBaseReader->select($groupSql, $groupParam);
}  else {
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
        <h2 class="header-grid">Join A Group</h2>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $dataIsGood = true;

            // Server side Sanitization
            $userId = getData("numUserId");
            $groupId = getData("lstGroup");

            // Server side Validation
            if ($userId == "") {
                print '<p class="mistake">No User Id found.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($userId)) {
                print '<p class="mistake">Your User Id appears to have extra character.</p>';
                $dataIsGood = false;
            } elseif ($userId < 0) {
                print '<p class="mistake">Your User Id must be greater than 0.</p>';
                $dataIsGood = false;
            }

            if ($groupId == "" || !in_array($groupId, array_column($groupArray, 'pmkGroupId'))) {
                print '<p class="mistake">Please select a valid Group to Join.</p>';
                $dataIsGood = false;
            } else {
                $selectedGroupSql = 'SELECT
                                        grp.pmkGroupId,
                                        grp.fldGroupLimit,
                                        COUNT(mem.pfkGroupId) AS totalMembers
                                    FROM tblGroup AS grp
                                    LEFT JOIN tblUserGroupMember AS mem ON mem.pfkGroupId = grp.pmkGroupId
                                    WHERE grp.pmkGroupId = ?
                                    GROUP BY grp.pmkGroupId';
                $selectedGroupParam = array($groupId);
                $selectedGroup = $thisDataBaseReader->select($selectedGroupSql, $selectedGroupParam);
                $totalMembers = $selectedGroup[0]['totalMembers'];
                $groupLimit = $selectedGroup[0]['fldGroupLimit'];
                
                if ($totalMembers >= $groupLimit) {
                    print '<p class="mistake">This group is already full. Please select another group.</p>';
                    $dataIsGood = false;
                }
            }

            // Save the Data
            print '<!-- Start Saving -->';
            if ($dataIsGood) {
                try {
                    $groupJoinSql = 'INSERT INTO tblUserGroupMember (pfkUserId, pfkGroupId) VALUES (?,?)';
                    $groupJoinParams = array($userId, $groupId);

                    // Join group record
                    if ($thisDataBaseWriter->insert($groupJoinSql, $groupJoinParams)) {
                        print '<p>Record was successfully saved.</p>';
                    } else {
                        print '<p>Failed to join group.</p>';
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

        <form action="<?php print $phpSelf . "?userId=" . $userId; ?>" id="frmJoinGroup" method="post">

            <fieldset class="newGroupMembership">
                <legend>Adding A New Groupmembership</legend>

                <fieldset class="listbox">
                <legend>Select a Group to Join</legend>
                <p>
                    <input id="numUserId" type="hidden" name="numUserId" tabindex="1" value="<?php print $userId; ?>">
                </p>

                <p>
                    <select id="lstGroup" name="lstGroup" tabindex="2">
                        <?php
                        foreach ($groupArray as $group) {
                            $selected = (isset($_POST['lstGroup']) && $_POST['lstGroup'] == $group['pmkGroupId']) ? 'selected' : '';
                            echo "<option value=\"{$group['pmkGroupId']}\" $selected>{$group['fldGroupName']}</option>";
                        }
                        ?>
                    </select>
                </p>
                </fieldset>

            </fieldset>

            <fieldset class="button">
                <input id="btnSubmit" name="btnSubmit" type="submit" tabindex="11" value="Join">
            </fieldset>
        </form>
    </section>
</main>

<?php
include 'footer.php';
?>