<nav class="d-flex w-100 border justify-space">
    <a href="index.php">
        <span class="logo">logo</span>
    </a>

    <ul class="d-flex nav-links gap" style="--c-gap:1.5rem">
        <li><a href="index.php">Home</a></li>
        <li><a href="home.php">Profile</a></li>
        <li><a href="opportunities.php">Opportunities</a></li>
        <li><a href="manage_opportunities.php">Manage Opportunities</a></li>

        <?php
        if (isset($_SESSION['user']))
            print('<li><a href="index.php?action=sign_out">Sign out</a></li>')
        ?>
    </ul>

</nav>