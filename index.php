<?php

use alcea\generator\PhpPasswordGenerator;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $passwordObj = new PhpPasswordGenerator(true, true, true, true);
    echo $passwordObj->generate(false, false, 4, false);
} catch (\Throwable $e) {
    echo $e->getMessage();
}
