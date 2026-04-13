<?php
require 'helpers.php';

$name = 'Guest';
$greet = fn($name) => 'Hello ' . $name;

if (isset($_GET['name'])) {
    $name = sanitiseString($_GET['name']);
}

if (isset($_GET['age']) && $_GET['age'] !== '') {
    $age = (int) $_GET['age'];
    if (isAdult($age)) {
        $name .= ' (Adult)';
    } else {
        $name .= ' (Minor)';
    }
} else {
    $name .= ' (Age not provided)';
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PHP basics home</title>
</head>

<body>

    <?php include 'nav.php'; ?>

    <h1>Home</h1>


    <p>
        <?php
        echo $greet($name);
        ?>
    </p>

</body>

</html>
