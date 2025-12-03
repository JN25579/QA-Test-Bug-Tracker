<?php

require 'helpers.php';

$name = 'Anonymous';

if (isset($_POST['name'])) {
    $name = sanitiseString($_POST['name']);
}

$numbersRaw = '';
if (isset($_POST['numbers'])) {
    $numbersRaw = $_POST['numbers'];
}

$numberParts = explode(',', $numbersRaw);

$numbers = [];

foreach ($numberParts as $part) {
    if ($part === '') {
        continue;
    }

    if (is_numeric($part)) {
        $numbers[] = (float) $part;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Form result</title>
</head>

<body>

    <?php include 'nav.php'; ?>

    <h1>Form result</h1>

    <p>Hello <?php echo $name; ?>.</p>

    <?php
    if (count($numbers) > 0) {
        $count = $numbers;
        echo 'You entered ' . $count . ' valid numbers.';
    } else {
        echo 'You did not enter any valid numbers.';
    }
    ?>

    <p><a href="form.php">Back to form</a></p>

</body>

</html>