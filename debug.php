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



$time_start = microtime(true); 

$NovelSource  = new \Tuefekci\NovelSource\NovelSource();



$url = "https://readnovelfull.com/hidden-marriage-v3.html";
$url = "https://readnovelfull.com/the-mech-touch-v5.html";
$source = $NovelSource->locateNovelSource($url);
#$novel = $source->novel($url, ['skipChapters' => false]);
$novels = $source->search("game");
$novel = $source->novel($novels[0]->url, ['skipChapters' => false]);
$chapter = $source->chapter($novel->chapters[0]->url);


var_dump($novel);
var_dump($chapter);

var_dump(count($novels));

// Anywhere else in the script
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);
