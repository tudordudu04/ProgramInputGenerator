<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'PIG'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header-footer.css">
    <?php if (!empty($scriptSource)) echo $scriptSource; ?>
    <?php if (!empty($additionalCss)) echo $additionalCss; ?>
</head>
<body>
<header class="site-header">
    <div class="margin">
        <a href="<?php echo $_SERVER['PHP_SELF'];?>" class="logo">Program Input Generator</a>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if(isset($_COOKIE['jwt'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="../auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.html">Login</a></li>
                    <li><a href="register.html">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>