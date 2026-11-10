<?php
// Bot trap: only bots visit this address. See api/botguard.php.
declare(strict_types=1);
define('PL_APP', true);
require __DIR__ . '/../api/lib.php';
require __DIR__ . '/../api/botguard.php';
run_trap('/hp/');
