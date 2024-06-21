<?php
include 'top.php';

// Initialize
$dataIsGood = false;
$reviewId = null;
$userId = 0;
$toReviewArray = array();
$adventureId = 0;
$currentDate = date('Y-m-d');
$datePosted = '';
$rating = 0;
$reviewText = '';

if ($adminLogged) {
    // Fetch user id from url
    $userId = isset($_GET['userId']) ? htmlspecialchars($_GET['userId']) : 0;

    if ($userId !== null && $userId > 0) {
        // Retrieve all adventures that user is joined in the group of and that has already occured.
        $toReviewSql = 'SELECT
                            mem.pfkUserId,
                            mem.pfkGroupId, 
                            grp.fnkAdventureId,
                            adv.fldAdventureName,
                            adv.fldAdventureDate
                        FROM tblUserGroupMember AS mem
                        JOIN tblGroup AS grp ON grp.pmkGroupId = mem.pfkGroupId
                        JOIN tblAdventure AS adv ON adv.pmkAdventureId = grp.fnkAdventureId
                        WHERE (adv.fldAdventureDate < CURRENT_DATE) AND mem.pfkUserId = ?
                        ORDER BY adv.fldAdventureDate ASC';
        $toReviewParam = array($userId);
        $toReviewArray = $thisDataBaseReader->select($toReviewSql, $toReviewParam);
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
    <section class='beige-half'>
        <h2 class="header-grid">Post New Review</h2>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $dataIsGood = true;

            // Server side Sanitization
            //$organizerId = 0;
            $userId = getData("numUserId");
            $adventureId = getData("lstAdventure");
            $datePosted = getData("dteReviewDate");
            $rating = getData("numRating");
            $reviewText = getData("txtReviewText");

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

            if ($adventureId == "" || !in_array($adventureId, array_column($toReviewArray, 'fnkAdventureId'))) {
                print '<p class="mistake">Please select a valid Adventure.</p>';
                $dataIsGood = false;
            }

            if ($datePosted == "") {
                print '<p class="mistake">Please enter your Review Date Posted.</p>';
                $dataIsGood = false;
            } elseif ($datePosted !== $currentDate) {
                print '<p class="mistake">Your new Review Date must be todays Date.</p>';
                $dataIsGood = false;
            }

            if ($rating == "") {
                print '<p class="mistake">Please enter the Rating for the Adventure.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($rating)) {
                print '<p class="mistake">Your rating appears to have extra character.</p>';
                $dataIsGood = false;
            } elseif ($rating < 0 || $rating > 5) {
                print '<p class="mistake">Your rating be between 0-5.</p>';
                $dataIsGood = false;
            }

            if ($reviewText == "") {
                print '<p class="mistake">Please enter your Review Text.</p>';
                $dataIsGood = false;
            } elseif (!verifyAlphaNum($reviewText)) {
                print '<p class="mistake">Your Review text appears to have extra character.</p>';
                $dataIsGood = false;
            }


            // Save the Data
            print '<!-- Start Saving -->';
            if ($dataIsGood) {
                try {
                    // Check if the user has already reviewed the adventure
                    $reviewExistSql = "SELECT pmkReviewID FROM tblReview WHERE fnkUserID = ? AND fnkAdventureId = ?";
                    $reviewExistParams = array($userId, $adventureId);
                    $existingReview = $thisDataBaseReader->select($reviewExistSql, $reviewExistParams);

                    if ($existingReview) {
                        // Update existing review
                        $reviewSql = 'UPDATE tblReview SET fldDatePosted = ?, fldRating = ?, fldReviewText = ? WHERE pmkReviewID = ?';
                        $reviewParams = array($datePosted, $rating, $reviewText, $existingReview[0]['pmkReviewID']);
                    } else {
                        // Insert new review
                        $reviewSql = 'INSERT INTO tblReview SET fnkUserID = ?, fnkAdventureId = ?, fldDatePosted = ?, fldRating = ?, fldReviewText = ?';
                        $reviewParams = array($userId, $adventureId, $datePosted, $rating, $reviewText);
                    }
                    
                    if ($thisDataBaseWriter->insert($reviewSql, $reviewParams)) {
                        print '<p>Record was successfully saved.</p>';
                    } else {
                        print '<p>Record was NOT successfully saved.</p>';
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

        <form action="<?php print $phpSelf . "?userId=" . $userId; ?>" id="frmInsertReview" method="post" enctype="multipart/form-data">

            <fieldset class="newReview">
                <legend>Posting New Review</legend>
                <p>
                    <input id="numUserId" type="hidden" name="numUserId" tabindex="1" value="<?php print $userId; ?>">
                </p>

                <fieldset class="listbox">
                <legend>Select an Adventure to Review</legend>
                <p>
                    <select id="lstAdventure" name="lstAdventure" tabindex="2">
                        <?php
                        foreach ($toReviewArray as $adventure) {
                            $selected = (isset($_POST['lstAdventure']) && $_POST['lstAdventure'] == $adventure['fnkAdventureId']) ? 'selected' : '';
                            echo "<option value=\"{$adventure['fnkAdventureId']}\" $selected>{$adventure['fldAdventureName']}</option>";
                        }
                        ?>
                    </select>
                </p>
                </fieldset>

                <p>
                    <input id="dteReviewDate" type="hidden" name="dteReviewDate" tabindex="3" value="<?php print $currentDate; ?>">
                </p>

                <p>
                    <label class="required" for="numRating">Rating (0-5)</label>
                    <input id="numRating" type="number" min="0" max="5" name="numRating"  tabindex="4" value="<?php print $rating; ?>" required>
                </p>

                <p>
                    <label class="required" for="txtReviewText">Review Text</label>
                    <textarea id="txtReviewText" name="txtReviewText" rows="10" cols="40" maxlength="400" tabindex="5" required><?php print $reviewText; ?></textarea>
                </p>

            </fieldset>

            <fieldset class="button">
                <input id="btnSubmit" name="btnSubmit" type="submit" tabindex="6" value="Post">
            </fieldset>
        </form>
    </section>
</main>

<?php
include 'footer.php';
?>