$files = @(
    "php-basics/index.php",
    "php-basics/form.php",
    "php-basics/process.php",
    "php-basics/helpers.php",
    "php-basics/nav.php"
)

foreach ($file in $files) {
    php -l $file
}
