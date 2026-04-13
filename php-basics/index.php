<?php
require 'helpers.php';

$statusOptions = getStatusOptions();
$severityOptions = getSeverityOptions();
$issuesByStatus = fetchIssuesByStatus();
$databaseReady = isDatabaseAvailable();
$totalIssues = 0;

foreach ($issuesByStatus as $issues) {
    $totalIssues += getIssueCount($issues);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PHP basics home</title>
    <?php renderPageStyles(); ?>
</head>

<body>
    <div class="page-shell">
        <main class="page-card">
            <?php include 'nav.php'; ?>

            <span class="hero-tag">Kanban Board</span>
            <h1>QA tracker</h1>
            <p class="lead">A small Jira-style board with server-side validation and a real SQLite database behind it.</p>

            <?php if (!$databaseReady) { ?>
                <div class="alert alert-error">
                    <strong><?php echo sanitiseString(getDatabaseSetupMessage()); ?></strong>
                </div>
            <?php } ?>

            <section class="summary-grid">
                <div class="summary-card">
                    <span class="muted">Total issues</span>
                    <strong><?php echo $totalIssues; ?></strong>
                </div>
                <?php foreach ($statusOptions as $status => $label) { ?>
                    <div class="summary-card">
                        <span class="muted"><?php echo sanitiseString($label); ?></span>
                        <strong><?php echo getIssueCount($issuesByStatus[$status]); ?></strong>
                    </div>
                <?php } ?>
            </section>

            <section class="board-grid">
                <?php foreach ($statusOptions as $status => $label) { ?>
                    <div class="board-column column-<?php echo sanitiseString($status); ?>">
                        <div class="column-heading">
                            <h2><?php echo sanitiseString($label); ?></h2>
                            <span class="pill status-pill"><?php echo getIssueCount($issuesByStatus[$status]); ?></span>
                        </div>

                        <?php if (count($issuesByStatus[$status]) === 0) { ?>
                            <div class="info-panel">
                                <p class="muted">No issues in this column yet.</p>
                            </div>
                        <?php } ?>

                        <?php foreach ($issuesByStatus[$status] as $issue) { ?>
                            <article class="issue-card">
                                <div class="issue-meta">
                                    <span class="pill severity-<?php echo sanitiseString($issue['severity']); ?>">
                                        <?php echo sanitiseString($severityOptions[$issue['severity']]); ?>
                                    </span>
                                    <span class="pill status-pill">#<?php echo (int) $issue['id']; ?></span>
                                </div>

                                <h3><?php echo sanitiseString($issue['title']); ?></h3>
                                <p><?php echo nl2br(sanitiseString($issue['description'])); ?></p>
                                <p><strong>Reporter:</strong> <?php echo sanitiseString($issue['reporter']); ?></p>
                                <p><strong>Assignee:</strong> <?php echo sanitiseString($issue['assignee']); ?></p>
                                <p class="muted">Created: <?php echo sanitiseString($issue['created_at']); ?></p>

                                <?php $actions = getNextStatusActions($issue['status']); ?>
                                <?php if (count($actions) > 0) { ?>
                                    <div class="card-actions">
                                        <?php foreach ($actions as $nextStatus => $buttonText) { ?>
                                            <form method="post" action="process.php">
                                                <input type="hidden" name="action" value="move">
                                                <input type="hidden" name="issue_id" value="<?php echo (int) $issue['id']; ?>">
                                                <input type="hidden" name="next_status" value="<?php echo sanitiseString($nextStatus); ?>">
                                                <button type="submit"><?php echo sanitiseString($buttonText); ?></button>
                                            </form>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </article>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>
        </main>
    </div>

</body>

</html>
