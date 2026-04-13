param(
    [int]$Port = 8000
)

$phpDir = "php-basics"

Write-Host "Starting PHP server at http://localhost:$Port"
php -S "localhost:$Port" -t $phpDir
