<?php

function sanitiseString($value): string
{
    if (is_array($value)) {
        $value = implode('', $value);
    }

    return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}

function calculateAverage(array $numbers): float
{
    $count = count($numbers);
    if ($count === 0) {
        return 0;
    }

    $sum = 0;
    foreach ($numbers as $number) {
        $sum += $number;
    }

    return $sum / $count;
}

function isAdult(int $age): bool
{
    return $age >= 18;
}

function getSeverityOptions(): array
{
    return [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];
}

function getStatusOptions(): array
{
    return [
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'done' => 'Done',
    ];
}

function getDatabasePath(): string
{
    return __DIR__ . '/data/issues.sqlite';
}

function getDatabaseSchemaPath(): string
{
    return __DIR__ . '/data/schema.sql';
}

function isDatabaseAvailable(): bool
{
    return in_array('sqlite', PDO::getAvailableDrivers(), true);
}

function getDatabaseSetupMessage(): string
{
    return 'SQLite is not enabled in this PHP environment. Enable the pdo_sqlite extension to use the kanban board database.';
}

function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (!isDatabaseAvailable()) {
        throw new RuntimeException(getDatabaseSetupMessage());
    }

    $databaseDirectory = dirname(getDatabasePath());
    if (!is_dir($databaseDirectory)) {
        mkdir($databaseDirectory, 0777, true);
    }

    $pdo = new PDO('sqlite:' . getDatabasePath());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    initialiseDatabase($pdo);

    return $pdo;
}

function initialiseDatabase(PDO $pdo): void
{
    $schemaPath = getDatabaseSchemaPath();
    if (!file_exists($schemaPath)) {
        throw new RuntimeException('Database schema file was not found.');
    }

    $schema = file_get_contents($schemaPath);
    if ($schema === false) {
        throw new RuntimeException('Database schema file could not be read.');
    }

    $pdo->exec($schema);
}

function getIssueFormData(array $source): array
{
    $data = [
        'title' => '',
        'description' => '',
        'reporter' => '',
        'assignee' => '',
        'severity' => 'medium',
        'status' => 'todo',
    ];

    foreach ($data as $key => $value) {
        if (isset($source[$key])) {
            $data[$key] = trim((string) $source[$key]);
        }
    }

    return $data;
}

function getRandomIssueFormData(): array
{
    $titles = [
        'Login button does not keep session',
        'Board count is wrong after refresh',
        'Error message overlaps the form',
        'Done column still shows active task',
        'New issue page loads too slowly',
    ];

    $descriptions = [
        'Open the page, submit the form, and refresh the board. The saved result does not match the expected issue state.',
        'Create a new issue and move it across the board. The summary numbers do not update the way a tester would expect.',
        'Use a smaller screen and trigger a validation error. The layout becomes cramped and the page is harder to read.',
        'Move an issue to done, then reopen it. The board flow is inconsistent and needs another check.',
        'Load the tracker with a few issues already saved. The page feels slower than it should for a small app.',
    ];

    $people = [
        'Jordan',
        'Alex',
        'Taylor',
        'Morgan',
        'Casey',
    ];

    $severityKeys = array_keys(getSeverityOptions());
    $statusKeys = array_keys(getStatusOptions());

    return [
        'title' => $titles[array_rand($titles)],
        'description' => $descriptions[array_rand($descriptions)],
        'reporter' => $people[array_rand($people)],
        'assignee' => $people[array_rand($people)],
        'severity' => $severityKeys[array_rand($severityKeys)],
        'status' => $statusKeys[array_rand($statusKeys)],
    ];
}

function validateIssueForm(array $data): array
{
    $errors = [];
    $severityOptions = getSeverityOptions();
    $statusOptions = getStatusOptions();

    if ($data['title'] === '') {
        $errors[] = 'Title is required.';
    } elseif (strlen($data['title']) < 4) {
        $errors[] = 'Title must be at least 4 characters.';
    }

    if ($data['description'] === '') {
        $errors[] = 'Description is required.';
    } elseif (strlen($data['description']) < 10) {
        $errors[] = 'Description must be at least 10 characters.';
    }

    if ($data['reporter'] === '') {
        $errors[] = 'Reporter is required.';
    }

    if ($data['assignee'] === '') {
        $errors[] = 'Assignee is required.';
    }

    if (!isset($severityOptions[$data['severity']])) {
        $errors[] = 'Choose a valid severity.';
    }

    if (!isset($statusOptions[$data['status']])) {
        $errors[] = 'Choose a valid status.';
    }

    return $errors;
}

