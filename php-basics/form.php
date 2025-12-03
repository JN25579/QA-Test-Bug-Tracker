<?php

require 'helpers.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Simple form</title>
</head>

<body>

    <?php include 'nav.php'; ?>

    <h1>Simple form</h1>

    <form method="post" action="process.php">
        <p>
            <label>
                Your name:
                <input type="text" name="name">
            </label>
        </p>
        <p>
            <label>
                Favourite numbers (comma separated):
                <input type="text" name="numbers" placeholder="10, 20, 30">
            </label>
        </p>
        <p>
            <button type="submit">Submit</button>
        </p>
    </form>

</body>

</html>