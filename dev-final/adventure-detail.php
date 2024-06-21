<?php
include 'top.php';


// Retrieve adventure ID from the URL
$adventureId = isset($_GET['adventureId']) ? htmlspecialchars($_GET['adventureId']) : null;

if ($adventureId !== null && $adventureId > 0) {
    // Fetch adventure record from the database based on adventure ID
    $adventureDetailSelect = 'SELECT 
                                adv.fldAdventureName,
                                usr.fldUserName,
                                cat.fldCategoryName,
                                town.fldMunicipalityName,
                                adv.fldAddress,
                                adv.fldAdventureDescription,
                                adv.fldAdventureDate,
                                adv.fldExpectedFee,
                                adv.fldAdventureImg
                            FROM tblAdventure AS adv
                            JOIN tblCategory AS cat ON adv.fnkCategoryId = cat.pmkCategoryId
                            JOIN tblMunicipality AS town ON adv.fnkMunicipalityId = town.pmkMunicipalityId
                            JOIN tblUser AS usr ON adv.fnkOrganizerId = usr.pmkUserId ';
    $adventureDetailSelect .= 'WHERE adv.pmkAdventureId = ?';
    $adventureParam = array($adventureId);

    $adventure = $thisDataBaseReader->select($adventureDetailSelect, $adventureParam);

    // Check if adventure record exists
    if (!$adventure) {
        print '<p class="mistake">Adventure record not found.</p>';
        exit();
    }

    $groupSelect = 'SELECT
                        grp.fldGroupName,
                        grp.fldGroupLimit,
                        COUNT(mem.pfkGroupId) AS totalMembers
                    FROM tblGroup AS grp
                    LEFT JOIN tblUserGroupMember AS mem ON mem.pfkGroupId = grp.pmkGroupId
                    WHERE grp.fnkAdventureId = ?
                    GROUP BY grp.pmkGroupId';
    $groupParam = array($adventureId);
    $group = $thisDataBaseReader->select($groupSelect, $groupParam);

    $reviewSelect = 'SELECT 
                        COUNT(*) AS totalReviews, 
                        AVG(fldRating) AS averageRating
                    FROM tblReview
                    WHERE fnkAdventureId = ?';
    $reviewParam = array($adventureId);
    $review = $thisDataBaseReader->select($reviewSelect, $reviewParam);
} else {
    // if adventure ID is invalid
    print '<p class="mistake">Invalid adventure ID.</p>';
    exit();
}


?>

<main>
    <section class="beige-half">
        <h2 class="header-grid">View Adventure Details</h2>
        <?php
            print '<article class="bigcard-grid">';
            print '<h3>' . $adventure[0]['fldAdventureName'] . '</h3>';
            print '<img src="' . $adventure[0]['fldAdventureImg'] . '" alt="Adventure Image">';
            print '<p>Adventure Date: ' . $adventure[0]['fldAdventureDate']. '</p>';
            print '<p>Organized By: ' . $adventure[0]['fldUserName']. '</p>';
            print '<p>Category: ' . $adventure[0]['fldCategoryName']. '</p>';
            print '<p>Municipality: ' . $adventure[0]['fldMunicipalityName']. '</p>';
            print '<p>Address: ' . $adventure[0]['fldAddress']. '</p>';
            print '<p class="bigtext">Description: ' . $adventure[0]['fldAdventureDescription']. '</p>';
            print '<p>Expected Fee: $' . $adventure[0]['fldExpectedFee']. '</p>';
            print '<p class="linebreaker-leaf">O</p>';
            print '<p>Group Name: ' . $group[0]['fldGroupName']. '</p>';
            print '<p>Maximum Member Limit: ' . $group[0]['fldGroupLimit']. '</p>';
            print '<p>Current Member Total: ' . $group[0]['totalMembers']. '</p>';
            print '<p class="linebreaker-leaf">O</p>';
            if (!empty($review)) {
                print '<p>Total Reviews: ' . $review[0]['totalReviews'] . '</p>';
                if (!empty($review[0]['averageRating'])) {
                    print '<p>Average Rating: ' . $review[0]['averageRating'] . '</p>';
                } else {
                    print '<p>No Ratings Posted.</p>';
                }
            } else {
                print '<p>No Reviews Posted.</p>';
            }
            print '</article>';
        ?>
    </section>
</main>

<?php
include 'footer.php';
?>
