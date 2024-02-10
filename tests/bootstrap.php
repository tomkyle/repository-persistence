<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

$root_path = dirname(__DIR__);
$autoloader = $root_path . '/vendor/autoload.php';
if (!is_readable($autoloader)) {
    die(sprintf("\nMissing Composer's Autoloader '%s'; Install Composer dependencies first.\n\n", $autoloader));
}
