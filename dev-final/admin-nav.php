<!--######### Admin's Navigation #########-->
<nav class = "nav">
    <a class="<?php
    if ($path_parts['filename']=="display-user"){
        print 'activePage';
    }
    ?>" href = "display-user.php">Users</a>
    <div class="dropdown">
        <a class="dropdown-button" aria-haspopup="true">Adventures</a>
        <div class="dropdown-content">
            <a class="<?php
            if ($path_parts['filename']=="display-adventures"){
                print 'activePage';
            }
            ?>" href = "display-adventures.php">Display Adventures</a>

            <a class="<?php
            if ($path_parts['filename']=="insert-adventure"){
                print 'activePage';
            }
            ?>" href = "insert-adventure.php?userId=<?php echo $admin[0]['pmkUserId']; ?>" >Insert Adventure</a>
        </div>
    </div>
    <a class="<?php
    if ($path_parts['filename']=="join-group"){
        print 'activePage';
    }
    ?>" href = "join-group.php?userId=<?php echo $admin[0]['pmkUserId']; ?>" >Group</a>

    <a class="<?php
    if ($path_parts['filename']=="post-review"){
        print 'activePage';
    }
    ?>" href = "post-review.php?userId=<?php echo $admin[0]['pmkUserId']; ?>" >Review</a>
</nav>

