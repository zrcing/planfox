<?php

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else if (is_file(__DIR__ . '/../../vendor/autoload.php')) {
    require __DIR__ . '/../../vendor/autoload.php';
} else if (is_file(__DIR__ . '/../../../vendor/autoload.php')) {
    require __DIR__ . '/../../../vendor/autoload.php';
}