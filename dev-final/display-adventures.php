<?php
include 'top.php';

if ($adminLogged) {
    try {
        // Fetch all adventure records from the database
        $sql = 'SELECT
                    adv.pmkAdventureId,
                    adv.fldAdventureName,
                    adv.fldAdventureDate,
                    adv.fldAdventureImg,
                    grp.pmkGroupId,
                    COUNT(mem.pfkGroupId) AS totalMembers
                FROM tblAdventure AS adv
                JOIN tblGroup AS grp ON grp.fnkAdventureId = adv.pmkAdventureId
                LEFT JOIN tblUserGroupMember AS mem ON mem.pfkGroupId = grp.pmkGroupId
                GROUP BY grp.pmkGroupId
                ORDER BY adv.fldAdventureDate';
        $adventures = $thisDataBaseReader->select($sql);

        if ($adventures) {
            print '<main>';
            print '<section class="beige-half">';
            print '<h2 class="header-grid">View All Adventure</h2>';

            // Display adventure records with update and delete buttons
            foreach ($adventures as $adventure) {
                print '<article class="card-grid">';
                print '<h3>' . $adventure['fldAdventureName'] . '</h3>';
                print '<img src="' . $adventure['fldAdventureImg'] . '" alt="Adventure Image">';
                print '<p>' . $adventure['fldAdventureDate']. '</p>';
                print '<a href="update-adventure.php?adventureId=' . $adventure['pmkAdventureId'] . '&groupId=' . $adventure['pmkGroupId'] . '" class="update-button">Update</a>';
                print '<a href="delete-adventure.php?adventureId=' . $adventure['pmkAdventureId'] . '&groupId=' . $adventure['pmkGroupId'] . '&totalMembers=' . $adventure['totalMembers'] . '" class="delete-button">Delete</a>';
                print '</article>';
            }

            print '</section>';
            print '</main>';
        } else {
            print '<p>No adventure records found.</p>';
        }
    } catch (PDOException $e) {
        print '<p>Database error: ' . $e->getMessage() . '</p>';
    }
} else {
    print '<p class>We\'re sorry! The server has encountered an internal error and could not process your request. Please try again later.</p>';
}

include 'footer.php';
?>
