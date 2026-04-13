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
