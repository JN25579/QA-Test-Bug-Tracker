<?php

require 'helpers.php';

$severityOptions = getSeverityOptions();
$statusOptions = getStatusOptions();
$databaseReady = isDatabaseAvailable();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Simple form</title>
    <?php renderPageStyles(); ?>
</head>

<body>
    <div class="page-shell">
        <main class="page-card">
            <?php include 'nav.php'; ?>

            <span class="hero-tag">Issue Intake</span>
            <h1>Create an issue</h1>
            <p class="lead">Log a bug or task, choose its severity, and drop it straight into the board.</p>

            <?php if (!$databaseReady) { ?>
                <div class="alert alert-error">
                    <strong><?php echo sanitiseString(getDatabaseSetupMessage()); ?></strong>
                </div>
            <?php } ?>

            <form method="post" action="process.php" class="form-grid">
                <div class="form-field">
                    <label for="title">Issue title:</label>
                    <input type="text" name="title" id="title" placeholder="Login button is not saving state" required>
                </div>

                <div class="form-field">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" placeholder="Add the bug details, expected behaviour, and any steps to reproduce." required></textarea>
                </div>

                <div class="form-field">
                    <label for="reporter">Reporter:</label>
                    <input type="text" name="reporter" id="reporter" placeholder="Jordan" required>
                </div>

                <div class="form-field">
                    <label for="assignee">Assignee:</label>
                    <input type="text" name="assignee" id="assignee" placeholder="Alex" required>
                </div>

                <div class="form-field">
                    <label for="severity">Severity:</label>
                    <select name="severity" id="severity">
                        <?php foreach ($severityOptions as $value => $label) { ?>
                            <option value="<?php echo sanitiseString($value); ?>"><?php echo sanitiseString($label); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-field">
                    <label for="status">Starting status:</label>
                    <select name="status" id="status">
                        <?php foreach ($statusOptions as $value => $label) { ?>
                            <option value="<?php echo sanitiseString($value); ?>"><?php echo sanitiseString($label); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <p>
                    <button type="submit" name="action" value="create" <?php if (!$databaseReady) { ?>disabled<?php } ?>>Create issue</button>
                </p>
            </form>
        </main>
    </div>

</body>

</html>
