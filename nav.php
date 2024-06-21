<!--######### Main Navigation #########-->
<nav class = "nav">
    <a class="<?php
    if ($path_parts['filename']=="index"){
        print 'activePage';
    }
    ?>" href = "index.php">Home</a>
    <a class="<?php
    if ($path_parts['filename']=="adventure"){
        print 'activePage';
    }
    ?>" href = "adventure.php">Adventure</a>
    <a class="<?php
    if ($path_parts['filename']=="form"){
        print 'activePage';
    }
    ?>" href = "form.php">Form</a>
    <a class="<?php
    if ($path_parts['filename']=="about"){
        print 'activePage';
    }
    ?>" href = "about.php">About Us</a>
</nav>

