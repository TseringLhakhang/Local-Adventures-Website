<?php
include 'top.php';

$orderBy = isset($_GET["sortField"]) ? htmlspecialchars($_GET["sortField"]) : 'fldAdventureName';

$sortDirection = isset($_GET["sortDirection"]) ? htmlspecialchars($_GET["sortDirection"]) :  'ASC';

$oppositeSort = $sortDirection == 'ASC' ? 'DESC' : 'ASC';

// paging 1/2
$start = isset($_GET["start"]) ? htmlspecialchars($_GET["start"]) :  0;
$numberToDisplay = 10;

$sql = 'SELECT pmkAdventureId, fldAdventureName, fldCategoryName, fldAdventureDate, fldExpectedFee FROM tblAdventure ';
$sql .= 'JOIN tblCategory ON fnkCategoryId = pmkCategoryId ';
$sql .= 'ORDER BY ' . $orderBy . ' ' . $sortDirection;
$sql .= ' LIMIT ' . $start . ', ' . $numberToDisplay;

$adventures =  $thisDataBaseReader->select($sql);

$sql  = 'SELECT count(pmkAdventureId) as totalRecords ';
$sql .= 'FROM tblAdventure ';

$totalRecords = $thisDataBaseReader->totalRecords($sql);

$paging = new Paging($adventures, $orderBy, $totalRecords, $start, $numberToDisplay + 1);

$start = $paging->getStart();
$nextStart = $paging->getNextStart();
$previous = $paging->getPrevious();

$total = $paging->getTotal();


$nextGetString = '?sortField=' . $orderBy . '&sortDirection=' . $sortDirection . '&start=' . $nextStart;
$previousGetString = '?sortField=' . $orderBy . '&sortDirection=' . $sortDirection . '&start=' . $previous;

$instructionParagraph = "Welcome to the Adventures page! Here, you can effortlessly organize adventure records based on your preferences. ";
$instructionParagraph .= "Toggle on the column headers to sort by adventure name, category, date, or expected fee by ascending ";
$instructionParagraph .= "and descending order. Simply click on the title of each adventure to get further detail.";

?>

<main>
    <section class="beige-half">
        <h2 class="header-grid">Adventures Page</h2>
        <p class="paragraph"><?php print $instructionParagraph?></p>

        <table class="center-table">
            <caption>
                <?php
                print '<p>Showing records ' . $start + 1;
                $nextStart = ($nextStart <= $total) ? $nextStart : $total;
                print ' to ' . $nextStart . ' of ' . $total . '<p>';
                ?>
            </caption>
            <tr>
                <td style="text-align: left;" colspan="4"><?php print $paging->getPageStartText(); ?></td>
                <td style="text-align: right;"><?php print $paging->getPageEndText(); ?></td>
            </tr>
            <tr>
                <th>
                    <a href='?sortField=fldAdventureName&amp;sortDirection=<?php print $oppositeSort . '&start=' . $start;  ?>'>Adventure Name
                    <img class="arrow" alt="arrow-image" src="<?php print ($oppositeSort == 'ASC') ? 'down-arrow.png' : 'up-arrow.png'; ?>"></a>
                </th>
                <th>
                    <a href='?sortField=pmkAdventureId&amp;sortDirection=<?php print $oppositeSort . '&start=' . $start;  ?>'>Adventure Id
                    <img class="arrow" alt="arrow-image" src="<?php print ($oppositeSort == 'ASC') ? 'down-arrow.png' : 'up-arrow.png'; ?>"></a>
                </th>
                <th>
                    <a href='?sortField=fldCategoryName&amp;sortDirection=<?php print $oppositeSort . '&start=' . $start;  ?>'>Category Name
                    <img class="arrow" alt="arrow-image" src="<?php print ($oppositeSort == 'ASC') ? 'down-arrow.png' : 'up-arrow.png'; ?>"></a>
                </th>
                <th>
                    <a href='?sortField=fldAdventureDate&amp;sortDirection=<?php print $oppositeSort . '&start=' . $start;  ?>'>Adventure Date
                    <img class="arrow" alt="arrow-image" src="<?php print ($oppositeSort == 'ASC') ? 'down-arrow.png' : 'up-arrow.png'; ?>"></a>
                </th>
                <th>
                    <a href='?sortField=fldExpectedFee&amp;sortDirection=<?php print $oppositeSort . '&start=' . $start;  ?>'>Expected Fee
                    <img class="arrow" alt="arrow-image" src="<?php print ($oppositeSort == 'ASC') ? 'down-arrow.png' : 'up-arrow.png'; ?>"></a>
                </th>
            </tr>
            <?php

            foreach ($adventures as $adventure) {
                print '<tr>';
                print '<td><a href="adventure-detail.php?adventureId=' . $adventure['pmkAdventureId'] . '" class="update-button">' . $adventure['fldAdventureName'] . '</a></td>';
                print '<td>' . $adventure['pmkAdventureId'] . '</td>';
                print '<td>' . $adventure['fldCategoryName'] . '</td>';
                print '<td>' . $adventure['fldAdventureDate'] . '</td>';
                print '<td> $' . $adventure['fldExpectedFee'] . '</td>';
                print '</tr>' . PHP_EOL;
            }

            print '<tr><th style="text-align: left;" colspan="4">';
            print ($previous >= 0) ? '<a href="' . $previousGetString . '">Previous</a>' : '<span class="noLink">Previous</span>';
            print '</th>';

            print '<th style="text-align: right;">';
            print ($nextStart < $total) ? ' <a href="' . $nextGetString . '">Next</a>' : '<span class="noLink">Next</span>';
            print '</th></tr>';

            ?>
        </table>
    </section>
</main>

<?php
include "footer.php";
?>