function createIssue(array $data): int
{
    $pdo = getDatabaseConnection();
    $statement = $pdo->prepare(
        'INSERT INTO issues (title, description, reporter, assignee, severity, status, created_at)
         VALUES (:title, :description, :reporter, :assignee, :severity, :status, :created_at)'
    );

    $statement->execute([
        ':title' => $data['title'],
        ':description' => $data['description'],
        ':reporter' => $data['reporter'],
        ':assignee' => $data['assignee'],
        ':severity' => $data['severity'],
        ':status' => $data['status'],
        ':created_at' => date('Y-m-d H:i:s'),
    ]);

    return (int) $pdo->lastInsertId();
}

function fetchIssuesByStatus(): array
{
    $groupedIssues = [];
    foreach (getStatusOptions() as $status => $label) {
        $groupedIssues[$status] = [];
    }

    if (!isDatabaseAvailable()) {
        return $groupedIssues;
    }

    $pdo = getDatabaseConnection();
    $statement = $pdo->query(
        "SELECT * FROM issues
         ORDER BY
            CASE severity
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                ELSE 3
            END,
            id DESC"
    );

    foreach ($statement as $issue) {
        if (isset($groupedIssues[$issue['status']])) {
            $groupedIssues[$issue['status']][] = $issue;
        }
    }

    return $groupedIssues;
}

function findIssueById(int $issueId): ?array
{
    if (!isDatabaseAvailable()) {
        return null;
    }

    $pdo = getDatabaseConnection();
    $statement = $pdo->prepare('SELECT * FROM issues WHERE id = :id');
    $statement->execute([':id' => $issueId]);
    $issue = $statement->fetch();

    if ($issue === false) {
        return null;
    }

    return $issue;
}

function updateIssueStatus(int $issueId, string $status): bool
{
    $statusOptions = getStatusOptions();
    if (!isset($statusOptions[$status])) {
        return false;
    }

    if (!isDatabaseAvailable()) {
        return false;
    }

    $pdo = getDatabaseConnection();
    $statement = $pdo->prepare('UPDATE issues SET status = :status WHERE id = :id');

    return $statement->execute([
        ':status' => $status,
        ':id' => $issueId,
    ]);
}

function getNextStatusActions(string $currentStatus): array
{
    if ($currentStatus === 'todo') {
        return [
            'in_progress' => 'Start work',
        ];
    }

    if ($currentStatus === 'in_progress') {
        return [
            'done' => 'Mark done',
            'todo' => 'Move back',
        ];
    }

    if ($currentStatus === 'done') {
        return [
            'todo' => 'Reopen',
        ];
    }

    return [];
}

function getIssueCount(array $issues): int
{
    return count($issues);
}

