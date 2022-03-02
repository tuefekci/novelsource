<?php
/**
 *
 * @copyright       Copyright (c) 2021. Giacomo Tüfekci (https://www.tuefekci.de)
 * @github          https://github.com/tuefekci
 * @license         https://www.tuefekci.de/LICENSE.md
 *
 */

require_once(__DIR__ . '/vendor/autoload.php');

// =================================================================
// Defines
define("__ROOT__", __DIR__);
define("__SRC__", __ROOT__."/src");
define("__DATA__", __ROOT__."/data");
define("__BIN__", __ROOT__."/bin");
// =================================================================

// =================================================================
// Ini Sets
ini_set("memory_limit","1024M");
// =================================================================

// =================================================================
// Define Timezone
date_default_timezone_set('Europe/Berlin');
// =================================================================

class Cli
{
	public function __construct()
	{
	}
}

new Cli();