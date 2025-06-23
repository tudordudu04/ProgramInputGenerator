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