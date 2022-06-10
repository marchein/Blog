<?php /** @noinspection PhpIncludeInspection */
session_start();
spl_autoload_register(function ($class_name) {
    require_once("classes/" . $class_name . ".php");
});

$databaseConnection = new DBConnection(Config::$DB_Host, Config::$DB_User, Config::$DB_Password, Config::$DB_Database);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?php echo Config::$AppName; ?></title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body>

<header>
    <?php new HeaderContent($databaseConnection); ?>
</header>

<main>
    <div class="container mt-4">
        <?php new MainContent($databaseConnection); ?>
    </div>
</main>

<?php $databaseConnection->disconnect(); ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>