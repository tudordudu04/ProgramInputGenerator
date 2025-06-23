<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'PIG'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header-footer.css">
    <?php if(isset($_COOKIE['jwt'])): ?> <script src="js/searchUser.js"></script> <?php endif; ?>
    <script src="js/mobile-menu.js"></script>
    <?php if (!empty($scriptSource)) echo $scriptSource; ?>
    <?php if (!empty($additionalCss)) echo $additionalCss; ?>
</head>
<body>
<div class="site-header">
    <div class="margin">
        <a href="index.php" class="logo">frankenpig.</a>
        <nav>
            <ul class="align">
                <?php if(isset($_COOKIE['jwt'])): ?>
                    <li>
                        <form action="database/searchUser.php" method="POST" style="display:inline;" class="search" autocomplete="off">
                            <input type="text" name="query" placeholder="Search..." required autocomplete="off" class="queryBox" id="queryBox">
                            <ul id="results"></ul>
                        </form>
                    </li>
                <?php endif; ?>
                <li><a href="index.php">HOME</a></li>
                <?php if(isset($_COOKIE['jwt'])): ?>
                    <li><a href="profile.php">PROFILE</a></li>
                    <li><a href="auth/logout.php">LOGOUT</a></li>
                <?php else: ?>
                    <li><a href="login.html">LOGIN</a></li>
                    <li><a href="register.html">REGISTER</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>