function renderPageStyles(): void
{
    echo '<style>
        :root {
            --page-background: #efe6d6;
            --panel-background: rgba(255, 251, 245, 0.94);
            --panel-border: #d9c8ae;
            --text-colour: #2d241c;
            --muted-text: #6c5a49;
            --accent: #be6a15;
            --accent-dark: #8f4f10;
            --highlight: #f5dbc0;
            --shadow: 0 18px 45px rgba(80, 53, 24, 0.16);
            --todo-colour: #6f7d8c;
            --progress-colour: #1f6c8b;
            --done-colour: #4f8a5b;
            --high-colour: #a53a2e;
            --medium-colour: #bf7a18;
            --low-colour: #4f7d58;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Georgia, "Times New Roman", serif;
            color: var(--text-colour);
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.78), transparent 30%),
                linear-gradient(135deg, #f8f1e7 0%, #e5d2b6 100%);
        }

        a {
            color: var(--accent-dark);
        }

        .page-shell {
            width: min(1200px, calc(100% - 2rem));
            margin: 2rem auto;
        }

        .page-card {
            background: var(--panel-background);
            border: 1px solid var(--panel-border);
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(6px);
        }

        .page-nav {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .page-nav a {
            text-decoration: none;
            padding: 0.65rem 1rem;
            border-radius: 999px;
            border: 1px solid var(--panel-border);
            background: #fffaf3;
            font-weight: bold;
        }

        .hero-tag,
        .result-tag {
            display: inline-block;
            margin-bottom: 0.75rem;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: var(--highlight);
            color: var(--accent-dark);
            font-size: 0.9rem;
            font-weight: bold;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3 {
            margin-top: 0;
        }

        h1 {
            margin-bottom: 0.75rem;
            font-size: clamp(2rem, 5vw, 3.2rem);
        }

        p {
            line-height: 1.6;
        }

        .lead {
            color: var(--muted-text);
            font-size: 1.05rem;
        }

        .highlight-box,
        .result-box,
        .info-panel {
            margin-top: 1.5rem;
            padding: 1rem 1.15rem;
            border-radius: 18px;
            background: #fffaf3;
            border: 1px solid #ead9c2;
        }

        .form-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .form-field label {
            display: block;
            margin-bottom: 0.45rem;
            font-weight: bold;
        }

        .form-field input,
        .form-field select,
        .form-field textarea {
            width: 100%;
            padding: 0.8rem 0.9rem;
            border: 1px solid #cbb79b;
            border-radius: 14px;
            background: #fffdf9;
            color: var(--text-colour);
            font: inherit;
        }

        .form-field textarea {
            min-height: 130px;
            resize: vertical;
        }

        button,
        .button-link {
            display: inline-block;
            padding: 0.8rem 1.15rem;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--accent) 0%, #d58733 100%);
            color: #fff8f0;
            font: inherit;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .secondary-button {
            background: #f0e0ca;
            color: var(--text-colour);
        }

        .stats-list,
        .error-list {
            padding-left: 1.2rem;
        }

        .stats-list li,
        .error-list li {
            margin-bottom: 0.45rem;
        }

        .summary-grid,
        .board-grid {
            display: grid;
            gap: 1rem;
        }

        .summary-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 1.5rem;
        }

        .summary-card {
            padding: 1rem;
            border-radius: 18px;
            background: #fffaf3;
            border: 1px solid #ead9c2;
        }

        .summary-card strong {
            display: block;
            font-size: 1.8rem;
        }

        .board-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: start;
            margin-top: 1.5rem;
        }

        .board-column {
            min-height: 260px;
            padding: 1rem;
            border-radius: 20px;
            border: 1px solid #dcc7a6;
            background: rgba(255, 249, 241, 0.88);
        }

        .column-todo {
            border-top: 6px solid var(--todo-colour);
        }

        .column-in_progress {
            border-top: 6px solid var(--progress-colour);
        }

        .column-done {
            border-top: 6px solid var(--done-colour);
        }

        .column-heading {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .issue-card {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 18px;
            background: #fffdf9;
            border: 1px solid #ead9c2;
        }

        .issue-meta {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }

        .pill {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: bold;
        }

        .severity-high {
            background: #f6d4cf;
            color: var(--high-colour);
        }

        .severity-medium {
            background: #f7e3bf;
            color: var(--medium-colour);
        }

        .severity-low {
            background: #dceadf;
            color: var(--low-colour);
        }

        .status-pill {
            background: #e9decd;
            color: var(--text-colour);
        }

        .card-actions {
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .card-actions form {
            margin: 0;
        }

        .card-actions button {
            padding: 0.55rem 0.85rem;
            font-size: 0.92rem;
        }

        .alert {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 18px;
        }

        .alert-success {
            background: #e1efe0;
            border: 1px solid #b9d4b7;
        }

        .alert-error {
            background: #f6d8d3;
            border: 1px solid #deb1a9;
        }

        .muted {
            color: var(--muted-text);
        }

        @media (max-width: 900px) {
            .board-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .page-shell {
                width: min(100% - 1rem, 1200px);
                margin: 1rem auto;
            }

            .page-card {
                padding: 1.1rem;
                border-radius: 18px;
            }
        }
    </style>';
}
