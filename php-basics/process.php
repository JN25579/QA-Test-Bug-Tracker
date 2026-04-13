<?php

require 'helpers.php';

$action = '';

if (isset($_POST['action'])) {
    $action = trim((string) $_POST['action']);
}

$errors = [];
$issue = null;
$statusLabel = '';
$message = '';

if ($action === 'move') {
    $issueId = 0;
    if (isset($_POST['issue_id'])) {
        $issueId = (int) $_POST['issue_id'];
    }

    $nextStatus = '';
    if (isset($_POST['next_status'])) {
        $nextStatus = trim((string) $_POST['next_status']);
    }

    if ($issueId <= 0) {
        $errors[] = 'Issue id is missing.';
    }

    if (!isset(getStatusOptions()[$nextStatus])) {
        $errors[] = 'Choose a valid status move.';
    }

    if (count($errors) === 0) {
        if (!isDatabaseAvailable()) {
            $errors[] = getDatabaseSetupMessage();
        } else {
            updateIssueStatus($issueId, $nextStatus);
            header('Location: index.php');
            exit;
        }
    }
}

$formData = getIssueFormData($_POST);

if ($action === 'create') {
    $errors = validateIssueForm($formData);

    if (!isDatabaseAvailable()) {
        $errors[] = getDatabaseSetupMessage();
    }

    if (count($errors) === 0) {
        $issueId = createIssue($formData);
        $issue = findIssueById($issueId);
        $statusOptions = getStatusOptions();
        $statusLabel = $statusOptions[$issue['status']];
        $message = 'The issue has been added to the board.';
    }
} else {
    if (count($errors) === 0) {
        $errors[] = 'No valid form action was provided.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Form result</title>
    <?php renderPageStyles(); ?>
</head>

<body>
    <div class="page-shell">
        <main class="page-card">
            <?php include 'nav.php'; ?>

            <span class="result-tag">Issue workflow</span>
            <h1>Issue result</h1>
            <p class="lead">This page handles server-side validation and saves valid issues into SQLite.</p>

            <?php if (count($errors) > 0) { ?>
                <div class="alert alert-error">
                    <h2>Fix these problems</h2>
                    <ul class="error-list">
                        <?php foreach ($errors as $error) { ?>
                            <li><?php echo sanitiseString($error); ?></li>
                        <?php } ?>
                    </ul>
                </div>

                <p><a class="button-link" href="form.php">Back to form</a></p>
            <?php } ?>

            <?php if ($issue !== null) { ?>
                <div class="alert alert-success">
                    <strong><?php echo sanitiseString($message); ?></strong>
                </div>

                <div class="result-box">
                    <h2><?php echo sanitiseString($issue['title']); ?></h2>
                    <p><?php echo nl2br(sanitiseString($issue['description'])); ?></p>
                    <ul class="stats-list">
                        <li><strong>Reporter:</strong> <?php echo sanitiseString($issue['reporter']); ?></li>
                        <li><strong>Assignee:</strong> <?php echo sanitiseString($issue['assignee']); ?></li>
                        <li><strong>Severity:</strong> <?php echo sanitiseString(getSeverityOptions()[$issue['severity']]); ?></li>
                        <li><strong>Status:</strong> <?php echo sanitiseString($statusLabel); ?></li>
                        <li><strong>Issue id:</strong> <?php echo (int) $issue['id']; ?></li>
                    </ul>
                </div>

                <p>
                    <a class="button-link" href="index.php">Open board</a>
                    <a class="button-link secondary-button" href="form.php">Create another</a>
                </p>
            <?php } ?>
        </main>
    </div>

</body>

</html>
