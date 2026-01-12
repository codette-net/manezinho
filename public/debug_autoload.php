<?php
header('Content-Type: text/plain; charset=utf-8');

$autoload = dirname(__DIR__) . '/vendor/autoload.php';

echo "autoload path: {$autoload}\n";
echo "autoload exists: " . (file_exists($autoload) ? 'yes' : 'no') . "\n";

if (file_exists($autoload)) {
    require_once $autoload;
}

echo "PHPMailer class exists: " . (class_exists(\PHPMailer\PHPMailer\PHPMailer::class) ? 'yes' : 'no') . "\n";

echo "vendor dir exists: " . (is_dir(dirname(__DIR__) . '/vendor') ? 'yes' : 'no') . "\n";
