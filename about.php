<?php
include 'top.php';

$about = "At Local Adventures, we're on a mission to celebrate the beauty, culture, ";
$about .= "and community of Vermont through a wide range of exciting experiences. ";
$about .= "Whether you're seeking outdoor thrills, artistic inspiration, or cultural immersion, you'll find it all here.";

$what = "What drives us is our passion for creating unforgettable moments and fostering connections ";
$what .= "between people and the diverse wonders of our state. ";

$where = "Wherever you are in Vermont, you'll discover us organizing hikes through scenic trails, hosting picnics "; 
$where .= "and painting sessions in picturesque settings, and screening classic movies like Lord of the Rings under the stars.";

$why = "Why do we do it? Because we believe in the power of adventure, creativity, and ";
$why .= "community to enrich lives and create lasting memories.";

$how = "How do we do it? By curating diverse events, prioritizing inclusivity and accessibility, ";
$how .= "and fostering a welcoming environment where everyone can feel inspired and engaged. ";
$how .= "Join us as we embark on a journey of discovery, exploration, and connection with Vermont Adventures!";
?>

<main>
<section class='beige-half'>
    <h2 class="header-grid">About Us</h2>
    <p class="paragraph"><?php print $about ?></p>
</section>
<section class='green-half'>
    <h2 class="header-grid">What?</h2>  
    <p class="paragraph"><?php print $what ?></p>
</section>
<section class='green-half'>
    <h2 class="header-grid">Where?</h2>
    <p class="paragraph"><?php print $where ?></p>
</section>
<section class='beige-half'>
    <h2 class="header-grid">Why?</h2>
    <p class="paragraph"><?php print $why ?></p>
</section>
<section class='beige-half'>
    <h2 class="header-grid">How?</h2>
    <p class="paragraph"><?php print $how ?></p>
</section>
</main>

<?php
include 'footer.php';
?>