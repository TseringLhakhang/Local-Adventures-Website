<?php
include 'top.php';

$discoverParagraph = "Discover the excitement that awaits with our upcoming adventures! Explore a range of thrilling ";
$discoverParagraph .= "activities, from scenic hikes to cultural tours and everything in between. Check out our carefully ";
$discoverParagraph .= "curated selection of the next five events, each offering unique experiences and unforgettable moments.";

$revisitParagraph = "Relive the magic of our recent adventures with our past events page! Take a trip down memory lane ";
$revisitParagraph .= " as we revisit the five most recent adventures, each filled with exciting experiences and unforgettable moments. ";
$revisitParagraph .= "Stay tuned for future adventures and join us as we continue to discover the wonders of Vermont together!";
?>

<main>
    <section class='beige-half'>
    <h2 class="header-grid">Upcoming Adventures</h2>
    <p class="paragraph"><?php print $discoverParagraph ?></p>
    <?php
    $sql = 'SELECT fldAdventureName, fldAdventureDate, fldAdventureImg FROM tblAdventure ';
    $sql .= 'WHERE fldAdventureDate > CURRENT_DATE ORDER BY fldAdventureDate LIMIT 5';
    $records = $thisDataBaseReader->select($sql);
    foreach ($records as $record) { 
        print '<article class="card-grid">';
        print '<h3>' . $record['fldAdventureName'] . '</h3>';
        print '<img src="' . $record['fldAdventureImg'] . '" alt="Adventure Image">';
        print '<p>' . $record['fldAdventureDate']. '</p>';
        print '</article>';
    }
    ?>
    </section>

    <section class='green-half'>
    <h2 class="header-grid">Past Adventures</h2>
    <p class="paragraph"><?php print $revisitParagraph?></p>
    <?php
    $sql = 'SELECT fldAdventureName, fldAdventureDate, fldAdventureImg FROM tblAdventure ';
    $sql .= 'WHERE fldAdventureDate < CURRENT_DATE ORDER BY fldAdventureDate LIMIT 5';
    $records = $thisDataBaseReader->select($sql);
    
    foreach ($records as $record) {
        print '<article class="card-grid">';
        print '<h3>' . $record['fldAdventureName'] . '</h3>';
        print '<img src="' . $record['fldAdventureImg'] . '" alt="Adventure Image">';
        print '<p>' . $record['fldAdventureDate'] . '</p>';
        print '</article>';
    }
    ?>
    </section>
</main>

<?php
include 'footer.php';
?>