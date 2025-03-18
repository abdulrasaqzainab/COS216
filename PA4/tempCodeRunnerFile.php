<?php
<nav>
            <div class="logo">
                <a href="index.php">Your Logo</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                
                <?php
                // If user is logged in, display their name and logout button
                if(isset($username)) {
                    echo '<li>Welcome, ' . $username . '!</li>';
                    echo '<li><a href="logout.php">Logout</a></li>';
                } else { // If user is not logged in, display login and register links
                    echo '<li><a href="login.php">Login</a></li>';
                    echo '<li><a href="register.php">Register</a></li>';
                }
                ?>
            </ul>
        </nav>