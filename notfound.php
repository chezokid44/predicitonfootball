<?php
// Requests for pages that don't exist end up here: from index.php, from
// .htaccess (Apache) or from nginx's error_page. Missing pages can count as
// strikes, and paths on the trap URL list block straight away.
// See api/botguard.php.
declare(strict_types=1);
if (!defined('PL_APP')) define('PL_APP', true);
require_once __DIR__ . '/api/lib.php';
require_once __DIR__ . '/api/botguard.php';
handle_not_found();
