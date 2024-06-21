<?php
include 'top.php';

// If they have admin privileges
if ($adminLogged) {
    // Fetch all user records from the database
    $userSelect = 'SELECT
                        pmkUserId,
                        fldUserName,
                        fldFirstName,
                        fldLastName,
                        fldEmail,
                        fldRegistrationDate
                    FROM tblUser';
    $users = $thisDataBaseReader->select($userSelect);

    print '<main>';
    if ($users !== null) {
    
        print '<section class="beige-half">';
        print '<h2 class="header-grid">View All Users</h2>';

        foreach ($users as $user) {
            print '<article class="card-grid">';
            print '<h3>Username: ' . $user['fldUserName'] . '</h3>';
            print '<p>User Id: ' . $user['pmkUserId'] . '</p>';
            print '<p>First Name: ' . $user['fldFirstName'] . '</p>';
            print '<p>Last Name: ' . $user['fldLastName'] . '</p>';
            print '<p>Email: ' . $user['fldEmail'] . '</p>';
            print '<p>Registration Date: ' . $user['fldRegistrationDate'] . '</p>';
            print '<a href="display-user.php?userId=' . $user['pmkUserId'] . '" class="update-button">User Details</a>';
            print '</article>';
        }
        print '</section>';
    }

    // Depending on user link clicked, display info about adventures, groups, reviews related to user.
    $userId = isset($_GET['userId']) ? htmlspecialchars($_GET['userId']) : null;
    if ($userId !== null && $userId > 0) {
        $userSelect = 'SELECT
                            pmkUserId,
                            fldUserName
                        FROM tblUser
                        WHERE pmkUserId = ?';
        $userParam = array($userId);
        $user = $thisDataBaseReader->select($userSelect, $userParam);
        
        print '<section class="beige-half">';
        print '<h2 class="header-grid">User Selected: ' . $user[0]['fldUserName'] . ' [' . $user[0]['pmkUserId'] . ']' . '</h2>';
        print '</section>';

        // Fetch adventures created by the user
        $adventureSelect = 'SELECT
                                    adv.pmkAdventureId,
                                    cat.fldCategoryName,
                                    town.fldMunicipalityName,
                                    adv.fldAddress,
                                    adv.fldAdventureDate,
                                    adv.fldExpectedFee,
                                    adv.fldAdventureImg,
                                    adv.fldAdventureName
                                FROM tblAdventure AS adv
                                JOIN tblCategory AS cat ON adv.fnkCategoryId = cat.pmkCategoryId
                                JOIN tblMunicipality AS town ON adv.fnkMunicipalityId = town.pmkMunicipalityId
                                WHERE fnkOrganizerId = ?
                                ORDER BY fldAdventureDate ASC';
        $adventureParam = array($userId);
        $adventures = $thisDataBaseReader->select($adventureSelect, $adventureParam);
        if (!empty($adventures)) {
            print '<section class="green-half">';
            print '<h2 class="header-grid">Adventures Created by ' . $user[0]['fldUserName'] . ' [' . $user[0]['pmkUserId'] . ']' . '</h2>';
            // Show Adventures created by the user
            foreach ($adventures as $adventure) {
                print '<article class="card-grid">';
                print '<h3>' . $adventure['fldAdventureName'] . '</h3>';
                print '<img src="' . $adventure['fldAdventureImg'] . '" alt="Adventure Image">';
                print '<p>Adventure Id: ' . $adventure['pmkAdventureId'] . '</p>';
                print '<p>Category: ' . $adventure['fldCategoryName'] . '</p>';
                print '<p>Municipality: ' . $adventure['fldMunicipalityName'] . '</p>';
                print '<p>Address: ' . $adventure['fldAddress'] . '</p>';
                print '<p>Date: ' . $adventure['fldAdventureDate'] . '</p>';
                print '<p>Expected Fee: $' . $adventure['fldExpectedFee'] . '</p>';
                print '</article>';
            }
            print '</section>';
        } else {
            print '<p>No Adventures Created.</p>';
        }

        // Fetch groups the user belongs to
        $groupSelect = 'SELECT
                                grp.pmkGroupId,
                                grp.fldGroupName,
                                grp.fldGroupLimit,
                                grp.fnkAdventureId
                            FROM tblGroup AS grp
                            JOIN tblUserGroupMember AS mem ON mem.pfkGroupId = grp.pmkGroupId
                            WHERE mem.pfkUserId = ?';
        $groupParam = array($userId);
        $groups = $thisDataBaseReader->select($groupSelect, $groupParam);
        if (!empty($groups)) {
            print '<section class="beige-half">';
            print '<h2 class="header-grid">Groups Joined by ' . $user[0]['fldUserName'] . ' [' . $user[0]['pmkUserId'] . ']' . '</h2>';

            // Show groups the user belongs to
            foreach ($groups as $group) {
                print '<article class="card-grid">';
                print '<h3>Group Name: ' . $group['fldGroupName'] . '</h3>';
                print '<p>Group Id: ' . $group['pmkGroupId'] . '</p>';
                print '<p>Group Limit: ' . $group['fldGroupLimit'] . '</p>';
                print '</article>';
            }
            print '</section>';
        } else {
            print '<p>No Groups Joined.</p>';
        }

        $reviewSelect = 'SELECT
                                pmkReviewID, 
                                fnkAdventureId,
                                fldDatePosted,
                                fldRating,
                                fldReviewText
                            FROM tblReview 
                            WHERE fnkUserId = ?';
        $reviewParam = array($userId);
        $reviews = $thisDataBaseReader->select($reviewSelect, $reviewParam);
        if (!empty($reviews)) {
            print '<section class="green-half">';
            print '<h2 class="header-grid">Reviews Posted by ' . $user[0]['fldUserName'] . ' [' . $user[0]['pmkUserId'] . ']' . '</h2>';
            // Show review left by the user
            foreach ($reviews as $review) {
                print '<article class="card-grid">';
                print '<h3>Review Id: ' . $review['pmkReviewID'] . '</h3>';
                print '<p>Adventure Id: ' . $review['fnkAdventureId'] . '</p>';
                print '<p>Rating: ' . $review['fldRating'] . '</p>';
                print '<p>Review Text: ' . $review['fldReviewText'] . '</p>';
                print '</article>';
            }
            print '</section>';
        } else {
            print '<p>No Reviews Posted.</p>';
        }
    } else {
        print '<p class="mistake">Please Select a User</p>';
    }
    print '</main>';
} else {
    print '<p class>We\'re sorry! The server has encountered an internal error and could not process your request. Please try again later.</p>';
}
?>

<?php
include 'footer.php';
?>