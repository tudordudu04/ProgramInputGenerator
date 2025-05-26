<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'PIG'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header-footer.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="logo">Program Input Generator</a>
        <nav>
            <ul>
                <li><a href="<?php echo $_SERVER['PHP_SELF'];?>">Home</a></li>
                <?php if(isset($_COOKIE['jwt'])): ?>
                    <li><a href="pages/profile.html">Profile</a></li>
                    <li><a href="auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="pages/login.html">Login</a></li>
                    <li><a href="pages/register.html">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main class="site-main">