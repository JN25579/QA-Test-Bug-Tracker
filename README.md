# QA-Test-Bug-Tracker

Small beginner-friendly PHP issue tracker with a kanban board.

## Run with Docker

1. Install Docker Desktop.
2. From the repo root, run `docker compose up --build`.
3. Open `http://localhost:8000`.

The app uses SQLite, so the database is stored as `php-basics/data/issues.sqlite`.
The schema lives in `php-basics/data/schema.sql` and is applied automatically when the app connects.
The `php-basics` folder is mounted into the container, which means your PHP file changes show up immediately and the SQLite file stays in the repo folder.

## Run without Docker

If you still want to use the local PHP setup in this repo:

`php -c C:\Users\JORDAN\QA-Test-Bug-Tracker\php.ini -S localhost:8000 -t php-basics`
